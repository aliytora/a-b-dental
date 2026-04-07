document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const product = document.getElementById('product').value.trim();
    const messageDiv = document.getElementById('formMessage');
    const submitBtn = document.querySelector('.btn-submit');
    
    if (name === '') {
        showMessage('Пожалуйста, введите ваше имя', 'error');
        return;
    }
    
    if (phone === '') {
        showMessage('Пожалуйста, введите номер телефона', 'error');
        return;
    }
    
    if (phone.length < 10) {
        showMessage('Введите корректный номер телефона (не менее 10 цифр)', 'error');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Отправка...';
    
    fetch('submit.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `name=${encodeURIComponent(name)}&phone=${encodeURIComponent(phone)}&product=${encodeURIComponent(product)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage(data.message, 'success');
            document.getElementById('name').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('product').value = '';
        } else {
            showMessage(data.message, 'error');
        }
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Отправить заявку';
    })
    .catch(error => {
        showMessage('Ошибка сети. Попробуйте позже.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Отправить заявку';
    });
});

function showMessage(text, type) {
    const messageDiv = document.getElementById('formMessage');
    messageDiv.style.display = 'block';
    messageDiv.textContent = text;
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#d4edda';
        messageDiv.style.color = '#155724';
        messageDiv.style.border = '1px solid #c3e6cb';
    } else {
        messageDiv.style.backgroundColor = '#f8d7da';
        messageDiv.style.color = '#721c24';
        messageDiv.style.border = '1px solid #f5c6cb';
    }
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

function selectProduct(productName) {
    document.getElementById('product').value = productName;
    document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
}