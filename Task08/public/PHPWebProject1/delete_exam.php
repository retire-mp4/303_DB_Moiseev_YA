<?php
require_once 'config.php';
require_once 'functions.php';

$id = $_GET['id'] ?? 0;
$exam = getExamById($id);

if (!$exam) {
    header('Location: index.php?message=Экзамен не найден');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        if (deleteExam($id)) {
            header("Location: exams.php?student_id={$exam['student_id']}&message=Экзамен удален");
            exit();
        } else {
            $error = "Ошибка при удалении экзамена";
        }
    } else {
        header("Location: exams.php?student_id={$exam['student_id']}");
        exit();
    }
}

$student = getStudentById($exam['student_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удалить экзамен</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .confirmation { 
            max-width: 500px; 
            padding: 20px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            background-color: #f9f9f9;
        }
        .btn { 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block;
            margin-right: 10px;
            border: none;
            cursor: pointer;
        }
        .btn-yes { background-color: #f44336; color: white; }
        .btn-no { background-color: #777; color: white; }
    </style>
</head>
<body>
    <h1>Удалить экзамен</h1>
    
    <?php if (isset($error)): ?>
        <div style="color: red; margin: 10px 0;"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="confirmation">
        <p>Вы уверены, что хотите удалить экзамен студента:</p>
        <p><strong><?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name']) ?></strong></p>
        
        <form method="POST">
            <input type="hidden" name="confirm" value="yes">
            <button type="submit" class="btn btn-yes">Да, удалить</button>
            <a href="exams.php?student_id=<?= $exam['student_id'] ?>" class="btn btn-no">Нет, отмена</a>
        </form>
    </div>
</body>
</html>
