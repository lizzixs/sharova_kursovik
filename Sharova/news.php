<?php
$pageTitle = 'Новости · Shift & Drift';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = Database::getInstance()->getConnection();
$result = $db->query("SELECT * FROM news ORDER BY created_at DESC");
?>

<div class="container" style="margin: 40px auto;">
    <h1 style="font-size: 2.5rem; margin-bottom: 30px;">Новости</h1>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 40px;">
            <?php while ($news = $result->fetch_assoc()): ?>
                <div style="display: grid; grid-template-columns: 250px 1fr; gap: 30px; border-bottom: 1px solid #ececec; padding-bottom: 40px;">
                    <div style="background: #f6f6f6; min-height: 150px; background-image: url('<?= htmlspecialchars($news['image_url'] ?: 'https://placehold.co/600x400/f5f5f5/b33b4a?text=News') ?>'); background-size: cover; background-position: center;"></div>
                    <div>
                        <span style="color: #b33b4a; font-size: 0.9rem; font-weight: 500;"><?= date('d.m.Y', strtotime($news['created_at'])) ?></span>
                        <h2 style="font-size: 1.8rem; margin: 10px 0 15px;">
                            <a href="news_detail.php?id=<?= $news['id'] ?>" style="text-decoration: none; color: #1e1e1e;"><?= htmlspecialchars($news['title']) ?></a>
                        </h2>
                        <p style="color: #4a4a4a; line-height: 1.6;"><?= nl2br(htmlspecialchars($news['short_text'])) ?></p>
                        <a href="news_detail.php?id=<?= $news['id'] ?>" style="display: inline-block; margin-top: 15px; color: #b33b4a; font-weight: 500; text-decoration: none;">Читать далее →</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Новостей пока нет.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>