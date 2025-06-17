<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['lec_id'])) {
    header("Location: login.php");
    exit;
}

$lec_id = $_SESSION['lec_id'];

// Most failed questions
$fail_stmt = $pdo->prepare("SELECT q.question_text, COUNT(*) AS wrong_count FROM answers a JOIN questions q ON q.question_id = a.question_id WHERE a.iscorrect = 0 AND q.lec_id = ? GROUP BY a.question_id ORDER BY wrong_count DESC LIMIT 5");
$fail_stmt->execute([$lec_id]);
$failed_questions = $fail_stmt->fetchAll();

// Average scores per quiz
$avg_stmt = $pdo->prepare("SELECT q.quiz_name, ROUND(AVG(h.score), 2) AS avg_score FROM history h JOIN quizzes q ON q.quiz_id = h.quiz_id WHERE q.lec_id = ? GROUP BY q.quiz_id");
$avg_stmt->execute([$lec_id]);
$averages = $avg_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8ff;
            padding: 2rem;
        }
        .analytics-container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        h3 {
            margin-top: 2rem;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.3rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #eef8ff;
            margin-top: 1rem;
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
<div class="analytics-container">
    <h2>Performance Analytics</h2>

    <h3>Most Frequently Failed Questions</h3>
    <?php if (count($failed_questions) > 0): ?>
        <table>
            <tr>
                <th>Question</th>
                <th>Times Failed</th>
            </tr>
            <?php foreach ($failed_questions as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['question_text']) ?></td>
                    <td><?= $row['wrong_count'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No incorrect answers recorded yet.</p>
    <?php endif; ?>

    <h3>Average Scores Per Quiz</h3>
    <?php if (count($averages) > 0): ?>
        <table>
            <tr>
                <th>Quiz</th>
                <th>Average Score</th>
            </tr>
            <?php foreach ($averages as $avg): ?>
                <tr>
                    <td><?= htmlspecialchars($avg['quiz_name']) ?></td>
                    <td><?= $avg['avg_score'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No quiz attempts recorded yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
