<?php
require_once 'config.php';
require_once 'functions.php';

$id = $_GET['id'] ?? 0;
$student = getStudentById($id);

if (!$student) {
    header('Location: index.php?message=Студент не найден');
    exit();
}

$groups = getAllGroups();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'last_name' => $_POST['last_name'],
        'first_name' => $_POST['first_name'],
        'middle_name' => $_POST['middle_name'],
        'group_id' => $_POST['group_id'],
        'gender' => $_POST['gender'],
        'birth_date' => $_POST['birth_date']
    ];
    
    if (updateStudent($id, $data)) {
        header('Location: index.php?message=Студент обновлен');
        exit();
    } else {
        $error = "Ошибка при обновлении студента";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать студента</title>
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
    <h1>Редактировать студента</h1>
    
    <?php if (isset($error)): ?>
        <div style="color: red; margin: 10px 0;"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label for="last_name">Фамилия *</label>
                <input type="text" id="last_name" name="last_name" 
                       value="<?= htmlspecialchars($student['last_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="first_name">Имя *</label>
                <input type="text" id="first_name" name="first_name" 
                       value="<?= htmlspecialchars($student['first_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="middle_name">Отчество</label>
                <input type="text" id="middle_name" name="middle_name" 
                       value="<?= htmlspecialchars($student['middle_name']) ?>">
            </div>
            
            <div class="form-group">
                <label for="group_id">Группа *</label>
                <select id="group_id" name="group_id" required>
                    <option value="">Выберите группу</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group['id'] ?>" 
                            <?= $student['group_id'] == $group['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($group['group_number']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Пол *</label>
                <div>
                    <input type="radio" id="male" name="gender" value="M" 
                           <?= $student['gender'] == 'M' ? 'checked' : '' ?> required>
                    <label for="male" style="display: inline;">Мужской</label>
                    
                    <input type="radio" id="female" name="gender" value="F" 
                           <?= $student['gender'] == 'F' ? 'checked' : '' ?> required>
                    <label for="female" style="display: inline;">Женский</label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="birth_date">Дата рождения</label>
                <input type="date" id="birth_date" name="birth_date" 
                       value="<?= $student['birth_date'] ?>">
            </div>
            
            <div>
                <button type="submit" class="btn btn-submit">Сохранить</button>
                <a href="index.php" class="btn btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
