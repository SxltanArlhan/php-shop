<?php 
    session_start();
    require_once 'config.php';

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usernameOrEmail = trim($_POST['username_or_email']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] === 'admin'){
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <style>
  :root {
    --bg: #000;
    --text: #e5e7eb;
    --muted: #9ca3af;
    --card: #111;
    --card-2: #171717;
    --border: rgba(255,255,255,.08);
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
    font-family: 'Poppins', system-ui, Segoe UI, Roboto, Arial, sans-serif;
    color: var(--text);
    background: var(--bg);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
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
    animation: moveGlow 30s ease-in-out infinite alternate;
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



  .login-card {
    width: 100%;
    max-width: 440px;
    margin: auto;
    padding: 34px 28px 28px;
    background: rgba(17,17,17,.78);
    border-radius: 20px;
    border: 1px solid var(--border);
    box-shadow:
      0 8px 30px rgba(0,0,0,.45),
      inset 0 0 0 1px rgba(255,255,255,.02);
    backdrop-filter: blur(10px);
  }

  .login-card h3 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 800;
    letter-spacing: .3px;
    color: var(--text);
    text-shadow: 0 6px 30px rgba(0,0,0,.45);
  }



  .form-label {
    font-weight: 600;
    color: var(--text);
  }

  .form-control {
    background: #0f0f10;
    color: var(--text);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 12px;
    font-size: 1.05em;
    box-shadow: none;
  }

  .form-control::placeholder {
    color: #8a8f98;
  }

  .form-control:focus {
    background: #131316;
    color: var(--text);
    border-color: transparent;
    outline: 2px solid rgba(0,255,117,.35);
    box-shadow: 0 0 0 6px rgba(0,255,117,.08);
  }



  .btn-primary {
    width: 100%;
    border-radius: 12px;
    background: linear-gradient(163deg, var(--glow-1), var(--glow-2));
    border: 1px solid rgba(255,255,255,.08);
    color: #0a0a0a;
    font-weight: 800;
    padding: 12px;
    font-size: 1.05em;
    box-shadow: 0 12px 24px rgba(0,0,0,.35);
    transition: .2s ease-in-out;
  }

  .btn-primary:hover {
    filter: brightness(1.05) saturate(1.08);
    transform: translateY(-1px);
  }

  .btn-link {
    display: block;
    width: 100%;
    text-align: center;
    margin-top: 12px;
    color: #9aa5ff;
    font-weight: 700;
    font-size: 1.02em;
    text-decoration: none;
  }

  .btn-link:hover {
    color: #c3c8ff;
    text-decoration: underline;
  }



  .alert {
    max-width: 520px;
    margin: 10px auto 18px;
    border-radius: 14px;
    font-size: 1.02em;
    border: 1px solid rgba(255,255,255,.12);
    background: rgba(23,23,23,.8);
    color: var(--text);
    backdrop-filter: blur(8px);
  }

  .alert-success {
    border-color: rgba(0,255,117,.25);
    box-shadow: 0 0 24px rgba(0,255,117,.08);
  }

  .alert-danger {
    border-color: rgba(255,64,64,.25);
    box-shadow: 0 0 24px rgba(255,64,64,.08);
  }



  .g-3 {
    --bs-gutter-y: 0.9rem;
  }



  @media (max-width: 480px) {
    .login-card {
      padding: 26px 18px 20px;
    }
  }
</style>

</head>
<body>
    <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
        <div class="alert alert-success text-center shadow-sm"> ✅ สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center shadow-sm">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="login-card">
        <h3> LOGIN </h3>
        <form method="post" class="row g-3">
            <div class="col-12">
                <label for="username_or_email" class="form-label">User</label>
                <input type="text" name="username_or_email" id="username_or_email" class="form-control" required>
            </div>
            <div class="col-12">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Login</button>
                <a href="register.php" class="btn btn-link">Register</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
