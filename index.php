<?php
// index.php
// Ahmed Koshary Store - Modern Luxury Home Page
include 'includes/db.php';
include 'includes/header.php';

// Prepare featured products query
$query = "SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY created_at DESC";
$products = $pdo->query($query)->fetchAll();
?>

<!-- Hero Section -->
<section class="relative min-h-[921px] flex items-center pt-20 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover opacity-50" data-alt="Luxury display of premium smartphones on dark background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAd86jKoDNdItXXwSdSGwBAnbuKAYlpq1syGfOjstlf5ipvmoJXfwknAFYNCncV9AX9sEiMVBHdaVJQNsjW28nX5GoZRlcEF1KOf71LsW6SDr1c0vGIW7DZy627B3JgtPAk9UtdpYaLav3Wln2mw80y4McNf864Zj04CaYziG5JjHa_vUVHrYls_zbA8W4zjlUGfXwyZl4XzPDPy0VoRmTYjJrjRogwj7UOMWluSlxCpCDb8MGRD_TIb6kXZXpWXQd0YBAu2SaGk4RM" />
        <div class="absolute inset-0 bg-gradient-to-t from-background via-background/40 to-transparent"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl text-right">
            <span class="inline-block text-primary font-bold tracking-[0.3em] mb-6 text-sm uppercase border-r-2 border-primary pr-4">الجودة والاحترافية</span>
            <h1 class="text-5xl md:text-7xl font-black mb-8 leading-tight">
                أحمد كشري.. <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent text-6xl md:text-8xl font-headline font-bold">ملك السوفت وير والهارد وير</span></h1>
            <p class="text-on-secondary-container text-lg md:text-xl max-w-2xl mb-10 font-medium leading-relaxed opacity-90">
                تسوق أحدث الهواتف الذكية العالمية بضمان معتمد، واستمتع بخدمات صيانة احترافية تحت سقف واحد.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#inventory" class="gold-gradient text-on-primary px-10 py-4 rounded-full font-bold text-lg flex items-center gap-3 hover:shadow-[0_0_30px_rgba(242,202,80,0.4)] transition-all transform hover:-translate-y-1">
                    <span class="material-symbols-outlined">shopping_bag</span>
                    تسوق الآن
                </a>
                <a href="#" class="bg-white/5 border border-white/10 backdrop-blur-md text-white px-10 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-all">
                    خدمات الصيانة
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-20 bg-surface-container-low/30">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10">
            <a class="flex flex-col items-center gap-5 group" href="#">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl glass-card flex items-center justify-center group-hover:border-primary/50 group-hover:scale-105 transition-all duration-500 overflow-hidden relative">
                    <div class="absolute inset-0 gold-gradient opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <span class="material-symbols-outlined text-5xl text-primary" data-weight="fill">ios</span>
                </div>
                <span class="font-bold text-xl tracking-wide group-hover:text-primary transition-colors">Apple</span>
            </a>
            <a class="flex flex-col items-center gap-5 group" href="#">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl glass-card flex items-center justify-center group-hover:border-primary/50 group-hover:scale-105 transition-all duration-500 overflow-hidden relative">
                    <div class="absolute inset-0 gold-gradient opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <span class="material-symbols-outlined text-5xl text-primary">smartphone</span>
                </div>
                <span class="font-bold text-xl tracking-wide group-hover:text-primary transition-colors">Samsung</span>
            </a>
            <a class="flex flex-col items-center gap-5 group" href="#">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl glass-card flex items-center justify-center group-hover:border-primary/50 group-hover:scale-105 transition-all duration-500 overflow-hidden relative">
                    <div class="absolute inset-0 gold-gradient opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <span class="material-symbols-outlined text-5xl text-primary">watch</span>
                </div>
                <span class="font-bold text-xl tracking-wide group-hover:text-primary transition-colors">Xiaomi</span>
            </a>
            <a class="flex flex-col items-center gap-5 group" href="#">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl glass-card flex items-center justify-center group-hover:border-primary/50 group-hover:scale-105 transition-all duration-500 overflow-hidden relative">
                    <div class="absolute inset-0 gold-gradient opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <span class="material-symbols-outlined text-5xl text-primary">terminal</span>
                </div>
                <span class="font-bold text-xl tracking-wide group-hover:text-primary transition-colors">Software</span>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section id="inventory" class="py-24 bg-surface">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-16 gap-6">
            <div class="text-center md:text-right">
                <h2 class="text-4xl md:text-5xl font-black mb-4">العروض المميزة</h2>
                <div class="w-32 h-1.5 gold-gradient mx-auto md:mr-0"></div>
            </div>
            <a class="text-primary font-bold flex items-center gap-2 group" href="products.php">
                <span>عرض كافة المنتجات</span>
                <span class="material-symbols-outlined group-hover:translate-x-[-5px] transition-transform">arrow_back</span>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($products)): ?>
                <div class="col-span-full py-20 text-center text-on-surface/40 italic">
                    لا توجد منتجات معروضة حالياً.
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                <div class="product-card glass-card rounded-[2rem] p-5 group hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)] transition-all duration-500 relative">
                    <?php if ($product['condition_status'] == 'new'): ?>
                        <div class="absolute top-6 right-6 z-10 bg-primary/20 backdrop-blur-md text-primary text-xs font-black px-4 py-1.5 rounded-full border border-primary/30">جديد</div>
                    <?php endif; ?>
                    
                    <div class="aspect-[4/5] mb-6 overflow-hidden rounded-2xl bg-surface-container-high relative">
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" src="<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>" />
                        <div class="absolute inset-x-0 bottom-4 px-4 cart-btn">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="w-full py-3 bg-white text-black font-bold rounded-xl flex items-center justify-center gap-2 hover:bg-primary transition-colors text-center">
                                <span class="material-symbols-outlined">visibility</span>
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                    
                    <div class="px-2">
                        <h3 class="text-2xl font-black mb-2"><?php echo $product['name']; ?></h3>
                        <p class="text-secondary text-sm mb-6 opacity-70"><?php echo substr($product['description'], 0, 80); ?>...</p>
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-2xl font-black text-primary"><?php echo number_format($product['price']); ?> ج.م</span>
                            </div>
                            <form action="cart_actions.php" method="POST" class="m-0 ajax-cart-form">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" title="إضافة للسلة" class="w-12 h-12 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary hover:bg-primary hover:text-on-primary transition-all">
                                    <span class="material-symbols-outlined">add_shopping_cart</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Trust Badges Section -->
