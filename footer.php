    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-tooth"></i>
                    <span>A.B. Dental Devices</span>
                </div>
                <div class="footer-links">
                    <a href="index.php#home">Главная</a>
                    <a href="categories.php">Изделия</a>
                    <a href="index.php#about">О компании</a>
                    <a href="index.php#contact">Контакты</a>
                </div>
                <div class="footer-copy">
                    <p>&copy; <?php echo date('Y'); ?> A.B. Dental Devices. Все права защищены.</p>
                    <?php if (!$is_admin): ?>
                        <p><a href="?show_login=1" style="color: #6c757d; text-decoration: none; font-size: 0.7rem;">🔒 Вход для администратора</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>