<?php
session_start(); // เริ่ม session

require_once 'config.php'; // เชื่อมต่อฐานข้อมูล
$isLoggedIn = isset($_SESSION['user_id']);// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่

$stmt = $conn->query("SELECT p.*,c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <style>
  :root{
    --bg:#000;
    --text:#e5e7eb;
    --muted:#9ca3af;
    --card:#111;
    --card-2:#171717;
    --border:rgba(255,255,255,.08);
    --glow-1:#00ff75;
    --glow-2:#3700ff;
    --glow-3:#00e5ff;
    --glow-4:#ff00cc;
    --accent:#00ffa8;
  }
  html,body{height:100%;}
  body{
    background: var(--bg);
    color: var(--text);
    font-family:'Poppins',system-ui,Segoe UI,Roboto,Arial,sans-serif;
    overflow-x:hidden;
  }

  body::before{
    content:"";
    position:fixed; inset:-25vmax -25vmax; z-index:-2;
    filter: blur(40px) saturate(120%); opacity:.8;
    background:
      radial-gradient(closest-side at 20% 25%, var(--glow-1), transparent 60%),
      radial-gradient(closest-side at 80% 20%, var(--glow-2), transparent 62%),
      radial-gradient(closest-side at 15% 80%, var(--glow-3), transparent 58%),
      radial-gradient(closest-side at 85% 75%, var(--glow-4), transparent 60%);
    animation: moveGlow 30s ease-in-out infinite alternate;
    pointer-events:none;
  }
  body::after{
    content:"";
    position:fixed; inset:0; z-index:-1; pointer-events:none;
    background:
      radial-gradient(120vmax 120vmax at 50% 50%, rgba(255,255,255,.02), transparent 60%),
      repeating-conic-gradient(from 0deg, rgba(255,255,255,.01) 0deg 2deg, transparent 2deg 4deg);
    mix-blend-mode: overlay; opacity:.35; animation: grain 8s steps(6,end) infinite;
  }
  @keyframes moveGlow{
    0%   {transform:translate(0,0) scale(1);}
    100% {transform:translate(4vmax,-3vmax) scale(1.05);}
  }
  @keyframes grain{
    0%{transform:translate(0,0)}20%{transform:translate(-10px,6px)}
    40%{transform:translate(6px,-4px)}60%{transform:translate(-4px,-8px)}
    80%{transform:translate(12px,10px)}100%{transform:translate(0,0)}
  }

  .main-card{
    max-width:1200px; margin:24px auto; padding:0;
    background: rgba(17,17,17,.75);
    border:1px solid var(--border);
    border-radius:18px;
    box-shadow: 0 8px 30px rgba(0,0,0,.35);
    backdrop-filter: blur(8px);
  }
  .main-card-inner{
    padding:20px;
  }

  .welcome-bar{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    padding:6px 2px 16px;
    border-bottom:1px solid rgba(255,255,255,.06);
  }
  .welcome-bar h1{
    margin:0; font-weight:800; letter-spacing:.2px;
    text-shadow:0 6px 30px rgba(0,0,0,.45);
    font-size: clamp(20px, 2.4vw, 28px);
  }
  .welcome-bar .me-3{ color:var(--muted); font-weight:600; }

  .btn{
    border-radius:12px !important; font-weight:700 !important;
    padding:.5rem .9rem !important; border:1px solid transparent !important;
    transition:.2s ease-in-out !important; box-shadow: 0 8px 18px rgba(0,0,0,.25) !important;
  }
  .btn:hover{ transform: translateY(-1px); filter: brightness(1.05) saturate(1.05); }

  .btn-primary{
    background: linear-gradient(163deg, #6a5cff, #3f32ff) !important; border-color: rgba(255,255,255,.1) !important;
    color:#fff !important;
  }
  .btn-success{
    background: linear-gradient(163deg, var(--glow-1), #00d65a) !important; border-color: rgba(255,255,255,.1) !important;
    color:#08110b !important;
  }
  .btn-info{
    background: linear-gradient(163deg, #00e5ff, #00bcd4) !important; border-color: rgba(255,255,255,.1) !important;
    color:#071012 !important;
  }
  .btn-warning{
    background: linear-gradient(163deg, #ffd54f, #ffb300) !important; border-color: rgba(255,255,255,.1) !important;
    color:#140a00 !important;
  }
  .btn-danger{
    background: linear-gradient(163deg, #ff5a7a, #ff1744) !important; border-color: rgba(255,255,255,.1) !important;
    color:#180407 !important;
  }
  .btn-outline-primary{
    background: #0f0f12 !important; color:#9aa5ff !important;
    border-color:#3b3bea !important;
  }
  .btn-outline-primary:hover{
    background: linear-gradient(163deg, #6a5cff, #3f32ff) !important; color:#fff !important;
  }

  .row{ row-gap:18px; }
  .card{
    background: var(--card-2) !important; color: var(--text) !important;
    border: 1px solid var(--border) !important;
    border-radius:16px !important;
    box-shadow: 0 8px 22px rgba(0,0,0,.35) !important;
    overflow:hidden !important; transform: translateZ(0);
    transition: .2s ease-in-out !important;
  }
  .card:hover{ transform: translateY(-3px) scale(1.01); box-shadow: 0 0 28px rgba(0,255,117,.08) !important; }
  .card .card-body{ padding:16px 16px 14px !important; }
  .card-title{ font-weight:800; margin-bottom:6px !important; }
  .card-subtitle{ color:var(--muted) !important; font-weight:600; }
  .card-text{ color:var(--text); opacity:.95; line-height:1.55; }
  .text-muted{ color:var(--muted) !important; }

  .card-body p strong{ font-weight:800; color:var(--text); }
  .card-body p{ margin-bottom:.6rem; }

  .card form .btn{ margin-right:8px; }
  .float-end{ float:right !important; }

  @media (max-width: 768px){
    .welcome-bar{ flex-wrap:wrap; }
    .welcome-bar > div{ display:flex; gap:8px; flex-wrap:wrap; }
    .main-card-inner{ padding:16px; }
  }
</style>

</head>
<body>
    <div class="main-card">
      <div class="main-card-inner">
        <div class="welcome-bar">
            <h1>รายการสินค้า</h1>
            <div>
                <?php if ($isLoggedIn) : ?>
                    <span class="me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
                    <a href="profile.php" class="btn btn-info">ข้อมูลส่วนตัว</a>
                    <a href="cart.php" class="btn btn-warning">ดูตะกร้าสินค้า</a>
                    <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-success">เข้าสู่ระบบ</a>
                    <a href="register.php" class="btn btn-primary">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($product['category_name']) ?></h6>
                            <p class="card-text"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                            <p><strong>ราคา:</strong> <?= number_format($product['price'], 2) ?> บาท</p>
                            <?php if ($isLoggedIn): ?>
                                <form action="cart.php" method="post" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-success">เพิ่มในตะกร้า</button>
                                </form>
                            <?php else: ?>
                                <small class="text-muted">เข้าสู่ระบบเพื่อสั่งสินค้า</small>
                            <?php endif; ?>
                            <a href="product_detail.php?id=<?= $product['product_id'] ?>" class="btn btn-sm btn-outline-primary float-end">ดูรายละเอียด</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>