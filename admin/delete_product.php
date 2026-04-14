<?php
// admin/delete_product.php
// Ahmed Koshary Store - Secure Product Deletion Logic
require_once 'includes/auth_middleware.php';
include '../includes/db.php';
require_once 'includes/activity_logger.php';

// Redirect to DB settings if offline
if ($db_connection_failed || $pdo === null) {
    header("Location: db_settings.php?err=offline");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Fetch product info for logging
        $stmt = $pdo->prepare("SELECT name, main_image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $productName = $product['name'] ?? 'غير معروف';
        $image = $product['main_image'] ?? null;

        // Delete from database (Cascades to specs via DB foreign keys)
        $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if ($del->execute([$id])) {
            log_activity($pdo, 'delete_product', "تم حذف المنتج: {$productName}", $id);
            // Delete physical file if it exists and isn't a placeholder
            if ($image && strpos($image, 'assets/products/') === 0 && file_exists('../' . $image)) {
                unlink('../' . $image);
            }
        }
        
        header("Location: inventory.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        die("خطأ في المسح السيادي: " . $e->getMessage());
    }
} else {
    header("Location: inventory.php");
    exit();
}
?>

