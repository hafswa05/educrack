<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Access denied.");

$conn = new mysqli('localhost', 'root', '', 'educrack');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);



$quiz_id = intval($_POST['quiz_id'] ?? 0);
// Get the unit_id for this quiz
$unitQuery = $conn->prepare("
    SELECT u.unit_id, u.unit_name 
    FROM quizzes q 
    JOIN units u ON q.unit_id = u.unit_id 
    WHERE q.quiz_id = ?
");
$unitQuery->bind_param("i", $quiz_id);
$unitQuery->execute();
$unitResult = $unitQuery->get_result();
$unitData = $unitResult->fetch_assoc();
$unit_id = $unitData['unit_id'];
$unit_name = $unitData['unit_name'];

// Fetch notes for the unit
$noteResult = $conn->query("
    SELECT * FROM notes 
    WHERE unit_id = '$unit_id' 
    ORDER BY created_at DESC
");


if ($quiz_id <= 0) die("Invalid quiz.");

// Fetch answers and calculate score
$stmt = $conn->prepare("
    SELECT q.question_id, q.question_text, q.correct_option, q.optionA, q.optionB, q.optionC, q.optionD
    FROM questions q
    JOIN quiz_questions qq ON q.question_id = qq.question_id
    WHERE qq.quiz_id = ?
");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

$score = 0;
$total = $result->num_rows;
$feedback = [];

while ($row = $result->fetch_assoc()) {
    $qid = $row['question_id'];
    $selected = $_POST["q$qid"] ?? 'Unanswered';
    $is_correct = ($selected === $row['correct_option']);
    if ($is_correct) $score++;

    $feedback[] = [
        'question' => $row['question_text'],
        'correct' => $row['correct_option'],
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
    

    if (!isset($_SESSION['student_id'])) {
        die("Student session not found. Please log in.");
    }
    $student_id = $_SESSION['student_id'];


    // Calculate percentage
    $percentage = round(($score / $total) * 100, 2);

    // Determine grade and status
    if ($percentage >= 70) {
        $grade = 'A';
        $status = 'Passed';
    } elseif ($percentage >= 60) {
        $grade = 'B';
        $status = 'Passed';
    } elseif ($percentage >= 50) {
        $grade = 'C';
        $status = 'Passed';
    } else {
        $grade = 'F';
        $status = 'Failed';
    }

// Get number of previous attempts
        $stmt = $conn->prepare("SELECT COUNT(*) FROM history WHERE student_id = ? AND quiz_id = ?");
        $stmt->bind_param("ii", $student_id, $quiz_id);
        $stmt->execute();
        $stmt->bind_result($attempts);
        $stmt->fetch();
        $stmt->close();
        $attempts++;

        // Generate a history_id like "H023"
        $history_id = 'H' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Insert result into history table
        $stmt = $conn->prepare("INSERT INTO history (history_id, student_id, quiz_id, score, grade, NumberOfAttempts, UnitStatus) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siidsis", $history_id, $student_id, $quiz_id, $percentage, $grade, $attempts, $status);
        $stmt->execute();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Feedback</title>
    <link rel="stylesheet" href="QuizTaking.css">
    <style>
        /* Your original styles + enhancements */
        body { background-color: #fdf6f1; color: #4b2e2e; }
        .result-box { max-width: 800px; margin: 2rem auto; padding: 1.5rem; background: #fff; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .score { font-size: 1.5rem; text-align: center; margin-bottom: 1.5rem; color: #5e3a2e; }
        .question { margin-bottom: 1.5rem; padding: 1rem; border-radius: 12px; background: #fff7f0; }
        .correct { color: #2e8b57; font-weight: bold; }
        .incorrect { color: #e74c3c; }
        .correct-answer { margin-top: 0.5rem; padding: 0.5rem; background: #e8f8f5; border-left: 3px solid #2e8b57; }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-box">
            <h1>Quiz Results</h1>
            <div class="score">
                Score: <strong><?php echo "$score / $total"; ?></strong>
                (<?php echo round(($score / $total) * 100); ?>%)
            </div>

            <?php foreach ($feedback as $i => $f): ?>
            <div class="question">
                <p><strong>Q<?php echo $i + 1; ?>:</strong> <?php echo htmlspecialchars($f['question']); ?></p>
                
               <?php foreach ($f['options'] as $opt => $text): ?>
                <?php
                    $isSelected = ($opt === $f['selected']);
                    $isCorrect = ($opt === $f['correct']);
                    $class = '';
                    $symbol = '';

                    if ($isSelected) {
                        $class = $f['is_correct'] ? 'correct' : 'incorrect';
                        $symbol = $f['is_correct'] ? '✔ (Your Answer)' : '✖ (Your Answer)';
                    } elseif ($isCorrect) {
                        $class = 'correct';
                        $symbol = '✔ (Correct Answer)';
                    }
                ?>
                <p class="<?= $class ?>">
                    <?= $opt ?>. <?= htmlspecialchars($text) ?>
                    <?php if ($symbol): ?> <?= $symbol ?><?php endif; ?>
                </p>
                <?php endforeach; ?>

                <?php if (!$f['is_correct']): ?>

                    <div class="correct-answer">
                        <strong>Correct Answer:</strong> <?php echo $f['correct']; ?>. <?php echo htmlspecialchars($f['options'][$f['correct']]); ?>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    <?php endforeach; ?>

    <hr>
<h2 style="text-align:center;">📚 Notes Related to <?= htmlspecialchars($unit_name) ?></h2>

<?php if ($noteResult->num_rows > 0): ?>
  <?php while ($note = $noteResult->fetch_assoc()): ?>
    <div class="result-box" style="margin-top: 1rem;">
      <h3><?= htmlspecialchars($note['title']) ?></h3>
      <p><strong>Uploaded by:</strong> Lecturer <?= $note['lec_id'] ?> |
         <strong>Date:</strong> <?= date('d M Y', strtotime($note['created_at'])) ?></p>
      <div><?= nl2br(htmlspecialchars($note['content'])) ?></div>
      <?php if ($note['is_summary']): ?>
        <p style="color: green; font-weight: bold;">This is a summary note.</p>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p style="text-align:center;">No notes available for this unit yet.</p>
<?php endif; ?>

</body>
</html>