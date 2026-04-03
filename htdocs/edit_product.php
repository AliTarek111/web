<?php
session_start();
include 'db.php'; // تأكد أن ملف db.php في نفس الفولدر وصحيح

// حماية الصفحة: لو مش مسجل دخول يرجعه للرئيسية
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: admin.php');
    exit;
}

// التأكد من وجود ID المنتج في الرابط
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("خطأ: لم يتم تحديد المنتج المراد تعديله.");
}

$id = (int)$_GET['id'];

// جلب بيانات المنتج الحالية من قاعدة البيانات
$query = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$product = mysqli_fetch_assoc($query);

if (!$product) {
    die("خطأ: المنتج غير موجود في قاعدة البيانات.");
}

// معالجة البيانات عند الضغط على زر "حفظ التعديلات"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $cost_price = mysqli_real_escape_string($conn, $_POST['cost_price']);
    $stock = (int)$_POST['stock'];
    $min_stock = (int)$_POST['min_stock'];

    // فحص إذا كان السعر قد تغير لتسجيله في تاريخ الأسعار
    if ($price != $product['price']) {
        $old_price = $product['price'];
        mysqli_query($conn, "INSERT INTO price_history (product_id, old_price, new_price) VALUES ($id, '$old_price', '$price')");
    }
    
    // تحديث البيانات الشاملة
    $sql = "UPDATE products SET name='$name', price='$price', cost_price='$cost_price', stock='$stock', min_stock='$min_stock' WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        header('Location: admin.php?msg=success');
        exit;
    } else {
        $error = "حدث خطأ أثناء التحديث: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل منتج - متجر أحمد كشري</title>
    <style>
        :root { --gold: #D4AF37; --dark: #121212; --card: #1e1e1e; }
        body { background: var(--dark); color: white; font-family: sans-serif; text-align: center; padding-top: 50px; }
        .edit-box { max-width: 500px; margin: auto; background: var(--card); padding: 30px; border-radius: 15px; border: 1px solid var(--gold); }
        input { width: 90%; padding: 12px; margin: 15px 0; background: #2a2a2a; border: 1px solid #444; color: white; border-radius: 8px; font-size: 16px; }
        .btn-save { background: var(--gold); color: black; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; font-size: 18px; }
        .btn-cancel { display: block; margin-top: 20px; color: #888; text-decoration: none; }
    </style>
</head>
<body>

<div class="edit-box">
    <h2 style="color:var(--gold)">تعديل بيانات الموبايل</h2>
    
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

    <form method="POST">
        <label>اسم الموبايل:</label>
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
        
        <label>سعر البيع (ج.م):</label>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
        
        <label>سعر التكلفة (ج.م):</label>
        <input type="number" name="cost_price" value="<?php echo $product['cost_price']; ?>" required>

        <div style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <label>الكمية المتاحة:</label>
                <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
            </div>
            <div style="flex: 1;">
                <label>تنبيه عند نقص:</label>
                <input type="number" name="min_stock" value="<?php echo $product['min_stock']; ?>" required>
            </div>
        </div>
        
        <button type="submit" name="update" class="btn-save">حفظ التعديلات</button>
        <a href="admin.php" class="btn-cancel">إلغاء والعودة</a>
    </form>
</div>

</body>
</html>