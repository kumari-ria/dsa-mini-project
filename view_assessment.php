<?php
session_start();
include("db.php");

// 1. Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['user']['id'];

// 2. Get student name and roll number from login table
$student_query = "SELECT name, roll_no FROM login WHERE id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();

// 3. If student found
if ($student_result->num_rows > 0) {
    $student = $student_result->fetch_assoc();

    // 4. Get assessment data
    $assessment_query = "SELECT * FROM assessments WHERE student_id = ?";
    $stmt2 = $conn->prepare($assessment_query);
    $stmt2->bind_param("i", $student_id);
    $stmt2->execute();
    $result = $stmt2->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>Assessment Details</h2>";
        echo "Name: " . $student['name'] . "<br>";
        echo "Roll No: " . $student['roll_no'] . "<br><br>";
        echo "Attendance: " . $row['attendance'] . "%<br>";
        echo "Unit Test Score: " . $row['unit_test_score'] . "<br>";
        echo "Mock Practical: " . $row['mock_practical_score'] . "<br>";
        echo "Achievements: " . $row['achievements'] . "<br>";
        echo "Final Score: " . $row['final_score'] . "<br>";
    } else {
        echo "⚠️ No assessment data found.";
    }
} else {
    echo "⚠️ Student data not found in login table.";
}
?>
