-- Создание таблицы групп
CREATE TABLE IF NOT EXISTS groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_number VARCHAR(10) NOT NULL UNIQUE
);

-- Создание таблицы студентов
CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    group_id INTEGER NOT NULL,
    gender CHAR(1) CHECK(gender IN ('M', 'F')),
    birth_date DATE,
    FOREIGN KEY (group_id) REFERENCES groups(id)
);

-- Создание таблицы дисциплин
CREATE TABLE IF NOT EXISTS subjects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    subject_name VARCHAR(100) NOT NULL,
    course_number INTEGER CHECK(course_number BETWEEN 1 AND 6),
    direction VARCHAR(50)
);

-- Создание таблицы экзаменов
CREATE TABLE IF NOT EXISTS exams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    subject_id INTEGER NOT NULL,
    exam_date DATE NOT NULL,
    grade INTEGER CHECK(grade BETWEEN 2 AND 5),
    teacher VARCHAR(100),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- Заполняем справочник групп
INSERT INTO groups (group_number) VALUES 
('101'), ('102'), ('103'), ('201'), ('202'), ('301'), ('302');

-- Заполняем справочник дисциплин
INSERT INTO subjects (subject_name, course_number, direction) VALUES
('Математика', 1, 'Общее'),
('Программирование', 1, 'Информатика'),
('Физика', 1, 'Общее'),
('Базы данных', 2, 'Информатика'),
('Web-технологии', 3, 'Информатика'),
('Операционные системы', 2, 'Информатика');

-- Пример студентов
INSERT INTO students (last_name, first_name, middle_name, group_id, gender, birth_date) VALUES
('Иванов', 'Иван', 'Иванович', 1, 'M', '2000-01-15'),
('Петрова', 'Мария', 'Сергеевна', 1, 'F', '2000-03-20'),
('Сидоров', 'Алексей', 'Петрович', 2, 'M', '1999-11-10');

-- Пример экзаменов
INSERT INTO exams (student_id, subject_id, exam_date, grade, teacher) VALUES
(1, 1, '2023-01-20', 5, 'Проф. Смирнов'),
(1, 2, '2023-01-25', 4, 'Доц. Козлова'),
(2, 1, '2023-01-20', 4, 'Проф. Смирнов');
