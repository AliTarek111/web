<?php
// inventory.php
// Ahmed Koshary Store - Modern Luxury Inventory Page
$pageTitle = "تصفح المنتجات";
include 'includes/db.php';
include 'includes/header.php';

// Fetch Categories sorted by sort_order
$cat_stmt = $pdo->prepare("SELECT * FROM categories ORDER BY sort_order ASC, id ASC");
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll();

$categories_tree = [];
foreach ($categories as $cat) {
    if (empty($cat['parent_id'])) {
        $cat['children'] = [];
        $categories_tree[$cat['id']] = $cat;
    }
}
foreach ($categories as $cat) {
    if (!empty($cat['parent_id']) && isset($categories_tree[$cat['parent_id']])) {
        $categories_tree[$cat['parent_id']]['children'][] = $cat;
    }
}

// Fetch Categories to build active filtering map if needed, though they want all random by default.
$active_cat_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Fetch Products Randomly
if ($active_cat_id > 0) {
    // If user clicked a category, show only that category but still randomized
    $prod_stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 AND category_id = ? ORDER BY RAND()");
    $prod_stmt->execute([$active_cat_id]);
} else {
    // Show all active products randomly
    $prod_stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 ORDER BY RAND()");
    $prod_stmt->execute();
}
$products = $prod_stmt->fetchAll();
?>

<!-- Hero Section for Inventory -->
<section class="relative pt-32 pb-16 overflow-hidden bg-[#0A0E17]">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-primary/10 via-background to-background"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-block text-primary font-bold tracking-[0.4em] mb-4 text-xs uppercase border-x border-primary px-6 rounded-full py-1 bg-white/5">INVENTORY</span>
            <h1 class="text-5xl md:text-6xl font-black mb-6 leading-tight">
                تصفح <span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">كافة المنتجات</span>
            </h1>
            <p class="text-on-surface-variant/60 text-lg max-w-2xl mx-auto font-medium leading-relaxed">
                اكتشف مجموعة فريدة ومتميزة من أحدث الأجهزة الذكية وملحقاتها بأسعار لا تقبل المنافسة.
            </p>
        </div>
    </div>
</section>

