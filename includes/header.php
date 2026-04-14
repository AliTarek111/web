<?php
// includes/header.php
// Global header for Ahmed Koshary Store - Modern Luxury Edition
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html class="dark" dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo isset($pageTitle) ? $pageTitle . " | أحمد كشري" : "أحمد كشري - خبير الحلول التقنية المتكاملة"; ?></title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700&family=Inter:wght@400;500;700&family=Work+Sans:wght@400;500;700&family=Cairo:wght@400;600;700;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class", 
            theme: {
                extend: {
                    colors: {
                        background: "#131313", 
                        "surface-dim": "#131313", 
                        "surface-variant": "#353534", 
                        "on-error": "#690005", 
                        "outline-variant": "#4d4635", 
                        "on-secondary-container": "#b5b5b5", 
                        "on-surface-variant": "#d0c5af", 
                        surface: "#131313", 
                        "primary-fixed": "#ffe088", 
                        "inverse-surface": "#e5e2e1", 
                        "error-container": "#93000a", 
                        "on-tertiary-fixed": "#1b1c1c", 
                        "tertiary-container": "#b4b2b2", 
                        "on-error-container": "#ffdad6", 
                        "surface-bright": "#393939", 
                        "secondary-fixed": "#e3e2e2", 
                        "on-tertiary-container": "#454545", 
                        "on-primary-fixed-variant": "#574500", 
                        secondary: "#c7c6c6", 
                        "surface-container": "#201f1f", 
                        primary: "#f2ca50", 
                        "on-secondary-fixed-variant": "#464747", 
                        "on-primary": "#3c2f00", 
                        outline: "#99907c", 
                        "inverse-primary": "#735c00", 
                        "inverse-on-surface": "#313030", 
                        "tertiary-fixed-dim": "#c8c6c5", 
                        error: "#ffb4ab", 
                        "primary-container": "#d4af37", 
                        "surface-tint": "#e9c349", 
                        "surface-container-lowest": "#0e0e0e", 
                        "surface-container-low": "#1c1b1b", 
                        "on-secondary-fixed": "#1b1c1c", 
                        tertiary: "#d0cdcd", 
                        "on-surface": "#e5e2e1", 
                        "on-primary-fixed": "#241a00", 
                        "on-background": "#e5e2e1", 
                        "secondary-fixed-dim": "#c7c6c6", 
                        "secondary-container": "#464747", 
                        "surface-container-high": "#2a2a2a", 
                        "primary-fixed-dim": "#e9c349", 
                        "surface-container-highest": "#353534", 
                        "on-primary-container": "#554300", 
                        "on-secondary": "#303031", 
                        "tertiary-fixed": "#e5e2e1", 
                        "on-tertiary-fixed-variant": "#474746", 
                        "on-tertiary": "#303030"
                    }, 
                    borderRadius: {
                        DEFAULT: "0.125rem", lg: "0.25rem", xl: "0.5rem", full: "0.75rem"
                    }, 
                    fontFamily: {
                        headline: ["Cairo", "Plus Jakarta Sans"], 
                        body: ["Cairo", "Inter"], 
                        label: ["Work Sans"], 
                        space: ["Space Grotesk", "sans-serif"], 
                        display: "Plus Jakarta Sans"
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0f131d; color: #dfe2f1; }
        .glass-card { background: rgba(49, 53, 64, 0.4); backdrop-filter: blur(16px); border: 1px solid rgba(242, 202, 80, 0.1); }
        .gold-gradient { background: linear-gradient(135deg, #f2ca50 0%, #d4af37 100%); }
        .nav-link { position: relative; transition: color 0.3s ease; }
        .nav-link::after { content: ''; position: absolute; bottom: -4px; right: 0; width: 0; height: 2px; background: #f2ca50; transition: width 0.3s ease; }
        .nav-link:hover::after { width: 100%; }
        .product-card:hover .cart-btn { transform: translateY(0); opacity: 1; }
        .cart-btn { transform: translateY(10px); opacity: 0; transition: all 0.3s ease; }
        .selection-gold::selection { background: #f2ca50; color: #3c2f00; }
    </style>
</head>
<body class="bg-background text-on-background overflow-x-hidden selection-gold">

<!-- Header Navigation -->
<nav class="fixed top-0 w-full z-50 bg-[#0f131d]/90 backdrop-blur-xl border-b border-primary/10">
    <div class="container mx-auto px-4 md:px-8 h-20 flex items-center justify-between gap-4">
        <!-- Logo -->
        <a class="flex items-center gap-3 bg-gradient-to-br from-[#f2ca50] to-[#d4af37] bg-clip-text text-transparent font-space shrink-0" href="index.php">
            <img src="logo.png/screen.png" alt="Ahmed Koshary" class="w-10 h-10 object-contain drop-shadow-lg"/>
            <span class="text-2xl font-black">أحمد كشري</span>
        </a>
        
        <!-- Desktop Nav -->
        <div class="hidden lg:flex items-center gap-8">
            <a class="nav-link font-bold <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-primary' : 'text-on-surface/80 hover:text-primary'; ?>" href="index.php">الرئيسية</a>
            <a class="nav-link font-medium <?php echo basename($_SERVER['PHP_SELF']) == 'mobiles.php' ? 'text-primary' : 'text-on-surface/80 hover:text-primary'; ?>" href="mobiles.php">موبيلات</a>
            <a class="nav-link font-medium <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'text-primary' : 'text-on-surface/80 hover:text-primary'; ?>" href="inventory.php">كافة المنتجات</a>
            <a class="nav-link font-medium text-on-surface/80 hover:text-primary" href="request_service.php">خدمات الصيانة</a>
            <?php if (isset($_SESSION['admin_logged_in']) && in_array($_SESSION['role'], ['admin', 'super_admin'])): ?>
                <a class="nav-link font-bold text-primary border-r-2 border-primary pr-4 mr-2" href="admin/index.php">لوحة الإدارة</a>
            <?php endif; ?>
        </div>

        <!-- Search Bar -->
        <div class="hidden md:flex flex-1 max-w-md relative group">
            <input class="w-full bg-surface-container border-none rounded-full py-2.5 pr-12 pl-4 text-sm focus:ring-1 focus:ring-primary/50 transition-all text-on-surface" placeholder="ابحث عن هاتف، إكسسوار، أو خدمة..." type="text"/>
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary">search</span>
        </div>

        <!-- Icons -->
        <div class="flex items-center gap-2 md:gap-5">
            <?php if (isset($_SESSION['admin_logged_in']) && in_array($_SESSION['role'], ['admin', 'super_admin'])): ?>
                <a href="admin/index.php" class="flex items-center justify-center p-2 hover:bg-white/5 rounded-full transition-colors text-primary border border-primary/20 bg-primary/5 shadow-[0_0_15px_rgba(242,202,80,0.1)]">
                    <span class="material-symbols-outlined text-2xl">shield_person</span>
                </a>
            <?php endif; ?>
            <button onclick="openCart()" class="relative flex items-center justify-center p-2 hover:bg-white/5 rounded-full transition-colors text-on-surface">
                <span class="material-symbols-outlined text-2xl">shopping_cart</span>
                <span id="cart-counter" class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-on-primary text-[10px] font-bold rounded-full flex items-center justify-center shadow-[0_0_10px_rgba(242,202,80,0.5)]"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
            </button>
            <button onclick="toggleMobileMenu()" class="lg:hidden flex items-center justify-center p-2 text-on-surface hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div id="mobileMenuOverlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-black/60 z-[60] hidden lg:hidden backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

<!-- Mobile Sidebar -->
<aside id="mobileSidebar" class="w-72 bg-surface/95 backdrop-blur-xl border-l border-white/10 flex flex-col pt-20 pb-10 px-6 fixed h-full right-0 top-0 z-[70] transition-transform duration-300 transform translate-x-full lg:hidden">
    <button onclick="toggleMobileMenu()" class="absolute top-6 left-6 w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-on-surface hover:text-primary transition-colors hover:bg-error/10 hover:text-error">
        <span class="material-symbols-outlined">close</span>
    </button>

    <div class="flex flex-col gap-6 mt-4">
        <a class="nav-link text-lg font-bold <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-primary' : 'text-on-surface'; ?>" href="index.php">الرئيسية</a>
        <a class="nav-link text-lg font-bold <?php echo basename($_SERVER['PHP_SELF']) == 'mobiles.php' ? 'text-primary' : 'text-on-surface'; ?>" href="mobiles.php">موبيلات</a>
        <a class="nav-link text-lg font-bold <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'text-primary' : 'text-on-surface hover:text-primary'; ?> transition-colors" href="inventory.php">كافة المنتجات</a>
        <a class="nav-link text-lg font-bold text-on-surface hover:text-primary transition-colors" href="request_service.php">خدمات الصيانة</a>
        <?php if (isset($_SESSION['admin_logged_in']) && in_array($_SESSION['role'], ['admin', 'super_admin'])): ?>
            <div class="w-full h-px bg-white/10 my-2"></div>
            <a class="flex items-center gap-2 text-lg font-bold text-primary hover:text-primary-container transition-colors" href="admin/index.php">
                <span class="material-symbols-outlined">admin_panel_settings</span>
                لوحة الإدارة
            </a>
        <?php endif; ?>
    </div>
</aside>

<script>
    function toggleMobileMenu() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('mobileMenuOverlay');
        
        if (sidebar.classList.contains('translate-x-full')) {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => { overlay.classList.remove('opacity-0'); overlay.classList.add('opacity-100'); }, 10);
        } else {
            sidebar.classList.add('translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            setTimeout(() => { overlay.classList.add('hidden'); }, 300);
        }
    }

    // AJAX Shopping Cart Logic
    function openCart() {
        fetchCart();
        const sidebar = document.getElementById('ajaxCartSidebar');
        const overlay = document.getElementById('ajaxCartOverlay');
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => { overlay.classList.remove('opacity-0'); overlay.classList.add('opacity-100'); }, 10);
    }
    
    function closeCart() {
        const sidebar = document.getElementById('ajaxCartSidebar');
        const overlay = document.getElementById('ajaxCartOverlay');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0');
        setTimeout(() => { overlay.classList.add('hidden'); }, 300);
    }

    async function fetchCart() {
        try {
            const res = await fetch('cart_actions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'get'})
            });
            const data = await res.json();
            updateCartUI(data);
        } catch(e) { console.error(e); }
    }

    async function submitCartAction(action, product_id, quantity = 1) {
        if (quantity < 1 && action === 'update') {
            action = 'remove';
        }
        try {
            const res = await fetch('cart_actions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action, product_id, quantity})
            });
            const data = await res.json();
            updateCartUI(data);
            if(action === 'add') showCartSuccessModal();
        } catch(e) { console.error(e); }
    }

    function updateCartUI(data) {
        if(data.status !== 'success') return;
        
        document.getElementById('cart-counter').innerText = data.cart_count;
        document.getElementById('ajaxCartTotal').innerText = data.total.toLocaleString() + ' ج.م';
        
        const container = document.getElementById('ajaxCartItems');
        if(data.items.length === 0) {
            container.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-center opacity-50 space-y-4 pt-20">
                    <span class="material-symbols-outlined text-6xl">remove_shopping_cart</span>
                    <p class="font-bold">سلة المشتريات فارغة</p>
                </div>`;
            return;
        }

        let html = '';
        data.items.forEach(item => {
            let safeImg = item.main_image ? item.main_image : '';
            let img = safeImg.startsWith('http') ? safeImg : safeImg;
            html += `
            <div class="flex gap-4 items-center bg-white/5 p-3 rounded-2xl border border-white/5 relative group hover:border-primary/20 transition-all">
                <img src="${img}" class="w-16 h-16 object-contain bg-[#0a0e18] p-2 rounded-xl border border-white/5" onerror="this.src=''; this.className='hidden'" />
                <div class="flex-1">
                    <h4 class="text-sm font-bold line-clamp-1">${item.name}</h4>
                    <p class="text-primary font-black text-sm mb-2">${Number(item.price).toLocaleString()} ج.م</p>
                    <div class="flex items-center gap-2 bg-[#0a0e18] w-max rounded-lg border border-white/5 px-1 py-1">
                        <button onclick="submitCartAction('update', ${item.id}, ${item.quantity - 1})" class="text-on-surface-variant hover:text-white px-2 rounded hover:bg-white/10">-</button>
                        <span class="text-xs font-bold w-4 text-center">${item.quantity}</span>
                        <button onclick="submitCartAction('update', ${item.id}, ${item.quantity + 1})" class="text-on-surface-variant hover:text-white px-2 rounded hover:bg-white/10">+</button>
                    </div>
                </div>
                <button onclick="submitCartAction('remove', ${item.id})" class="absolute top-2 left-2 w-7 h-7 flex items-center justify-center rounded-lg bg-error/10 text-error hover:bg-error hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[14px]">delete</span>
                </button>
            </div>
            `;
        });
        container.innerHTML = html;
    }

    // Hijack forms logic globally
    document.addEventListener('DOMContentLoaded', () => {
        fetchCart();
        document.body.addEventListener('submit', (e) => {
            const form = e.target;
            if(form.classList.contains('ajax-cart-form')) {
                e.preventDefault();
                const fd = new FormData(form);
                submitCartAction(fd.get('action'), fd.get('product_id'), fd.get('quantity') || 1);
            }
        });
    });
</script>

<!-- Ajax Cart Overlay -->
<div id="ajaxCartOverlay" onclick="closeCart()" class="fixed inset-0 bg-black/60 z-[80] hidden backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

<!-- Ajax Cart Sidebar -->
<aside id="ajaxCartSidebar" class="w-full md:w-[400px] bg-surface/95 backdrop-blur-xl border-r md:border-l border-white/10 flex flex-col fixed h-full left-0 z-[90] transition-transform duration-300 transform -translate-x-full">
    <div class="p-6 border-b border-white/5 flex items-center justify-between">
        <h2 class="text-xl font-headline font-black uppercase text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">shopping_cart</span>
            سلة المشتريات
        </h2>
        <button onclick="closeCart()" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-on-surface hover:text-error hover:bg-error/10 transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    
    <div id="ajaxCartItems" class="flex-1 overflow-y-auto p-6 space-y-4">
        <!-- JS Populated -->
    </div>
    
    <div class="p-6 border-t border-white/5 space-y-4 bg-[#0a0e18]">
        <div class="flex justify-between items-center text-lg">
            <span class="font-bold text-on-surface-variant/70">الإجمالي</span>
            <span id="ajaxCartTotal" class="font-black text-primary font-headline text-2xl">0 ج.م</span>
        </div>
        <a href="checkout.php" class="w-full py-5 gold-gradient text-on-primary font-black text-sm uppercase tracking-widest rounded-xl text-center shadow-[0_10px_20px_rgba(242,202,80,0.2)] hover:scale-[1.02] active:scale-95 transition-all block">الاستمرار للدفع</a>
        <a href="cart.php" class="w-full py-4 bg-white/5 border border-white/10 text-white font-black text-xs uppercase tracking-widest rounded-xl text-center hover:bg-white/10 transition-all block">عرض سلة المشتريات</a>
    </div>
</aside>

<!-- Cart Success Modal -->
<div id="cartSuccessModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-md px-4 transition-opacity duration-300 opacity-0">
    <div class="bg-[#0f131d] border border-primary/20 rounded-[2.5rem] p-8 max-w-sm w-full shadow-[0_20px_50px_rgba(0,0,0,0.5)] text-center transform scale-95 transition-transform duration-300">
        <div class="w-20 h-20 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-4xl">check_circle</span>
        </div>
        <h3 class="text-2xl font-black text-on-surface mb-2">تمت الإضافة بنجاح</h3>
        <p class="text-on-surface-variant/60 font-bold mb-8 text-sm">تم إضافة المنتج إلى سلة مشترياتك. هل تود إتمام الطلب الآن؟</p>
        
        <div class="flex flex-col gap-3">
            <a href="cart.php" class="w-full py-4 gold-gradient text-on-primary font-black uppercase tracking-widest rounded-2xl shadow-xl hover:scale-105 active:scale-95 transition-all text-sm">عرض السلة</a>
            <button onclick="hideCartSuccessModal()" class="w-full py-4 bg-white/5 border border-white/10 text-on-surface hover:text-white font-bold tracking-widest rounded-2xl hover:bg-white/10 transition-colors text-sm">متابعة التسوق</button>
        </div>
    </div>
</div>

<script>
    function showCartSuccessModal() {
        const modal = document.getElementById('cartSuccessModal');
        const content = modal.querySelector('div');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }
    
    function hideCartSuccessModal() {
        const modal = document.getElementById('cartSuccessModal');
        const content = modal.querySelector('div');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>

<main class="min-h-screen pt-20">
