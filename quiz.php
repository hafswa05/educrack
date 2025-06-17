<?php
$conn = new mysqli('localhost', 'root', '', 'educrack');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : '';

if ($quiz_id == '') {
    die("No quiz selected. Add ?quiz_id=1 to the URL.");
}

// Fetch quiz name (optional)
$quiz_title = '';
$quizInfo = $conn->query("SELECT quiz_name FROM quizzes WHERE quiz_id = $quiz_id");
if ($quizInfo && $quizInfo->num_rows > 0) {
    $quiz_title = $quizInfo->fetch_assoc()['quiz_name'];
}

// Fetch questions linked to the quiz
$stmt = $conn->prepare("
    SELECT q.question_id, q.question_text, q.optionA, q.optionB, q.optionC, q.optionD
    FROM questions q
    JOIN quiz_questions qq ON q.question_id = qq.question_id
    WHERE qq.quiz_id = ?
");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($quiz_title); ?> Quiz</title>
    <link rel="stylesheet" href="QuizTaking.css">
</head>
<body>
<form action="submit_quiz.php" method="POST">
    <h1><?php echo htmlspecialchars($quiz_title); ?></h1>
    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

    <?php
    $qNum = 1;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='question' id='q$qNum' style='display:" . ($qNum == 1 ? 'block' : 'none') . "'>";

            echo "<p class='Para'><strong>Q$qNum.</strong> " . htmlspecialchars($row["question_text"]) . "</p>";

            foreach (['A', 'B', 'C', 'D'] as $opt) {
                  $optLabel = htmlspecialchars($row["option" . $opt]);
                  echo "<div style='margin: 8px 0;'>
                     <label style='cursor: pointer; display: block; font-size: 16px;'>
                        <input type='radio' name='q" . $row['question_id'] . "' value='" . $opt . "' style='margin-right: 8px;'>
                        <strong>" . $opt . ".</strong> " . $optLabel . "
                    </label>
                 </div>";
}

            if ($qNum < $result->num_rows) {
                echo "<button type='button' class='next-btn' onclick='showNext($qNum)'>Next Question</button>";
            } else {
                echo "<button type='submit'>Submit Quiz</button>";
}


            echo "</div><hr>";
            $qNum++;
        }
    } else {
        echo "<p>No questions found for this quiz.</p>";
    }

    ?>

</form>
<script>
function showNext(current) {
  document.getElementById("q" + current).style.display = "none";
  document.getElementById("q" + (current + 1)).style.display = "block";
}

// Highlight selected answer
document.querySelectorAll("input[type=radio]").forEach(radio => {
  radio.addEventListener("change", function() {
    let group = document.getElementsByName(this.name);
    group.forEach(r => r.parentElement.classList.remove("selected"));
    this.parentElement.classList.add("selected");
  });
});
</script>
</body>
</html>
