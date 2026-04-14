<?php
// admin/login.php
// Ahmed Koshary Store - Admin Secure Entrance (with DB Fallback)
session_start();
include '../includes/db.php';
require_once 'includes/activity_logger.php';

$error = "";

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    // ─── Case 1: DB is available — authenticate normally ───────────────────────
    if (!$db_connection_failed && $pdo !== null) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role IN ('super_admin', 'admin')");
        $stmt->execute([$user]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($pass, $admin['password'])) {
            // Check if account is disabled
            if (isset($admin['is_active']) && !$admin['is_active']) {
                $error = "حسابك معطّل حالياً. تواصل مع الأدمن الرئيسي لإعادة تفعيله.";
            } else {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = $admin['id'];
                $_SESSION['username']        = $admin['username'];
                $_SESSION['role']            = $admin['role'];
                $_SESSION['full_name']       = $admin['full_name'];
                $_SESSION['db_offline']      = false;
                log_activity($pdo, 'login', "تسجيل دخول: {$admin['full_name']} (@{$admin['username']})", $admin['id']);
                header("Location: index.php");
                exit();
            }
        } else {
            $error = "إذن الدخول مرفوض. الصلاحيات غير كافية للوصول إلى النظام.";
        }

    // ─── Case 2: DB offline — use hardcoded emergency credentials ──────────────
    } else {
        if ($user === 'admin' && $pass === '123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = 0;
            $_SESSION['username']        = 'admin';
            $_SESSION['role']            = 'admin';
            $_SESSION['db_offline']      = true; // flag for offline mode
            header("Location: index.php");
            exit();
        } else {
            $error = "فشل الاتصال بقاعدة البيانات. يمكنك الدخول بحساب الطوارئ: admin / 123";
        }
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: { extend: { colors: { primary: "#f2ca50", "on-primary": "#3c2f00", error: "#ff5449" }, fontFamily: { headline: ["Cairo"] } } }
        };
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0a0e18; color: #dfe2f1; overflow: hidden; }
        .gold-gradient { background: linear-gradient(135deg, #f2ca50 0%, #d4af37 100%); }
        .login-card { background: rgba(19, 19, 19, 0.6); backdrop-filter: blur(40px); border: 1px solid rgba(242, 202, 80, 0.1); }
        .floating-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .circle { position: absolute; border-radius: 50%; background: linear-gradient(135deg, rgba(242, 202, 80, 0.05) 0%, transparent 100%); }
        .offline-banner { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative">

    <div class="floating-bg">
        <div class="circle w-[500px] h-[500px] -top-20 -right-20 blur-3xl animate-pulse"></div>
        <div class="circle w-[300px] h-[300px] bottom-0 left-0 blur-3xl opacity-50"></div>
    </div>

    <main class="w-full max-w-lg p-10 md:p-16 login-card rounded-[3rem] shadow-[0_50px_100px_rgba(0,0,0,0.8)] border border-white/5 text-center relative overflow-hidden transition-all hover:border-primary/20">
        <div class="absolute top-0 left-0 w-full h-1 gold-gradient opacity-50"></div>

        <?php if ($db_connection_failed): ?>
        <div class="mb-6 p-3 bg-orange-500/10 border border-orange-500/30 rounded-2xl flex items-center gap-3">
            <span class="material-symbols-outlined text-orange-400 text-sm">wifi_off</span>
            <p class="text-orange-400 text-[9px] font-black uppercase tracking-widest">وضع الطوارئ — قاعدة البيانات غير متاحة</p>
        </div>
        <?php endif; ?>
        
        <div class="mb-14 flex flex-col items-center gap-6">
            <img src="../logo.png/screen.png" alt="Ahmed Koshary Logo" class="w-20 h-20 object-contain drop-shadow-[0_0_20px_rgba(242,202,80,0.4)]"/>
            <div class="text-center">
                <h1 class="text-4xl font-black text-primary tracking-tighter uppercase font-headline">Admin Dashboard</h1>
                <p class="text-white/20 text-[9px] uppercase font-bold tracking-[0.4em]">Ahmed Koshary Professional Access</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="mb-10 p-5 bg-red-500/10 border border-red-500/20 text-red-400 text-[10px] font-black uppercase tracking-widest rounded-2xl">
                <?php echo $error; ?>
            </div>
        <?php elseif (isset($_GET['err']) && $_GET['err'] === 'disabled'): ?>
            <div class="mb-10 p-5 bg-orange-500/10 border border-orange-500/20 rounded-2xl flex flex-col items-center gap-3">
                <span class="material-symbols-outlined text-orange-400 text-3xl">block</span>
                <p class="text-orange-400 text-xs font-black uppercase tracking-widest">تم تعطيل حسابك</p>
                <p class="text-orange-300/60 text-[10px] font-bold">ارجع للأدمن الرئيسي لإعادة تفعيل حسابك.</p>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-10">
            <div class="space-y-3 text-right">
                <label class="text-[9px] font-black uppercase tracking-[0.3em] text-white/30 pr-3">اسم المستخدم</label>
                <div class="relative group">
                    <input required name="username" type="text" placeholder="<?php echo $db_connection_failed ? 'admin' : 'أدخل اسم المستخدم'; ?>"
                           class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-6 text-center transition-all shadow-inner text-white"/>
                </div>
            </div>
            <div class="space-y-3 text-right">
                <label class="text-[9px] font-black uppercase tracking-[0.3em] text-white/30 pr-3">كلمة المرور</label>
                <div class="relative group">
                    <input required name="password" type="password" placeholder="••••••••"
                           class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-6 text-center transition-all shadow-inner text-white"/>
                </div>
            </div>

            <div class="pt-8">
                <button type="submit" class="w-full py-6 gold-gradient text-[#3c2f00] font-headline font-black text-xs uppercase rounded-2xl shadow-2xl hover:scale-[1.03] active:scale-95 transition-all flex items-center justify-center gap-4 group">
                    <span class="material-symbols-outlined group-hover:translate-x-2 transition-transform" style="font-variation-settings: 'FILL' 1;">security</span>
                    تسجيل الدخول للنظام
                </button>
            </div>
        </form>
        
        <div class="mt-16 pt-10 border-t border-white/5 flex flex-col items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full <?php echo $db_connection_failed ? 'bg-orange-400' : 'bg-primary/40 animate-pulse'; ?>"></span>
                <p class="text-[9px] text-white/20 uppercase tracking-[0.3em] font-black">
                    <?php echo $db_connection_failed ? 'Emergency Mode Active' : 'Professional Admin Suite v4.0'; ?>
                </p>
            </div>
        </div>
    </main>

</body>
</html>
