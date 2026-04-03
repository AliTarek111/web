<?php
session_start();
include 'db.php';

// لو العميل مسجل دخول أصلاً، حوّله لصفحة حسابه
if (isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if (isset($_POST['signup'])) {
    // 1. نظام منع السبام (الانتظار 15 دقيقة)
    $current_time = time();
    $wait_time = 15 * 60; // 900 ثانية

    if (isset($_SESSION['last_signup_attempt'])) {
        $time_passed = $current_time - $_SESSION['last_signup_attempt'];
        if ($time_passed < $wait_time) {
            $remaining = ceil(($wait_time - $time_passed) / 60);
            $error = "عفواً يا هندسة! أمنع السبام.. انتظر $remaining دقيقة قبل المحاولة مرة أخرى ⏳";
        }
    }

    // 2. فحص فخ البوتات (Honeypot)
    if (!empty($_POST['robot_check'])) {
        die("Spam Detected!"); // لو حقل مخفي اتملى يبقى بوت
    }

    if (empty($error)) {
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $error = "كلمتا المرور غير متطابقتين!";
        } else {
            $check_phone = mysqli_query($conn, "SELECT id FROM customers WHERE BINARY phone_number = '$phone'");
            if (mysqli_num_rows($check_phone) > 0) {
                $error = "هذا الرقم مسجل لدينا بالفعل، جرب تسجيل الدخول.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO customers (full_name, phone_number, address, password) 
                        VALUES ('$full_name', '$phone', '$address', '$hashed_password')";
                
                if (mysqli_query($conn, $sql)) {
                    // تسجيل وقت المحاولة الناجحة لمنع التكرار فوراً
                    $_SESSION['last_signup_attempt'] = time();
                    
                    $success = "تم إنشاء حسابك الملكي بنجاح! جاري تحويلك...";
                    echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
                } else {
                    $error = "حدث خطأ أثناء التسجيل، حاول مرة أخرى.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انضم لمتجرنا | متجر أحمد كشري ✨</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container" style="animation: fadeInUp 0.8s ease;">
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="logo.png" alt="Logo" style="height: 80px; filter: drop-shadow(0 0 10px var(--gold));">
        <h2 style="color: var(--gold); margin-top: 15px; font-size: 2rem;">إنشاء حساب جديد</h2>
        <p style="color: var(--text-muted);">كن جزءاً من عملائنا المميزين</p>
    </div>

    <?php if($error): ?>
        <div style="background: rgba(212, 175, 55, 0.1); color: #D4AF37; padding: 15px; border-radius: 12px; border: 1px dotted #D4AF37; margin-bottom: 20px; text-align: center;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div style="background: rgba(46, 213, 115, 0.1); color: #2ed573; padding: 15px; border-radius: 12px; border: 1px solid rgba(46, 213, 115, 0.3); margin-bottom: 20px; text-align: center;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="robot_check" style="display:none;">

        <input type="text" name="full_name" placeholder="الاسم بالكامل" required>
        <input type="text" name="phone" placeholder="رقم الموبايل" required>
        <textarea name="address" placeholder="العنوان بالتفصيل" rows="2" required style="width:100%; border-radius:15px; padding:15px; background:rgba(0,0,0,0.3); color:white; border:1px solid rgba(255,255,255,0.05); margin-bottom:15px;"></textarea>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required>
        
        <button type="submit" name="signup" class="cta-gold" style="width: 100%; margin-top: 10px; font-size: 1.1rem;">
            ابدأ رحلتك الآن ✨
        </button>
    </form>

    <div style="text-align: center; margin-top: 30px;">
        <span style="color: var(--text-muted);">لديك حساب بالفعل؟</span>
        <a href="login.php" style="color: var(--gold); text-decoration: none; font-weight: 800; margin-right: 5px;">تسجيل الدخول</a>
    </div>
</div>

</body>
</html>