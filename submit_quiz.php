<?php
$conn = new mysqli('localhost', 'root', '', 'educrack');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$quiz_id = $_POST['quiz_id'];
$score = 0;
$total = 0;
$feedback = [];

// Step 1: Get correct answers for this quiz
$sql = "
SELECT q.question_id, q.correct_option, q.question_text, q.optionA, q.optionB, q.optionC, q.optionD
FROM questions q
JOIN quiz_questions qq ON q.question_id = qq.question_id
WHERE qq.quiz_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $qid = $row['question_id'];
    $correct = $row['correct_option'];
    $selected = isset($_POST["q$qid"]) ? $_POST["q$qid"] : 'None';
    $is_correct = ($selected == $correct);
    $total++;
    if ($is_correct) $score++;

    $feedback[] = [
        'question' => $row['question_text'],
        'correct' => $correct,
        'selected' => $selected,
        'is_correct' => $is_correct,
        'options' => [
            'A' => $row['optionA'],
            'B' => $row['optionB'],
            'C' => $row['optionC'],
            'D' => $row['optionD']
        ]
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Feedback</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .question { margin-bottom: 20px; padding: 15px; border-radius: 10px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .correct { color: green; font-weight: bold; }
        .wrong { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Quiz Completed!</h1>
    <h2>Score: <?php echo "$score / $total"; ?></h2>

    <?php foreach ($feedback as $f): ?>
    <div class="question">
        <p><strong>Q:</strong> <?php echo htmlspecialchars($f['question']); ?></p>
        <?php foreach ($f['options'] as $key => $val): ?>
            <p>
                <?php if ($key == $f['selected']): ?>
                    <strong><?php echo $key; ?>. <?php echo htmlspecialchars($val); ?></strong>
                    <?php if ($f['is_correct']): ?>
                        <span class="correct">✔ Correct</span>
                    <?php else: ?>
                        <span class="wrong">✘ Incorrect</span>
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo $key; ?>. <?php echo htmlspecialchars($val); ?>
                <?php endif; ?>
            </p>
        <?php endforeach; ?>
        <?php if (!$f['is_correct']): ?>
            <p><em class="correct">Correct Answer: <?php echo $f['correct']; ?>. <?php echo htmlspecialchars($f['options'][$f['correct']]); ?></em></p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</body>
</html>
