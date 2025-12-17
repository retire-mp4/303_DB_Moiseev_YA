<?php
// Конфигурация базы данных
// Файлы находятся в той же папке
define('DB_PATH', __DIR__ . '/university.db');
define('INIT_SQL_PATH', __DIR__ . '/init.sql');

// Создание подключения к SQLite
function getDBConnection()
{
    try {
        // Создаем соединение с базой данных
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}

// Инициализация базы данных
function initDatabase()
{
    try {
        // Проверяем существование файла базы
        if (!file_exists(DB_PATH)) {
            // Создаем пустой файл базы данных
            file_put_contents(DB_PATH, '');
        }

        $pdo = getDBConnection();

        // Проверяем существование таблиц
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);

        // Если таблиц нет, инициализируем
        if (empty($tables)) {
            if (file_exists(INIT_SQL_PATH)) {
                $sql = file_get_contents(INIT_SQL_PATH);
                $pdo->exec($sql);
                error_log("База данных инициализирована из init.sql");
            } else {
                error_log("Файл init.sql не найден по пути: " . INIT_SQL_PATH);
            }
        }

        return true;
    } catch (Exception $e) {
        error_log("Ошибка инициализации базы данных: " . $e->getMessage());
        return false;
    }
}
?>