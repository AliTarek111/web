<?php
// admin/index.php
// Ahmed Koshary Store - Admin Dashboard Overview
require_once 'includes/auth_middleware.php';
include '../includes/db.php';
require_once 'includes/activity_logger.php';

// Stats — safe fallback to 0 when DB is offline
$totalMobiles  = 0;
$totalSoftware = 0;
$pendingOrders = 0;
$totalSales    = 0;
$totalProducts = 0;
$totalUsers    = 0;

if (!$db_connection_failed && $pdo !== null) {
    $totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?: 0;
    $totalMobiles  = $pdo->query("SELECT COUNT(*) FROM products WHERE condition_status IN ('new','used','grade_a','grade_b')")->fetchColumn() ?: 0;
    $totalSoftware = $pdo->query("SELECT COUNT(*) FROM products WHERE product_type = 'software'")->fetchColumn() ?: 0;
    $pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn() ?: 0;
    $totalSales    = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?: 0;
    $totalUsers    = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn() ?: 0;
}

// Get recent activities
$recent_activities = [];
if (!$db_connection_failed && $pdo !== null) {
    $recent_activities = get_recent_activities($pdo, 8);
}

$pageTitle = "لوحة القيادة - نظرة عامة";
include 'includes/admin_header.php';
?>

<?php if (isset($_GET['err']) && $_GET['err'] === 'no_permission'): ?>
<div class="mb-10 p-5 bg-error/10 border border-error/20 rounded-2xl flex items-center gap-4 relative z-10">
    <span class="material-symbols-outlined text-error" style="font-variation-settings: 'FILL' 1;">gpp_bad</span>
    <div>
        <p class="text-error text-xs font-black uppercase tracking-widest">⛔ ليس لديك صلاحية للوصول لهذه الصفحة</p>
        <p class="text-white/30 text-[10px] mt-1">تواصل مع الأدمن الرئيسي لتفعيل الصلاحيات المطلوبة.</p>
    </div>
</div>
<?php endif; ?>

<?php if ($db_connection_failed): ?>
<div class="mb-10 p-5 bg-orange-500/10 border border-orange-500/20 rounded-2xl flex items-center justify-between gap-4 relative z-10">
    <div class="flex items-center gap-4">
        <span class="material-symbols-outlined text-orange-400 animate-pulse" style="font-variation-settings: 'FILL' 1;">wifi_off</span>
        <div>
            <p class="text-orange-400 text-xs font-black uppercase tracking-widest">⚠️ وضع الطوارئ — قاعدة البيانات غير متاحة</p>
            <p class="text-white/30 text-[10px] mt-1">جميع الإحصاءات معطّلة مؤقتاً. لا يمكن إدارة المنتجات والطلبات.</p>
        </div>
    </div>
    <a href="db_settings.php" class="flex-shrink-0 px-5 py-2.5 gold-gradient text-[#3c2f00] text-[10px] font-black uppercase tracking-widest rounded-xl hover:scale-105 transition-all shadow-lg flex items-center gap-2">
        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">storage</span>
        إصلاح الاتصال
    </a>
