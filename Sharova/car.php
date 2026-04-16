<?php
// Включаем отображение ошибок 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
require_once 'config/session.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: catalog.php');
    exit;
}

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("
    SELECT c.*, b.name AS brand_name 
    FROM cars c 
    JOIN brands b ON c.brand_id = b.id 
    WHERE c.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: catalog.php');
    exit;
}
$car = $result->fetch_assoc();

$is_favorite = false;
if (Session::isLoggedIn()) {
    $userId = Session::getUserId();   
    $favStmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND car_id = ?");
    $favStmt->bind_param("ii", $userId, $id);
    $favStmt->execute();
    $is_favorite = $favStmt->get_result()->num_rows > 0;
}

$reviews = $db->prepare("
    SELECT client_name, rating, text, created_at 
    FROM reviews 
    WHERE car_id = ? AND is_approved = TRUE 
    ORDER BY created_at DESC
");
$reviews->bind_param("i", $id);
$reviews->execute();
$reviews_result = $reviews->get_result();

$pageTitle = $car['brand_name'] . ' ' . $car['model'] . ' · Shift & Drift';
require_once 'includes/header.php';
?>

<div class="container" style="margin-top: 40px; margin-bottom: 60px;">
    <?php if (isset($_GET['review_pending']) && $_GET['review_pending'] == 1): ?>
        <div style="background: #fff3e0; color: #e65100; padding: 15px 20px; margin-bottom: 25px; border-left: 4px solid #e65100; border-radius: 0 4px 4px 0;">
            <i class="fas fa-clock" style="margin-right: 10px;"></i>
            Ваш отзыв отправлен на модерацию и появится после проверки.
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['review_ok']) && $_GET['review_ok'] == 1): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 15px 20px; margin-bottom: 25px; border-left: 4px solid #2e7d32; border-radius: 0 4px 4px 0;">
            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
            Ваш отзыв успешно опубликован! Спасибо, что делитесь мнением.
        </div>
    <?php endif; ?>
    
    <a href="catalog.php" style="display: inline-block; margin-bottom: 30px; color: #b33b4a; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Вернуться в каталог
    </a>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
        <div style="background: #f6f6f6; min-height: 400px; background-image: url('<?= htmlspecialchars(str_replace(' ', '%20', $car['image_url'] ?: 'https://placehold.co/800x600/f5f5f5/b33b4a?text=No+Image')) ?>'); background-size: cover; background-position: center;"></div>
        
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 700;"><?= htmlspecialchars($car['brand_name'] . ' ' . $car['model']) ?></h1>
            <p style="color: #b33b4a; font-weight: 500; margin-bottom: 20px;">
                <?= htmlspecialchars($car['generation'] ?? '') ?> · <?= $car['year_from'] ?> г.
            </p>
            
            <div style="border-top: 1px solid #ececec; border-bottom: 1px solid #ececec; padding: 25px 0; margin: 25px 0; display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div><i class="fas fa-calendar"></i> <strong>Год:</strong> <?= $car['year_from'] ?></div>
                <div><i class="fas fa-tachometer-alt"></i> <strong>Пробег:</strong> <?= number_format($car['mileage'], 0, '', ' ') ?> км</div>
                <div><i class="fas fa-cogs"></i> <strong>Двигатель:</strong> <?= htmlspecialchars($car['engine_type']) ?></div>
                <div><i class="fas fa-horse-head"></i> <strong>Мощность:</strong> <?= $car['engine_power'] ?> л.с.</div>
                <div><i class="fas fa-caravan"></i> <strong>Привод:</strong> <?= $car['drive'] ?></div>
                <div><i class="fas fa-cog"></i> <strong>КПП:</strong> <?= $car['transmission'] ?></div>
                <div><i class="fas fa-palette"></i> <strong>Цвет:</strong> <?= htmlspecialchars($car['color']) ?></div>
                <div><i class="fas fa-clipboard-check"></i> <strong>Состояние:</strong> <?= $car['condition'] ?></div>
            </div>
            
            <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($car['description'])) ?></p>
            
            <div style="margin: 30px 0; font-size: 2.5rem; font-weight: 700;">
                <?= number_format($car['price'], 0, '', ' ') ?> ₽
            </div>
            
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <?php if (Session::isLoggedIn()): ?>
                    <a href="request.php?car_id=<?= $car['id'] ?>&type=test_drive" class="btn-primary">Записаться на тест-драйв</a>
                    <a href="request.php?car_id=<?= $car['id'] ?>&type=price" class="btn-primary" style="background: #8f2f3b;">Запросить цену</a>
                    <a href="favorite.php?car_id=<?= $car['id'] ?>&action=<?= $is_favorite ? 'remove' : 'add' ?>" class="btn-detail" style="padding: 14px 30px;">
                        <i class="fas fa-<?= $is_favorite ? 'heart' : 'heart-o' ?>"></i> 
                        <?= $is_favorite ? 'В избранном' : 'В избранное' ?>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-primary">Войдите для действий</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Отзывы -->
    <div style="margin-top: 60px;">
        <h3 style="font-size: 1.8rem; margin-bottom: 30px;">Отзывы владельцев</h3>
        <?php if ($reviews_result->num_rows > 0): ?>
            <?php while($rev = $reviews_result->fetch_assoc()): ?>
                <div style="background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-left: 4px solid #b33b4a;">
                    <div style="display: flex; justify-content: space-between;">
                        <strong><?= htmlspecialchars($rev['client_name']) ?></strong>
                        <span><?= str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']) ?></span>
                    </div>
                    <p style="margin-top: 10px;"><?= nl2br(htmlspecialchars($rev['text'])) ?></p>
                    <small><?= date('d.m.Y', strtotime($rev['created_at'])) ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Пока нет отзывов. Будьте первым!</p>
        <?php endif; ?>
        
        <?php if (Session::isLoggedIn()): ?>
            <form method="POST" action="add_review.php" style="margin-top: 30px; background: #f9f9f9; padding: 30px;">
                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                <h4>Оставить отзыв</h4>
                <div style="margin-bottom: 15px;">
                    <label>Ваша оценка</label>
                    <select name="rating" required style="width: 100%; padding: 10px;">
                        <option value="5">5 - Отлично</option>
                        <option value="4">4 - Хорошо</option>
                        <option value="3">3 - Средне</option>
                        <option value="2">2 - Плохо</option>
                        <option value="1">1 - Ужасно</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Текст отзыва</label>
                    <textarea name="text" rows="4" required style="width: 100%; padding: 10px;"></textarea>
                </div>
                <button type="submit" class="btn-primary">Отправить отзыв</button>
                <p><small>Отзыв появится после проверки модератором.</small></p>
            </form>
        <?php else: ?>
            <p><a href="login.php">Войдите</a>, чтобы оставить отзыв.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
