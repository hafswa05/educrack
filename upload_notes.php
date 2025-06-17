<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['lec_id'])) {
    header("Location: login.php");
    exit;
}

$lec_id = $_SESSION['lec_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit_id = $_POST['unit_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare("INSERT INTO notes (unit_id, lec_id, title, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([$unit_id, $lec_id, $title, $content]);
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Notes</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8ff;
            padding: 2rem;
        }
        .form-container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 2rem;
        }
        form {
            background: #eef8ff;
            padding: 1rem;
            border-radius: 8px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 1rem;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            padding: 0.5rem 1.5rem;
            margin-top: 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
        .success-msg {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Upload Notes</h2>
    <?php if (!empty($success)) echo "<p class='success-msg'> Notes uploaded successfully!</p>"; ?>
    <form method="POST">
        <label for="unit_id">Unit ID</label>
        <input type="text" id="unit_id" name="unit_id" required>

        <label for="title">Title</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Note Content</label>
        <textarea id="content" name="content" rows="6" required></textarea>

        <button class="submit-btn" type="submit">Upload</button>
    </form>
</div>
</body>
</html>
