<?php
require_once 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $username = trim($_POST['username']);
    $fname = trim($_POST['fname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // ตรวจสอบรหัสผ่านกับยืนยันรหัสผ่าน
    if ($password !== $cpassword) {
        $message = "รหัสผ่านไม่ตรงกัน";
    } else {
        // ตรวจสอบว่าผู้ใช้ซ้ำหรือไม่
        $checkSql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$username, $email]);
        if ($checkStmt->rowCount() > 0) {
            $message = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว";
        } else {
            // บันทึกลงฐานข้อมูล
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users(username, password, email, full_name, role) VALUES (?, ?, ?, ?, 'admin')";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$username, $hashedPassword, $email, $fname])) {
                $message = "สมัครสมาชิกเรียบร้อยแล้ว";
            } else {
                $message = "เกิดข้อผิดพลาดในการสมัครสมาชิก";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>สมัครสมาชิก</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info mt-3"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <div class="mb-3">
            <label for="username" class="form-label">ชื่อผู้ใช้</label>
            <input type="text" name="username" class="form-control" id="username" required>
        </div>
        <div class="mb-3">
            <label for="fname" class="form-label">ชื่อ - นามสกุล</label>
            <input type="text" name="fname" class="form-control" id="fname" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">อีเมล</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">รหัสผ่าน</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>
        <div class="mb-3">
            <label for="cpassword" class="form-label">ยืนยันรหัสผ่าน</label>
            <input type="password" name="cpassword" class="form-control" id="cpassword" required>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
            <a href="login.php" class="btn btn-link">เข้าสู่ระบบ</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
