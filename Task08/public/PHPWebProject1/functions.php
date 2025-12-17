<?php

function getAllGroups()
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM groups ORDER BY group_number");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Ошибка при получении групп: " . $e->getMessage());
        return [];
    }
}

// Получение всех студентов (с фильтром по группе)
function getAllStudents($groupFilter = null)
{
    try {
        $pdo = getDBConnection();

        if ($groupFilter) {
            $sql = "SELECT s.*, g.group_number 
                    FROM students s 
                    LEFT JOIN groups g ON s.group_id = g.id 
                    WHERE s.group_id = :group_id 
                    ORDER BY g.group_number, s.last_name, s.first_name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['group_id' => $groupFilter]);
        } else {
            $sql = "SELECT s.*, g.group_number 
                    FROM students s 
                    LEFT JOIN groups g ON s.group_id = g.id 
                    ORDER BY g.group_number, s.last_name, s.first_name";
            $stmt = $pdo->query($sql);
        }

        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Ошибка при получении студентов: " . $e->getMessage());
        return [];
    }
}

// Получение студента по ID
function getStudentById($id)
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Ошибка при получении студента: " . $e->getMessage());
        return null;
    }
}

// Добавление студента
function addStudent($data)
{
    try {
        $pdo = getDBConnection();
        $sql = "INSERT INTO students (last_name, first_name, middle_name, group_id, gender, birth_date) 
                VALUES (:last_name, :first_name, :middle_name, :group_id, :gender, :birth_date)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    } catch (Exception $e) {
        error_log("Ошибка при добавлении студента: " . $e->getMessage());
        return false;
    }
}

// Обновление студента
function updateStudent($id, $data)
{
    try {
        $pdo = getDBConnection();
        $sql = "UPDATE students SET 
                last_name = :last_name,
                first_name = :first_name,
                middle_name = :middle_name,
                group_id = :group_id,
                gender = :gender,
                birth_date = :birth_date
                WHERE id = :id";
        $data['id'] = $id;
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    } catch (Exception $e) {
        error_log("Ошибка при обновлении студента: " . $e->getMessage());
        return false;
    }
}

// Удаление студента
function deleteStudent($id)
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        error_log("Ошибка при удалении студента: " . $e->getMessage());
        return false;
    }
}

// Получение экзаменов студента
function getExamsByStudent($studentId)
{
    try {
        $pdo = getDBConnection();
        $sql = "SELECT e.*, s.subject_name 
                FROM exams e 
                JOIN subjects s ON e.subject_id = s.id 
                WHERE e.student_id = ? 
                ORDER BY e.exam_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Ошибка при получении экзаменов: " . $e->getMessage());
        return [];
    }
}

// Получение всех дисциплин
function getAllSubjects()
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Ошибка при получении дисциплин: " . $e->getMessage());
        return [];
    }
}

// Добавление экзамена
function addExam($data)
{
    try {
        $pdo = getDBConnection();
        $sql = "INSERT INTO exams (student_id, subject_id, exam_date, grade, teacher) 
                VALUES (:student_id, :subject_id, :exam_date, :grade, :teacher)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    } catch (Exception $e) {
        error_log("Ошибка при добавлении экзамена: " . $e->getMessage());
        return false;
    }
}

// Получение экзамена по ID
function getExamById($id)
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Ошибка при получении экзамена: " . $e->getMessage());
        return null;
    }
}

// Обновление экзамена
function updateExam($id, $data)
{
    try {
        $pdo = getDBConnection();
        $sql = "UPDATE exams SET 
                student_id = :student_id,
                subject_id = :subject_id,
                exam_date = :exam_date,
                grade = :grade,
                teacher = :teacher
                WHERE id = :id";
        $data['id'] = $id;
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    } catch (Exception $e) {
        error_log("Ошибка при обновлении экзамена: " . $e->getMessage());
        return false;
    }
}

// Удаление экзамена
function deleteExam($id)
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM exams WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        error_log("Ошибка при удалении экзамена: " . $e->getMessage());
        return false;
    }
}
?>