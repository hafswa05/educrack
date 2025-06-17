<?php
session_start();


$conn = new mysqli('localhost', 'root', '', 'educrack');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
if ($quiz_id <= 0) die("Invalid quiz ID.");

// Fetch questions and join quizzes and units
$stmt = $conn->prepare("
  SELECT q.*, quizzes.quiz_name, u.unit_name
  FROM questions q
  JOIN quiz_questions qq ON q.question_id = qq.question_id
  JOIN quizzes ON quizzes.quiz_id = qq.quiz_id
  JOIN units u ON quizzes.unit_id = u.unit_id
  WHERE qq.quiz_id = ?
");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) die("No questions found.");

$firstRow = $result->fetch_assoc();
$quiz_title = htmlspecialchars($firstRow['quiz_name']);
$unit_name = htmlspecialchars($firstRow['unit_name']);
$result->data_seek(0); // reset pointer for looping questions
?>



<!DOCTYPE html>
<head>

<meta charset="UTF-8" />
<title>Quiz Screen</title>
<link rel = "stylesheet" href="QuizTaking.css"/>
</head>
<body>
  <form action="submit_quiz.php" method="POST">
  <h1><?= $unit_name ?></h1>
  <p class="Para" id="progressText" >Question 1 of <?=$result->num_rows?></p>
  


  <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

    <p class="Para" id="ab">Time Left: <span id="timer">10:00</span></p>
    <ul id="question-numbers">

        <?php for ($i = 1; $i <= $result->num_rows; $i++): ?>
            <li id="num<?= $i ?>"><?= $i ?></li>
        <?php endfor; ?>
    </ul>


            <?php $qNum = 1; while ($row = $result->fetch_assoc()): ?>
                <div class="question-block" data-qnum="<?= $qNum ?>" style="<?= $qNum === 1 ? '' : 'display: none;' ?>">
                <p class="Para" id="ba"><strong>Q<?= $qNum ?>:</strong> <?= htmlspecialchars($row['question_text']) ?></p><br>

                <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
                   <label class="option-box">
                    <input type="radio" name="q<?= $row['question_id'] ?>" value="<?= $opt ?>">
                    <span><strong><?= $opt ?>.</strong> <?= htmlspecialchars($row["option$opt"]) ?></span>
                    </label>

                    <?php endforeach; ?>


                <!-- Add navigation buttons here if needed -->
                <?php if ($qNum === $result->num_rows): ?>
                <button type="submit" class="submit-btn">Submit Quiz</button>
                <?php else: ?>
                <button type="button" class="Buut" onclick="showNext(<?= $qNum ?>)">Next Question</button>
                <?php endif; ?>

        </div>
        <?php $qNum++; endwhile; ?>
        




</form>
<script>
        // Countdown Timer
        let time = 600;
        setInterval(() => {
        const mins = Math.floor(time / 60);
        const secs = time % 60;
        document.getElementById("timer").textContent = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
        if (time-- <= 0) document.forms[0].submit();
        }, 1000);

        // Highlight selected answer
        document.querySelectorAll("input[type=radio]").forEach(radio => {
        radio.addEventListener("change", function () {
            document.querySelectorAll(`input[name="${this.name}"]`).forEach(r => {
            r.closest('.option-box').classList.remove("selected");
            });
            this.closest('.option-box').classList.add("selected");
        });
        });

        // Navigation logic
        let currentQ = 1;
        const totalQ = document.querySelectorAll(".question-block").length;

        function showNext(current) {
        const currentBlock = document.querySelector(`.question-block[data-qnum="${current}"]`);
        const nextBlock = document.querySelector(`.question-block[data-qnum="${current + 1}"]`);
        if (!nextBlock) return;

        currentBlock.style.display = "none";
        nextBlock.style.display = "block";
        currentQ++;
        document.getElementById("progressText").textContent = `Question ${currentQ} of ${totalQ}`;

        document.querySelectorAll("#question-numbers li").forEach(li => li.classList.remove("active"));
        const activeLi = document.getElementById(`num${currentQ}`);
        if (activeLi) activeLi.classList.add("active");
        }
</script>

</body>


</html>


