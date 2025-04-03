<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtuosic - College Event</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
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
            line-height: 1.6;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary-bg), var(--secondary-bg));
            color: var(--accent-color);
            padding: 40px 20px;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            animation: fadeInDown 0.8s ease-out;
        }
        
        header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
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
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease-out 0.5s both;
            border: 1px solid rgba(255, 107, 0, 0.1);
        }
        
        h2 {
            color: var(--accent-color);
            font-size: 2rem;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
            animation: textGlow 2s infinite alternate;
        }
        
        h2::after {
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
        
        h2:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        h3 {
            color: var(--accent-color);
            font-size: 1.5rem;
            margin: 30px 0 15px;
            position: relative;
            padding-bottom: 8px;
        }
        
        h3::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent-color);
        }
        
        p {
            color: var(--text-muted);
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        ul {
            list-style-type: none;
            padding: 0;
            margin: 20px 0;
        }
        
        li {
            background: rgba(255, 107, 0, 0.1);
            margin: 15px 0;
            padding: 15px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
            position: relative;
            overflow: hidden;
        }
        
        li::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 107, 0, 0.2), transparent);
            transition: all 0.5s ease;
        }
        
        li:hover {
            transform: translateX(10px);
            background: rgba(255, 107, 0, 0.2);
        }
        
        li:hover::before {
            left: 100%;
        }
        
        .footer {
            margin-top: 50px;
            padding: 20px;
            background: linear-gradient(135deg, var(--secondary-bg), var(--primary-bg));
            color: var(--text-muted);
            text-align: center;
            font-size: 0.9rem;
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
                padding: 30px 15px;
                font-size: 1.8rem;
            }
            
            .container {
                padding: 20px;
                margin: 20px 15px;
            }
            
            nav a {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            h3 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

<header>
    Welcome to Virtuosic - College Event
</header>

<nav>
    <a href="login.php">Login</a>
    <a href="signup.php">Sign Up</a>
    <a href="admin_signup.php">Admin Sign Up</a>
    <a href="contact.php">Contact Us</a>
</nav>

<div class="container">
    <h2>About Virtuosic</h2>
    <p>Virtuosic is a dynamic college event featuring exciting competitions like Project Exhibition, Debate, Poster Presentation, LAN Gaming, and more!</p>
    
    <h3>Why Participate?</h3>
    <ul>
        <li>Showcase your skills and creativity.</li>
        <li>Compete with the best minds from different colleges.</li>
        <li>Win exciting prizes and certificates.</li>
        <li>Network with peers and industry experts.</li>
    </ul>

    <h3>Event Categories</h3>
    <ul>
        <li>🎤 Debate Competition</li>
        <li>🎨 Poster Presentation</li>
        <li>🖥️ Project Exhibition</li>
        <li>🎮 LAN Gaming</li>
    </ul>

    <p>Register now and be part of the ultimate college fest!</p>
</div>

<div class="footer">
    &copy; 2025 Virtuosic. All rights reserved.
</div>

</body>
</html>