<?php
// Include database connection
include("db.php");  // Make sure db.php has the correct database connection setup

// Handle form submission for signup
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    // Only assign roll_no for students
    $roll_no = ($role == 'student') ? $_POST['roll_no'] : null;

    // Insert user registration into the database (role-based)
    $stmt = $conn->prepare("INSERT INTO login(name, email, password, role, roll_no) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $roll_no);

    if ($stmt->execute()) {

        echo "Registration execute successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script type="text/javascript">
        // JavaScript to show/hide the roll number field based on selected role
        function toggleRollNoField() {
            var role = document.getElementById('role').value;
            var rollNoField = document.getElementById('roll_no_field');
            if (role === 'student') {
                rollNoField.style.display = 'block'; // Show roll number field
            } else {
                rollNoField.style.display = 'none'; // Hide roll number field
            }
        }

        // Call toggleRollNoField() when the page loads to ensure it reflects the default selection
        window.onload = function() {
            toggleRollNoField();
        }
    </script>
</head>
<body>

<h2>Sign Up</h2>

<form method="POST">
    <!-- Name field -->
    <input type="text" name="name" required placeholder="Name"><br>

    <!-- Email field -->
    <input type="email" name="email" required placeholder="Email"><br>

    <!-- Password field -->
    <input type="password" name="password" required placeholder="Password"><br>

    <!-- Role selection -->
    <select name="role" id="role" onchange="toggleRollNoField()">
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
    </select><br>

    <!-- Roll No field will only be shown if the role is student -->
    <div id="roll_no_field" style="display:none;">
        <input type="text" name="roll_no" placeholder="Roll No" /><br>
    </div>

    <!-- Submit button -->
    <button type="submit">Register</button>
</form>
<link rel="stylesheet" href="signup.css">


</body>
</html