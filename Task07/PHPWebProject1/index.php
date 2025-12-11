<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система учета студентов</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .current-year {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .filter-form {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        select, button {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        select:focus {
            outline: none;
            border-color: #3498db;
        }
        
        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }
        
        .students-table th {
            background-color: #2c3e50;
            color: white;
            text-align: left;
            padding: 15px;
            font-weight: 600;
        }
        
        .students-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .students-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .gender-male { color: #3498db; }
        .gender-female { color: #e74c3c; }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .stats {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            font-weight: 600;
        }
        
        footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .students-table {
                font-size: 14px;
            }
            
            .students-table th,
            .students-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 Система учета студентов</h1>
            <div class="current-year">
                Текущий год: <?php echo date('Y'); ?>
            </div>
        </header>

        <div class="filter-form">
            <form method="GET" action="">
                <div class="form-group">
                    <label for="group">Выберите группу для фильтрации:</label>
                    <select name="group" id="group">
                        <option value="">Все действующие группы</option>
                        <?php
                        try {
                            $db = new PDO('sqlite:students.db');
                            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $current_year = date('Y');
                            $stmt = $db->prepare("
                                SELECT DISTINCT group_number 
                                FROM groups 
                                WHERE end_year >= :current_year 
                                ORDER BY group_number
                            ");
                            $stmt->execute(['current_year' => $current_year]);
                            $groups = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            
                            $selected_group = $_GET['group'] ?? '';
                            
                            foreach ($groups as $group):
                        ?>
                            <option value="<?php echo htmlspecialchars($group); ?>"
                                <?php echo ($selected_group == $group) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($group); ?>
                            </option>
                        <?php
                            endforeach;
                        } catch (PDOException $e) {
                            echo '<option value="">Ошибка загрузки групп</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit">Показать студентов</button>
            </form>
        </div>

        <?php
        try {
            $db = new PDO('sqlite:students.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $current_year = date('Y');
            $selected_group = $_GET['group'] ?? '';
            
            // Подготовка запроса в зависимости от выбора группы
            if (!empty($selected_group)) {
                $stmt = $db->prepare("
                    SELECT 
                        g.group_number,
                        g.specialization,
                        s.full_name,
                        s.gender,
                        s.birth_date,
                        s.student_card_number
                    FROM students s
                    JOIN groups g ON s.group_id = g.id
                    WHERE g.group_number = :group_number 
                        AND g.end_year >= :current_year
                    ORDER BY g.group_number, s.full_name
                ");
                $stmt->execute([
                    'group_number' => $selected_group,
                    'current_year' => $current_year
                ]);
            } else {
                $stmt = $db->prepare("
                    SELECT 
                        g.group_number,
                        g.specialization,
                        s.full_name,
                        s.gender,
                        s.birth_date,
                        s.student_card_number
                    FROM students s
                    JOIN groups g ON s.group_id = g.id
                    WHERE g.end_year >= :current_year
                    ORDER BY g.group_number, s.full_name
                ");
                $stmt->execute(['current_year' => $current_year]);
            }
            
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($students)):
        ?>
        
        <div class="table-container">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>Группа</th>
                        <th>Направление подготовки</th>
                        <th>ФИО</th>
                        <th>Пол</th>
                        <th>Дата рождения</th>
                        <th>Номер студенческого билета</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['group_number']); ?></td>
                        <td><?php echo htmlspecialchars($student['specialization']); ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td>
                            <span class="<?php echo ($student['gender'] == 'M') ? 'gender-male' : 'gender-female'; ?>">
                                <?php echo ($student['gender'] == 'M') ? '♂ Мужской' : '♀ Женский'; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($student['birth_date']); ?></td>
                        <td><?php echo htmlspecialchars($student['student_card_number']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="stats">
                Найдено студентов: <?php echo count($students); ?> 
                <?php if (!empty($selected_group)): ?>
                    в группе <?php echo htmlspecialchars($selected_group); ?>
                <?php else: ?>
                    во всех действующих группах
                <?php endif; ?>
            </div>
        </div>
        
        <?php else: ?>
        
        <div class="no-data">
            <p>❌ Нет данных для отображения.</p>
            <p>Пожалуйста, выберите другую группу или проверьте фильтры.</p>
        </div>
        
        <?php
            endif;
            
        } catch (PDOException $e) {
            echo '<div class="no-data">';
            echo '<p>Ошибка подключения к базе данных: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>
        
        <footer>
            <p>Система учета студентов &copy; <?php echo date('Y'); ?></p>
            <p>Лабораторная работа 7 - Веб-приложение для отображения данных из БД</p>
        </footer>
    </div>
</body>
</html>
