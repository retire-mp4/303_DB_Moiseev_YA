-- База данных для СТО (Станции Технического Обслуживания автомобилей)
-- Автор: Ян Моисеев
-- Дата: $(date)

-- Удаление существующих таблиц (в обратном порядке из-за внешних ключей)
DROP TABLE IF EXISTS salary_calculations;
DROP TABLE IF EXISTS completed_works;
DROP TABLE IF EXISTS appointment_services;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS cars;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS positions;
DROP TABLE IF EXISTS service_categories;

-- 1. Таблица должностей (справочник)
CREATE TABLE positions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL UNIQUE,
    base_salary_rate REAL DEFAULT 0.0,
    description TEXT
);

-- 2. Таблица сотрудников (мастеров)
CREATE TABLE employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    position_id INTEGER NOT NULL,
    hire_date TEXT NOT NULL DEFAULT (date('now')),
    dismissal_date TEXT,
    salary_percent REAL CHECK(salary_percent >= 0 AND salary_percent <= 100) DEFAULT 30.0,
    phone TEXT,
    email TEXT UNIQUE,
    work_schedule TEXT, -- график работы (например: "Пн-Пт 9:00-18:00")
    is_active INTEGER CHECK(is_active IN (0, 1)) DEFAULT 1, -- 1 - работает, 0 - уволен
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE RESTRICT
);

-- 3. Таблица категорий услуг
CREATE TABLE service_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT
);

-- 4. Таблица услуг (справочник)
CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category_id INTEGER NOT NULL,
    duration_minutes INTEGER CHECK(duration_minutes > 0) NOT NULL,
    price REAL CHECK(price >= 0) NOT NULL,
    description TEXT,
    is_active INTEGER CHECK(is_active IN (0, 1)) DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE RESTRICT
);

-- 5. Таблица клиентов
CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    phone TEXT NOT NULL UNIQUE,
    email TEXT,
    registration_date TEXT DEFAULT (date('now')),
    address TEXT
);

-- 6. Таблица автомобилей
CREATE TABLE cars (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    brand TEXT NOT NULL,
    model TEXT NOT NULL,
    year INTEGER CHECK(year >= 1900 AND year <= CAST(strftime('%Y', 'now') AS INTEGER)),
    vin TEXT UNIQUE, -- идентификационный номер транспортного средства
    license_plate TEXT UNIQUE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- 7. Таблица записей на прием
CREATE TABLE appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    car_id INTEGER NOT NULL,
    employee_id INTEGER NOT NULL,
    appointment_date TEXT NOT NULL,
    appointment_time TEXT NOT NULL,
    status TEXT CHECK(status IN ('запланировано', 'в процессе', 'завершено', 'отменено')) DEFAULT 'запланировано',
    total_price REAL DEFAULT 0.0,
    notes TEXT,
    created_at TEXT DEFAULT (datetime('now', 'localtime')),
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT
);

