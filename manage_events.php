<?php
session_start();
include 'config.php';

// Handle adding event
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Input sanitization
    $event_name = htmlspecialchars(trim($_POST['event_name']));
    $event_description = htmlspecialchars(trim($_POST['event_description']));
    $event_date = $_POST['event_date'];
    $event_time = htmlspecialchars(trim($_POST['event_time']));
    $event_venue = htmlspecialchars(trim($_POST['event_venue']));
    $team_size = (int)$_POST['team_size'];
    $entry_fee = (int)$_POST['entry_fee'];
    $first_prize = htmlspecialchars(trim($_POST['first_prize']));
    $second_prize = htmlspecialchars(trim($_POST['second_prize']));
    $third_prize = htmlspecialchars(trim($_POST['third_prize']));
    $num_categories = (int)$_POST['num_categories'];
    $num_coordinators = (int)$_POST['num_coordinators'];

    // Image upload handling
    $target_dir = "uploads/";
    $image_name = basename($_FILES["event_image"]["name"]);
    $target_file = $target_dir . uniqid() . '_' . $image_name;
    
    if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file)) {
        // Save event details in DB
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_description, event_date, event_time, event_venue, team_size, entry_fee, first_prize, second_prize, third_prize, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiissss", $event_name, $event_description, $event_date, $event_time, $event_venue, $team_size, $entry_fee, $first_prize, $second_prize, $third_prize, $target_file);

        if ($stmt->execute()) {
            $event_id = $stmt->insert_id;

            // Save categories
            for ($i = 1; $i <= $num_categories; $i++) {
                $category_name = htmlspecialchars(trim($_POST["category_name_$i"]));
                $category_desc = htmlspecialchars(trim($_POST["category_desc_$i"]));
                
                $category_image_name = basename($_FILES["category_image_$i"]["name"]);
                $category_image_path = $target_dir . uniqid() . '_' . $category_image_name;
                
                if (move_uploaded_file($_FILES["category_image_$i"]["tmp_name"], $category_image_path)) {
                    $stmt_category = $conn->prepare("INSERT INTO event_categories (event_id, category_name, category_description, category_image) VALUES (?, ?, ?, ?)");
                    $stmt_category->bind_param("isss", $event_id, $category_name, $category_desc, $category_image_path);
                    $stmt_category->execute();
                }
            }

            // Save coordinators
            for ($i = 1; $i <= $num_coordinators; $i++) {
                $coordinator_name = htmlspecialchars(trim($_POST["coordinator_name_$i"]));
                $coordinator_contact = htmlspecialchars(trim($_POST["coordinator_contact_$i"]));
                
                $stmt_coordinator = $conn->prepare("INSERT INTO event_coordinators (event_id, coordinator_name, coordinator_contact) VALUES (?, ?, ?)");
                $stmt_coordinator->bind_param("iss", $event_id, $coordinator_name, $coordinator_contact);
                $stmt_coordinator->execute();
            }

            // Generate event page
            $event_slug = strtolower(str_replace(' ', '_', $event_name));
            $event_page_path = "events/" . $event_slug . ".php";
            
            // Fetch coordinators for the template
            $coordinators_result = $conn->query("SELECT * FROM event_coordinators WHERE event_id = $event_id");
            $coordinators = [];
            while ($row = $coordinators_result->fetch_assoc()) {
                $coordinators[] = $row;
            }
            
            $page_content = "<?php
                \$event_name = \"$event_name\";
                \$event_description = \"$event_description\";
                \$event_date = \"$event_date\";
                \$event_time = \"$event_time\";
                \$event_venue = \"$event_venue\";
                \$team_size = \"$team_size\";
                \$entry_fee = \"$entry_fee\";
                \$first_prize = \"$first_prize\";
                \$second_prize = \"$second_prize\";
                \$third_prize = \"$third_prize\";
                \$event_image = \"$target_file\";
                
                // Coordinators data
                \$coordinators = " . var_export($coordinators, true) . ";
                
                include '../event_template.php';
            ?>";
            
            file_put_contents($event_page_path, $page_content);

            $_SESSION['message'] = "Event added successfully!";
            header("Location: manage_events.php");
            exit;
        } else {
            $error = "Error adding event: " . $conn->error;
        }
    } else {
        $error = "Image upload failed.";
    }
}

