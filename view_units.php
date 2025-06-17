<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['lec_id'])) {
    header("Location: login.php");
    exit;
}

$lec_id = $_SESSION['lec_id'];

$stmt = $pdo->prepare("SELECT u.unit_id, u.unit_name, c.course_name
                       FROM units u
                       JOIN courses c ON u.course_id = c.course_id
                       JOIN questions q ON q.unit_id = u.unit_id
                       WHERE q.lec_id = ?
                       GROUP BY u.unit_id");
$stmt->execute([$lec_id]);
$units = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Units</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8ff;
            padding: 2rem;
        }
        .form-container {
            max-width: 800px;
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
    <h2>My Units</h2>
    <table>
        <tr>
            <th>Unit ID</th>
            <th>Unit Name</th>
            <th>Course</th>
        </tr>
        <?php foreach ($units as $unit): ?>
        <tr>
            <td><?= htmlspecialchars($unit['unit_id']) ?></td>
            <td><?= htmlspecialchars($unit['unit_name']) ?></td>
            <td><?= htmlspecialchars($unit['course_name']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
