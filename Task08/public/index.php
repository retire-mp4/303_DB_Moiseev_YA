<?php
// Подключаем файлы из той же папки
require_once 'config.php';
require_once 'functions.php';

// Инициализируем базу данных
initDatabase();

// Получаем параметр фильтра по группе
$groupFilter = isset($_GET['group']) && $_GET['group'] !== '' ? $_GET['group'] : null;

// Получаем студентов с учетом фильтра
$students = getAllStudents($groupFilter);
$groups = getAllGroups();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление студентами</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .actions a { margin-right: 10px; text-decoration: none; }
        .btn-edit { color: #2196F3; }
        .btn-delete { color: #f44336; }
        .btn-exams { color: #4CAF50; }
        .btn-add { 
            display: inline-block; 
            background-color: #4CAF50; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin-top: 20px; 
        }
        .filter-form { margin-bottom: 20px; }
        .message { 
            background-color: #d4edda; 
            color: #155724; 
            padding: 10px; 
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Список студентов</h1>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="message"><?= htmlspecialchars($_GET['message']) ?></div>
    <?php endif; ?>
    
    <!-- Фильтр по группам -->
    <form method="GET" action="" class="filter-form">
        <label for="group">Фильтр по группе:</label>
        <select name="group" id="group" onchange="this.form.submit()">
            <option value="">Все группы</option>
            <?php foreach ($groups as $group): ?>
                <option value="<?= $group['id'] ?>" 
                    <?= isset($_GET['group']) && $_GET['group'] == $group['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($group['group_number']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($groupFilter): ?>
            <a href="index.php" style="margin-left: 10px;">Сбросить фильтр</a>
        <?php endif; ?>
    </form>
    
    <!-- Таблица студентов -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Группа</th>
                <th>Пол</th>
                <th>Дата рождения</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($students)): ?>
                <tr>
                    <td colspan="8">Нет данных о студентах</td>
                </tr>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['id'] ?></td>
                    <td><?= htmlspecialchars($student['last_name']) ?></td>
                    <td><?= htmlspecialchars($student['first_name']) ?></td>
                    <td><?= htmlspecialchars($student['middle_name']) ?></td>
                    <td><?= htmlspecialchars($student['group_number']) ?></td>
                    <td><?= $student['gender'] == 'M' ? 'Мужской' : 'Женский' ?></td>
                    <td><?= $student['birth_date'] ?></td>
                    <td class="actions">
                        <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn-edit">Редактировать</a>
                        <a href="delete_student.php?id=<?= $student['id'] ?>" class="btn-delete" 
                           onclick="return confirm('Удалить студента <?= htmlspecialchars($student['last_name']) ?>?')">Удалить</a>
                        <a href="exams.php?student_id=<?= $student['id'] ?>" class="btn-exams">Экзамены</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <a href="add_student.php" class="btn-add">Добавить студента</a>
</body>
</html>