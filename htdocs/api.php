<?php
// تنظيف أي مخرجات سابقة لضمان إرسال الهيدرز بشكل سليم
ob_start();

header('Access-Control-Allow-Origin: https://ahmedkosharystore.netlify.app');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// التعامل مع طلب OPTIONS (Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require 'db.php';

// ... باقي الكود الخاص بك

// جلب اتصال قاعدة البيانات
require 'db.php';

// التأكد من جلسة الدخول
session_start();

// استرجاع المنتجات والإعدادات
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $products = [];
    $whatsapp = '';
    
    // جلب المنتجات
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    // جلب رقم الواتساب
    $sql_set = "SELECT key_value FROM settings WHERE key_name = 'whatsapp_number'";
    $res_set = mysqli_query($conn, $sql_set);
    if($res_set && $row_set = mysqli_fetch_assoc($res_set)) {
        $whatsapp = $row_set['key_value'];
    }

    echo json_encode(['products' => $products, 'whatsapp' => $whatsapp], JSON_UNESCAPED_UNICODE);
    exit;
}

// عمليات الإضافة والتحديث (يتطلب تسجيل الدخول)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بالقيام بهذه العملية']);
        exit;
    }

    // تحديث رقم الواتساب
    if (isset($_POST['action']) && $_POST['action'] === 'update_whatsapp') {
        $number = $_POST['whatsapp_number'] ?? '';
        
        $stmt = mysqli_prepare($conn, "INSERT INTO settings (key_name, key_value) VALUES ('whatsapp_number', ?) ON DUPLICATE KEY UPDATE key_value = ?");
        mysqli_stmt_bind_param($stmt, "ss", $number, $number);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'تم تحديث الرقم بنجاح']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'حدث خطأ في قاعدة البيانات']);
        }
        mysqli_stmt_close($stmt);
        exit;
    }
    $name = $_POST['name'] ?? null;
    $price = $_POST['price'] ?? null;
    $imagePath = "";

    // التأكد من وجود مجلد الرفع
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        // تجنب تداخل الأسماء
        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", basename($_FILES['image']['name']));
        $destPath = $uploadDir . $fileName;

        if(move_uploaded_file($fileTmpPath, $destPath)) {
            $imagePath = $destPath;
        }
    } else {
        // بحال فشل الرفع لسبب ما
        $imagePath = 'https://placehold.co/300x300/111/d4af37?text=No+Image';
    }

    if ($name && $price && $imagePath) {
        $stmt = mysqli_prepare($conn, "INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $price, $imagePath);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'تمت إضافة المنتج ورفع الصورة بنجاح', 'path' => $imagePath]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'حدث خطأ في قاعدة البيانات أثناء الحفظ']);
        }
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'بيانات مفقودة']);
    }
    exit;
}

// حذف منتج
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بحذف المنتجات']);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        $id = (int)$data['id'];
        
        // جلب مسار الصورة لحذفها من السيرفر (اختياري لكن مفضل)
        $imgResult = mysqli_query($conn, "SELECT image FROM products WHERE id = $id");
        if($imgResult && $row = mysqli_fetch_assoc($imgResult)){
            $imgToDelete = $row['image'];
            if(file_exists($imgToDelete) && strpos($imgToDelete, 'uploads/') !== false){
                unlink($imgToDelete);
            }
        }
        
        $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(['status' => 'success', 'message' => 'تم الحذف بنجاح']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'خطأ في قاعدة البيانات أثناء الحذف']);
        }
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'معرف رقم المنتج (id) مفقود']);
    }
    exit;
}
?>
