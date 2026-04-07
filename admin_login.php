<?php
session_start();
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$valid_username = 'admin';
$valid_password = 'admin123';

if ($username === $valid_username && $password === $valid_password) {
    $_SESSION['admin_logged_in'] = true;
    echo json_encode(['status' => 'success', 'message' => 'Вход выполнен']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Неверный логин или пароль']);
}
?>