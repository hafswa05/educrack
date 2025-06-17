<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "educrack";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = 191400;

$student_query = "SELECT s.fullname, s.student_id FROM students s WHERE s.student_id = $student_id";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();

$answers_query = "SELECT a.question_id, a.select_option, a.iscorrect, q.unit_id, q.question_text, q.correct_option 
                  FROM answers a 
                  JOIN questions q ON a.question_id = q.question_id 
                  WHERE a.student_id = $student_id 
                  ORDER BY a.answered_at DESC 
                  LIMIT 5";
$answers_result = $conn->query($answers_query);

$total_questions = 0;
$correct_answers = 0;
$unit_id = '';
$answers_data = [];

while($row = $answers_result->fetch_assoc()) {
    $answers_data[] = $row;
    $total_questions++;
    if($row['iscorrect'] == 1) {
        $correct_answers++;
    }
    if(empty($unit_id)) {
        $unit_id = $row['unit_id'];
    }
}

$score = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100, 2) : 0;

$grade = 'F';
if($score >= 80) $grade = 'A';
elseif($score >= 70) $grade = 'B';
elseif($score >= 60) $grade = 'C';
elseif($score >= 50) $grade = 'D';

$comment = '';
if($score >= 80) $comment = 'Excellent performance';
elseif($score >= 70) $comment = 'Good work';
elseif($score >= 60) $comment = 'Satisfactory';
elseif($score >= 50) $comment = 'Needs improvement';
else $comment = 'Poor performance';

$check_result = "SELECT * FROM results WHERE studentId = $student_id AND unit_id = '$unit_id'";
$result_exists = $conn->query($check_result);

if($result_exists->num_rows > 0) {
    $update_result = "UPDATE results SET score = $score, grade = '$grade', comment = '$comment' WHERE studentId = $student_id AND unit_id = '$unit_id'";
    $conn->query($update_result);
} else {
    $names = explode(' ', $student['fullname'], 2);
    $fname = $names[0];
    $lname = isset($names[1]) ? $names[1] : '';
    
    $insert_result = "INSERT INTO results (studentId, FName, LName, unit_id, score, grade, comment) 
                      VALUES ($student_id, '$fname', '$lname', '$unit_id', $score, '$grade', '$comment')";
    $conn->query($insert_result);
}

$conn->close();
?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>Quiz Results</title>
<link rel="stylesheet" href="result.css">
</head>
<body>
<h1>Quiz Results</h1>
<h2>Student: <?php echo $student['fullname']; ?> (ID: <?php echo $student['student_id']; ?>)</h2>
<h3>Unit: <?php echo $unit_id; ?></h3>

<div>
<h3>Summary</h3>
<p>Total Questions: <?php echo $total_questions; ?></p>
<p>Correct Answers: <?php echo $correct_answers; ?></p>
<p>Score: <?php echo $score; ?>%</p>
<p>Grade: <?php echo $grade; ?></p>
<p>Comment: <?php echo $comment; ?></p>
</div>

<div>
<h3>Answer Details</h3>
<?php foreach($answers_data as $index => $answer): ?>
<div>
<p><strong>Question <?php echo ($index + 1); ?>:</strong> <?php echo $answer['question_text']; ?></p>
<p>Your Answer: <?php echo $answer['select_option']; ?></p>
<p>Correct Answer: <?php echo $answer['correct_option']; ?></p>
<p>Result: <?php echo ($answer['iscorrect'] == 1) ? 'Correct' : 'Incorrect'; ?></p>
</div>
<hr>
<?php endforeach; ?>
</div>

</body>
</html>