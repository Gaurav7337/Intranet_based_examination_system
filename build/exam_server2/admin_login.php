<?php
date_default_timezone_set('Asia/Kolkata'); // or UTC, etc.

session_start();
$admin_user = 'admin';
$admin_pass = 'admin123'; // 🔐 Change this to your preferred password

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['is_admin'] = true;
        header('Location: results.php');
        exit;
    } else {
        $error = "Invalid admin credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <style>
    body { font-family: Arial; background: #eef; padding: 40px; text-align: center; }
    form { display: inline-block; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.2); }
    input { padding: 10px; margin: 10px; width: 250px; }
    button { padding: 10px 20px; }
    .error { color: red; margin-top: 10px; }
  </style>
</head>
<body>

<h2>🔒 Admin Login</h2>

<form method="POST">
  <input type="text" name="username" placeholder="Admin Username" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Login</button>
</form>

<?php if (isset($error)): ?>
  <div class="error"><?= $error ?></div>
<?php endif; ?>

</body>
</html>
