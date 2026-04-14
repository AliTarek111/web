<?php
// process_order.php
// Ahmed Koshary Store - Cart Order Processor
include 'includes/db.php';
session_start();

function send_telegram_notification($name, $phone, $total) {
    // ⚙️ إعدادات تيليجرام (قم بتغييرها)
    $botToken = 'YOUR_BOT_TOKEN_HERE';
    $chatId = 'YOUR_CHAT_ID_HERE';
    
    // إيقاف الدالة إذا لم يتم وضع التوكن
    if ($botToken === 'YOUR_BOT_TOKEN_HERE' || empty($botToken)) {
        return false;
    }
    
    $message = "🚨 طلب جديد من متجر أحمد كشري!\n";
    $message .= "━━━━━━━━━━━━━━━━━━\n";
    $message .= "👤 العميل: $name\n";
    $message .= "📱 واتساب: $phone\n";
    $message .= "💰 الإجمالي: " . number_format($total) . " ج.م\n";
    $message .= "━━━━━━━━━━━━━━━━━━";
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
    ];
    
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type:application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
            'timeout' => 3 // Timeout short to avoid delaying checkout
        ]
    ];
    $context  = stream_context_create($options);
    @file_get_contents($url, false, $context);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_checkout'])) {
    if (empty($_SESSION['cart'])) {
        header("Location: index.php");
        exit;
    }

    $customer_name = trim($_POST['name']);
    $customer_whatsapp = trim($_POST['phone']);
    $customer_address = trim($_POST['address']);
    $notes = trim($_POST['notes']);
    $customer_id = $_SESSION['user_id'] ?? null;

    try {
        $pdo->beginTransaction();

        // Calculate total amount
        $total_amount = 0;
        $order_items = [];
        $ids = array_keys($_SESSION['cart']);
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id IN ($in)");
        $stmt->execute($ids);
        
        $products_text = "";
        while ($row = $stmt->fetch()) {
            $qty = $_SESSION['cart'][$row['id']];
            $total_amount += $row['price'] * $qty;
            $order_items[] = ['id' => $row['id'], 'qty' => $qty, 'price' => $row['price']];
            $products_text .= "📦 {$row['name']} ($qty)\n";
        }

        // 1. Save to Database: Orders
        $stmt_order = $pdo->prepare("INSERT INTO orders (customer_name, whatsapp_number, total_amount, order_notes, customer_address, status, customer_id) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
        $stmt_order->execute([$customer_name, $customer_whatsapp, $total_amount, $notes, $customer_address, $customer_id]);
        $order_id = $pdo->lastInsertId();

        // 2. Save Order Items
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        foreach ($order_items as $item) {
            $stmt_item->execute([$order_id, $item['id'], $item['qty']]);
        }

        $pdo->commit();

        send_telegram_notification($customer_name, $customer_whatsapp, $total_amount);

        // Clear Cart
        unset($_SESSION['cart']);

        // Fetch Store WhatsApp from Settings
        $st_stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
        $st_stmt->execute();
        $store_whatsapp = $st_stmt->fetchColumn() ?: '201234567890';

        // Construct WhatsApp Message
        $address_line = $customer_address ? "\n📍 العنوان: $customer_address" : "";
        $notes_line = $notes ? "\n📝 ملاحظات: $notes" : "";
        $message = urlencode("🛍️ طلب جديد من متجر أحمد كشري\n" .
            "━━━━━━━━━━━━━━━━━━\n" .
            "👤 العميل: $customer_name\n" .
            "🔖 رقم الطلب: #ORD-$order_id\n" .
            " المنتجات:\n$products_text" .
            "💰 السعر الإجمالي: " . number_format($total_amount) . " ج.م" .
            "$address_line" .
            "$notes_line\n" .
            "━━━━━━━━━━━━━━━━━━\n" .
            "✅ تم تفريغ السلة وتأكيد الطلب.");
        
        // Redirect to success.php with whatsapp link to trigger
        $_SESSION['success_order_id'] = $order_id;
        $_SESSION['success_wa_link'] = "https://wa.me/$store_whatsapp?text=$message";
        header("Location: success.php");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("خطأ في معالجة الطلب: " . $e->getMessage());
    }
} 
// Compatible handling for direct orders if kept
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    // Quick directly bypasses cart
    $product_id = $_POST['product_id'];
    $customer_whatsapp = $_POST['whatsapp'];
    $notes = $_POST['notes'];
    $total_amount = $_POST['price'];
    $customer_address = $_POST['address'] ?? '';
    // Use user id if possible, Name can be NA for quick buy unless we prompt for it
    $customer_name = "Quick Buyer";
    $customer_id = $_SESSION['user_id'] ?? null;

    try {
        $pdo->beginTransaction();
        $stmt_order = $pdo->prepare("INSERT INTO orders (customer_name, whatsapp_number, total_amount, order_notes, customer_address, status, customer_id) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
        $stmt_order->execute([$customer_name, $customer_whatsapp, $total_amount, $notes, $customer_address, $customer_id]);
        $order_id = $pdo->lastInsertId();

        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt_item->execute([$order_id, $product_id, 1]);

        $pdo->commit();
        
        send_telegram_notification($customer_name, $customer_whatsapp, $total_amount);
        
        // redirect logic as before
        $prod_stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $prod_stmt->execute([$product_id]);
        $product_name = $prod_stmt->fetchColumn();

        $st_stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
        $st_stmt->execute();
        $store_whatsapp = $st_stmt->fetchColumn() ?: '201234567890';

        $address_line = $customer_address ? "\n📍 العنوان: $customer_address" : "";
        $notes_line = $notes ? "\n📝 ملاحظات: $notes" : "";
        $message = urlencode("🛍️ طلب جديد سريع\n" .
            "━━━━━━━━━━━━━━━━━━\n" .
            "🔖 رقم الطلب: #ORD-$order_id\n" .
            "📦 المنتج: $product_name\n" .
            "💰 السعر: " . number_format($total_amount) . " ج.م" .
            "$address_line\n$notes_line\n" .
            "━━━━━━━━━━━━━━━━━━\n" .
            "✅ تم تسجيل الطلب.");
        
        $wa_link = "https://wa.me/$store_whatsapp?text=$message";
        header("Location: $wa_link");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("خطأ في المعالجة: " . $e->getMessage());
    }
}
else {
    header("Location: index.php");
    exit();
}
?>
