<?php
require_once 'config/database.php';
require_once 'config/session.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Заполните обязательные поля';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        $db = Database::getInstance()->getConnection();
        
        $check = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Пользователь с таким email или именем уже существует';
        } else {
            // Сохраняем пароль как есть
            $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, full_name, is_admin) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("ssss", $username, $email, $password, $full_name);
            if ($stmt->execute()) {
                $success = 'Регистрация успешна! Теперь вы можете войти.';
            } else {
                $error = 'Ошибка при регистрации';
            }
        }
    }
}

$pageTitle = 'Регистрация · Shift & Drift';
require_once 'includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 60px auto;">
    <div class="auth-form">
        <h1 style="font-size: 2rem; margin-bottom: 30px; text-align: center;">Регистрация</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label>Имя пользователя *</label>
                <input type="text" name="username" required style="width: 100%; padding: 12px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Полное имя</label>
                <input type="text" name="full_name" style="width: 100%; padding: 12px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Email *</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Пароль *</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Подтверждение пароля *</label>
                <input type="password" name="confirm_password" required style="width: 100%; padding: 12px;">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Зарегистрироваться</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            Уже есть аккаунт? <a href="login.php" style="color: #b33b4a;">Войти</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>