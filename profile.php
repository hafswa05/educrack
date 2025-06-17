<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['lec_id'])) {
    header("Location: login.php");
    exit;
}

$lec_id = $_SESSION['lec_id'];
$stmt = $pdo->prepare("SELECT name, email FROM lecturers WHERE lec_id = ?");
$stmt->execute([$lec_id]);
$lecturer = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecturer Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8ff;
            padding: 2rem;
        }
        .profile-container {
            max-width: 600px;
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
        .info {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="profile-container">
    <h2>My Profile</h2>
    <div class="info"><span class="label">Name:</span> <?= htmlspecialchars($lecturer['name']) ?></div>
    <div class="info"><span class="label">Email:</span> <?= htmlspecialchars($lecturer['email']) ?></div>
</div>
</body>
</html>

