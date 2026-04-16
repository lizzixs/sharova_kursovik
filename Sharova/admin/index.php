<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!Session::isAdmin()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$db = Database::getInstance()->getConnection();

$action = $_GET['action'] ?? '';
$tab = $_GET['tab'] ?? 'cars';

// --- Удаление ---
if ($action === 'delete') {
    $type = $_GET['type'] ?? '';
    $id = intval($_GET['id'] ?? 0);
    if ($id > 0 && in_array($type, ['car','request','review','news'])) {
        $table = $type === 'car' ? 'cars' : ($type === 'request' ? 'requests' : ($type === 'review' ? 'reviews' : 'news'));
        $stmt = $db->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: " . BASE_URL . "/admin/?tab=$tab");
    exit;
}

// --- Смена статуса заявки ---
if ($action === 'update_status' && isset($_POST['request_id'], $_POST['status'])) {
    $id = intval($_POST['request_id']);
    $status = $_POST['status'];
    $stmt = $db->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    header("Location: " . BASE_URL . "/admin/?tab=requests");
    exit;
}

// --- Одобрение отзыва ---
if ($action === 'approve_review') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $db->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . BASE_URL . "/admin/?tab=reviews");
    exit;
}

// --- Сохранение автомобиля ---
if ($action === 'save_car' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $brand_id = intval($_POST['brand_id']);
    $name = trim($_POST['name']);
    $model = trim($_POST['model']);
    $generation = trim($_POST['generation'] ?? '');
    $year_from = intval($_POST['year_from']);
    $price = intval($_POST['price']);
    $engine_type = trim($_POST['engine_type'] ?? '');
    $engine_volume = floatval($_POST['engine_volume'] ?? 0);
    $engine_power = intval($_POST['engine_power'] ?? 0);
    $transmission = trim($_POST['transmission'] ?? '');
    $drive = trim($_POST['drive'] ?? '');
    $fuel = trim($_POST['fuel'] ?? 'Бензин');
    $color = trim($_POST['color'] ?? '');
    $mileage = intval($_POST['mileage'] ?? 0);
    $condition = $_POST['condition'] ?? 'С пробегом';
    $badge_text = trim($_POST['badge_text'] ?? '');
    $model_code = trim($_POST['model_code'] ?? '');
    $grade_info = trim($_POST['grade_info'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $image_alt = trim($_POST['image_alt'] ?? '');
    $description = trim($_POST['description']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE cars SET brand_id=?, name=?, model=?, generation=?, year_from=?, price=?, engine_type=?, engine_volume=?, engine_power=?, transmission=?, drive=?, fuel=?, color=?, mileage=?, `condition`=?, badge_text=?, model_code=?, grade_info=?, image_url=?, image_alt=?, description=?, is_available=? WHERE id=?");
        $stmt->bind_param("isssiisdisssssissssssii", $brand_id, $name, $model, $generation, $year_from, $price, $engine_type, $engine_volume, $engine_power, $transmission, $drive, $fuel, $color, $mileage, $condition, $badge_text, $model_code, $grade_info, $image_url, $image_alt, $description, $is_available, $id);
    } else {
        $stmt = $db->prepare("INSERT INTO cars (brand_id, name, model, generation, year_from, price, engine_type, engine_volume, engine_power, transmission, drive, fuel, color, mileage, `condition`, badge_text, model_code, grade_info, image_url, image_alt, description, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiisdisssssissssssi", $brand_id, $name, $model, $generation, $year_from, $price, $engine_type, $engine_volume, $engine_power, $transmission, $drive, $fuel, $color, $mileage, $condition, $badge_text, $model_code, $grade_info, $image_url, $image_alt, $description, $is_available);
    }
    $stmt->execute();
    header("Location: " . BASE_URL . "/admin/?tab=cars");
    exit;
}

// --- Сохранение новости ---
if ($action === 'save_news' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title']);
    $short_text = trim($_POST['short_text']);
    $full_text = trim($_POST['full_text']);
    $image_url = trim($_POST['image_url'] ?? '');

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE news SET title=?, short_text=?, full_text=?, image_url=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $short_text, $full_text, $image_url, $id);
    } else {
        $stmt = $db->prepare("INSERT INTO news (title, short_text, full_text, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $short_text, $full_text, $image_url);
    }
    $stmt->execute();
    header("Location: " . BASE_URL . "/admin/?tab=news");
    exit;
}

// ================== ПОЛУЧЕНИЕ ДАННЫХ ==================
$cars = $db->query("SELECT c.*, b.name AS brand_name FROM cars c JOIN brands b ON c.brand_id = b.id ORDER BY c.id DESC");
$brands = $db->query("SELECT * FROM brands ORDER BY name");
$requests = $db->query("SELECT r.*, c.model, b.name AS brand_name FROM requests r LEFT JOIN cars c ON r.car_id = c.id LEFT JOIN brands b ON c.brand_id = b.id ORDER BY r.created_at DESC");
$reviews = $db->query("SELECT r.*, c.model, b.name AS brand_name FROM reviews r LEFT JOIN cars c ON r.car_id = c.id LEFT JOIN brands b ON c.brand_id = b.id ORDER BY r.created_at DESC");
$news = $db->query("SELECT * FROM news ORDER BY created_at DESC");

$cars_count = $cars->num_rows;
$users_count = $db->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'];
$new_requests = $db->query("SELECT COUNT(*) AS cnt FROM requests WHERE status = 'new'")->fetch_assoc()['cnt'];
$pending_reviews = $db->query("SELECT COUNT(*) AS cnt FROM reviews WHERE is_approved = 0")->fetch_assoc()['cnt'];

$pageTitle = 'Админ-панель · Shift & Drift';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <base href="<?= BASE_URL ?>/">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-header { background: #1e1e1e; color: #fff; padding: 20px 0; margin-bottom: 30px; }
        .admin-container { max-width: 1280px; margin: 0 auto; padding: 0 32px; }
        .admin-header .admin-container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .admin-header h1 { font-size: 1.8rem; margin: 0; }
        .admin-header h1 a { color: #fff; text-decoration: none; }
        .admin-header nav { display: flex; align-items: center; gap: 25px; }
        .admin-header .admin-user { color: #aaa; }
        .admin-header .btn-logout { background: transparent; border: 1px solid #666; color: #fff; padding: 6px 15px; text-decoration: none; }
        .admin-footer { background: #f5f5f5; padding: 20px 0; margin-top: 60px; text-align: center; border-top: 1px solid #ddd; color: #6a6a6a; }
        .tabs { display: flex; border-bottom: 2px solid #ececec; margin-bottom: 30px; }
        .tab-btn { padding: 12px 24px; background: none; border: none; font-size: 1rem; font-weight: 500; color: #6a6a6a; cursor: pointer; border-bottom: 2px solid transparent; }
        .tab-btn i { margin-right: 8px; }
        .tab-btn.active { color: #b33b4a; border-bottom-color: #b33b4a; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f5f5; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: #fff; margin: 2% auto; padding: 30px; max-width: 900px; max-height: 90vh; overflow-y: auto; position: relative; }
        .close-modal { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; }
        .btn { padding: 8px 16px; background: #b33b4a; color: #fff; border: none; cursor: pointer; text-decoration: none; }
        .btn-icon { color: #b33b4a; margin-right: 10px; background: none; border: none; cursor: pointer; }
        .btn-delete { color: #c00; }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-container">
            <h1><a href="<?= BASE_URL ?>/admin/">Shift & Drift · Админ</a></h1>
            <nav>
                <span class="admin-user"><i class="fas fa-user-shield"></i> <?= htmlspecialchars(Session::getUserName()) ?></span>
                <a href="<?= BASE_URL ?>/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Выйти</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="admin-container">
            <div style="display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; margin-bottom: 30px;">
                <div style="background:#f9f9f9; padding:20px; text-align:center;"><strong>Авто</strong><br><?= $cars_count ?></div>
                <div style="background:#f9f9f9; padding:20px; text-align:center;"><strong>Пользователи</strong><br><?= $users_count ?></div>
                <div style="background:#f9f9f9; padding:20px; text-align:center;"><strong>Новые заявки</strong><br><?= $new_requests ?></div>
                <div style="background:#f9f9f9; padding:20px; text-align:center;"><strong>Отзывы на модерации</strong><br><?= $pending_reviews ?></div>
            </div>

            <div class="tabs">
                <button class="tab-btn <?= $tab === 'cars' ? 'active' : '' ?>" data-tab="cars"><i class="fas fa-car"></i> Автомобили</button>
                <button class="tab-btn <?= $tab === 'requests' ? 'active' : '' ?>" data-tab="requests"><i class="fas fa-clipboard-list"></i> Заявки</button>
                <button class="tab-btn <?= $tab === 'reviews' ? 'active' : '' ?>" data-tab="reviews"><i class="fas fa-star"></i> Отзывы</button>
                <button class="tab-btn <?= $tab === 'news' ? 'active' : '' ?>" data-tab="news"><i class="fas fa-newspaper"></i> Новости</button>
            </div>

            <!-- Автомобили -->
            <div id="tab-cars" class="tab-pane <?= $tab === 'cars' ? 'active' : '' ?>">
                <button class="btn" onclick="openCarModal()"><i class="fas fa-plus"></i> Добавить</button>
                <div class="table-wrapper" style="margin-top:20px;">
                    <table>
                        <tr><th>ID</th><th>Бренд</th><th>Модель</th><th>Год</th><th>Цена</th><th>В наличии</th><th></th></tr>
                        <?php while ($car = $cars->fetch_assoc()): ?>
                        <tr>
                            <td><?= $car['id'] ?></td>
                            <td><?= htmlspecialchars($car['brand_name']) ?></td>
                            <td><?= htmlspecialchars($car['model']) ?></td>
                            <td><?= $car['year_from'] ?></td>
                            <td><?= number_format($car['price'], 0, '', ' ') ?> ₽</td>
                            <td><?= $car['is_available'] ? '✅' : '❌' ?></td>
                            <td>
                                <a href="#" onclick='editCar(<?= json_encode($car) ?>); return false;' class="btn-icon"><i class="fas fa-edit"></i></a>
                                <a href="<?= BASE_URL ?>/admin/?action=delete&type=car&id=<?= $car['id'] ?>&tab=cars" onclick="return confirm('Удалить?')" class="btn-delete"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>

            <!-- Заявки (расширенная версия) -->
            <div id="tab-requests" class="tab-pane <?= $tab === 'requests' ? 'active' : '' ?>">
                <div class="table-wrapper">
                    <table style="min-width: 1200px;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Дата</th>
                                <th>Имя</th>
                                <th>Телефон</th>
                                <th>Email</th>
                                <th>Тип</th>
                                <th>Авто</th>
                                <th>Сообщение</th>
                                <th>Статус</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $requests->fetch_assoc()): ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></td>
                                <td><?= htmlspecialchars($r['name']) ?></td>
                                <td><?= htmlspecialchars($r['phone']) ?></td>
                                <td><?= htmlspecialchars($r['email'] ?: '—') ?></td>
                                <td>
                                    <?php
                                    $types = [
                                        'test_drive'=>'Тест-драйв',
                                        'price'=>'Запрос цены',
                                        'callback'=>'Звонок',
                                        'trade_in'=>'Трейд-ин',
                                        'auction'=>'Аукцион',
                                        'logistics'=>'Логистика',
                                        'restoration'=>'Реставрация',
                                        'inspection'=>'Инспекция',
                                        'tuning'=>'Тюнинг',
                                        'finance'=>'Финансы'
                                    ];
                                    echo $types[$r['request_type']] ?? $r['request_type'];
                                    ?>
                                </td>
                                <td>
                                    <?= $r['brand_name'] 
                                        ? htmlspecialchars($r['brand_name'] . ' ' . $r['model']) 
                                        : '—' ?>
                                </td>
                                <td style="max-width: 200px;">
                                    <?php if (!empty($r['message'])): ?>
                                        <?= htmlspecialchars(mb_substr($r['message'], 0, 30)) ?>…
                                        <button type="button" class="btn-icon" 
                                                onclick="showMessageModal('<?= htmlspecialchars(addslashes($r['message'])) ?>')"
                                                title="Показать полностью">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/?action=update_status&tab=requests">
                                        <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                        <select name="status" onchange="this.form.submit()" style="padding: 4px;">
                                            <option value="new" <?= $r['status']=='new'?'selected':'' ?>>Новая</option>
                                            <option value="in_progress" <?= $r['status']=='in_progress'?'selected':'' ?>>В обработке</option>
                                            <option value="done" <?= $r['status']=='done'?'selected':'' ?>>Завершена</option>
                                            <option value="cancelled" <?= $r['status']=='cancelled'?'selected':'' ?>>Отменена</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/?action=delete&type=request&id=<?= $r['id'] ?>&tab=requests" 
                                       onclick="return confirm('Удалить заявку?')" 
                                       class="btn-delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($requests->num_rows === 0): ?>
                                <tr><td colspan="10" style="text-align:center; padding:20px;">Нет заявок</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Отзывы -->
            <div id="tab-reviews" class="tab-pane <?= $tab === 'reviews' ? 'active' : '' ?>">
                <div class="table-wrapper">
                    <table>
                        <tr><th>ID</th><th>Дата</th><th>Автор</th><th>Авто</th><th>Оценка</th><th>Текст</th><th>Статус</th><th></th></tr>
                        <?php while ($rev = $reviews->fetch_assoc()): ?>
                        <tr>
                            <td><?= $rev['id'] ?></td>
                            <td><?= date('d.m.Y', strtotime($rev['created_at'])) ?></td>
                            <td><?= htmlspecialchars($rev['client_name']) ?></td>
                            <td><?= $rev['brand_name'] ? htmlspecialchars($rev['brand_name'].' '.$rev['model']) : '—' ?></td>
                            <td><?= str_repeat('★', $rev['rating']) ?></td>
                            <td><?= htmlspecialchars(mb_substr($rev['text'],0,30)) ?>...</td>
                            <td><?= $rev['is_approved'] ? '✅' : '⏳' ?></td>
                            <td>
                                <?php if (!$rev['is_approved']): ?>
                                    <a href="<?= BASE_URL ?>/admin/?action=approve_review&id=<?= $rev['id'] ?>&tab=reviews" class="btn-icon"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>/admin/?action=delete&type=review&id=<?= $rev['id'] ?>&tab=reviews" onclick="return confirm('Удалить?')" class="btn-delete"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>

            <!-- Новости -->
            <div id="tab-news" class="tab-pane <?= $tab === 'news' ? 'active' : '' ?>">
                <button class="btn" onclick="openNewsModal()"><i class="fas fa-plus"></i> Добавить новость</button>
                <div class="table-wrapper" style="margin-top:20px;">
                    <table>
                        <tr><th>ID</th><th>Заголовок</th><th>Дата</th><th></th></tr>
                        <?php while ($item = $news->fetch_assoc()): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td><?= date('d.m.Y', strtotime($item['created_at'])) ?></td>
                            <td>
                                <a href="#" onclick='editNews(<?= json_encode($item) ?>); return false;' class="btn-icon"><i class="fas fa-edit"></i></a>
                                <a href="<?= BASE_URL ?>/admin/?action=delete&type=news&id=<?= $item['id'] ?>&tab=news" onclick="return confirm('Удалить?')" class="btn-delete"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Модальное окно автомобиля -->
    <div id="carModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeCarModal()">&times;</span>
            <h2 id="carModalTitle">Добавить автомобиль</h2>
            <form method="POST" action="<?= BASE_URL ?>/admin/?action=save_car&tab=cars">
                <input type="hidden" name="id" id="car_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Бренд</label>
                        <select name="brand_id" id="car_brand_id" required>
                            <?php $brands->data_seek(0); while($b = $brands->fetch_assoc()): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Название</label><input type="text" name="name" id="car_name" required></div>
                    <div class="form-group"><label>Модель</label><input type="text" name="model" id="car_model" required></div>
                    <div class="form-group"><label>Поколение</label><input type="text" name="generation" id="car_generation"></div>
                    <div class="form-group"><label>Год</label><input type="number" name="year_from" id="car_year" required></div>
                    <div class="form-group"><label>Цена</label><input type="number" name="price" id="car_price" required></div>
                    <div class="form-group"><label>Двигатель</label><input type="text" name="engine_type" id="car_engine_type"></div>
                    <div class="form-group"><label>Объём (л)</label><input type="number" step="0.1" name="engine_volume" id="car_engine_volume"></div>
                    <div class="form-group"><label>Мощность</label><input type="number" name="engine_power" id="car_engine_power"></div>
                    <div class="form-group"><label>КПП</label><input type="text" name="transmission" id="car_transmission"></div>
                    <div class="form-group"><label>Привод</label><input type="text" name="drive" id="car_drive"></div>
                    <div class="form-group"><label>Топливо</label><input type="text" name="fuel" id="car_fuel" value="Бензин"></div>
                    <div class="form-group"><label>Цвет</label><input type="text" name="color" id="car_color"></div>
                    <div class="form-group"><label>Пробег</label><input type="number" name="mileage" id="car_mileage"></div>
                    <div class="form-group">
                        <label>Состояние</label>
                        <select name="condition" id="car_condition">
                            <option value="Новый">Новый</option>
                            <option value="С пробегом">С пробегом</option>
                            <option value="Реставрирован">Реставрирован</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Badge текст</label><input type="text" name="badge_text" id="car_badge_text"></div>
                    <div class="form-group"><label>Код модели</label><input type="text" name="model_code" id="car_model_code"></div>
                    <div class="form-group"><label>Аукц. оценка</label><input type="text" name="grade_info" id="car_grade_info"></div>
                    <div class="form-group"><label>URL фото</label><input type="text" name="image_url" id="car_image_url"></div>
                    <div class="form-group"><label>ALT фото</label><input type="text" name="image_alt" id="car_image_alt"></div>
                    <div class="form-group"><label><input type="checkbox" name="is_available" id="car_is_available" value="1" checked> В наличии</label></div>
                </div>
                <div class="form-group"><label>Описание</label><textarea name="description" id="car_description" rows="4"></textarea></div>
                <button type="submit" class="btn">Сохранить</button>
            </form>
        </div>
    </div>

    <!-- Модальное окно новости -->
    <div id="newsModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeNewsModal()">&times;</span>
            <h2 id="newsModalTitle">Добавить новость</h2>
            <form method="POST" action="<?= BASE_URL ?>/admin/?action=save_news&tab=news">
                <input type="hidden" name="id" id="news_id">
                <div class="form-group"><label>Заголовок</label><input type="text" name="title" id="news_title" required></div>
                <div class="form-group"><label>Краткий текст</label><textarea name="short_text" id="news_short_text" rows="3" required></textarea></div>
                <div class="form-group"><label>Полный текст</label><textarea name="full_text" id="news_full_text" rows="6" required></textarea></div>
                <div class="form-group"><label>URL изображения</label><input type="text" name="image_url" id="news_image_url"></div>
                <button type="submit" class="btn">Сохранить</button>
            </form>
        </div>
    </div>

    <!-- Модальное окно для просмотра сообщения заявки -->
    <div id="messageModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close-modal" onclick="closeMessageModal()">&times;</span>
            <h3>Сообщение заявки</h3>
            <p id="messageModalText" style="white-space: pre-wrap; word-break: break-word;"></p>
        </div>
    </div>

    <footer class="admin-footer">
        <div class="admin-container">© 2026 Shift & Drift · Административная панель</div>
    </footer>

    <script>
        // Переключение вкладок
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('tab-' + tab).classList.add('active');
                history.pushState(null, '', '?tab=' + tab);
            });
        });

        const carModal = document.getElementById('carModal');
        const newsModal = document.getElementById('newsModal');
        const messageModal = document.getElementById('messageModal');
        const messageModalText = document.getElementById('messageModalText');

        window.openCarModal = function() {
            document.getElementById('carModalTitle').innerText = 'Добавить автомобиль';
            document.getElementById('car_id').value = '';
            carModal.querySelectorAll('input:not([type=checkbox]), select, textarea').forEach(el => el.value = '');
            document.getElementById('car_is_available').checked = true;
            carModal.style.display = 'block';
        };

        window.editCar = function(car) {
            document.getElementById('carModalTitle').innerText = 'Редактировать автомобиль';
            document.getElementById('car_id').value = car.id;
            document.getElementById('car_brand_id').value = car.brand_id;
            document.getElementById('car_name').value = car.name || '';
            document.getElementById('car_model').value = car.model;
            document.getElementById('car_generation').value = car.generation || '';
            document.getElementById('car_year').value = car.year_from;
            document.getElementById('car_price').value = car.price;
            document.getElementById('car_engine_type').value = car.engine_type || '';
            document.getElementById('car_engine_volume').value = car.engine_volume || '';
            document.getElementById('car_engine_power').value = car.engine_power || '';
            document.getElementById('car_transmission').value = car.transmission || '';
            document.getElementById('car_drive').value = car.drive || '';
            document.getElementById('car_fuel').value = car.fuel || 'Бензин';
            document.getElementById('car_color').value = car.color || '';
            document.getElementById('car_mileage').value = car.mileage || '';
            document.getElementById('car_condition').value = car.condition;
            document.getElementById('car_badge_text').value = car.badge_text || '';
            document.getElementById('car_model_code').value = car.model_code || '';
            document.getElementById('car_grade_info').value = car.grade_info || '';
            document.getElementById('car_image_url').value = car.image_url || '';
            document.getElementById('car_image_alt').value = car.image_alt || '';
            document.getElementById('car_description').value = car.description || '';
            document.getElementById('car_is_available').checked = car.is_available == 1;
            carModal.style.display = 'block';
        };

        window.closeCarModal = function() { carModal.style.display = 'none'; };

        window.openNewsModal = function() {
            document.getElementById('newsModalTitle').innerText = 'Добавить новость';
            document.getElementById('news_id').value = '';
            newsModal.querySelectorAll('input, textarea').forEach(el => el.value = '');
            newsModal.style.display = 'block';
        };

        window.editNews = function(item) {
            document.getElementById('newsModalTitle').innerText = 'Редактировать новость';
            document.getElementById('news_id').value = item.id;
            document.getElementById('news_title').value = item.title;
            document.getElementById('news_short_text').value = item.short_text;
            document.getElementById('news_full_text').value = item.full_text;
            document.getElementById('news_image_url').value = item.image_url || '';
            newsModal.style.display = 'block';
        };

        window.closeNewsModal = function() { newsModal.style.display = 'none'; };

        window.showMessageModal = function(message) {
            messageModalText.textContent = message;
            messageModal.style.display = 'block';
        };

        window.closeMessageModal = function() {
            messageModal.style.display = 'none';
        };

        window.onclick = function(e) {
            if (e.target === carModal) carModal.style.display = 'none';
            if (e.target === newsModal) newsModal.style.display = 'none';
            if (e.target === messageModal) messageModal.style.display = 'none';
        };
    </script>
</body>
</html>