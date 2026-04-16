<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: news.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM news WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: news.php');
    exit;
}

$news = $result->fetch_assoc();
$pageTitle = $news['title'] . ' · Shift & Drift';
?>

<div class="container" style="margin: 40px auto; max-width: 900px;">
    <a href="news.php" style="display: inline-block; margin-bottom: 30px; color: #b33b4a; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Все новости
    </a>
    <p>
    <span style="color: #b33b4a; font-weight: 500;"><?= date('d.m.Y', strtotime($news['created_at'])) ?></span>
    <h1 style="font-size: 2.5rem; margin: 15px 0 30px;"><?= htmlspecialchars($news['title']) ?></h1>
    
    <?php if (!empty($news['image_url'])): ?>
        <div style="width: 100%; height: 400px; background: #f6f6f6; margin-bottom: 30px; background-image: url('<?= htmlspecialchars($news['image_url']) ?>'); background-size: cover; background-position: center;"></div>
    <?php endif; ?>
    
    <div style="font-size: 1.1rem; line-height: 1.8; color: #2e2e2e;">
        <?= nl2br(htmlspecialchars($news['full_text'])) ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>