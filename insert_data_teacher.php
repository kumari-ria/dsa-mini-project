<?php
session_start();
include("db.php");

// Only for teacher
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$student_id = $_GET['student_id'] ?? null;
$student_name = '';
$student_roll_no = '';
$message = '';

if ($student_id) {
    // Fetch student info
    $stmt = $conn->prepare("SELECT name, roll_no FROM login WHERE id = ? AND role = 'student'");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_name, $student_roll_no);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $attendance = $_POST['attendance'];
    $unit_test = $_POST['unit_test'];
    $mock_practical = $_POST['mock_practical'];
    $achievements = $_POST['achievements'];

    // Final score calculation
    $final = ($attendance * 0.2) + ($unit_test * 0.4) + ($mock_practical * 0.3);
    if (!empty($achievements)) $final += 5;

    // Check if record already exists for the student
    $check = $conn->prepare("SELECT id FROM assessments WHERE student_id = ?");
    $check->bind_param("i", $student_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {


        // Update existing record
        $check->close();
        $stmt = $conn->prepare("UPDATE assessments 
                                SET name = ?, roll_no = ?, attendance = ?, unit_test_score = ?, achievements = ?, mock_practical_score = ?, final_score = ?
                                WHERE student_id = ?");
        $stmt->bind_param("ssdsdddi", $student_name, $student_roll_no, $attendance, $unit_test, $achievements, $mock_practical, $final, $student_id);
        $stmt->execute();
        $message = "✅ Assessment updated!";
    } else {
        // Insert new record
        $check->close();
        $stmt = $conn->prepare("INSERT INTO assessments 
                                (student_id, name, roll_no, attendance, unit_test_score, achievements, mock_practical_score, final_score) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdsdd", $student_id, $student_name, $student_roll_no, $attendance, $unit_test, $achievements, $mock_practical, $final);
        if ($stmt->execute()) {
            $message = "✅ Assessment saved!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Insert Assessment for <?= htmlspecialchars($student_name) ?> (<?= htmlspecialchars($student_roll_no) ?>)</h2>

<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
    Attendance (%): <input type="number" name="attendance" required><br>
    Unit Test Score: <input type="number" name="unit_test" required><br>
    Mock Practical Score: <input type="number" name="mock_practical" required><br>
    Achievements: <textarea name="achievements"></textarea><br>
    <button type="submit">Submit Assessment</button>
</form>

<p><a href="teacher_dashboard.php">⬅ Back</a></p>

</body>
</html>
