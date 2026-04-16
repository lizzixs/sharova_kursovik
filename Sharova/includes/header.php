<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Shift & Drift '; ?></title>
    <base href="<?= BASE_URL ?>/">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Стили слайдера (перенесены из index.php для валидности) -->
    <style>
        .hero-slider {
            position: relative;
            width: 100%;
            height: 500px;
            overflow: hidden;
            margin-bottom: 40px;
        }
        .slides-container {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 100%;
        }
        .slide {
            flex: 0 0 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 50%);
        }
        .slide-content {
            position: absolute;
            bottom: 60px;
            left: 60px;
            color: #fff;
            z-index: 2;
            max-width: 600px;
        }
        .slide-content h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }
        .slide-content p {
            font-size: 1.2rem;
            margin-bottom: 25px;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.5);
        }
        .slider-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 3;
        }
        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.8);
        }
        .slider-dot.active {
            background: #b33b4a;
            transform: scale(1.2);
        }
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: rgba(0,0,0,0.3);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            z-index: 3;
            transition: background 0.3s;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .slider-arrow:hover {
            background: #b33b4a;
        }
        .slider-arrow.left {
            left: 20px;
        }
        .slider-arrow.right {
            right: 20px;
        }
        @media (max-width: 768px) {
            .hero-slider {
                height: 400px;
            }
            .slide-content {
                left: 20px;
                bottom: 40px;
            }
            .slide-content h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="Shift & Drift" style="height: 70px;">
            </a>
            
            <!-- Кнопка бургер-меню (видна только на мобильных) -->
            <button class="burger-menu" id="burgerMenu" aria-label="Меню">
                <span class="burger-line"></span>
                <span class="burger-line"></span>
                <span class="burger-line"></span>
            </button>
            
            <nav class="main-nav" id="mainNav">
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="catalog.php">Каталог</a></li>
                    <li><a href="news.php">Новости</a></li>
                    <li><a href="index.php#services">Услуги</a></li>
                    <li><a href="index.php#team">О нас</a></li>
                    <?php if (Session::isLoggedIn()): ?>
                        <li><a href="profile.php">Личный кабинет</a></li>
                        <?php if (Session::isAdmin()): ?>
                            <li><a href="admin/">Админ</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <div class="user-actions" id="userActions">
                <?php if (Session::isLoggedIn()): ?>
                    <span class="user-greeting">Привет, <?= htmlspecialchars(Session::getUserName()) ?></span>
                    <a href="logout.php" class="btn-login"><i class="fas fa-sign-out-alt"></i> Выйти</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login"><i class="fas fa-user"></i> Войти</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>

    