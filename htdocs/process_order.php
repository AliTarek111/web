<?php
session_start();
include 'db.php';

// 1. التأكد أن العميل مسجل دخول
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'customer') {
    echo "error_auth";
    exit;
}

// 2. استلام البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $customer_id = $_SESSION['user_id'] ?? $_SESSION['customer_id'];
    $items_string = $_POST['items'];
    $total_selling = (float)$_POST['total'];
    $total_cost = 0;
    $status = "Pending";

    // 3. تحليل المنتجات لحساب التكلفة وتحديث المخزن
    $item_names = explode(', ', $items_string);
    foreach ($item_names as $name) {
        $name_safe = mysqli_real_escape_string($conn, $name);
        $p_res = mysqli_query($conn, "SELECT id, cost_price, price FROM products WHERE name = '$name_safe' LIMIT 1");
        if ($row = mysqli_fetch_assoc($p_res)) {
            $total_cost += (float)$row['cost_price'];
            // تقليل المخزن بمقدار 1 لكل قطعة
            mysqli_query($conn, "UPDATE products SET stock = stock - 1 WHERE id = {$row['id']}");
        }
    }

    // 4. حفظ الطلب مع "لقطة" للأسعار (Snapshot) لضمان دقة الأرباح حتى لو تغير السعر مستقبلاً
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, product_details, total_price, cost_price, selling_price, order_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isddds", $customer_id, $items_string, $total_selling, $total_cost, $total_selling, $status);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error_db: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    echo "invalid_request";
}
?>