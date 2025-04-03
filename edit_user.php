<?php
session_start();
include 'config.php';

// Check admin session
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Get user details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM users WHERE id=$id");
    $user = $result->fetch_assoc();
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name=?, role=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $role, $id);

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully!'); window.location='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error updating user.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        h2 { color: #007bff; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 10px; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background: #0056b3; }
        .back-btn { margin-top: 10px; display: inline-block; text-decoration: none; background: #007bff; color: white; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Edit User</h2>
        <form method="POST">
            <input type="text" name="name" value="<?= $user['name'] ?>" required>
            <select name="role">
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <button type="submit">Update</button>
        </form>
        <a href="manage_users.php" class="back-btn">Back</a>
    </div>

</body>
</html>
