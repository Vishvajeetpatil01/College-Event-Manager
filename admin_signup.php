<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $secret_code = trim($_POST['secret_code']);

    $correct_secret_code = "VIRTUOSIC2025";

    // Validate inputs
    if (empty($name) || empty($email) || empty($contact) || empty($password) || empty($secret_code)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (!preg_match('/^[0-9]{10}$/', $contact)) {
        $error = "Invalid contact number (10 digits required)!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters!";
    } elseif ($secret_code !== $correct_secret_code) {
        $error = "Invalid secret code!";
    } else {
        // Check if email or contact exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR contact = ?");
        $stmt->bind_param("ss", $email, $contact);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Email or contact number already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, contact, password, role) VALUES (?, ?, ?, ?, 'admin')");
            $stmt->bind_param("ssss", $name, $email, $contact, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['admin_id'] = $conn->insert_id;
                $_SESSION['role'] = 'admin';
                $success = "Admin registration successful! Redirecting...";
                header("Refresh: 2; url=admin_dashboard.php");
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup - Virtuosic</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #0d0d0d;
            --secondary-bg: #1a1a1a;
            --accent-color: #ff6b00;
            --accent-hover: #ff8c00;
            --text-color: #f0f0f0;
            --text-muted: #b3b3b3;
            --error-color: #ff3333;
            --success-color: #33ff33;
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            animation: fadeIn 1s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .admin-signup-container {
            background: var(--secondary-bg);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(255, 107, 0, 0.2);
            transform: scale(0.95);
            animation: scaleUp 0.5s ease-out forwards;
        }
        
        @keyframes scaleUp {
            to { transform: scale(1); }
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .admin-header h2 {
            color: var(--accent-color);
            font-size: 2rem;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
            animation: textGlow 2s infinite alternate;
        }
        
        @keyframes textGlow {
            from { text-shadow: 0 0 5px rgba(255, 107, 0, 0.5); }
            to { text-shadow: 0 0 15px rgba(255, 107, 0, 0.8); }
        }
        
        .admin-header h2::after {
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
        
        .admin-header:hover h2::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .admin-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .admin-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 107, 0, 0.3);
            border-radius: 8px;
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
        }
        
        .form-group input::placeholder {
            color: var(--text-muted);
        }
        
        .contact-input {
            position: relative;
        }
        
        .contact-input::before {
            content: "+91";
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            background: rgba(255, 107, 0, 0.1);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            z-index: 1;
        }
        
        .contact-input input {
            padding-left: 55px !important;
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
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
        }
        
        .submit-btn:active {
            transform: translateY(-1px);
        }
        
        .submit-btn::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -60%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            transition: all 0.3s;
        }
        
        .submit-btn:hover::after {
            left: 100%;
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
        
        .alert-error {
            background: rgba(255, 51, 51, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }
        
        .alert-success {
            background: rgba(51, 255, 51, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }
        
        .password-strength {
            height: 5px;
            background: var(--secondary-bg);
            margin-top: 5px;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            background: var(--error-color);
            transition: width 0.3s, background 0.3s;
        }
        
        @media (max-width: 600px) {
            .admin-signup-container {
                padding: 30px 20px;
            }
            
            .admin-header h2 {
                font-size: 1.5rem;
            }
            
            .contact-input::before {
                left: 10px;
            }
            
            .contact-input input {
                padding-left: 50px !important;
            }
        }
    </style>
</head>
<body>
    <div class="admin-signup-container">
        <div class="admin-header">
            <h2>Admin Registration</h2>
            <p>Restricted access - College authorities only</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form class="admin-form" method="POST">
            <div class="form-group">
                <input type="text" name="name" placeholder="Full Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Official Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group contact-input">
                <input type="tel" name="contact" placeholder="Contact Number" required 
                       pattern="[0-9]{10}" title="10 digit phone number"
                       value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Password (min 8 characters)" required minlength="8">
                <div class="password-strength">
                    <div class="strength-meter" id="strengthMeter"></div>
                </div>
            </div>
            
            <div class="form-group">
                <input type="text" name="secret_code" placeholder="Enter Secret College Code" required>
            </div>
            
            <button type="submit" class="submit-btn">Register as Admin</button>
        </form>
    </div>

    <script>
        // Password strength indicator
        document.querySelector('input[name="password"]').addEventListener('input', function(e) {
            const password = e.target.value;
            const meter = document.getElementById('strengthMeter');
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9!@#$%^&*]/)) strength += 25;
            
            meter.style.width = strength + '%';
            
            if (strength < 50) {
                meter.style.background = 'var(--error-color)';
            } else if (strength < 75) {
                meter.style.background = 'orange';
            } else {
                meter.style.background = 'var(--success-color)';
            }
        });

        // Contact number validation
        document.querySelector('input[name="contact"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>