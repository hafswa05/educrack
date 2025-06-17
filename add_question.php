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
    $question_text = $_POST['question_text'];
    $optionA = $_POST['optionA'];
    $optionB = $_POST['optionB'];
    $optionC = $_POST['optionC'];
    $optionD = $_POST['optionD'];
    $correct_option = $_POST['correct_option'];
    $explanation = $_POST['explanation'];

    $stmt = $pdo->prepare("INSERT INTO questions 
        (unit_id, question_text, optionA, optionB, optionC, optionD, correct_option, lec_id, explanation)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$unit_id, $question_text, $optionA, $optionB, $optionC, $optionD, $correct_option, $lec_id, $explanation]);

    echo "<p class='success-msg'>Question added successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUCRACK - Add Question</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8ff;
            color: #2c3e50;
            line-height: 1.6;
            padding: 20px;
        }

        .form-container {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 1.5rem;
            font-family: Georgia, 'Times New Roman', Times, serif;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group label.required::after {
            content: " *";
            color: #e74c3c;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: #70bce4;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #70bce4;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            display: block;
            margin: 2rem auto 0;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #559ec3;
        }

        .success-msg {
            text-align: center;
            margin-top: 10px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add a New Question</h1>
        <form method="POST">
            <div class="form-group">
                <label for="unit_id" class="required">Unit ID</label>
                <input type="text" id="unit_id" name="unit_id" required placeholder="Enter Unit ID">
            </div>

            <div class="form-group">
                <label for="question_text" class="required">Question Text</label>
                <textarea id="question_text" name="question_text" required placeholder="Enter the question..."></textarea>
            </div>

            <div class="form-group">
                <label for="optionA">Option A</label>
                <input type="text" id="optionA" name="optionA" required>
            </div>
            <div class="form-group">
                <label for="optionB">Option B</label>
                <input type="text" id="optionB" name="optionB" required>
            </div>
            <div class="form-group">
                <label for="optionC">Option C</label>
                <input type="text" id="optionC" name="optionC" required>
            </div>
            <div class="form-group">
                <label for="optionD">Option D</label>
                <input type="text" id="optionD" name="optionD" required>
            </div>

            <div class="form-group">
                <label for="correct_option" class="required">Correct Option</label>
                <select id="correct_option" name="correct_option" required>
                    <option value="">--Select--</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <div class="form-group">
                <label for="explanation">Explanation for Common Mistakes (optional)</label>
                <textarea id="explanation" name="explanation" rows="3" placeholder="e.g. Most students confuse 'will' with 'intellect'..."></textarea>
            </div>

            <button type="submit">Add Question</button>
        </form>
    </div>
</body>
</html>
