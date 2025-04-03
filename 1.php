<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Virtuosic</title>
    <style>
        /* Black Background */
        body {
            font-family: Arial, sans-serif;
            background: black;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Login Box */
        .login-container {
            background: rgba(30, 30, 30, 0.95);
            padding: 25px;
            width: 350px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.2);
            animation: fadeIn 0.8s ease-in-out;
        }

        /* Fade-in Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Heading */
        h2 {
            margin-bottom: 20px;
            color: #ff9800;
        }

        /* Input Fields */
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #555;
            border-radius: 5px;
            font-size: 16px;
            background: black;
            color: white;
            transition: 0.3s;
        }

        input:focus {
            border-color: #ff9800;
            outline: none;
            box-shadow: 0 0 5px rgba(255, 152, 0, 0.8);
        }

        /* Submit Button */
        .login-btn {
            background: #ff9800;
            color: black;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: #e68900;
        }

        /* Error Message */
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Back Link */
        .back-link {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #ff9800;
            font-weight: bold;
            transition: 0.3s;
        }

        .back-link:hover {
            color: #e68900;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="login-btn">Login</button>
        </form>
        <a href="index.php" class="back-link">Back to Home</a>
    </div>

</body>
</html>
