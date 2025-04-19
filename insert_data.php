<?php
session_start();
include("db.php");

// ✅ Only allow access to teachers
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

// Initialize variables
$student_id = '';
$student_name = '';
$student_roll_no = '';
$attendance = '';
$unit_test_score = '';
$mock_practical_score = '';
$achievements = '';
$message = '';

// ✅ Prefill student info if accessed via dashboard link
if (isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);
    $stmt = $conn->prepare("SELECT name, roll_no FROM login WHERE id = ? AND role = 'student'");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_name, $student_roll_no);
    $stmt->fetch();
    $stmt->close();
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $attendance = $_POST['attendance'];
    $unit_test_score = $_POST['unit_test_score'];
    $mock_practical_score = $_POST['mock_practical_score'];
    $achievements = $_POST['achievements'];

    // Fetch name and roll no again for data integrity
    $stmt = $conn->prepare("SELECT name, roll_no FROM login WHERE id = ? AND role = 'student'");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_name, $student_roll_no);
    $stmt->fetch();
    $stmt->close();

    // Calculate final score
    $final_score = ($attendance * 0.2) + ($unit_test_score * 0.4) + ($mock_practical_score * 0.3);
    if (!empty($achievements)) {
        $final_score += 5;
    }

    // Check if assessment already exists
    $check_stmt = $conn->prepare("SELECT id FROM assessments WHERE student_id = ?");
    $check_stmt->bind_param("i", $student_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Update existing assessment
        $stmt = $conn->prepare("UPDATE assessments SET 
            name = ?, 
            roll_no = ?, 
            attendance = ?, 
            unit_test_score = ?, 
            achievements = ?, 
            mock_practical_score = ?, 
            final_score = ?
            WHERE student_id = ?");
        $stmt->bind_param("ssdddsdi", $student_name, $student_roll_no, $attendance, $unit_test_score, $achievements, $mock_practical_score, $final_score, $student_id);
        $stmt->execute();
        $message = "✅ Assessment updated successfully!";
    } else {
        // Insert new assessment
        $stmt = $conn->prepare("INSERT INTO assessments (student_id, name, roll_no, attendance, unit_test_score, achievements, mock_practical_score, final_score) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdsdd", $student_id, $student_name, $student_roll_no, $attendance, $unit_test_score, $achievements, $mock_practical_score, $final_score);
        $stmt->execute();
        $message = "✅ Assessment inserted successfully!";
    }

    $stmt->close();
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert Assessment</title>
</head>
<body>

<h2>Insert Assessment</h2>

<?php if (!empty($message)): ?>
    <p style="color: green;"><?= $message ?></p>
<?php endif; ?>

<?php if (!empty($student_name)): ?>
    <p><strong>Student:</strong> <?= htmlspecialchars($student_name) ?> (<?= htmlspecialchars($student_roll_no) ?>)</p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>" required>

    <label>Attendance (%):</label><br>
    <input type="number" name="attendance" step="0.01" value="<?= htmlspecialchars($attendance) ?>" required><br><br>

    <label>Unit Test Score:</label><br>
    <input type="number" name="unit_test_score" step="0.01" value="<?= htmlspecialchars($unit_test_score) ?>" required><br><br>

    <label>Achievements:</label><br>
    <textarea name="achievements"><?= htmlspecialchars($achievements) ?></textarea><br><br>

    <label>Mock Practical Score:</label><br>
    <input type="number" name="mock_practical_score" step="0.01" value="<?= htmlspecialchars($mock_practical_score) ?>" required><br><br>

    <button type="submit">Submit Assessment</button>
</form>

<p><a href="teacher_dashboard.php">⬅ Back to Dashboard</a></p>
<link rel="stylesheet" href="style.css">


</body>
</html>
