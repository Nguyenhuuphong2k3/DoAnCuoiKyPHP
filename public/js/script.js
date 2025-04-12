document.addEventListener('DOMContentLoaded', () => {
    // Hiệu ứng cuộn mượt
    document.querySelectorAll('a[href^="?"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            window.location.href = url;
        });
    });

    // Hiệu ứng hover cho sản phẩm
    const productItems = document.querySelectorAll('.product-item');
    productItems.forEach(item => {
        item.addEventListener('mouseover', () => {
            item.style.transform = 'translateY(-5px)';
        });
        item.addEventListener('mouseout', () => {
            item.style.transform = 'translateY(0)';
        });
    });

    // Hiệu ứng fade-in cho các phần tử trong main
    const fadeInElements = document.querySelectorAll('main > *');
    fadeInElements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 200);
    });

    // Hiệu ứng cho nút thêm vào giỏ hàng
    const addToCartButtons = document.querySelectorAll('form[action*="cart&action=add"] button');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.textContent = 'Đã thêm!';
            button.style.backgroundColor = '#28a745';
            setTimeout(() => {
                button.textContent = 'Thêm vào giỏ hàng';
                button.style.backgroundColor = '#333';
            }, 1000);
        });
    });

    // Hiệu ứng cho nút thanh toán
    const checkoutButton = document.querySelector('a[href*="order&action=checkout"]');
    if (checkoutButton) {
        checkoutButton.addEventListener('click', (e) => {
            e.preventDefault();
            checkoutButton.textContent = 'Đang xử lý...';
            setTimeout(() => {
                window.location.href = checkoutButton.getAttribute('href');
            }, 1000);
        });
    }
});