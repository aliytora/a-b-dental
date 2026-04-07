<?php
session_start();

// Обработка выхода
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $auth_user = $_SERVER['PHP_AUTH_USER'] ?? '';
    $auth_pass = $_SERVER['PHP_AUTH_PW'] ?? '';
    
    if ($auth_user == 'admin' && $auth_pass == 'admin123') {
        $_SESSION['admin_logged_in'] = true;
    } else {
        header('WWW-Authenticate: Basic realm="Admin Panel"');
        header('HTTP/1.0 401 Unauthorized');
        echo '<h1>Доступ запрещен</h1>';
        exit;
    }
}

require_once 'config.php';

// ===== ОБРАБОТКА ТОВАРОВ =====

// Добавление товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    $stmt = $conn->prepare("INSERT INTO products (category_id, name, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $category_id, $name, $description);
    $stmt->execute();
    header('Location: admin.php?tab=products');
    exit;
}

// Редактирование товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = (int)$_POST['id'];
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    $stmt = $conn->prepare("UPDATE products SET category_id = ?, name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("issi", $category_id, $name, $description, $id);
    $stmt->execute();
    header('Location: admin.php?tab=products');
    exit;
}

// Удаление товара
if (isset($_GET['delete_product'])) {
    $id = (int)$_GET['delete_product'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: admin.php?tab=products');
    exit;
}

// ===== ОБРАБОТКА ЗАЯВОК =====

// Обновление статуса заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

// Удаление заявки
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM requests WHERE id = $id");
    header('Location: admin.php');
    exit;
}

// ===== ПОЛУЧЕНИЕ ДАННЫХ =====

// Статистика заявок
$total_requests = $conn->query("SELECT COUNT(*) as count FROM requests")->fetch_assoc()['count'];
$new_requests = $conn->query("SELECT COUNT(*) as count FROM requests WHERE status = 'новая'")->fetch_assoc()['count'];
$processed = $conn->query("SELECT COUNT(*) as count FROM requests WHERE status = 'обработана'")->fetch_assoc()['count'];
$refused = $conn->query("SELECT COUNT(*) as count FROM requests WHERE status = 'отказ'")->fetch_assoc()['count'];
$no_answer = $conn->query("SELECT COUNT(*) as count FROM requests WHERE status = 'не дозвонились'")->fetch_assoc()['count'];

// Статистика товаров
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];

// Статистика по дням
$daily_stats = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $count = $conn->query("SELECT COUNT(*) as count FROM requests WHERE DATE(created_at) = '$date'")->fetch_assoc()['count'];
    $daily_stats[] = ['date' => $date, 'count' => $count];
}

