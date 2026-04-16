<?php
// index.php
$pageTitle = '· Shift & Drift · ';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = Database::getInstance()->getConnection();

// Последние 8 авто
$sql = "SELECT c.*, b.name AS brand_name 
        FROM cars c 
        JOIN brands b ON c.brand_id = b.id 
        WHERE c.is_available = TRUE 
        ORDER BY c.created_at DESC 
        LIMIT 8";
$result = $db->query($sql);

// Последние 3 новости
$newsQuery = $db->query("SELECT id, title, short_text, image_url, created_at FROM news ORDER BY created_at DESC LIMIT 3");
?>

<div class="container">
    <!-- Слайдер вместо статичного hero -->
    <div class="hero-slider">
        <div class="slides-container" id="slidesContainer">
            <div class="slide" style="background-image: url('https://a.d-cd.net/qKxMMpBQh3eaYEE7LlEdP3cXaoM-960.jpg');">
                <div class="slide-content">
                    <h2>JDM Легенды</h2>
                    <p>Аукционы, реставрация, доставка.</p>
                    <a href="catalog.php" class="btn-primary"><i class="fas fa-flame"></i> Перейти к лотам</a>
                </div>
            </div>
            <div class="slide" style="background-image: url('https://avatars.mds.yandex.net/get-altay/11400839/2a0000018d646a2768cbdc7d963b8d9eb0d2/XL');">
                <div class="slide-content">
                    <h2>Nissan GT-R R34</h2>
                    <p>Новое поступление в шоуруме. Оценка 5A, полностью оригинал.</p>
                    <a href="car.php?id=2" class="btn-primary">Смотреть авто</a>
                </div>
            </div>
            <div class="slide" style="background-image: url('https://avatars.mds.yandex.net/i?id=d92bca3dbfcbed5ebe9ae3fc25ce7ae0_l-5143133-images-thumbs&n=13');">
                <div class="slide-content">
                    <h2>Реставрация и тюнинг</h2>
                    <p>Профессиональное обслуживание JDM. Гарантия качества.</p>
                    <a href="index.php#services" class="btn-primary">Узнать больше</a>
                </div>
            </div>
        </div>
        
        <!-- Стрелки -->
        <div class="slider-arrow left" onclick="changeSlide(-1)">&#10094;</div>
        <div class="slider-arrow right" onclick="changeSlide(1)">&#10095;</div>
        
        <!-- Точки -->
        <div class="slider-nav" id="sliderDots"></div>
    </div>

    <div class="quick-filter">
        <span class="filter-label"><i class="fas fa-bolt"></i> Быстрый старт</span>
        <div class="filter-badges">
            <?php
            $brands = $db->query("SELECT * FROM brands ORDER BY name");
            while ($b = $brands->fetch_assoc()):
            ?>
                <a href="catalog.php?brand=<?= $b['id'] ?>" class="badge">
                    <i class="fas fa-car"></i> <?= htmlspecialchars($b['name']) ?>
                </a>
            <?php endwhile; ?>

        </div>
    </div>

    <div>
        <div class="section-title">
            <span>新着</span> Новые поступления
        </div>
        <div class="car-scroll">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($car = $result->fetch_assoc()): ?>
                    <div class="car-card">
                        <img src="<?= htmlspecialchars(str_replace(' ', '%20', $car['image_url'] ?: 'https://placehold.co/600x400/f5f5f5/b33b4a?text=No+Image')) ?>" 
                             alt="<?= htmlspecialchars($car['brand_name'] . ' ' . $car['model']) ?>">
                        <div class="car-header">
                            <h3><?= htmlspecialchars($car['brand_name'] . ' ' . $car['model']) ?></h3>
                            <span class="car-badge"><?= htmlspecialchars($car['generation'] ?? '') ?></span>
                        </div>
                        <div class="spec-grid">
                            <div class="spec-item"><i class="fas fa-calendar"></i><span class="spec-label"><?= $car['year_from'] ?></span></div>
                            <div class="spec-item"><i class="fas fa-tachometer-alt"></i><span class="spec-label"><?= number_format($car['mileage'], 0, '', ' ') ?> км</span></div>
                            <div class="spec-item"><i class="fas fa-cogs"></i><span class="spec-label"><?= htmlspecialchars($car['engine_type']) ?></span></div>
                            <div class="spec-item"><i class="fas fa-horse-head"></i><span class="spec-label"><?= $car['engine_power'] ?> л.с.</span></div>
                            <div class="spec-item"><i class="fas fa-caravan"></i><span class="spec-label"><?= $car['drive'] ?></span></div>
                            <div class="spec-item"><i class="fas fa-cog"></i><span class="spec-label"><?= $car['transmission'] ?></span></div>
                        </div>
                        <div class="auction-grade"><?= htmlspecialchars($car['condition']) ?></div>
                        <div class="car-price"><?= number_format($car['price'], 0, '', ' ') ?> ₽</div>
                        <a href="car.php?id=<?= $car['id'] ?>" class="btn-detail">Подробнее</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Автомобили временно отсутствуют.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Услуги (с модальными окнами) -->
    <div id="services" style="margin-top: 60px;">
        <div class="section-title">
            <span>職人</span> Наши услуги
        </div>
        <div class="services-grid">
            <div class="service-item" data-service="auction">
                <i class="fas fa-gavel"></i>
                <h4>Доступ к закрытым аукционам</h4>
                <p>USS Tokyo, TAA, JU Gifu. Инспекция, торги, перевод.</p>
            </div>
            <div class="service-item" data-service="logistics">
                <i class="fas fa-ship"></i>
                <h4>Доставка «под ключ»</h4>
                <p>Море / авиа, таможня, сертификация, автовоз.</p>
            </div>
            <div class="service-item" data-service="restoration">
                <i class="fas fa-tools"></i>
                <h4>Реставрация и обслуживание</h4>
                <p>Кузов, оригинальные запчасти, детейлинг.</p>
            </div>
            <div class="service-item" data-service="inspection">
                <i class="fas fa-search"></i>
                <h4>Выездная инспекция</h4>
                <p>Личный осмотр экспертом с фото/видео отчётом.</p>
            </div>
            <div class="service-item" data-service="tuning">
                <i class="fas fa-microchip"></i>
                <h4>Тюнинг и дооснащение</h4>
                <p>HKS, BLITZ, RAYS, мультимедиа, безопасность.</p>
            </div>
            <div class="service-item" data-service="finance">
                <i class="fas fa-hand-holding-usd"></i>
                <h4>Финансовые решения</h4>
                <p>Кредит, лизинг, Trade-In на специальных условиях.</p>
            </div>
        </div>
    </div>

    <!-- Модальное окно услуг -->
    <div id="serviceModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <!-- Команда -->
    <div id="team" style="margin-top: 60px;">
        <div class="section-title"><span>匠</span> Команда</div>
        <div class="team-grid">
            <div class="team-card">
                <img src="images/kenichi.jpg" alt="Кеничи Хаяси">
                <h5>Кеничи Хаяси</h5>
                <div class="team-role">Tech Lead · RB26</div>
                <p>19 лет в NISMO, инженер-испытатель.</p>
            </div>
            <div class="team-card">
                <img src="images/dmitry.jpg" alt="Дмитрий Торнадо">
                <h5>Дмитрий Торнадо</h5>
                <div class="team-role">Байер USS</div>
                <p>600+ сделок, личная инспекция.</p>
            </div>
            <div class="team-card">
                <img src="images/elena.jpg" alt="Елена Сато">
                <h5>Елена Сато</h5>
                <div class="team-role">Клиентский сервис</div>
                <p>Логистика, сопровождение.</p>
            </div>
            <div class="team-card">
                <img src="images/ryuji.jpg" alt="Рюдзи Такахаси">
                <h5>Рюдзи Такахаси</h5>
                <div class="team-role">Ротор-мастер</div>
                <p>Экс-RE Amemiya, гуру 13B/20B</p>
            </div>
        </div>
    </div>

    <!-- Карта с местоположением -->
    <div style="margin-top: 60px;">
        <div class="section-title">
            <span>所在地</span> Где нас найти
        </div>
        <div style="width: 100%; height: 400px; border: 1px solid #ececec;">
            <iframe 
                src="https://yandex.ru/map-widget/v1/?um=constructor%3A2b6767049ca9f60c133edc471206a3aee8d4c542214beae17de2c294d9a76647&amp;source=constructor" 
                height="400" 
                style="width: 100%; border: 0;" 
                allowfullscreen>
            </iframe>
        </div>
        <p style="margin-top: 10px; color: #6a6a6a;">
            <i class="fas fa-map-marker-alt" style="color: #b33b4a;"></i> 
            г. Ярославль, ул. Дорожная, д. 22
        </p>
    </div>

    <!-- Последние новости -->
    <div style="margin-top: 60px;">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 30px;">
            <div class="section-title" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
                <span>お知らせ</span> Новости
            </div>
            <a href="news.php" style="color: #b33b4a; text-decoration: none; font-weight: 500;">Все новости →</a>
        </div>
        
        <?php if ($newsQuery && $newsQuery->num_rows > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
                <?php while ($news = $newsQuery->fetch_assoc()): ?>
                    <div style="border: 1px solid #ececec; padding: 20px;">
                        <div style="height: 180px; background: #f6f6f6; margin-bottom: 20px; background-image: url('<?= htmlspecialchars(str_replace(' ', '%20', $news['image_url'] ?: 'https://placehold.co/600x400/f5f5f5/b33b4a?text=News')) ?>'); background-size: cover; background-position: center;"></div>
                        <span style="color: #b33b4a; font-size: 0.8rem; font-weight: 500;"><?= date('d.m.Y', strtotime($news['created_at'])) ?></span>
                        <h3 style="font-size: 1.2rem; margin: 10px 0;"><?= htmlspecialchars($news['title']) ?></h3>
                        <p style="color: #6a6a6a; font-size: 0.9rem;"><?= htmlspecialchars(mb_substr($news['short_text'], 0, 100)) ?>...</p>
                        <a href="news_detail.php?id=<?= $news['id'] ?>" style="display: inline-block; margin-top: 10px; color: #b33b4a; text-decoration: none;">Читать →</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="color: #6a6a6a;">Новостей пока нет.</p>
        <?php endif; ?>
    </div>
</div>

<script>
// Скрипт слайдера
document.addEventListener('DOMContentLoaded', function() {
    const slidesContainer = document.getElementById('slidesContainer');
    const slides = document.querySelectorAll('.slide');
    const dotsContainer = document.getElementById('sliderDots');
    let currentIndex = 0;
    const totalSlides = slides.length;
    let autoSlideInterval;

    // Создаём точки
    for (let i = 0; i < totalSlides; i++) {
        const dot = document.createElement('span');
        dot.classList.add('slider-dot');
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    }
    const dots = document.querySelectorAll('.slider-dot');
    
    function updateDots() {
        dots.forEach((dot, idx) => {
            dot.classList.toggle('active', idx === currentIndex);
        });
    }

    function goToSlide(index) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;
        currentIndex = index;
        slidesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
        updateDots();
    }

    window.changeSlide = function(direction) {
        goToSlide(currentIndex + direction);
        resetAutoSlide();
    };

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(nextSlide, 5000);
    }

    // Запуск автопрокрутки
    autoSlideInterval = setInterval(nextSlide, 5000);
    
    // Инициализация
    goToSlide(0);
});
</script>

<?php require_once 'includes/footer.php'; ?>