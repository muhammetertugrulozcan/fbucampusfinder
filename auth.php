<?php
// Oturum başlatılmamışsa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kullanıcının admin olup olmadığını kontrol eder.
 */
function isAdmin() {
    // Session'da role var mı ve bu role 'admin' mi?
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        return true;
    }
    return false;
}

/**
 * Giriş yapmamış kullanıcıyı login sayfasına atar.
 */
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>