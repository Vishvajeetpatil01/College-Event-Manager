<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Virtuosic</title>
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
        
        header {
            background: linear-gradient(135deg, var(--primary-bg), var(--secondary-bg));
            color: var(--accent-color);
            padding: 25px 20px;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), transparent);
            animation: headerUnderline 3s infinite;
        }
        
        @keyframes headerUnderline {
            0% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
            100% { transform: translateX(100%); }
        }
        
        nav {
            margin: 30px auto;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            animation: slideDown 0.5s ease-out 0.3s both;
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
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        nav a:hover {
            background-color: var(--accent-color);
            color: var(--primary-bg);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(255, 107, 0, 0.4);
        }
        
        nav a i {
            font-size: 16px;
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: var(--secondary-bg);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease-out 0.5s both;
            border: 1px solid rgba(255, 107, 0, 0.1);
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
        
        .welcome-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .welcome-section h2 {
            color: var(--accent-color);
            font-size: 2.2rem;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .welcome-section h2::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--accent-color);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }
        
        .welcome-section:hover h2::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .welcome-section p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .stats-section {
            margin-top: 40px;
        }
        
        .stats-section h3 {
            color: var(--accent-color);
            font-size: 1.8rem;
            margin-bottom: 25px;
            text-align: center;
            position: relative;
            padding-bottom: 10px;
        }
        
        .stats-section h3::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--accent-color);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.2);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 15px;
        }
        
        .stat-card h4 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        
        .stat-card p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .footer {
            margin-top: 50px;
            padding: 20px;
            background: linear-gradient(135deg, var(--secondary-bg), var(--primary-bg));
            color: var(--text-muted);
            text-align: center;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 107, 0, 0.2);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            header {
                padding: 20px 15px;
                font-size: 1.8rem;
            }
            
            .container {
                padding: 20px;
                margin: 20px 15px;
            }
            
            nav a {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .welcome-section h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<header>
    Admin Dashboard - Virtuosic
</header>

<nav>
    <a href="manage_events.php"><i class="fas fa-calendar-alt"></i> Manage Events</a>
    <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="manage_registrations.php"><i class="fas fa-clipboard-list"></i> Registrations</a>
    <a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<div class="container">
    <div class="welcome-section">
        <h2>Welcome, Admin!</h2>
        <p>Manage all aspects of the Virtuosic event from this powerful dashboard. Monitor statistics, create events, and manage participants with ease.</p>
    </div>

    <div class="stats-section">
        <h3>Quick Stats</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-calendar"></i>
                <h4>24</h4>
                <p>Total Events</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h4>1,248</h4>
                <p>Registered Users</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-trophy"></i>
                <h4>36</h4>
                <p>Winners Announced</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-ticket-alt"></i>
                <h4>₹84,500</h4>
                <p>Total Revenue</p>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    &copy; 2025 Virtuosic Admin Panel. All rights reserved.
</div>

</body>
</html>