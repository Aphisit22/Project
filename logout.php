<?php
session_start();

// เคลียร์ session ทั้งหมด
session_unset();
session_destroy();

// เริ่ม session ใหม่เพื่อเก็บ flag สำหรับ Toast
session_start();
$_SESSION['logout_success'] = true;
// กลับไปหน้าแรก
header("Location: index.php");
exit;
?>