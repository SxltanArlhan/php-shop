<?php
session_start();
// ล้าง session ทั้งหมดและทำลาย
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// กลับไปหน้า login พร้อมพารามิเตอร์แจ้งสถานะ
header("Location: login.php?logout=1");
exit;
