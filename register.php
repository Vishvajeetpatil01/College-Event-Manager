<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch registered events along with category name
$stmt = $conn->prepare("
    SELECT 
        r.id, 
        e.event_name, 
        r.team_name, 
        r.leader_name, 
        r.contact, 
        r.email, 
        r.team_size, 
        ec.category_name 
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    LEFT JOIN event_categories ec ON r.category_id = ec.id
    WHERE r.user_id = ?
    ORDER BY r.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Registered Events - Virtuosic 2025</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #121212;
            --secondary-bg: #1e1e1e;
            --accent-color: #ff6b00;
            --accent-hover: #ff8c00;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-primary);
            min-height: 100vh;
            padding-top: 40px;
        }

        .container {
            max-width: 1200px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: var(--accent-color);
            margin-bottom: 30px;
            font-weight: 700;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 2px;
        }

        .table {
            background-color: var(--secondary-bg);
            color: var(--text-primary);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            margin-top: 30px !important;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: var(--accent-color);
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px;
            text-align: center;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(255, 107, 0, 0.1);
            transform: scale(1.01);
        }

        .table tbody td {
            border-bottom: 1px solid #333;
            padding: 12px 15px;
            vertical-align: middle;
            text-align: center;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .no-events {
            background-color: var(--secondary-bg);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-out;
        }

        .text-danger {
            color: #ff4d4d !important;
        }

        /* Pulse animation for table rows */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 107, 0, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 107, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 107, 0, 0); }
        }

        .table tbody tr {
            position: relative;
        }

        .table tbody tr::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 5px;
            pointer-events: none;
        }

        .table tbody tr:hover::after {
            animation: pulse 1.5s infinite;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .table thead {
                display: none;
            }
            
            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            
            .table tr {
                margin-bottom: 20px;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }
            
            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid #333;
            }
            
            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 45%;
                padding-right: 15px;
                font-weight: 600;
                text-align: left;
                color: var(--accent-color);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>My Registered Events</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Event Name</th>
                        <th>Team Name</th>
                        <th>Team Leader</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Team Size</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="#"><?= htmlspecialchars($row['id']) ?></td>
                            <td data-label="Event Name"><?= htmlspecialchars($row['event_name']) ?></td>
                            <td data-label="Team Name"><?= htmlspecialchars($row['team_name']) ?></td>
                            <td data-label="Team Leader"><?= htmlspecialchars($row['leader_name']) ?></td>
                            <td data-label="Contact"><?= htmlspecialchars($row['contact']) ?></td>
                            <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                            <td data-label="Team Size"><?= htmlspecialchars($row['team_size']) ?></td>
                            <td data-label="Category"><?= htmlspecialchars($row['category_name'] ?: 'N/A') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-events">
            <p class="text-danger">You have not registered for any events yet.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>