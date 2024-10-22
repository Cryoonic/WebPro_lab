<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        
        $stmt = $conn->prepare("DELETE FROM todo_lists WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        
        $conn->query("DELETE FROM tasks WHERE todo_list_id = $id");

        $stmt->close();
        $conn->close();
    }
    header("Location: ../dashboard.php");
    exit();
}
?>
