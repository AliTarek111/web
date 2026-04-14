<?php
// mobiles.php
// Ahmed Koshary Store - Mobiles Category
include 'includes/db.php';



$pageTitle = "موبيلات";
include 'includes/header.php';
?>

<!-- Header Section -->
<section class="pt-32 pb-16 bg-surface-container-lowest relative overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-30">
        <div class="absolute w-[500px] h-[500px] bg-primary/10 blur-[100px] rounded-full top-0 right-0 translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute w-[400px] h-[400px] bg-primary/5 blur-[80px] rounded-full bottom-0 left-0 -translate-x-1/2 translate-y-1/4"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-5xl md:text-6xl font-black font-headline mb-6 text-on-surface">قسم <span class="text-primary bg-gradient-to-r from-primary to-primary/60 bg-clip-text">الموبيلات</span></h1>
        <p class="text-on-surface-variant/70 text-lg max-w-2xl mx-auto">تصفح أحدث الأجهزة الجديدة والمستعملة المتوفرة لدينا بأفضل الأسعار المتاحة.</p>
    </div>
</section>

<!-- Mobile Categories Cards Section -->
<section class="py-16 bg-surface-container-low/30">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-4xl mx-auto">
            <!-- New Devices Card -->
            <a class="flex flex-col items-center gap-5 group rounded-[2rem] glass-card p-10 hover:border-primary/50 hover:-translate-y-2 transition-all duration-500 overflow-hidden relative shadow-[0_10px_30px_rgba(0,0,0,0.2)] hover:shadow-[0_20px_40px_rgba(242,202,80,0.15)]" href="new_devices.php">
                <div class="absolute inset-0 gold-gradient opacity-0 group-hover:opacity-10 transition-opacity"></div>
                <div class="w-24 h-24 rounded-full bg-primary/10 flex items-center justify-center mb-4 border border-primary/20 group-hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-5xl text-primary" data-weight="fill">new_releases</span>
                </div>
                <h3 class="font-black text-3xl tracking-wide group-hover:text-primary transition-colors text-center text-on-surface">أجهزة جديد</h3>
                <p class="text-on-surface-variant/70 text-center text-sm md:text-base leading-relaxed">تصفح أحدث الهواتف الذكية بضمان معتمد وبحالتها الأصلية.</p>
            </a>
            
            <!-- Used Devices Card -->
            <a class="flex flex-col items-center gap-5 group rounded-[2rem] glass-card p-10 hover:border-primary/50 hover:-translate-y-2 transition-all duration-500 overflow-hidden relative shadow-[0_10px_30px_rgba(0,0,0,0.2)] hover:shadow-[0_20px_40px_rgba(242,202,80,0.15)]" href="used_devices.php">
                <div class="absolute inset-0 gold-gradient opacity-0 group-hover:opacity-10 transition-opacity"></div>
                <div class="w-24 h-24 rounded-full bg-primary/10 flex items-center justify-center mb-4 border border-primary/20 group-hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-5xl text-primary">published_with_changes</span>
                </div>
                <h3 class="font-black text-3xl tracking-wide group-hover:text-primary transition-colors text-center text-on-surface">أجهزة المستعمل</h3>
                <p class="text-on-surface-variant/70 text-center text-sm md:text-base leading-relaxed">تشكيلة من الهواتف المستعملة استعمال خفيف والمفحوصة بدقة لضمان الجودة.</p>
            </a>
        </div>
    </div>
</section>



<?php include 'includes/footer.php'; ?>
