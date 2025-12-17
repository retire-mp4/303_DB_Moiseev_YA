-- База данных для студентов
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS groups;

-- Таблица учебных групп
CREATE TABLE groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_number TEXT NOT NULL UNIQUE,
    specialization TEXT NOT NULL,
    end_year INTEGER NOT NULL
);

-- Таблица студентов
CREATE TABLE students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER NOT NULL,
    full_name TEXT NOT NULL,
    gender TEXT CHECK(gender IN ('M', 'F')) NOT NULL,
    birth_date TEXT NOT NULL,
    student_card_number TEXT NOT NULL UNIQUE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE
);

-- Вставка тестовых данных групп
INSERT INTO groups (group_number, specialization, end_year) VALUES
('ПИ-101', 'Программная инженерия', 2024),
('ПИ-102', 'Программная инженерия', 2025),
('ИВТ-201', 'Информатика и вычислительная техника', 2024),
('ИВТ-202', 'Информатика и вычислительная техника', 2023),
('ФИИТ-301', 'Фундаментальная информатика и информационные технологии', 2024),
('БИ-401', 'Бизнес-информатика', 2025);

-- Вставка тестовых данных студентов
INSERT INTO students (group_id, full_name, gender, birth_date, student_card_number) VALUES
(1, 'Иванов Иван Иванович', 'M', '2002-05-15', '2020ПИ101001'),
(1, 'Петрова Анна Сергеевна', 'F', '2003-03-20', '2020ПИ101002'),
(1, 'Сидоров Алексей Петрович', 'M', '2002-11-05', '2020ПИ101003'),
(2, 'Кузнецова Мария Владимировна', 'F', '2003-06-30', '2020ПИ102001'),
(2, 'Антонов Дмитрий Николаевич', 'M', '2002-02-14', '2020ПИ102002'),
(3, 'Фролова Екатерина Андреевна', 'F', '2001-12-25', '2019ИВТ201001'),
(3, 'Григорьев Павел Олегович', 'M', '2002-04-10', '2019ИВТ201002'),
(3, 'Жукова Алина Игоревна', 'F', '2002-07-22', '2019ИВТ201003'),
(4, 'Смирнов Андрей Викторович', 'M', '2000-08-18', '2018ИВТ202001'),
(4, 'Козлова Ольга Дмитриевна', 'F', '2001-01-12', '2018ИВТ202002'),
(5, 'Морозов Илья Сергеевич', 'M', '2001-09-30', '2019ФИИТ301001'),
(5, 'Павлова Виктория Александровна', 'F', '2002-03-08', '2019ФИИТ301002'),
(6, 'Волков Артем Игоревич', 'M', '2003-10-25', '2020БИ401001'),
(6, 'Семенова Дарья Павловна', 'F', '2003-12-03', '2020БИ401002');

-- Индексы для оптимизации запросов
CREATE INDEX idx_groups_end_year ON groups(end_year);
CREATE INDEX idx_students_group_id ON students(group_id);
CREATE INDEX idx_students_full_name ON students(full_name);
