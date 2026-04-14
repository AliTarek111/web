<?php
// admin/staff.php
// Ahmed Koshary Store — Staff Management (Super Admin Only)
require_once 'includes/super_admin_middleware.php';
include '../includes/db.php';
require_once 'includes/activity_logger.php';

$message = '';
$message_type = 'success';

// ─── Ensure DB columns exist (safe migration) ─────────────────────────────────
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS allowed_pages TEXT DEFAULT NULL");
} catch (PDOException $e) {
    // Columns may already exist
}

// ─── Available Dashboard Pages ──────────────────────────────────────────────────
$all_pages = [
    'index.php'            => ['icon' => 'dashboard',       'label' => 'لوحة القيادة'],
    'activity_log.php'     => ['icon' => 'history',         'label' => 'سجل النشاط'],
    'categories.php'       => ['icon' => 'category',        'label' => 'إدارة الأقسام'],
    'manage_devices.php'   => ['icon' => 'smartphone',      'label' => 'أجهزة الموبايلات'],
    'inventory.php'        => ['icon' => 'inventory_2',     'label' => 'المخزون'],
    'add_product.php'      => ['icon' => 'add_circle',      'label' => 'إضافة منتج'],
    'edit_product.php'     => ['icon' => 'edit',            'label' => 'تعديل منتج'],
    'service_requests.php' => ['icon' => 'build',           'label' => 'طلبات الاستعجال'],
    'orders.php'           => ['icon' => 'shopping_cart',   'label' => 'الطلبات'],
    'customers.php'        => ['icon' => 'group',           'label' => 'العملاء'],
    'settings.php'         => ['icon' => 'settings',        'label' => 'إعدادات المتجر'],
    'db_settings.php'      => ['icon' => 'storage',         'label' => 'إعدادات قاعدة البيانات'],
    'backup.php'           => ['icon' => 'cloud_download',  'label' => 'النسخ الاحتياطي'],
];

