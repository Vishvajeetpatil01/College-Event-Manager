<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Virtuosic</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root {
            --primary-bg: #0d0d0d;
            --secondary-bg: #1a1a1a;
            --accent-color: #ff6b00;
            --accent-hover: #ff8c00;
            --text-color: #f0f0f0;
            --text-muted: #b3b3b3;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--primary-bg);
            color: var(--text-color);
            min-height: 100vh;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary-bg), var(--secondary-bg));
            color: var(--accent-color);
            padding: 30px 20px;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            animation: fadeInDown 0.8s ease-out;
        }
        
        nav {
            margin: 30px auto;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            animation: fadeIn 1s ease-out 0.3s both;
        }
        
        nav a {
            text-decoration: none;
            padding: 12px 25px;
            background-color: var(--secondary-bg);
            color: var(--text-color);
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid var(--accent-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        nav a:hover {
            background-color: var(--accent-color);
            color: var(--primary-bg);
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(255, 107, 0, 0.3);
        }
        
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: var(--secondary-bg);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.8s ease-out 0.5s both;
            border: 1px solid rgba(255, 107, 0, 0.1);
        }
        
        .welcome-message {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .welcome-message h2 {
            font-size: 28px;
            color: var(--accent-color);
            margin-bottom: 10px;
            animation: textGlow 2s infinite alternate;
        }
        
        .welcome-message p {
            color: var(--text-muted);
            font-size: 16px;
        }
        
        h3 {
            color: var(--accent-color);
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 10px;
            margin-top: 30px;
            display: inline-block;
        }
        
        ul {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }
        
        li {
            background: rgba(255, 107, 0, 0.1);
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
        }
        
        li:hover {
            transform: translateX(10px);
            background: rgba(255, 107, 0, 0.2);
        }
        
        .footer {
            margin-top: 50px;
            padding: 20px;
            background: linear-gradient(135deg, var(--secondary-bg), var(--primary-bg));
            color: var(--text-muted);
            text-align: center;
            font-size: 14px;
            border-top: 1px solid rgba(255, 107, 0, 0.2);
            animation: fadeIn 1s ease-out;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes textGlow {
            from {
                text-shadow: 0 0 5px rgba(255, 107, 0, 0.5);
            }
            to {
                text-shadow: 0 0 15px rgba(255, 107, 0, 0.8);
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            header {
                padding: 20px 15px;
                font-size: 24px;
            }
            
            .container {
                padding: 20px;
                margin: 20px 15px;
            }
            
            nav a {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<header>
     Virtuosic 2K25...
</header>

<nav>
    <a href="view_events.php">View Events</a>
    <a href="register.php">My Registrations</a>
    <a href="user_announcements.php">Announcements</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <div class="welcome-message">
        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        <p>Check out the latest events and manage your registrations.</p>
    </div>

    <h3>Upcoming Events</h3>
    <ul>
        <li>🎤 Debate Competition - April 5</li>
        <li>🎨 Poster Presentation - April 6</li>
        <li>🖥️ Project Exhibition - April 7</li>
        <li>🎮 LAN Gaming - April 8</li>
    </ul>

    <h3>Recent Announcements</h3>
    <ul>
        <li>📢 Registration closes on April 3.</li>
        <li>🏆 Winners will be announced on April 9.</li>
    </ul>
</div>

<div class="footer">
    &copy; 2025 Virtuosic Event Platform. All rights reserved.
</div>

</body>
</html>