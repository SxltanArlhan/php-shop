<?php
session_start();
require_once 'config.php';


$product_id = $_GET['id'];

$stmt = $conn->prepare("SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = ?");
    $stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!isset($_GET['id'])){
    header('Location: test_index.php');
    exit();
}

$isLoggedIn = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดสินค้า</title>
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



  .btn.btn-secondary {
    border-radius: 12px;
    color: #cfd3ff;
    border-color: #5b5be6;
    background: #0f0f12;
    font-weight: 700;
    padding: 8px 14px;
    margin-bottom: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,.35);
    transition: .2s ease-in-out;
  }

  .btn.btn-secondary:hover {
    color: #fff;
    background: linear-gradient(163deg, #6a5cff, #3f32ff);
    border-color: transparent;
    box-shadow: 0 8px 22px rgba(63,50,255,.38);
    transform: translateY(-1px);
  }



  .detail-card {
    width: 100%;
    max-width: 520px;
    margin: 70px auto 32px;
    padding: 30px 26px;
    background: rgba(17,17,17,.80);
    border-radius: 20px;
    border: 1px solid var(--border);
    box-shadow:
      0 10px 28px rgba(0,0,0,.45),
      inset 0 0 0 1px rgba(255,255,255,.03);
    backdrop-filter: blur(10px);
  }

  .detail-card h3 {
    text-align: center;
    margin-bottom: 10px;
    font-weight: 800;
    color: var(--text);
    letter-spacing: .2px;
    text-shadow: 0 6px 30px rgba(0,0,0,.45);
  }

  .detail-card h6 {
    text-align: center;
    color: var(--muted);
    margin-bottom: 18px;
    font-weight: 700;
  }

  .detail-card .card-text {
    font-size: 1.02em;
    color: var(--text);
  }

  .detail-card p {
    margin-bottom: 10px;
  }

  .detail-card p strong {
    font-weight: 800;
  }



  label {
    font-weight: 700;
    color: var(--text);
    margin-right: 8px;
  }

  input[type="number"] {
    background: #0f0f10;
    color: var(--text);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 10px 12px;
    width: 120px;
    box-shadow: none;
    outline: none;
  }

  input[type="number"]:focus {
    background: #141418;
    border-color: transparent;
    box-shadow: 0 0 0 6px rgba(0,255,117,.08);
    outline: 2px solid rgba(0,255,117,.35);
  }



  .btn-success {
    width: 100%;
    border-radius: 12px;
    background: linear-gradient(163deg, var(--glow-1), #00d65a);
    border: 1px solid rgba(255,255,255,.10);
    color: #09120a;
    font-weight: 800;
    padding: 12px;
    box-shadow: 0 12px 24px rgba(0,0,0,.35);
    transition: .2s ease-in-out;
  }

  .btn-success:hover {
    filter: brightness(1.05) saturate(1.08);
    transform: translateY(-1px);
  }



  .alert {
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(23,23,23,.85);
    color: var(--text);
    backdrop-filter: blur(8px);
  }

  .alert-info {
    border-color: rgba(0,229,255,.28);
    box-shadow: 0 0 24px rgba(0,229,255,.10);
  }



  @media (max-width: 576px) {
    .detail-card {
      margin-top: 48px;
      padding: 24px 18px;
    }

    input[type="number"] {
      width: 100%;
      margin-top: 8px;
    }
  }
</style>

</head>
<body>
    <a href="index.php" class="btn btn-secondary">← กลับหน้ารายการสินค้า</a>
    <div class="detail-card">
        <h3><?= htmlspecialchars($product['product_name']) ?></h3>
        <h6>หมวดหมู่: <?= htmlspecialchars($product['category_name']) ?></h6>
        <div class="card-text">
            <p><strong>ราคา:</strong> <?= htmlspecialchars($product['price']) ?> บาท</p>
            <p><strong>คงเหลือ:</strong> <?= htmlspecialchars($product['stock']) ?>  ชิ้น </p>
        </div>
        <?php if ($isLoggedIn): ?>
            <form action="cart.php" method="post" class="mt-3">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <label for="quantity">จำนวน:</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>" required>
                <button type="submit" class="btn btn-success mt-2">เพิ่มในตะกร้า</button>
            </form>
        <?php else: ?>
            <div class="alert alert-info mt-3 text-center">กรุณาเข้าสู่ระบบเพื่อสั่งซื้อสินค้า</div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>