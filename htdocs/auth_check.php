<?php
// ملف حماية الجلسات - متجر أحمد كشري

function checkRole($allowed_role) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== $allowed_role) {
        header("Location: login.php");
        exit;
    }
}
?>
