<?php
require_once 'config/database.php';
require_once 'config/session.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;
$action = $_GET['action'] ?? 'add';
$return = $_GET['return'] ?? 'car'; 

if ($car_id <= 0) {
    header('Location: catalog.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$user_id = Session::getUserId();

// Проверка существования авто
$check = $db->prepare("SELECT id FROM cars WHERE id = ?");
$check->bind_param("i", $car_id);
$check->execute();
if ($check->get_result()->num_rows == 0) {
    header('Location: catalog.php');
    exit;
}

if ($action == 'add') {
    $stmt = $db->prepare("INSERT IGNORE INTO favorites (user_id, car_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $car_id);
    $stmt->execute();
} else {
    $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND car_id = ?");
    $stmt->bind_param("ii", $user_id, $car_id);
    $stmt->execute();
}

if ($return === 'profile') {
    header('Location: profile.php#favorites');
} else {
    header('Location: car.php?id=' . $car_id);
}
exit;
