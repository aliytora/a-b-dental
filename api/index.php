<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверяем, что PHP вообще работает
echo "<h1>Диагностика PHP</h1>";
echo "Версия PHP: " . phpversion() . "<br>";
echo "Текущая директория: " . __DIR__ . "<br>";
echo "Корень проекта: " . __DIR__ . "/.." . "<br>";

// Проверяем, существует ли index.php
$indexPath = __DIR__ . '/../index.php';
if (file_exists($indexPath)) {
    echo "✅ Файл index.php НАЙДЕН по пути: " . $indexPath . "<br>";
    echo "Размер файла: " . filesize($indexPath) . " байт<br>";
    
    // Пробуем подключить файл и поймать ошибку
    try {
        require $indexPath;
        echo "<br>✅ index.php успешно загружен!";
    } catch (Throwable $e) {
        echo "<br>❌ ОШИБКА при загрузке index.php: " . $e->getMessage();
        echo "<br>В файле index.php строка " . $e->getLine() . ": " . $e->getFile();
    }
} else {
    echo "❌ Файл index.php НЕ НАЙДЕН по пути: " . $indexPath . "<br>";
    
    // Показываем, какие файлы есть в корне
    $rootFiles = scandir(__DIR__ . '/..');
    echo "<br>Файлы в корне проекта:<br>";
    foreach ($rootFiles as $file) {
        if ($file != '.' && $file != '..') {
            echo "- " . $file . "<br>";
        }
    }
}