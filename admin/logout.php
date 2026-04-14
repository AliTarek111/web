<?php
// admin/logout.php
// Ahmed Koshary Store - Secure Exit
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
