<?php
// admin/activity_log.php
// Ahmed Koshary Store - Full Activity Log Page
require_once 'includes/auth_middleware.php';
include '../includes/db.php';
require_once 'includes/activity_logger.php';

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 25;
$offset = ($page - 1) * $per_page;

// Filter
$filter_type = $_GET['type'] ?? '';

$where = '';
$params = [];
if ($filter_type) {
    $where = 'WHERE action_type = ?';
    $params[] = $filter_type;
}

// Total count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM activity_log {$where}");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$total_pages = max(1, ceil($total / $per_page));

// Fetch
$stmt = $pdo->prepare("SELECT * FROM activity_log {$where} ORDER BY created_at DESC LIMIT {$per_page} OFFSET {$offset}");
$stmt->execute($params);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get distinct action types for filter
$types = $pdo->query("SELECT DISTINCT action_type FROM activity_log ORDER BY action_type")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'سجل النشاط الكامل';
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">Activity Log</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">سجل <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">النشاطات</span></h1>
        <p class="text-on-surface/40 text-sm mt-3">كل الأحداث والإجراءات المسجلة في النظام (<?php echo $total; ?> سجل)</p>
    </div>
    <div class="flex items-center gap-4">
        <!-- Filter Dropdown -->
        <form method="GET" class="flex items-center gap-3">
            <select name="type" onchange="this.form.submit()" class="bg-[#0a0e18] border border-white/10 text-on-surface rounded-xl px-5 py-3 text-xs font-bold focus:border-primary transition-all appearance-none cursor-pointer">
                <option value="">كل الأنواع</option>
                <?php foreach ($types as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo $filter_type === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="export_report.php?type=activities" class="flex items-center gap-2 px-6 py-3 glass-card rounded-xl text-xs font-bold uppercase tracking-widest hover:border-primary/40 transition-all border border-white/5">
            <span class="material-symbols-outlined text-sm">download</span>
            تصدير CSV
        </a>
    </div>
</header>

<!-- Activity Table -->
<section class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-3xl relative z-10 mb-10">
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-white/5 text-on-surface-variant/40 text-[10px] uppercase font-black tracking-widest border-b border-white/5">
                    <th class="px-8 py-5">#</th>
                    <th class="px-8 py-5">المستخدم</th>
                    <th class="px-8 py-5">الإجراء</th>
                    <th class="px-8 py-5">التفاصيل</th>
                    <th class="px-8 py-5">IP</th>
                    <th class="px-8 py-5">التوقيت</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($activities)): ?>
                <tr>
                    <td colspan="6" class="px-10 py-24 text-center text-on-surface-variant/20 italic">
                        <span class="material-symbols-outlined text-5xl mb-4 block opacity-30">history</span>
                        لا توجد نشاطات مسجلة بعد.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($activities as $act): ?>
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-8 py-5 text-[10px] font-bold text-primary/60 tracking-widest"><?php echo $act['id']; ?></td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary text-sm"><?php echo get_action_icon($act['action_type']); ?></span>
                            </div>
                            <span class="text-xs font-bold text-on-surface"><?php echo htmlspecialchars($act['username'] ?? 'system'); ?></span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 bg-white/5 text-[9px] font-black uppercase rounded-full border border-white/5 tracking-widest text-on-surface-variant"><?php echo htmlspecialchars($act['action_type']); ?></span>
                    </td>
                    <td class="px-8 py-5 text-xs text-on-surface/70 font-medium max-w-xs truncate"><?php echo htmlspecialchars($act['description']); ?></td>
                    <td class="px-8 py-5 text-[10px] text-on-surface-variant/30 font-mono" dir="ltr"><?php echo htmlspecialchars($act['ip_address'] ?? '—'); ?></td>
                    <td class="px-8 py-5">
                        <div>
                            <p class="text-[10px] text-primary font-bold"><?php echo time_ago_arabic($act['created_at']); ?></p>
                            <p class="text-[9px] text-on-surface-variant/30 mt-1" dir="ltr"><?php echo date('d/m/Y H:i:s', strtotime($act['created_at'])); ?></p>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div class="flex justify-center gap-2 mb-10 relative z-10">
    <?php if ($page > 1): ?>
    <a href="?page=<?php echo $page - 1; ?>&type=<?php echo urlencode($filter_type); ?>" class="px-5 py-3 glass-card rounded-xl text-xs font-bold hover:border-primary/40 transition-all border border-white/5 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
        السابق
    </a>
    <?php endif; ?>
    
    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
    <a href="?page=<?php echo $i; ?>&type=<?php echo urlencode($filter_type); ?>" 
       class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-black transition-all
       <?php echo $i === $page ? 'gold-gradient text-on-primary shadow-lg' : 'glass-card border border-white/5 hover:border-primary/40'; ?>">
        <?php echo $i; ?>
    </a>
    <?php endfor; ?>
    
    <?php if ($page < $total_pages): ?>
    <a href="?page=<?php echo $page + 1; ?>&type=<?php echo urlencode($filter_type); ?>" class="px-5 py-3 glass-card rounded-xl text-xs font-bold hover:border-primary/40 transition-all border border-white/5 flex items-center gap-2">
        التالي
        <span class="material-symbols-outlined text-sm">arrow_back</span>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>
