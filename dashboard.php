<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];

echo "<h2>Welcome, " . $user['name'] . "</h2>";

if ($user['role'] == 'teacher') {
    echo '<a href="insert_data.php">Insert Assessment</a>';
} else {
    echo '<a href="view_assessment.php">View Your Assessment</a>';
}
?>
