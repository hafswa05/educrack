<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "educrack"  ;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM questions ORDER BY question_id";
$result = $conn->query($sql);
$questions = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8" />
<title>Quiz Screen</title>
<link rel="stylesheet" href="QuizTaking.css"/>
</head>
<body>
<form action="answer.php" method="post">
<h1>COMM SKILLS 1</h1>
<p1 class="Para" id="ab">Time : 16:35</p1>
<ul>
<?php for($i = 1; $i <= count($questions); $i++): ?>
<li><?php echo $i; ?></li>
<?php endfor; ?>
</ul>

<?php foreach($questions as $index => $question): ?>
<div>
<p2 class="Para" id="ba"><?php echo ($index + 1) . ". " . $question['question_text']; ?></p2><br><br>
</div>
<div>
<input type="radio" id="q<?php echo $question['question_id']; ?> a" name="question_<?php echo $question['question_id']; ?>" value="A">
<label for="q<?php echo $question['question_id']; ?>a"><span>A</span> <?php echo $question['optionA']; ?></label><br>

<input type="radio" id="q<?php echo $question['question_id']; ?>b" name="question_<?php echo $question['question_id']; ?>" value="B">
<label for="q<?php echo $question['question_id']; ?>b"><span>B</span> <?php echo $question['optionB']; ?></label><br>

<input type="radio" id="q<?php echo $question['question_id']; ?>c" name="question_<?php echo $question['question_id']; ?>" value="C">
<label for="q<?php echo $question['question_id']; ?>c"><span>C</span> <?php echo $question['optionC']; ?></label><br>

<input type="radio" id="q<?php echo $question['question_id']; ?>d" name="question_<?php echo $question['question_id']; ?>" value="D">
<label for="q<?php echo $question['question_id']; ?>d"><span>D</span> <?php echo $question['optionD']; ?></label><br><br>
</div>
<?php endforeach ; ?>

<button type="submit" class="11">Submit Quiz</button>
</form>
</body>
</html>
