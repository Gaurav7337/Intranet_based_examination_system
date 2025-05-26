<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
require 'database.php';

$student_id = $_SESSION['student_id'] ?? null;
$exam_id = $_SESSION['exam_id'] ?? null;

if (!$student_id || !$exam_id) {
    header("Location: dashboard.php");
    exit();
}

// Fetch exam session info
$stmt = $conn->prepare("SELECT * FROM exam_sessions WHERE student_id = ? AND exam_id = ?");
$stmt->bind_param("si", $student_id, $exam_id);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

$start_time = $session['start_time'];
$current_question = $session['current_question'] ?? 0;
$saved_answers = json_decode($session['last_saved_answers'] ?? '{}', true);

// Fetch questions
$stmt = $conn->prepare("SELECT * FROM exam_questions WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$total = count($questions);

// Timer logic
$start_timestamp = strtotime($start_time);
$now = time();
$duration_minutes = 10;
$elapsed = $now - $start_timestamp;
$remaining_time = max(0, ($duration_minutes * 60) - $elapsed);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Exam</title>
  <style>
    body { font-family: Arial; background: #f7f9fc; margin: 0; padding: 0; }
    .exam-container { display: flex; min-height: 100vh; }
    .question-nav {
      width: 100px; background: #333; color: #fff;
      display: flex; flex-direction: column; align-items: center;
      padding: 10px; gap: 8px;
    }
    .nav-btn {
      width: 40px; height: 40px; border: none; border-radius: 5px;
      background: #444; color: white; cursor: pointer;
    }
    .nav-btn.active { background: #00bcd4; }
    .question-area { flex: 1; padding: 30px; background: white; }
    .question-box {
      background: #f0f8ff; border-radius: 10px;
      padding: 20px; margin-bottom: 20px; display: none;
    }
    .nav-buttons { margin-top: 20px; display: flex; gap: 10px; }
    button {
      padding: 10px 15px; background: #007BFF;
      color: white; border: none; border-radius: 5px; cursor: pointer;
    }
  </style>
</head>
<body>

<div class="exam-container">
  <div class="question-nav">
    <?php foreach ($questions as $i => $q): ?>
      <button class="nav-btn" data-index="<?= $i ?>"><?= $i + 1 ?></button>
    <?php endforeach; ?>
  </div>

  <div class="question-area">
    <h2>Exam</h2>
    <h3>Time Left: <span id="timer"></span></h3>

    <form method="post" action="submit.php" id="examForm">
      <?php foreach ($questions as $i => $q): ?>
        <div class="question-box" data-index="<?= $i ?>">
          <p><strong>Q<?= $i + 1 ?>:</strong> <?= htmlspecialchars($q['question_text']) ?></p>
          <input type="hidden" name="question_ids[]" value="<?= $q['id'] ?>">
          <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
            <label>
              <input type="radio"
                     name="answer[<?= $q['id'] ?>]"
                     value="<?= $opt ?>"
                     <?= (isset($saved_answers[$q['id']]) && $saved_answers[$q['id']] === $opt) ? 'checked' : '' ?>>
              <?= htmlspecialchars($q['option_' . strtolower($opt)]) ?>
            </label><br>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <div class="nav-buttons">
        <button type="button" id="prevBtn">Previous</button>
        <button type="button" id="nextBtn">Next</button>
        <button type="submit">Submit</button>
      </div>
    </form>
  </div>
</div>

<!-- Pass PHP vars to JS -->
<script>
const total = <?= $total ?>;
let current = <?= $current_question ?>;
let remaining = <?= $remaining_time ?>;

function updateTimer() {
  const timer = document.getElementById("timer");
  const interval = setInterval(() => {
    if (remaining <= 0) {
      clearInterval(interval);
      alert("Time's up! Submitting...");
      document.getElementById("examForm").submit();
      return;
    }
    let mins = String(Math.floor(remaining / 60)).padStart(2, '0');
    let secs = String(remaining % 60).padStart(2, '0');
    timer.textContent = `${mins}:${secs}`;
    remaining--;
  }, 1000);
}

function showQuestion(index) {
  document.querySelectorAll(".question-box").forEach(box => {
    box.style.display = "none";
  });
  document.querySelector(`.question-box[data-index="${index}"]`).style.display = "block";

  document.querySelectorAll(".nav-btn").forEach(btn => btn.classList.remove("active"));
  document.querySelector(`.nav-btn[data-index="${index}"]`).classList.add("active");

  document.getElementById("prevBtn").disabled = index === 0;
  document.getElementById("nextBtn").disabled = index === total - 1;

  current = index;
  saveCurrentProgress();
}

function saveCurrentProgress() {
  const formData = new FormData(document.getElementById("examForm"));
  formData.append("current_question", current);

  fetch("save_progress.php", {
    method: "POST",
    body: formData
  });
}

document.querySelectorAll(".nav-btn").forEach(btn => {
  btn.addEventListener("click", () => showQuestion(parseInt(btn.dataset.index)));
});
document.getElementById("prevBtn").addEventListener("click", () => showQuestion(current - 1));
document.getElementById("nextBtn").addEventListener("click", () => showQuestion(current + 1));
document.querySelectorAll("input[type=radio]").forEach(input => {
  input.addEventListener("change", saveCurrentProgress);
});

window.onload = () => {
  showQuestion(current);
  updateTimer();
};
</script>
</body>
</html>
