<?php
require '../config.php'; // TODO-1: เชื่อมต่อข้อมูลด้วย PDO
require 'auth_admin.php'; // TODO-2: กำหนดสิทธิ์ (Admin Guard)

// TODO-3: ตรวจสอบว่ามีพารามิเตอร์ id มาจริงไหม (ผ่าน GET)
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

// TODO-4: ดึงค่า id และ "แคสต์เป็น int" เพื่อความปลอดภัย
$user_id = (int)$_GET['id'];

// ดึงข้อมูลสมาชิกที่จะถูกแก้ไข
/*
TODO-5: เตรียม/รัน SELECT (เฉพาะ role = 'member')
SQL แนะนำ:
SELECT * FROM users WHERE user_id = ? AND role = 'member'
- ใช้ prepare + execute([$user_id])
- fetch(PDO::FETCH_ASSOC) แล้วเก็บใน $user
*/
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'member'");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// TODO-6: ถ้าไม่พบข้อมูล -> แสดงข้อความเเละ exit;
if (!$user) {
    echo "<h3>ไม่พบสมาชิก</h3>";
    exit;
}

// ========== เมื่อผู้ใช้กด Submit ฟอร์ม ==========
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO-7: รับค่า POST + trim
    $username  = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);

    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // TODO-8: ตรวจสอบความครบถ้วน และตรวจรูปแบบ email
    if ($username === '' || $email === '') {
        $error = "กรุณากรอกข้อมูลให้ครบ";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบ Email ไม่ถูกต้อง";
    }

    // TODO-9: ถ้า validate ผ่าน ให้ตรวจสอบซ้ำ (username/email ชนกับคนอื่นที่ไม่ใช่ตัวเองหรือไม่)
    // SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?
    if (!$error) {
        $chk = $conn->prepare("SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $chk->execute([$username, $email, $user_id]);
        if ($chk->fetch()) {
            $error = "Username หรือ Email มีอยู่ในระบบแล้ว";
        }
    }

    // ตรวจรหัสผ่าน (กรณีต้องการเปลี่ยน) — อนุญาตให้ปล่อยว่างได้
    $updatePassword = false;
    $hashed = null;
    if (!$error && ($password !== '' || $confirm !== '')) {
        if (strlen($password) < 6) {
            $error = "รหัสผ่านต้องมีอย่างน้อย 6 ตัว";
        } elseif ($password !== $confirm) {
            $error = "รหัสใหม่กับยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $updatePassword = true;
        }
    }

    // อัปเดตข้อมูล
    if (!$error) {
        if ($updatePassword) {
            $sql  = "UPDATE users SET username = ?, full_name = ?, email = ?, password = ? WHERE user_id = ?";
            $args = [$username, $full_name, $email, $hashed, $user_id];
        } else {
            $sql  = "UPDATE users SET username = ?, full_name = ?, email = ? WHERE user_id = ?";
            $args = [$username, $full_name, $email, $user_id];
        }
        $upd = $conn->prepare($sql);
        $upd->execute($args);
        header("Location: users.php");
        exit;
    }

    // OPTIONAL: อัปเดตค่า $user เพื่อสะท้อนค่าบนฟอร์ม (หากมี error)
    $user['username']  = $username;
    $user['full_name'] = $full_name;
    $user['email']     = $email;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขสมาชิก</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap (ตามไฟล์เดิม) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ฟอนต์ Sarabun ให้เข้ากับธีม -->
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <!-- === Style Block (จากที่คุณส่งมา) + เสริมเล็กน้อยให้ฟอร์มเข้าธีมดาร์ก === -->
  <style>
  :root {
    --bg: #000;
    --text: #e5e7eb;
    --muted: #9ca3af;
    --card: #111;
    --card-2: #171717;
    --border: rgba(255,255,255,.10);
    --glow-1: #00ff75;
    --glow-2: #3700ff;
    --glow-3: #00e5ff;
    --glow-4: #ff00cc;
    --accent: #00ffa8;
  }

  html, body { height: 100%; }

  body {
    margin: 0;
    min-height: 100vh;
    overflow-x: hidden;
    font-family: 'Sarabun', system-ui, Segoe UI, Roboto, Arial, sans-serif;
    color: var(--text);
    background: var(--bg);
    padding: 24px 12px;
  }

  body::before {
    content: "";
    position: fixed;
    inset: -25vmax -25vmax;
    z-index: -2;
    filter: blur(40px) saturate(120%);
    opacity: .8;
    background:
      radial-gradient(closest-side at 20% 25%, var(--glow-1), transparent 60%),
      radial-gradient(closest-side at 80% 20%, var(--glow-2), transparent 62%),
      radial-gradient(closest-side at 15% 80%, var(--glow-3), transparent 58%),
      radial-gradient(closest-side at 85% 75%, var(--glow-4), transparent 60%);
    animation: moveGlow 28s ease-in-out infinite alternate;
    pointer-events: none;
  }

  body::after {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background:
      radial-gradient(120vmax 120vmax at 50% 50%, rgba(255,255,255,.02), transparent 60%),
      repeating-conic-gradient(from 0deg, rgba(255,255,255,.01) 0deg 2deg, transparent 2deg 4deg);
    mix-blend-mode: overlay;
    opacity: .35;
    animation: grain 8s steps(6, end) infinite;
  }

  @keyframes moveGlow {
    0%   { transform: translate(0, 0) scale(1); }
    100% { transform: translate(4vmax, -3vmax) scale(1.05); }
  }

  @keyframes grain {
    0%   { transform: translate(0, 0); }
    20%  { transform: translate(-10px, 6px); }
    40%  { transform: translate(6px, -4px); }
    60%  { transform: translate(-4px, -8px); }
    80%  { transform: translate(12px, 10px); }
    100% { transform: translate(0, 0); }
  }

  .register-card {
    background: rgba(17,17,17,.80);
    border-radius: 20px;
    padding: 32px 26px;
    border: 1px solid var(--border);
    box-shadow:
      0 10px 28px rgba(0,0,0,.45),
      inset 0 0 0 1px rgba(255,255,255,.03);
    backdrop-filter: blur(10px);
    max-width: 1140px;
    margin: 0 auto;
  }

  .register-card h2 {
    color: var(--text);
    font-weight: 800;
    letter-spacing: .3px;
    text-shadow: 0 6px 30px rgba(0,0,0,.45);
    margin-bottom: 10px;
  }

  .register-card p {
    color: var(--muted);
    font-weight: 700;
    margin-bottom: 24px !important;
  }

  .btn {
    border-radius: 14px !important;
    font-weight: 800 !important;
    padding: 12px 14px !important;
    border: 1px solid rgba(255,255,255,.10) !important;
    box-shadow: 0 10px 22px rgba(0,0,0,.35) !important;
    transition: .2s ease-in-out !important;
  }
  .btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.05) saturate(1.06);
  }
  .btn-primary {
    background: linear-gradient(163deg, #6a5cff, #3f32ff) !important;
    color: #0b140d !important;
    border: none !important;
  }
  .btn-success {
    background: linear-gradient(163deg, var(--glow-1), #00d65a) !important;
    color: #0b140d !important;
    border: none !important;
  }
  .btn-warning {
    background: linear-gradient(163deg, #ffd54f, #ffb300) !important;
    color: #0b140d !important;
    border: none !important;
  }
  .btn-dark {
    background: linear-gradient(163deg, #2d2d2d, #111113) !important;
    color: #e5e7eb !important;
    border-color: rgba(255,255,255,.14) !important;
  }
  .btn-secondary {
    background: #0f0f12 !important;
    color: #cfd3ff !important;
    border-color: #5b5be6 !important;
  }
  .btn-secondary:hover {
    color: #fff !important;
    background: linear-gradient(163deg, #6a5cff, #3f32ff) !important;
    border-color: transparent !important;
    box-shadow: 0 8px 20px rgba(63,50,255,.35) !important;
  }

  .row > [class*="col-"] { margin-bottom: 12px; }

  .container { max-width: 1140px; }

  /* เสริม: ให้ input/form เป็นดาร์กเข้ากับธีม โดยไม่กระทบไฟล์อื่น */
  .register-card .form-label { color: var(--text); font-weight: 700; }
  .register-card .form-control {
    background: #0f0f12;
    color: var(--text);
    border: 1px solid var(--border);
    border-radius: 12px;
  }
  .register-card .form-control:focus {
    outline: none;
    box-shadow: 0 0 0 4px rgba(106,92,255,.18);
    border-color: #6a5cff;
  }
  .register-card .text-muted { color: var(--muted) !important; }

  /* สไตล์ alert ให้เข้าธีม */
  .alert-glass {
    background: rgba(255, 77, 77, .08);
    border: 1px solid rgba(255, 77, 77, .35);
    color: #ffdede;
    border-radius: 14px;
  }

  @media (max-width: 576px) {
    .register-card { padding: 24px 18px; }
    .btn { padding: 12px; }
  }
  </style>
</head>
<body>
  <div class="container">
    <div class="register-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h2 class="mb-1">แก้ไขข้อมูลสมาชิก</h2>
          <p class="mb-0">อัปเดตข้อมูลผู้ใช้งาน (เฉพาะสมาชิกทั่วไป)</p>
        </div>
        <div>
          <a href="users.php" class="btn btn-secondary">← กลับหน้ารายชื่อสมาชิก</a>
        </div>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-glass mb-3" role="alert">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required
                 value="<?= htmlspecialchars($user['username']) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Fullname</label>
          <input type="text" name="full_name" class="form-control"
                 value="<?= htmlspecialchars($user['full_name']) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required
                 value="<?= htmlspecialchars($user['email']) ?>">
        </div>

        <div class="col-12">
          <hr style="border-color: rgba(255,255,255,.08);">
          <p class="text-muted mb-2">เปลี่ยนรหัสผ่าน (ถ้าไม่ต้องการเปลี่ยนให้เว้นว่างไว้)</p>
        </div>

        <div class="col-md-6">
          <label class="form-label">รหัสผ่านใหม่</label>
          <input type="password" name="password" class="form-control" placeholder="ใส่รหัสผ่านใหม่ (อย่างน้อย 6 ตัว)">
        </div>
        <div class="col-md-6">
          <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="กรอกรหัสผ่านใหม่อีกครั้ง">
        </div>

        <div class="col-12 d-flex gap-2 mt-2">
          <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
          <a href="users.php" class="btn btn-dark">ยกเลิก</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
