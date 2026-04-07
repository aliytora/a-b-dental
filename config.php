<?php
// Защита от прямого доступа к файлу
if (!defined('ABSPATH')) {
    // Разрешаем только через include
    define('ABSPATH', true);
}

// Отключаем вывод ошибок на продакшене
error_reporting(0);
ini_set('display_errors', 0);

// Настройки подключения
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'ab_dental_db';

// Таймаут подключения (секунды)
ini_set('mysql.connect_timeout', 5);
ini_set('default_socket_timeout', 5);

// Подключение с обработкой ошибок
$conn = @new mysqli($host, $user, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    // Логируем ошибку (не показываем пользователю)
    error_log('Database connection failed: ' . $conn->connect_error);
    
    // Пользователю показываем общую ошибку
    die('Технические работы. Пожалуйста, зайдите позже.');
}

// Устанавливаем кодировку
$conn->set_charset('utf8mb4');

// Режим строгих типов для SQL
$conn->query("SET sql_mode = 'STRICT_ALL_TABLES'");