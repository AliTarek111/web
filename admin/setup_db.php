<?php
// admin/setup_db.php
// Script to initialize the Ahmed Koshary Store database

$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read SQL file
    $sql = file_get_contents('../setup_db.sql');

    // Execute SQL
    $pdo->exec($sql);

    echo "<h1>تم تهيئة قاعدة البيانات بنجاح!</h1>";
    echo "<p>تم إنشاء قاعدة البيانات 'ahmedkoshary_db' والجداول اللازمة.</p>";
    echo "<a href='index.php'>الذهاب إلى لوحة الإدارة</a>";

} catch (PDOException $e) {
    die("خطأ في تهيئة قاعدة البيانات: " . $e->getMessage());
}
?>
