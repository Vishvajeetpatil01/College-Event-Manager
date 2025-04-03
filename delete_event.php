<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM events WHERE id = $id");
}

header("Location: manage_events.php");
exit();
?>
