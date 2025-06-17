<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['lec_id'])) {
    header("Location: login.php");
    exit;
}

$lec_id = $_SESSION['lec_id'];

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM questions WHERE question_id = ? AND lec_id = ?");
    $stmt->execute([$delete_id, $lec_id]);
    echo "<p class='success-msg'>Question deleted successfully!</p>";
}

// Handle update submission
if (isset($_POST['update_id'])) {
    $stmt = $pdo->prepare("UPDATE questions SET unit_id=?, question_text=?, optionA=?, optionB=?, optionC=?, optionD=?, correct_option=?, explanation=? WHERE question_id=? AND lec_id=?");
    $stmt->execute([
        $_POST['unit_id'], $_POST['question_text'], $_POST['optionA'], $_POST['optionB'],
        $_POST['optionC'], $_POST['optionD'], $_POST['correct_option'], $_POST['explanation'],
        $_POST['update_id'], $lec_id
    ]);
    echo "<p class='success-msg'>Question updated successfully!</p>";
}

$questions = $pdo->prepare("SELECT * FROM questions WHERE lec_id = ?");
$questions->execute([$lec_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Questions</title>
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
            margin-bottom: 2rem;
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
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 1rem;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        button[type="submit"] {
            background-color: #70bce4;
            color: white;
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .success-msg {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Edit or Delete Your Questions</h2>

    <?php foreach ($questions as $q): ?>
        <form method="POST">
            <input type="hidden" name="update_id" value="<?= $q['question_id'] ?>">

            <label>Unit ID:</label>
            <input type="text" name="unit_id" value="<?= htmlspecialchars($q['unit_id']) ?>" required>

            <label>Question:</label>
            <textarea name="question_text" required><?= htmlspecialchars($q['question_text']) ?></textarea>

            <label>Option A:</label>
            <input type="text" name="optionA" value="<?= htmlspecialchars($q['optionA']) ?>" required>

            <label>Option B:</label>
            <input type="text" name="optionB" value="<?= htmlspecialchars($q['optionB']) ?>" required>

            <label>Option C:</label>
            <input type="text" name="optionC" value="<?= htmlspecialchars($q['optionC']) ?>" required>

            <label>Option D:</label>
            <input type="text" name="optionD" value="<?= htmlspecialchars($q['optionD']) ?>" required>

            <label>Correct Option:</label>
            <select name="correct_option" required>
                <option value="A" <?= $q['correct_option'] === 'A' ? 'selected' : '' ?>>A</option>
                <option value="B" <?= $q['correct_option'] === 'B' ? 'selected' : '' ?>>B</option>
                <option value="C" <?= $q['correct_option'] === 'C' ? 'selected' : '' ?>>C</option>
                <option value="D" <?= $q['correct_option'] === 'D' ? 'selected' : '' ?>>D</option>
            </select>

            <label>Explanation:</label>
            <textarea name="explanation"><?= htmlspecialchars($q['explanation']) ?></textarea>

            <button type="submit">Update</button>
            <a class="delete-btn" href="?delete=<?= $q['question_id'] ?>" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>
        </form>
    <?php endforeach; ?>
</div>
</body>
</html>
