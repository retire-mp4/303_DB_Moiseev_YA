<?php
/**
 * Лабораторная работа 7 - Консольное приложение для отображения студентов
 */

// Настройки отображения ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к базе данных SQLite
try {
    $db = new PDO('sqlite:students.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA encoding = "UTF-8"');
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получаем текущий год
$current_year = date('Y');

// Функция для вывода таблицы в псевдографике
function printTable($data) {
    if (empty($data)) {
        echo "Нет данных для отображения.\n";
        return;
    }
    
    // Определяем ширину колонок
    $widths = [
        'group' => 10,
        'specialization' => 25,
        'name' => 25,
        'gender' => 6,
        'birth_date' => 15,
        'student_card' => 20
    ];
    
    // Заголовок таблицы
    echo "\n";
    echo str_repeat('-', array_sum($widths) + count($widths) * 3 + 1) . "\n";
    echo '| ' . str_pad('Группа', $widths['group']) . ' | ';
    echo str_pad('Направление', $widths['specialization']) . ' | ';
    echo str_pad('ФИО', $widths['name']) . ' | ';
    echo str_pad('Пол', $widths['gender']) . ' | ';
    echo str_pad('Дата рождения', $widths['birth_date']) . ' | ';
    echo str_pad('Студ. билет', $widths['student_card']) . " |\n";
    echo str_repeat('-', array_sum($widths) + count($widths) * 3 + 1) . "\n";
    
    // Данные таблицы
    foreach ($data as $row) {
        echo '| ' . str_pad($row['group_number'], $widths['group']) . ' | ';
        echo str_pad($row['specialization'], $widths['specialization']) . ' | ';
        echo str_pad($row['full_name'], $widths['name']) . ' | ';
        echo str_pad(($row['gender'] == 'M' ? 'Муж' : 'Жен'), $widths['gender']) . ' | ';
        echo str_pad($row['birth_date'], $widths['birth_date']) . ' | ';
        echo str_pad($row['student_card_number'], $widths['student_card']) . " |\n";
    }
    
    echo str_repeat('-', array_sum($widths) + count($widths) * 3 + 1) . "\n";
    echo "Всего записей: " . count($data) . "\n\n";
}

// Получаем список действующих групп (end_year >= текущий год)
try {
    $stmt = $db->prepare("
        SELECT DISTINCT group_number 
        FROM groups 
        WHERE end_year >= :current_year 
        ORDER BY group_number
    ");
    $stmt->execute(['current_year' => $current_year]);
    $active_groups = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Ошибка получения списка групп: " . $e->getMessage());
}

// Выводим доступные группы
echo "================ СИСТЕМА УЧЕТА СТУДЕНТОВ ================\n";
echo "Текущий год: $current_year\n";
echo "Действующие группы (end_year >= $current_year):\n";

if (empty($active_groups)) {
    echo "Нет действующих групп.\n";
    exit(0);
}

foreach ($active_groups as $index => $group) {
    echo ($index + 1) . ". $group\n";
}

// Запрос номера группы для фильтрации
echo "\nВведите номер группы для фильтрации (или нажмите Enter для всех групп): ";
$selected_group = trim(fgets(STDIN));

// Валидация введенного номера группы
if (!empty($selected_group)) {
    if (!in_array($selected_group, $active_groups)) {
        echo "Ошибка: Группа '$selected_group' не найдена или не является действующей.\n";
        echo "Доступные группы: " . implode(', ', $active_groups) . "\n";
        exit(1);
    }
    
    // Получаем студентов выбранной группы
    try {
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
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Ошибка получения данных студентов: " . $e->getMessage());
    }
    
    echo "\nСтуденты группы '$selected_group':\n";
} else {
    // Получаем всех студентов действующих групп
    try {
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
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Ошибка получения данных студентов: " . $e->getMessage());
    }
    
    echo "\nВсе студенты действующих групп:\n";
}

// Выводим результат
printTable($students);
?>
