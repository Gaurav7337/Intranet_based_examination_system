<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
require 'database.php';

$student_id = $_SESSION['student_id'] ?? null;
if (!$student_id || empty($_POST['answer'])) {
    http_response_code(400);
    exit("Invalid");
}

foreach ($_POST['answer'] as $question_id => $selected_answer) {
    // Check if already saved
    $check = $conn->prepare("SELECT id FROM student_answers WHERE student_id = ? AND question_id = ?");
    $check->bind_param("ii", $student_id, $question_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update
        $update = $conn->prepare("UPDATE student_answers SET selected_answer = ? WHERE student_id = ? AND question_id = ?");
        $update->bind_param("sii", $selected_answer, $student_id, $question_id);
        $update->execute();
    } else {
        // Insert
        $insert = $conn->prepare("INSERT INTO student_answers (student_id, question_id, selected_answer) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $student_id, $question_id, $selected_answer);
        $insert->execute();
    }
}
http_response_code(200);