<section class="py-24 bg-surface-container-lowest">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="flex flex-col items-center text-center gap-6 group">
                <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-all duration-300">
                    <span class="material-symbols-outlined text-4xl" data-weight="fill">verified_user</span>
                </div>
                <div>
                    <h4 class="font-black text-xl mb-2">ضمان لمدة 3 أشهر</h4>
                    <p class="text-secondary/70 max-w-[250px] font-medium leading-relaxed">نوفر ضماناً حقيقياً وشاملاً على كافة خدمات الصيانة وقطع الغيار</p>
                </div>
            </div>
            <div class="flex flex-col items-center text-center gap-6 group">
                <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-all duration-300">
                    <span class="material-symbols-outlined text-4xl" data-weight="fill">new_releases</span>
                </div>
                <div>
                    <h4 class="font-black text-xl mb-2">أصالة المنتجات 100%</h4>
                    <p class="text-secondary/70 max-w-[250px] font-medium leading-relaxed">نلتزم بتوفير قطع غيار أصلية وأجهزة مضمونة المصدر لراحتكم</p>
                </div>
            </div>
            <div class="flex flex-col items-center text-center gap-6 group">
                <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-all duration-300">
                    <span class="material-symbols-outlined text-4xl" data-weight="fill">published_with_changes</span>
                </div>
                <div>
                    <h4 class="font-black text-xl mb-2">سياسة استبدال مرنة</h4>
                    <p class="text-secondary/70 max-w-[250px] font-medium leading-relaxed">نضمن لك حق الاستبدال أو الاسترجاع بكل سهولة ووفق الشروط</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