// ─── Handle Actions ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Add new staff member
    if ($action === 'add_staff') {
        $username    = trim($_POST['username'] ?? '');
        $password    = trim($_POST['password'] ?? '');
        $full_name   = trim($_POST['full_name'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $selected_pages = $_POST['allowed_pages'] ?? [];

        if (empty($username) || empty($password) || empty($full_name)) {
            $message = 'الاسم الكامل، اسم المستخدم، وكلمة المرور مطلوبين.';
            $message_type = 'error';
        } else {
            $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $check->execute([$username]);
            if ($check->fetch()) {
                $message = 'اسم المستخدم موجود مسبقاً، اختر اسم آخر.';
                $message_type = 'error';
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $pages_json = !empty($selected_pages) ? json_encode($selected_pages) : null;
                $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, phone, role, is_active, allowed_pages) VALUES (?, ?, ?, ?, 'admin', 1, ?)");
                if ($stmt->execute([$username, $hashed, $full_name, $phone, $pages_json])) {
                    log_activity($pdo, 'add_staff', "تم إضافة موظف جديد: {$full_name} (@{$username})");
                    $message = "تم إضافة الموظف «{$full_name}» بنجاح! 🎉";
                } else {
                    $message = 'حصل خطأ أثناء الإضافة.';
                    $message_type = 'error';
                }
            }
        }
    }

    // Change password
    elseif ($action === 'change_password') {
        $staff_id    = (int)($_POST['staff_id'] ?? 0);
        $new_password = trim($_POST['new_password'] ?? '');

        if ($staff_id && strlen($new_password) >= 6) {
            $hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'admin'");
            $stmt->execute([$hashed, $staff_id]);
            log_activity($pdo, 'change_password', "تم تغيير كلمة مرور الموظف ID: {$staff_id}", $staff_id);
            $message = 'تم تغيير كلمة المرور بنجاح! 🔑';
        } else {
            $message = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.';
            $message_type = 'error';
        }
    }

    // Toggle active/disabled
    elseif ($action === 'toggle_status') {
        $staff_id = (int)($_POST['staff_id'] ?? 0);
        if ($staff_id) {
            $currentStmt = $pdo->prepare("SELECT full_name, is_active FROM users WHERE id = ? AND role = 'admin'");
            $currentStmt->execute([$staff_id]);
            $current = $currentStmt->fetch();
            if ($current) {
                $new_status = $current['is_active'] ? 0 : 1;
                $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? AND role = 'admin'");
                $stmt->execute([$new_status, $staff_id]);
                $status_text = $new_status ? 'تفعيل' : 'تعطيل';
                log_activity($pdo, 'toggle_staff', "تم {$status_text} حساب الموظف: {$current['full_name']}", $staff_id);
                $message = $new_status ? "تم تفعيل حساب «{$current['full_name']}» بنجاح! ✅" : "تم تعطيل حساب «{$current['full_name']}» ⛔";
            }
        }
    }

    // Update permissions
    elseif ($action === 'update_permissions') {
        $staff_id = (int)($_POST['staff_id'] ?? 0);
        $selected_pages = $_POST['allowed_pages'] ?? [];
        if ($staff_id) {
            $pages_json = !empty($selected_pages) ? json_encode($selected_pages) : null;
            $stmt = $pdo->prepare("UPDATE users SET allowed_pages = ? WHERE id = ? AND role = 'admin'");
            $stmt->execute([$pages_json, $staff_id]);
            
            $nameStmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
            $nameStmt->execute([$staff_id]);
            $staffName = $nameStmt->fetchColumn();
            log_activity($pdo, 'update_permissions', "تم تحديث صلاحيات الموظف: {$staffName}", $staff_id);
            $message = "تم تحديث صلاحيات «{$staffName}» بنجاح! 🔒";
        }
    }

    // Delete staff
    elseif ($action === 'delete_staff') {
        $staff_id = (int)($_POST['staff_id'] ?? 0);
        if ($staff_id) {
            $nameStmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ? AND role = 'admin'");
            $nameStmt->execute([$staff_id]);
            $deletedName = $nameStmt->fetchColumn() ?: 'غير معروف';
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
            $stmt->execute([$staff_id]);
            log_activity($pdo, 'delete_staff', "تم حذف حساب الموظف: {$deletedName}", $staff_id);
            $message = 'تم حذف حساب الموظف نهائياً.';
        }
    }
}

