<?php
require '../config.php'; // TODO-1: เชื่อมต่อข้อมูลด้วย PDO
require 'auth_admin.php'; // TODO-2: กำหนดสิทธิ์ (Admin Guard)

// เพิ่มหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->execute([$category_name]);
        header("Location: category.php");
        exit;
    }
}

// ลบหมวดหมู่ (ตรวจสอบการใช้งานก่อนลบ)
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $productCount = $stmt->fetchColumn();
    if ($productCount > 0) {
        $_SESSION['error'] = "ไม่สามารถลบได้เนื่องจากยังมีสินค้าอยู่";
    } else {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $_SESSION['success'] = "ลบหมวดหมู่นี้เรียบร้อยแล้ว";
    }
    header("Location: category.php");
    exit;
}

// แก้ไขหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = trim($_POST['new_name']);
    if ($category_name) {
        $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
        $stmt->execute([$category_name, $category_id]);
        header("Location: category.php");
        exit;
    }
}

// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดกำรหมวดหมู่</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap ตามไฟล์เดิม -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- ฟอนต์ Sarabun ให้เข้ากับธีม -->
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <!-- === Style Block (จากที่คุณส่ง) + เสริมสำหรับฟอร์ม/ตารางโทนดาร์ก === -->
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
  .register-card p { color: var(--muted); font-weight: 700; margin-bottom: 24px !important; }

  .btn {
    border-radius: 14px !important;
    font-weight: 800 !important;
    padding: 12px 14px !important;
    border: 1px solid rgba(255,255,255,.10) !important;
    box-shadow: 0 10px 22px rgba(0,0,0,.35) !important;
    transition: .2s ease-in-out !important;
  }
  .btn:hover { transform: translateY(-1px); filter: brightness(1.05) saturate(1.06); }
  .btn-primary { background: linear-gradient(163deg, #6a5cff, #3f32ff) !important; color: #0b140d !important; border: none !important; }
  .btn-warning { background: linear-gradient(163deg, #ffd54f, #ffb300) !important; color: #0b140d !important; border: none !important; }
  .btn-danger  { background: linear-gradient(163deg, #ff6b6b, #ff1744) !important; color: #0b140d !important; border: none !important; }
  .btn-secondary {
    background: #0f0f12 !important; color: #cfd3ff !important; border-color: #5b5be6 !important;
  }
  .btn-secondary:hover {
    color: #fff !important;
    background: linear-gradient(163deg, #6a5cff, #3f32ff) !important;
    border-color: transparent !important;
    box-shadow: 0 8px 20px rgba(63,50,255,.35) !important;
  }

  .container { max-width: 1140px; }

  /* ฟอร์มดาร์กในการ์ด */
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

  /* Alert แบบ glass */
  .alert-glass-danger {
    background: rgba(255, 77, 77, .08);
    border: 1px solid rgba(255, 77, 77, .35);
    color: #ffdede;
    border-radius: 14px;
  }
  .alert-glass-success {
    background: rgba(0, 255, 117, .08);
    border: 1px solid rgba(0, 255, 117, .35);
    color: #dbffe9;
    border-radius: 14px;
  }

  /* ตารางโทนดาร์ก */
  .table-darkish {
    color: var(--text);
    border-color: var(--border);
  }
  .table-darkish thead th {
    background: #121212;
    border-bottom: 1px solid var(--border);
  }
  .table-darkish tbody tr { background: rgba(17,17,17,.6); }
  .table-darkish td, .table-darkish th {
    border-color: rgba(255,255,255,.06) !important;
    vertical-align: middle;
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
          <h2 class="mb-1">จัดการหมวดหมู่สินค้า</h2>
          <p class="mb-0">เพิ่ม/แก้ไข/ลบหมวดหมู่ภายในระบบ</p>
        </div>
        <div>
          <a href="index.php" class="btn btn-secondary">← กลับหน้าผู้ดูแล</a>
        </div>
      </div>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-glass-danger mb-3"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-glass-success mb-3"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <form method="post" class="row g-3 mb-4">
        <div class="col-md-6">
          <input type="text" name="category_name" class="form-control" placeholder="ชื่อหมวดหมู่" required>
        </div>
        <div class="col-md-2">
          <button type="submit" name="add_category" class="btn btn-primary w-100">เพิ่มหมวดหมู่</button>
        </div>
      </form>

      <h5 class="mb-3">รายการหมดหมู่</h5>
      <div class="table-responsive">
        <table class="table table-bordered table-darkish">
          <thead>
            <tr>
              <th style="width:40%;">ชื่อหมวดหมู่</th>
              <th>แก้ไขชื่อ</th>
              <th style="width:120px;">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $cat): ?>
              <tr>
                <td><?= htmlspecialchars($cat['category_name']) ?></td>
                <td>
                  <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                    <input type="text" name="new_name" class="form-control" placeholder="ชื่อใหม่" required>
                    <button type="submit" name="update_category" class="btn btn-warning btn-sm">แก้ไข</button>
                  </form>
                </td>
                <td>
                  <a href="category.php?delete=<?= $cat['category_id'] ?>"
                     class="btn btn-danger btn-sm w-100"
                     onclick="return confirm('คุณต้องการลบหมวดหมู่นี้หรือไม่ ?')">ลบ</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$categories): ?>
              <tr><td colspan="3" class="text-center text-muted">ยังไม่มีหมวดหมู่</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
