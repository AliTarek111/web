<?php
session_start();
include 'db.php';

// حماية الصفحة - الأدمن فقط
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}

$msg = "";

// 1. معالجة الإضافة الجماعية (شيت الإكسيل)
if (isset($_POST['bulk_save'])) {
    if (!empty($_POST['products'])) {
        foreach ($_POST['products'] as $p) {
            $name = mysqli_real_escape_string($conn, $p['name']);
            $price = (float)$p['price'];
            $category = mysqli_real_escape_string($conn, $p['cat']);
            $desc = mysqli_real_escape_string($conn, $p['desc']);
            
            if (!empty($name)) {
                $sql = "INSERT INTO products (name, price, category, description, status) 
                        VALUES ('$name', '$price', '$category', '$desc', 'available')";
                mysqli_query($conn, $sql);
            }
        }
        $msg = "تم حفظ جميع المنتجات في المخزن بنجاح ✅";
    }
}

// 2. معالجة تحديث منتج معين (تعديل كامل)
if (isset($_POST['update_item'])) {
    $id = (int)$_POST['p_id'];
    $name = mysqli_real_escape_string($conn, $_POST['u_name']);
    $price = (float)$_POST['u_price'];
    $category = mysqli_real_escape_string($conn, $_POST['u_cat']);
    $desc = mysqli_real_escape_string($conn, $_POST['u_desc']);
    
    $update_query = "UPDATE products SET name='$name', price='$price', category='$category', description='$desc' WHERE id=$id";
    
    if (!empty($_FILES['u_image']['name'])) {
        $imgName = time() . "_" . $_FILES['u_image']['name'];
        move_uploaded_file($_FILES['u_image']['tmp_name'], "uploads/" . $imgName);
        mysqli_query($conn, "UPDATE products SET image='$imgName' WHERE id=$id");
    }
    
    mysqli_query($conn, $update_query);
    $msg = "تم تحديث بيانات المنتج بنجاح ✨";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>THE BOSS PANEL | الإدارة الملكية 🛡️</title>
    <style>
        :root { --gold: #D4AF37; --black: #0a0a0a; --dark: #151515; }
        body { background: var(--black); color: #eee; font-family: 'Segoe UI', Tahoma, sans-serif; margin:0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gold); padding-bottom: 10px; margin-bottom: 30px; }
        .section-card { background: var(--dark); padding: 20px; border-radius: 15px; border: 1px solid #333; margin-bottom: 30px; }
        
        /* تابات التنقل */
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn { padding: 12px 25px; background: #222; color: var(--gold); border: 1px solid var(--gold); border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .tab-btn.active { background: var(--gold); color: #000; }

        /* ستايل الجدول (الإكسيل) */
        .excel-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .excel-table th { color: var(--gold); text-align: right; padding: 10px; }
        .excel-table td { padding: 5px; }
        .excel-table input, .excel-table select { width: 100%; padding: 10px; background: #222; border: 1px solid #444; color: #fff; border-radius: 5px; box-sizing: border-box; }

        /* ستايل قائمة المستخدمين */
        .user-row { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #333; align-items: center; }
        .date-badge { background: rgba(212,175,55,0.1); color: var(--gold); padding: 5px 10px; border-radius: 5px; font-size: 0.8rem; }

        .btn-save { background: var(--gold); color: #000; border: none; padding: 15px 40px; border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-bar">
        <h2>لوحة التحكم | م. علي طارق ✨</h2>
        <a href="logout.php" style="color:#ff4d4d; text-decoration:none;">تسجيل خروج</a>
    </div>

    <?php if($msg) echo "<p style='color:var(--gold); text-align:center; font-weight:bold;'>$msg</p>"; ?>

    <div class="tabs">
        <button class="tab-btn active" onclick="openTab('bulk')">إضافة جماعية (Excel Mode)</button>
        <button class="tab-btn" onclick="openTab('users')">إدارة المستخدمين</button>
        <button class="tab-btn" onclick="openTab('edit')">المخزن والتعديل</button>
    </div>

    <section id="tab-bulk" class="section-card">
        <h3>إضافة سريعة لعدة منتجات 📝</h3>
        <form method="POST">
            <table class="excel-table">
                <thead>
                    <tr>
                        <th>اسم المنتج</th>
                        <th>السعر (بيع)</th>
                        <th>القسم</th>
                        <th>الوصف (المواصفات)</th>
                    </tr>
                </thead>
                <tbody id="bulk-body">
                    <?php for($i=0; $i<5; $i++): ?>
                    <tr>
                        <td><input type="text" name="products[<?php echo $i; ?>][name]" placeholder="مثلاً: iPhone 13 Pro"></td>
                        <td><input type="number" name="products[<?php echo $i; ?>][price]" placeholder="0.00"></td>
                        <td>
                            <select name="products[<?php echo $i; ?>][cat]">
                                <option value="Apple">Apple</option>
                                <option value="Samsung">Samsung</option>
                                <option value="Xiaomi">Xiaomi</option>
                                <option value="Accessories">Accessories</option>
                            </select>
                        </td>
                        <td><input type="text" name="products[<?php echo $i; ?>][desc]" placeholder="الرامات، المساحة، الحالة..."></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <button type="submit" name="bulk_save" class="btn-save">حفظ جميع الصفوف 💾</button>
        </form>
    </section>

    <section id="tab-users" class="section-card" style="display:none;">
        <h3>العملاء المسجلين في المتجر 👥</h3>
        <?php
        $users_q = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC");
        while($u = mysqli_fetch_assoc($users_q)):
        ?>
        <div class="user-row">
            <div>
                <strong><?php echo $u['full_name']; ?></strong><br>
                <small style="color:#888;">هاتف: <?php echo $u['phone_number']; ?></small>
            </div>
            <div class="date-badge">انضم في: <?php echo date('Y-m-d', strtotime($u['created_at'])); ?></div>
        </div>
        <?php endwhile; ?>
    </section>

    <section id="tab-edit" class="section-card" style="display:none;">
        <h3>المخزن الحالي (تعديل كامل) ⚙️</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
            <?php
            $inventory = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
            while($item = mysqli_fetch_assoc($inventory)):
            ?>
            <div style="background:#222; padding:15px; border-radius:12px; border:1px solid #444;">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="p_id" value="<?php echo $item['id']; ?>">
                    
                    <div style="display:flex; gap:10px; margin-bottom:10px;">
                        <img src="uploads/<?php echo $item['image']; ?>" width="60" height="60" style="border-radius:8px; object-fit:cover;">
                        <div style="flex:1;">
                            <label style="font-size:0.7rem; color:var(--gold);">تغيير الصورة:</label>
                            <input type="file" name="u_image" style="font-size:0.8rem;">
                        </div>
                    </div>

                    <input type="text" name="u_name" value="<?php echo $item['name']; ?>" style="width:100%; margin-bottom:10px; background:#111; color:#fff; border:1px solid #333; padding:8px; border-radius:5px;">
                    
                    <div style="display:flex; gap:10px; margin-bottom:10px;">
                        <input type="number" name="u_price" value="<?php echo $item['price']; ?>" style="flex:1; background:#111; color:#fff; border:1px solid #333; padding:8px; border-radius:5px;">
                        <select name="u_cat" style="flex:1; background:#111; color:#fff; border:1px solid #333; padding:8px; border-radius:5px;">
                            <option value="Apple" <?php if($item['category']=='Apple') echo 'selected'; ?>>Apple</option>
                            <option value="Samsung" <?php if($item['category']=='Samsung') echo 'selected'; ?>>Samsung</option>
                            <option value="Accessories" <?php if($item['category']=='Accessories') echo 'selected'; ?>>Accessories</option>
                        </select>
                    </div>

                    <textarea name="u_desc" style="width:100%; height:80px; background:#111; color:#ccc; border:1px solid #333; padding:8px; border-radius:5px; margin-bottom:10px;"><?php echo $item['description']; ?></textarea>
                    
                    <button type="submit" name="update_item" style="width:100%; background:#2ed573; color:black; border:none; padding:10px; border-radius:8px; font-weight:bold; cursor:pointer;">تحديث البيانات ✅</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </section>
</div>

<script>
    function openTab(tabName) {
        // إخفاء كل السكاشن
        document.getElementById('tab-bulk').style.display = 'none';
        document.getElementById('tab-users').style.display = 'none';
        document.getElementById('tab-edit').style.display = 'none';
        
        // إظهار السكشن المختار
        document.getElementById('tab-' + tabName).style.display = 'block';
        
        // تحديث شكل الأزرار
        let buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
</script>

</body>
</html>