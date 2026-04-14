<?php
// admin/settings.php
// Ahmed Koshary Store - General Settings
require_once 'includes/auth_middleware.php';
include '../includes/db.php';

$message = "";
$settings = [];

// If DB is offline, redirect with a notice
if ($db_connection_failed || $pdo === null) {
    $message = "⚠️ قاعدة البيانات غير متاحة. يمكنك تعديل إعدادات الاتصال من هنا.";
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        $message = "تم تحديث إعدادات المتجر بنجاح!";
    }

    // Fetch all settings
    $settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
    foreach ($settings_raw as $s) {
        $settings[$s['setting_key']] = $s['setting_value'];
    }
}

$pageTitle = "إعدادات الهوية";
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">تخصيص النظام</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">إعدادات <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">هوية المتجر</span></h1>
    </div>
</header>

<?php if ($message): ?>
    <div class="mb-12 p-5 glass-card border-primary/20 text-primary text-xs font-black uppercase tracking-widest rounded-2xl shadow-2xl relative z-10 flex items-center gap-4">
        <span class="material-symbols-outlined">verified</span>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<section class="glass-card rounded-[2.5rem] p-10 md:p-16 border border-white/5 shadow-3xl relative z-10 max-w-4xl">
    <form method="POST" class="space-y-10">
        <div class="grid grid-cols-1 gap-12">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined text-sm">storefront</span>
                    اسم المتجر (العرض العلوي)
                </label>
                <input name="settings[store_name]" type="text" value="<?php echo htmlspecialchars($settings['store_name'] ?? 'أحمد كشري'); ?>" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-5 text-on-surface shadow-inner transition-all"/>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined text-sm">chat_bubble</span>
                    رقم واتساب المبيعات المركزي
                </label>
                <input name="settings[whatsapp_number]" type="text" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>" placeholder="201234567890" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-5 text-on-surface shadow-inner transition-all"/>
                <p class="text-[9px] text-on-surface-variant/20 uppercase tracking-widest font-bold pr-2">أدخل الرقم مع كود الدولة وبدون علامة +</p>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined text-sm">payments</span>
                    العملة المستخدمة
                </label>
                <input name="settings[currency]" type="text" value="<?php echo htmlspecialchars($settings['currency'] ?? 'ج.م'); ?>" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary focus:ring-1 focus:ring-primary/20 text-sm p-5 text-on-surface shadow-inner transition-all"/>
            </div>
        </div>

        <div class="pt-8 border-t border-white/5">
            <button type="submit" class="w-full py-6 gold-gradient text-on-primary font-headline font-black text-xs uppercase rounded-2xl shadow-2xl hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-4 group">
                <span class="material-symbols-outlined group-hover:rotate-12 transition-transform" style="font-variation-settings: 'FILL' 1;">save</span>
                حفظ الإعدادات
            </button>
        </div>
    </form>
</section>

<?php include 'includes/admin_footer.php'; ?>

