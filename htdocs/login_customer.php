<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass = $_POST['password'];

    // البحث عن الزبون برقم التليفون
    $result = mysqli_query($conn, "SELECT * FROM customers WHERE BINARY phone_number='$phone'");
    $customer = mysqli_fetch_assoc($result);

    // التحقق من الباسورد (لو مستخدم Hashing)
    if ($customer && password_verify($pass, $customer['password'])) {
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_name'] = $customer['full_name'];
        $_SESSION['customer_phone'] = $customer['phone_number'];
        $_SESSION['customer_address'] = $customer['address'];
        
        header('Location: index.php'); // يرجعه للمتجر وهو مسجل دخول
        exit;
    } else {
        $error = "رقم الموبايل أو الباسورد غلط يا فنان!";
    }
}
?>