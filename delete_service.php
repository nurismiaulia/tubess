<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $conn->prepare("DELETE FROM services WHERE id = ?");
    $query->bind_param('i', $id);

    if ($query->execute()) {
        header("Location: admin_dashboard.php?message=Service deleted successfully");
        exit;
    } else {
        header("Location: admin_dashboard.php?error=Failed to delete service");
        exit;
    }
}
?>