// Fetch events
$events = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

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
    <title>Manage Events - Virtuosic Admin</title>
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
        
        .form-container h3 {
            color: var(--accent-color);
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            background: var(--card-bg);
            border-radius: 10px;
            border-left: 4px solid var(--accent-color);
        }
        
        .form-section h4 {
            color: var(--accent-color);
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.2rem;
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
        
        .form-control::placeholder {
            color: var(--text-muted);
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
        
        .submit-btn:active {
            transform: translateY(-1px);
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
        
        .view-btn {
            background: rgba(33, 150, 243, 0.1);
            color: #2196F3;
            border: 1px solid #2196F3;
        }
        
        .view-btn:hover {
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
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .form-section {
                padding: 15px;
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
            <h2><i class="fas fa-calendar-alt"></i> Manage Events</h2>
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

        <div class="form-container">
            <h3>Add New Event</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <h4><i class="fas fa-info-circle"></i> Basic Information</h4>
                    <div class="form-group">
                        <label for="event_name">Event Name</label>
                        <input type="text" id="event_name" name="event_name" class="form-control" placeholder="Enter event name" required>
                    </div>
                    <div class="form-group">
                        <label for="event_description">Event Description</label>
                        <textarea id="event_description" name="event_description" class="form-control" placeholder="Enter event description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Event Date</label>
                        <input type="date" id="event_date" name="event_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="event_time">Event Time</label>
                        <input type="text" id="event_time" name="event_time" class="form-control" placeholder="HH:MM AM/PM" required>
                    </div>
                    <div class="form-group">
                        <label for="event_venue">Event Venue</label>
                        <input type="text" id="event_venue" name="event_venue" class="form-control" placeholder="Enter venue" required>
                    </div>
                    <div class="form-group">
                        <label for="event_image">Event Image</label>
                        <input type="file" id="event_image" name="event_image" class="form-control" accept="image/*" required>
                    </div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-trophy"></i> Event Rules & Prizes</h4>
                    <div class="form-group">
                        <label for="team_size">Team Size</label>
                        <input type="number" id="team_size" name="team_size" class="form-control" placeholder="Enter team size" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="entry_fee">Entry Fee (₹)</label>
                        <input type="number" id="entry_fee" name="entry_fee" class="form-control" placeholder="Enter entry fee" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="first_prize">First Prize (₹)</label>
                        <input type="text" id="first_prize" name="first_prize" class="form-control" placeholder="Enter first prize" required>
                    </div>
                    <div class="form-group">
                        <label for="second_prize">Second Prize (₹)</label>
                        <input type="text" id="second_prize" name="second_prize" class="form-control" placeholder="Enter second prize" required>
                    </div>
                    <div class="form-group">
                        <label for="third_prize">Third Prize (₹)</label>
                        <input type="text" id="third_prize" name="third_prize" class="form-control" placeholder="Enter third prize" required>
                    </div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-tags"></i> Event Categories</h4>
                    <div class="form-group">
                        <label for="num_categories">Number of Categories</label>
                        <input type="number" id="num_categories" name="num_categories" class="form-control" min="0" required oninput="generateCategoryInputs()">
                    </div>
                    <div id="category_inputs"></div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-user-tie"></i> Event Coordinators</h4>
                    <div class="form-group">
                        <label for="num_coordinators">Number of Coordinators</label>
                        <input type="number" id="num_coordinators" name="num_coordinators" class="form-control" min="1" required oninput="generateCoordinatorInputs()">
                    </div>
                    <div id="coordinator_inputs"></div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus-circle"></i> Add Event
                </button>
            </form>
        </div>

        <div class="table-container">
            <h3><i class="fas fa-list"></i> Existing Events</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Venue</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $events->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($row['event_date'])) ?></td>
                        <td><?= htmlspecialchars($row['event_time']) ?></td>
                        <td><?= htmlspecialchars($row['event_venue']) ?></td>
                        <td>
                            <a href="events/<?= strtolower(str_replace(' ', '_', $row['event_name'])) ?>.php" class="action-btn view-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="delete_event.php?id=<?= $row['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this event?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function generateCategoryInputs() {
            let count = document.getElementById("num_categories").value;
            let container = document.getElementById("category_inputs");
            container.innerHTML = "";
            
            for (let i = 1; i <= count; i++) {
                let categoryDiv = document.createElement("div");
                categoryDiv.className = "form-section";
                categoryDiv.innerHTML = `
                    <h4><i class="fas fa-tag"></i> Category ${i}</h4>
                    <div class="form-group">
                        <label for="category_name_${i}">Category Name</label>
                        <input type="text" id="category_name_${i}" name="category_name_${i}" class="form-control" placeholder="Enter category name" required>
                    </div>
                    <div class="form-group">
                        <label for="category_desc_${i}">Category Description</label>
                        <textarea id="category_desc_${i}" name="category_desc_${i}" class="form-control" placeholder="Enter category description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category_image_${i}">Category Image</label>
                        <input type="file" id="category_image_${i}" name="category_image_${i}" class="form-control" accept="image/*" required>
                    </div>
                `;
                container.appendChild(categoryDiv);
            }
        }

        function generateCoordinatorInputs() {
            let count = document.getElementById("num_coordinators").value;
            let container = document.getElementById("coordinator_inputs");
            container.innerHTML = "";
            
            for (let i = 1; i <= count; i++) {
                let coordinatorDiv = document.createElement("div");
                coordinatorDiv.className = "form-section";
                coordinatorDiv.innerHTML = `
                    <h4><i class="fas fa-user"></i> Coordinator ${i}</h4>
                    <div class="form-group">
                        <label for="coordinator_name_${i}">Coordinator Name</label>
                        <input type="text" id="coordinator_name_${i}" name="coordinator_name_${i}" class="form-control" placeholder="Enter coordinator name" required>
                    </div>
                    <div class="form-group">
                        <label for="coordinator_contact_${i}">Contact Information</label>
                        <input type="text" id="coordinator_contact_${i}" name="coordinator_contact_${i}" class="form-control" placeholder="Enter contact number/email" required>
                    </div>
                `;
                container.appendChild(coordinatorDiv);
            }
        }

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

</body>
</html>