<?php
// require_once 'config.php';   // ВРЕМЕННО ОТКЛЮЧЕНО
require_once 'header.php';

// Сохранение статистики (ВРЕМЕННО ОТКЛЮЧЕНО)
// $ip = $_SERVER['REMOTE_ADDR'];
// $page = $_SERVER['REQUEST_URI'];
// $stmt = $conn->prepare("INSERT INTO visits (ip_address, page) VALUES (?, ?)");
// $stmt->bind_param("ss", $ip, $page);
// $stmt->execute();

// Получаем все категории для аккордеона (ВРЕМЕННО ОТКЛЮЧЕНО)
// $categories = $conn->query("SELECT * FROM categories ORDER BY parent_id, sort_order");
// $tree = [];
// while ($cat = $categories->fetch_assoc()) {
//     if ($cat['parent_id'] == 0) {
//         $tree[$cat['id']] = $cat;
//         $tree[$cat['id']]['children'] = [];
//     } else {
//         $tree[$cat['parent_id']]['children'][] = $cat;
//     }
// }

// Временно добавим проверку, что PHP работает
echo "<!-- Сайт a b dental работает -->";
?> 

<section class="hero" id="home">
    <div class="container hero-content">
        <div class="hero-text">
            <span class="hero-badge">С 1990-х годов</span>
            <h1>Имплантационные системы<br><span class="highlight">мирового уровня</span></h1>
            <p>Двухэлементные имплантаты с внутренним шестигранником и инновационные одноэлементные решения для стоматологии будущего.</p>
            <div class="hero-buttons">
                <a href="#catalog" class="btn btn-primary" aria-label="Перейти к каталогу продуктов"><i class="fas fa-microscope" aria-hidden="true"></i> Наши продукты</a>
                <a href="#contact" class="btn btn-outline" aria-label="Перейти к форме связи"><i class="fas fa-headset" aria-hidden="true"></i> Связаться</a>
            </div>
        </div>
        <div class="hero-stats">
            <div class="stat-item"><span class="stat-number">25+</span><span class="stat-label">лет на рынке</span></div>
            <div class="stat-item"><span class="stat-number">50+</span><span class="stat-label">стран мира</span></div>
            <div class="stat-item"><span class="stat-number">1000+</span><span class="stat-label">клиник доверяют</span></div>
        </div>
    </div>
</section>

<section class="about-section" id="about">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">О компании</span>
            <h2>Признание пришло к нам <span class="highlight">не случайно</span></h2>
            <div class="section-divider"></div>
        </div>
        <div class="about-grid">
            <div class="about-text">
                <h3>Профиль компании</h3>
                <p><strong>A.B. Dental Devices</strong> является единственной в своем роде международной компанией по имплантации зубов. С 1990-х годов мы сосредоточились на <strong>двухэлементных имплантатах с внутренним шестигранником</strong> как для одноэтапной, так и для двухэтапной технологии.</p>
                <h3>Передовые технологии</h3>
                <p>Все многообразие изделий производится с применением передовых методов работы, с использованием самых совершенных технологий.</p>
                <div class="features-list">
                    <div class="feature-item"><i class="fas fa-check-circle" aria-hidden="true"></i> <span>ISO 13485 сертификация</span></div>
                    <div class="feature-item"><i class="fas fa-check-circle" aria-hidden="true"></i> <span>Клинические исследования</span></div>
                    <div class="feature-item"><i class="fas fa-check-circle" aria-hidden="true"></i> <span>Глобальная доставка</span></div>
                </div>
            </div>
            <div class="about-image">
                <div class="image-placeholder" aria-label="Логотип A.B. Dental Devices"><i class="fas fa-tooth" aria-hidden="true"></i></div>
            </div>
        </div>
    </div>
</section>

