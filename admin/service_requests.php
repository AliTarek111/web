<?php
// admin/service_requests.php
// Ahmed Koshary Store - Service Requests Management
require_once 'includes/auth_middleware.php';
include '../includes/db.php';

// Redirect to DB settings if offline
if ($db_connection_failed || $pdo === null) {
    header("Location: db_settings.php?err=offline");
    exit();
}

$message = "";

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE service_requests SET status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $request_id])) {
        $message = "تم تحديث حالة الطلب بنجاح.";
    }
}

// Fetch requests
$query = "SELECT * FROM service_requests ORDER BY created_at DESC";
$requests = $pdo->query($query)->fetchAll();

$pageTitle = "طلبات الصيانة والاستعجال";
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">قسم الاستعجال</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">طلبات <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">الصيانة والاستبدال</span></h1>
    </div>
</header>

<?php if ($message): ?>
    <div class="mb-12 p-5 glass-card border-primary/20 text-primary text-xs font-black uppercase tracking-widest rounded-2xl shadow-2xl relative z-10 flex items-center gap-4 animate-bounce">
        <span class="material-symbols-outlined">verified</span>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<section class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl space-y-10 relative z-10 overflow-hidden">
    <div class="overflow-x-auto custom-scrollbar pb-6">
        <table class="w-full text-right border-separate border-spacing-y-4">
            <thead>
                <tr>
                    <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10 w-1/4">الزبون والرقم</th>
                    <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10 w-1/6">الجهاز</th>
                    <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10 w-1/3">المشكلة والتصنيف</th>
                    <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10 w-1/6 text-left">الإجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="4" class="py-12 text-center text-sm font-bold text-on-surface-variant/40">لا توجد طلبات استعجال حالياً. الأمور تمام!</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($requests as $req): 
                        // Color styling based on category
                        $cat_color = 'border-white/5';
                        $cat_bg = 'bg-[#0a0e18]';
                        $icon = 'help';
                        $cat_label = '';
                        $text_color = 'text-on-surface';
                        
                        if ($req['main_category'] == 'dead') {
                            $cat_color = 'border-error/30';
                            $cat_bg = 'bg-error/10';
                            $icon = 'build';
                            $cat_label = 'صيانة هاردوير';
                            $text_color = 'text-error';
                        } elseif ($req['main_category'] == 'hanging') {
                            $cat_color = 'border-blue-500/30';
                            $cat_bg = 'bg-blue-500/10';
                            $icon = 'memory';
                            $cat_label = 'سوفت وير';
                            $text_color = 'text-blue-500';
                        } elseif ($req['main_category'] == 'upgrade') {
                            $cat_color = 'border-green-500/30';
                            $cat_bg = 'bg-green-500/10';
                            $icon = 'swap_horiz';
                            $cat_label = 'استبدال وتجديد';
                            $text_color = 'text-green-500';
                        }
                    ?>
                    <tr class="group">
                        <td class="p-6 bg-[#0a0e18] rounded-r-3xl border-y border-r border-white/5 group-hover:border-primary/20 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center bg-surface shrink-0">
                                    <span class="material-symbols-outlined text-primary">person</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface"><?php echo htmlspecialchars($req['customer_name']); ?></p>
                                    <p class="text-xs text-on-surface-variant/60 font-medium tracking-widest mt-1" dir="ltr"><?php echo htmlspecialchars($req['whatsapp_number']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="p-6 bg-[#0a0e18] border-y border-white/5 group-hover:border-primary/20 transition-all">
                            <span class="text-xs font-bold text-on-surface tracking-wide" dir="ltr"><?php echo htmlspecialchars($req['device_model']); ?></span>
                        </td>
                        <td class="p-6 bg-[#0a0e18] border-y border-white/5 group-hover:border-primary/20 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="p-3 rounded-2xl border <?php echo $cat_color; ?> <?php echo $cat_bg; ?> flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined <?php echo $text_color; ?>"><?php echo $icon; ?></span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest <?php echo $text_color; ?>"><?php echo $cat_label; ?></p>
                                    <p class="text-sm font-bold text-on-surface mt-1"><?php echo htmlspecialchars($req['sub_issue']); ?></p>
                                    <?php if (!empty($req['notes'])): ?>
                                        <div class="mt-2 bg-black/30 border border-white/5 rounded-lg p-2 max-w-xs">
                                            <p class="text-[10px] text-primary/40 uppercase font-black mb-1">الملاحظات</p>
                                            <p class="text-[11px] text-on-surface-variant/80 leading-relaxed font-medium"><?php echo nl2br(htmlspecialchars($req['notes'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="p-6 bg-[#0a0e18] rounded-l-3xl border-y border-l border-white/5 group-hover:border-primary/20 transition-all text-left">
                            <form method="POST" class="inline-flex">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                <select name="status" onchange="this.form.submit()" class="bg-[#0f131d] border border-white/10 rounded-xl text-xs font-bold p-3 text-on-surface cursor-pointer focus:border-primary transition-all">
                                    <option value="new" <?php echo $req['status'] == 'new' ? 'selected' : ''; ?>>جديد 🆕</option>
                                    <option value="in_progress" <?php echo $req['status'] == 'in_progress' ? 'selected' : ''; ?>>جاري العمل ⚙️</option>
                                    <option value="completed" <?php echo $req['status'] == 'completed' ? 'selected' : ''; ?>>مكتمل ✅</option>
                                </select>
                            </form>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $req['whatsapp_number']); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-green-500/10 text-green-500 border border-green-500/30 flex items-center justify-center hover:bg-green-500 hover:text-white transition-all ml-2 inline-flex" title="تواصل واتساب">
                                <span class="material-symbols-outlined text-sm">chat</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<style>
.custom-scrollbar::-webkit-scrollbar { height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(242, 202, 80, 0.3); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(242, 202, 80, 0.5); }
</style>

<?php include 'includes/admin_footer.php'; ?>
