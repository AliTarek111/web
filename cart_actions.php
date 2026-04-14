<?php
// cart_actions.php
// Ahmed Koshary Store - AJAX Shopping Cart API
session_start();
include 'includes/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If sent via raw JSON body (fetch api)
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);
    if ($input) {
        $_POST = array_merge($_POST, $input);
        $is_ajax = true;
    }

    $action = $_POST['action'] ?? '';
    // Special action to just get cart state
    if ($action !== 'get') {
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if ($product_id > 0) {
            if ($action === 'add') {
                $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = $quantity;
                }
            } 
            elseif ($action === 'update') {
                $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] = $quantity;
                }
            } 
            elseif ($action === 'remove') {
                if (isset($_SESSION['cart'][$product_id])) {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
        }
    }
}

// Prepare cart data to return
$cart_details = [];
$total_amount = 0;
$cart_count = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $in = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, name, price, main_image, condition_status FROM products WHERE id IN ($in)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $cart_count += 1; // Or $qty if count means total quantity
        $total_amount += $p['price'] * $qty;
        
        $p['quantity'] = $qty;
        $cart_details[] = $p;
    }
}

// Return JSON response if ajax
if ($is_ajax || (isset($_POST['ajax']) && $_POST['ajax'] == 1)) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'cart_count' => count($_SESSION['cart']),
        'total' => $total_amount,
        'items' => $cart_details
    ]);
    exit;
}

// Fallback to normal HTTP logic if somehow not JS
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    
    // Support GET request for clearing cart easily via link
    if (isset($_GET['action']) && $_GET['action'] == 'clear') {
        $_SESSION['cart'] = [];
        header("Location: cart.php");
        exit;
    }

    $redirect = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
    // Ensure if we fallback from inside cart or index, we probably want to see cart.php now
    // unless they specifically stayed on index
    if (strpos($redirect, 'cart_actions.php') !== false || empty($_SERVER['HTTP_REFERER'])) {
        $redirect = 'cart.php';
    } else {
        // Force redirect to cart if it was form submit
        $redirect = 'cart.php';
    }
    header("Location: " . $redirect);
    exit;
}
header("Location: cart.php");
exit;
?>
