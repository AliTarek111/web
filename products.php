<?php
// products.php
// Ahmed Koshary Store - All Products Directory
include 'includes/db.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1";
$params = [];

if ($category_id > 0) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

$query .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$pageTitle = "جميع المنتجات";
include 'includes/header.php';
?>

<!-- Products Header Section -->
<section class="pt-32 pb-16 bg-surface-container-lowest relative overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-30">
        <div class="absolute w-[500px] h-[500px] bg-primary/10 blur-[100px] rounded-full top-0 right-0 translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute w-[400px] h-[400px] bg-primary/5 blur-[80px] rounded-full bottom-0 left-0 -translate-x-1/2 translate-y-1/4"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-5xl md:text-6xl font-black font-headline mb-6 text-on-surface">المنتجات <span class="text-primary bg-gradient-to-r from-primary to-primary/60 bg-clip-text">الرقمية والمادية</span></h1>
        <p class="text-on-surface-variant/70 text-lg max-w-2xl mx-auto">اكتشف مجموعتنا الكاملة من الأجهزة الذكية والبرمجيات الاحترافية، مختارة بعناية لتناسب احتياجاتك.</p>
    </div>
</section>

<!-- Main Products Section -->
<section class="py-16 bg-surface">
    <div class="container mx-auto px-6 flex flex-col lg:flex-row gap-10">
        
        <!-- Sidebar Filter -->
        <aside class="w-full lg:w-1/4">
            <div class="glass-card p-8 rounded-[2rem] sticky top-32 border border-white/5 shadow-2xl">
                <h3 class="text-sm font-black uppercase tracking-widest text-primary/60 border-b border-primary/10 pb-6 mb-8">تصفية حسب القسم</h3>
                <ul class="space-y-4">
                    <li>
                        <a href="products.php" class="flex items-center justify-between p-4 rounded-xl <?php echo $category_id == 0 ? 'bg-primary/10 border border-primary/20 text-primary' : 'bg-white/5 border border-transparent text-on-surface-variant/70 hover:bg-white/10 hover:text-white'; ?> transition-all">
                            <span class="font-bold text-sm">الكل المتاح</span>
                            <span class="material-symbols-outlined text-sm">category</span>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="products.php?category=<?php echo $cat['id']; ?>" class="flex items-center justify-between p-4 rounded-xl <?php echo $category_id == $cat['id'] ? 'bg-primary/10 border border-primary/20 text-primary' : 'bg-white/5 border border-transparent text-on-surface-variant/70 hover:bg-white/10 hover:text-white'; ?> transition-all">
                            <span class="font-bold text-sm"><?php echo $cat['name']; ?></span>
                            <span class="material-symbols-outlined text-sm"><?php echo $cat['icon']; ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="w-full lg:w-3/4">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                <?php if (empty($products)): ?>
                    <div class="col-span-full p-20 text-center glass-card rounded-[2.5rem] border border-white/5 flex flex-col items-center justify-center gap-6">
                        <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant/40">inventory_2</span>
                        </div>
                        <p class="text-on-surface-variant/60 font-bold uppercase tracking-widest text-sm">لا توجد منتجات متوفرة في هذا القسم حالياً.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <div class="product-card glass-card rounded-[2rem] p-5 group hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)] transition-all duration-500 relative flex flex-col h-full border border-white/5 hover:border-primary/30">
                        <?php if ($product['condition_status'] == 'new'): ?>
                            <div class="absolute top-8 right-8 z-10 bg-primary/20 backdrop-blur-md text-primary text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full border border-primary/30 shadow-lg">جديد</div>
                        <?php endif; ?>
                        
                        <div class="aspect-[4/5] mb-6 overflow-hidden rounded-2xl bg-[#0a0e18] relative group-hover:border-primary/20 transition-all border border-transparent">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 opacity-90 group-hover:opacity-100" src="<?php echo $product['main_image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                            <div class="absolute inset-x-0 bottom-4 px-4 cart-btn opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="w-full py-4 bg-white/90 backdrop-blur-sm text-black font-black uppercase tracking-widest text-[10px] rounded-xl flex items-center justify-center gap-3 hover:bg-primary transition-colors text-center shadow-2xl">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                        
                        <div class="px-2 flex-grow flex flex-col">
                            <div class="flex justify-between items-start mb-3 gap-2">
                                <h3 class="text-xl font-headline font-black line-clamp-2 text-on-surface group-hover:text-primary transition-colors leading-tight"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <?php if ($product['is_featured']): ?>
                                    <span class="material-symbols-outlined text-primary text-xl flex-shrink-0 drop-shadow-[0_0_10px_rgba(242,202,80,0.5)]" style="font-variation-settings: 'FILL' 1;" title="مميز">star</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-on-surface-variant/60 text-xs mb-6 line-clamp-2 flex-grow leading-relaxed"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="flex justify-between items-center mt-auto pt-4 border-t border-white/5">
                                <span class="text-2xl font-black font-headline text-primary tracking-tighter"><?php echo number_format($product['price']); ?> <span class="text-sm font-normal">ج.م</span></span>
                                <span class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover:bg-primary/20 group-hover:text-primary transition-all shadow-inner">
                                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</section>

<?php include 'includes/footer.php'; ?>
