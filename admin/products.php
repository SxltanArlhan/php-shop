<?php
require '../config.php'; // TODO-1: เชื่อมต่อข้อมูลด้วย PDO
require 'auth_admin.php'; // TODO-2: กำหนดสิทธิ์ (Admin Guard)

// เพิ่มสินค้าใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name        = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']); // แปลงเป็น float
    $stock       = intval($_POST['stock']);   // แปลงเป็น int
    $category_id = intval($_POST['category_id']);

    if ($name && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, stock, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category_id]);
        header("Location: products.php");
        exit;
    }
}

// ลบสินค้า (แก้บั๊ก: ชื่อตาราง/พารามิเตอร์ผิด)
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    header("Location: products.php");
    exit;
}

// ดึงรายการสินค้า
$stmt = $conn->query("SELECT p.*, c.category_name
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.category_id
                      ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงหมวดหมู่
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการสินค้า</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- ฟอนต์ Sarabun -->
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <!-- === Style Block (ธีมนีออน-ดาร์กจากที่ให้มา) + เสริมฟอร์ม/ตาราง === -->
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
    margin: 0; min-height: 100vh; overflow-x: hidden;
    font-family: 'Sarabun', system-ui, Segoe UI, Roboto, Arial, sans-serif;
    color: var(--text); background: var(--bg); padding: 24px 12px;
  }
  body::before{
    content:""; position:fixed; inset:-25vmax -25vmax; z-index:-2;
    filter: blur(40px) saturate(120%); opacity:.8;
    background:
      radial-gradient(closest-side at 20% 25%, var(--glow-1), transparent 60%),
      radial-gradient(closest-side at 80% 20%, var(--glow-2), transparent 62%),
      radial-gradient(closest-side at 15% 80%, var(--glow-3), transparent 58%),
      radial-gradient(closest-side at 85% 75%, var(--glow-4), transparent 60%);
    animation: moveGlow 28s ease-in-out infinite alternate; pointer-events:none;
  }
  body::after{
    content:""; position:fixed; inset:0; z-index:-1; pointer-events:none;
    background:
      radial-gradient(120vmax 120vmax at 50% 50%, rgba(255,255,255,.02), transparent 60%),
      repeating-conic-gradient(from 0deg, rgba(255,255,255,.01) 0deg 2deg, transparent 2deg 4deg);
    mix-blend-mode: overlay; opacity:.35; animation: grain 8s steps(6,end) infinite;
  }
  @keyframes moveGlow{0%{transform:translate(0,0) scale(1)}100%{transform:translate(4vmax,-3vmax) scale(1.05)}}
  @keyframes grain{0%{transform:translate(0,0)}20%{transform:translate(-10px,6px)}40%{transform:translate(6px,-4px)}60%{transform:translate(-4px,-8px)}80%{transform:translate(12px,10px)}100%{transform:translate(0,0)}}

  .register-card{
    background: rgba(17,17,17,.80); border-radius: 20px; padding: 32px 26px;
    border:1px solid var(--border);
    box-shadow: 0 10px 28px rgba(0,0,0,.45), inset 0 0 0 1px rgba(255,255,255,.03);
    backdrop-filter: blur(10px); max-width: 1140px; margin: 0 auto;
  }
  .register-card h2{ color:var(--text); font-weight:800; letter-spacing:.3px; text-shadow:0 6px 30px rgba(0,0,0,.45); margin-bottom:10px; }
  .register-card p{ color:var(--muted); font-weight:700; margin-bottom:24px !important; }

  .btn{ border-radius:14px !important; font-weight:800 !important; padding:12px 14px !important;
        border:1px solid rgba(255,255,255,.10) !important; box-shadow:0 10px 22px rgba(0,0,0,.35) !important;
        transition:.2s ease-in-out !important; }
  .btn:hover{ transform: translateY(-1px); filter: brightness(1.05) saturate(1.06); }
  .btn-primary{ background: linear-gradient(163deg, #6a5cff, #3f32ff) !important; color:#0b140d !important; border:none !important; }
  .btn-secondary{ background:#0f0f12 !important; color:#cfd3ff !important; border-color:#5b5be6 !important; }
  .btn-secondary:hover{ color:#fff !important; background:linear-gradient(163deg,#6a5cff,#3f32ff) !important; border-color:transparent !important; box-shadow:0 8px 20px rgba(63,50,255,.35) !important; }
  .btn-warning{ background: linear-gradient(163deg, #ffd54f, #ffb300) !important; color:#0b140d !important; border:none !important; }
  .btn-danger{  background: linear-gradient(163deg, #ff6b6b, #ff1744) !important; color:#0b140d !important; border:none !important; }

  .container{ max-width:1140px; }

  /* ฟอร์มดาร์ก */
  .register-card .form-label{ color:var(--text); font-weight:700; }
  .register-card .form-control, .register-card .form-select, .register-card textarea{
    background:#0f0f12; color:var(--text); border:1px solid var(--border); border-radius:12px;
  }
  .register-card .form-control:focus, .register-card .form-select:focus, .register-card textarea:focus{
    outline:none; box-shadow:0 0 0 4px rgba(106,92,255,.18); border-color:#6a5cff;
  }
  .register-card .text-muted{ color:var(--muted) !important; }

  /* ตารางโทนดาร์ก */
  .table-darkish{ color:var(--text); border-color:var(--border); }
  .table-darkish thead th{ background:#121212; border-bottom:1px solid var(--border); }
  .table-darkish tbody tr{ background: rgba(17,17,17,.6); }
  .table-darkish td, .table-darkish th{ border-color: rgba(255,255,255,.06) !important; vertical-align: middle; }

  @media (max-width:576px){
    .register-card{ padding:24px 18px; }
    .btn{ padding:12px; }
  }
  </style>
</head>
<body>
  <div class="container">
    <div class="register-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h2 class="mb-1">จัดการสินค้า</h2>
          <p class="mb-0">เพิ่มสินค้าใหม่และดูรายการสินค้าในระบบ</p>
        </div>
        <div>
          <a href="index.php" class="btn btn-secondary">← กลับหน้าผู้ดูแล</a>
        </div>
      </div>

      <!-- ฟอร์มเพิ่มสินค้าใหม่ -->
      <form method="post" class="row g-3 mb-4">
        <h5 class="mb-0">เพิ่มสินค้าใหม่</h5>
        <div class="col-md-4">
          <input type="text" name="product_name" class="form-control" placeholder="ชื่อสินค้า" required>
        </div>
        <div class="col-md-2">
          <input type="number" step="0.01" name="price" class="form-control" placeholder="ราคา" required>
        </div>
        <div class="col-md-2">
          <input type="number" name="stock" class="form-control" placeholder="จำนวน" required>
        </div>
        <div class="col-md-2">
          <select name="category_id" class="form-select" required>
            <option value="">เลือกหมวดหมู่</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <textarea name="description" class="form-control" placeholder="รายละเอียดสินค้า" rows="2"></textarea>
        </div>
        <div class="col-12">
          <button type="submit" name="add_product" class="btn btn-primary">เพิ่มสินค้า</button>
        </div>
      </form>

      <!-- ตารางรายการสินค้า -->
      <h5 class="mb-3">รายละเอียดสินค้า</h5>
      <div class="table-responsive">
        <table class="table table-bordered table-darkish">
          <thead>
            <tr>
              <th>ชื่อสินค้า</th>
              <th>หมวดหมู่</th>
              <th>ราคา</th>
              <th>คงเหลือ</th>
              <th style="width:160px;">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><?= number_format((float)$p['price'], 2) ?> บาท</td>
                <td><?= (int)$p['stock'] ?></td>
                <td class="d-flex gap-2">
                  <a href="products.php?delete=<?= (int)$p['product_id'] ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('ยืนยันการลบสินค้านี้?')">ลบ</a>
                  <a href="edit_product.php?id=<?= (int)$p['product_id'] ?>"
                     class="btn btn-warning btn-sm">แก้ไข</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$products): ?>
              <tr><td colspan="5" class="text-center text-muted">ยังไม่มีสินค้า</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
