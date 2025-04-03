<?php
session_start();
include 'config.php';


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO announcements (title, content, is_published) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $is_published);
    
    if ($stmt->execute()) {
        $success = "Announcement published successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM announcements WHERE id = $id");
    $_SESSION['message'] = "Announcement deleted successfully!";
    header("Location: admin_announcements.php");
    exit;
}

// Toggle publish status
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $conn->query("UPDATE announcements SET is_published = NOT is_published WHERE id = $id");
    header("Location: admin_announcements.php");
    exit;
}

// Fetch all announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

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
    <title>Manage Announcements - Admin Panel</title>
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
        
        .alert-error {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }
        
        .form-container {
            background: var(--secondary-bg);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 107, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 107, 0, 0.3);
            border-radius: 8px;
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .submit-btn {
            background: var(--accent-color);
            color: var(--primary-bg);
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .submit-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
        }
        
        .announcements-container {
            background: var(--secondary-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 107, 0, 0.1);
        }
        
        .announcement-card {
            padding: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        .announcement-card:hover {
            background: rgba(255, 107, 0, 0.03);
        }
        
        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .announcement-title {
            color: var(--accent-color);
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }
        
        .announcement-date {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .announcement-content {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .announcement-actions {
            display: flex;
            gap: 10px;
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
        
        .toggle-btn {
            background: rgba(33, 150, 243, 0.1);
            color: #2196F3;
            border: 1px solid #2196F3;
        }
        
        .toggle-btn:hover {
            background: #2196F3;
            color: white;
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
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-published {
            background: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .status-draft {
            background: rgba(255, 193, 7, 0.1);
            color: #FFC107;
            border: 1px solid #FFC107;
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
            
            .announcement-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .announcement-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-bullhorn"></i> Manage Announcements</h2>
            <a href="admin_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if(isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h3><i class="fas fa-plus-circle"></i> Create New Announcement</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Enter announcement title" required>
                </div>
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" class="form-control" placeholder="Enter announcement content" required></textarea>
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_published" name="is_published" checked>
                    <label for="is_published">Publish immediately</label>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Publish Announcement
                </button>
            </form>
        </div>

        <div class="announcements-container">
            <h3><i class="fas fa-list"></i> All Announcements</h3>
            <?php if ($announcements->num_rows > 0): ?>
                <?php while ($announcement = $announcements->fetch_assoc()): ?>
                    <div class="announcement-card">
                        <div class="announcement-header">
                            <h3 class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></h3>
                            <div>
                                <span class="status-badge <?= $announcement['is_published'] ? 'status-published' : 'status-draft' ?>">
                                    <?= $announcement['is_published'] ? 'Published' : 'Draft' ?>
                                </span>
                                <span class="announcement-date">
                                    <?= date('M d, Y h:i A', strtotime($announcement['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="announcement-content">
                            <?= nl2br(htmlspecialchars($announcement['content'])) ?>
                        </div>
                        <div class="announcement-actions">
                            <a href="admin_announcements.php?toggle=<?= $announcement['id'] ?>" class="action-btn toggle-btn">
                                <i class="fas fa-toggle-<?= $announcement['is_published'] ? 'on' : 'off' ?>"></i> 
                                <?= $announcement['is_published'] ? 'Unpublish' : 'Publish' ?>
                            </a>
                            <a href="admin_announcements.php?delete=<?= $announcement['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this announcement?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-message">
                    <i class="fas fa-info-circle"></i> No announcements found.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>