// Заявки для таблицы
$requests = $conn->query("SELECT * FROM requests ORDER BY 
    CASE WHEN status = 'новая' THEN 1 ELSE 2 END, 
    created_at DESC");

// Товары для таблицы
$products = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");

// Категории для выпадающего списка
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$active_tab = $_GET['tab'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Admin Panel | A.B. Dental Devices</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f8; color: #1a1a2e; }
        
        /* Sidebar */
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #0a2e4a 0%, #043b5a 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 100;
        }
        .sidebar-header { padding: 30px 25px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-header h2 { font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
        .sidebar-header h2 i { font-size: 1.8rem; }
        .sidebar-header p { font-size: 0.8rem; opacity: 0.7; margin-top: 8px; }
        .nav-item {
            padding: 12px 25px;
            margin: 5px 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.15); color: white; }
        .nav-item i { width: 22px; font-size: 1.1rem; }
        
        /* Main Content */
        .main-content { flex: 1; margin-left: 280px; padding: 25px 35px; transition: all 0.3s; }
        
        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .page-title h1 { font-size: 1.6rem; font-weight: 600; }
        .page-title p { color: #6c757d; font-size: 0.85rem; margin-top: 4px; }
        .admin-info { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .logout-btn {
            background: #fee2e2;
            color: #dc2626;
            padding: 8px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        .logout-btn:hover { background: #dc2626; color: white; }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .stat-icon.blue { background: #e3f2fd; color: #0a5c8e; }
        .stat-icon.green { background: #e8f5e9; color: #2e7d32; }
        .stat-icon.orange { background: #fff3e0; color: #ed6c02; }
        .stat-icon.red { background: #ffebee; color: #d32f2f; }
        .stat-icon.purple { background: #f3e5f5; color: #7b1fa2; }
        .stat-card h3 { font-size: 1.8rem; font-weight: 700; margin-bottom: 5px; }
        .stat-card p { color: #6c757d; font-size: 0.85rem; }
        
        /* Chart */
        .chart-section {
            background: white;
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .chart-section h3 { margin-bottom: 20px; font-size: 1.1rem; }
        .chart-bars {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            gap: 15px;
            height: 250px;
        }
        .chart-bar-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .bar {
            width: 100%;
            background: linear-gradient(180deg, #0a5c8e 0%, #043b5a 100%);
            border-radius: 12px 12px 6px 6px;
            transition: height 0.3s;
            min-height: 4px;
        }
        .bar-value { font-size: 0.8rem; font-weight: 600; color: #0a5c8e; }
        .bar-label { font-size: 0.7rem; color: #6c757d; }
        
        /* Tabs */
        .tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            background: white;
            padding: 10px 20px;
            border-radius: 20px;
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 10px 25px;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .tab-btn.active { background: #0a5c8e; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        /* Tables */
        .table-section {
            background: white;
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f5f7fa;
            padding: 8px 16px;
            border-radius: 40px;
        }
        .search-box i { color: #6c757d; }
        .search-box input {
            border: none;
            background: none;
            outline: none;
            font-size: 0.9rem;
            width: 200px;
        }
        .btn-add {
            background: #2e7d32;
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-add:hover { background: #1b5e20; }
        
        /* Table wrapper for horizontal scroll on mobile */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -5px;
            padding: 0 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eef2f6;
        }
        th {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td { font-size: 0.85rem; }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-new { background: #fff3e0; color: #ed6c02; }
        .status-done { background: #e8f5e9; color: #2e7d32; }
        .status-refused { background: #ffebee; color: #d32f2f; }
        .status-waiting { background: #e3f2fd; color: #0a5c8e; }
        
        select.status-select {
            padding: 6px 10px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: white;
            font-size: 0.75rem;
        }
        .btn-edit, .btn-delete {
            padding: 6px 12px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-block;
            text-decoration: none;
        }
        .btn-edit { background: #0a5c8e; color: white; }
        .btn-edit:hover { background: #043b5a; }
        .btn-delete { background: #fee2e2; color: #dc2626; }
        .btn-delete:hover { background: #dc2626; color: white; }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 24px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-content input, .modal-content select, .modal-content textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 0.9rem;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        /* ===== АДАПТИВ ===== */
        
        /* Планшеты и маленькие ноутбуки */
        @media (max-width: 992px) {
            .sidebar { width: 80px; }
            .sidebar-header h2 span, .sidebar-header p, .nav-item span { display: none; }
            .nav-item { justify-content: center; padding: 12px; margin: 5px 8px; }
            .main-content { margin-left: 80px; padding: 20px; }
        }
        
        /* Телефоны */
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar-header { padding: 15px 10px; }
            .sidebar-header h2 i { font-size: 1.5rem; }
            .nav-item { margin: 5px 5px; padding: 10px; }
            .main-content { margin-left: 70px; padding: 15px; }
            
            .top-bar {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }
            .page-title h1 { font-size: 1.3rem; }
            .admin-info { justify-content: center; }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .stat-card { padding: 15px; text-align: center; }
            .stat-card h3 { font-size: 1.3rem; }
            .stat-icon { width: 40px; height: 40px; margin: 0 auto 12px; }
            
            .chart-bars { gap: 8px; height: 160px; }
            .bar-value { font-size: 0.7rem; }
            .bar-label { font-size: 0.6rem; }
            
            .tabs { justify-content: center; }
            .tab-btn { padding: 8px 16px; font-size: 0.8rem; }
            
            .table-header { flex-direction: column; align-items: stretch; }
            .search-box { justify-content: center; }
            .search-box input { width: 100%; }
            
            th, td { padding: 8px; font-size: 0.75rem; }
            .btn-edit, .btn-delete { padding: 4px 8px; font-size: 0.65rem; }
            select.status-select { padding: 4px 8px; font-size: 0.7rem; }
        }
        
        /* Маленькие телефоны */
        @media (max-width: 480px) {
            .sidebar { width: 60px; }
            .main-content { margin-left: 60px; padding: 10px; }
            
            .stats-grid { grid-template-columns: 1fr; }
            
            .table-section { padding: 12px; }
            .modal-content { padding: 20px; margin: 20px; }
            
            .btn-add { padding: 8px 16px; font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-tooth"></i> <span>A.B. Dental</span></h2>
                <p><span>Админ панель</span></p>
            </div>
            <nav>
                <a href="?tab=dashboard" class="nav-item <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> <span>Дашборд</span>
                </a>
                <a href="?tab=requests" class="nav-item <?php echo $active_tab == 'requests' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> <span>Заявки</span>
                </a>
                <a href="?tab=products" class="nav-item <?php echo $active_tab == 'products' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> <span>Товары</span>
                </a>
                <a href="index.php" target="_blank" class="nav-item">
                    <i class="fas fa-globe"></i> <span>На сайт</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1>Админ-панель</h1>
                    <p>Управление заявками и каталогом</p>
                </div>
                <div class="admin-info">
                    <i class="fas fa-user-circle" style="font-size: 2rem; color: #0a5c8e;"></i>
                    <span>Admin</span>
                    <a href="?logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Выход</a>
                </div>
            </div>

            <div class="tabs">
                <button class="tab-btn <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>" onclick="switchTab('dashboard')"> Дашборд</button>
                <button class="tab-btn <?php echo $active_tab == 'requests' ? 'active' : ''; ?>" onclick="switchTab('requests')"> Заявки</button>
                <button class="tab-btn <?php echo $active_tab == 'products' ? 'active' : ''; ?>" onclick="switchTab('products')"> Товары</button>
            </div>

            <!-- DASHBOARD -->
            <div id="dashboard" class="tab-content <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-inbox"></i></div><h3><?php echo $total_requests; ?></h3><p>Всего заявок</p></div>
                    <div class="stat-card"><div class="stat-icon orange"><i class="fas fa-clock"></i></div><h3><?php echo $new_requests; ?></h3><p>Новых заявок</p></div>
                    <div class="stat-card"><div class="stat-icon green"><i class="fas fa-check-circle"></i></div><h3><?php echo $processed; ?></h3><p>Обработано</p></div>
                    <div class="stat-card"><div class="stat-icon red"><i class="fas fa-times-circle"></i></div><h3><?php echo $refused; ?></h3><p>Отказов</p></div>
                    <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-box"></i></div><h3><?php echo $total_products; ?></h3><p>Товаров</p></div>
                </div>

                <div class="chart-section">
                    <h3><i class="fas fa-calendar-alt"></i> Заявки за 7 дней</h3>
                    <div class="chart-bars">
                        <?php 
                        $max_count = max(array_column($daily_stats, 'count')) ?: 1;
                        foreach ($daily_stats as $day): 
                            $height = ($day['count'] / $max_count) * 200;
                            $short_date = date('d/m', strtotime($day['date']));
                        ?>
                            <div class="chart-bar-item">
                                <div class="bar" style="height: <?php echo max($height, 4); ?>px;"></div>
                                <span class="bar-value"><?php echo $day['count']; ?></span>
                                <span class="bar-label"><?php echo $short_date; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            
            <div id="requests" class="tab-content <?php echo $active_tab == 'requests' ? 'active' : ''; ?>">
                <div class="table-section">
                    <div class="table-header">
                        <h3><i class="fas fa-clipboard-list"></i> Заявки</h3>
                        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="searchInput" placeholder="Поиск..."></div>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th><th>Имя</th><th>Телефон</th><th>Продукт</th><th>Дата</th><th>Статус</th><th>Действия</th>
                                </tr>
                            </thead>
                            <tbody id="requestsTable">
                                <?php while ($row = $requests->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($row['product_name'] ?: '—'); ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td><span class="status-badge status-<?php echo $row['status'] == 'новая' ? 'new' : ($row['status'] == 'отказ' ? 'refused' : 'done'); ?>"><?php echo $row['status']; ?></span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <select name="status" class="status-select">
                                                        <option value="новая" <?php echo $row['status'] == 'новая' ? 'selected' : ''; ?>>Новая</option>
                                                        <option value="обработана" <?php echo $row['status'] == 'обработана' ? 'selected' : ''; ?>>Обработана</option>
                                                        <option value="отказ" <?php echo $row['status'] == 'отказ' ? 'selected' : ''; ?>>Отказ</option>
                                                        <option value="не дозвонились" <?php echo $row['status'] == 'не дозвонились' ? 'selected' : ''; ?>>Не дозвонились</option>
                                                    </select>
                                                    <button type="submit" name="update_status" class="btn-edit">Обн.</button>
                                                </form>
                                                <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Удалить заявку?')">Удал.</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PRODUCTS -->
            <div id="products" class="tab-content <?php echo $active_tab == 'products' ? 'active' : ''; ?>">
                <div class="table-section">
                    <div class="table-header">
                        <h3><i class="fas fa-box"></i> Товары</h3>
                        <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Добавить товар</button>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th><th>Название</th><th>Категория</th><th>Описание</th><th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $products->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category_name'] ?: 'Без категории'); ?></td>
                                        <td><?php echo htmlspecialchars(mb_substr($row['description'], 0, 50)) . '...'; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-edit" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', '<?php echo addslashes($row['description']); ?>', <?php echo $row['category_id'] ?? 0; ?>)">Ред.</button>
                                                <a href="?delete_product=<?php echo $row['id']; ?>&tab=products" class="btn-delete" onclick="return confirm('Удалить товар?')">Удал.</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>


    <div id="productModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Добавить товар</h3>
            <form id="productForm" method="POST">
                <input type="hidden" name="id" id="productId">
                <select name="category_id" id="categoryId" required>
                    <option value="">Выберите категорию</option>
                    <?php 
                    $cats = $conn->query("SELECT * FROM categories ORDER BY name");
                    while ($cat = $cats->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="name" id="productName" placeholder="Название товара" required>
                <textarea name="description" id="productDesc" placeholder="Описание" rows="4"></textarea>
                <div class="modal-buttons">
                    <button type="button" class="btn-delete" onclick="closeModal()">Отмена</button>
                    <button type="submit" class="btn-add" name="add_product" id="submitBtn">Добавить</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            window.location.href = '?tab=' + tab;
        }

        const modal = document.getElementById('productModal');
        const form = document.getElementById('productForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');

        function openAddModal() {
            modalTitle.innerText = 'Добавить товар';
            submitBtn.name = 'add_product';
            document.getElementById('productId').value = '';
            document.getElementById('categoryId').value = '';
            document.getElementById('productName').value = '';
            document.getElementById('productDesc').value = '';
            modal.style.display = 'flex';
        }

        function openEditModal(id, name, desc, catId) {
            modalTitle.innerText = 'Редактировать товар';
            submitBtn.name = 'edit_product';
            document.getElementById('productId').value = id;
            document.getElementById('categoryId').value = catId;
            document.getElementById('productName').value = name;
            document.getElementById('productDesc').value = desc;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(e) {
            if (e.target === modal) closeModal();
        }

        // Поиск по заявкам
        document.getElementById('searchInput')?.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#requestsTable tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    </script>
</body>
</html>