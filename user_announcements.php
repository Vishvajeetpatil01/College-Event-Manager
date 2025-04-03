<?php
session_start();
include 'config.php';

// Fetch all published announcements
$announcements = $conn->query("SELECT * FROM announcements WHERE is_published = TRUE ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Virtuosic</title>
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
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h2 {
            color: var(--accent-color);
            font-size: 2.5rem;
            position: relative;
            display: inline-block;
        }
        
        .header h2::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
        }
        
        .announcements-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .announcement-card {
            background: var(--secondary-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 107, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(255, 107, 0, 0.2);
        }
        
        .announcement-header {
            padding: 20px;
            background: rgba(255, 107, 0, 0.1);
            border-bottom: 1px solid rgba(255, 107, 0, 0.2);
        }
        
        .announcement-title {
            color: var(--accent-color);
            font-size: 1.4rem;
            margin: 0 0 10px 0;
        }
        
        .announcement-date {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .announcement-content {
            padding: 20px;
            line-height: 1.7;
        }
        
        .empty-message {
            text-align: center;
            padding: 50px;
            color: var(--text-muted);
            font-size: 1.2rem;
            grid-column: 1 / -1;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .announcements-container {
                grid-template-columns: 1fr;
            }
            
            .header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-bullhorn"></i> Announcements</h2>
            <p>Stay updated with the latest news and updates</p>
        </div>

        <div class="announcements-container">
            <?php if ($announcements->num_rows > 0): ?>
                <?php while ($announcement = $announcements->fetch_assoc()): ?>
                    <div class="announcement-card">
                        <div class="announcement-header">
                            <h3 class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></h3>
                            <div class="announcement-date">
                                <?= date('F j, Y \a\t g:i A', strtotime($announcement['created_at'])) ?>
                            </div>
                        </div>
                        <div class="announcement-content">
                            <?= nl2br(htmlspecialchars($announcement['content'])) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-message">
                    <i class="fas fa-info-circle"></i> No announcements available at the moment.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>