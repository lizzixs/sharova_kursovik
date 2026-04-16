<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class Session {
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }
    public static function delete($key) {
        unset($_SESSION[$key]);
    }
    public static function destroy() {
        session_destroy();
    }
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    public static function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    public static function getUserName() {
        return $_SESSION['user_name'] ?? 'Гость';
    }
}
?>