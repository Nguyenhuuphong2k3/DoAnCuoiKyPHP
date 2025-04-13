<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Store</title>
    <link rel="stylesheet" href="/css/style.css">
    <!-- Thêm Font Awesome để sử dụng biểu tượng -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-container">
            <!-- Logo/Brand -->
            <div class="brand">
                <a href="?controller=default&action=index" class="brand-logo">Phone Store</a>
            </div>

            <!-- Menu điều hướng -->
            <nav class="nav-menu">
                <div class="nav-links">
                    <a href="?controller=default&action=index">Trang chủ</a>
                    <a href="?controller=product&action=list">Sản phẩm</a>
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="?controller=cart&action=index">Giỏ hàng</a>
                        <a href="?controller=order&action=history">Lịch sử đơn hàng</a>
                        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                            <a href="?controller=category&action=list">Quản lý thể loại</a>
                        <?php endif; ?>
                        <a href="?controller=user&action=logout">Đăng xuất</a>
                    <?php else: ?>
                        <a href="?controller=user&action=login">Đăng nhập</a>
                        <a href="?controller=user&action=register">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </nav>

            <!-- Thanh tìm kiếm -->
            <form action="?controller=product&action=search" method="GET" class="search-form">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="search">
                <div class="search-container">
                    <input type="text" name="keyword" placeholder="Tìm kiếm..." class="search-input">
                    <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </header>