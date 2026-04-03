<?php
session_start();
include 'db.php';

// تأمين الصفحة: لو مش مسجل دخول يرجعه للوجن
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// جلب بيانات المستخدم من القاعدة
$user_query = mysqli_query($conn, "SELECT * FROM customers WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حسابي | متجر أحمد كُشري ✨</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #0a0a0a; color: #fff; }
        .profile-container {
            max-width: 900px;
            margin: 120px auto 50px;
            padding: 20px;
        }
        .profile-card {
            background: #151515;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 20px;
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 30px;
        }
        .user-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #D4AF37, #f2d06b);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 3rem;
            color: #000;
        }
        .user-info h2 { color: #D4AF37; margin: 0; font-size: 1.8rem; }
        .user-info p { color: #888; margin: 5px 0 0; }

        .orders-section {
            background: #151515;
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        .section-title {
            color: #D4AF37;
            border-right: 4px solid #D4AF37;
            padding-right: 15px;
            margin-bottom: 25px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .order-table th { color: #D4AF37; text-align: right; padding: 15px; border-bottom: 1px solid #333; }
        .order-table td { padding: 15px; border-bottom: 1px solid #222; color: #ccc; }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            background: rgba(212, 175, 55, 0.1);
            color: #D4AF37;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            color: #D4AF37;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header class="glass-header">
    <div class="navbar">
        <div class="logo">
            <a href="index.php"><img src="logo.png" alt="Logo"></a>
        </div>
        <nav class="nav-links">
            <a href="index.php">الرئيسية</a>
            <a href="logout.php" style="color: #ff4d4d;">خروج</a>
        </nav>
    </div>
</header>

<div class="profile-container">
    <div class="profile-card">
        <div class="user-avatar">
            <?php echo mb_substr($user_data['full_name'], 0, 1, 'utf-8'); ?>
        </div>
        <div class="user-info">
            <h2><?php echo $user_data['full_name']; ?></h2>
            <p>رقم الموبايل: <?php echo $user_data['phone_number']; ?></p>
            <p>تاريخ الانضمام: <?php echo date('Y-m-d', strtotime($user_data['created_at'] ?? 'now')); ?></p>
        </div>
    </div>

    <div class="orders-section">
        <h3 class="section-title">طلباتي الأخيرة 📦</h3>
        
        <table class="order-table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>التاريخ</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // جلب طلبات العميل من جدول الـ orders (لو عندك الجدول ده جاهز)
                $orders_query = mysqli_query($conn, "SELECT * FROM orders WHERE customer_id = '$user_id' ORDER BY id DESC LIMIT 5");
                
                if (mysqli_num_rows($orders_query) > 0) {
                    while ($order = mysqli_fetch_assoc($orders_query)) {
                        echo "<tr>
                                <td>#{$order['id']}</td>
                                <td>" . date('Y-m-d', strtotime($order['created_at'])) . "</td>
                                <td>" . number_format($order['total_price'], 0) . " ج.م</td>
                                <td><span class='status-badge'>{$order['status']}</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; padding:30px; color:#666;'>لم تقم بأي طلبات بعد.. اكتشف أحدث الموبايلات الآن!</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <a href="index.php" class="back-btn">← العودة للتسوق</a>
    </div>
</div>

</body>
</html>