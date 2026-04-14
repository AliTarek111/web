<?php
// request_service.php
// Ahmed Koshary Store - Smart Service Request System
include 'includes/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['customer_name'] ?? '';
    $phone = $_POST['whatsapp_number'] ?? '';
    $model = $_POST['device_model'] ?? '';
    $category = $_POST['main_category'] ?? '';
    $sub_issue = $_POST['sub_issue'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (!empty($name) && !empty($phone) && !empty($model)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO service_requests (customer_name, whatsapp_number, device_model, main_category, sub_issue, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $model, $category, $sub_issue, $notes]);
            $message = "تم استلام طلبك يا نجم! كشري هيظبطلك الدنيا وهنتواصل معاك في أسرع وقت.";
        } catch (PDOException $e) {
            $message = "حصل خطأ في إرسال الطلب، كلمنا على الواتساب أحسن!";
        }
    } else {
        $message = "يا ريت تملا كل البيانات المطلوبة عشان نقدر نخدمك صح.";
    }
}

$pageTitle = "طلب صيانة أو تشخيص";
include 'includes/header.php';
?>

<!-- Form Header -->
<section class="pt-32 pb-16 bg-surface-container-lowest relative overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-30">
        <div class="absolute w-[500px] h-[500px] bg-primary/10 blur-[100px] rounded-full top-0 left-0 -translate-x-1/2 -translate-y-1/2"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-5xl md:text-6xl font-black font-headline mb-6 text-on-surface">قسم <span class="text-primary bg-gradient-to-r from-primary to-primary/60 bg-clip-text">الاستعجال</span> والتشخيص</h1>
        <p class="text-on-surface-variant/70 text-lg max-w-2xl mx-auto">سيب بياناتك وكشري هيخلص لك الحكاية في ثانية! ⚡🔥</p>
    </div>
</section>

