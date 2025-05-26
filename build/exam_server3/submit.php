<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
require 'database.php';

$student_id = $_SESSION['student_id'] ?? null;

if (!$student_id || empty($_POST['answer'])) {
    die("Invalid submission: student ID missing or no answers submitted.");
}

// 🔓 Temporarily disable foreign key checks (for testing only)
$conn->query("SET FOREIGN_KEY_CHECKS=0");

foreach ($_POST['answer'] as $question_id => $selected_answer) {
    // Check if already saved (from autosave)
    $check = $conn->prepare("SELECT id FROM student_answers WHERE student_id = ? AND question_id = ?");
    $check->bind_param("ii", $student_id, $question_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing answer
        $update = $conn->prepare("UPDATE student_answers SET selected_answer = ? WHERE student_id = ? AND question_id = ?");
        $update->bind_param("sii", $selected_answer, $student_id, $question_id);
        $update->execute();
    } else {
        // Insert new answer
        $insert = $conn->prepare("INSERT INTO student_answers (student_id, question_id, selected_answer) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $student_id, $question_id, $selected_answer);
        $insert->execute();
    }
}

// ✅ Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS=1");

echo "<h2>✅ Exam submitted successfully!</h2>";
echo "<a href='dashboard.php'>Go back to Dashboard</a>";
?>
