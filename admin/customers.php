<?php
// admin/customers.php
// Ahmed Koshary Store - Sovereign Customer Directory
require_once 'includes/auth_middleware.php';
include '../includes/db.php';

// Safe fallback when DB offline
$top_customers = [];
$all_customers = [];

if (!$db_connection_failed && $pdo !== null) {
    $top_query = "SELECT whatsapp_number, COUNT(id) as total_orders, SUM(total_amount) as total_spent, MAX(order_date) as last_order_date 
                  FROM orders 
                  GROUP BY whatsapp_number 
                  ORDER BY total_spent DESC 
                  LIMIT 3";
    $top_customers = $pdo->query($top_query)->fetchAll();

    $all_query = "SELECT whatsapp_number, COUNT(id) as total_orders, SUM(total_amount) as total_spent, MAX(order_date) as last_order_date 
                  FROM orders 
                  GROUP BY whatsapp_number 
                  ORDER BY last_order_date DESC";
    $all_customers = $pdo->query($all_query)->fetchAll();
}

$pageTitle = "دليل النخبة | أحمد كشري";
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">وصول مدير الكونسيرج</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">دليل <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">العملاء النخبة</span></h1>
    </div>
    <button class="gold-gradient text-on-primary font-headline font-black px-8 py-4 rounded-xl shadow-2xl hover:scale-[1.03] transition-all flex items-center gap-3 text-xs uppercase tracking-widest">
        <span class="material-symbols-outlined text-sm">add</span>
        تسجيل عضو جديد
    </button>
</header>

<!-- Bento Grid of Identity Cards -->
<section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mb-20 relative z-10">
    <?php if (empty($top_customers)): ?>
        <div class="col-span-full glass-card rounded-[2.5rem] p-20 text-center text-on-surface-variant/20 italic">
            لا توجد بيانات عملاء كافية لعرض النخبة حالياً.
        </div>
    <?php else: ?>
        <?php foreach ($top_customers as $index => $cust): ?>
        <div class="glass-card rounded-[2.5rem] p-8 flex flex-col relative group transition-all duration-500 hover:bg-white/[0.03] border border-white/5 shadow-3xl">
            <!-- Action Icons -->
            <div class="absolute top-6 left-6 flex gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                <button class="w-8 h-8 rounded-full bg-white/5 text-on-surface-variant/40 hover:text-primary transition-colors flex items-center justify-center border border-white/5">
                    <span class="material-symbols-outlined text-xs">settings</span>
                </button>
            </div>

            <div class="flex items-start gap-6 mb-8">
                <div class="relative">
                    <div class="w-20 h-20 rounded-[1.5rem] overflow-hidden shadow-2xl border border-white/10 bg-white/5 flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-primary opacity-40">person</span>
                    </div>
                    <div class="absolute -bottom-1 -left-1 w-5 h-5 bg-green-500 rounded-full border-4 border-[#0a0e18] shadow-lg" title="نشط"></div>
                </div>
                <div class="space-y-1">
                    <h3 class="text-xl font-headline font-black text-on-surface leading-tight"><?php echo $cust['whatsapp_number']; ?></h3>
                    <p class="text-primary font-bold text-[9px] tracking-[0.2em] uppercase">عضو فئة النخبة #<?php echo $index + 1; ?></p>
                    <div class="flex items-center gap-2 mt-3 text-[9px] font-black uppercase tracking-widest text-[#D4AF37] px-3 py-1 bg-primary/10 rounded-full border border-primary/20 w-fit">نشط</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="space-y-1">
                    <p class="text-[9px] text-on-surface-variant/40 uppercase font-black tracking-widest">إجمالي الطلبات</p>
                    <p class="text-on-surface font-bold text-sm tracking-tight"><?php echo $cust['total_orders']; ?> طلبات</p>
                </div>
                <div class="space-y-1 text-left">
                    <p class="text-[9px] text-on-surface-variant/40 uppercase font-black tracking-widest">إجمالي الإنفاق</p>
                    <p class="text-primary font-headline font-black text-sm tracking-tight"><?php echo number_format($cust['total_spent']); ?> ج.م</p>
                </div>
            </div>

            <div class="mt-auto flex items-center justify-between border-t border-white/5 pt-6">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant/40 text-sm">calendar_today</span>
                    <div class="flex flex-col">
                        <span class="text-[8px] text-on-surface-variant/20 uppercase tracking-widest font-black">آخر ظهور</span>
                        <span class="text-[10px] font-bold text-on-surface/60"><?php echo date('d M, Y', strtotime($cust['last_order_date'])); ?></span>
                    </div>
                </div>
                <span class="material-symbols-outlined text-primary/10 text-4xl">verified_user</span>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<!-- Detailed Directory Table -->
<section class="space-y-8 relative z-10">
    <div class="flex items-center justify-between px-2">
        <h2 class="text-3xl font-headline font-black text-on-surface tracking-tighter">الاعضاء</h2>
        <button class="text-primary font-bold text-[10px] tracking-[0.3em] uppercase hover:underline underline-offset-8">تحميل تقرير CSV</button>
    </div>

    <div class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-3xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5">
                        <th class="px-10 py-6 text-[10px] font-black text-on-surface-variant/40 tracking-[0.2em] uppercase">هوية العضو</th>
                        <th class="px-10 py-6 text-center text-[10px] font-black text-on-surface-variant/40 tracking-[0.2em] uppercase">الحالة الشرائية</th>
                        <th class="px-10 py-6 text-center text-[10px] font-black text-on-surface-variant/40 tracking-[0.2em] uppercase">الخط الزمني</th>
                        <th class="px-10 py-6 text-left text-[10px] font-black text-on-surface-variant/40 tracking-[0.2em] uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($all_customers as $cust): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center overflow-hidden">
                                    <span class="material-symbols-outlined text-lg text-primary/40">person_search</span>
                                </div>
                                <div>
                                    <p class="font-headline font-bold text-on-surface"><?php echo $cust['whatsapp_number']; ?></p>
                                    <p class="text-[9px] text-on-surface-variant/40 font-black uppercase tracking-widest mt-1">Sovereign Client</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="text-primary font-headline font-black text-sm"><?php echo number_format($cust['total_spent']); ?> ج.م</span>
                                <span class="text-[9px] text-on-surface-variant/40 font-bold uppercase tracking-tighter"><?php echo $cust['total_orders']; ?> طلبـات موثقة</span>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-center">
                            <div class="inline-flex items-center gap-2 text-[10px] font-black text-on-surface-variant/60 uppercase tracking-widest">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                <?php echo date('d M, Y', strtotime($cust['last_order_date'])); ?>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-left">
                            <div class="flex justify-start gap-3">
                                <a href="https://wa.me/<?php echo $cust['whatsapp_number']; ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-on-surface-variant/40 hover:text-primary hover:bg-primary/10 transition-all">
                                    <span class="material-symbols-outlined text-lg">chat</span>
                                </a>
                                <button class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-on-surface-variant/40 hover:text-error hover:bg-error/10 transition-all">
                                    <span class="material-symbols-outlined text-lg">block</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include 'includes/admin_footer.php'; ?>

