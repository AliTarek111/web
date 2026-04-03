<?php
session_start();
include 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php"); exit;
}

$user_id = $_SESSION['customer_id'];
$res = mysqli_query($conn, "SELECT * FROM customers WHERE id=$user_id");
$user = mysqli_fetch_assoc($res);

if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $new_pass = $_POST['new_password'];

    if (!empty($new_pass)) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE customers SET full_name='$name', address='$address', password='$hashed_pass' WHERE id=$user_id");
    } else {
        mysqli_query($conn, "UPDATE customers SET full_name='$name', address='$address' WHERE id=$user_id");
    }
    $_SESSION['name'] = $name; // تحديث الاسم في السيشن فوراً
    echo "<script>alert('تم تحديث بياناتك بنجاح'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ملفي الشخصي - متجر علي</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { max-width: 500px; margin: 100px auto; background: #1a1a1a; padding: 30px; border-radius: 15px; border: 1px solid var(--gold); }
        input { width: 90%; padding: 12px; margin: 10px 0; background: #222; border: 1px solid #444; color: white; border-radius: 8px; }
        .btn-save { background: var(--gold); color: black; padding: 12px; width: 100%; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2 style="color:var(--gold)">تعديل بيانات الحساب</h2>
        <form method="POST">
            <label>الاسم بالكامل:</label>
            <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
            
            <label>عنوان التوصيل:</label>
            <input type="text" name="address" value="<?php echo $user['address']; ?>" required>
            
            <label>كلمة مرور جديدة (اتركها فارغة إذا لا تريد التغيير):</label>
            <input type="password" name="new_password" placeholder="******">
            
            <button type="submit" name="update_profile" class="btn-save">حفظ التغييرات</button>
        </form>
        <a href="index.php" style="display:block; margin-top:20px; color:#aaa; text-decoration:none;">العودة للمتجر</a>
    </div>
</body>
</html>