<?php
session_start();
include 'config.php';

// Fetch event-wise registrations with proper field names
$registrations = $conn->query("
    SELECT r.id, r.team_name, r.leader_name, r.email, r.registered_at, 
           e.event_name, e.event_date
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    ORDER BY e.event_name, r.registered_at DESC
");

// Group data by event
$grouped_data = [];
while ($row = $registrations->fetch_assoc()) {
    $grouped_data[$row['event_name']][] = $row;
}

// Handle deletion of a registration
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM registrations WHERE id = $id");
    $_SESSION['message'] = "Registration deleted successfully!";
    header("Location: manage_registrations.php");
    exit();
}

// Display success message if exists
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Registrations - Virtuosic Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-bg: #0d0d0d;
            --secondary-bg: #1a1a1a;
            --accent-color: #ff6b00;
            --accent-hover: #ff8c00;
            --text-color: #f0f0f0;
            --text-muted: #b3b3b3;
            --card-bg: #252525;
            --success-color: #4CAF50;
            --error-color: #f44336;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-color);
            min-height: 100vh;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .header h2 {
            color: var(--accent-color);
            font-size: 2rem;
            position: relative;
        }
        
        .header h2::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent-color);
        }
        
        .back-btn {
            background: var(--secondary-bg);
            color: var(--text-color);
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            border: 2px solid var(--accent-color);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-btn:hover {
            background: var(--accent-color);
            color: var(--primary-bg);
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
            animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }
        
        .event-section {
            margin-bottom: 40px;
            animation: fadeIn 0.8s ease-out;
        }
        
        .event-title {
            background: var(--accent-color);
            color: var(--primary-bg);
            padding: 12px 20px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3);
        }
        
        .table-container {
            background: var(--secondary-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 107, 0, 0.1);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        th {
            background: rgba(255, 107, 0, 0.1);
            color: var(--accent-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
        
        tr:hover {
            background: rgba(255, 107, 0, 0.03);
        }
        
        .action-btn {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        
        .delete-btn {
            background: rgba(244, 67, 54, 0.1);
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }
        
        .delete-btn:hover {
            background: var(--error-color);
            color: white;
        }
        
        .empty-message {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
            font-size: 1.1rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            th, td {
                padding: 10px;
                font-size: 0.9rem;
            }
            
            .action-btn {
                padding: 6px 8px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h2><i class="fas fa-clipboard-list"></i> Manage Registrations</h2>
            <a href="admin_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if(isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (!empty($grouped_data)): ?>
            <?php foreach ($grouped_data as $event_name => $registrations): ?>
                <div class="event-section">
                    <div class="event-title">
                        <i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($event_name) ?>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Team Name</th>
                                    <th>Leader</th>
                                    <th>Email</th>
                                    <th>Registered On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reg['id']) ?></td>
                                    <td><?= htmlspecialchars($reg['team_name']) ?></td>
                                    <td><?= htmlspecialchars($reg['leader_name']) ?></td>
                                    <td><?= htmlspecialchars($reg['email']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($reg['registered_at'])) ?></td>
                                    <td>
                                        <a href="manage_registrations.php?delete=<?= $reg['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this registration?')">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-message">
                <i class="fas fa-info-circle"></i> No event registrations found.
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>