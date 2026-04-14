<?php
// admin/categories.php
// Ahmed Koshary Store - Categories Management
require_once 'includes/auth_middleware.php';
include '../includes/db.php';
require_once 'includes/activity_logger.php';

$message = '';
$message_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $icon = trim($_POST['icon'] ?? 'token');
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (empty($name) || empty($slug)) {
            $message = 'الاسم والـ Slug مطلوبان.';
            $message_type = 'error';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $message = 'هذا الـ Slug موجود مسبقاً.';
                $message_type = 'error';
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, parent_id) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$name, $slug, $icon, $parent_id])) {
                    log_activity($pdo, 'category_add', "تم إضافة قسم جديد: {$name}");
                    $message = 'تمت إضافة القسم بنجاح.';
                } else {
                    $message = 'حدث خطأ أثناء الإضافة.';
                    $message_type = 'error';
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            // Note: DB restriction might prevent delete if products attached
            try {
                $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                if ($stmt->execute([$id])) {
                    log_activity($pdo, 'category_delete', "تم حذف قسم ID: {$id}", $id);
                    $message = 'تم حذف القسم بنجاح.';
                } else {
                    $message = 'لا يمكن الحذف.';
                    $message_type = 'error';
                }
            } catch (Exception $e) {
                $message = 'يرجى إزالة المنتجات من القسم أولاً.';
                $message_type = 'error';
            }
        }
    } elseif ($action === 'sort') {
        if (isset($_POST['sort_order']) && is_array($_POST['sort_order'])) {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("UPDATE categories SET sort_order = ? WHERE id = ?");
                foreach ($_POST['sort_order'] as $cat_id => $order) {
                    $stmt->execute([(int)$order, (int)$cat_id]);
                }
                $pdo->commit();
                $message = 'تم تحديث الترتيب بنجاح.';
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = 'خطأ أثناء التحديث: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Build category tree
$all_cats = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll();
$categories_tree = [];
$parent_cats = [];

foreach ($all_cats as $c) {
    if (!$c['parent_id']) {
        $parent_cats[] = $c;
    }
}

foreach ($parent_cats as $p) {
    $children = [];
    foreach ($all_cats as $c) {
        if ($c['parent_id'] == $p['id']) {
            $children[] = $c;
        }
    }
    $categories_tree[] = [
        'category' => $p,
        'children' => $children
    ];
}

$pageTitle = 'إدارة الأقسام';
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">Categories</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter leading-tight">إدارة <span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">الأقسام</span></h1>
        <p class="text-on-surface-variant/60 mt-2 font-bold tracking-widest text-sm">تحكم في الأقسام الرئيسية والفرعية وترتيبها</p>
    </div>
    <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="flex items-center gap-3 px-8 py-4 gold-gradient text-on-primary font-black text-xs uppercase tracking-widest rounded-2xl shadow-2xl hover:scale-105 transition-all">
        <span class="material-symbols-outlined mb-0.5">add</span>
        إضافة قسم
    </button>
</header>

<?php if ($message): ?>
<div class="mb-10 p-5 glass-card rounded-2xl flex items-center gap-4 text-xs font-black uppercase tracking-widest <?php echo $message_type === 'error' ? 'border-error/20 text-error bg-error/5' : 'border-primary/20 text-primary bg-primary/5'; ?>">
    <span class="material-symbols-outlined"><?php echo $message_type === 'error' ? 'warning' : 'verified'; ?></span>
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl">
    <form method="POST" action="">
        <input type="hidden" name="action" value="sort">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-separate border-spacing-y-3">
                <thead>
                    <tr>
                        <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10">ترتيب</th>
                        <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10">القسم</th>
                        <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10">الرابط (Slug)</th>
                        <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10">الأيقونة</th>
                        <th class="pb-6 px-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 border-b border-primary/10">حذف</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($categories_tree)): ?>
                    <tr><td colspan="5" class="text-center py-10 text-on-surface-variant/50">لا يوجد أقسام.</td></tr>
                    <?php else: foreach($categories_tree as $node): 
                        $parent = $node['category'];
                        $children = $node['children'];
                    ?>
                    <!-- Parent Category -->
                    <tr class="group">
                        <td class="p-4 bg-[#0a0e18] rounded-r-2xl border-y border-r border-white/5 w-24">
                            <input type="number" name="sort_order[<?php echo $parent['id']; ?>]" value="<?php echo htmlspecialchars($parent['sort_order']); ?>" class="w-full bg-[#141822] border border-white/10 rounded-lg p-2 text-center text-on-surface font-bold">
                        </td>
                        <td class="p-4 bg-[#0a0e18] border-y border-white/5 font-black text-lg text-primary"><?php echo htmlspecialchars($parent['name']); ?></td>
                        <td class="p-4 bg-[#0a0e18] border-y border-white/5 font-medium text-secondary" dir="ltr"><?php echo htmlspecialchars($parent['slug']); ?></td>
                        <td class="p-4 bg-[#0a0e18] border-y border-white/5">
                            <span class="material-symbols-outlined text-primary"><?php echo htmlspecialchars($parent['icon']); ?></span>
                        </td>
                        <td class="p-4 bg-[#0a0e18] rounded-l-2xl border-y border-l border-white/5 w-24">
                            <!-- We use a separate form for deletion so we don't submit the sorting form -->
                            <button type="button" onclick="confirmDelete(<?php echo $parent['id']; ?>)" class="w-9 h-9 rounded-xl bg-error/10 border border-error/20 text-error hover:bg-error hover:text-white transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </td>
                    </tr>
                    <!-- Child Categories -->
                    <?php foreach($children as $child): ?>
                    <tr class="group">
                        <td class="p-3 bg-[#0f131d] rounded-r-2xl border-y border-r border-white/5/50 w-24 pl-10 border-r-4 border-r-primary/50 relative">
                            <!-- Link line aesthetic -->
                            <div class="absolute right-[-10px] top-1/2 w-4 border-b border-primary/30"></div>
                            <input type="number" name="sort_order[<?php echo $child['id']; ?>]" value="<?php echo htmlspecialchars($child['sort_order']); ?>" class="w-full bg-[#141822] border border-white/10 rounded-lg p-1.5 text-center text-sm text-on-surface font-bold">
                        </td>
                        <td class="p-3 bg-[#0f131d] border-y border-white/5/50 font-bold text-sm text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary/40 text-[14px]">subdirectory_arrow_left</span>
                            <?php echo htmlspecialchars($child['name']); ?>
                        </td>
                        <td class="p-3 bg-[#0f131d] border-y border-white/5/50 font-medium text-secondary/70 text-sm" dir="ltr"><?php echo htmlspecialchars($child['slug']); ?></td>
                        <td class="p-3 bg-[#0f131d] border-y border-white/5/50">
                            <span class="material-symbols-outlined text-primary/60 text-sm"><?php echo htmlspecialchars($child['icon']); ?></span>
                        </td>
                        <td class="p-3 bg-[#0f131d] rounded-l-2xl border-y border-l border-white/5/50 w-24">
                            <button type="button" onclick="confirmDelete(<?php echo $child['id']; ?>)" class="w-8 h-8 rounded-xl bg-error/10 border border-error/20 text-error hover:bg-error hover:text-white transition-all flex items-center justify-center scale-90">
                                <span class="material-symbols-outlined text-[12px]">delete</span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(!empty($categories_tree)): ?>
        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-primary/10 border border-primary/30 text-primary hover:bg-primary hover:text-on-primary px-8 py-4 rounded-xl font-black uppercase tracking-widest text-xs flex items-center gap-3 transition-colors">
                <span class="material-symbols-outlined text-[18px]">save</span>
                حفظ الترتيب
            </button>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- Hidden Delete Form to handle JS triggers -->
<form id="delete-form" method="POST" class="hidden">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="delete-id" value="">
</form>

<div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="glass-card rounded-[2.5rem] p-10 w-full max-w-md border border-primary/10 shadow-2xl relative">
        <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="absolute top-6 left-6 w-9 h-9 flex items-center justify-center text-on-surface/60 hover:text-white transition-colors"><span class="material-symbols-outlined">close</span></button>
        <h2 class="text-2xl font-black mb-8 text-on-surface">قسم جديد</h2>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="add">
            
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">القسم الرئيسي (اختياري)</label>
                <select name="parent_id" class="w-full bg-[#0a0e18] rounded-2xl p-5 text-sm border-white/5 focus:border-primary text-on-surface">
                    <option value="">-- كقسم رئيسي --</option>
                    <?php foreach($parent_cats as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[9px] text-on-surface-variant/40 tracking-widest font-bold px-1 mt-1">اختر قسماً إذا أردت جعل هذا القسم فرعياً منه.</p>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">اسم القسم</label>
                <input required name="name" type="text" class="w-full bg-[#0a0e18] rounded-2xl p-5 text-sm border-white/5 focus:border-primary">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">الرابط (Slug بالانجليزية)</label>
                <input required name="slug" type="text" class="w-full bg-[#0a0e18] rounded-2xl p-5 text-sm border-white/5 focus:border-primary" dir="ltr">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">رمز الأيقونة (Material Symbols)</label>
                <input required name="icon" type="text" value="token" class="w-full bg-[#0a0e18] rounded-2xl p-5 text-sm border-white/5 focus:border-primary" dir="ltr">
            </div>
            
            <button type="submit" class="w-full py-5 gold-gradient rounded-2xl text-on-primary font-black uppercase tracking-widest pt-5 mt-4 hover:scale-105 active:scale-95 transition-all">إضافة الآن</button>
        </form>
    </div>
</div>

<script>
document.getElementById('add-modal').addEventListener('click', function(e) {
    if(e.target === this) this.classList.add('hidden');
});
function confirmDelete(id) {
    if (confirm("هل أنت متأكد من حذف هذا القسم؟ (سيتم حذف المنتجات المرتبطة به إن وجدت)")) {
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-form').submit();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>
