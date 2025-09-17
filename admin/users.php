<?php
require_once '../config.php';
require_once 'auth_admin.php';
// ลบสมำชกิ
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    // ป้องกันลบตัวเอง
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
        $stmt->execute([$user_id]);
    }
    header("Location: users.php");
    exit;
}
// ดงึขอ้ มลู สมำชกิ
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการสมาชิก</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  :root {
    --bg: #000;
    --text: #aaaaaaff;
    --muted: #9ca3af;
    --card: #111;
    --card-2: #171717;
    --border: rgba(255,255,255,.10);
    --glow-1: #00ff75;
    --glow-2: #3700ff;
    --glow-3: #00e5ff;
    --glow-4: #ff00cc;
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
    padding: 28px 24px;
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
    margin-bottom: 14px;
  }



  .btn {
    border-radius: 12px;
    font-weight: 800;
    padding: 10px 14px;
    border: 1px solid rgba(255,255,255,.10);
    box-shadow: 0 10px 22px rgba(0,0,0,.35);
    transition: .2s ease-in-out;
  }
  .btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.05) saturate(1.06);
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

  .btn-warning {
    background: linear-gradient(163deg, #ffd54f, #ffb300);
    color: #140a00;
    border-color: rgba(255,255,255,.14);
  }
  .btn-danger {
    background: linear-gradient(163deg, #ff5a7a, #ff1744);
    color: #180407;
    border-color: rgba(255,255,255,.14);
  }

  .btn-sm {
    padding: 6px 10px;
    border-radius: 10px;
    font-weight: 800;
  }



  .table {
    color: var(--text);
    background: transparent;
    border-color: rgba(255,255,255,.06);
    margin-bottom: 0;
  }

  .table thead th {
    color: var(--text);
    background: #121216;
    border-bottom: 1px solid rgba(255,255,255,.12) !important;
    font-weight: 800;
    letter-spacing: .2px;
  }

  .table tbody tr {
    background: #0e0e11;
    border-color: rgba(255,255,255,.06);
    transition: background .15s ease;
  }

  .table tbody tr:nth-child(even) {
    background: #101015;
  }

  .table tbody tr:hover {
    background: #17171f;
  }

  .table td, .table th {
    border-color: rgba(255,255,255,.06) !important;
    vertical-align: middle;
  }



  .alert {
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(23,23,23,.85);
    color: var(--text);
    backdrop-filter: blur(8px);
  }
  .alert-warning {
    border-color: rgba(255,193,7,.35);
    box-shadow: 0 0 24px rgba(255,193,7,.12);
    color: #ffe9a6;
  }



  .container { max-width: 1140px; }
  .mb-3 { margin-bottom: 1rem !important; }


  @media (max-width: 576px) {
    .register-card { padding: 20px 16px; }
    .btn { padding: 10px 12px; }
    .table thead { font-size: .95rem; }
    .table td, .table th { padding: .55rem; }
  }
</style>

</head>
<body>
<div class="container my-5">
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="register-card">
<h2>จัดการสมาชิก</h2>
    <a href="index.php" class="btn btn-secondary mb-3">← กลับหน้าผู้ดูแล</a>
    <?php if (count($users) === 0): ?>
    <div class="alert alert-warning">ยังไม่มีสมาชิกในระบบ</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>อีเมล</th>
                    <th>วันที่สมัคร</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['created_at'] ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>

                            <!-- <a href="users.php?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger"onclick="return confirm('คุณต้องการลบสมาชิกหรือไม่ ?')">ลบ</a> -->
                            
                            <form action="deluser_sweet.php" method="POST" style="display:inline;">
                              <input type="hidden" name="u_id" value="<?php echo $user['user_id']; ?>">
                              <button type="button" class="delete-button btn btn-danger btn-sm " data-user-id="<?php echo $user['user_id']; ?>">ลบ</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </div>
  </div>
</div>
</div>
<script>
// ฟังกช์ นั ส ำหรับแสดงกลอ่ งยนื ยัน SweetAlert2
  function showDeleteConfirmation(userId) {
    Swal.fire({
      title: 'คุณแน่ใจหรือไม่?',
      text: 'คุณไม่สามารถเรียกคือข้อมูลกลับได้ !',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'ลบ',
      cancelButtonText: 'ยกเลิก',
    }).then((result) => {
      if (result.isConfirmed) {
      // หำกผใู้ชย้นื ยัน ใหส้ ง่ คำ่ ฟอรม์ ไปยัง delete.php เพื่อลบข ้อมูล
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = 'deluser_sweet.php';
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'u_id';
          input.value = userId;
          form.appendChild(input);
          document.body.appendChild(form);
          form.submit();
        }
    });
  }
  // แนบตัวตรวจจับเหตุกำรณ์คลิกกับองค์ปุ ่่มลบทั ่ ้งหมดที่มีคลำส delete-button
  const deleteButtons = document.querySelectorAll('.delete-button');
  deleteButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const userId = button.getAttribute('data-user-id');
      showDeleteConfirmation(userId);
    });
  });
</script>

</body>
</html>