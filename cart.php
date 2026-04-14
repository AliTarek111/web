<?php
// cart.php
// Ahmed Koshary Store - Shopping Cart Page
$pageTitle = "سلة المشتريات";
include 'includes/header.php';
include 'includes/db.php';

// Fetch cart items
$cart_items = [];
$total_amount = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $in = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, name, price, main_image, condition_status FROM products WHERE id IN ($in)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $p['quantity'] = $qty;
        $cart_items[] = $p;
        $total_amount += $p['price'] * $qty;
    }
}
?>

<div class="min-h-screen bg-background pt-32 pb-24">
    <div class="container mx-auto px-4 md:px-8 max-w-5xl">
        <header class="mb-12 text-center md:text-right flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-4xl font-headline font-black uppercase tracking-tight text-on-surface">سلة المشتريات</h1>
                <p class="text-on-surface-variant/60 mt-2 font-bold tracking-widest text-sm">راجع المنتجات قبل إتمام الطلب</p>
            </div>
            <?php if (!empty($cart_items)): ?>
            <a href="cart_actions.php?action=clear" class="px-6 py-3 rounded-full border border-error/50 text-error hover:bg-error hover:text-white transition-colors text-sm font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                إفراغ السلة
            </a>
            <?php endif; ?>
        </header>

        <?php if (empty($cart_items)): ?>
            <div class="glass-card p-16 rounded-[3.5rem] border border-white/5 text-center flex flex-col items-center justify-center space-y-6">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center text-on-surface/40 mb-6">
                    <span class="material-symbols-outlined text-5xl">remove_shopping_cart</span>
                </div>
                <h2 class="text-2xl font-black text-on-surface">سلتك فارغة حالياً!</h2>
                <p class="text-on-surface-variant/60">تصفح منتجاتنا وأضف ما يعجبك إلى سلة المشتريات.</p>
                <a href="index.php" class="mt-8 px-10 py-4 gold-gradient text-on-primary font-bold rounded-full hover:scale-105 transition-transform inline-flex items-center gap-2">
                    <span class="material-symbols-outlined">shopping_bag</span>
                    تسوق الآن
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Cart Items List -->
                <div class="lg:col-span-2 space-y-6">
                    <?php foreach ($cart_items as $item): 
                        $img_path = (!empty($item['main_image']) && strpos($item['main_image'], 'http') === 0) ? $item['main_image'] : ($item['main_image'] ?? '');
                    ?>
                    <div class="glass-card p-6 rounded-[2rem] border border-white/5 flex flex-col sm:flex-row gap-6 items-center relative group hover:border-primary/30 transition-all">
                        <div class="w-28 h-28 shrink-0 bg-[#0a0e18] rounded-2xl flex items-center justify-center p-2 border border-white/5">
                            <img src="<?php echo $img_path; ?>" alt="<?php echo $item['name']; ?>" class="w-full h-full object-contain filter drop-shadow-md" />
                        </div>
                        
                        <div class="flex-1 text-center sm:text-right space-y-2">
                            <h3 class="text-xl font-black text-on-surface line-clamp-1"><?php echo $item['name']; ?></h3>
                            <?php if($item['condition_status']): ?>
                            <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase rounded-lg border border-primary/20">
                                <?php echo str_replace('_', ' ', strtoupper($item['condition_status'])); ?>
                            </span>
                            <?php endif; ?>
                            <p class="text-2xl font-black text-primary font-headline mt-2"><?php echo number_format($item['price']); ?> ج.م</p>
                        </div>

                        <div class="flex flex-col items-center gap-4 shrink-0 sm:border-r border-white/10 sm:pr-6">
                            <!-- Quantity Controls -->
                            <form action="cart_actions.php" method="POST" class="flex items-center gap-3 bg-[#0a0e18] p-1.5 rounded-xl border border-white/5">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="quantity" value="<?php echo $item['quantity'] - 1; ?>">
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-white/10 hover:text-white transition-colors" <?php echo $item['quantity'] <= 1 ? 'disabled style="opacity:0.5"' : ''; ?>>
                                    <span class="material-symbols-outlined text-[16px]">remove</span>
                                </button>
                                <span class="w-6 text-center font-bold text-sm"><?php echo $item['quantity']; ?></span>
                            </form>
                            
                            <!-- Hidden + Form to work without JS -->
                            <form action="cart_actions.php" method="POST" class="absolute inset-0 hidden" id="add-qty-<?php echo $item['id']; ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                            </form>
                            <button onclick="document.getElementById('add-qty-<?php echo $item['id']; ?>').submit()" class="absolute right-[4rem] sm:right-auto sm:left-[5.5rem] bottom-8 sm:bottom-auto w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-white/10 hover:text-white transition-colors z-10" style="margin-top:-3rem; margin-right: 4.8rem;">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                            </button>

                            <!-- Remove Button -->
                            <form action="cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="text-error/80 hover:text-error text-sm font-bold flex items-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="glass-card p-8 rounded-[2.5rem] border border-primary/20 sticky top-32 shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
                        <h3 class="text-xl font-headline font-black uppercase tracking-widest text-on-surface mb-8 border-b border-white/10 pb-6">ملخص الطلب</h3>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center text-on-surface-variant">
                                <span>عدد المنتجات</span>
                                <span class="font-bold"><?php echo array_sum($_SESSION['cart']); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-on-surface-variant">
                                <span>الشحن</span>
                                <span class="font-bold text-primary text-sm">يطبق عند الدفع</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-white/10 pt-6 mb-8 flex justify-between items-center">
                            <span class="text-lg font-bold text-on-surface">الإجمالي</span>
                            <span class="text-3xl font-black font-headline text-primary"><?php echo number_format($total_amount); ?> ج.م</span>
                        </div>

                        <a href="checkout.php" class="w-full py-6 gold-gradient text-on-primary font-headline font-black text-sm uppercase tracking-[0.2em] rounded-2xl shadow-3xl hover:scale-[1.02] active:scale-95 transition-all text-center block flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined shrink-0">credit_card</span>
                            الاستمرار لإتمام الدفع
                        </a>
                        
                        <a href="inventory.php" class="w-full py-4 mt-4 bg-white/5 text-on-surface/80 font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-white/10 transition-all text-center block">
                            متابعة التسوق
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
