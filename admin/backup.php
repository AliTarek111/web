<?php
// admin/backup.php
// Ahmed Koshary Store - Database Backup & Restore System
require_once 'includes/auth_middleware.php';

// Only Super Admin or Admin allowed
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'super_admin'])) {
    die("Unauthorized access.");
}

include '../includes/db.php';
$message = '';
$message_type = '';

// Handle Download
if (isset($_GET['action']) && $_GET['action'] == 'download') {
    // Generate SQL using PHP
    $tables = [];
    $query = $pdo->query('SHOW TABLES');
    while($row = $query->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    $sql_dump = "-- Ahmed Koshary Store Backup\n";
    $sql_dump .= "-- Date: " . date("Y-m-d H:i:s") . "\n\n";

    foreach($tables as $table) {
        $query = $pdo->query("SELECT * FROM `$table`");
        $num_rows = $query->rowCount();
        
        $sql_dump .= "DROP TABLE IF EXISTS `$table`;\n";
        $row2 = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
        $sql_dump .= "\n" . $row2[1] . ";\n\n";
        
        for ($i = 0; $i < $num_rows; $i++) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $sql_dump .= "INSERT INTO `$table` VALUES(";
                $values = array_values($row);
                foreach($values as $index => $val) {
                    if (is_null($val)) {
                        $sql_dump .= "NULL";
                    } else {
                        $val = addslashes($val);
                        $val = str_replace("\n", "\\n", $val);
                        $sql_dump .= "'".$val."'";
                    }
                    if ($index < count($values) - 1) {
                        $sql_dump .= ",";
                    }
                }
                $sql_dump .= ");\n";
            }
        }
        $sql_dump .= "\n\n";
    }

    $filename = 'backup_ahmed_koshary_' . date("Y-m-d_H-i-s") . '.sql';
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($sql_dump));
    echo $sql_dump;
    exit;
}

// Handle Restore
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restore'])) {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['backup_file']['tmp_name'];
        $ext = pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION);
        
        if (strtolower($ext) == 'sql') {
            $sql = file_get_contents($file);
            try {
                // Execute the SQL file statements
                $pdo->exec($sql);
                $message = "تم استعادة النسخة الاحتياطية بنجاح.";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "فشل في استعادة النسخة الاحتياطية: " . htmlspecialchars($e->getMessage());
                $message_type = "error";
            }
        } else {
            $message = "يرجى رفع ملف بصيغة .sql فقط!";
            $message_type = "error";
        }
    } else {
        $message = "يرجى اختيار ملف صالح.";
        $message_type = "error";
    }
}

$pageTitle = "النسخ الاحتياطي (Backup)";
include 'includes/admin_header.php';
?>

<header class="mb-16 relative z-10">
    <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">الأمن السيبراني والبيانات</span>
    <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">النسخ الاحتياطي <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">واستعادة البيانات</span></h1>
</header>

<?php if ($message): ?>
<div class="p-6 mb-12 rounded-2xl border <?php echo $message_type == 'success' ? 'bg-green-500/10 border-green-500/20 text-green-500' : 'bg-error/10 border-error/20 text-error'; ?> font-bold text-sm">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-12 relative z-10">
    <!-- Download Backup -->
    <div class="glass-card p-10 rounded-[2.5rem] border border-white/5 space-y-6 flex flex-col justify-between hover:border-primary/20 transition-all">
        <div>
            <div class="w-16 h-16 rounded-2xl gold-gradient text-on-primary flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-3xl">download</span>
            </div>
            <h2 class="text-xl font-black text-on-surface font-headline mb-2 uppercase">تنزيل نسخة احتياطية</h2>
            <p class="text-sm font-medium text-on-surface-variant/60 leading-relaxed mb-8">يقوم بسحب كافة البيانات، والطلبات، والمنتجات بصيغة ملف .sql لتتمكن من الاحتفاظ به كنسخة آمنة.</p>
        </div>
        <a href="backup.php?action=download" class="w-full py-5 gold-gradient text-on-primary font-black uppercase text-xs tracking-widest rounded-2xl text-center shadow-3xl hover:scale-105 transition-all">تنزيل الـ Backup الآن</a>
    </div>

    <!-- Upload Backup -->
    <div class="glass-card p-10 rounded-[2.5rem] border border-white/5 space-y-6 flex flex-col justify-between hover:border-error/20 transition-all">
        <div>
            <div class="w-16 h-16 rounded-2xl bg-error/10 text-error border border-error/20 flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-3xl">upload</span>
            </div>
            <h2 class="text-xl font-black text-on-surface font-headline mb-2 uppercase">استعادة النظام (Restore)</h2>
            <p class="text-sm font-medium text-on-surface-variant/60 leading-relaxed mb-8">ارفع ملف .sql الذي قمت بتحميله مسبقاً وسيقوم النظام بمحو البيانات الحالية وإحلال بيانات الملف بدلاً منها. <b>استخدم هذا الإجراء بحذر شديد!</b></p>
        </div>
        
        <form action="backup.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="file" name="backup_file" accept=".sql" required class="w-full text-[10px] text-on-surface-variant/60 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-white/5 file:text-primary hover:file:bg-white/10 transition-all"/>
            <button type="submit" name="restore" onclick="return confirm('تنبيه هام! استعادة النسخة الاحتياطية ستمسح كل بيانات المتجر والطلبات الموجودة حاليا ليحل محلها الملف المرفوع. هل أنت متأكد من الاستمرار؟');" class="w-full py-5 bg-error/10 text-error border border-error/20 font-black uppercase text-xs tracking-widest rounded-2xl hover:bg-error hover:text-white transition-all shadow-xl">بدء الاستعادة</button>
        </form>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
