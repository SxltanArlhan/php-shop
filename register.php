<?php
require_once 'config.php'; 
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $fname     = trim($_POST['fname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';

    // ตรวจอินพุตพื้นฐาน
    if ($username === '' || $fname === '' || $email === '' || $password === '' || $cpassword === '') {
        $error[] = "กรุณากรอกให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "กรุณากรอกอีเมลให้ถูกต้อง";
    } elseif ($password !== $cpassword) {
        $error[] = "รหัสผ่านไม่ตรงกัน";
    } elseif (strlen($password) < 6) {
        $error[] = "รหัสผ่านควรมีอย่างน้อย 6 ตัวอักษร";
    }

    if (empty($error)) {
        try {
            
            $sql  = "SELECT 1 FROM users WHERE username = ? OR email = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้แล้ว";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql  = "INSERT INTO users (username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'member')";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$username, $fname, $email, $hashedPassword]);

                header("Location: login.php?register=success");
                exit;
            }
        } catch (PDOException $e) {
            // ข้อความนี้ควร log จริงๆ แล้วแสดงข้อความกลางๆ ให้ผู้ใช้
            $error[] = "ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง";
            // error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สมัครสมาชิก</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
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
    background: rgba(17,17,17,.78);
    border-radius: 20px;
    padding: 32px 26px;
    border: 1px solid var(--border);
    box-shadow:
      0 10px 28px rgba(0,0,0,.45),
      inset 0 0 0 1px rgba(255,255,255,.03);
    backdrop-filter: blur(10px);
  }

  .register-card h2 {
    color: var(--text);
    font-weight: 800;
    letter-spacing: .3px;
    text-shadow: 0 6px 30px rgba(0,0,0,.45);
    margin-bottom: 18px;
    text-align: center;
  }



  .register-card .row.g-3 {
    --bs-gutter-x: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .register-card .row.g-3 > .col-md-6,
  .register-card .row.g-3 > .col-12 {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
  }



  .form-label {
    font-weight: 700;
    color: var(--text);
    margin-bottom: 6px;
  }

  .form-control {
    background: #0f0f10;
    color: var(--text);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 12px;
    font-size: 1.04em;
    box-shadow: none;
  }

  .form-control::placeholder { color: #8a8f98; }

  .form-control:focus {
    background: #141418;
    color: var(--text);
    border-color: transparent;
    outline: 2px solid rgba(0,255,117,.35);
    box-shadow: 0 0 0 6px rgba(0,255,117,.08);
  }



  .btn-primary {
    border-radius: 12px;
    background: linear-gradient(163deg, var(--glow-1), var(--glow-2));
    border: 1px solid rgba(255,255,255,.10);
    color: #0a0a0a;
    font-weight: 800;
    padding: 12px;
    box-shadow: 0 12px 24px rgba(0,0,0,.35);
    transition: .2s ease-in-out;
  }

  .btn-primary:hover {
    filter: brightness(1.05) saturate(1.08);
    transform: translateY(-1px);
  }

  .btn-outline-secondary {
    border-radius: 12px;
    color: #cfd3ff;
    border-color: #5b5be6;
    background: #0f0f12;
    font-weight: 700;
  }

  .btn-outline-secondary:hover {
    color: #fff;
    background: linear-gradient(163deg, #6a5cff, #3f32ff);
    border-color: transparent;
    box-shadow: 0 8px 20px rgba(63,50,255,.35);
  }



  .alert {
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(23,23,23,.85);
    color: var(--text);
    backdrop-filter: blur(8px);
  }

  .alert-danger {
    border-color: rgba(255,64,64,.28);
    box-shadow: 0 0 24px rgba(255,64,64,.10);
  }

  .alert-success {
    border-color: rgba(0,255,117,.28);
    box-shadow: 0 0 24px rgba(0,255,117,.10);
  }



  .g-3 { --bs-gutter-y: 1rem; }
  .container { max-width: 1140px; }



  @media (max-width: 576px) {
    .register-card { padding: 24px 18px; }
  }
</style>

</head>
<body>
<div class="container my-5">
<div class="row justify-content-center">
    <div class="col-lg-8">
    <div class="register-card">
        <h2 class="text-center mb-4">Rigister</h2>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
            <?php foreach ($error as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="post" action="">
        <div class="row g-3">
            <div class="col-md-6">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="ชื่อผู้ใช้"
                value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="col-md-6">
            <label for="fname" class="form-label">Fullname</label>
            <input type="text" class="form-control" id="fname" name="fname" placeholder="ชื่อ - นามสกุล"
                value="<?= htmlspecialchars($_POST['fname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="col-md-6">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="example@email.com"
                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="รหัสผ่าน" required>
            </div>
            <div class="col-md-6">
            <label for="cpassword" class="form-label">Confirmpassword</label>
            <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="ยืนยันรหัสผ่าน" required>
            </div>
        </div>
        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Register</button>
            <a href="login.php" class="btn btn-outline-secondary">Back login</a>
        </div>
        </form>

    </div>
    </div>
</div>
</div>
</body>
</html>
