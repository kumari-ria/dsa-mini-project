<?php
session_start();
include("db.php");

// Only for teacher
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

// Fetch students
$stmt = $conn->prepare("SELECT * FROM login WHERE role = 'student'");
$stmt->execute();
$students = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head><title>Teacher Dashboard</title></head>
<body>

<h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?> (Teacher)</h2>

<h3>Students List</h3>
<ul>
    <?php while ($student = $students->fetch_assoc()): ?>
        <li>
            <?= htmlspecialchars($student['name']) ?> (<?= htmlspecialchars($student['roll_no']) ?>)
            — <a href="insert_data_teacher.php?student_id=<?= $student['id'] ?>">Insert Assessment</a>
        </li>
    <?php endwhile; ?>
</ul>

<p><a href="logout.php">Logout</a></p>
<link rel="stylesheet" href="teacher.css">


</body>
</html>
