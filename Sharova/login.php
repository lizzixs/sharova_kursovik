<?php
require_once 'config/database.php';
require_once 'config/session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT id, username, full_name, password_hash, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            // Прямое сравнение пароля
            if ($password === $user['password_hash']) {
                Session::set('user_id', $user['id']);
                Session::set('user_name', $user['full_name'] ?: $user['username']);
                Session::set('is_admin', (bool)$user['is_admin']);
                
                if ($user['is_admin']) {
                    header('Location: admin/');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error = 'Неверный пароль';
            }
        } else {
            $error = 'Пользователь с таким email не найден';
        }
    }
}

$pageTitle = 'Вход · Shift & Drift';
require_once 'includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 60px auto;">
    <div class="auth-form">
        <h1 style="font-size: 2rem; margin-bottom: 30px; text-align: center;">Вход в систему</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="background: #fee; color: #c00; padding: 12px; margin-bottom: 20px; border: 1px solid #fcc;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label>Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px;" value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Пароль</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px;">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Войти</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Нет аккаунта? <a href="register.php" style="color: #b33b4a;">Зарегистрироваться</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>