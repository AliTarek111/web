<?php
// admin/manage_devices.php
// Ahmed Koshary Store - Manage Mobiles & Featured Devices
require_once 'includes/auth_middleware.php';
include '../includes/db.php';

$message = '';
$message_type = 'success';

// Handle Toggle Featured
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_featured') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $current_status = (int)($_POST['current_status'] ?? 0);
    $new_status = $current_status ? 0 : 1;
    
    $stmt = $pdo->prepare("UPDATE products SET is_featured = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $product_id])) {
        $message = 'تم تحديث حالة الجهاز بنجاح.';
    } else {
        $message = 'لم يتم التحديث، يرجى المحاولة لاحقاً.';
        $message_type = 'error';
    }
}

// Fetch only mobiles/smartphones
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE c.slug = 'smartphones' OR c.name LIKE '%موبيل%' OR c.name LIKE '%هواتف%' 
          ORDER BY p.is_featured DESC, p.created_at DESC";
$devices = $pdo->query($query)->fetchAll();

$pageTitle = 'إدارة الأجهزة المميزة';
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">Devices Management</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter leading-tight">إدارة <span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">الموبايلات</span></h1>
        <p class="text-on-surface-variant/70 text-sm mt-3">من هنا يمكنك تحديد الأجهزة التي تظهر في قسم "الأجهزة المميزة"</p>
    </div>
    <a href="inventory.php" class="px-8 py-4 glass-card border-white/5 rounded-xl text-xs font-black uppercase tracking-widest hover:border-primary/40 transition-all flex items-center gap-3">
        <span class="material-symbols-outlined text-sm">inventory_2</span>
        المخزون الكامل
    </a>
</header>

<?php if ($message): ?>
<div class="mb-10 p-5 glass-card rounded-2xl flex items-center gap-4 text-xs font-black uppercase tracking-widest <?php echo $message_type === 'error' ? 'border-error/20 text-error' : 'border-primary/20 text-primary'; ?>">
    <span class="material-symbols-outlined"><?php echo $message_type === 'error' ? 'warning' : 'verified'; ?></span>
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl relative z-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php if (empty($devices)): ?>
            <div class="col-span-full py-16 text-center text-on-surface-variant/40">لا توجد موبايلات مسجلة حتى الآن. تأكد من إضافتها لقسم "هواتف ذكية".</div>
        <?php else: ?>
            <?php foreach ($devices as $d): ?>
            <div class="glass-card rounded-[2rem] p-5 border border-white/5 hover:border-primary/30 transition-all flex flex-col group relative">
                <?php if ($d['is_featured']): ?>
                    <div class="absolute top-4 right-4 z-10 bg-primary text-on-primary text-[10px] font-black uppercase px-3 py-1 rounded-full shadow-[0_0_15px_rgba(242,202,80,0.5)]">
                        ⭐ مميز
                    </div>
                <?php endif; ?>
                
                <div class="h-40 w-full mb-4 bg-[#0a0e18] rounded-[1.5rem] overflow-hidden">
                    <img src="../<?php echo $d['main_image']; ?>" class="w-full h-full object-contain p-2 group-hover:scale-110 transition-transform">
                </div>
                
                <h3 class="font-black text-sm text-on-surface mb-1 line-clamp-1"><?php echo htmlspecialchars($d['name']); ?></h3>
                <p class="text-xs text-on-surface-variant/60 font-bold mb-4 font-headline uppercase" dir="ltr"><?php echo htmlspecialchars($d['uid']); ?></p>
                
                <div class="mt-auto flex gap-2">
                    <form method="POST" class="flex-1">
                        <input type="hidden" name="action" value="toggle_featured">
                        <input type="hidden" name="product_id" value="<?php echo $d['id']; ?>">
                        <input type="hidden" name="current_status" value="<?php echo $d['is_featured']; ?>">
                        <?php if ($d['is_featured']): ?>
                            <button class="w-full py-2 bg-white/5 hover:bg-error/20 border border-white/10 hover:border-error/50 text-white hover:text-error rounded-xl text-[10px] font-black tracking-widest transition-all">إلغاء التمييز</button>
                        <?php else: ?>
                            <button class="w-full py-2 bg-primary/10 hover:bg-primary border border-primary/30 hover:border-primary text-primary hover:text-on-primary rounded-xl text-[10px] font-black tracking-widest transition-all">جعله مميزاً</button>
                        <?php endif; ?>
                    </form>
                    <a href="edit_product.php?id=<?php echo $d['id']; ?>" class="w-10 h-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 hover:border-white/30 transition-all">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