<!-- Dynamic Categories Navigation Bar -->
<section class="sticky top-20 z-40 bg-[#0f131d]/90 backdrop-blur-xl border-b border-white/5 shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
    <div class="container mx-auto px-6">
        <div class="flex flex-wrap md:flex-nowrap overflow-x-auto py-4 gap-4 no-scrollbar items-center md:justify-center">
            <a href="inventory.php" class="shrink-0 flex items-center gap-2 px-6 py-2.5 rounded-full border transition-all text-sm font-bold <?php echo $active_cat_id === 0 ? 'bg-primary border-primary text-on-primary shadow-[0_0_15px_rgba(242,202,80,0.4)]' : 'bg-surface border-white/10 text-on-surface hover:border-primary/50'; ?>">
                <span class="material-symbols-outlined text-[18px]">apps</span>
                الكل
            </a>
            
            <?php foreach ($categories_tree as $parent): 
                $has_children = !empty($parent['children']);
                
                // Check if current active category is this parent or one of its children
                $is_active = ($active_cat_id === $parent['id']);
                if (!$is_active && $has_children) {
                    foreach ($parent['children'] as $child) {
                        if ($active_cat_id === $child['id']) {
                            $is_active = true;
                            break;
                        }
                    }
                }
            ?>
                <div class="relative group shrink-0">
                    <a href="inventory.php?category=<?php echo $parent['id']; ?>" class="flex items-center gap-2 px-6 py-2.5 rounded-full border transition-all text-sm font-bold <?php echo $is_active ? 'bg-primary border-primary text-on-primary shadow-[0_0_15px_rgba(242,202,80,0.4)]' : 'bg-surface border-white/10 text-on-surface hover:border-primary/50'; ?>">
                        <span class="material-symbols-outlined text-[18px]"><?php echo $parent['icon'] ?: 'label'; ?></span>
                        <?php echo htmlspecialchars($parent['name']); ?>
                        <?php if($has_children): ?>
                        <span class="material-symbols-outlined text-[14px]">expand_more</span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if($has_children): ?>
                    <!-- Dropdown for Subcategories -->
                    <div class="absolute right-0 top-full mt-2 w-48 bg-[#0f131d] border border-white/10 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 flex flex-col overflow-hidden">
                        <?php foreach($parent['children'] as $child): ?>
                            <a href="inventory.php?category=<?php echo $child['id']; ?>" class="px-5 py-3 hover:bg-white/5 border-b border-white/5 last:border-b-0 text-sm font-bold flex items-center gap-2 <?php echo $active_cat_id === $child['id'] ? 'text-primary' : 'text-on-surface'; ?> transition-colors">
                                <span class="material-symbols-outlined text-[16px]"><?php echo $child['icon'] ?: 'subdirectory_arrow_left'; ?></span>
                                <?php echo htmlspecialchars($child['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
        </div>
    </div>
</section>

<!-- Randomized Products Grid -->
<section class="py-24 bg-background min-h-screen">
    <div class="container mx-auto px-6">
        <?php if (empty($products)): ?>
            <div class="flex flex-col items-center justify-center text-center space-y-6 py-20">
                <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center text-on-surface-variant/40">
                    <span class="material-symbols-outlined text-5xl">inventory_2</span>
                </div>
                <h3 class="text-2xl font-black text-on-surface">لا توجد منتجات متاحة</h3>
                <p class="text-on-surface-variant/50 max-w-sm">عذراً، لا يوجد منتجات في هذا القسم بالوقت الحالي، يرجى التحقق لاحقاً.</p>
                <a href="inventory.php" class="px-8 py-3 bg-white/5 border border-white/10 rounded-full hover:bg-white/10 transition-colors text-sm font-bold mt-4">عرض كل المنتجات</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($products as $product): ?>
                <div class="product-card glass-card rounded-[2rem] p-5 group hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)] hover:-translate-y-2 transition-all duration-500 relative flex flex-col h-full bg-[#141822] border border-white/5 hover:border-primary/30">
                    <!-- Badges -->
                    <div class="absolute top-6 right-6 z-10 flex flex-col gap-2">
                        <?php if ($product['is_featured']): ?>
                            <span class="bg-primary backdrop-blur-md text-on-primary text-[10px] uppercase tracking-widest font-black px-3 py-1 rounded-full shadow-[0_0_10px_rgba(242,202,80,0.5)]">مميز</span>
                        <?php endif; ?>
                        <?php if ($product['condition_status']): ?>
                            <span class="bg-black/50 backdrop-blur-md text-white text-[9px] uppercase tracking-widest font-bold px-3 py-1 rounded-full border border-white/10"><?php echo str_replace('_', ' ', strtoupper($product['condition_status'])); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Image -->
                    <div class="aspect-[4/5] mb-6 overflow-hidden rounded-2xl bg-[#0a0e18] relative flex items-center justify-center p-4">
                        <?php $img = (!empty($product['main_image']) && strpos($product['main_image'], 'http') === 0) ? $product['main_image'] : ($product['main_image'] ?? ''); ?>
                        <img class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-1000 filter drop-shadow-2xl" src="<?php echo $img; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src=''; this.className='hidden'" />
                        
                        <!-- Overlay Details Button -->
                        <div class="absolute inset-x-0 bottom-4 px-4 cart-btn opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="w-full py-3 bg-white text-black font-bold rounded-xl flex items-center justify-center gap-2 hover:bg-primary transition-colors text-center shadow-lg">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                    
                    <!-- Details -->
                    <div class="px-2 flex-1 flex flex-col">
                        <h3 class="text-lg font-black mb-1 line-clamp-2 leading-snug group-hover:text-primary transition-colors"><?php echo $product['name']; ?></h3>
                        <p class="text-on-surface-variant/50 text-xs mb-6 line-clamp-2 mt-2 leading-relaxed flex-1"><?php echo strip_tags($product['description']); ?></p>
                        
                        <div class="flex justify-between items-end mt-auto pt-4 border-t border-white/5">
                            <div class="flex flex-col">
                                <span class="text-xs text-on-surface-variant/40 font-bold uppercase tracking-widest mb-1">السعر</span>
                                <span class="text-xl font-black text-primary font-headline"><?php echo number_format($product['price']); ?> ج.م</span>
                            </div>
                            <!-- Add to Cart Form -->
                            <form action="cart_actions.php" method="POST" class="m-0 ajax-cart-form relative z-20">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" title="إضافة للسلة" class="w-12 h-12 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary hover:bg-primary hover:text-on-primary hover:scale-110 active:scale-95 transition-all shadow-[0_0_15px_rgba(242,202,80,0)] hover:shadow-[0_0_15px_rgba(242,202,80,0.5)]">
                                    <span class="material-symbols-outlined">shopping_cart_checkout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    /* Hide Scrollbar for category slider but keep functionality */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>

<?php include 'includes/footer.php'; ?>
