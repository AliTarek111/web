<?php
// admin/edit_product.php
// Ahmed Koshary Store - Sovereign Product Modification Suite
require_once 'includes/auth_middleware.php';
include '../includes/db.php';

// Redirect to DB settings if offline
if ($db_connection_failed || $pdo === null) {
    header("Location: db_settings.php?err=offline");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = "";

// Fetch existing product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    die("المنتج غير موجود في السجلات الملكية.");
}

// Fetch existing specs
$spec_stmt = $pdo->prepare("SELECT * FROM product_specs WHERE product_id = ?");
$spec_stmt->execute([$id]);
$existing_specs = $spec_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $uid = $_POST['uid'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    $condition = $_POST['condition'];
    $desc = $_POST['description'];
    $type = $_POST['product_type'];
    $version = $_POST['software_version'];
    $battery = $_POST['battery_health'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Image Upload
    $image = $p['main_image'];
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $upload_dir = '../assets/products/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $_FILES['main_image']['name']);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $filename);
        $image = 'assets/products/' . $filename;
    }

    try {
        $pdo->beginTransaction();
        
        // 1. Update Product
        $upd_stmt = $pdo->prepare("UPDATE products SET uid=?, name=?, price=?, category_id=?, stock_count=?, condition_status=?, description=?, main_image=?, product_type=?, software_version=?, battery_health=?, is_active=?, is_featured=? WHERE id=?");
        $upd_stmt->execute([$uid, $name, $price, $category_id, $stock, $condition, $desc, $image, $type, $version, $battery, $is_active, $is_featured, $id]);

        // 2. Refresh Specifications (Delete & Re-insert for simplicity/EAV)
        $del_specs = $pdo->prepare("DELETE FROM product_specs WHERE product_id = ?");
        $del_specs->execute([$id]);

        if (isset($_POST['spec_keys']) && isset($_POST['spec_values'])) {
            $ins_spec = $pdo->prepare("INSERT INTO product_specs (product_id, spec_key, spec_value) VALUES (?, ?, ?)");
            for ($i = 0; $i < count($_POST['spec_keys']); $i++) {
                $key = trim($_POST['spec_keys'][$i]);
                $val = trim($_POST['spec_values'][$i]);
                if (!empty($key) && !empty($val)) {
                    $ins_spec->execute([$id, $key, $val]);
                }
            }
        }

        $pdo->commit();
        $message = "تم تحديث بيانات المنتج بنجاح!";
        
        // Refresh local mirror
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        $spec_stmt->execute([$id]);
        $existing_specs = $spec_stmt->fetchAll();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "خطأ في التحديث: " . $e->getMessage();
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$pageTitle = "تعديل " . $p['name'];
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">تعديل البيانات</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">تعديل <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent"><?php echo $p['name']; ?></span></h1>
    </div>
    <a href="inventory.php" class="px-8 py-4 glass-card border-white/5 rounded-xl text-xs font-black uppercase tracking-widest hover:border-primary/40 transition-all flex items-center gap-3">
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
        العودة للمخزون
    </a>
</header>

<?php if ($message): ?>
    <div class="mb-12 p-5 glass-card border-primary/20 text-primary text-xs font-black uppercase tracking-widest rounded-2xl shadow-2xl relative z-10 flex items-center gap-4 animate-bounce">
        <span class="material-symbols-outlined">verified</span>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="space-y-12 relative z-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Details Column -->
        <div class="lg:col-span-2 space-y-8">
            <section class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl space-y-10">
                <div class="flex justify-between items-center border-b border-primary/10 pb-4">
                    <h3 class="text-sm font-black uppercase tracking-widest text-primary/60">المعلومات الأساسية</h3>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-on-surface-variant/40">حالة الظهور</span>
                        <input type="checkbox" name="is_active" <?php echo $p['is_active'] ? 'checked' : ''; ?> class="w-10 h-5 bg-[#0a0e18] border-white/5 rounded-full text-primary focus:ring-0 checked:bg-primary transition-all">
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">اسم المنتج</label>
                        <input required name="name" type="text" value="<?php echo htmlspecialchars($p['name']); ?>" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-5 text-on-surface transition-all"/>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">كود المنتج (UID)</label>
                        <input required name="uid" type="text" value="<?php echo htmlspecialchars($p['uid']); ?>" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-5 text-on-surface transition-all"/>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">السعر (ج.م)</label>
                        <input required name="price" type="number" step="0.01" value="<?php echo $p['price']; ?>" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-5 text-on-surface transition-all font-bold"/>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">الكمية</label>
                        <input required name="stock" type="number" value="<?php echo $p['stock_count']; ?>" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-5 text-on-surface transition-all"/>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">الوصف التفصيلي</label>
                    <textarea name="description" rows="6" class="w-full bg-[#0a0e18] border-white/5 rounded-3xl focus:border-primary text-sm p-6 text-on-surface transition-all"><?php echo htmlspecialchars($p['description']); ?></textarea>
                </div>
            </section>

            <!-- Dynamic Specs Section -->
            <section class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl space-y-10">
                <div class="flex justify-between items-center border-b border-primary/10 pb-4">
                    <h3 class="text-sm font-black uppercase tracking-widest text-primary/60">المواصفات التقنية (Key Features)</h3>
                    <button type="button" onclick="addSpecRow()" class="text-primary text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-primary/10 px-4 py-2 rounded-xl transition-all">
                        <span class="material-symbols-outlined text-sm">add_circle</span>
                        إضافة خاصية
                    </button>
                </div>

                <div id="specsContainer" class="space-y-4">
                    <?php if (empty($existing_specs)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end animate-fade-in">
                            <div class="space-y-2">
                                <input name="spec_keys[]" type="text" placeholder="الخاصية" class="w-full bg-[#0a0e18]/50 border-white/5 rounded-xl text-xs p-4 text-on-surface"/>
                            </div>
                            <div class="flex gap-4 items-end">
                                <div class="space-y-2 flex-1">
                                    <input name="spec_values[]" type="text" placeholder="القيمة" class="w-full bg-[#0a0e18]/50 border-white/5 rounded-xl text-xs p-4 text-on-surface"/>
                                </div>
                                <button type="button" class="p-4 text-error/30 hover:text-error transition-colors"><span class="material-symbols-outlined">delete</span></button>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($existing_specs as $spec): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end animate-fade-in">
                            <div class="space-y-2">
                                <input name="spec_keys[]" type="text" value="<?php echo htmlspecialchars($spec['spec_key']); ?>" class="w-full bg-[#0a0e18]/50 border-white/5 rounded-xl text-xs p-4 text-on-surface"/>
                            </div>
                            <div class="flex gap-4 items-end">
                                <div class="space-y-2 flex-1">
                                    <input name="spec_values[]" type="text" value="<?php echo htmlspecialchars($spec['spec_value']); ?>" class="w-full bg-[#0a0e18]/50 border-white/5 rounded-xl text-xs p-4 text-on-surface"/>
                                </div>
                                <button type="button" onclick="this.parentElement.parentElement.remove()" class="p-4 text-error/30 hover:text-error transition-colors"><span class="material-symbols-outlined">delete</span></button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- Sidebar Config Column -->
        <div class="space-y-8">
            <section class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl space-y-8">
                <h3 class="text-sm font-black uppercase tracking-widest text-primary/60 border-b border-primary/10 pb-4">التحكم في العرض</h3>
                
                <div class="space-y-6">
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">القسم الوظيفي</label>
                        <select required name="category_id" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-4 text-on-surface transition-all appearance-none cursor-pointer">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $p['category_id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-4 rounded-2xl border border-white/5 bg-[#0a0e18] cursor-pointer group hover:border-primary/50 transition-all">
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/80 group-hover:text-primary flex items-center gap-2"><span class="material-symbols-outlined text-sm">star</span>منتج مميز (الرئيسية)</span>
                            <div class="relative">
                                <input type="checkbox" name="is_featured" <?php echo $p['is_featured'] ? 'checked' : ''; ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">نوع الكيان</label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="product_type" value="hardware" <?php echo $p['product_type'] == 'hardware' ? 'checked' : ''; ?> class="hidden peer">
                                <div class="p-4 rounded-2xl border border-white/5 bg-[#0a0e18] text-center text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 peer-checked:border-primary peer-checked:text-primary transition-all">Hardware</div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="product_type" value="software" <?php echo $p['product_type'] == 'software' ? 'checked' : ''; ?> class="hidden peer">
                                <div class="p-4 rounded-2xl border border-white/5 bg-[#0a0e18] text-center text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40 peer-checked:border-primary peer-checked:text-primary transition-all">Software</div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-3" id="condition_container">
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-primary/40 pr-2">الحالة الفنية</label>
                        <select name="condition" class="w-full bg-[#0a0e18] border-white/5 rounded-2xl focus:border-primary text-sm p-4 text-on-surface transition-all">
                            <option value="new" <?php echo $p['condition_status'] == 'new' ? 'selected' : ''; ?>>جديد (New Sovereign)</option>
                            <option value="used" <?php echo $p['condition_status'] == 'used' ? 'selected' : ''; ?>>مستعمل (Used)</option>
                            <option value="grade_a" <?php echo $p['condition_status'] == 'grade_a' ? 'selected' : ''; ?>>Grade A+ Pristine</option>
                            <option value="grade_b" <?php echo $p['condition_status'] == 'grade_b' ? 'selected' : ''; ?>>Grade B Good</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2" id="battery_container">
                            <label class="text-[8px] font-black uppercase tracking-widest text-on-surface-variant/20">صحة البطارية</label>
                            <input name="battery_health" type="number" value="<?php echo $p['battery_health']; ?>" class="w-full bg-[#0a0e18] border-white/5 rounded-xl text-xs p-3 text-on-surface"/>
                        </div>
                        <div class="space-y-2" id="software_container" style="display: none;">
                            <label class="text-[8px] font-black uppercase tracking-widest text-on-surface-variant/20">إصدار السوفت وير</label>
                            <input name="software_version" type="text" value="<?php echo htmlspecialchars($p['software_version']); ?>" class="w-full bg-[#0a0e18] border-white/5 rounded-xl text-xs p-3 text-on-surface"/>
                        </div>
                    </div>
                </div>
            </section>

            <section class="glass-card rounded-[2.5rem] p-10 border border-white/5 shadow-2xl space-y-6">
                <h3 class="text-sm font-black uppercase tracking-widest text-primary/60 border-b border-primary/10 pb-4">صور المنتج</h3>
                <div class="relative w-full aspect-square border-2 border-dashed border-white/5 rounded-[2rem] hover:border-primary/40 transition-all flex flex-col items-center justify-center gap-4 group cursor-pointer overflow-hidden bg-[#0a0e18]">
                    <span class="material-symbols-outlined text-4xl text-white/5 absolute">image</span>
                    <input type="file" name="main_image" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="previewImage(this)"/>
                    <img id="imagePreview" src="../<?php echo $p['main_image']; ?>" class="absolute inset-0 w-full h-full object-contain p-4"/>
                    <p id="uploadLabel" class="hidden text-[8px] text-on-surface-variant/20 font-black uppercase tracking-widest relative z-0">انقر لتحديث صورة المنتج</p>
                </div>
            </section>

            <button type="submit" class="w-full py-8 gold-gradient text-on-primary font-headline font-black text-xs uppercase rounded-[2rem] shadow-3xl hover:scale-[1.03] active:scale-95 transition-all flex items-center justify-center gap-4 group relative overflow-hidden">
                <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">save</span>
                حفظ التغييرات
            </button>
        </div>
    </div>
</form>

<script>
function addSpecRow() {
    const container = document.getElementById('specsContainer');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-1 md:grid-cols-2 gap-4 items-end animate-fade-in';
    row.innerHTML = `
        <div class="space-y-2">
            <input name="spec_keys[]" type="text" placeholder="الخاصية" class="w-full bg-[#0a0e18]/50 border-white/5 rounded-xl text-xs p-4 text-on-surface focus:border-primary transition-all"/>
        </div>
        <div class="flex gap-4 items-end">
            <div class="space-y-2 flex-1">
                <input name="spec_values[]" type="text" placeholder="القيمة" class="w-full bg-[#0a0e18]/50 border-white/5 rounded-xl text-xs p-4 text-on-surface focus:border-primary transition-all"/>
            </div>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="p-4 text-error/30 hover:text-error transition-colors"><span class="material-symbols-outlined">delete</span></button>
        </div>
    `;
    container.appendChild(row);
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const label = document.getElementById('uploadLabel');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if(label) label.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Logic for conditional fields based on product type
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="product_type"]');
    const batteryContainer = document.getElementById('battery_container');
    const softwareContainer = document.getElementById('software_container');

    function updateFields() {
        const selectedRadio = document.querySelector('input[name="product_type"]:checked');
        if (!selectedRadio) return;
        const selectedType = selectedRadio.value;
        if (selectedType === 'hardware') {
            batteryContainer.style.display = 'block';
            softwareContainer.style.display = 'none';
        } else {
            batteryContainer.style.display = 'none';
            softwareContainer.style.display = 'block';
        }
    }

    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateFields);
    });

    updateFields(); // Init on load
});
</script>

<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
</style>

<?php include 'includes/admin_footer.php'; ?>

