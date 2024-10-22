<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE tasks SET is_completed = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_completed, $task_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}

header("Location: ../dashboard.php");
exit();
?>
