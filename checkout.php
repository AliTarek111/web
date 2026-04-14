<?php
// checkout.php
// Ahmed Koshary Store - Secure Checkout
$pageTitle = "إتمام الطلب";
include 'includes/header.php';
include 'includes/db.php';

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$total = 0;
$ids = array_keys($_SESSION['cart']);
$in = str_repeat('?,', count($ids) - 1) . '?';
$stmt = $pdo->prepare("SELECT price, id FROM products WHERE id IN ($in)");
$stmt->execute($ids);
while ($row = $stmt->fetch()) {
    $total += $row['price'] * $_SESSION['cart'][$row['id']];
}
?>

<div class="min-h-screen bg-background pt-32 pb-24">
    <div class="container mx-auto px-4 md:px-8 max-w-4xl">
        <header class="mb-12 text-center">
            <h1 class="text-4xl font-headline font-black uppercase tracking-tight text-on-surface">إتمام عملية الدفع</h1>
            <p class="text-on-surface-variant/60 mt-2 font-bold tracking-widest text-sm">رجاءً أكمل بيانات التوصيل بدقة</p>
        </header>

        <div class="glass-card p-10 lg:p-16 rounded-[3.5rem] border border-primary/20 shadow-[0_50px_100px_rgba(0,0,0,0.5)]">
            <form action="process_order.php" method="POST" class="space-y-10">
                <input type="hidden" name="cart_checkout" value="1">
                
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.4em] text-on-surface-variant/40 pr-4">الاسم بالكامل <span class="text-error/60">*</span></label>
                    <input required name="name" type="text" placeholder="الاسم ثلاثي" class="w-full bg-[#0a0e18] border-white/5 rounded-3xl p-7 text-sm font-bold tracking-widest text-on-surface focus:border-primary transition-all shadow-inner"/>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.4em] text-on-surface-variant/40 pr-4">رقم الموبايل (واتساب) <span class="text-error/60">*</span></label>
                    <input required name="phone" type="text" placeholder="01..." class="w-full bg-[#0a0e18] border-white/5 rounded-3xl p-7 text-sm font-bold tracking-widest text-on-surface focus:border-primary transition-all shadow-inner"/>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.4em] text-on-surface-variant/40 pr-4">عنوان التوصيل <span class="text-error/60">*</span></label>
                    <textarea required name="address" placeholder="المحافظة، المدينة، الشارع، رقم العمارة والدور..." class="w-full bg-[#0a0e18] border-white/5 rounded-3xl p-7 text-sm font-bold text-on-surface focus:border-primary transition-all h-36 shadow-inner resize-none"></textarea>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.4em] text-on-surface-variant/40 pr-4">ملاحظات إضافية (اختياري)</label>
                    <textarea name="notes" placeholder="ملاحظات حول التوصيل..." class="w-full bg-[#0a0e18] border-white/5 rounded-3xl p-7 text-sm font-bold text-on-surface focus:border-primary transition-all h-32 shadow-inner resize-none"></textarea>
                </div>

                <button type="submit" class="w-full py-8 gold-gradient text-on-primary font-headline font-black text-xs uppercase tracking-[0.3em] rounded-3xl shadow-[0_20px_50px_rgba(242,202,80,0.4)] hover:scale-[1.03] active:scale-95 transition-all">
                    تأكيد الطلب (<?php echo number_format($total); ?> ج.م)
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
