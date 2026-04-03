<?php
session_start();
include 'db.php';

// 1. التأكد إنك جاي من صفحة اللوجن (كاتب 1 و 1)
if (!isset($_SESSION['temp_admin_step'])) {
    header("Location: login.php");
    exit;
}

$error = "";

if (isset($_POST['verify'])) {
    $full_code = mysqli_real_escape_string($conn, trim($_POST['full_code']));
    
    // 2. البحث عن الأدمن (هندور بالباسورد أو التليفون)
    $query = mysqli_query($conn, "SELECT * FROM admins LIMIT 1"); // هنجيب أول أدمن مسجل
    
    if ($query && mysqli_num_rows($query) > 0) {
        $admin = mysqli_fetch_assoc($query);
        
        // 3. فحص الكود (هل هو 1911؟)
        $is_valid = false;
        if (password_verify($full_code, $admin['password'])) {
            $is_valid = true; // لو متخزن هاش
        } elseif ($full_code === $admin['password'] || $full_code === '1911') {
            $is_valid = true; // لو متخزن نص عادي أو كود الطوارئ
        }

        if ($is_valid) {
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = 'admin';
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['name'];
            $_SESSION['last_activity'] = time();
            
            // تأمين الجلسة عشان م يخرجكش تاني
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            
            unset($_SESSION['temp_admin_step']); // مسح الخطوة المؤقتة
            header("Location: admin.php"); // الذهاب للوحة التحكم
            exit;
        } else {
            $error = "الكود ($full_code) مش مطابق للي في القاعدة يا فنان ❌";
        }
    } else {
        $error = "مفيش أدمن مسجل في القاعدة أصلاً! ⚠️";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تأكيد الهوية | الخزنة الملكية 🛡️</title>
    <style>
        body { background: #0a0a0a; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #151515; padding: 40px; border-radius: 20px; border: 1px solid #D4AF37; text-align: center; width: 90%; max-width: 400px; }
        input { width: 100%; padding: 15px; margin: 20px 0; background: #222; border: 1px solid #333; color: white; border-radius: 10px; text-align: center; font-size: 1.2rem; }
        button { background: #D4AF37; color: black; border: none; padding: 15px; border-radius: 10px; font-weight: bold; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="color:#D4AF37">الخطوة الأخيرة 🛡️</h2>
        <?php if($error) echo "<p style='color:#ff4d4d'>$error</p>"; ?>
        <form method="POST">
            <input type="password" name="full_code" placeholder="أدخل كود الـ 11 خانة" required>
            <button type="submit" name="verify">فتح لوحة التحكم 🔓</button>
        </form>
    </div>
</body>
</html>