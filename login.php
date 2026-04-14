<?php
// login.php
// Ahmed Koshary Store - Unified Professional Login
session_start();
include 'includes/db.php';

$error = "";

if (isset($_SESSION['admin_logged_in'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: profile.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Check users table for both users and admins
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $u = $stmt->fetch();

    if ($u && password_verify($pass, $u['password'])) {
        // Set session variables based on role
        if ($u['role'] === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $u['id'];
            $_SESSION['db_offline'] = false;
        } else {
            $_SESSION['admin_logged_in'] = false;
        }
        
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['username'] = $u['username'];
        $_SESSION['role'] = $u['role'];
        
        if ($u['role'] === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: profile.php");
        }
        exit();
    } else {
        $error = "بيانات الدخول غير صحيحة. يرجى التأكد من اسم المستخدم وكلمة المرور.";
    }
}
?>
<!DOCTYPE html>
<html class="dark" dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>تسجيل الدخول | أحمد كشري</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&family=Cairo:wght@400;700;900&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0d111a; color: #dfe2f1; overflow: hidden; }
        .gold-gradient { background: linear-gradient(135deg, #f2ca50 0%, #d4af37 100%); }
        .glass-card { background: rgba(19, 19, 19, 0.4); backdrop-filter: blur(40px); border: 1px solid rgba(242, 202, 80, 0.05); }
        .floating-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .circle { position: absolute; border-radius: 50%; background: linear-gradient(135deg, rgba(242, 202, 80, 0.03) 0%, transparent 100%); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative selection:bg-primary selection:text-on-primary">

    <div class="floating-bg">
        <div class="circle w-[600px] h-[600px] -top-40 -left-20 blur-3xl animate-pulse"></div>
        <div class="circle w-[400px] h-[400px] bottom-0 right-0 blur-3xl opacity-30"></div>
    </div>

    <main class="w-full max-w-lg p-12 md:p-20 glass-card rounded-[3.5rem] shadow-[0_50px_100px_rgba(0,0,0,0.6)] border border-white/5 text-center relative overflow-hidden">
        <div class="mb-12 flex flex-col items-center gap-6">
            <a href="index.php"><img src="logo.png/screen.png" alt="Logo" class="w-20 h-20 object-contain drop-shadow-[0_0_20px_rgba(242,202,80,0.3)] hover:scale-110 transition-transform"/></a>
            <div class="text-center">
                <h1 class="text-4xl font-black text-primary tracking-tighter uppercase font-headline">User Login</h1>
                <p class="text-on-surface-variant/40 text-[9px] uppercase font-bold tracking-[0.4em]">Ahmed Koshary Professional Access</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="mb-10 p-5 bg-error/10 border border-error/20 text-error text-[10px] font-black uppercase tracking-widest rounded-2xl">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-8">
            <div class="space-y-2 text-right">
                <label class="text-[9px] font-black uppercase tracking-[0.3em] text-on-surface-variant/40 pr-4">اسم المستخدم</label>
                <input required name="username" type="text" placeholder="أدخل اسم المستخدم" class="w-full bg-[#0a0e18]/60 border-white/5 rounded-2xl focus:border-primary text-sm p-5 text-center transition-all"/>
            </div>
            <div class="space-y-2 text-right">
                <label class="text-[9px] font-black uppercase tracking-[0.3em] text-on-surface-variant/40 pr-4">كلمة المرور</label>
                <input required name="password" type="password" placeholder="••••••••" class="w-full bg-[#0a0e18]/60 border-white/5 rounded-2xl focus:border-primary text-sm p-5 text-center transition-all"/>
            </div>

            <div class="pt-8">
                <button type="submit" class="w-full py-6 gold-gradient text-on-primary font-headline font-black text-xs uppercase rounded-2xl shadow-2xl hover:scale-[1.03] active:scale-95 transition-all flex items-center justify-center gap-4 group">
                    <span class="material-symbols-outlined group-hover:translate-x-2 transition-transform" style="font-variation-settings: 'FILL' 1;">login</span>
                    دخول الحساب
                </button>
            </div>
        </form>
        
        <div class="mt-12 pt-8 border-t border-white/5 flex flex-col gap-4">
            <p class="text-[9px] text-on-surface-variant/20 uppercase tracking-[0.3em] font-black italic">ليس لديك حساب؟</p>
            <a href="signup.php" class="text-[10px] text-primary hover:underline uppercase tracking-[0.2em] font-black">سجل حساباً جديداً الآن</a>
            <a href="index.php" class="mt-4 text-[9px] text-on-surface-variant/40 hover:text-primary transition-colors uppercase tracking-[0.3em] font-black flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                الرجوع للرئيسية
            </a>
        </div>
    </main>

</body>
</html>
