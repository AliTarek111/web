<?php
// success.php
// Ahmed Koshary Store - Order Success Page
session_start();
$pageTitle = "تم الطلب بنجاح";
include 'includes/header.php';

$order_id = $_SESSION['success_order_id'] ?? null;
$wa_link = $_SESSION['success_wa_link'] ?? '#';

if (!$order_id) {
    header("Location: index.php");
    exit();
}
?>

<div class="min-h-screen bg-background pt-40 pb-24 flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="glass-card max-w-2xl mx-auto rounded-[3.5rem] p-12 text-center border border-primary/20 shadow-[0_50px_100px_rgba(242,202,80,0.1)] relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent opacity-50 pointer-events-none"></div>
            
            <div class="w-32 h-32 mx-auto gold-gradient rounded-full flex items-center justify-center shadow-[0_0_50px_rgba(242,202,80,0.5)] mb-8 animate-bounce">
                <span class="material-symbols-outlined text-6xl text-on-primary">check_circle</span>
            </div>
            
            <h1 class="text-4xl lg:text-5xl font-headline font-black text-on-surface uppercase tracking-tight mb-6">تم استلام طلبك بنجاح!</h1>
            
            <p class="text-lg text-on-surface-variant/80 tracking-widest font-bold mb-4">
                رقم طلبك هو: <span class="text-primary font-black ml-2 px-4 py-1 bg-white/5 rounded-full border border-white/10">#ORD-<?php echo $order_id; ?></span>
            </p>
            
            <p class="text-sm text-on-surface-variant/60 tracking-wider mb-10 leading-relaxed font-bold max-w-md mx-auto">
                شكراً لتسوقك من أحمد كشري. هنتواصل معاك في أقرب وقت لتأكيد تفاصيل الشحن. 
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                <a href="<?php echo htmlspecialchars($wa_link); ?>" target="_blank" class="px-8 py-5 text-sm gold-gradient text-on-primary font-black uppercase tracking-widest rounded-2xl flex items-center justify-center gap-3 shadow-3xl hover:scale-105 transition-transform w-full sm:w-auto">
                    إرسال التأكيد واتساب
                    <span class="material-symbols-outlined">chat</span>
                </a>
                <a href="index.php" class="px-8 py-5 text-sm bg-white/5 text-on-surface border border-white/10 font-black uppercase tracking-widest rounded-2xl flex items-center justify-center gap-3 hover:bg-white/10 transition-colors w-full sm:w-auto">
                    العودة للمتجر
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
unset($_SESSION['success_order_id']);
unset($_SESSION['success_wa_link']);
include 'includes/footer.php'; 
?>