// ─── Fetch Staff List ─────────────────────────────────────────────────────────
$staff_list = $pdo->query("SELECT * FROM users WHERE role = 'admin' ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'إدارة الموظفين';
include 'includes/admin_header.php';
?>

<!-- Page Header -->
<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">Super Admin</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter leading-tight">إدارة <span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">الموظفين</span></h1>
        <p class="text-on-surface/40 text-sm mt-3">الصفحة دي ليك إنت بس يا أحمد 👑</p>
    </div>
    <button onclick="document.getElementById('add-staff-modal').classList.remove('hidden')"
        class="flex items-center gap-3 px-8 py-4 gold-gradient text-on-primary font-black text-xs uppercase tracking-widest rounded-2xl shadow-2xl hover:scale-105 active:scale-95 transition-all">
        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">person_add</span>
        إضافة موظف جديد
    </button>
</header>

<!-- Messages -->
<?php if ($message): ?>
<div class="mb-10 p-5 glass-card rounded-2xl flex items-center gap-4 text-xs font-black uppercase tracking-widest
    <?php echo $message_type === 'error' ? 'border-error/20 text-error' : 'border-primary/20 text-primary'; ?>">
    <span class="material-symbols-outlined"><?php echo $message_type === 'error' ? 'warning' : 'verified'; ?></span>
    <?php echo $message; ?>
</div>
<?php endif; ?>

<!-- Staff Cards -->
<section class="relative z-10 space-y-6">
    <?php if (empty($staff_list)): ?>
    <div class="glass-card p-16 rounded-[2.5rem] border border-white/5 text-center">
        <span class="material-symbols-outlined text-5xl text-on-surface/20 mb-4 block">group_off</span>
        <p class="text-sm font-bold text-on-surface-variant/40">لا يوجد موظفون حتى الآن. ابدأ بإضافة أول موظف!</p>
    </div>
    <?php else: ?>
        <?php foreach ($staff_list as $staff): 
            $is_active = $staff['is_active'] ?? 1;
            $allowed = $staff['allowed_pages'] ? json_decode($staff['allowed_pages'], true) : array_keys($all_pages);
        ?>
        <div class="glass-card rounded-[2rem] border <?php echo $is_active ? 'border-white/5 hover:border-primary/20' : 'border-error/10 opacity-60'; ?> transition-all overflow-hidden">
            <!-- Staff Header Row -->
            <div class="p-6 px-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full <?php echo $is_active ? 'bg-primary/10 border-primary/20' : 'bg-error/10 border-error/20'; ?> border flex items-center justify-center shrink-0">
                        <span class="<?php echo $is_active ? 'text-primary' : 'text-error'; ?> font-black text-sm"><?php echo mb_substr($staff['full_name'], 0, 1); ?></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-bold text-on-surface"><?php echo htmlspecialchars($staff['full_name']); ?></p>
                            <?php if ($is_active): ?>
                            <span class="text-[8px] uppercase font-black tracking-widest text-green-400 bg-green-400/10 border border-green-400/20 px-2 py-0.5 rounded-full">نشط</span>
                            <?php else: ?>
                            <span class="text-[8px] uppercase font-black tracking-widest text-error bg-error/10 border border-error/20 px-2 py-0.5 rounded-full">معطّل</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-4 mt-1">
                            <span class="text-[10px] font-bold text-on-surface/50 tracking-widest" dir="ltr">@<?php echo htmlspecialchars($staff['username']); ?></span>
                            <span class="text-[9px] text-on-surface-variant/30">•</span>
                            <span class="text-[10px] text-on-surface-variant/40"><?php echo htmlspecialchars($staff['phone'] ?? '—'); ?></span>
                            <span class="text-[9px] text-on-surface-variant/30">•</span>
                            <span class="text-[10px] text-on-surface-variant/30"><?php echo date('d M Y', strtotime($staff['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Toggle Active/Disabled -->
                    <form method="POST">
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="staff_id" value="<?php echo $staff['id']; ?>">
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black transition-all
                            <?php echo $is_active 
                                ? 'bg-error/10 border border-error/20 text-error hover:bg-error hover:text-white' 
                                : 'bg-green-500/10 border border-green-500/20 text-green-400 hover:bg-green-500 hover:text-white'; ?>">
                            <span class="material-symbols-outlined text-sm"><?php echo $is_active ? 'block' : 'check_circle'; ?></span>
                            <?php echo $is_active ? 'تعطيل' : 'تفعيل'; ?>
                        </button>
                    </form>
                    <!-- Change Password -->
                    <button onclick="openPasswordModal(<?php echo $staff['id']; ?>, '<?php echo htmlspecialchars($staff['full_name']); ?>')"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-primary/10 border border-primary/20 text-primary hover:bg-primary hover:text-on-primary transition-all text-xs font-black">
                        <span class="material-symbols-outlined text-sm">key</span>
                        الباسورد
                    </button>
                    <!-- Permissions -->
                    <button onclick="openPermissionsModal(<?php echo $staff['id']; ?>, '<?php echo htmlspecialchars($staff['full_name']); ?>', <?php echo htmlspecialchars(json_encode($allowed)); ?>)"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white transition-all text-xs font-black">
                        <span class="material-symbols-outlined text-sm">shield_person</span>
                        الصلاحيات
                    </button>
                    <!-- Delete -->
                    <form method="POST" onsubmit="return confirm('هتحذف حساب <?php echo htmlspecialchars($staff['full_name']); ?> نهائياً؟')">
                        <input type="hidden" name="action" value="delete_staff">
                        <input type="hidden" name="staff_id" value="<?php echo $staff['id']; ?>">
                        <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 border border-white/5 text-on-surface/40 hover:bg-error hover:text-white hover:border-error transition-all">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Allowed Pages Preview -->
            <div class="px-8 pb-5 flex flex-wrap gap-2">
                <?php foreach ($all_pages as $page_key => $page_info): 
                    $has_access = in_array($page_key, $allowed);
                ?>
                <span class="text-[8px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-lg border
                    <?php echo $has_access 
                        ? 'bg-primary/5 border-primary/15 text-primary/70' 
                        : 'bg-white/[0.02] border-white/5 text-on-surface/15 line-through'; ?>">
                    <?php echo $page_info['label']; ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<!-- ═══════════════════ ADD STAFF MODAL ═══════════════════ -->
<div id="add-staff-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="glass-card rounded-[2.5rem] p-10 w-full max-w-lg border border-primary/10 shadow-2xl relative my-10">
        <button onclick="document.getElementById('add-staff-modal').classList.add('hidden')"
            class="absolute top-6 left-6 w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-on-surface/60 hover:text-on-surface transition-all">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
        <h2 class="text-2xl font-black font-headline text-on-surface mb-2">إضافة موظف جديد</h2>
        <p class="text-xs text-on-surface-variant/40 mb-8">الحساب الجديد هيكون بصلاحية Staff</p>

        <form method="POST" class="space-y-5">
            <input type="hidden" name="action" value="add_staff">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">الاسم الكامل</label>
                    <input required name="full_name" type="text" placeholder="مثال: محمد علي"
                        class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary text-sm p-4 text-on-surface transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">اسم المستخدم</label>
                    <input required name="username" type="text" placeholder="staff_01" dir="ltr"
                        class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary text-sm p-4 text-on-surface transition-all text-left">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">رقم التليفون</label>
                    <input name="phone" type="tel" placeholder="01XXXXXXXXX"
                        class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary text-sm p-4 text-on-surface transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">كلمة المرور</label>
                    <input required name="password" type="password" placeholder="••••••••" minlength="6"
                        class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary text-sm p-4 text-on-surface transition-all">
                </div>
            </div>

            <!-- Page Permissions -->
            <div class="space-y-3 pt-4">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">الصفحات المسموح الوصول إليها</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <?php foreach ($all_pages as $page_key => $page_info): ?>
                    <label class="flex items-center gap-2 bg-[#0a0e18] border border-white/5 rounded-xl p-3 cursor-pointer hover:border-primary/30 transition-all">
                        <input type="checkbox" name="allowed_pages[]" value="<?php echo $page_key; ?>" checked
                            class="rounded border-white/20 bg-transparent text-primary focus:ring-primary/50">
                        <span class="material-symbols-outlined text-sm text-on-surface/40"><?php echo $page_info['icon']; ?></span>
                        <span class="text-[10px] font-bold text-on-surface/70"><?php echo $page_info['label']; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit"
                class="w-full py-5 gold-gradient text-on-primary font-black text-xs uppercase tracking-widest rounded-2xl hover:scale-[1.02] active:scale-95 transition-all mt-4">
                إنشاء الحساب
            </button>
        </form>
    </div>
</div>

<!-- ═══════════════════ CHANGE PASSWORD MODAL ═══════════════════ -->
<div id="change-password-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="glass-card rounded-[2.5rem] p-10 w-full max-w-sm border border-primary/10 shadow-2xl relative">
        <button onclick="document.getElementById('change-password-modal').classList.add('hidden')"
            class="absolute top-6 left-6 w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-on-surface/60 hover:text-on-surface transition-all">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 rounded-full gold-gradient flex items-center justify-center">
                <span class="material-symbols-outlined text-on-primary" style="font-variation-settings:'FILL' 1">key</span>
            </div>
            <div>
                <h2 class="text-xl font-black font-headline text-on-surface">تغيير كلمة المرور</h2>
                <p id="modal-staff-name" class="text-xs text-primary/60 font-bold mt-1"></p>
            </div>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="change_password">
            <input type="hidden" name="staff_id" id="modal-staff-id">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">كلمة المرور الجديدة</label>
                <input required name="new_password" type="password" placeholder="6 أحرف على الأقل" minlength="6"
                    class="w-full bg-[#0a0e18] border border-white/5 rounded-2xl focus:border-primary text-sm p-5 text-on-surface transition-all">
            </div>
            <button type="submit"
                class="w-full py-5 gold-gradient text-on-primary font-black text-xs uppercase tracking-widest rounded-2xl hover:scale-[1.02] active:scale-95 transition-all">
                حفظ كلمة المرور
            </button>
        </form>
    </div>
</div>

<!-- ═══════════════════ PERMISSIONS MODAL ═══════════════════ -->
<div id="permissions-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="glass-card rounded-[2.5rem] p-10 w-full max-w-lg border border-blue-500/10 shadow-2xl relative my-10">
        <button onclick="document.getElementById('permissions-modal').classList.add('hidden')"
            class="absolute top-6 left-6 w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-on-surface/60 hover:text-on-surface transition-all">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 rounded-full bg-blue-500/20 border border-blue-500/30 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-400" style="font-variation-settings:'FILL' 1">shield_person</span>
            </div>
            <div>
                <h2 class="text-xl font-black font-headline text-on-surface">إدارة الصلاحيات</h2>
                <p id="perm-staff-name" class="text-xs text-blue-400/60 font-bold mt-1"></p>
            </div>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="update_permissions">
            <input type="hidden" name="staff_id" id="perm-staff-id">
            
            <div class="flex items-center justify-between mb-4">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/40">الصفحات المسموح بها</label>
                <div class="flex gap-2">
                    <button type="button" onclick="toggleAllPerms(true)" class="text-[9px] font-bold text-primary bg-primary/10 px-3 py-1 rounded-lg hover:bg-primary/20 transition-all">تحديد الكل</button>
                    <button type="button" onclick="toggleAllPerms(false)" class="text-[9px] font-bold text-error bg-error/10 px-3 py-1 rounded-lg hover:bg-error/20 transition-all">إلغاء الكل</button>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3" id="perm-checkboxes">
                <?php foreach ($all_pages as $page_key => $page_info): ?>
                <label class="flex items-center gap-3 bg-[#0a0e18] border border-white/5 rounded-xl p-4 cursor-pointer hover:border-blue-500/30 transition-all group">
                    <input type="checkbox" name="allowed_pages[]" value="<?php echo $page_key; ?>" class="perm-checkbox rounded border-white/20 bg-transparent text-blue-500 focus:ring-blue-500/50">
                    <span class="material-symbols-outlined text-sm text-on-surface/30 group-hover:text-blue-400 transition-colors"><?php echo $page_info['icon']; ?></span>
                    <span class="text-[10px] font-bold text-on-surface/60"><?php echo $page_info['label']; ?></span>
                </label>
                <?php endforeach; ?>
            </div>

            <button type="submit"
                class="w-full py-5 bg-blue-500 text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:scale-[1.02] active:scale-95 transition-all">
                حفظ الصلاحيات
            </button>
        </form>
    </div>
</div>

<script>
function openPasswordModal(staffId, staffName) {
    document.getElementById('modal-staff-id').value = staffId;
    document.getElementById('modal-staff-name').textContent = staffName;
    document.getElementById('change-password-modal').classList.remove('hidden');
}

function openPermissionsModal(staffId, staffName, allowedPages) {
    document.getElementById('perm-staff-id').value = staffId;
    document.getElementById('perm-staff-name').textContent = staffName;
    
    // Reset all checkboxes first
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.checked = allowedPages.includes(cb.value);
    });
    
    document.getElementById('permissions-modal').classList.remove('hidden');
}

function toggleAllPerms(state) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = state);
}

// Close modals on backdrop click
['add-staff-modal', 'change-password-modal', 'permissions-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
});
</script>

<?php include 'includes/admin_footer.php'; ?>
