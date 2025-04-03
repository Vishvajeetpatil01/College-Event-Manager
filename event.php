<?php
session_start();

// Redirect if no event is selected
if (!isset($_SESSION['event_details'])) {
    header("Location: view_events.php");
    exit();
}

$event = $_SESSION['event_details'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['event_name']) ?> - Event Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 {
            color: #007bff;
        }
        .event-box {
            background: #007bff;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: left;
        }
        .event-box h3 {
            margin: 0;
        }
        .register-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background: white;
            color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .register-btn:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2><?= htmlspecialchars($event['event_name']) ?></h2>
        <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
        <p><strong>Time:</strong> <?= htmlspecialchars($event['event_time']) ?></p>
        <p><strong>Venue:</strong> <?= htmlspecialchars($event['event_venue']) ?></p>
        <p><strong>Team Size:</strong> <?= htmlspecialchars($event['team_size']) ?></p>
        <p><strong>Entry Fee:</strong> ₹<?= htmlspecialchars($event['entry_fee']) ?></p>
        <h3>🏆 Prizes 🏆</h3>
        <p><strong>1st Prize:</strong> ₹<?= htmlspecialchars($event['first_prize']) ?></p>
        <p><strong>2nd Prize:</strong> ₹<?= htmlspecialchars($event['second_prize']) ?></p>
        <p><strong>3rd Prize:</strong> ₹<?= htmlspecialchars($event['third_prize']) ?></p>

        <a href="register.php?event_id=<?= $event['id'] ?>" class="register-btn">Register Now</a>
        <a href="view_events.php" class="register-btn" style="background: red; color: white;">Back to Events</a>
    </div>

</body>
</html>
