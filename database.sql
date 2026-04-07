-- Создание базы данных
CREATE DATABASE IF NOT EXISTS ab_dental_db;
USE ab_dental_db;

-- Таблица категорий
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_id INT DEFAULT 0,
    sort_order INT DEFAULT 0
);

-- Таблица продуктов (расширенная)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Таблица заявок
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    product_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'новая'
);

-- Таблица статистики посещений
CREATE TABLE visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45),
    page VARCHAR(255),
    visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Вставка категорий
INSERT INTO categories (name, parent_id, sort_order) VALUES
('Имплантаты', 0, 1),
('Формирователь десны', 0, 2),
('Абатменты', 0, 3),
('Трансферы, аналоги', 0, 4),
('Хирургические наборы', 0, 5),
('Инструменты', 0, 6),
('Фрезы', 0, 7),
('Сертификаты', 0, 8),
('Упаковка', 0, 9);

-- Подкатегории Имплантатов
INSERT INTO categories (name, parent_id, sort_order) VALUES
('I2', 1, 1),
('I5', 1, 2),
('I6', 1, 3),
('I6B', 1, 4),
('I7', 1, 5),
('I10', 1, 6);

-- Подкатегории Формирователь десны
INSERT INTO categories (name, parent_id, sort_order) VALUES
('Широкий формирователь десны', 2, 1),
('Узкий формирователь десны', 2, 2);

-- Подкатегории Абатментов
INSERT INTO categories (name, parent_id, sort_order) VALUES
('Временные абатменты', 3, 1),
('Пластиковый абатмент', 3, 2),
('Пластиковый абатмент с титановой основой', 3, 3),
('Антиротационный эстетический абатмент', 3, 4),
('Прямой адаптер', 3, 5),
('Угловые абатменты', 3, 6),
('Абатмент с плоским соединением', 3, 7),
('Абатмент без шестигранника', 3, 8),
('Антиротационный абатмент', 3, 9),
('Узкий антиротационный абатмент', 3, 10),
('Широкий антиротационный абатмент', 3, 11),
('Анатомический антиротационный абатмент', 3, 12),
('Угловой Адаптер', 3, 13),
('Анатомический угловой абатмент', 3, 14),
('Шаровидные абатменты', 3, 15),
('Локатор', 3, 16);

-- Подкатегории Трансферы, аналоги
INSERT INTO categories (name, parent_id, sort_order) VALUES
('Аналог имплантата', 4, 1),
('Трансферы', 4, 2);

-- Добавление продуктов с описаниями
INSERT INTO products (category_id, name, description) VALUES
(2, 'I2', 'Имплантат I2 - классическая модель для стандартных клинических случаев.'),
(3, 'I5', 'Имплантат I5 - увеличенный диаметр для жевательной группы зубов.'),
(4, 'I6', 'Имплантат I6 - оптимальное соотношение цены и качества.'),
(5, 'I6B', 'Имплантат I6B - усиленная версия с улучшенной стабильностью.'),
(6, 'I7', 'Имплантат I7 - для сложных клинических случаев.'),
(7, 'I10', 'Имплантат I10 - максимальная длина для глубокой фиксации.');