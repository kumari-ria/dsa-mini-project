<?php
session_start();
include("db.php");

// Handle form submission for login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Prepare the SQL query based on the role
    $stmt = $conn->prepare("SELECT * FROM login WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists and if the password is correct
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        if ($role == 'student') {
            header("Location: student_dashboard.php");  // Redirect to the student dashboard
        } elseif ($role == 'teacher') {
            header("Location: teacher_dashboard.php");  // Redirect to the teacher dashboard
        }
        exit;
    } else {
        echo "Invalid credentials.";
    }
}
?>

<!-- Login Form -->
<form method="POST" action="index.php">
    <h2>Login</h2>
    
    <!-- Email Input -->
    <input type="email" name="email" required placeholder="Email"><br>
    
    <!-- Password Input -->
    <input type="password" name="password" required placeholder="Password"><br>
    
    <!-- Role Selection (Student or Teacher) -->
    <select name="role" required>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
    </select><br>

    <button type="submit">Login</button><br><br>
    
    <!-- Links for Sign Up and Forgot Password -->
    <p><a href="signup.php">Sign Up</a></p>
    <p><a href="forgot_password.php">Forgot Password?</a></p>
    <link rel="stylesheet" href="style.css">
</form>
