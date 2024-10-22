<?php
session_start();
include '../config.php';

if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "Error deleting task: " . $conn->error;
    }
} else {
    echo "Invalid task ID.";
}
?>
