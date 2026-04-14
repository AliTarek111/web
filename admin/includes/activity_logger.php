<?php
// admin/includes/activity_logger.php
// Ahmed Koshary Store — Activity Logger Helper
// Usage: log_activity($pdo, 'action_type', 'Description of what happened');

function log_activity($pdo, $action_type, $description, $target_id = null) {
    if (!$pdo) return;
    
    try {
        // Auto-create the table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT NULL,
            username VARCHAR(50) DEFAULT NULL,
            action_type VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            target_id INT DEFAULT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");

        $user_id  = $_SESSION['admin_id'] ?? null;
        $username = $_SESSION['admin_username'] ?? ($_SESSION['username'] ?? 'system');
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, username, action_type, description, target_id, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $username, $action_type, $description, $target_id, $ip]);
    } catch (PDOException $e) {
        error_log('[ActivityLog Error] ' . $e->getMessage());
    }
}

function get_recent_activities($pdo, $limit = 10) {
    if (!$pdo) return [];
    
    try {
        // Ensure table exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT NULL,
            username VARCHAR(50) DEFAULT NULL,
            action_type VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            target_id INT DEFAULT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");

        $stmt = $pdo->prepare("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('[ActivityLog Error] ' . $e->getMessage());
        return [];
    }
}

function time_ago_arabic($datetime) {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return 'منذ ' . $diff->y . ' سنة';
    if ($diff->m > 0) return 'منذ ' . $diff->m . ' شهر';
    if ($diff->d > 0) return 'منذ ' . $diff->d . ' يوم';
    if ($diff->h > 0) return 'منذ ' . $diff->h . ' ساعة';
    if ($diff->i > 0) return 'منذ ' . $diff->i . ' دقيقة';
    return 'الآن';
}

function get_action_icon($action_type) {
    $icons = [
        'add_product'       => 'add_circle',
        'edit_product'      => 'edit',
        'delete_product'    => 'delete',
        'add_staff'         => 'person_add',
        'delete_staff'      => 'person_remove',
        'change_password'   => 'key',
        'order_status'      => 'shopping_cart',
        'new_order'         => 'receipt_long',
        'login'             => 'login',
        'logout'            => 'logout',
        'settings_update'   => 'settings',
        'category_add'      => 'create_new_folder',
        'category_delete'   => 'folder_delete',
        'export_report'     => 'download',
    ];
    return $icons[$action_type] ?? 'info';
}
?>
