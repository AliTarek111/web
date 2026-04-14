<?php
// admin/db_settings.php
// Ahmed Koshary Store - Database Connection Settings
require_once 'includes/auth_middleware.php';

include '../includes/db.php';

$config_path = realpath(__DIR__ . '/../includes/config.php') ?: (__DIR__ . '/../includes/config.php');
$message  = '';
$msg_type = 'success';

// Auto-redirect notice from other pages when DB was offline
if (isset($_GET['err']) && $_GET['err'] === 'offline' && empty($message)) {
    $message  = '⚠️ تم تحويلك هنا لأن قاعدة البيانات غير متاحة. أصلح الاتصال أولاً ثم عد للصفحة السابقة.';
    $msg_type = 'error';
}

// ─── Handle Form Submission ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'save_db') {
        $new_host    = trim($_POST['db_host']);
        $new_name    = trim($_POST['db_name']);
        $new_user    = trim($_POST['db_user']);
        $new_pass    = $_POST['db_pass'];
        $new_charset = 'utf8mb4';

        // Validate — test the connection before saving
        $test_dsn = "mysql:host={$new_host};dbname={$new_name};charset={$new_charset}";
        try {
            $test_pdo = new PDO($test_dsn, $new_user, $new_pass, [
                PDO::ATTR_ERRMODE  => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT  => 5,
            ]);
            // Connection succeeded — write to config.php
            $config_content = "<?php\n// includes/config.php\n// Ahmed Koshary Store - Central Database Configuration\n// This file is managed by the Admin Panel > DB Settings page.\n\nreturn [\n    'db_host'    => " . var_export($new_host, true) . ",\n    'db_name'    => " . var_export($new_name, true) . ",\n    'db_user'    => " . var_export($new_user, true) . ",\n    'db_pass'    => " . var_export($new_pass, true) . ",\n    'db_charset' => 'utf8mb4',\n];\n?>\n";

            if (file_put_contents($config_path, $config_content) !== false) {
                $message  = "✅ تم حفظ إعدادات قاعدة البيانات وتم التحقق من الاتصال بنجاح!";
                $msg_type = 'success';
                // Reload config
                $cfg = require $config_path;
                $pdo = $test_pdo;
                $db_connection_failed = false;
            } else {
                $message  = "⚠️ تعذّر كتابة ملف الإعدادات. تأكد من صلاحيات الكتابة على المجلد includes/";
                $msg_type = 'error';
            }
        } catch (\PDOException $e) {
            $message  = "❌ فشل الاتصال بقاعدة البيانات: " . htmlspecialchars($e->getMessage());
            $msg_type = 'error';
        }
    }

    if ($_POST['action'] === 'test_db') {
        $test_host = trim($_POST['db_host']);
        $test_name = trim($_POST['db_name']);
        $test_user = trim($_POST['db_user']);
        $test_pass = $_POST['db_pass'];
        try {
            $t = new PDO("mysql:host={$test_host};dbname={$test_name};charset=utf8mb4", $test_user, $test_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);
            $message  = "✅ الاتصال ناجح! قاعدة البيانات جاهزة. (لم يتم الحفظ بعد — انقر حفظ لتأكيد الإعدادات)";
            $msg_type = 'success';
        } catch (\PDOException $e) {
            $message  = "❌ فشل الاختبار: " . htmlspecialchars($e->getMessage());
            $msg_type = 'error';
        }
    }
}

// ─── Load current config ───────────────────────────────────────────────────────
$current_cfg = file_exists($config_path) ? require $config_path : [
    'db_host' => 'localhost', 'db_name' => 'ahmedkoshary_db',
    'db_user' => 'root',      'db_pass' => '',
];

$pageTitle = "إعدادات قاعدة البيانات";
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">إعدادات النظام</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">اتصال <br/><span class="bg-gradient-to-l from-primary to-yellow-600 bg-clip-text text-transparent">قاعدة البيانات</span></h1>
    </div>
    <!-- Current Status Badge -->
    <div class="flex items-center gap-3 px-6 py-3 glass-card rounded-2xl border border-white/5">
        <span class="w-2.5 h-2.5 rounded-full <?php echo ($db_connection_failed ?? false) ? 'bg-red-500 animate-pulse' : 'bg-green-500 animate-pulse'; ?>"></span>
        <span class="text-[10px] font-black uppercase tracking-widest <?php echo ($db_connection_failed ?? false) ? 'text-red-400' : 'text-green-400'; ?>">
            <?php echo ($db_connection_failed ?? false) ? 'الاتصال مقطوع' : 'متصل بقاعدة البيانات'; ?>
        </span>
    </div>
</header>

<?php if ($message): ?>
<div class="mb-10 p-5 <?php echo $msg_type === 'success' ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-red-500/10 border-red-500/20 text-red-400'; ?> border text-sm font-bold rounded-2xl shadow-2xl relative z-10 flex items-start gap-4">
    <span class="material-symbols-outlined text-xl flex-shrink-0"><?php echo $msg_type === 'success' ? 'check_circle' : 'error'; ?></span>
    <span><?php echo $message; ?></span>
</div>
<?php endif; ?>

