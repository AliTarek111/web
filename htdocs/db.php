<?php
// إعدادات قاعدة البيانات الفعلية - متجر أحمد كشري (نسخة السيرفر)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// نظام الأمان الذكي: فحص الجلسة (Session Security)
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // 1. فحص اختطاف الجلسة (IP & User Agent Hijacking)
    if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_unset();
        session_destroy();
        header("Location: login.php?error=sec_breach");
        exit;
    }

    // 2. فحص الخروج التلقائي (Session Timeout - ساعتين)
    $timeout_duration = 7200; // 2 hours
    if (time() - $_SESSION['last_activity'] > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?error=timeout");
        exit;
    }

    // 3. تحديث وقت النشاط الأخير
    $_SESSION['last_activity'] = time();
}

// ملحوظة: البيانات دي لازم تجيبها من لوحة تحكم InfinityFree (MySQL Details)

$host = "sql203.infinityfree.com"; // 👈 غير ده للهوست اللي في لوحة تحكمك (مثلاً sql301)
$user = "if0_41433854";            // 👈 ده اليوزر نيم بتاعك (موجود في الـ Client Area)
$pass = "ali012212431";
$dbname = "if0_41433854_store"; // 👈 اسم القاعدة اللي أنشأتها (لازم يبدأ بـ if0_)

// محاولة الاتصال بالسيرفر
$conn = mysqli_connect($host, $user, $pass, $dbname);

// فحص الاتصال "على نضيف"
if (!$conn) {
    die("<div style='color:#ff4d4d; background:#1a1a1a; padding:20px; text-align:center; border:1px solid #ff4d4d; border-radius:10px; margin:50px auto; max-width:500px; font-family:sans-serif;'>
            <h3>عفواً يا هندسة! فيه مشكلة في الربط 🛠️</h3>
            <p>" . mysqli_connect_error() . "</p>
            <small>تأكد من بيانات MySQL في لوحة تحكم الاستضافة</small>
         </div>");
}

// ضبط الترميز عشان العربي يظهر صح 100%
mysqli_set_charset($conn, "utf8mb4");

// كدة القاعدة جاهزة للشغل يا علي!
?>