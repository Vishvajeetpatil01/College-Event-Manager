<?php
session_start();
include 'config.php';

// Pagination setup
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page-1) * $results_per_page;

// Fetch total users
$total_users = $conn->query("SELECT COUNT(id) AS total FROM users")->fetch_assoc()['total'];
$total_pages = ceil($total_users / $results_per_page);

// Fetch users with pagination
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT $start_from, $results_per_page");

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    $_SESSION['message'] = "User deleted successfully!";
    header("Location: manage_users.php");
    exit;
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
    <title>Manage Users - Virtuosic Admin</title>
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
        
        .edit-btn {
            background: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .edit-btn:hover {
            background: var(--success-color);
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
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .pagination a {
            color: var(--text-color);
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid rgba(255, 107, 0, 0.3);
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .pagination a.active {
            background: var(--accent-color);
            color: var(--primary-bg);
            border-color: var(--accent-color);
        }
        
        .pagination a:hover:not(.active) {
            background: rgba(255, 107, 0, 0.1);
        }
        
        .role-admin {
            color: var(--accent-color);
            font-weight: 600;
        }
        
        .role-user {
            color: var(--success-color);
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
            <h2><i class="fas fa-users"></i> Manage Users</h2>
            <a href="admin_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if(isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['contact'] ?? 'N/A') ?></td>
                        <td>
                            <span class="role-<?= $row['role'] ?>">
                                <?= ucfirst(htmlspecialchars($row['role'])) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $row['id'] ?>" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="manage_users.php?delete=<?= $row['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="manage_users.php?page=<?= $page-1 ?>"><i class="fas fa-chevron-left"></i></a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="manage_users.php?page=<?= $i ?>" <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="manage_users.php?page=<?= $page+1 ?>"><i class="fas fa-chevron-right"></i></a>
            <?php endif; ?>
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