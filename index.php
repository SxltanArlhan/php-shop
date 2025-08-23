<?php
    session_start();

$username = $_SESSION['user']['username'] ?? ($_SESSION['username'] ?? 'ผู้ใช้');
$role     = $_SESSION['user']['role']     ?? ($_SESSION['role'] ?? 'member');

$products = [
  [
    'name' => 'Product1',
    'desc' => 'product test 1',
    'price'=> 1890.00,
    'img'  => 'https://images.unsplash.com/photo-1511367461989-f85a21fda167?q=80&w=1600&auto=format&fit=crop'
  ],
  [
    'name' => 'Product2',
    'desc' => 'product test 2',
    'price'=> 990.00,
    'img'  => 'https://images.unsplash.com/photo-1511367461989-f85a21fda167?q=80&w=1600&auto=format&fit=crop'
  ],
  [
    'name' => 'Product3',
    'desc' => 'product test 3',
    'price'=> 1490.00,
    'img'  => 'https://images.unsplash.com/photo-1511367461989-f85a21fda167?q=80&w=1600&auto=format&fit=crop'
  ],
  [
    'name' => 'Product4',
    'desc' => 'product test 4',
    'price'=> 390.00,
    'img'  => 'https://images.unsplash.com/photo-1511367461989-f85a21fda167?q=80&w=1600&auto=format&fit=crop'
  ],
  [
    'name' => 'Product5',
    'desc' => 'product test 5',
    'price'=> 690.00,
    'img'  => 'https://images.unsplash.com/photo-1511367461989-f85a21fda167?q=80&w=1600&auto=format&fit=crop'
  ],
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home | NeonShop</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;600;500;400&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#000;
      --text:#e5e7eb;
      --muted:#9ca3af;
      --card:#111;
      --card-2:#171717;
      --glow-1:#00ff75;
      --glow-2:#3700ff;
      --glow-3:#00e5ff;
      --glow-4:#ff00cc;
      --accent:#00ffa8;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; min-height:100vh; overflow-x:hidden;
      font-family:'Poppins',system-ui,Segoe UI,Roboto,Arial,sans-serif; color:var(--text);
      background: var(--bg);
      padding:24px;
    }

    .bg{ position: fixed; inset: -20vmax -20vmax; filter: blur(40px) saturate(120%); opacity:.75; z-index:0; }
    .blob{ position:absolute; border-radius:50%; mix-blend-mode: screen; width:60vmax; height:60vmax; opacity:.65; animation-timing-function:ease-in-out; animation-iteration-count:infinite; }
    .b1{ left:-10vmax; top:-10vmax; background:radial-gradient(closest-side at 40% 40%, var(--glow-1), transparent 60%); animation: m1 18s alternate infinite, r1 28s linear infinite; }
    .b2{ right:-8vmax; top:-12vmax; background:radial-gradient(closest-side at 60% 30%, var(--glow-2), transparent 65%); animation: m2 22s alternate-reverse infinite, r2 32s linear infinite; }
    .b3{ left:-12vmax; bottom:-10vmax; background:radial-gradient(closest-side at 50% 50%, var(--glow-3), transparent 60%); animation: m3 24s alternate infinite, r3 36s linear infinite; }
    .b4{ right:-14vmax; bottom:-8vmax; background:radial-gradient(closest-side at 45% 55%, var(--glow-4), transparent 62%); animation: m4 26s alternate-reverse infinite, r4 40s linear infinite; }
    .grain{
      position: fixed; inset:0; pointer-events:none; z-index:0;
      background: radial-gradient(120vmax 120vmax at 50% 50%, rgba(255,255,255,.02), transparent 60%),
                  repeating-conic-gradient(from 0deg, rgba(255,255,255,.01) 0deg 2deg, transparent 2deg 4deg);
      mix-blend-mode: overlay; opacity:.35; animation: g 8s steps(6,end) infinite;
    }
    @keyframes m1{0%{transform:translate(0,0) scale(1.05)}100%{transform:translate(10vmax,6vmax) scale(1.2)}}
    @keyframes m2{0%{transform:translate(0,0) scale(1)}100%{transform:translate(-8vmax,7vmax) scale(1.15)}}
    @keyframes m3{0%{transform:translate(0,0) scale(1.05)}100%{transform:translate(12vmax,-6vmax) scale(1.18)}}
    @keyframes m4{0%{transform:translate(0,0) scale(1)}100%{transform:translate(-10vmax,-8vmax) scale(1.22)}}
    @keyframes r1{to{transform:rotate(360deg)}} @keyframes r2{to{transform:rotate(-360deg)}}
    @keyframes r3{to{transform:rotate(360deg)}} @keyframes r4{to{transform:rotate(-360deg)}}
    @keyframes g{0%{transform:translate(0,0)}20%{transform:translate(-10px,6px)}40%{transform:translate(6px,-4px)}60%{transform:translate(-4px,-8px)}80%{transform:translate(12px,10px)}100%{transform:translate(0,0)}}

    /* ===== Layout / Header ===== */
    .container{ position:relative; z-index:1; max-width: 1200px; margin:0 auto; }
    .topbar{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      background: rgba(17,17,17,.8); border:1px solid rgba(255,255,255,.06);
      border-radius:16px; padding:14px 18px; backdrop-filter: blur(8px);
      box-shadow: 0 6px 30px rgba(0,0,0,.25);
    }
    .brand{ font-weight:800; letter-spacing:.5px; }
    .userbox{ color:var(--muted); font-weight:500; }
    .logout{
      border:0; border-radius:10px; padding:10px 14px; font-weight:700; cursor:pointer;
      background: linear-gradient(163deg, var(--glow-1), var(--glow-2)); color:#0a0a0a;
      box-shadow: 0 8px 24px rgba(0,0,0,.35);
    }
    .logout:hover{ filter: brightness(1.05) saturate(1.05); }

    .welcome{
      margin: 22px 0 10px; padding: 4px 2px; font-weight:700; font-size: clamp(20px, 2.4vw, 28px);
      text-shadow: 0 6px 30px rgba(0,0,0,.45);
    }
    .subtitle{ margin:0 0 16px; color:var(--muted); font-weight:500 }

    /* ===== Product Grid + Glow Card ===== */
    .grid{
      display:grid; gap:18px;
      grid-template-columns: repeat(5, 1fr);
    }
    @media (max-width:1100px){ .grid{ grid-template-columns: repeat(4, 1fr);} }
    @media (max-width:920px){ .grid{ grid-template-columns: repeat(3, 1fr);} }
    @media (max-width:680px){ .grid{ grid-template-columns: repeat(2, 1fr);} }
    @media (max-width:460px){ .grid{ grid-template-columns: 1fr;} }

    .glow-card{
      background-image: linear-gradient(163deg, var(--glow-1) 0%, var(--glow-2) 100%);
      border-radius: 18px; transition: .25s ease; box-shadow: 0 8px 22px rgba(0,0,0,.35);
    }
    .glow-card:hover{ box-shadow: 0 0 28px 4px rgba(0,255,117,.3) }
    .card-inner{
      background: var(--card-2); border-radius: 14px; overflow:hidden;
      transform: translate3d(0,0,0); transition: .2s ease;
    }
    .glow-card:hover .card-inner{ transform: scale(.985); border-radius: 16px; }

    .thumb{
      width:100%; height:180px; object-fit:cover; display:block; filter: saturate(1.15) contrast(1.05);
    }
    .content{ padding:14px 14px 16px; }
    .title{ font-size:15.5px; font-weight:700; margin:0 0 4px; }
    .desc{ font-size:13px; color:var(--muted); min-height: 38px; margin:0 0 10px; }
    .price{ font-weight:800; font-size: 18px; color: var(--text); }
    .actions{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:10px }

    .button {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: rgb(20, 20, 20);
      border: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.164);
      cursor: pointer;
      transition-duration: .3s;
      overflow: hidden;
      position: relative;
    }
    .svgIcon {
      width: 14px;
      transition-duration: .3s;
    }
    .svgIcon path, .svgIcon circle {
      fill: white;
    }
    .button:hover {
      width: 160px;     
      border-radius: 50px;
      transition-duration: .3s;
      background-color: rgba(0, 222, 48, 1);
      align-items: center;
    }
    .button:hover .svgIcon {
      width: 50px;
      transition-duration: .3s;
      transform: translateY(60%);
    }
    .button::before {
      position: absolute;
      top: -20px;
      content: "Buy";
      color: white;
      transition-duration: .3s;
      font-size: 2px;
      opacity: 0;
    }
    .button:hover::before {
      font-size: 13px;
      opacity: 1;
      transform: translateY(30px);
      transition-duration: .3s;
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

<div class="container">
  <div class="topbar">
    <div class="brand">NeonShop</div>
    <div class="userbox">ผู้ใช้: <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</div>
    <a href="loguot.php"><button class="logout">ออกจากระบบ</button></a>
  </div>

  <div class="welcome">ยินดีต้อนรับสู่หน้าหลัก</div>
  <p class="subtitle">สินค้าแนะ — เลือกช้อปได้เลย!</p>

  <div class="grid">
    <?php foreach ($products as $p): ?>
      <div class="glow-card">
        <div class="card-inner">
          <img class="thumb" src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          <div class="content">
            <div class="title"><?= htmlspecialchars($p['name']) ?></div>
            <div class="desc"><?= htmlspecialchars($p['desc']) ?></div>
            <div class="actions">
              <div class="price">฿<?= number_format($p['price'], 2) ?></div>

              <!-- ปุ่ม Uiverse เวอร์ชันไอคอนตะกร้า -->
              <button class="button" type="button" aria-label="หยิบใส่ตะกร้า">
                <svg class="svgIcon" viewBox="0 0 24 24" aria-hidden="true">
                  <!-- ตัวรถเข็น -->
                  <path d="M7 6h14l-2.2 7.3a2 2 0 0 1-1.9 1.4H9.2L8.4 18H19v2H8a3 3 0 0 1-2.8-4l1.1-2.7L4 4H1V2h4a1 1 0 0 1 .95.68L7 6z"/>
                  <!-- ล้อ -->
                  <circle cx="9" cy="20" r="2"></circle>
                  <circle cx="17" cy="20" r="2"></circle>
                </svg>
              </button>

            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>
