<?php
session_start();
require_once 'db.php'; // assumes db connection is set up here

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if lecturer exists
    $stmt = $pdo->prepare("SELECT lec_id, name, password FROM lecturers WHERE email = ?");
    $stmt->execute([$email]);
    $lecturer = $stmt->fetch();

    if ($lecturer && $password === $lecturer['password']) {
        $_SESSION['lec_id'] = $lecturer['lec_id'];
        $_SESSION['lec_name'] = $lecturer['name'];
        header("Location: lecdashboard.html"); // or your dashboard file
        exit;
    } else {
        $error = "Invalid credentials. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Lecturer Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .login-box {
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      min-width: 320px;
    }
    .login-box h2 {
      margin-bottom: 20px;
      color: #0000ff;
      font-weight: 600;
      text-align: center;
    }
    .login-box label {
      font-size: 15px;
      font-weight: 500;
    }
    .login-box input {
      width: 100%;
      padding: 10px;
      margin: 8px 0 18px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      background: #f8f9fb;
    }
    .login-box button {
      width: 100%;
      padding: 10px;
      background: #0000ff;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s;
    }
    .login-box button:hover {
      background: #0000cc;
    }
    .error-message {
      color: #ff3333;
      background: #ffeaea;
      border: 1px solid #ffcccc;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Lecturer Login</h2>
    <?php if ($error): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" autocomplete="off">
      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required autofocus />

      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required />

      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
