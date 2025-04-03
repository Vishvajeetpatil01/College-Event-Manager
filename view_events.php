<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch events from database with categories
$events = $conn->query("
    SELECT e.*, GROUP_CONCAT(ec.category_name SEPARATOR ', ') AS categories 
    FROM events e
    LEFT JOIN event_categories ec ON e.id = ec.event_id
    GROUP BY e.id
    ORDER BY e.event_date, e.event_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Events - Virtuosic 2025</title>
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
            margin: 40px auto;
            padding: 20px;
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
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .header h2 {
            color: var(--accent-color);
            font-size: 2.2rem;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .header h2::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent-color);
        }
        
        .header p {
            color: var(--text-muted);
            font-size: 1rem;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .event-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 107, 0, 0.1);
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease-out;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(255, 107, 0, 0.2);
        }
        
        .event-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .event-image::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        }
        
        .event-date {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
            z-index: 1;
        }
        
        .event-content {
            padding: 20px;
        }
        
        .event-title {
            color: var(--accent-color);
            font-size: 1.4rem;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .event-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        
        .event-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .event-meta i {
            color: var(--accent-color);
        }
        
        .event-description {
            margin-bottom: 20px;
            line-height: 1.6;
            color: var(--text-muted);
        }
        
        .event-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .category-tag {
            background: rgba(255, 107, 0, 0.1);
            color: var(--accent-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .event-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .register-btn {
            background: var(--accent-color);
            color: var(--primary-bg);
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .register-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
        }
        
        .event-prizes {
            font-size: 0.9rem;
            color: var(--text-muted);
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
        }
        
        .back-btn:hover {
            background: var(--accent-color);
            color: var(--primary-bg);
            transform: translateY(-2px);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .header h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h2><i class="fas fa-calendar-alt"></i> Available Events</h2>
            <p>Register for exciting events at Virtuosic 2025</p>
        </div>

        <div class="events-grid">
            <?php while ($event = $events->fetch_assoc()): ?>
                <div class="event-card">
                    <div class="event-image" style="background-image: url('<?= htmlspecialchars($event['image'] ?: 'images/event-default.jpg') ?>')">
                        <div class="event-date">
                            <i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($event['event_date'])) ?>
                        </div>
                    </div>
                    <div class="event-content">
                        <h3 class="event-title"><?= htmlspecialchars($event['event_name']) ?></h3>
                        
                        <div class="event-meta">
                            <span><i class="far fa-clock"></i> <?= htmlspecialchars($event['event_time']) ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['event_venue']) ?></span>
                            <span><i class="fas fa-users"></i> Team: <?= htmlspecialchars($event['team_size']) ?></span>
                        </div>
                        
                        <?php if (!empty($event['categories'])): ?>
                        <div class="event-categories">
                            <?php foreach (explode(', ', $event['categories']) as $category): ?>
                                <span class="category-tag"><?= htmlspecialchars($category) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <p class="event-description">
                            <?= htmlspecialchars($event['description'] ?: 'No description available') ?>
                        </p>
                        
                        <div class="event-actions">
                            <div class="event-prizes">
                                <i class="fas fa-trophy"></i> Prizes: ₹<?= htmlspecialchars($event['first_prize']) ?>/₹<?= htmlspecialchars($event['second_prize']) ?>/₹<?= htmlspecialchars($event['third_prize']) ?>
                            </div>
                            <a href="register_event.php?event_id=<?= $event['id'] ?>" class="register-btn">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="text-center">
            <a href="user_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        // Add staggered animation to event cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.event-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>