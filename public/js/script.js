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

    // JavaScript cho Slider
    const slider = document.querySelector('.slider');
    if (slider) {
        let slideIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        const totalSlides = slides.length;

        // Hàm hiển thị slide
        function showSlide(index) {
            if (index >= totalSlides) slideIndex = 0;
            else if (index < 0) slideIndex = totalSlides - 1;
            else slideIndex = index;

            // Xóa class active khỏi tất cả các slide và dot
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Thêm class active cho slide và dot hiện tại
            slides[slideIndex].classList.add('active');
            dots[slideIndex].classList.add('active');
        }

        // Chuyển slide tự động mỗi 5 giây
        function autoSlide() {
            slideIndex++;
            showSlide(slideIndex);
        }

        // Khởi tạo slider: Hiển thị slide đầu tiên
        showSlide(slideIndex);

        // Tự động chuyển slide
        let autoSlideInterval = setInterval(autoSlide, 5000);

        // Xử lý nút điều hướng
        document.querySelector('.prev-slide').addEventListener('click', () => {
            slideIndex--;
            showSlide(slideIndex);
            // Reset interval để tránh chuyển slide quá nhanh
            clearInterval(autoSlideInterval);
            autoSlideInterval = setInterval(autoSlide, 5000);
        });

        document.querySelector('.next-slide').addEventListener('click', () => {
            slideIndex++;
            showSlide(slideIndex);
            // Reset interval để tránh chuyển slide quá nhanh
            clearInterval(autoSlideInterval);
            autoSlideInterval = setInterval(autoSlide, 5000);
        });

        // Xử lý khi click vào dot
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                slideIndex = index;
                showSlide(slideIndex);
                // Reset interval để tránh chuyển slide quá nhanh
                clearInterval(autoSlideInterval);
                autoSlideInterval = setInterval(autoSlide, 5000);
            });
        });
    }
});