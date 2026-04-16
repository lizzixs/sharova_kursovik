<?php
require_once 'config/database.php';
require_once 'config/session.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$user_id = Session::getUserId();

$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

$favStmt = $db->prepare("
    SELECT c.*, b.name AS brand_name 
    FROM favorites f 
    JOIN cars c ON f.car_id = c.id 
    JOIN brands b ON c.brand_id = b.id 
    WHERE f.user_id = ? 
    ORDER BY f.created_at DESC
");
$favStmt->bind_param("i", $user_id);
$favStmt->execute();
$favorites = $favStmt->get_result();

$reqStmt = $db->prepare("
    SELECT r.*, c.model, b.name AS brand_name 
    FROM requests r 
    LEFT JOIN cars c ON r.car_id = c.id 
    LEFT JOIN brands b ON c.brand_id = b.id 
    WHERE r.name = ? OR r.email = ?
    ORDER BY r.created_at DESC
");
$reqStmt->bind_param("ss", $user['full_name'], $user['email']);
$reqStmt->execute();
$requests = $reqStmt->get_result();

$pageTitle = 'Личный кабинет · Shift & Drift';
require_once 'includes/header.php';
?>

<div class="container" style="margin: 40px auto;">
    <h1 style="font-size: 2rem; margin-bottom: 30px;">Личный кабинет</h1>
    
    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 40px;">
        <div style="background: #f9f9f9; padding: 30px; border: 1px solid #ececec;">
            <div style="text-align: center; margin-bottom: 30px;">
                <i class="fas fa-user-circle" style="font-size: 80px; color: #b33b4a;"></i>
                <h2 style="margin: 15px 0 5px;"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></h2>
                <p style="color: #6a6a6a;"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <ul style="list-style: none;">
                <li><a href="#profile" style="color: #b33b4a;"><i class="fas fa-user"></i> Профиль</a></li>
                <li><a href="#favorites" style="color: #b33b4a;"><i class="fas fa-heart"></i> Избранное</a></li>
                <li><a href="#requests" style="color: #b33b4a;"><i class="fas fa-clock"></i> Заявки</a></li>
                <li><a href="logout.php" style="color: #b33b4a;"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
            </ul>
        </div>
        
        <div>
            <div id="profile" style="margin-bottom: 50px;">
                <h2 style="border-bottom: 2px solid #ececec; padding-bottom: 10px;">Данные профиля</h2>
                <form method="POST" action="update_profile.php" style="max-width: 500px;">
                    <div style="margin-bottom: 15px;">
                        <label>Имя пользователя</label>
                        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled style="width: 100%; padding: 10px; background: #f5f5f5;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Полное имя</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" style="width: 100%; padding: 10px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" style="width: 100%; padding: 10px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Новый пароль (оставьте пустым, если не меняете)</label>
                        <input type="password" name="password" style="width: 100%; padding: 10px;">
                    </div>
                    <button type="submit" class="btn-primary">Сохранить</button>
                </form>
            </div>
            
            <div id="favorites" style="margin-bottom: 50px;">
                <h2 style="border-bottom: 2px solid #ececec; padding-bottom: 10px;">Избранное</h2>
                <div class="car-scroll">
                    <?php if ($favorites->num_rows > 0): ?>
                        <?php while($car = $favorites->fetch_assoc()): ?>
                            <div class="car-card">
                                <img src="<?= htmlspecialchars($car['image_url'] ?: 'https://placehold.co/600x400/f5f5f5/b33b4a') ?>" alt="<?= $car['brand_name'] . ' ' . $car['model'] ?>">
                                <h3><?= $car['brand_name'] . ' ' . $car['model'] ?></h3>
                                <div class="car-price"><?= number_format($car['price'], 0, '', ' ') ?> ₽</div>
                                <a href="car.php?id=<?= $car['id'] ?>" class="btn-detail">Подробнее</a>
                                <a href="favorite.php?car_id=<?= $car['id'] ?>&action=remove&return=profile" class="btn-detail" style="background:#fee; color:#c00;">Удалить</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Нет избранных авто</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div id="requests">
                <h2 style="border-bottom: 2px solid #ececec; padding-bottom: 10px;">Мои заявки</h2>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f5f5f5;"><th>Дата</th><th>Авто</th><th>Тип</th><th>Статус</th></tr>
                    </thead>
                    <tbody>
                        <?php if ($requests->num_rows > 0): ?>
                            <?php while($req = $requests->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d.m.Y', strtotime($req['created_at'])) ?></td>
                                    <td><?= $req['brand_name'] ? $req['brand_name'] . ' ' . $req['model'] : 'Не указан' ?></td>
                                    <td>
                                        <?php
                                        $types = ['test_drive'=>'Тест-драйв','price'=>'Запрос цены','callback'=>'Обратный звонок','trade_in'=>'Трейд-ин','auction'=>'Аукцион','logistics'=>'Логистика','restoration'=>'Реставрация','inspection'=>'Инспекция','tuning'=>'Тюнинг','finance'=>'Финансы'];
                                        echo $types[$req['request_type']] ?? $req['request_type'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statuses = ['new'=>'Новая','in_progress'=>'В обработке','done'=>'Завершена','cancelled'=>'Отменена'];
                                        echo $statuses[$req['status']] ?? $req['status'];
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">Нет заявок</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>