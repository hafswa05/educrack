<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "educrack";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = 191400;
    
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_') === 0) {
            $question_id = substr($key, 9);
            $selected_option = $value;
            
            $check_correct = "SELECT correct_option FROM questions WHERE question_id = $question_id";
            $result = $conn->query($check_correct);
            $row = $result->fetch_assoc();
            $is_correct = ($selected_option == $row['correct_option']) ? 1 : 0;
            
            $answer_id = 'ANS' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $insert_answer = "INSERT INTO answers (answer_id, student_id, question_id, select_option, iscorrect) 
                             VALUES ('$answer_id', $student_id, $question_id, '$selected_option', $is_correct)";
            $conn->query($insert_answer);
        }
    }
    echo "<h2>Quiz submitted successfully!</h2>";
    $conn->close();
}
?>