<section class="catalog-main-section" id="catalog">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Каталог</span>
            <h2>Наши <span class="highlight">изделия</span></h2>
            <div class="section-divider"></div>
        </div>
        
      <?php if (!empty($tree) && is_array($tree)): ?>
    <div class="catalog-flex">
        <div class="catalog-sidebar">
            <h3><i class="fas fa-bars" aria-hidden="true"></i> Категории</h3>
            <ul class="accordion-list">
                <?php foreach ($tree as $cat): ?>
                    <li class="accordion-item">
                        <div class="accordion-header" data-cat-id="<?php echo $cat['id']; ?>" data-cat-name="<?php echo htmlspecialchars($cat['name']); ?>" role="button" tabindex="0" aria-label="Открыть категорию <?php echo htmlspecialchars($cat['name']); ?>">
                            <span><i class="fas fa-folder" aria-hidden="true"></i> <?php echo htmlspecialchars($cat['name']); ?></span>
                            <?php if (count($cat['children']) > 0): ?>
                                <i class="fas fa-chevron-down accordion-icon" aria-hidden="true"></i>
                            <?php endif; ?>
                        </div>
                        <?php if (count($cat['children']) > 0): ?>
                            <ul class="accordion-submenu">
                                <?php foreach ($cat['children'] as $sub): ?>
                                    <li class="submenu-item" data-cat-id="<?php echo $sub['id']; ?>" data-cat-name="<?php echo htmlspecialchars($sub['name']); ?>" role="button" tabindex="0" aria-label="Выбрать подкатегорию <?php echo htmlspecialchars($sub['name']); ?>">
                                        <i class="fas fa-file" aria-hidden="true"></i> <?php echo htmlspecialchars($sub['name']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
            
            <div class="catalog-products">
                <div class="catalog-header-info">
                    <div class="current-category">
                        <i class="fas fa-tags" aria-hidden="true"></i> <span id="currentCategoryName">Не выбрана</span>
                    </div>
                    <div class="products-count" id="productsCount">0 товаров</div>
                </div>
                <div class="products-grid-ajax" id="productsGrid">
                    <div class="empty-products">
                        <i class="fas fa-folder-open" aria-hidden="true" style="font-size: 3rem; color: #ccc;"></i>
                        <p>Выберите категорию слева, чтобы увидеть товары</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact-section" id="contact">
    <div class="container">
        <div class="contact-wrapper">
            <div class="contact-info">
                <span class="section-tag">Свяжитесь с нами</span>
                <h2>Оставьте <span class="highlight">заявку</span></h2>
                <p>Заполните форму — и наш специалист свяжется с вами.</p>
                <div class="info-block">
                    <div class="info-item"><i class="fas fa-phone-alt" aria-hidden="true"></i><div><strong>Телефон</strong><span>+7 (495) 123-45-67</span></div></div>
                    <div class="info-item"><i class="fas fa-envelope" aria-hidden="true"></i><div><strong>Email</strong><span>info@ab-dental.ru</span></div></div>
                    <div class="info-item"><i class="fas fa-map-marker-alt" aria-hidden="true"></i><div><strong>Адрес</strong><span>Москва, ул. Стоматологическая, 15</span></div></div>
                </div>
            </div>
            <div class="contact-form">
                <form id="orderForm" novalidate>
                    <div class="form-group">
                        <label for="name" class="sr-only">Ваше имя</label>
                        <input type="text" id="name" name="name" placeholder="Ваше имя *" required aria-required="true" aria-label="Ваше имя">
                    </div>
                    <div class="form-group">
                        <label for="phone" class="sr-only">Телефон</label>
                        <input type="tel" id="phone" name="phone" placeholder="Телефон *" required aria-required="true" aria-label="Номер телефона">
                    </div>
                    <div class="form-group">
                        <label for="product" class="sr-only">Интересующий продукт</label>
                        <input type="text" id="product" name="product" placeholder="Интересующий продукт" aria-label="Интересующий продукт">
                    </div>
                    <button type="submit" class="btn-submit" aria-label="Отправить заявку"><i class="fas fa-paper-plane" aria-hidden="true"></i> Отправить заявку</button>
                    <div id="formMessage" class="message" role="alert"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== КАТАЛОГ С АККОРДЕОНОМ ===== */
.catalog-main-section {
    padding: 60px 0;
    background: #f8f9fa;
}

.catalog-flex {
    display: flex;
    gap: 30px;
    margin-top: 30px;
}

.catalog-sidebar {
    width: 300px;
    flex-shrink: 0;
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.catalog-sidebar h3 {
    color: #043b5a;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #0a5c8e;
    font-size: 1.2rem;
}

.accordion-list {
    list-style: none;
}

.accordion-item {
    margin-bottom: 8px;
}

.accordion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
}

.accordion-header:hover {
    background: #e3f2fd;
    color: #0a5c8e;
}

.accordion-header.active {
    background: #0a5c8e;
    color: white;
}

.accordion-icon {
    transition: transform 0.3s;
}

.accordion-icon.rotated {
    transform: rotate(180deg);
}

.accordion-submenu {
    list-style: none;
    padding-left: 20px;
    margin-top: 5px;
    margin-bottom: 5px;
    display: none;
}

.accordion-submenu.show {
    display: block;
}

.submenu-item {
    padding: 8px 15px;
    margin: 4px 0;
    border-radius: 8px;
    cursor: pointer;
    color: #555;
    transition: all 0.3s;
}

.submenu-item:hover {
    background: #e9ecef;
    color: #0a5c8e;
}

.submenu-item.active {
    background: #0a5c8e;
    color: white;
}

.catalog-products {
    flex: 1;
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.catalog-header-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.current-category {
    font-size: 1.1rem;
    font-weight: 600;
    color: #043b5a;
}

.current-category i {
    color: #0a5c8e;
    margin-right: 8px;
}

.products-count {
    color: #6c757d;
    font-size: 0.9rem;
}

.products-grid-ajax {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.loading-spinner {
    text-align: center;
    padding: 50px;
    color: #0a5c8e;
}

.loading-spinner i {
    font-size: 2rem;
    margin-bottom: 10px;
}

.product-card-ajax {
    background: #f8f9fa;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s;
    border: 1px solid #eee;
}

.product-card-ajax:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.product-card-ajax .product-icon {
    font-size: 2rem;
    color: #0a5c8e;
    margin-bottom: 12px;
}

.product-card-ajax h4 {
    color: #043b5a;
    margin-bottom: 10px;
    font-size: 1rem;
}

.product-card-ajax p {
    color: #4a5568;
    font-size: 0.8rem;
    margin-bottom: 15px;
    line-height: 1.4;
}

.btn-order-ajax {
    background: #0a5c8e;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.3s;
}

.btn-order-ajax:hover {
    background: #043b5a;
}

.empty-products {
    text-align: center;
    padding: 50px;
    color: #6c757d;
}

.empty-products i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* Скрытый класс для скринридеров */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

/* Модальное окно для сертификатов */
.certificate-modal {
    display: none;
    position: fixed;
    z-index: 3000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    align-items: center;
    justify-content: center;
}

.certificate-modal-content {
    position: relative;
    background: white;
    padding: 20px;
    border-radius: 24px;
    max-width: 90%;
    max-height: 90%;
    text-align: center;
    animation: fadeInScale 0.3s ease;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.certificate-modal-content img {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
    border-radius: 12px;
}

.certificate-modal-content h3 {
    margin-top: 15px;
    color: #043b5a;
    font-size: 1.2rem;
}

.certificate-modal-close {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 32px;
    cursor: pointer;
    color: #999;
    transition: color 0.3s;
}

.certificate-modal-close:hover {
    color: #dc2626;
}

/* Адаптив */
@media (max-width: 768px) {
    .catalog-flex {
        flex-direction: column;
    }
    .catalog-sidebar {
        width: 100%;
        position: static;
    }
    .products-grid-ajax {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Загрузка товаров ТОЛЬКО при выборе категории
async function loadProducts(categoryId, categoryName) {
    const url = categoryId ? `get_products.php?category_id=${categoryId}` : null;
    
    if (!url) {
        document.getElementById('productsGrid').innerHTML = `
            <div class="empty-products">
                <i class="fas fa-folder-open" style="font-size: 3rem; color: #ccc;"></i>
                <p>Выберите категорию слева, чтобы увидеть товары</p>
            </div>
        `;
        document.getElementById('currentCategoryName').innerText = 'Не выбрана';
        document.getElementById('productsCount').innerText = '0 товаров';
        return;
    }
    
    document.getElementById('productsGrid').innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-pulse"></i><p>Загрузка...</p></div>';
    
    try {
        const response = await fetch(url);
        const products = await response.json();
        const grid = document.getElementById('productsGrid');
        const countSpan = document.getElementById('productsCount');
        const categorySpan = document.getElementById('currentCategoryName');
        
        categorySpan.textContent = categoryName || 'Все товары';
        countSpan.textContent = products.length + ' товаров';
        
        if (products.length === 0) {
            grid.innerHTML = '<div class="empty-products"><i class="fas fa-box-open"></i><p>Нет товаров в этой категории</p></div>';
            return;
        }
        
        grid.innerHTML = products.map(p => {
            const isCertificate = (p.category_id == 8) || p.name.includes('Сертификат') || p.name.includes('Регистрационное');
            const buttonIcon = isCertificate ? 'fa-eye' : 'fa-paper-plane';
            const buttonText = isCertificate ? 'Посмотреть' : 'Заказать';
            const onclickAction = isCertificate 
                ? `openCertificateByProductName('${escapeHtml(p.name)}')` 
                : `orderProduct('${escapeHtml(p.name)}')`;
            
            return `
                <div class="product-card-ajax">
                    <div class="product-icon"><i class="fas fa-microchip" aria-hidden="true"></i></div>
                    <h4>${escapeHtml(p.name)}</h4>
                    <p>${escapeHtml(p.description || 'Описание отсутствует')}</p>
                    <button class="btn-order-ajax" onclick="${onclickAction}" aria-label="${buttonText} ${escapeHtml(p.name)}">
                        <i class="fas ${buttonIcon}" aria-hidden="true"></i> ${buttonText}
                    </button>
                </div>
            `;
        }).join('');
    } catch (error) {
        document.getElementById('productsGrid').innerHTML = '<div class="empty-products"><i class="fas fa-exclamation-triangle"></i><p>Ошибка загрузки</p></div>';
    }
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
    document.getElementById('product').value = productName;
    document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
}

function openCertificateByProductName(productName) {
    let imageSrc = '';
    if (productName.includes('Сертификат соответствия')) {
        imageSrc = 'images/certificate.jpg';
    } else if (productName.includes('Регистрационное удостоверение')) {
        imageSrc = 'images/registration.jpg';
    } else {
        imageSrc = 'images/certificate.jpg';
    }
    openCertificateModal(imageSrc, productName);
}

function openCertificateModal(imageSrc, title) {
    let modal = document.getElementById('certificateModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'certificateModal';
        modal.className = 'certificate-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-label', 'Просмотр сертификата');
        modal.innerHTML = `
            <div class="certificate-modal-content">
                <span class="certificate-modal-close" aria-label="Закрыть">&times;</span>
                <img id="certificateModalImg" src="" alt="${title}" loading="lazy">
                <h3 id="certificateModalTitle"></h3>
            </div>
        `;
        document.body.appendChild(modal);
        
        modal.querySelector('.certificate-modal-close').addEventListener('click', () => {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
    }
    
    document.getElementById('certificateModalImg').src = imageSrc;
    document.getElementById('certificateModalTitle').textContent = title;
    modal.style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', (e) => {
            e.stopPropagation();
            const submenu = header.parentElement.querySelector('.accordion-submenu');
            if (submenu) {
                submenu.classList.toggle('show');
                const icon = header.querySelector('.accordion-icon');
                if (icon) icon.classList.toggle('rotated');
            }
            const catId = header.dataset.catId;
            const catName = header.dataset.catName;
            loadProducts(catId, catName);
            
            document.querySelectorAll('.accordion-header').forEach(h => h.classList.remove('active'));
            document.querySelectorAll('.submenu-item').forEach(s => s.classList.remove('active'));
            header.classList.add('active');
        });
        
        // Добавляем поддержку клавиатуры
        header.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                header.click();
            }
        });
    });
    
    document.querySelectorAll('.submenu-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            const catId = item.dataset.catId;
            const catName = item.dataset.catName;
            loadProducts(catId, catName);
            
            document.querySelectorAll('.accordion-header').forEach(h => h.classList.remove('active'));
            document.querySelectorAll('.submenu-item').forEach(s => s.classList.remove('active'));
            item.classList.add('active');
        });
        
        item.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                item.click();
            }
        });
    });
});

document.querySelector('.nav-toggle')?.addEventListener('click', () => {
    document.querySelector('.nav-menu')?.classList.toggle('active');
});

window.addEventListener('scroll', () => {
    let current = '';
    const sections = document.querySelectorAll('section[id]');
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        if (scrollY >= sectionTop) current = section.getAttribute('id');
    });
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) link.classList.add('active');
    });
});
</script>

<?php require_once 'footer.php'; ?>