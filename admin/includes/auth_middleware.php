<?php
// admin/includes/auth_middleware.php
// Middleware: التأكد من أن المستخدم لديه صلاحية الوصول للوحة التحكم
// يتحقق من: تسجيل الدخول + الصلاحية + حالة الحساب + صلاحيات الصفحة

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$allowed_roles = ['super_admin', 'admin'];

// 1. Check if logged in with correct role
if (empty($_SESSION['admin_logged_in']) || !in_array($_SESSION['role'] ?? '', $allowed_roles)) {
    header("Location: login.php");
    exit();
}

// 2. Super admin bypasses all checks
if (($_SESSION['role'] ?? '') === 'super_admin') {
    return; // Full access
}

// 3. Staff — Check if account is still active (re-verify from DB)
// We only check on page loads, not every request, for performance
if (!isset($_SESSION['db_offline']) || !$_SESSION['db_offline']) {
    // Include DB if not already included
    if (!isset($pdo)) {
        include __DIR__ . '/../../includes/db.php';
    }
    
    if (isset($pdo) && $pdo !== null) {
        $checkStmt = $pdo->prepare("SELECT is_active, allowed_pages FROM users WHERE id = ? AND role = 'admin'");
        $checkStmt->execute([$_SESSION['admin_id'] ?? 0]);
        $staffData = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        // Account deleted or deactivated
        if (!$staffData || (isset($staffData['is_active']) && !$staffData['is_active'])) {
            session_destroy();
            header("Location: login.php?err=disabled");
            exit();
        }
        
        // Check page permissions
        $current_page = basename($_SERVER['PHP_SELF']);
        if (!empty($staffData['allowed_pages'])) {
            $allowed_pages = json_decode($staffData['allowed_pages'], true);
            if (is_array($allowed_pages) && !in_array($current_page, $allowed_pages)) {
                // Also allow delete_product.php if inventory.php is allowed
                $implicit_access = [
                    'delete_product.php' => 'inventory.php',
                    'edit_product.php'   => 'inventory.php',
                    'add_product.php'    => 'inventory.php',
                ];
                $parent = $implicit_access[$current_page] ?? null;
                if (!$parent || !in_array($parent, $allowed_pages)) {
                    header("Location: index.php?err=no_permission");
                    exit();
                }
            }
        }
    }
}
?>