-- 8. Таблица связи записей и услуг (многие-ко-многим)
CREATE TABLE appointment_services (
    appointment_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    quantity INTEGER CHECK(quantity > 0) DEFAULT 1,
    price_at_time REAL NOT NULL, -- цена на момент оказания услуги
    PRIMARY KEY (appointment_id, service_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

-- 9. Таблица выполненных работ
CREATE TABLE completed_works (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    employee_id INTEGER NOT NULL,
    start_time TEXT NOT NULL,
    end_time TEXT NOT NULL,
    actual_duration_minutes INTEGER CHECK(actual_duration_minutes > 0),
    notes TEXT,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT
);

-- 10. Таблица расчетов зарплаты
CREATE TABLE salary_calculations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    calculation_date TEXT DEFAULT (date('now')),
    period_start TEXT NOT NULL,
    period_end TEXT NOT NULL,
    total_revenue REAL CHECK(total_revenue >= 0) NOT NULL,
    salary_percent REAL CHECK(salary_percent >= 0 AND salary_percent <= 100) NOT NULL,
    calculated_salary REAL CHECK(calculated_salary >= 0) NOT NULL,
    notes TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT
);

-- Заполнение справочников тестовыми данными

-- Должности
INSERT INTO positions (title, base_salary_rate, description) VALUES
('Мастер-приемщик', 15.0, 'Прием автомобилей, консультация клиентов'),
('Автомеханик', 30.0, 'Ремонт и обслуживание автомобилей'),
('Автоэлектрик', 35.0, 'Диагностика и ремонт электрооборудования'),
('Маляр', 25.0, 'Кузовные работы, покраска'),
('Мойщик', 10.0, 'Мойка и уборка автомобилей');

-- Сотрудники
INSERT INTO employees (full_name, position_id, hire_date, phone, email, work_schedule, salary_percent) VALUES
('Иванов Петр Сергеевич', 2, '2020-03-15', '+79101234567', 'ivanov@sto.ru', 'Пн-Пт 9:00-18:00', 30.0),
('Сидорова Мария Владимировна', 1, '2021-06-10', '+79107654321', 'sidorova@sto.ru', 'Пн-Сб 8:00-17:00', 15.0),
('Кузнецов Алексей Иванович', 3, '2019-11-05', '+79109876543', 'kuznetsov@sto.ru', 'Вт-Сб 10:00-19:00', 35.0),
('Уволенный Сотрудник', 4, '2022-01-15', '+79105555555', 'uvolenny@sto.ru', 'Пн-Пт 9:00-18:00', 25.0);

-- Обновляем уволенного сотрудника
UPDATE employees SET dismissal_date = '2023-12-31', is_active = 0 WHERE id = 4;

-- Категории услуг
INSERT INTO service_categories (name, description) VALUES
('Диагностика', 'Комплексная диагностика автомобиля'),
('Техническое обслуживание', 'Регламентное ТО, замена жидкостей и фильтров'),
('Ремонт двигателя', 'Ремонт и замена деталей двигателя'),
('Ремонт ходовой части', 'Ремонт подвески, тормозной системы'),
('Кузовные работы', 'Ремонт и покраска кузова'),
('Электрика', 'Ремонт электрооборудования');

-- Услуги
INSERT INTO services (name, category_id, duration_minutes, price, description) VALUES
('Компьютерная диагностика', 1, 60, 1500.00, 'Диагностика электронных систем'),
('Диагностика подвески', 1, 45, 1200.00, 'Проверка элементов подвески'),
('Замена масла двигателя', 2, 30, 2500.00, 'Замена моторного масла и фильтра'),
('Замена тормозных колодок', 4, 60, 3000.00, 'Замена передних или задних колодок'),
('Ремонт стартера', 6, 120, 5000.00, 'Ремонт или замена стартера'),
('Покраска бампера', 5, 240, 8000.00, 'Полная покраска бампера'),
('Замена аккумулятора', 6, 30, 1500.00, 'Замена аккумуляторной батареи');

-- Клиенты
INSERT INTO clients (full_name, phone, email, registration_date) VALUES
('Смирнов Андрей Викторович', '+79211112233', 'smirnov@mail.ru', '2023-01-15'),
('Петрова Ольга Ивановна', '+79214445566', 'petrova@gmail.com', '2023-02-20'),
('Козлов Дмитрий Сергеевич', '+79217778899', 'kozlov@yandex.ru', '2023-03-10');

-- Автомобили
INSERT INTO cars (client_id, brand, model, year, vin, license_plate) VALUES
(1, 'Toyota', 'Camry', 2018, 'JTNBB46KX00123456', 'А123ВС777'),
(1, 'BMW', 'X5', 2020, 'WBANF51000C987654', 'О456ТУ777'),
(2, 'Lada', 'Vesta', 2021, 'XTA21924012345678', 'У789ХХ777'),
(3, 'Kia', 'Rio', 2019, 'KNAFK8110K5123456', 'Е321КО777');

-- Записи на прием
INSERT INTO appointments (client_id, car_id, employee_id, appointment_date, appointment_time, status, total_price) VALUES
(1, 1, 2, '2024-01-15', '10:00', 'завершено', 4000.00),
(2, 3, 1, '2024-01-16', '14:30', 'в процессе', 3000.00),
(3, 4, 3, '2024-01-17', '11:00', 'запланировано', 1500.00);

-- Услуги в записях
INSERT INTO appointment_services (appointment_id, service_id, quantity, price_at_time) VALUES
(1, 3, 1, 2500.00), -- Замена масла
(1, 7, 1, 1500.00), -- Замена аккумулятора
(2, 4, 1, 3000.00), -- Замена тормозных колодок
(3, 1, 1, 1500.00); -- Компьютерная диагностика

-- Выполненные работы
INSERT INTO completed_works (appointment_id, service_id, employee_id, start_time, end_time, actual_duration_minutes) VALUES
(1, 3, 1, '2024-01-15 10:00', '2024-01-15 10:35', 35),
(1, 7, 3, '2024-01-15 10:40', '2024-01-15 11:00', 20);

-- Расчеты зарплаты
INSERT INTO salary_calculations (employee_id, period_start, period_end, total_revenue, salary_percent, calculated_salary) VALUES
(1, '2024-01-01', '2024-01-15', 50000.00, 30.0, 15000.00),
(2, '2024-01-01', '2024-01-15', 30000.00, 15.0, 4500.00),
(3, '2024-01-01', '2024-01-15', 40000.00, 35.0, 14000.00);

-- Создание индексов для оптимизации запросов
CREATE INDEX idx_employees_full_name ON employees(full_name);
CREATE INDEX idx_employees_is_active ON employees(is_active);
CREATE INDEX idx_clients_phone ON clients(phone);
CREATE INDEX idx_appointments_date ON appointments(appointment_date);
CREATE INDEX idx_appointments_status ON appointments(status);
CREATE INDEX idx_appointments_employee_id ON appointments(employee_id);
CREATE INDEX idx_services_category_id ON services(category_id);
CREATE INDEX idx_cars_client_id ON cars(client_id);
CREATE INDEX idx_completed_works_appointment_id ON completed_works(appointment_id);
CREATE INDEX idx_salary_calculations_employee_id ON salary_calculations(employee_id);
CREATE INDEX idx_salary_calculations_period ON salary_calculations(period_start, period_end);

-- Триггеры для автоматического обновления

-- Триггер для обновления общей суммы в записи при добавлении услуги
CREATE TRIGGER update_appointment_total_price_insert
AFTER INSERT ON appointment_services
BEGIN
    UPDATE appointments 
    SET total_price = (
        SELECT SUM(price_at_time * quantity) 
        FROM appointment_services 
        WHERE appointment_id = NEW.appointment_id
    )
    WHERE id = NEW.appointment_id;
END;

-- Триггер для обновления общей суммы в записи при удалении услуги
CREATE TRIGGER update_appointment_total_price_delete
AFTER DELETE ON appointment_services
BEGIN
    UPDATE appointments 
    SET total_price = (
        SELECT COALESCE(SUM(price_at_time * quantity), 0)
        FROM appointment_services 
        WHERE appointment_id = OLD.appointment_id
    )
    WHERE id = OLD.appointment_id;
END;

-- Триггер для проверки доступности мастера при записи
CREATE TRIGGER check_employee_availability
BEFORE INSERT ON appointments
BEGIN
    SELECT CASE
        WHEN EXISTS (
            SELECT 1 FROM appointments a
            WHERE a.employee_id = NEW.employee_id
                AND a.appointment_date = NEW.appointment_date
                AND a.appointment_time = NEW.appointment_time
                AND a.status != 'отменено'
        ) THEN
            RAISE(ABORT, 'Мастер уже занят в это время')
    END;
END;
