<?php
// profile.php
// Ahmed Koshary Store - Professional Member Dashboard
session_start();
include 'includes/db.php';

// Secure the page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user data from users
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get recent orders (if orders table exists)
try {
    $orders_stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5");
    $orders_stmt->execute([$user_id]);
    $orders = $orders_stmt->fetchAll();
} catch (PDOException $e) {
    $orders = []; // Table might not exist yet or error
}

$pageTitle = "حسابي";
include 'includes/header.php';
?>

<div class="pt-32 pb-24 container mx-auto px-6 max-w-6xl">
    <!-- Profile Header -->
    <header class="mb-16 flex flex-col md:flex-row justify-between items-center gap-10">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <!-- Avatar -->
            <div class="w-32 h-32 rounded-[2.5rem] gold-gradient flex items-center justify-center text-on-primary text-5xl font-black shadow-2xl relative group">
                <div class="absolute inset-4 border-2 border-on-primary/20 rounded-2xl group-hover:scale-110 transition-transform"></div>
                <?php echo mb_substr($user['full_name'], 0, 1, 'utf-8'); ?>
            </div>
            <div class="text-center md:text-right">
                <span class="inline-block text-primary font-black tracking-[0.4em] text-[10px] uppercase mb-2">عضوية مميزة</span>
                <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight"><?php echo $user['full_name']; ?></h1>
                <p class="text-on-surface-variant/40 mt-4 flex items-center justify-center md:justify-start gap-3">
                    <span class="material-symbols-outlined text-sm">alternate_email</span>
                    <?php echo $user['username']; ?>
                    <span class="w-1 h-1 rounded-full bg-white/10 mx-2"></span>
                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                    انضم في <?php echo date('Y-m-d', strtotime($user['created_at'])); ?>
                </p>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="logout.php" class="px-8 py-4 glass-card border-white/5 rounded-2xl text-xs font-black uppercase tracking-widest hover:border-error/40 hover:text-error transition-all flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">logout</span>
                تسجيل الخروج
            </a>
            <a href="index.php" class="px-8 py-4 gold-gradient text-on-primary rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl hover:scale-[1.05] transition-all flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">shopping_cart</span>
                العودة للمتجر
            </a>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Account Details -->
        <div class="lg:col-span-1 space-y-10">
            <section class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl">
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/60 border-b border-primary/10 pb-6 mb-8">بيانات الحساب</h3>
                <div class="space-y-8">
                    <div class="flex justify-between items-center bg-white/[0.02] p-4 rounded-2xl border border-white/5">
                        <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40">رقم الهاتف</span>
                        <span class="font-bold text-on-surface"><?php echo htmlspecialchars($user['phone']); ?></span>
                    </div>
                    <div class="flex justify-between items-center bg-white/[0.02] p-4 rounded-2xl border border-white/5">
                        <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40">عنوان التوصيل</span>
                        <span class="font-bold text-on-surface text-sm text-left line-clamp-2 max-w-[60%]"><?php echo htmlspecialchars($user['address'] ?? 'غير محدد'); ?></span>
                    </div>
                    <div class="flex justify-between items-center bg-white/[0.02] p-4 rounded-2xl border border-white/5">
                        <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40">حالة الحساب</span>
                        <span class="px-3 py-1 bg-green-500/10 text-green-500 text-[9px] font-black uppercase rounded-full border border-green-500/20">نشط</span>
                    </div>
                </div>
            </section>
            
            <section class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl bg-gradient-to-br from-primary/5 to-transparent">
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/60 mb-6">الدعم الفني</h3>
                <p class="text-xs text-on-surface-variant/60 leading-relaxed mb-8">هل واجهت مشكلة في طلبك؟ فريقنا جاهز لمساعدتك في أي وقت عبر الواتساب.</p>
                <a href="https://wa.me/201200000000" class="w-full py-4 glass-card border-primary/20 text-primary text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-primary hover:text-on-primary transition-all flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-sm">chat</span>
                    تحدث معنا الآن
                </a>
            </section>
        </div>

        <!-- Orders History -->
        <div class="lg:col-span-2">
            <section class="glass-card rounded-[3rem] overflow-hidden border border-white/5 shadow-3xl min-h-[500px]">
                <div class="p-8 px-10 bg-white/5 flex justify-between items-center border-b border-white/5">
                    <h2 class="text-xl font-black font-headline tracking-tight">آخر الطلبات</h2>
                    <span class="text-[9px] text-on-surface-variant/40 uppercase tracking-[0.2em] font-black">History Overview</span>
                </div>
                
                <div class="p-10">
                    <?php if (empty($orders)): ?>
                    <div class="flex flex-col items-center justify-center py-20 text-center space-y-6">
                        <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl text-on-surface-variant/20">package_2</span>
                        </div>
                        <div>
                            <p class="text-on-surface-variant/40 text-[10px] font-black uppercase tracking-[0.3em] mb-4">لا توجد طلبات سابقة</p>
                            <a href="index.php" class="text-primary font-bold hover:underline">ابدأ التسوق الآن</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="border-b border-white/5 text-[10px] font-black uppercase tracking-[0.3em] text-on-surface-variant/40">
                                <th class="pb-6 pr-4">رقم الطلب</th>
                                <th class="pb-6">التاريخ</th>
                                <th class="pb-6">الإجمالي</th>
                                <th class="pb-6 text-left">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($orders as $order): ?>
                            <tr class="group hover:bg-white/[0.02] transition-colors">
                                <td class="py-8 pr-4">
                                    <span class="font-bold text-on-surface">#<?php echo $order['id']; ?></span>
                                </td>
                                <td class="py-8">
                                    <span class="text-xs text-on-surface/60"><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></span>
                                </td>
                                <td class="py-8">
                                    <span class="font-black text-primary uppercase text-sm"><?php echo number_format($order['total_amount']); ?> ج.م</span>
                                </td>
                                <td class="py-8 text-left">
                                    <span class="px-4 py-2 bg-primary/10 text-primary text-[9px] font-black uppercase rounded-full border border-primary/20">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
