<?php
require_once 'config.php';
require_once 'functions.php';

$student_id = $_GET['student_id'] ?? 0;
$exams = getExamsByStudent($student_id);
$student = getStudentById($student_id);

if (!$student) {
    header('Location: index.php?message=Студент не найден');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты экзаменов</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .btn { 
            padding: 8px 15px; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block;
            margin-right: 10px;
            font-size: 14px;
        }
        .btn-back { background-color: #777; color: white; }
        .btn-add { background-color: #4CAF50; color: white; }
        .btn-edit { color: #2196F3; }
        .btn-delete { color: #f44336; }
    </style>
</head>
<body>
    <h1>Результаты экзаменов</h1>
    <h2>Студент: <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name'] . ' ' . $student['middle_name']) ?></h2>
    
    <table>
        <thead>
            <tr>
                <th>Дисциплина</th>
                <th>Дата экзамена</th>
                <th>Оценка</th>
                <th>Преподаватель</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($exams)): ?>
                <tr>
                    <td colspan="5">Нет данных об экзаменах</td>
                </tr>
            <?php else: ?>
                <?php foreach ($exams as $exam): ?>
                <tr>
                    <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                    <td><?= $exam['exam_date'] ?></td>
                    <td><?= $exam['grade'] ?></td>
                    <td><?= htmlspecialchars($exam['teacher']) ?></td>
                    <td>
                        <a href="edit_exam.php?id=<?= $exam['id'] ?>" class="btn-edit">Редактировать</a>
                        <a href="delete_exam.php?id=<?= $exam['id'] ?>" class="btn-delete" onclick="return confirm('Удалить запись об экзамене?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div>
        <a href="add_exam.php?student_id=<?= $student_id ?>" class="btn btn-add">Добавить экзамен</a>
        <a href="index.php" class="btn btn-back">Назад к списку</a>
    </div>
</body>
</html>
