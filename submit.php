<?php
header('Content-Type: application/json');

require_once 'config.php';

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$product = trim($_POST['product'] ?? '');

if (empty($name) || empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Заполните имя и телефон']);
    exit;
}

if (strlen($phone) < 10) {
    echo json_encode(['status' => 'error', 'message' => 'Введите корректный номер телефона']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO requests (name, phone, product_name, status) VALUES (?, ?, ?, 'новая')");
$stmt->bind_param("sss", $name, $phone, $product);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Спасибо, ' . $name . '! Ваша заявка принята. Администратор свяжется с вами.'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка при отправке. Попробуйте позже.']);
}

$stmt->close();
$conn->close();
?>