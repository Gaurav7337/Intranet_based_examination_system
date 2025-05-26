<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
include 'database.php';

$common_password = "exam123"; // Set your universal password

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate against DB
    $stmt = $conn->prepare("SELECT * FROM students WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1 && $password === $common_password) {
      $row = $result->fetch_assoc();
      $_SESSION['student_id'] = (int)$row['id'];   

        // Optional: Log login time
        $log = $conn->prepare("INSERT INTO login_logs (student_id) VALUES (?)");
        $log->bind_param("s", $username);
        $log->execute();

        // ✅ Redirect to exam selection/dashboard page
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Student ID or Password.";
    }
}
?>

<!-- Frontend HTML form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exam Portal - Student Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(to right, #00c6ff, #0072ff);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
    }

    .header {
      font-size: 36px;
      color: white;
      margin-bottom: 40px;
      text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.6);
    }

    .login-box {
      background: rgba(255, 255, 255, 0.9);
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }

    h2 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #333;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 15px 0;
      border-radius: 10px;
      border: 1px solid #ccc;
      outline: none;
      font-size: 16px;
      box-sizing: border-box; /* Ensures padding and border are included in width */
      transition: all 0.3s ease;
    }

    input:focus {
      border-color: #0072ff;
      box-shadow: 0 0 5px rgba(0, 114, 255, 0.5);
    }

    button {
      background: #0072ff;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #005bb5;
    }

    .error-message {
      color: red;
      font-size: 14px;
      margin-top: 10px;
    }

    /* Animation for fade-in effect */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    /* Responsive Design */
    @media (max-width: 600px) {
      .login-box {
        padding: 25px;
      }

      h2 {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>

  <!-- Exam Portal Header -->
  <div class="header">
    Exam Portal
  </div>

  <div class="login-box">
    <h2>Student Login</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Student ID" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Login</button>
    </form>

    <!-- Display error message if exists -->
    <?php if (isset($error)): ?>
      <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
  </div>

</body>
</html>
