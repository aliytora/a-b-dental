<?php
require_once 'config.php';
header('Content-Type: application/json');

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($category_id > 0) {
    // Проверяем, есть ли у категории подкатегории
    $check = $conn->query("SELECT COUNT(*) as cnt FROM categories WHERE parent_id = $category_id");
    $has_children = $check->fetch_assoc()['cnt'] > 0;
    
    if ($has_children) {
        // Если есть подкатегории - показываем товары из всех подкатегорий
        $stmt = $conn->prepare("
            SELECT p.* FROM products p 
            WHERE p.category_id IN (SELECT id FROM categories WHERE parent_id = ? OR id = ?)
        ");
        $stmt->bind_param("ii", $category_id, $category_id);
    } else {
        // Если нет подкатегорий - показываем товары только этой категории
        $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM products LIMIT 30");
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>