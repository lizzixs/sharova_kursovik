<?php
require_once 'config/database.php';
require_once 'config/session.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$user_id = Session::getUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $check = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $user_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        // Ошибка (в production нужно передать в сессию)
        header('Location: profile.php?error=email_exists');
        exit;
    }
    
    if (!empty($password)) {
        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, password_hash = ? WHERE id = ?");
        $stmt->bind_param("sssi", $full_name, $email, $password, $user_id);
    } else {
        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $full_name, $email, $user_id);
    }
    $stmt->execute();
    Session::set('user_name', $full_name ?: Session::get('user_name'));
    header('Location: profile.php?updated=1');
    exit;
}

header('Location: profile.php');
exit;