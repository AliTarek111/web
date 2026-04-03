<?php
error_reporting(0); 
ini_set('display_errors', 0);
session_start();
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>متجر أحمد كُشري | الفخامة الملكية 📱</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* تحسينات الـ Card الاحترافية */
        .product-card {
            background: #151515;
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 20px;
            padding: 15px;
            transition: 0.4s;
            position: relative;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-10px);
            border-color: #D4AF37;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.1);
        }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 15px;
        }
        .product-desc {
            color: #888;
            font-size: 0.85rem;
            margin: 10px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* عرض سطرين فقط */
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 40px;
        }
        .price {
            color: #D4AF37;
            font-size: 1.4rem;
            font-weight: 800;
            display: block;
            margin: 10px 0;
        }
        .card-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }
        .wa-btn {
            background: #25D366;
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 10px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .order-btn {
            background: #D4AF37;
            color: black;
            border: none;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<header class="glass-header">
    <div class="navbar">
        <div class="logo"><img src="logo.png" alt="Logo"></div>
        <nav class="nav-links">
            <a href="index.php">الرئيسية</a>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" style="color:#D4AF37">لوحة التحكم 🛡️</a>
            <?php endif; ?>
            <a href="logout.php">خروج</a>
        </nav>
    </div>
</header>

<main style="padding-top: 100px;">
    <section id="categories" style="padding: 40px 20px; text-align: center;">
        <h2 class="section-title">الأقسام الملكية</h2>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 20px;">
            <a href="index.php" class="cta-btn" style="background:#222; border:1px solid #D4AF37; color:white; text-decoration:none;">الكل</a>
            <?php
            // جلب الأقسام من المنتجات الموجودة فعلاً (تحكم تلقائي)
            $cat_query = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE status = 'available'");
            while($cat = mysqli_fetch_assoc($cat_query)):
            ?>
                <a href="index.php?cat=<?php echo $cat['category']; ?>" class="cta-btn" style="background:#151515; border:1px solid #333; color:#888; text-decoration:none;">
                    <?php echo $cat['category']; ?>
                </a>
            <?php endwhile; ?>
        </div>
    </section>

    <section id="products" style="padding: 20px;">
        <div class="product-list" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; max-width: 1200px; margin: 0 auto;">
            <?php
            // فلترة حسب القسم
            $where = "WHERE status = 'available'";
            if(isset($_GET['cat'])) {
                $c = mysqli_real_escape_string($conn, $_GET['cat']);
                $where .= " AND category = '$c'";
            }

            $query = mysqli_query($conn, "SELECT * FROM products $where ORDER BY id DESC");
            if(mysqli_num_rows($query) > 0):
                while($row = mysqli_fetch_assoc($query)):
                    $wa_link = "https://wa.me/201000000000?text=" . urlencode("أهلاً يا بشمهندس، محتاج أستفسر عن: " . $row['name']);
            ?>
                <div class="product-card">
                    <img src="uploads/<?php echo $row['image']; ?>" alt="Product">
                    <h3 style="margin:0; font-size: 1.1rem;"><?php echo $row['name']; ?></h3>
                    
                    <p class="product-desc"><?php echo $row['description'] ?: 'أقوى العروض بضمان متجر أحمد كشري، حالة زيرو وأداء جبار.'; ?></p>
                    
                    <?php if(isset($_SESSION['logged_in'])): ?>
                        <span class="price"><?php echo number_format($row['price'], 0); ?> ج.م</span>
                        <div class="card-buttons">
                            <a href="<?php echo $wa_link; ?>" target="_blank" class="wa-btn">واتساب 💬</a>
                            <button class="order-btn" onclick="addToCart(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', <?php echo $row['price']; ?>)">شراء 🛒</button>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="order-btn" style="display:block; text-align:center; text-decoration:none; margin-top:10px;">سجل دخول لمشاهدة السعر</a>
                    <?php endif; ?>
                </div>
            <?php 
                endwhile;
            else:
                echo "<p style='text-align:center; grid-column: 1/-1;'>لا توجد منتجات في هذا القسم حالياً.</p>";
            endif;
            ?>
        </div>
    </section>
</main>

<script src="script.js"></script>
</body>
</html>