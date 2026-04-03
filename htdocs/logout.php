<?php
// ملف الخروج الآمن - متجر أحمد كشري
session_start();

// 1. مسح مصفوفة السيشن بالكامل
$_SESSION = array();

// 2. تدمير الكوكيز الخاصة بالسيشن لو موجودة
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. تدمير السيشن من السيرفر
session_destroy();

// 4. التحويل لصفحة الدخول
header("Location: login.php");
exit;
?>