</div>
<?php endif; ?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">نظرة عامة</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">مدير النظام <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">Admin Overview</span></h1>
    </div>
    <div class="flex gap-4">
        <!-- Export Reports Dropdown -->
        <div class="relative group">
            <button class="px-8 py-3 glass-card rounded-xl text-xs font-bold uppercase tracking-widest hover:border-primary/40 transition-all border border-white/5 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">download</span>
                تصدير التقارير
            </button>
            <div class="absolute left-0 top-full mt-2 w-56 bg-[#0f131d] border border-white/10 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] hidden group-hover:block z-50 overflow-hidden">
                <a href="export_report.php?type=all" class="flex items-center gap-3 px-5 py-4 text-xs font-bold text-on-surface hover:bg-primary/10 hover:text-primary transition-colors border-b border-white/5">
                    <span class="material-symbols-outlined text-sm">summarize</span>
                    تقرير شامل
                </a>
                <a href="export_report.php?type=products" class="flex items-center gap-3 px-5 py-4 text-xs font-bold text-on-surface hover:bg-primary/10 hover:text-primary transition-colors border-b border-white/5">
                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                    تقرير المنتجات
                </a>
                <a href="export_report.php?type=orders" class="flex items-center gap-3 px-5 py-4 text-xs font-bold text-on-surface hover:bg-primary/10 hover:text-primary transition-colors border-b border-white/5">
                    <span class="material-symbols-outlined text-sm">shopping_cart</span>
                    تقرير الطلبات
                </a>
                <a href="export_report.php?type=staff" class="flex items-center gap-3 px-5 py-4 text-xs font-bold text-on-surface hover:bg-primary/10 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-sm">group</span>
                    تقرير المستخدمين
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16 relative z-10">
    <div class="stat-card p-10 rounded-[2rem] relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-primary/5 blur-3xl rounded-full -translate-y-12 translate-x-12 group-hover:scale-150 transition-transform"></div>
        <p class="text-on-surface-variant/40 uppercase text-[10px] font-black tracking-widest mb-6">إجمالي المبيعات</p>
        <h3 class="text-4xl font-headline font-black text-primary tracking-tighter"><?php echo number_format($totalSales); ?> <span class="text-sm font-normal">ج.م</span></h3>
    </div>
    <div class="stat-card p-10 rounded-[2rem] relative overflow-hidden group">
        <p class="text-on-surface-variant/40 uppercase text-[10px] font-black tracking-widest mb-6">طلبات قيد المراجعة</p>
        <h3 class="text-4xl font-headline font-black text-on-surface tracking-tighter"><?php echo $pendingOrders; ?> <span class="text-sm font-normal">طلب</span></h3>
    </div>
    <div class="stat-card p-10 rounded-[2rem] relative overflow-hidden group">
        <p class="text-on-surface-variant/40 uppercase text-[10px] font-black tracking-widest mb-6">إجمالي المنتجات</p>
        <h3 class="text-4xl font-headline font-black text-on-surface tracking-tighter"><?php echo $totalProducts; ?> <span class="text-sm font-normal">منتج</span></h3>
    </div>
    <div class="stat-card p-10 rounded-[2rem] relative overflow-hidden group">
        <p class="text-on-surface-variant/40 uppercase text-[10px] font-black tracking-widest mb-6">عدد العملاء</p>
        <h3 class="text-4xl font-headline font-black text-on-surface tracking-tighter"><?php echo $totalUsers; ?> <span class="text-sm font-normal">عميل</span></h3>
    </div>
</div>

<!-- Activity Section -->
<section class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-3xl relative z-10">
    <div class="p-8 px-10 bg-white/5 flex justify-between items-center border-b border-white/5">
        <h2 class="text-xl font-black font-headline tracking-tight flex items-center gap-3">
            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">history</span>
            آخر النشاطات في النظام
        </h2>
        <div class="flex items-center gap-3">
            <span class="px-4 py-1.5 bg-primary/10 text-primary text-[10px] font-black rounded-full uppercase tracking-widest shadow-lg">التحديثات الحية</span>
            <a href="activity_log.php" class="flex items-center gap-2 px-5 py-2 bg-white/5 border border-white/10 text-on-surface hover:border-primary/40 hover:text-primary rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                سجل كامل
            </a>
        </div>
    </div>
    <div class="p-10 space-y-6">
        <?php if (empty($recent_activities)): ?>
        <div class="text-center py-16 opacity-30">
            <span class="material-symbols-outlined text-5xl block mb-4">history</span>
            <p class="text-sm font-bold">لا توجد نشاطات مسجلة بعد. ستظهر هنا تلقائياً عند تنفيذ أي إجراء.</p>
        </div>
        <?php else: ?>
        <?php foreach ($recent_activities as $i => $act): ?>
        <div class="flex items-start gap-5 group hover:bg-white/[0.02] p-4 -mx-4 rounded-2xl transition-all">
            <div class="w-10 h-10 rounded-xl <?php echo $i === 0 ? 'gold-gradient shadow-[0_0_15px_rgba(242,202,80,0.3)]' : 'bg-white/5 border border-white/5'; ?> flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-sm <?php echo $i === 0 ? 'text-on-primary' : 'text-on-surface/40'; ?>"><?php echo get_action_icon($act['action_type']); ?></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold <?php echo $i === 0 ? 'text-on-surface/90' : 'text-on-surface/60'; ?> truncate"><?php echo htmlspecialchars($act['description']); ?></p>
                <div class="flex items-center gap-4 mt-2">
                    <p class="text-[10px] text-primary/60 font-bold uppercase tracking-widest"><?php echo time_ago_arabic($act['created_at']); ?></p>
                    <span class="text-[8px] text-on-surface-variant/20">•</span>
                    <p class="text-[10px] text-on-surface-variant/30 font-bold">بواسطة <?php echo htmlspecialchars($act['username'] ?? 'النظام'); ?></p>
                    <span class="text-[8px] text-on-surface-variant/20">•</span>
                    <span class="px-2 py-0.5 bg-white/5 text-[8px] font-black uppercase rounded-full text-on-surface-variant/30 tracking-widest"><?php echo htmlspecialchars($act['action_type']); ?></span>
                </div>
            </div>
        </div>
        <?php if ($i < count($recent_activities) - 1): ?>
        <div class="border-b border-white/5 mr-14"></div>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/admin_footer.php'; ?>
