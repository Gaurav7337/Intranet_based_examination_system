<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
require 'database.php';

$student_id = $_SESSION['student_id'];
$exam_id = $_SESSION['exam_id'];
$current_question = $_POST['current_question'] ?? 0;
$answers = json_encode($_POST['answer'] ?? []);

$stmt = $conn->prepare("UPDATE exam_sessions SET last_saved_answers = ?, current_question = ? WHERE student_id = ? AND exam_id = ?");
$stmt->bind_param("sisi", $answers, $current_question, $student_id, $exam_id);
$stmt->execute();
