<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['lec_id'])) {
    header("Location: login.php");
    exit;
}

$lec_id = $_SESSION['lec_id'];
$stmt = $pdo->prepare("SELECT unit_id, title, content, created_at FROM notes WHERE lec_id = ?");
$stmt->execute([$lec_id]);
$notes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Notes</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8ff;
            padding: 2rem;
        }
        .form-container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #eef8ff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background-color: #d6ecf9;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>My Uploaded Notes</h2>
    <table>
        <tr>
            <th>Unit</th>
            <th>Title</th>
            <th>Content</th>
            <th>Uploaded At</th>
        </tr>
        <?php foreach ($notes as $note): ?>
        <tr>
            <td><?= htmlspecialchars($note['unit_id']) ?></td>
            <td><?= htmlspecialchars($note['title']) ?></td>
            <td><?= nl2br(htmlspecialchars($note['content'])) ?></td>
            <td><?= htmlspecialchars($note['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
