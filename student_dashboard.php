<?php
session_start();
include("db.php");

// Only for student
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user']['id'];

// Fetch assessment
$stmt = $conn->prepare("SELECT * FROM assessments WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head><title>Student Dashboard</title></head>
<body>

<h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?> (Student)</h2>

<?php if ($data): ?>
    <h3>Your Assessment</h3>
    <table border="1" cellpadding="8">
        <tr><th>Roll No</th><td><?= $data['roll_no'] ?></td></tr>
        <tr><th>Attendance</th><td><?= $data['attendance'] ?>%</td></tr>
        <tr><th>Unit Test Score</th><td><?= $data['unit_test_score'] ?></td></tr>
        <tr><th>Mock Practical</th><td><?= $data['mock_practical_score'] ?></td></tr>
        <tr><th>Achievements</th><td><?= htmlspecialchars($data['achievements']) ?></td></tr>
        <tr><th>Final Score</th><td><?= $data['final_score'] ?></td></tr>
    </table>
<?php else: ?>
    <p>No assessment data found.</p>
<?php endif; ?>

<p><a href="logout.php">Logout</a></p>
<link rel="stylesheet" href="student.css">


</body>
</html>