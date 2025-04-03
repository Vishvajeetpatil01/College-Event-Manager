<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$user_stmt = $conn->prepare("SELECT name, email, contact FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $user_name = $user['name'];
    $user_email = $user['email'];
    $user_contact = $user['contact'];
} else {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check event_id
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    header("Location: view_events.php");
    exit();
}

$event_id = intval($_GET['event_id']);

// Fetch event details with poster image
$stmt = $conn->prepare("SELECT id, event_name, event_description, event_date, event_time, event_venue, image, team_size, entry_fee, first_prize, second_prize, third_prize FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
    $event_poster = $event['image'] ?: 'images/event-default.jpg';
} else {
    die("<script>alert('Event not found!'); window.location='view_events.php';</script>");
}

// Fetch categories, coordinators, etc. (same as before)
// ... [previous code for fetching categories and coordinators]

// Handle registration (same as before)
// ... [previous registration handling code]
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['event_name']) ?> - Virtuosic 2025</title>
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
        
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('<?= htmlspecialchars($event_poster) ?>');
            background-size: cover;
            background-position: center;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            position: relative;
            animation: fadeIn 1s ease-out;
        }
        
        .hero-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            z-index: 2;
            animation: fadeInUp 1s ease-out 0.3s both;
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
        
        .hero img {
            width: 150px;
            filter: drop-shadow(0 0 10px rgba(255, 107, 0, 0.7));
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.7);
            margin: 0;
            color: var(--accent-color);
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
        }
        
        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .event-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        
        .detail-card {
            background: var(--secondary-bg);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
        }
        
        .detail-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.2);
        }
        
        .detail-card h3 {
            color: var(--accent-color);
            margin-bottom: 10px;
            font-size: 1.3rem;
        }
        
        .prize-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        
        .prize-card {
            background: var(--secondary-bg);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .prize-card:hover {
            transform: translateY(-5px);
        }
        
        .prize-card h4 {
            color: var(--accent-color);
            margin-bottom: 10px;
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .category-card {
            background: var(--secondary-bg);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.2);
        }
        
        .category-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
        
        .category-content {
            padding: 20px;
        }
        
        .category-content h3 {
            color: var(--accent-color);
            margin-bottom: 10px;
        }
        
        .coordinator-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .coordinator-card {
            background: var(--secondary-bg);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .coordinator-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.2);
        }
        
        .coordinator-card h3 {
            color: var(--accent-color);
            margin-bottom: 10px;
        }
        
        .register-section {
            background: var(--secondary-bg);
            border-radius: 15px;
            padding: 30px;
            margin: 50px auto;
            max-width: 800px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 107, 0, 0.1);
        }
        
        .register-btn {
            background: var(--accent-color);
            color: var(--primary-bg);
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .register-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
        }
        
        #registrationForm {
            display: none;
            animation: fadeIn 0.5s ease-out;
            margin-top: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--accent-color);
            font-weight: 600;
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
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .alert-danger {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }
        
        .submit-btn {
            background: var(--accent-color);
            color: var(--primary-bg);
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            background: var(--accent-hover);
        }
        
        .team-member-fields {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .member-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .member-row .form-control {
            flex: 1;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .prize-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>

<!-- Hero Section with Event Poster -->
<div class="hero">
    <div class="hero-content">
        <img src="images/logo.png" alt="Virtuosic 2025 Logo">
        <h1><?= htmlspecialchars($event['event_name']) ?></h1>
        <p><?= htmlspecialchars($event['event_description']) ?></p>
    </div>
</div>

<div class="container">
    <!-- Event Details Section -->
    <section class="text-center">
        <h2 class="section-title">Event Details</h2>
        <div class="event-details-grid">
            <div class="detail-card">
                <h3><i class="far fa-calendar-alt"></i> Date</h3>
                <p><?= htmlspecialchars($event['event_date']) ?></p>
            </div>
            <div class="detail-card">
                <h3><i class="far fa-clock"></i> Time</h3>
                <p><?= htmlspecialchars($event['event_time']) ?></p>
            </div>
            <div class="detail-card">
                <h3><i class="fas fa-map-marker-alt"></i> Venue</h3>
                <p><?= htmlspecialchars($event['event_venue']) ?></p>
            </div>
            <div class="detail-card">
                <h3><i class="fas fa-users"></i> Team Size</h3>
                <p><?= htmlspecialchars($event['team_size']) ?></p>
            </div>
            <div class="detail-card">
                <h3><i class="fas fa-ticket-alt"></i> Entry Fee</h3>
                <p>₹<?= htmlspecialchars($event['entry_fee']) ?></p>
            </div>
        </div>
    </section>

    <!-- Prizes Section -->
    <section class="mt-5 text-center">
        <h2 class="section-title">🏆 Prizes</h2>
        <div class="prize-grid">
            <div class="prize-card">
                <h4>🥇 1st Prize</h4>
                <p>₹<?= htmlspecialchars($event['first_prize']) ?></p>
            </div>
            <div class="prize-card">
                <h4>🥈 2nd Prize</h4>
                <p>₹<?= htmlspecialchars($event['second_prize']) ?></p>
            </div>
            <div class="prize-card">
                <h4>🥉 3rd Prize</h4>
                <p>₹<?= htmlspecialchars($event['third_prize']) ?></p>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <?php if (!empty($categories)): ?>
    <section class="mt-5">
        <h2 class="section-title text-center">Categories</h2>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <div class="category-image" style="background-image: url('<?= htmlspecialchars($category['category_image'] ?: 'images/category-default.jpg') ?>')"></div>
                    <div class="category-content">
                        <h3><?= htmlspecialchars($category['category_name']) ?></h3>
                        <p><?= htmlspecialchars($category['category_description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Coordinators Section -->
    <?php if (!empty($coordinators)): ?>
    <section class="mt-5">
        <h2 class="section-title text-center">Event Coordinators</h2>
        <div class="coordinator-grid">
            <?php foreach ($coordinators as $coordinator): ?>
                <div class="coordinator-card">
                    <h3><?= htmlspecialchars($coordinator['coordinator_name']) ?></h3>
                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($coordinator['coordinator_contact']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Registration Section -->
    <section class="register-section">
        <div class="text-center">
            <h2 class="section-title">Register Now</h2>
            <p>Join <?= htmlspecialchars($event['event_name']) ?> by filling out the registration form</p>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>
            
            <button id="registerBtn" class="register-btn">
                <i class="fas fa-user-plus"></i> Register Now
            </button>
            
            <form id="registrationForm" method="POST" action="">
                <input type="hidden" name="register" value="1">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <input type="hidden" name="entry_fee" value="<?= $event['entry_fee'] ?>">
                
                <div class="form-group">
                    <label>Team Name</label>
                    <input type="text" name="team_name" class="form-control" placeholder="Enter your team name" required>
                </div>
                
                <div class="form-group">
                    <label>Team Leader Name</label>
                    <input type="text" name="leader_name" class="form-control" value="<?= htmlspecialchars($user_name) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Team Leader Contact</label>
                    <input type="tel" name="contact" class="form-control" value="<?= htmlspecialchars($user_contact) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_email) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Number of Team Members</label>
                    <select class="form-control" name="team_size" id="teamSizeSelect" required onchange="generateTeamMemberInputs()">
                        <option value="" disabled selected>Select number of members</option>
                        <?php for ($i = 1; $i <= $event['team_size']; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div id="teamMemberFields" class="team-member-fields"></div>
                
                <?php if (!empty($categories)): ?>
                <div class="form-group">
                    <label>Select Your Category</label>
                    <select class="form-control" name="category" required>
                        <option value="" disabled selected>Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <p>Entry Fee: <strong>₹<?= htmlspecialchars($event['entry_fee']) ?></strong></p>
                </div>
                
                <button type="submit" class="submit-btn" onclick="disableSubmit(this)">
                    <i class="fas fa-paper-plane"></i> Submit Registration
                </button>
            </form>
        </div>
    </section>
</div>

<script>
    // Toggle registration form
    document.getElementById("registerBtn").addEventListener("click", function(e) {
        e.preventDefault();
        const form = document.getElementById("registrationForm");
        form.style.display = form.style.display === "none" ? "block" : "none";
        form.scrollIntoView({ behavior: 'smooth' });
    });

    // Generate team member inputs
    function generateTeamMemberInputs() {
        const teamSize = document.getElementById('teamSizeSelect').value;
        const container = document.getElementById('teamMemberFields');
        container.innerHTML = '';
        
        if (teamSize > 1) {
            container.innerHTML = '<h4>Team Members</h4>';
            
            for (let i = 2; i <= teamSize; i++) {
                container.innerHTML += `
                    <div class="member-row">
                        <input type="text" class="form-control" placeholder="Member ${i} Name" 
                               name="team_members[${i}][name]" required>
                        <input type="tel" class="form-control" placeholder="Member ${i} Contact" 
                               name="team_members[${i}][contact]" required>
                    </div>
                `;
            }
        }
    }

    // Disable submit button on form submission
    function disableSubmit(btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        return true;
    }
</script>
</body>
</html>