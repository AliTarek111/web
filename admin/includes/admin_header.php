<?php
// admin/includes/admin_header.php
// Global Admin Header for Ahmed Koshary Store - Boss Panel
?>
<!DOCTYPE html>
<html class="dark" dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo isset($pageTitle) ? $pageTitle . " | لوحة الإدارة" : "لوحة الإدارة | أحمد كشري"; ?></title>
    
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
                        background: "#0a0e18", 
                        surface: "#131313", 
                        primary: "#f2ca50", 
                        "on-primary": "#3c2f00", 
                        "outline-variant": "#4d4635"
                    },
                    fontFamily: {
                        headline: ["Cairo", "Plus Jakarta Sans"], 
                        body: ["Cairo", "Inter"]
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0a0e18; color: #dfe2f1; }
        .glass-sidebar { background: rgba(19, 19, 19, 0.8); backdrop-filter: blur(20px); border-left: 1px solid rgba(242, 202, 80, 0.05); }
        .gold-gradient { background: linear-gradient(135deg, #f2ca50 0%, #d4af37 100%); }
        .sidebar-link { position: relative; transition: all 0.3s ease; }
        .sidebar-link:hover { background: rgba(242, 202, 80, 0.05); color: #f2ca50; }
        .sidebar-active { background: rgba(242, 202, 80, 0.08); color: #f2ca50; font-weight: 900; }
        .sidebar-active::after { content: ''; position: absolute; right: 0; top: 20%; height: 60%; width: 3px; background: #f2ca50; border-radius: 0 4px 4px 0; }
        .stat-card { background: rgba(19, 19, 19, 0.4); border: 1px solid rgba(255, 255, 255, 0.03); transition: all 0.3s ease; }
        .stat-card:hover { border-color: rgba(242, 202, 80, 0.2); transform: translateY(-5px); }
    </style>
</head>
<body class="flex min-h-screen">

<!-- Mobile Sidebar Overlay -->
<div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

<!-- Sidebar -->
<aside id="adminSidebar" class="w-72 glass-sidebar flex flex-col py-10 fixed h-full z-50 transition-transform duration-300 transform translate-x-full lg:translate-x-0 right-0">
    <div class="px-10 mb-16 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <img src="../logo.png/screen.png" alt="Ahmed Koshary Logo" class="w-10 h-10 object-contain brightness-110 drop-shadow-[0_0_15px_rgba(242,202,80,0.3)]"/>
            <div>
                <h2 class="text-xl font-black text-[#f2ca50] tracking-tighter uppercase font-headline leading-tight">The Admin Panel</h2>
                <p class="text-[#8b949e] text-[8px] uppercase tracking-[0.2em] font-bold opacity-40">Tech Security</p>
            </div>
        </div>
        <button onclick="toggleSidebar()" class="lg:hidden text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    
    <nav class="flex-1 space-y-3 px-4">
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']); 
        $is_super = ($_SESSION['role'] ?? '') === 'super_admin';
        
        // Get staff allowed pages from DB (cache in session for sidebar)
        $staff_allowed_pages = null;
        if (!$is_super && isset($pdo) && $pdo !== null) {
            $permStmt = $pdo->prepare("SELECT allowed_pages FROM users WHERE id = ?");
            $permStmt->execute([$_SESSION['admin_id'] ?? 0]);
            $permData = $permStmt->fetchColumn();
            if ($permData) {
                $staff_allowed_pages = json_decode($permData, true);
            }
        }
        
        $links = [
            ['index.php', 'dashboard', 'لوحة القيادة'],
            ['activity_log.php', 'history', 'سجل النشاط'],
            ['categories.php', 'category', 'إدارة الأقسام'],
            ['manage_devices.php', 'smartphone', 'أجهزة الموبايلات'],
            ['inventory.php', 'inventory_2', 'المخزون'],
            ['service_requests.php', 'build', 'طلبات الاستعجال'],
            ['orders.php', 'shopping_cart', 'الطلبات'],
            ['customers.php', 'group', 'العملاء'],
            ['settings.php', 'settings', 'إعدادات المتجر'],
            ['db_settings.php', 'storage', 'إعدادات قاعدة البيانات'],
            ['backup.php', 'cloud_download', 'النسخ الاحتياطي'],
        ];
        foreach ($links as $link):
            // Skip pages staff doesn't have access to
            if (!$is_super && $staff_allowed_pages !== null && !in_array($link[0], $staff_allowed_pages)) continue;
            $active = ($current_page == $link[0]) ? 'sidebar-active' : 'sidebar-link text-[#8b949e]';
        ?>
        <a href="<?php echo $link[0]; ?>" class="flex items-center gap-4 px-6 py-4 rounded-xl <?php echo $active; ?>">
            <span class="material-symbols-outlined text-xl"><?php echo $link[1]; ?></span>
            <span class="text-sm tracking-wide"><?php echo $link[2]; ?></span>
        </a>
        <?php endforeach; ?>

        <?php if (($_SESSION['role'] ?? '') === 'super_admin'): ?>
        <div class="mt-4 pt-4 border-t border-white/5">
            <p class="px-6 text-[8px] uppercase tracking-[0.25em] text-primary/20 font-black mb-3">Super Admin</p>
            <a href="staff.php" class="flex items-center gap-4 px-6 py-4 rounded-xl <?php echo ($current_page == 'staff.php') ? 'sidebar-active' : 'sidebar-link text-[#8b949e]'; ?>">
                <span class="material-symbols-outlined text-xl">manage_accounts</span>
                <span class="text-sm tracking-wide">إدارة الموظفين</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <div class="px-8 mt-auto pt-10">
        <a href="logout.php" class="flex items-center gap-4 px-6 py-4 rounded-xl text-error/60 hover:bg-error/5 hover:text-error transition-all">
            <span class="material-symbols-outlined text-xl">logout</span>
            <span class="text-sm font-bold">تسجيل الخروج</span>
        </a>
    </div>
</aside>

<!-- Content Area -->
<main class="flex-1 lg:mr-72 p-6 md:p-12 lg:p-20 relative w-full lg:w-auto">
    <!-- Header Decor -->
    <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-primary/5 to-transparent pointer-events-none"></div>

    <!-- Mobile Header -->
    <div class="lg:hidden flex items-center justify-between w-full mb-8 relative z-10 bg-surface/80 p-4 rounded-2xl border border-white/5 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl">admin_panel_settings</span>
            <h1 class="text-lg font-bold font-headline">لوحة الإدارة</h1>
        </div>
        <button onclick="toggleSidebar()" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-on-surface hover:text-primary hover:bg-primary/10 transition-colors">
            <span class="material-symbols-outlined">menu</span>
        </button>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('translate-x-full')) {
                // Open Sidebar
                sidebar.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
                // timeout to allow display block to render before opacity transition
                setTimeout(() => { overlay.classList.remove('opacity-0'); overlay.classList.add('opacity-100'); }, 10);
            } else {
                // Close Sidebar
                sidebar.classList.add('translate-x-full');
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                setTimeout(() => { overlay.classList.add('hidden'); }, 300);
            }
        }
    </script>
