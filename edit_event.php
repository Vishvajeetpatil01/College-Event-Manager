<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM events WHERE id = $id");
    $event = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];

    $stmt = $conn->prepare("UPDATE events SET event_name=?, event_description=?, event_date=?, event_time=? WHERE id=?");
    $stmt->bind_param("ssssi", $event_name, $event_description, $event_date, $event_time, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Event updated successfully!'); window.location='manage_events.php';</script>";
    } else {
        echo "<script>alert('Error updating event.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
</head>
<body>
    <h2>Edit Event</h2>
    <form action="" method="POST">
        <input type="text" name="event_name" value="<?= $event['event_name'] ?>" required><br>
        <textarea name="event_description" required><?= $event['event_description'] ?></textarea><br>
        <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required><br>
        <input type="time" name="event_time" value="<?= $event['event_time'] ?>" required><br>
        <button type="submit">Update Event</button>
    </form>
    <br>
    <a href="manage_events.php">Back</a>
</body>
</html>
