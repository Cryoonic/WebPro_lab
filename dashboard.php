<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$search_query = '';
$filter_status = 'all';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_query = isset($_POST['search']) ? htmlspecialchars($_POST['search']) : '';
    $filter_status = isset($_POST['filter']) ? htmlspecialchars($_POST['filter']) : 'all';
}

$query = "SELECT DISTINCT tl.* FROM todo_lists tl 
          LEFT JOIN tasks t ON tl.id = t.todo_list_id 
          WHERE tl.user_id = ?";

$params = [$user_id];
$types = "i";

if ($search_query !== '') {
    $query .= " AND t.title LIKE ?";
    $params[] = '%' . $search_query . '%';
    $types .= "s";
}

if ($filter_status === 'completed') {
    $query .= " AND t.is_completed = 1";
} elseif ($filter_status === 'incomplete') {
    $query .= " AND t.is_completed = 0";
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
    <style>
        .profile-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 10px;
        }
        .dropdown-menu {
            right: 0;
            left: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Hello, <?php echo $_SESSION['username']; ?></a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="profile-circle">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="../WebPro_lab/fitur/profile.php">Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../WebPro_lab/auth/logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-3">
        <form method="POST" action="dashboard.php" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search tasks..." value="<?php echo htmlspecialchars($search_query); ?>">
            <select name="filter" class="form-control mr-2">
                <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All</option>
                <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="incomplete" <?php echo $filter_status === 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</a>
                            <a href="../WebPro_lab/fitur/add_task.php?list_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Add Task</a>

                            <?php
                            $task_query = "SELECT * FROM tasks WHERE todo_list_id = ?";
                            $task_params = [$row['id']];
                            $task_types = "i";

                            if ($search_query !== '') {
                                $task_query .= " AND title LIKE ?";
                                $task_params[] = '%' . $search_query . '%';
                                $task_types .= "s";
                            }

                            if ($filter_status === 'completed') {
                                $task_query .= " AND is_completed = 1";
                            } elseif ($filter_status === 'incomplete') {
                                $task_query .= " AND is_completed = 0";
                            }

                            $task_stmt = $conn->prepare($task_query);
                            $task_stmt->bind_param($task_types, ...$task_params);
                            $task_stmt->execute();
                            $task_result = $task_stmt->get_result();
                            ?>

                            <ul class="list-group mt-3">
                                <?php if ($task_result->num_rows > 0): ?>
                                    <?php while ($task = $task_result->fetch_assoc()): ?>
                                        <li class="list-group-item">
                                            <form method="POST" action="../WebPro_lab/fitur/update_task.php" style="display:inline;">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <input type="checkbox" name="is_completed" value="1" <?php echo $task['is_completed'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                                                <?php echo htmlspecialchars($task['title']); ?>
                                            </form>
                                            <a href="#" class="btn btn-danger btn-sm float-right" onclick="confirmTaskDelete(<?php echo $task['id']; ?>)">Delete</a>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item">No tasks found.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <a href="../WebPro_lab/fitur/add_list.php" class="btn btn-primary mt-3">Add New To-Do List</a>
    </div>

    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this to-do list and all its associated tasks?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="fitur/delete_list.php">
                        <input type="hidden" name="id" id="deleteListId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteTaskConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTaskConfirmationModalLabel">Confirm Task Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this task?
                </div>
                <div class="modal-footer">
                    <form id="deleteTaskForm" method="POST" action="fitur/delete_task.php">
                        <input type="hidden" name="task_id" id="deleteTaskId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmDelete(listId) {
            $('#deleteListId').val(listId);
            $('#deleteConfirmationModal').modal('show');
        }

        function confirmTaskDelete(taskId) {
            $('#deleteTaskId').val(taskId);
            $('#deleteTaskConfirmationModal').modal('show');
        }
    </script>
</body>
</html>
