<?php
$pageTitle = 'Каталог JDM · Shift & Drift';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = Database::getInstance()->getConnection();

$brand_id = $_GET['brand'] ?? '';
$drive = $_GET['drive'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT c.*, b.name AS brand_name 
        FROM cars c 
        JOIN brands b ON c.brand_id = b.id 
        WHERE c.is_available = TRUE";
$params = [];
$types = "";

if (!empty($brand_id) && is_numeric($brand_id)) {
    $sql .= " AND c.brand_id = ?";
    $params[] = (int)$brand_id;
    $types .= "i";
}
if (!empty($drive)) {
    $sql .= " AND c.drive = ?";
    $params[] = $drive;
    $types .= "s";
}
if (!empty($search)) {
    $sql .= " AND (c.model LIKE ? OR c.description LIKE ? OR b.name LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}
$sql .= " ORDER BY c.created_at DESC";

$stmt = $db->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$brands = $db->query("SELECT * FROM brands ORDER BY name");
?>

<div class="container">
    <h1 style="font-size: 2.5rem; margin: 40px 0 20px;">Каталог</h1>
    
    <div class="quick-filter" style="margin-top: 0;">
        <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Поиск по названию..." value="<?= htmlspecialchars($search) ?>" 
                   style="flex: 2; min-width: 250px; padding: 12px; border: 1px solid #ddd;">
            <select name="brand" style="flex: 1; min-width: 150px; padding: 12px; border: 1px solid #ddd;">
                <option value="">Все бренды</option>
                <?php while($b = $brands->fetch_assoc()): ?>
                    <option value="<?= $b['id'] ?>" <?= $brand_id == $b['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select name="drive" style="flex: 1; min-width: 150px; padding: 12px; border: 1px solid #ddd;">
                <option value="">Любой привод</option>
                <option value="4WD" <?= $drive == '4WD' ? 'selected' : '' ?>>4WD</option>
                <option value="RWD" <?= $drive == 'RWD' ? 'selected' : '' ?>>RWD</option>
                <option value="FWD" <?= $drive == 'FWD' ? 'selected' : '' ?>>FWD</option>
                <option value="AWD" <?= $drive == 'AWD' ? 'selected' : '' ?>>AWD</option>
            </select>
            <button type="submit" class="btn-primary" style="padding: 12px 30px;">Применить</button>
        </form>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; margin: 40px 0;">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($car = $result->fetch_assoc()): ?>
                <div class="car-card">
                    <img src="<?= htmlspecialchars($car['image_url'] ?: 'https://placehold.co/600x400/f5f5f5/b33b4a?text=No+Image') ?>" 
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
            <p style="grid-column: 1/-1; text-align: center; padding: 50px;">Автомобили не найдены</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>