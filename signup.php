<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email or contact already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR contact = ?");
    $stmt->bind_param("ss", $email, $contact);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email or contact number already registered!";
    } else {
        // Insert new user with contact
        $stmt = $conn->prepare("INSERT INTO users (name, email, contact, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $contact, $password);
        if ($stmt->execute()) {
            $success = "Registration successful! <a href='login.php'>Login Here</a>";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Virtuosic</title>
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
        
        .signup-container {
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
        
        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .signup-header h2 {
            color: var(--accent-color);
            font-size: 2rem;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .signup-header h2::after {
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
        
        .signup-header:hover h2::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .signup-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .signup-form {
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
        }
        
        .submit-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
        }
        
        .submit-btn:active {
            transform: translateY(-1px);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
        }
        
        .login-link a {
            color: var(--accent-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--accent-hover);
            text-decoration: underline;
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
        
        /* Phone number input styling */
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
        }
        
        .contact-input input {
            padding-left: 55px !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 600px) {
            .signup-container {
                padding: 30px 20px;
            }
            
            .signup-header h2 {
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
    <div class="signup-container">
        <div class="signup-header">
            <h2>Create Your Account</h2>
            <p>Join Virtuosic and participate in exciting events</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form class="signup-form" method="POST">
            <div class="form-group">
                <input type="text" name="name" placeholder="Full Name" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            
            <div class="form-group contact-input">
                <input type="tel" name="contact" placeholder="Contact Number" pattern="[0-9]{10}" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="submit-btn">Sign Up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>

    <script>
        // Validate contact number input
        document.querySelector('input[name="contact"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    </script>
</body>
</html>