<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO todo_lists (user_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $title);

    if ($stmt->execute()) {
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
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
    <title>Add To-Do List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Add To-Do List</h1>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="add_list.php">
                    <div class="form-group">
                        <label for="title">List Title</label>
                        <input type="text" name="title" id="title" class="form-control" required placeholder="Enter list title">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> Add List
                    </button>
                    <a href="../dashboard.php" class="btn btn-secondary btn-block">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
