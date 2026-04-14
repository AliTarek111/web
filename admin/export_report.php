<?php
// admin/export_report.php
// Ahmed Koshary Store - Export Reports as CSV
require_once 'includes/auth_middleware.php';
include '../includes/db.php';
require_once 'includes/activity_logger.php';

if ($db_connection_failed || $pdo === null) {
    die('قاعدة البيانات غير متاحة');
}

$type = $_GET['type'] ?? 'all';

// Set filename and headers
$filename = 'ahmed_koshary_report_' . date('Y-m-d_H-i') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Add BOM for Excel Arabic support
$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

if ($type === 'products' || $type === 'all') {
    fputcsv($output, ['=== تقرير المنتجات ===']);
    fputcsv($output, ['الكود', 'اسم المنتج', 'القسم', 'السعر', 'المخزون', 'الحالة', 'مميز', 'تاريخ الإضافة']);
    
    $products = $pdo->query("
        SELECT p.uid, p.name, c.name as category_name, p.price, p.stock_count, 
               p.condition_status, p.is_featured, p.created_at 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC
    ")->fetchAll();
    
    foreach ($products as $p) {
        fputcsv($output, [
            $p['uid'],
            $p['name'],
            $p['category_name'] ?? 'بدون قسم',
            $p['price'],
            $p['stock_count'],
            $p['condition_status'],
            $p['is_featured'] ? 'نعم' : 'لا',
            $p['created_at']
        ]);
    }
    fputcsv($output, []);
}

if ($type === 'orders' || $type === 'all') {
    fputcsv($output, ['=== تقرير الطلبات ===']);
    fputcsv($output, ['رقم الطلب', 'العميل', 'رقم الواتساب', 'العنوان', 'الإجمالي', 'الحالة', 'تاريخ الطلب']);
    
    $orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
    
    foreach ($orders as $o) {
        fputcsv($output, [
            '#' . $o['id'],
            $o['customer_name'],
            $o['whatsapp_number'],
            $o['customer_address'] ?? '—',
            $o['total_amount'],
            $o['status'],
            $o['order_date']
        ]);
    }
    fputcsv($output, []);
}

if ($type === 'staff' || $type === 'all') {
    fputcsv($output, ['=== تقرير الموظفين ===']);
    fputcsv($output, ['الاسم', 'اسم المستخدم', 'الصلاحية', 'تاريخ الإنشاء']);
    
    $staff = $pdo->query("SELECT full_name, username, role, created_at FROM users ORDER BY id ASC")->fetchAll();
    
    foreach ($staff as $s) {
        fputcsv($output, [
            $s['full_name'],
            $s['username'],
            $s['role'],
            $s['created_at']
        ]);
    }
    fputcsv($output, []);
}

if ($type === 'activities' || $type === 'all') {
    fputcsv($output, ['=== سجل النشاط ===']);
    fputcsv($output, ['المستخدم', 'نوع الإجراء', 'الوصف', 'IP', 'التاريخ']);
    
    $activities = $pdo->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 500")->fetchAll();
    
    foreach ($activities as $a) {
        fputcsv($output, [
            $a['username'],
            $a['action_type'],
            $a['description'],
            $a['ip_address'],
            $a['created_at']
        ]);
    }
}

fclose($output);

log_activity($pdo, 'export_report', "تم تصدير تقرير: {$type}");
exit;
?>
