<?php
// 1. تشغيل الجلسة أول حاجة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. استدعاء ملف الاتصال
include 'db.php'; 

// 3. لو مسجل دخول ابعته لمكانه فوراً
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? "admin.php" : "index.php"));
    exit;
}

$error = "";

// 4. دالة مساعدة لتقليل تكرار الكود (لازم تكون فوق قبل الاستدعاء)
function loginSuccess($data, $role) {
    $_SESSION['logged_in'] = true;
    $_SESSION['role'] = $role;
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['user_name'] = ($role === 'admin') ? $data['name'] : $data['full_name']; 
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['last_activity'] = time();
    header("Location: " . ($role === 'admin' ? "admin.php" : "index.php"));
    exit;
}

// 5. معالجة بيانات الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $password = $_POST['password'];

    // --- [ نظام الدخول السريع للادمن ] ---
    if ($phone === '1' && $password === '1') {
        $_SESSION['temp_admin_step'] = true; 
        header("Location: verify_admin.php");
        exit;
    }

    // --- [ الدخول الطبيعي ] ---
    if (strlen($phone) === 11) {
        // فحص الأدمن
        $admin_query = mysqli_query($conn, "SELECT * FROM admins WHERE phone = '$phone' LIMIT 1");
        if ($admin_query && mysqli_num_rows($admin_query) > 0) {
            $admin = mysqli_fetch_assoc($admin_query);
            if (password_verify($password, $admin['password'])) {
                loginSuccess($admin, 'admin');
            }
        }

        // فحص الزبائن
        $user_query = mysqli_query($conn, "SELECT * FROM customers WHERE phone_number = '$phone' LIMIT 1");
        if ($user_query && mysqli_num_rows($user_query) > 0) {
            $user = mysqli_fetch_assoc($user_query);
            if (password_verify($password, $user['password'])) {
                loginSuccess($user, 'customer');
            }
        }
        $error = "بيانات الدخول غير صحيحة يا فنان ⚠️";
    } else {
        $error = "رقم الموبايل لازم يكون 11 خانة للدخول العادي 📱";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | متجر الفخامة ✨</title>
    <style>
        :root { --gold: #D4AF37; --black: #0a0a0a; --gray: #151515; }
        body { background: var(--black); font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; color: white; }
        .login-card { background: var(--gray); padding: 40px; border-radius: 25px; border: 1px solid rgba(212, 175, 55, 0.2); box-shadow: 0 20px 50px rgba(0,0,0,0.7); width: 90%; max-width: 400px; text-align: center; }
        .logo { width: 80px; margin-bottom: 20px; filter: drop-shadow(0 0 10px var(--gold)); }
        h2 { color: var(--gold); margin: 0 0 10px; }
        input { width: 100%; padding: 15px; margin-bottom: 20px; background: #222; border: 1px solid #333; border-radius: 12px; color: white; box-sizing: border-box; outline: none; text-align: center; font-size: 1rem; }
        input:focus { border-color: var(--gold); }
        .btn-submit { width: 100%; padding: 15px; background: linear-gradient(45deg, var(--gold), #f2d06b); border: none; border-radius: 12px; font-weight: bold; cursor: pointer; font-size: 1.1rem; color: black; }
        .error-box { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; padding: 12px; border-radius: 10px; margin-bottom: 20px; border: 1px solid rgba(255, 77, 77, 0.2); font-size: 0.9rem; }
        .footer-links { margin-top: 25px; font-size: 0.85rem; color: #666; }
        .footer-links a { color: var(--gold); text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="logo.png" alt="Logo" class="logo">
        <h2>مرحباً بك ✨</h2>
        <?php if($error): ?> <div class="error-box"><?php echo $error; ?></div> <?php endif; ?>
        <form method="POST">
            <input type="text" name="phone" placeholder="رقم الموبايل" required autocomplete="off">
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit" name="login" class="btn-submit">دخول آمن 🔓</button>
        </form>
        <div class="footer-links">ليس لديك حساب؟ <a href="signup.php">سجل الآن</a></div>
    </div>
</body>
</html>