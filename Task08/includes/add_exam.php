<?php
require_once 'config.php';
require_once 'functions.php';

$student_id = $_GET['student_id'] ?? 0;
$student = getStudentById($student_id);
$subjects = getAllSubjects();

if (!$student) {
    header('Location: index.php?message=Студент не найден');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'student_id' => $student_id,
        'subject_id' => $_POST['subject_id'],
        'exam_date' => $_POST['exam_date'],
        'grade' => $_POST['grade'],
        'teacher' => $_POST['teacher']
    ];
    
    if (addExam($data)) {
        header("Location: exams.php?student_id=$student_id&message=Экзамен добавлен");
        exit();
    } else {
        $error = "Ошибка при добавлении экзамена";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить экзамен</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-container { max-width: 500px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block;
            margin-right: 10px;
        }
        .btn-submit { background-color: #4CAF50; color: white; border: none; }
        .btn-cancel { background-color: #777; color: white; }
    </style>
</head>
<body>
    <h1>Добавить экзамен</h1>
    <h2>Студент: <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name']) ?></h2>
    
    <?php if (isset($error)): ?>
        <div style="color: red; margin: 10px 0;"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label for="subject_id">Дисциплина *</label>
                <select id="subject_id" name="subject_id" required>
                    <option value="">Выберите дисциплину</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>">
                            <?= htmlspecialchars($subject['subject_name']) ?> 
                            (Курс: <?= $subject['course_number'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="exam_date">Дата экзамена *</label>
                <input type="date" id="exam_date" name="exam_date" required>
            </div>
            
            <div class="form-group">
                <label for="grade">Оценка *</label>
                <select id="grade" name="grade" required>
                    <option value="">Выберите оценку</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="teacher">Преподаватель</label>
                <input type="text" id="teacher" name="teacher">
            </div>
            
            <div>
                <button type="submit" class="btn btn-submit">Добавить</button>
                <a href="exams.php?student_id=<?= $student_id ?>" class="btn btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