<!-- DB Settings Form -->
<section class="glass-card rounded-[2.5rem] p-10 md:p-16 border border-white/5 shadow-3xl relative z-10 max-w-4xl">
    <form method="POST" class="space-y-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            <!-- DB Host -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined text-sm">dns</span>
                    عنوان الخادم (Host)
                </label>
                <input name="db_host" type="text" value="<?php echo htmlspecialchars($current_cfg['db_host'] ?? 'localhost'); ?>"
                       placeholder="localhost" 
                       class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-5 text-on-surface shadow-inner transition-all font-mono"/>
                <p class="text-[9px] text-white/20 font-bold pr-2">عادةً: localhost أو عنوان IP الخادم</p>
            </div>

            <!-- DB Name -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined text-sm">database</span>
                    اسم قاعدة البيانات
                </label>
                <input name="db_name" type="text" value="<?php echo htmlspecialchars($current_cfg['db_name'] ?? ''); ?>"
                       placeholder="ahmedkoshary_db"
                       class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-5 text-on-surface shadow-inner transition-all font-mono"/>
            </div>

            <!-- DB User -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined text-sm">person</span>
                    اسم مستخدم قاعدة البيانات
                </label>
                <input name="db_user" type="text" value="<?php echo htmlspecialchars($current_cfg['db_user'] ?? 'root'); ?>"
                       placeholder="root"
                       class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-5 text-on-surface shadow-inner transition-all font-mono"/>
            </div>

            <!-- DB Pass -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    كلمة مرور قاعدة البيانات
                </label>
                <input name="db_pass" id="db_pass_input" type="password" value="<?php echo htmlspecialchars($current_cfg['db_pass'] ?? ''); ?>"
                       placeholder="اتركها فارغة إن لم تكن هناك كلمة مرور"
                       class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-5 text-on-surface shadow-inner transition-all font-mono"/>
                <button type="button" onclick="togglePass()" class="text-[9px] text-primary/60 hover:text-primary transition-colors uppercase tracking-widest font-bold">
                    إظهار / إخفاء كلمة المرور
                </button>
            </div>
        </div>

        <!-- Info Box -->
        <div class="p-6 bg-primary/5 border border-primary/10 rounded-2xl flex items-start gap-4">
            <span class="material-symbols-outlined text-primary/60 flex-shrink-0">info</span>
            <div class="space-y-1">
                <p class="text-[10px] font-black uppercase tracking-widest text-primary/80">ملاحظة مهمة</p>
                <p class="text-[11px] text-white/40 leading-relaxed">
                    سيتم اختبار الاتصال قبل حفظ البيانات. إذا فشل test connection لن يتم تحديث الملف.
                    في حالة فصل قاعدة البيانات، يمكن الدخول للوحة الإدارة بـ <strong class="text-primary">admin / 123</strong>
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button type="submit" name="action" value="test_db"
                    class="flex-1 py-5 bg-white/5 border border-white/10 hover:border-primary/30 hover:bg-primary/5 text-white font-headline font-black text-xs uppercase rounded-2xl shadow-lg hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3 group">
                <span class="material-symbols-outlined group-hover:rotate-12 transition-transform" style="font-variation-settings: 'FILL' 1;">cable</span>
                اختبار الاتصال فقط
            </button>
            <button type="submit" name="action" value="save_db"
                    class="flex-1 py-5 gold-gradient text-[#3c2f00] font-headline font-black text-xs uppercase rounded-2xl shadow-2xl hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3 group">
                <span class="material-symbols-outlined group-hover:rotate-12 transition-transform" style="font-variation-settings: 'FILL' 1;">save</span>
                اختبار وحفظ الإعدادات
            </button>
        </div>
    </form>
</section>

<!-- Emergency Credentials Info -->
<section class="mt-12 glass-card rounded-[2rem] p-8 border border-orange-500/10 relative z-10 max-w-4xl">
    <div class="flex items-start gap-6">
        <div class="w-12 h-12 rounded-2xl bg-orange-500/10 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-orange-400" style="font-variation-settings: 'FILL' 1;">emergency</span>
        </div>
        <div>
            <h3 class="text-sm font-black uppercase tracking-widest text-orange-400 mb-3">بيانات الطوارئ</h3>
            <p class="text-white/40 text-xs leading-relaxed mb-4">
                إذا كانت قاعدة البيانات غير متاحة، يمكن الدخول إلى لوحة الأدمن باستخدام بيانات الطوارئ التالية:
            </p>
            <div class="flex gap-4">
                <div class="px-4 py-2 bg-[#0a0e18] border border-white/5 rounded-xl">
                    <p class="text-[8px] text-white/20 uppercase tracking-widest mb-1">اسم المستخدم</p>
                    <p class="font-mono font-black text-orange-400 text-sm">admin</p>
                </div>
                <div class="px-4 py-2 bg-[#0a0e18] border border-white/5 rounded-xl">
                    <p class="text-[8px] text-white/20 uppercase tracking-widest mb-1">كلمة المرور</p>
                    <p class="font-mono font-black text-orange-400 text-sm">123</p>
                </div>
            </div>
            <p class="text-[9px] text-orange-400/40 mt-3">⚠️ هذه البيانات مشفرة في الكود — لا يمكن تغييرها من هنا. للتعديل، غيّرها في ملف admin/login.php</p>
        </div>
    </div>
</section>

<script>
function togglePass() {
    const inp = document.getElementById('db_pass_input');
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>

<?php include 'includes/admin_footer.php'; ?>

