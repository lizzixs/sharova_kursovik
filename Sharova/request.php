<?php
require_once 'config/database.php';
require_once 'config/session.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;
$type = $_GET['type'] ?? 'callback';

$db = Database::getInstance()->getConnection();

if ($car_id > 0) {
    $stmt = $db->prepare("SELECT c.*, b.name AS brand_name FROM cars c JOIN brands b ON c.brand_id = b.id WHERE c.id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $car = $stmt->get_result()->fetch_assoc();
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    // Исправление: передаём NULL, если автомобиль не выбран
    $car_id_post = isset($_POST['car_id']) && $_POST['car_id'] > 0 ? intval($_POST['car_id']) : null;
    $request_type = $_POST['request_type'] ?? 'callback';
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($phone)) {
        $error = 'Имя и телефон обязательны';
    } else {
        $stmt = $db->prepare("INSERT INTO requests (name, phone, email, car_id, request_type, message) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $name, $phone, $email, $car_id_post, $request_type, $message);
        
        if ($stmt->execute()) {
            $success = 'Заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.';
        } else {
            $error = 'Ошибка при отправке заявки';
        }
    }
}

$pageTitle = 'Создание заявки · Shift & Drift';
require_once 'includes/header.php';
?>

<div class="container" style="max-width: 600px; margin: 60px auto;">
    <h1 style="font-size: 2rem; margin-bottom: 30px;">Создание заявки</h1>
    
    <?php if ($success): ?>
        <div style="background: #efe; color: #0a0; padding: 15px; margin-bottom: 20px;"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div style="background: #fee; color: #c00; padding: 15px; margin-bottom: 20px;"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" style="background: #f9f9f9; padding: 40px;">
        <input type="hidden" name="car_id" value="<?= $car_id ?>">
        
        <div style="margin-bottom: 20px;">
            <label>Тип заявки</label>
            <select name="request_type" required style="width: 100%; padding: 12px;">
                <option value="test_drive" <?= $type == 'test_drive' ? 'selected' : '' ?>>Тест-драйв</option>
                <option value="price" <?= $type == 'price' ? 'selected' : '' ?>>Запрос цены</option>
                <option value="callback" <?= $type == 'callback' ? 'selected' : '' ?>>Обратный звонок</option>
                <option value="trade_in" <?= $type == 'trade_in' ? 'selected' : '' ?>>Трейд-ин</option>
                <option value="auction" <?= $type == 'auction' ? 'selected' : '' ?>>Доступ к аукционам</option>
                <option value="logistics" <?= $type == 'logistics' ? 'selected' : '' ?>>Доставка «под ключ»</option>
                <option value="restoration" <?= $type == 'restoration' ? 'selected' : '' ?>>Реставрация и обслуживание</option>
                <option value="inspection" <?= $type == 'inspection' ? 'selected' : '' ?>>Выездная инспекция</option>
                <option value="tuning" <?= $type == 'tuning' ? 'selected' : '' ?>>Тюнинг и дооснащение</option>
                <option value="finance" <?= $type == 'finance' ? 'selected' : '' ?>>Финансовые решения</option>
            </select>
        </div>
        
        <?php if ($car_id > 0 && isset($car)): ?>
            <div style="margin-bottom: 20px; padding: 15px; background: #fff;">
                <strong>Автомобиль:</strong> <?= htmlspecialchars($car['brand_name'] . ' ' . $car['model']) ?><br>
                <strong>Цена:</strong> <?= number_format($car['price'], 0, '', ' ') ?> ₽
            </div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <label>Ваше имя *</label>
            <input type="text" name="name" required style="width: 100%; padding: 12px;" value="<?= htmlspecialchars(Session::getUserName()) ?>">
        </div>
        <div style="margin-bottom: 20px;">
            <label>Телефон *</label>
            <input type="tel" name="phone" required style="width: 100%; padding: 12px;">
        </div>
        <div style="margin-bottom: 20px;">
            <label>Email</label>
            <input type="email" name="email" style="width: 100%; padding: 12px;">
        </div>
        <div style="margin-bottom: 20px;">
            <label>Сообщение</label>
            <textarea name="message" rows="4" style="width: 100%; padding: 12px;"></textarea>
        </div>
        
        <button type="submit" class="btn-primary" style="width: 100%;">Отправить заявку</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>