<!-- Main Form Section -->
<section class="py-16 bg-surface relative z-10">
    <div class="container mx-auto px-6 max-w-3xl">
        
        <?php if ($message): ?>
            <div class="mb-12 p-6 glass-card <?php echo strpos($message, 'خطأ') !== false ? 'border-error/20 text-error' : 'border-primary/20 text-primary'; ?> text-sm font-black uppercase tracking-widest rounded-3xl shadow-2xl relative z-10 flex text-center items-center justify-center gap-4 animate-bounce">
                <span class="material-symbols-outlined"><?php echo strpos($message, 'خطأ') !== false ? 'warning' : 'verified'; ?></span>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="glass-card rounded-[3rem] p-10 md:p-16 shadow-2xl border border-white/5 space-y-10 group/form">
            
            <!-- Type Selection (The Core) -->
            <div class="space-y-6 border-b border-primary/10 pb-10">
                <h3 class="text-xl font-black font-headline text-primary mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined rounded-xl bg-primary/10 p-2">vital_signs</span>
                    أيه مشكلة الجهاز بالظبط؟
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="cursor-pointer group">
                        <input type="radio" name="main_category" value="dead" checked class="hidden peer" onchange="updateSubIssues()">
                        <div class="p-6 rounded-3xl border-2 border-white/5 bg-[#0a0e18] text-center peer-checked:border-error peer-checked:bg-error/10 transition-all group-hover:-translate-y-1 relative overflow-hidden">
                            <span class="material-symbols-outlined text-4xl mb-3 text-on-surface-variant/40 peer-checked:text-error transition-colors block">build</span>
                            <span class="text-sm font-black text-on-surface-variant/60 peer-checked:text-error transition-colors">صيانة هاردوير</span>
                            <span class="block text-[10px] uppercase font-bold text-on-surface-variant/30 mt-2">أعطال مادية وإصلاحات</span>
                        </div>
                    </label>

                    <label class="cursor-pointer group">
                        <input type="radio" name="main_category" value="hanging" class="hidden peer" onchange="updateSubIssues()">
                        <div class="p-6 rounded-3xl border-2 border-white/5 bg-[#0a0e18] text-center peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all group-hover:-translate-y-1 relative overflow-hidden">
                            <span class="material-symbols-outlined text-4xl mb-3 text-on-surface-variant/40 peer-checked:text-blue-500 transition-colors block">memory</span>
                            <span class="text-sm font-black text-on-surface-variant/60 peer-checked:text-blue-500 transition-colors">سوفت وير</span>
                            <span class="block text-[10px] uppercase font-bold text-on-surface-variant/30 mt-2">أنظمة تشغيل وتخطيات</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="space-y-6" id="sub_issue_container">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">الحالة التفصيلية</label>
                <select name="sub_issue" id="sub_issue" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-5 text-on-surface transition-all appearance-none cursor-pointer font-bold">
                    <!-- Populated by JS -->
                </select>
            </div>

            <!-- Customer Details -->
            <div class="space-y-8 pt-8">
                <h3 class="text-xl font-black font-headline text-primary mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined rounded-xl bg-primary/10 p-2">contacts</span>
                    بياناتك عشان نكلمك
                </h3>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">اسم الزبون يا أستاذنا</label>
                    <input required name="customer_name" type="text" placeholder="مثال: أحمد محمود" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-6 text-on-surface transition-all"/>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">رقم الواتساب (عشان نتابع معاك لحظة بلحظة)</label>
                    <input required name="whatsapp_number" type="tel" placeholder="مثال: 01012345678" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-6 text-on-surface transition-all font-bold tracking-widest text-left" dir="ltr"/>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">موديل الجهاز</label>
                    <input required name="device_model" type="text" placeholder="مثال: iPhone 13 Pro Max" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-6 text-on-surface transition-all text-left" dir="ltr"/>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">أي ملاحظات إضافية بخصوص العطل (اختياري)</label>
                    <textarea name="notes" rows="4" placeholder="اكتب تفاصيل أكثر لو حابب..." class="w-full bg-[#0a0e18] border-white/5 rounded-3xl focus:border-primary text-sm p-6 text-on-surface transition-all"></textarea>
                </div>
            </div>

            <div class="pt-8 text-center border-t border-primary/10">
                <p class="text-lg font-bold text-on-surface mb-8 italic">"سيب بياناتك وكشري هيخلص لك الحكاية في ثانية! 🚀"</p>
                <button type="submit" class="w-full py-8 gold-gradient text-on-primary font-headline font-black text-sm uppercase tracking-widest rounded-[2rem] shadow-3xl hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-4 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/10 translate-x-full group-hover:translate-x-0 transition-transform skew-x-12 duration-700"></div>
                    <span class="material-symbols-outlined text-xl hover:translate-x-1 transition-transform" style="font-variation-settings: 'FILL' 1;">send</span>
                    إرسال الطلب للمعلم
                </button>
            </div>
        </form>
    </div>
</section>

<script>
const subIssues = {
    'dead': [
        {value: 'broken', text: 'كسر بالشاشة أو الظهر'},
        {value: 'water', text: 'الجهاز وقع في مياه'},
        {value: 'charging', text: 'مشكلة في الشحن'},
        {value: 'sound', text: 'مشكلة في الصوت أو السماعة'},
        {value: 'power', text: 'قاطع باور تماماً'},
        {value: 'other_hw', text: 'هاردوير آخر (أكتبه لمكالمة المتابعة)'}
    ],
    'hanging': [
        {value: 'password', text: 'نسيت الباسورد / فورمات'},
        {value: 'google_frp', text: 'تخطي حساب جوجل / آي كلاود'},
        {value: 'update', text: 'محتاج تحديث نظام'},
        {value: 'language', text: 'تعريب وتنزيل برامج'},
        {value: 'slow', text: 'الجهاز تقيل ومهنج'},
        {value: 'other_sw', text: 'سوفت وير آخر'}
    ]
};

function updateSubIssues() {
    const selectedCategory = document.querySelector('input[name="main_category"]:checked').value;
    const selectBox = document.getElementById('sub_issue');
    
    // Clear current options
    selectBox.innerHTML = '';
    
    // Populate new options
    subIssues[selectedCategory].forEach(issue => {
        const option = document.createElement('option');
        option.value = issue.value;
        option.textContent = issue.text;
        selectBox.appendChild(option);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateSubIssues);
</script>

<?php include 'includes/footer.php'; ?>
