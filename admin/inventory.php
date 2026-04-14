<?php
// admin/inventory.php
// Ahmed Koshary Store - Inventory Management
require_once 'includes/auth_middleware.php';
include '../includes/db.php';

// Prepare products query — safe fallback when DB offline
$products = [];
if (!$db_connection_failed && $pdo !== null) {
    $products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
}

$pageTitle = "إدارة المخزون";
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">إدارة المنتجات</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">قائمة <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">المخزون المتوفر</span></h1>
    </div>
    <div class="flex gap-4">
        <a href="add_product.php" class="px-8 py-4 gold-gradient text-on-primary rounded-xl text-xs font-black uppercase tracking-widest shadow-2xl hover:scale-[1.03] active:scale-95 transition-all flex items-center gap-3">
            <span class="material-symbols-outlined text-sm">add</span>
            إضافة منتج جديد للعرش
        </a>
    </div>
</header>

<!-- Products Table -->
<section class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-3xl relative z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-white/5 text-on-surface-variant/40 text-[10px] uppercase font-black tracking-widest border-b border-white/5">
                    <th class="px-10 py-6">كود المنتج</th>
                    <th class="px-10 py-6">المنتج</th>
                    <th class="px-10 py-6 text-center">القسم</th>
                    <th class="px-10 py-6 text-center">المخزون</th>
                    <th class="px-10 py-6 text-center">السعر</th>
                    <th class="px-10 py-6 text-center">الحالة</th>
                    <th class="px-10 py-6 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" class="px-10 py-24 text-center text-on-surface-variant/20 italic">لا توجد منتجات مسجلة في المخزن حاليا.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-10 py-6 text-[10px] font-bold text-primary tracking-widest uppercase">#<?php echo $product['uid']; ?></td>
                    <td class="px-10 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center p-2 group-hover:scale-110 transition-transform">
                                <img src="<?php echo $product['main_image']; ?>" class="w-full h-full object-contain" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-on-surface flex items-center gap-2">
                                    <?php echo $product['name']; ?>
                                    <?php if ($product['is_featured']): ?>
                                        <span class="material-symbols-outlined text-primary text-[14px]" style="font-variation-settings: 'FILL' 1;" title="منتج مميز">star</span>
                                    <?php endif; ?>
                                </p>
                                <p class="text-[10px] text-on-surface-variant/40 font-bold uppercase mt-1">تحديث: <?php echo date('d M, Y', strtotime($product['created_at'])); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-10 py-6 text-center">
                        <span class="px-3 py-1 bg-white/5 text-on-surface-variant text-[9px] font-black uppercase rounded-full border border-white/5 tracking-widest"><?php echo $product['category_name']; ?></span>
                    </td>
                    <td class="px-10 py-6 text-center">
                        <span class="text-sm font-headline font-bold <?php echo $product['stock_count'] < 5 ? 'text-error' : 'text-on-surface'; ?>"><?php echo $product['stock_count']; ?></span>
                    </td>
                    <td class="px-10 py-6 text-center text-sm font-headline font-black text-primary tracking-tight"><?php echo number_format($product['price']); ?> ج.م</td>
                    <td class="px-10 py-6 text-center">
                        <span class="px-3 py-1 bg-primary/10 text-primary text-[9px] font-black uppercase rounded-full border border-primary/20 tracking-widest"><?php echo $product['condition_status']; ?></span>
                    </td>
                    <td class="px-10 py-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                             <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="w-8 h-8 rounded-lg bg-white/5 border border-white/5 flex items-center justify-center text-on-surface/40 hover:bg-primary/20 hover:text-primary transition-all shadow-lg"><span class="material-symbols-outlined text-sm">edit</span></a>
                             <a href="delete_product.php?id=<?php echo $product['id']; ?>" onclick="return confirm('هل أنت متأكد من مسح هذه التحفة التقنية نهائياً من العرش؟')" class="w-8 h-8 rounded-lg bg-white/5 border border-white/5 flex items-center justify-center text-on-surface/40 hover:bg-error/20 hover:text-error transition-all shadow-lg"><span class="material-symbols-outlined text-sm">delete</span></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include 'includes/admin_footer.php'; ?>

