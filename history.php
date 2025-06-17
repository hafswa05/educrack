<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "educrack";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = 191400; // This should ideally come from session or parameter

// Get student information
$student_query = "SELECT s.fullname, s.student_id FROM students s WHERE s.student_id = $student_id";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();

// Get the latest quiz attempt data from results table
$latest_result_query = "SELECT * FROM results WHERE studentId = $student_id ORDER BY studentId DESC LIMIT 1";
$latest_result = $conn->query($latest_result_query);

if ($latest_result->num_rows > 0) {
    $result_data = $latest_result->fetch_assoc();
    
    // Get the quiz_id for this unit (assuming the most recent quiz for the unit)
    $quiz_query = "SELECT quiz_id FROM quizzes WHERE unit_id = '{$result_data['unit_id']}' ORDER BY quiz_id DESC LIMIT 1";
    $quiz_result = $conn->query($quiz_query);
    $quiz_data = $quiz_result->fetch_assoc();
    $quiz_id = $quiz_data ? $quiz_data['quiz_id'] : 0;
    
    // Generate unique history ID
    $history_id_query = "SELECT COUNT(*) as count FROM history";
    $count_result = $conn->query($history_id_query);
    $count_data = $count_result->fetch_assoc();
    $history_id = 'H' . str_pad(($count_data['count'] + 1), 3, '0', STR_PAD_LEFT);
    
    // Check if history record already exists for this student and quiz
    $check_history = "SELECT * FROM history WHERE student_id = $student_id AND quiz_id = $quiz_id";
    $history_exists = $conn->query($check_history);
    
    // Determine unit status based on score
    $unit_status = 'Failed';
    if ($result_data['score'] >= 50) {
        $unit_status = 'Passed';
    }
    
    if ($history_exists->num_rows > 0) {
        // Update existing history record
        $update_history = "UPDATE history SET 
                          score = {$result_data['score']}, 
                          grade = '{$result_data['grade']}', 
                          NumberOfAttempts = NumberOfAttempts + 1,
                          UnitStatus = '$unit_status',
                          ChangedAt = NOW()
                          WHERE student_id = $student_id AND quiz_id = $quiz_id";
        $conn->query($update_history);
        $message = "History updated successfully!";
    } else {
        // Insert new history record
        $insert_history = "INSERT INTO history (history_id, student_id, quiz_id, score, grade, NumberOfAttempts, UnitStatus, ChangedAt) 
                          VALUES ('$history_id', $student_id, $quiz_id, {$result_data['score']}, '{$result_data['grade']}', 1, '$unit_status', NOW())";
        $conn->query($insert_history);
        $message = "History record created successfully!";
    }
}

// Retrieve all history records for the student
$history_query = "SELECT h.*, q.quiz_name, u.unit_name, q.unit_id
                  FROM history h
                  JOIN quizzes q ON h.quiz_id = q.quiz_id
                  JOIN units u ON q.unit_id = u.unit_id
                  WHERE h.student_id = $student_id
                  ORDER BY h.ChangedAt DESC";
$history_result = $conn->query($history_query);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quiz History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-bottom: 30px;
        }
        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .grade-A { color: #28a745; font-weight: bold; }
        .grade-B { color: #17a2b8; font-weight: bold; }
        .grade-C { color: #ffc107; font-weight: bold; }
        .grade-D { color: #fd7e14; font-weight: bold; }
        .grade-F { color: #dc3545; font-weight: bold; }
        .status-Passed { 
            background-color: #d4edda; 
            color: #155724; 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-weight: bold;
        }
        .status-Failed { 
            background-color: #f8d7da; 
            color: #721c24; 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-weight: bold;
        }
        .no-history {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px 0;
        }
        .nav-buttons {
            margin-bottom: 20px;
        }
        .nav-buttons a {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
            display: inline-block;
        }
        .nav-buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quiz History</h1>
        
        <div class="nav-buttons">
            <a href="result.php">View Latest Results</a>
            <a href="quiz.php">Take New Quiz</a>
        </div>
        
        <?php if ($student): ?>
            <h2>Student: <?php echo htmlspecialchars($student['fullname']); ?> (ID: <?php echo $student['student_id']; ?>)</h2>
        <?php endif; ?>
        
        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($history_result && $history_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>History ID</th>
                        <th>Quiz Name</th>
                        <th>Unit</th>
                        <th>Score</th>
                        <th>Grade</th>
                        <th>Attempts</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['history_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['quiz_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['unit_name']); ?> (<?php echo htmlspecialchars($row['unit_id']); ?>)</td>
                            <td><?php echo number_format($row['score'], 2); ?>%</td>
                            <td><span class="grade-<?php echo $row['grade']; ?>"><?php echo $row['grade']; ?></span></td>
                            <td><?php echo $row['NumberOfAttempts']; ?></td>
                            <td><span class="status-<?php echo $row['UnitStatus']; ?>"><?php echo $row['UnitStatus']; ?></span></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($row['ChangedAt'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-history">
                <h3>No quiz history found</h3>
                <p>You haven't taken any quizzes yet. Take your first quiz to see your history here!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>