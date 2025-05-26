<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
require 'database.php';

// 1. Fetch exam list for dropdown
$exams = $conn->query("SELECT id, exam_name FROM exams");

// 2. Determine selected exam
$selected_exam = $_GET['exam_id'] ?? null;
$whereClause = $selected_exam ? "WHERE q.exam_id = $selected_exam" : "";

// 3. Fetch answers + corrects
$sql = "
SELECT 
    sa.student_id, s.username,
    q.exam_id, q.question_text,
    sa.selected_answer, q.correct_answer
FROM student_answers sa
JOIN students s ON sa.student_id = s.id
JOIN exam_questions q ON sa.question_id = q.id
$whereClause
ORDER BY sa.student_id, q.id
";

$result = $conn->query($sql);

// Organize answers by student
$student_data = [];

while ($row = $result->fetch_assoc()) {
    $sid = $row['student_id'];
    if (!isset($student_data[$sid])) {
        $student_data[$sid] = [
            'username' => $row['username'],
            'correct' => 0,
            'total' => 0,
            'answers' => []
        ];
    }

    $isCorrect = $row['selected_answer'] === $row['correct_answer'];
    if ($isCorrect) $student_data[$sid]['correct']++;

    $student_data[$sid]['total']++;
    $student_data[$sid]['answers'][] = [
        'question' => $row['question_text'],
        'selected' => $row['selected_answer'],
        'correct' => $row['correct_answer'],
        'status' => $isCorrect
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Results</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f9f9f9; }
        h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 40px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #007BFF; color: white; }
        .correct { color: green; font-weight: bold; }
        .wrong { color: red; font-weight: bold; }
        .score { margin: 5px 0; font-weight: bold; }
    </style>
</head>
<body>

<h1>📊 Student Exam Results</h1>

<!-- Exam filter form -->
<form method="GET" action="results.php">
    <label>Select Exam:</label>
    <select name="exam_id" onchange="this.form.submit()">
        <option value="">-- All Exams --</option>
        <?php while ($exam = $exams->fetch_assoc()): ?>
            <option value="<?= $exam['id'] ?>" <?= ($selected_exam == $exam['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($exam['exam_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<!-- Download CSV -->
<form method="GET" action="export_csv.php" style="margin-top: 10px;">
    <?php if ($selected_exam): ?>
        <input type="hidden" name="exam_id" value="<?= $selected_exam ?>">
    <?php endif; ?>
    <button type="submit">⬇️ Download CSV</button>
</form>

<?php if (empty($student_data)): ?>
    <p>No answers submitted yet.</p>
<?php else: ?>
    <?php foreach ($student_data as $sid => $data): ?>
        <h2>👤 Student: <?= htmlspecialchars($data['username']) ?> (ID: <?= $sid ?>)</h2>
        <p class="score">✅ Correct: <?= $data['correct'] ?> / <?= $data['total'] ?> (Score: <?= round(($data['correct'] / $data['total']) * 100, 2) ?>%)</p>
        <table>
            <tr>
                <th>Question</th>
                <th>Selected</th>
                <th>Correct</th>
                <th>Status</th>
            </tr>
            <?php foreach ($data['answers'] as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['question']) ?></td>
                    <td><?= $a['selected'] ?></td>
                    <td><?= $a['correct'] ?></td>
                    <td class="<?= $a['status'] ? 'correct' : 'wrong' ?>">
                        <?= $a['status'] ? 'Correct' : 'Wrong' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
