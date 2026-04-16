<?php
// Включаем отображение ошибок 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
require_once 'config/session.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = isset($_POST['car_id']) ? intval($_POST['car_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $user_id = Session::getUserId();
    
    if ($car_id > 0 && $rating >= 1 && $rating <= 5 && !empty($text)) {
        $db = Database::getInstance()->getConnection();
        
        // Получаем имя пользователя
        $userStmt = $db->prepare("SELECT full_name, username FROM users WHERE id = ?");
        $userStmt->bind_param("i", $user_id);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $user = $userResult->fetch_assoc();
        
        $client_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];
        
        $stmt = $db->prepare("INSERT INTO reviews (client_name, car_id, rating, text, is_approved) VALUES (?, ?, ?, ?, FALSE)");
        $stmt->bind_param("siis", $client_name, $car_id, $rating, $text);
        $stmt->execute();
        
        // Редирект обратно на страницу автомобиля с уведомлением 
        header('Location: car.php?id=' . $car_id . '&review_pending=1');
        exit;
    }
}

header('Location: catalog.php');
exit;
