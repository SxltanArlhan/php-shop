<?php
require_once 'config.php';

$error = []; 
$dbError = null;
$dbErrorDetail = null;
$registerMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username  = trim($_POST['username'] ?? '');
        $fname     = trim($_POST['fname'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $cpassword = $_POST['cpassword'] ?? '';

        if (empty($username) || empty($fname) || empty($email) || empty($password) || empty($cpassword)) {
            $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = "กรุณากรอกอีเมลให้ถูกต้อง";
        } elseif ($password !== $cpassword) {
            $error[] = "รหัสผ่านไม่ตรงกัน";
        } else {
            $sql = "SELECT user_id FROM users WHERE username = ? OR email = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $error[] = "Username หรือ Email นี้ถูกใช้แล้ว";
            }
        }

        if (empty($error)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users(username, full_name, email, password, role)
                    VALUES (?,?,?,?,'member')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username, $fname, $email, $hashedPassword]);

            header("Location: login.php?register=success");
            exit;
        }
    } catch (PDOException $e) {
        $dbError = "เชื่อมต่อฐานข้อมูลไม่สำเร็จ";
        $dbErrorDetail = "รายละเอียด: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>สมัครสมาชิก | NeonShop</title>
  <style>
    :root{
      --bg:#000;
      --card-bg:#171717;
      --glow-1:#00ff75;
      --glow-2:#3700ff;
      --glow-3:#00e5ff;
      --glow-4:#ff00cc;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      min-height:100vh;
      margin:0;
      font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:#e5e7eb;
      background: var(--bg);
      overflow:hidden;
      display:flex; align-items:center; justify-content:center;
      padding:24px;
    }

    .bg{
      position: fixed; inset: -20vmax -20vmax;
      filter: blur(40px) saturate(120%);
      opacity:.75;
      z-index: 0;
    }
    .blob{
      position:absolute;
      border-radius:50%;
      will-change: transform, opacity;
      mix-blend-mode: screen;
      width:60vmax; height:60vmax;
      opacity:.65;
      animation-timing-function: ease-in-out;
      animation-iteration-count: infinite;
    }
    .blob.b1{
      left:-10vmax; top:-10vmax;
      background: radial-gradient(closest-side at 40% 40%, var(--glow-1), transparent 60%);
      animation: move1 18s alternate infinite, spin1 28s linear infinite;
    }
    .blob.b2{
      right:-8vmax; top:-12vmax;
      background: radial-gradient(closest-side at 60% 30%, var(--glow-2), transparent 65%);
      animation: move2 22s alternate-reverse infinite, spin2 32s linear infinite;
    }
    .blob.b3{
      left:-12vmax; bottom:-10vmax;
      background: radial-gradient(closest-side at 50% 50%, var(--glow-3), transparent 60%);
      animation: move3 24s alternate infinite, spin3 36s linear infinite;
    }
    .blob.b4{
      right:-14vmax; bottom:-8vmax;
      background: radial-gradient(closest-side at 45% 55%, var(--glow-4), transparent 62%);
      animation: move4 26s alternate-reverse infinite, spin4 40s linear infinite;
    }
    .grain{
      position: fixed; inset:0;
      pointer-events:none;
      z-index:0;
      background:
        radial-gradient(120vmax 120vmax at 50% 50%, rgba(255,255,255,.02), transparent 60%),
        repeating-conic-gradient(from 0deg, rgba(255,255,255,.01) 0deg 2deg, transparent 2deg 4deg);
      mix-blend-mode: overlay;
      animation: grainShift 8s steps(6,end) infinite;
      opacity:.35;
    }
    @keyframes move1{
      0%{ transform: translate(0,0) scale(1.05); }
      100%{ transform: translate(10vmax, 6vmax) scale(1.2); }
    }
    @keyframes move2{
      0%{ transform: translate(0,0) scale(1); }
      100%{ transform: translate(-8vmax, 7vmax) scale(1.15); }
    }
    @keyframes move3{
      0%{ transform: translate(0,0) scale(1.05); }
      100%{ transform: translate(12vmax, -6vmax) scale(1.18); }
    }
    @keyframes move4{
      0%{ transform: translate(0,0) scale(1); }
      100%{ transform: translate(-10vmax, -8vmax) scale(1.22); }
    }
    @keyframes spin1{ to{ transform: rotate(360deg); } }
    @keyframes spin2{ to{ transform: rotate(-360deg); } }
    @keyframes spin3{ to{ transform: rotate(360deg); } }
    @keyframes spin4{ to{ transform: rotate(-360deg); } }
    @keyframes grainShift{
      0%{ transform: translate(0,0) }
      20%{ transform: translate(-10px, 6px) }
      40%{ transform: translate(6px, -4px) }
      60%{ transform: translate(-4px, -8px) }
      80%{ transform: translate(12px, 10px) }
      100%{ transform: translate(0,0) }
    }

    .card {
      position: relative;
      z-index: 1;
      background-image: linear-gradient(163deg, var(--glow-1) 0%, var(--glow-2) 100%);
      border-radius: 22px;
      transition: all 0.3s;
    }
    .card2 {
      border-radius: 0;
      transition: all 0.2s;
      background-color: var(--card-bg);
    }
    .card2:hover { transform: scale(0.98); border-radius: 20px; }
    .card:hover { box-shadow: 0px 0px 30px 1px rgba(0, 255, 117, 0.3); }

    .form {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding: 2em;
      background-color: var(--card-bg);
      border-radius: 25px;
      transition: 0.4s ease-in-out;
      width: 360px;
    }
    #heading {
      text-align: center;
      margin: 1em 0;
      color: #fff;
      font-size: 1.3em;
    }
    .field {
      display: flex;
      align-items: center;
      gap: 0.5em;
      border-radius: 25px;
      padding: 0.6em;
      background-color: #1a1a1a;
      box-shadow: inset 2px 5px 10px rgb(5, 5, 5);
    }
    .input-icon {
      height: 1.3em;
      width: 1.3em;
      fill: white;
    }
    .input-field {
      background: none;
      border: none;
      outline: none;
      width: 100%;
      color: #d3d3d3;
    }
    .form .btn {
      display: flex;
      justify-content: center;
      margin-top: 1.5em;
      gap:10px;
    }
    .button1, .button2 {
      padding: 0.6em 1.5em;
      border-radius: 5px;
      border: none;
      background-color: #252525;
      color: white;
      cursor: pointer;
      transition:.3s;
    }
    .button1:hover, .button2:hover { background-color: #000; }

    .error-msg {
      color: #f87171;
      background: rgba(239,68,68,0.12);
      border:1px solid rgba(239,68,68,0.25);
      border-radius: 10px;
      padding: .55em 1em;
      font-size: 0.9em;
      margin-bottom: .5em;
      text-align:center;
    }
    .info-msg{
      color:#86efac;
      background: rgba(22,163,74,.12);
      border:1px solid rgba(22,163,74,.25);
      border-radius:10px; padding:.55em 1em; font-size:.9em; margin-bottom:.5em; text-align:center;
    }

    @media (max-width: 420px){
      .form{ width: 94vw; padding: 1.6em }
    }
  </style>
</head>
<body>

<div class="bg">
  <div class="blob b1"></div>
  <div class="blob b2"></div>
  <div class="blob b3"></div>
  <div class="blob b4"></div>
</div>
<div class="grain"></div>

<div class="card">
  <div class="card2">
    <form method="post" class="form" autocomplete="off" novalidate>
      <p id="heading"> Sign Up </p>

      <?php if (!empty($dbError)): ?>
        <div class="error-msg">
          <?= htmlspecialchars($dbError) ?><br>
          <?php if (!empty($dbErrorDetail)): ?>
            <small><?= htmlspecialchars($dbErrorDetail) ?></small>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="error-msg">
          <?php foreach ($error as $e): ?>
            <?= htmlspecialchars($e) ?><br>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (empty($dbError) && !empty($registerMsg)): ?>
        <div class="info-msg"><?= $registerMsg ?></div>
      <?php endif; ?>

      <div class="field">
        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M10 10a4 4 0 100-8 4 4 0 000 8zM2 18a8 8 0 1116 0H2z"/>
        </svg>
        <input id="username" type="text" name="username" class="input-field" placeholder="ชื่อผู้ใช้"
          value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
      </div>

      <div class="field">
        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M3 5h18a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2zm2 3v8h14V8H5zm2 2h4v2H7v-2zm0 3h6v2H7v-2z"/>
        </svg>
        <input id="fname" type="text" name="fname" class="input-field" placeholder="ชื่อ - นามสกุล"
          value="<?= isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : '' ?>" required>
      </div>

      <div class="field">
        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M2.94 6.34A2 2 0 014.6 5h10.8a2 2 0 011.66.94L10 11 2.94 6.34zM2 7.67V14a2 2 0 002 2h12a2 2 0 002-2V7.67l-7.4 4.63a2 2 0 01-2.2 0L2 7.67z"/>
        </svg>
        <input id="email" type="email" name="email" class="input-field" placeholder="อีเมล"
          value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
      </div>

      <div class="field">
        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M4 8V6a6 6 0 1112 0v2h1a1 1 0 011 1v9a1 1 0 01-1 1H3a1 1 0 01-1-1V9a1 1 0 011-1h1zm2 0h8V6a4 4 0 10-8 0v2z"/>
        </svg>
        <input id="password" type="password" name="password" class="input-field" placeholder="รหัสผ่าน" minlength="6" required>
      </div>

      <div class="field">
        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 116 0v3H9z"/>
        </svg>
        <input id="cpassword" type="password" name="cpassword" class="input-field" placeholder="ยืนยันรหัสผ่าน" minlength="6" required>
      </div>

      <div class="btn">
        <button type="submit" class="button1"> Register </button>
        <a href="login.php"><button type="button" class="button2"> Login </button></a>
      </div>
    </form>
  </div>
</div>

</body>
</html>
