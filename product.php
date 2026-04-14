<?php
// product.php
// Ahmed Koshary Store - Sovereign Product Details Page
include 'includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

// Redirect if not found
if (!$product && $id !== 0) {
    header("Location: index.php");
    exit();
}

// Placeholder for development if DB is completely empty (id=0 case)
if (!$product) {
    $product = [
        'id' => 0,
        'name' => 'Titan X Premium Edition',
        'price' => 2499.00,
        'description' => 'قمة الهندسة والابتكار. مصممة لأصحاب الذوق الرفيع الباحثين عن الأداء المطلق والجماليات المتطورة. كل قطعة تخضع لفحص دقيق لضمان الجودة المثالية.',
        'main_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCGE8Ro3rLGqT_MS4QUntU-MyYmLguHZ28vDwwpvkvsAWhNkeiNjgXA4z5_7F3ZDabwcTCOVsLETc0resxHU30OizwerxH3m6jrvFWcpNLo7tAa5sh4zrqYTqIqtz6voSDLKSIZiTqBOr-04eBnosd91s9uf5KvdfKk-JkoOUe2I-udFDp9s5tbVpOyqfOk14k36dVawSBO1-cot_M1kjjL3kmmXmFr-IdoYhejfgZ_APCJhG5yQNsLPFB_BgfC25UFAT8Agb58Wb4T',
        'condition_status' => 'grade_a',
        'battery_health' => 100,
        'stock_count' => 2
    ];
}

// Fetch real specifications
$spec_stmt = $pdo->prepare("SELECT * FROM product_specs WHERE product_id = ?");
$spec_stmt->execute([$product['id']]);
$specs = $spec_stmt->fetchAll();

// Default values if no specs in DB
if (empty($specs) && $product['id'] == 0) {
    $specs = [
        ['spec_key' => 'Processor', 'spec_value' => 'Bionic Quantum 18 Core'],
        ['spec_key' => 'Ram', 'spec_value' => '24GB DDR5x Modular'],
        ['spec_key' => 'Storage', 'spec_value' => '2TB NVMe Sanctuary Gen 5'],
        ['spec_key' => 'Security', 'spec_value' => 'Bio-Metric Lattice']
    ];
}

// Fetch WhatsApp number from settings
$ws_stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
$ws_stmt->execute();
$whatsapp = $ws_stmt->fetchColumn() ?: '201234567890'; // Fallback

// Prepare WhatsApp message
$wa_message = urlencode("مرحباً أحمد كشري، أريد الاستفسار عن " . $product['name'] . " بسعر " . number_format($product['price']) . " ج.م.");
$wa_link = "https://wa.me/" . $whatsapp . "?text=" . $wa_message;

$pageTitle = $product['name'] . " | تفاصيل المنتج";
include 'includes/header.php';
?>

<!-- Floating Quick Chat -->
<div class="fixed bottom-10 left-10 z-[100]">
    <a href="<?php echo $wa_link; ?>" target="_blank" class="w-16 h-16 gold-gradient rounded-3xl shadow-[0_20px_50px_rgba(242,202,80,0.3)] flex items-center justify-center text-on-primary hover:scale-110 active:scale-95 transition-all group">
        <span class="material-symbols-outlined text-3xl group-hover:rotate-12 transition-transform">chat</span>
    </a>
</div>

