<?php
// admin/includes/super_admin_middleware.php
// حارس الصفحات الحساسة: يقبل فقط super_admin ويطرد أي شخص آخر

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in']) || ($_SESSION['role'] ?? '') !== 'super_admin') {
    // تسجيل محاولة الاختراق (سايلنت)
    error_log('[AhmedKoshary SECURITY] Unauthorized access attempt to restricted page by: ' . ($_SESSION['username'] ?? 'unknown'));
    // إعادة توجيه لصفحة اللوحة الرئيسية مع رسالة خطأ
    header("Location: index.php?err=unauthorized");
    exit();
}
?>
