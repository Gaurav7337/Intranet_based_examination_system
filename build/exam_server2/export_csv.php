<?php
require 'database.php';

$exam_id = $_GET['exam_id'] ?? null;
$whereClause = "";

if ($exam_id && is_numeric($exam_id)) {
    $whereClause = "WHERE q.exam_id = " . intval($exam_id);
}

// Get all answer data
$sql = "
SELECT 
    s.id AS student_id,
    s.username,
    q.question_text,
    sa.selected_answer,
    q.correct_answer
FROM student_answers sa
JOIN students s ON sa.student_id = s.id
JOIN exam_questions q ON sa.question_id = q.id
$whereClause
ORDER BY s.id, q.id
";

$result = $conn->query($sql);

if (!$result) {
    header('Content-Type: text/plain');
    echo "Query error: " . $conn->error;
    exit;
}

if ($result->num_rows === 0) {
    header('Content-Type: text/plain');
    echo "No results found for export.\nMake sure questions have valid exam_id and students submitted answers.";
    exit;
}

// Prepare results grouped by student
$student_scores = [];

while ($row = $result->fetch_assoc()) {
    $sid = $row['student_id'];

    if (!isset($student_scores[$sid])) {
        $student_scores[$sid] = [
            'username' => $row['username'],
            'total' => 0,
            'correct' => 0,
        ];
    }

    $student_scores[$sid]['total']++;

    if ($row['selected_answer'] === $row['correct_answer']) {
        $student_scores[$sid]['correct']++;
    }
}

// Send headers for CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="exam_scores_summary.csv"');

$output = fopen('php://output', 'w');

// CSV Header
fputcsv($output, ['Student ID', 'Username', 'Correct Answers', 'Total Questions', 'Score (%)']);

// Write each student's summary
foreach ($student_scores as $sid => $data) {
    $score_percent = ($data['total'] > 0)
        ? round(($data['correct'] / $data['total']) * 100, 2)
        : 0;

    fputcsv($output, [
        $sid,
        $data['username'],
        $data['correct'],
        $data['total'],
        $score_percent
    ]);
}

fclose($output);
exit;
