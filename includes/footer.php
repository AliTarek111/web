<?php
// includes/footer.php
// Global footer for Ahmed Koshary Store - Modern Luxury Edition
?>
</main> <!-- End Main Content from Header -->

<!-- Footer -->
<footer class="bg-[#0a0e18] w-full border-t border-[#4d4635]/20 pt-20 pb-10">
    <div class="container mx-auto px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-4 mb-6">
                    <img src="logo.png/screen.png" alt="Ahmed Koshary Logo" class="w-12 h-12 object-contain filter brightness-110"/>
                    <div class="text-3xl font-black text-primary font-space">Ahmed Koshary</div>
                </div>
                <p class="text-on-surface/60 font-medium mb-8 max-w-sm">عالمك المتكامل للهواتف الذكية والصيانة الاحترافية. نسعى دائماً لتقديم الأفضل لعملائنا.</p>
                <div class="flex gap-4">
                    <a class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center hover:bg-primary hover:text-on-primary transition-all text-on-surface" href="#">
                        <span class="material-symbols-outlined text-lg">public</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center hover:bg-primary hover:text-on-primary transition-all text-on-surface" href="#">
                        <span class="material-symbols-outlined text-lg">share</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center hover:bg-primary hover:text-on-primary transition-all text-on-surface" href="#">
                        <span class="material-symbols-outlined text-lg">person_pin</span>
                    </a>
                </div>
            </div>
            <div>
                <h5 class="font-black text-xl mb-8 text-on-surface">روابط سريعة</h5>
                <ul class="space-y-4">
                    <li><a class="text-on-surface/60 hover:text-primary transition-colors text-sm" href="index">الرئيسية</a></li>
                    <li><a class="text-on-surface/60 hover:text-primary transition-colors text-sm" href="index.php#inventory">كافة المنتجات</a></li>
                    <li><a class="text-on-surface/60 hover:text-primary transition-colors text-sm" href="#">سياسة الخصوصية</a></li>
                    <li><a class="text-on-surface/60 hover:text-primary transition-colors text-sm" href="#">الشروط والأحكام</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-black text-xl mb-8 text-on-surface">خدمة العملاء</h5>
                <ul class="space-y-4">
                    <li><a class="text-on-surface/60 hover:text-primary transition-colors text-sm" href="request_service">طلب صيانة</a></li>
                    <li><a class="text-on-surface/60 hover:text-primary transition-colors text-sm" href="#">اتصل بنا</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-black text-xl mb-8 text-on-surface">نقبل الدفع عبر</h5>
                <div class="grid grid-cols-3 gap-4">
                    <div class="h-10 bg-surface-container rounded flex items-center justify-center border border-white/5"><span class="material-symbols-outlined text-on-surface/40">credit_card</span></div>
                    <div class="h-10 bg-surface-container rounded flex items-center justify-center border border-white/5"><span class="material-symbols-outlined text-on-surface/40">account_balance_wallet</span></div>
                    <div class="h-10 bg-surface-container rounded flex items-center justify-center border border-white/5"><span class="material-symbols-outlined text-on-surface/40">payments</span></div>
                </div>
            </div>
        </div>
        <div class="pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6 text-on-surface">
            <div class="text-on-surface/40 text-sm font-medium">
                © 2026 أحمد كشري. جميع الحقوق محفوظة.
            </div>
            <div class="flex gap-6">
                <span class="text-on-surface/60 text-xs tracking-widest font-space" dir="ltr">Developed by <a href="https://wa.me/201210042099" target="_blank" class="text-primary font-bold hover:text-[#d4af37] transition-colors" dir="ltr">Ali Tarek</a></span>
            </div>
        </div>
    </div>
</footer>

<!-- Floating WhatsApp -->
<?php
// Fetch WhatsApp number from settings for global button
if (isset($pdo)) {
    $st_ft_ws = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'")->fetchColumn();
}
$ft_whatsapp = $st_ft_ws ?? '201234567890';
?>
<a class="fixed bottom-8 right-8 w-16 h-16 gold-gradient rounded-full shadow-[0_10px_40px_rgba(242,202,80,0.3)] flex items-center justify-center text-on-primary z-50 hover:scale-110 hover:-rotate-12 transition-transform" href="https://wa.me/<?php echo $ft_whatsapp; ?>" target="_blank">
    <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.748-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217s.231.006.332.013c.105.007.246-.04.385.292.144.346.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.274.072.376-.043.101-.116.433-.506.548-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564c.173.087.289.13.332.202.043.072.043.419-.101.824z"></path>
    </svg>
</a>

</body>
</html>