<section class="max-w-[1400px] mx-auto px-8 md:px-12 pt-32 pb-24">
    <!-- Breadcrumbs / Path -->
    <nav class="mb-12 flex items-center gap-4 text-[10px] uppercase font-black tracking-[0.3em] text-on-surface-variant/20">
        <a href="index.php" class="hover:text-primary transition-colors">المتجر</a>
        <span class="w-1 h-1 rounded-full bg-white/10"></span>
        <a href="#" class="hover:text-primary transition-colors">الأجهزة المميزة</a>
        <span class="w-1 h-1 rounded-full bg-white/10"></span>
        <span class="text-on-surface/40"><?php echo $product['name']; ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 xl:gap-24">
        <!-- Gallery Section -->
        <div class="lg:col-span-7 space-y-10">
            <div class="relative group aspect-[4/3] rounded-[3rem] overflow-hidden glass-card flex items-center justify-center p-12 lg:p-20 border border-white/5 shadow-3xl bg-[#0f1115]">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
                
                <!-- Main Image -->
                <?php 
                    $img_path = strpos($product['main_image'], 'http') === 0 ? $product['main_image'] : $product['main_image'];
                ?>
                <img src="<?php echo $img_path; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-full object-contain transform group-hover:scale-105 transition-transform duration-1000 drop-shadow-[0_35px_60px_rgba(0,0,0,0.8)]"/>
                
                <!-- Zoom Toggle (Visual) -->
                <button class="absolute bottom-10 right-10 w-12 h-12 rounded-2xl bg-white/5 backdrop-blur-xl border border-white/10 flex items-center justify-center text-on-surface/40 hover:bg-primary/20 hover:text-primary transition-all">
                    <span class="material-symbols-outlined text-xl">zoom_in</span>
                </button>
            </div>

            <!-- Enhanced Thumbnails -->
            <div class="grid grid-cols-4 gap-6 text-center">
                <div class="aspect-square rounded-[1.5rem] border-2 border-primary overflow-hidden bg-white/5 shadow-2xl p-2 cursor-pointer transition-all hover:scale-105">
                    <img src="<?php echo $img_path; ?>" class="w-full h-full object-contain filter drop-shadow-lg"/>
                </div>
                <!-- Icons/Badges for mobile details -->
                <div class="aspect-square rounded-[1.5rem] border border-white/5 overflow-hidden bg-[#0f1115] p-2 flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-primary/40 text-2xl">battery_charging_full</span>
                    <span class="text-[9px] font-black uppercase text-on-surface/40"><?php echo $product['battery_health']; ?>% Health</span>
                </div>
                <div class="aspect-square rounded-[1.5rem] border border-white/5 overflow-hidden bg-[#0f1115] p-2 flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-primary/40 text-2xl">verified</span>
                    <span class="text-[9px] font-black uppercase text-on-surface/40"><?php echo str_replace('_', ' ', strtoupper($product['condition_status'])); ?></span>
                </div>
                <div class="aspect-square rounded-[1.5rem] border border-white/5 overflow-hidden bg-[#0f1115] p-2 flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-primary/40 text-2xl">inventory_2</span>
                    <span class="text-[9px] font-black uppercase text-on-surface/40"><?php echo $product['stock_count']; ?> In Stock</span>
                </div>
            </div>
        </div>

        <!-- Information Section -->
        <div class="lg:col-span-5 space-y-12">
            <div class="space-y-6">
                <span class="inline-block text-primary font-black tracking-[0.4em] text-[10px] uppercase">Professional Tech Store</span>
                <h1 class="text-6xl xl:text-7xl font-black font-headline text-on-surface tracking-tighter leading-[0.9] uppercase"><?php echo $product['name']; ?></h1>
                <div class="pt-4">
                    <p class="text-primary text-5xl font-headline font-black tracking-tighter"><?php echo number_format($product['price']); ?> ج.م</p>
                </div>
            </div>

            <p class="text-on-surface-variant/60 text-lg leading-relaxed font-medium max-w-xl">
                <?php echo $product['description']; ?>
            </p>

            <!-- Dynamic Technical Specs Grid -->
            <?php if (!empty($specs)): ?>
            <div class="space-y-8 pt-10 border-t border-white/10">
                <h3 class="font-headline text-[10px] uppercase tracking-[0.3em] font-black text-on-surface/40">Technical Specifications</h3>
                <div class="grid grid-cols-2 gap-x-12 gap-y-10">
                    <?php foreach ($specs as $spec): ?>
                    <div class="group">
                        <p class="text-on-surface-variant/20 text-[9px] uppercase font-black tracking-widest mb-3 transition-colors group-hover:text-primary"><?php echo htmlspecialchars($spec['spec_key']); ?></p>
                        <p class="text-on-surface font-bold text-sm tracking-tight"><?php echo htmlspecialchars($spec['spec_value']); ?></p>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Hardware Extras if applicable -->
                    <?php if ($product['condition_status']): ?>
                    <div class="group">
                        <p class="text-on-surface-variant/20 text-[9px] uppercase font-black tracking-widest mb-3 transition-colors group-hover:text-primary">Condition</p>
                        <span class="px-3 py-1 bg-primary/10 text-primary text-[9px] font-black tracking-widest uppercase rounded-lg border border-primary/20"><?php echo str_replace('_', ' ', strtoupper($product['condition_status'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-5 pt-8">
                <!-- Add to Cart -->
                <form action="cart_actions.php" method="POST" class="w-full ajax-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full py-7 gold-gradient text-on-primary font-headline font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl shadow-3xl hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-4 group">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                        إضافة للسلة
                    </button>
                </form>
                
                <!-- Quick Order Toggle -->
                <button onclick="document.getElementById('orderForm').scrollIntoView({behavior: 'smooth'})" class="w-full py-7 bg-white/5 border border-white/10 text-on-surface/80 font-headline font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl hover:bg-white/10 hover:border-primary/20 transition-all flex items-center justify-center gap-4 group">
                    <span class="material-symbols-outlined text-lg text-primary">credit_card</span>
                    شراء سريع مباشر
                </button>
                
                <!-- WhatsApp Link -->
                <a href="<?php echo $wa_link; ?>" target="_blank" class="w-full py-7 bg-white/5 border border-white/10 text-on-surface/80 font-headline font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl hover:bg-white/10 hover:border-primary/20 transition-all flex items-center justify-center gap-4 group">
                    <span class="material-symbols-outlined text-xl text-green-500 group-hover:scale-125 transition-transform">chat</span>
                    تواصل عبر واتساب الآن
                </a>
                
                <div class="flex items-center gap-3 justify-center pt-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <p class="text-[9px] font-black uppercase tracking-widest text-on-surface-variant/40"><?php echo $product['stock_count']; ?> Units Remaining in Inventory</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24 pt-24 border-t border-white/5">
        <div class="glass-card p-12 rounded-[2.5rem] text-center space-y-6 hover:bg-white/[0.03] transition-all border border-white/5">
            <div class="w-14 h-14 rounded-2xl gold-gradient mx-auto flex items-center justify-center text-on-primary shadow-2xl">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">verified_user</span>
            </div>
            <div class="space-y-4">
                <h4 class="text-sm font-headline font-black uppercase tracking-widest">3-Month Warranty</h4>
                <p class="text-[10px] text-on-surface-variant/40 leading-relaxed font-bold uppercase tracking-tighter">Full coverage for peace of mind</p>
            </div>
        </div>
        
        <div class="glass-card p-12 rounded-[2.5rem] text-center space-y-6 hover:bg-white/[0.03] transition-all border border-white/5">
            <div class="w-14 h-14 rounded-2xl gold-gradient mx-auto flex items-center justify-center text-on-primary shadow-2xl">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">stars</span>
            </div>
            <div class="space-y-4">
                <h4 class="text-sm font-headline font-black uppercase tracking-widest">Originality Check</h4>
                <p class="text-[10px] text-on-surface-variant/40 leading-relaxed font-bold uppercase tracking-tighter">Verified hardware authenticity</p>
            </div>
        </div>

        <div class="glass-card p-12 rounded-[2.5rem] text-center space-y-6 hover:bg-white/[0.03] transition-all border border-white/5">
            <div class="w-14 h-14 rounded-2xl gold-gradient mx-auto flex items-center justify-center text-on-primary shadow-2xl">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">sync</span>
            </div>
            <div class="space-y-4">
                <h4 class="text-sm font-headline font-black uppercase tracking-widest">Easy Exchange</h4>
                <p class="text-[10px] text-on-surface-variant/40 leading-relaxed font-bold uppercase tracking-tighter">Seamless device trade-ins</p>
            </div>
        </div>
    </div>

    <!-- Hidden Order Form (Triggered by button) -->
    <div id="orderForm" class="pt-32 max-w-3xl mx-auto">
        <div class="glass-card p-12 rounded-[3.5rem] border border-primary/20 shadow-[0_50px_100px_rgba(0,0,0,0.5)]">
            <h3 class="font-headline text-3xl font-black text-primary mb-12 text-center flex items-center justify-center gap-4">
                <span class="material-symbols-outlined text-4xl">inventory_2</span>
                طلب شراء مباشر
            </h3>
            <form action="process_order.php" method="POST" class="space-y-10">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.4em] text-on-surface-variant/40 pr-4">رقم الواتساب للتواصل</label>
                    <input required name="whatsapp" type="text" placeholder="201234567890" class="w-full bg-[#0a0e18] border-white/5 rounded-3xl p-7 text-sm font-bold tracking-widest text-on-surface focus:border-primary transition-all shadow-inner"/>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.4em] text-on-surface-variant/40 pr-4">عنوان التوصيل <span class="text-error/60">*</span></label>
                    <textarea required name="address" placeholder="مثال: ش الجمهورية، برج النيل، شقة 5، الدور 3 - الإسكندرية" class="w-full bg-[#0a0e18] border-white/5 rounded-3xl p-7 text-sm font-bold text-on-surface focus:border-primary transition-all h-36 shadow-inner resize-none"></textarea>
                    <p class="text-[9px] text-on-surface-variant/30 uppercase tracking-widest font-black pr-2">يرجى كتابة العنوان بالتفصيل (الشارع، الرقم، المحافظة)</p>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.4em] text-on-surface-variant/40 pr-4">ملاحظات إضافية (اختياري)</label>
                    <textarea name="notes" placeholder="اللون المطلوب، مواصفات خاصة، أو أي طلبات إضافية..." class="w-full bg-[#0a0e18] border-white/5 rounded-3xl p-7 text-sm font-bold text-on-surface focus:border-primary transition-all h-32 shadow-inner resize-none"></textarea>
                </div>

                <button type="submit" class="w-full py-8 gold-gradient text-on-primary font-headline font-black text-xs uppercase tracking-[0.3em] rounded-3xl shadow-[0_20px_50px_rgba(242,202,80,0.4)] hover:scale-[1.03] active:scale-95 transition-all">
                    تأكيد الطلب الآن
                </button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
