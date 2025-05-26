<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

// Fetch available exams
$exams = $conn->query("SELECT * FROM exams");

// Use student name if available
$username = $_SESSION['username'] ?? $_SESSION['student_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard - Exam Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(to right, #00c6ff, #0072ff);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background: white;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      text-align: center;
      width: 90%;
      max-width: 500px;
    }

    h2 {
      margin-bottom: 10px;
      color: #0072ff;
    }

    p {
      font-size: 16px;
      color: #333;
      margin-bottom: 25px;
    }

    select {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
      margin-bottom: 20px;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #0072ff;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #005bb5;
    }

    .info {
      background: #f0f8ff;
      padding: 15px;
      border-radius: 10px;
      font-size: 14px;
      color: #444;
      margin-bottom: 20px;
    }

    .logout {
      display: inline-block;
      margin-top: 20px;
      color: #f44336;
      font-weight: bold;
      text-decoration: none;
    }

    .logout:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Welcome, <?= htmlspecialchars($username) ?> 👋</h2>
    <p>Select your exam from the list below:</p>

    <div class="info">
      <p>⏱ Once you start the exam, the timer begins immediately.</p>
      <p>🚫 Do not refresh or close your browser during the exam.</p>
    </div>

    <form method="POST" action="start_exam.php">
      <select name="exam_id" required>
        <option value="">-- Select Exam --</option>
        <?php while($row = $exams->fetch_assoc()): ?>
          <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['exam_name']); ?></option>
        <?php endwhile; ?>
      </select>
      <button type="submit">Starttt Exam</button>
    </form>

    <a href="logout.php" class="logout">🚪 Logout</a>
  </div>

</body>
</html>
