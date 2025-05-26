


<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
include 'database.php';

if (!isset($_SESSION['student_id']) || !isset($_POST['exam_id'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$exam_id = (int) $_POST['exam_id'];
$_SESSION['exam_id'] = $exam_id;

// Check if session already exists
$stmt = $conn->prepare("SELECT id FROM exam_sessions WHERE student_id = ? AND exam_id = ?");
$stmt->bind_param("si", $student_id, $exam_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // New session
    $stmt = $conn->prepare("INSERT INTO exam_sessions (student_id, exam_id, start_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("si", $student_id, $exam_id);
    $stmt->execute();
} else {
    // ✅ Optional: Reset start time each time they re-start exam
    $stmt = $conn->prepare("UPDATE exam_sessions SET start_time = NOW() WHERE student_id = ? AND exam_id = ?");
    $stmt->bind_param("si", $student_id, $exam_id);
    $stmt->execute();
}
// Redirect to exam
header("Location: exam.php");
exit();
