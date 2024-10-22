<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../config.php';

if (isset($_GET['list_id'])) {
    $list_id = $_GET['list_id'];
} else {
    header("Location: ../dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);

    $stmt = $conn->prepare("INSERT INTO tasks (todo_list_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $list_id, $title);

    if ($stmt->execute()) {
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 100%;
            max-width: 500px;
            margin: 20px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-center">Add New Task</h3>
            <form method="POST" action="add_task.php?list_id=<?php echo $list_id; ?>">
                <div class="form-group">
                    <label for="title">Task Title</label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter task title" required>
                </div>
                <button type="submit" class="btn btn-custom btn-block">Add Task</button>
                <a href="../dashboard.php" class="btn btn-secondary btn-block">Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
