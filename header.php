<?php
session_start();

$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if (isset($_GET['logout_admin'])) {
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    
    <!-- SEO метатеги -->
    <meta name="description" content="A.B. Dental Devices - международный производитель дентальных имплантатов. Двухэлементные и одноэлементные имплантаты, абатменты, инструменты. Оставьте заявку на консультацию.">
    <meta name="keywords" content="имплантаты, дентальная имплантация, A.B. Dental, зубные имплантаты, абатменты, I2, I5, I6">
    <meta name="author" content="A.B. Dental Devices">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph для соцсетей -->
    <meta property="og:title" content="A.B. Dental Devices | Современные имплантационные системы">
    <meta property="og:description" content="Международный производитель дентальных имплантатов. Двухэлементные и одноэлементные системы.">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ru_RU">
    
    <title>A.B. Dental Devices | Современные имплантационные системы</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.ico">
    
    <!-- Preconnect для ускорения загрузки -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <nav class="navbar" aria-label="Основная навигация">
                <div class="logo" aria-label="Логотип A.B. Dental Devices">
                    <span class="logo-icon"><i class="fas fa-tooth" aria-hidden="true"></i></span>
                    <div class="logo-text">
                        <span class="logo-title">A.B. Dental</span>
                        <span class="logo-subtitle">Devices</span>
                    </div>
                </div>
                <ul class="nav-menu" role="list">
                    <li role="listitem"><a href="index.php" class="nav-link" aria-label="Перейти на главную">Главная</a></li>
                    <li role="listitem"><a href="index.php#about" class="nav-link" aria-label="Перейти к информации о компании">О компании</a></li>
                    <li role="listitem"><a href="index.php#catalog-section" class="nav-link" aria-label="Перейти к каталогу изделий">Изделия</a></li>
                    <li role="listitem"><a href="index.php#contact" class="nav-link" aria-label="Перейти к контактам и форме заявки">Контакты</a></li>
                    <?php if ($is_admin): ?>
                        <li role="listitem"><a href="admin.php" class="nav-link admin-link" aria-label="Перейти в административную панель"><i class="fas fa-shield-alt" aria-hidden="true"></i> Админ-панель</a></li>
                        <li role="listitem"><a href="?logout_admin=1" class="nav-link logout-link" aria-label="Выйти из аккаунта"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Выход</a></li>
                    <?php endif; ?>
                </ul>
                <button class="nav-toggle" aria-label="Открыть меню" aria-expanded="false">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Модальное окно авторизации -->
    <div id="loginModal" class="modal" role="dialog" aria-label="Вход в админ-панель" aria-modal="true">
        <div class="modal-content">
            <button class="close-modal" aria-label="Закрыть окно входа">&times;</button>
            <h3><i class="fas fa-shield-alt" aria-hidden="true"></i> Вход в админ-панель</h3>
            <form id="adminLoginForm" novalidate>
                <div class="form-group">
                    <label for="login_username">Логин</label>
                    <input type="text" id="login_username" name="username" placeholder="admin" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="login_password">Пароль</label>
                    <input type="password" id="login_password" name="password" placeholder="••••••" required aria-required="true">
                </div>
                <button type="submit" class="btn-submit" aria-label="Войти в административную панель">Войти</button>
                <div id="loginMessage" class="message" role="alert"></div>
            </form>
        </div>
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 24px;
            width: 90%;
            max-width: 400px;
            position: relative;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            background: none;
            border: none;
            padding: 0;
        }
        .close-modal:hover {
            color: #333;
        }
        .modal-content h3 {
            margin-bottom: 20px;
            color: #0a5c8e;
        }
        .modal-content .form-group {
            margin-bottom: 15px;
        }
        .modal-content label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .modal-content input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .modal-content .btn-submit {
            width: 100%;
            margin-top: 10px;
            background: #0a5c8e;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
            display: none;
            font-size: 0.85rem;
        }
        @media (max-width: 480px) {
            .modal-content {
                padding: 20px;
                margin: 20px;
                width: auto;
            }
        }
    </style>

    <script>
        (function() {
            const modalEl = document.getElementById('loginModal');
            const navToggle = document.querySelector('.nav-toggle');
            const navMenu = document.querySelector('.nav-menu');
            
            if (window.location.href.includes('show_login=1') && modalEl) {
                modalEl.style.display = 'flex';
                window.history.replaceState({}, '', window.location.pathname);
            }
            
            document.querySelector('.close-modal')?.addEventListener('click', () => {
                if (modalEl) modalEl.style.display = 'none';
            });
            
            window.addEventListener('click', (e) => {
                if (e.target === modalEl) modalEl.style.display = 'none';
            });
            
            document.getElementById('adminLoginForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const username = document.getElementById('login_username').value;
                const password = document.getElementById('login_password').value;
                const msgDiv = document.getElementById('loginMessage');
                
                const response = await fetch('admin_login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
                });
                const data = await response.json();
                
                if (data.status === 'success') {
                    if (msgDiv) {
                        msgDiv.style.display = 'block';
                        msgDiv.style.backgroundColor = '#d4edda';
                        msgDiv.style.color = '#155724';
                        msgDiv.innerHTML = 'Успешный вход! Перенаправление...';
                    }
                    setTimeout(() => {
                        window.location.href = 'admin.php';
                    }, 1000);
                } else if (msgDiv) {
                    msgDiv.style.display = 'block';
                    msgDiv.style.backgroundColor = '#f8d7da';
                    msgDiv.style.color = '#721c24';
                    msgDiv.innerHTML = data.message;
                }
            });
            
            if (navToggle && navMenu) {
                navToggle.addEventListener('click', () => {
                    const expanded = navToggle.getAttribute('aria-expanded') === 'true' ? false : true;
                    navToggle.setAttribute('aria-expanded', expanded);
                    navMenu.classList.toggle('active');
                });
                
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        navMenu.classList.remove('active');
                        navToggle.setAttribute('aria-expanded', 'false');
                    });
                });
            }
        })();
    </script>