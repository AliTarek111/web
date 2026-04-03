<?php
session_start();
include '../../db.php'; // اخرج خطوتين لملف القاعدة الرئيسي

// حماية حديدية: التأكد من السيشن وصلاحية الأدمن
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login"); exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>THE BOSS | نظام الإدارة المتكامل 🛡️</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { --main-bg: #0b0f19; --card-bg: #151a26; --gold: #D4AF37; --text-muted: #8b949e; }
        body { background-color: var(--main-bg); color: #e6edf3; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        .sidebar { width: 260px; background: var(--card-bg); height: 100vh; position: fixed; right: 0; top: 0; border-left: 1px solid #30363d; z-index: 1000; }
        .main-content { margin-right: 260px; padding: 30px; transition: 0.3s; }
        .boss-card { background: var(--card-bg); border: 1px solid #30363d; border-radius: 12px; padding: 20px; transition: 0.3s; }
        .boss-card:hover { border-color: var(--gold); box-shadow: 0 0 15px rgba(212, 175, 55, 0.1); }
        .nav-link { color: var(--text-muted); padding: 12px 20px; border-radius: 8px; margin: 4px 15px; display: flex; align-items: center; gap: 10px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: rgba(212, 175, 55, 0.1); color: var(--gold); }
    </style>
</head>
<body>