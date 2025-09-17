<?php
require_once '../config.php'; // เชื่อมต่อฐานข้อมูล
require_once 'auth_admin.php';
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
    background: rgba(17,17,17,.80);
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
    margin-bottom: 10px;
  }

  .register-card p {
    color: var(--muted);
    font-weight: 700;
    margin-bottom: 24px !important;
  }



  .btn {
    border-radius: 14px;
    font-weight: 800;
    padding: 12px 14px;
    border: 1px solid rgba(255,255,255,.10);
    box-shadow: 0 10px 22px rgba(0,0,0,.35);
    transition: .2s ease-in-out;
  }

  .btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.05) saturate(1.06);
  }

  .btn-primary {
    background: linear-gradient(163deg, #6a5cff, #3f32ff);
    color: #0b140d;
  }

  .btn-success {
    background: linear-gradient(163deg, var(--glow-1), #00d65a);
    color: #0b140d;
  }

  .btn-warning {
    background: linear-gradient(163deg, #ffd54f, #ffb300);
    color: #0b140d;
  }

  .btn-dark {
    background: linear-gradient(163deg, #2d2d2d, #111113);
    color: #e5e7eb;
    border-color: rgba(255,255,255,.14);
  }

  .btn-secondary {
    background: #0f0f12;
    color: #cfd3ff;
    border-color: #5b5be6;
  }

  .btn-secondary:hover {
    color: #fff;
    background: linear-gradient(163deg, #6a5cff, #3f32ff);
    border-color: transparent;
    box-shadow: 0 8px 20px rgba(63,50,255,.35);
  }



  .row > [class*="col-"] {
    margin-bottom: 12px;
  }



  .container { max-width: 1140px; }



  @media (max-width: 576px) {
    .register-card { padding: 24px 18px; }
    .btn { padding: 12px; }
  }
</style>

</head>

<body>
<div class="container my-5">
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="register-card">
<h2>ระบบผู้ดูแลระบบ</h2>
<p class="mb-4">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?></p>
<div class="row">
<div class="col-md-4 mb-3">
<a href="products.php" class="btn btn-primary w-100">จัดการสินค้า</a>
</div>
<div class="col-md-4 mb-3">
<a href="orders.php" class="btn btn-success w-100">จัดการคำสั่งซื้อ </a>
</div>
<div class="col-md-4 mb-3">
<a href="users.php" class="btn btn-warning w-100">จัดการสมาชิก</a>
</div>
<div class="col-md-4 mb-3">
<a href="category.php" class="btn btn-dark w-100">จัดการหมวดหมู่</a>
</div>
</div>
<a href="../logout.php" class="btn btn-secondary mt-3">ออกจากระบบ</a>
    </div>
  </div>
</div>
</div>
</body>
</html>