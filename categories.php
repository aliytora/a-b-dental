<?php
require_once 'config.php';

// Получаем все категории
$categories = $conn->query("SELECT * FROM categories ORDER BY parent_id, sort_order");

// Строим дерево категорий
$tree = [];
while ($cat = $categories->fetch_assoc()) {
    if ($cat['parent_id'] == 0) {
        $tree[$cat['id']] = $cat;
        $tree[$cat['id']]['children'] = [];
    } else {
        $tree[$cat['parent_id']]['children'][] = $cat;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог продукции | A.B. Dental Devices</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .catalog-page {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .catalog-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .catalog-header h1 {
            font-size: 2.5rem;
            color: #043b5a;
            margin-bottom: 10px;
        }
        
        .catalog-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .catalog-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
        }
        
        /* Sidebar меню */
        .catalog-sidebar {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            position: sticky;
            top: 100px;
            height: fit-content;
        }
        
        .catalog-sidebar h3 {
            color: #043b5a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0a5c8e;
        }
        
        .category-list {
            list-style: none;
        }
        
        .category-item {
            margin-bottom: 8px;
        }
        
        .category-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 12px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .category-link:hover {
            background: #e3f2fd;
            color: #0a5c8e;
        }
        
        .category-link.active {
            background: #0a5c8e;
            color: white;
        }
        
        .category-link .count {
            background: rgba(0,0,0,0.1);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        
        .subcategory-list {
            list-style: none;
            padding-left: 20px;
            margin-top: 5px;
            display: none;
        }
        
        .subcategory-list.show {
            display: block;
        }
        
        .subcategory-link {
            display: block;
            padding: 8px 15px;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .subcategory-link:hover {
            background: #e9ecef;
            color: #0a5c8e;
        }
        
        /* Контент каталога */
        .catalog-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .products-grid-catalog {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .product-item {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .product-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .product-icon {
            font-size: 2.5rem;
            color: #0a5c8e;
            margin-bottom: 15px;
        }
        
        .product-item h4 {
            color: #043b5a;
            margin-bottom: 10px;
        }
        
        .product-item p {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        
        .btn-order-small {
            background: #0a5c8e;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        
        .btn-order-small:hover {
            background: #043b5a;
        }
        
        .breadcrumb {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .breadcrumb a {
            color: #0a5c8e;
            text-decoration: none;
        }
        
        .breadcrumb span {
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .catalog-grid {
                grid-template-columns: 1fr;
            }
            .catalog-sidebar {
                position: static;
            }
        }
        /* Адаптив для страницы категорий */
@media (max-width: 768px) {
    .catalog-grid {
        grid-template-columns: 1fr;
    }
    
    .catalog-sidebar {
        position: static;
        margin-bottom: 20px;
    }
    
    .products-grid-catalog {
        grid-template-columns: 1fr;
    }
    
    .catalog-header h1 {
        font-size: 1.8rem;
    }
}
@media (max-width: 768px) {
    .products-grid-catalog {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 15px;
        padding: 10px 5px 20px 5px;
        margin: 0 -15px;
        padding-left: 15px;
        padding-right: 15px;
        -webkit-overflow-scrolling: touch;
    }
    
    .product-item {
        flex: 0 0 85%;
        scroll-snap-align: start;
    }
}
    </style>
</head>
<body>
    <?php require_once 'header.php'; ?>
    
    <div class="catalog-page">
        <div class="container">
            <div class="catalog-header">
                <h1>Каталог продукции</h1>
                <p>Все изделия A.B. Dental Devices для имплантологии</p>
            </div>
            
            <div class="catalog-grid">
                <aside class="catalog-sidebar">
                    <h3><i class="fas fa-bars"></i> Категории</h3>
                    <ul class="category-list">
                        <?php foreach ($tree as $cat): ?>
                            <li class="category-item">
                                <div class="category-link" data-cat-id="<?php echo $cat['id']; ?>">
                                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                    <?php if (count($cat['children']) > 0): ?>
                                        <i class="fas fa-chevron-down"></i>
                                    <?php endif; ?>
                                </div>
                                <?php if (count($cat['children']) > 0): ?>
                                    <ul class="subcategory-list" data-parent="<?php echo $cat['id']; ?>">
                                        <?php foreach ($cat['children'] as $sub): ?>
                                            <li>
                                                <a href="#" class="subcategory-link" data-cat-id="<?php echo $sub['id']; ?>">
                                                    <?php echo htmlspecialchars($sub['name']); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>
                <div class="catalog-content">
                    <div class="breadcrumb">
                        <a href="#">Главная</a> / 
                        <a href="#">Изделия</a> / 
                        <span id="current-category">Все категории</span>
                    </div>
                    
                    <div id="productsContainer">
                        <div class="products-grid-catalog" id="productsGrid">
                            <!-- Товары подгрузятся через JS -->
                            <div style="text-align: center; padding: 50px;">
                                <i class="fas fa-spinner fa-pulse" style="font-size: 2rem; color: #0a5c8e;"></i>
                                <p>Загрузка...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once 'footer.php'; ?>
    
    <script>
        // Загрузка продуктов по категории
        async function loadProducts(categoryId, categoryName) {
            document.getElementById('current-category').innerText = categoryName;
            
            const url = categoryId ? `get_products.php?category_id=${categoryId}` : 'get_products.php';
            const response = await fetch(url);
            const products = await response.json();
            
            const grid = document.getElementById('productsGrid');
            
            if (products.length === 0) {
                grid.innerHTML = '<div style="text-align: center; padding: 50px;"><i class="fas fa-box-open" style="font-size: 3rem; color: #ccc;"></i><p>Нет товаров в этой категории</p></div>';
                return;
            }
            
            grid.innerHTML = products.map(p => `
                <div class="product-item">
                    <div class="product-icon"><i class="fas fa-microchip"></i></div>
                    <h4>${escapeHtml(p.name)}</h4>
                    <p>${escapeHtml(p.description || 'Описание отсутствует')}</p>
                    <button class="btn-order-small" onclick="orderProduct('${escapeHtml(p.name)}')">
                        <i class="fas fa-shopping-cart"></i> Заказать
                    </button>
                </div>
            `).join('');
        }
        
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        function orderProduct(productName) {
            // Сохраняем в localStorage и перенаправляем на главную с формой
            localStorage.setItem('selected_product', productName);
            window.location.href = 'index.php#contact';
        }
        
        // Обработчики кликов
        document.querySelectorAll('.category-link').forEach(link => {
            link.addEventListener('click', () => {
                const catId = link.dataset.catId;
                const catName = link.querySelector('span:first-child').innerText;
                loadProducts(catId, catName);
                
                // Подсветка активной категории
                document.querySelectorAll('.category-link').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            });
        });
        
        document.querySelectorAll('.subcategory-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const catId = link.dataset.catId;
                const catName = link.innerText;
                loadProducts(catId, catName);
                
                document.querySelectorAll('.category-link').forEach(l => l.classList.remove('active'));
            });
        });
        
        // Загрузка всех товаров при старте
        loadProducts(null, 'Все категории');
    </script>
</body>
</html>