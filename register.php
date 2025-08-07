<?php
require_once 'config.php';

$dbError = null;
$registerMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username']);
        $fname = trim($_POST['fname']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];

        if ($password !== $cpassword) {
            $registerMsg = '<div class="alert alert-danger mt-3">❌ Passwords do not match!</div>';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users(username,full_name,email,password,role) VALUES (?,?,?,?,'admin')";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$username, $fname, $email, $hashedPassword])) {
                $registerMsg = '<div class="alert alert-success mt-3">✅ Register success!</div>';
            } else {
                $registerMsg = '<div class="alert alert-danger mt-3">❌ Register failed. Try again.</div>';
            }
        }
    } catch (PDOException $e) {
        $dbError = "เชื่อมต่อไม่สำเร็จ: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | NeonGlass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@900;700;500;400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a2980, #26d0ce 80%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            animation: bgmove 8s linear infinite alternate;
        }
        @keyframes bgmove {
            0% {background-position: 0 0;}
            100% {background-position: 100% 100%;}
        }
        .glass-card {
            background: rgba(30,40,70,0.7);
            box-shadow: 0 8px 40px 0 rgba(30,40,90,0.3);
            border-radius: 2rem;
            padding: 2.5rem 2.5rem 1.5rem 2.5rem;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            position: relative;
            overflow: hidden;
            max-width: 430px;
            width: 100%;
            z-index: 2;
            margin-top: 32px;
        }
        .glass-card:before {
            content: "";
            position: absolute;
            top: -40%; left: -40%;
            width: 180%;
            height: 180%;
            background: radial-gradient(circle at 60% 40%, #40c9ff80 0%, #e81cff60 90%);
            opacity: 0.13;
            z-index: 0;
            pointer-events: none;
            filter: blur(40px);
        }
        .title-gradient {
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            font-weight: 900;
            font-size: 2.3rem;
            background: linear-gradient(90deg, #40c9ff, #e81cff 90%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.03em;
            display: flex;
            align-items: center;
            gap: .5em;
        }
        .form-label {
            color: #fff;
            font-weight: 500;
            letter-spacing: 0.01em;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
        .form-control {
            background: rgba(255,255,255,0.15);
            border: 1.5px solid rgba(255,255,255,0.18);
            color: #fff;
            border-radius: 1rem;
            font-size: 1.08rem;
            margin-bottom: .95rem;
            transition: border .2s;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.19);
            border-color: #40c9ff;
            color: #fff;
            box-shadow: 0 0 0 2px #40c9ff44;
        }
        ::placeholder {
            color: #c8e7ffbb;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
        .btn-primary {
            background: linear-gradient(90deg, #40c9ff 10%, #e81cff 90%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.07em;
            border-radius: 1em;
            padding: .75em 2.2em;
            font-size: 1.18rem;
            box-shadow: 0 4px 18px 0 #1a29804d;
            transition: transform .16s cubic-bezier(.4,2,.3,1), box-shadow .22s;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
        .btn-primary:hover, .btn-primary:focus {
            transform: translateY(-3px) scale(1.025) rotate(-.7deg);
            box-shadow: 0 6px 30px 0 #40c9ff50;
        }
        .btn-link {
            color: #e81cff !important;
            font-weight: 500;
            font-size: 1.05rem;
            margin-left: 10px;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
        .btn-link:hover {
            color: #40c9ff !important;
            text-decoration: underline;
        }
        .neon-border {
            box-shadow: 0 0 0 4px #40c9ff44, 0 0 14px 2px #e81cff66;
            animation: neon 1.8s infinite alternate;
        }
        @keyframes neon {
            0% {box-shadow: 0 0 0 4px #40c9ff44, 0 0 10px 2px #e81cff55;}
            100% {box-shadow: 0 0 0 7px #40c9ff77, 0 0 28px 8px #e81cffcc;}
        }
        .icon-circle {
            background: linear-gradient(135deg, #40c9ff 40%, #e81cff 100%);
            border-radius: 100%;
            width: 48px; height: 48px;
            display: flex; align-items: center; justify-content: center;
            margin-right: .7em;
            box-shadow: 0 2px 14px 0 #40c9ff4c;
            color: #fff;
            font-size: 2rem;
        }
        .form-icon {
            position: absolute;
            left: 18px;
            top: 45px;
            color: #40c9ffbb;
            font-size: 1.2rem;
        }
        .position-relative {
            position: relative !important;
        }
        .floating-error {
            position: fixed;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            max-width: 470px;
            width: 92vw;
            text-align: center;
            animation: fadein 0.5s;
        }
        @keyframes fadein {
            from {opacity: 0; top: 0;}
            to {opacity: 1; top: 30px;}
        }
        @media (max-width: 540px){
            .glass-card {
                padding: 1.3rem 1rem 1rem 1rem;
                border-radius: 1.2rem;
                max-width: 99vw;
            }
            .title-gradient { font-size: 1.6rem; }
            .icon-circle { width:36px; height:36px; font-size:1.3rem; }
            .floating-error { font-size: 0.98rem; }
        }
    </style>
</head>
<body>
    <?php if ($dbError): ?>
        <div class="floating-error">
            <div class="alert alert-danger shadow-lg py-3 px-4" style="border-radius: 1.2em; background:rgba(220,30,110,0.98); color:#fff; font-weight:500; font-size:1.1em;">
                <i class="bi bi-wifi-off" style="font-size:1.45em"></i>
                <br>
                <?php echo htmlspecialchars($dbError); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="glass-card neon-border shadow-lg mx-auto">
        <div class="d-flex align-items-center mb-3" style="z-index:2;">
            <div class="icon-circle"><i class="bi bi-person-plus"></i></div>
            <span class="title-gradient">REGISTER</span>
        </div>
        <?php if (!$dbError) echo $registerMsg; ?>
        <form action="" method="post" autocomplete="off">
            <div class="mb-3 position-relative">
                <label for="username" class="form-label"><i class="bi bi-person"></i> User</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="ชื่อผู้ใช้" required <?php if($dbError) echo 'disabled'; ?>>
            </div>
            <div class="mb-3 position-relative">
                <label for="fname" class="form-label"><i class="bi bi-card-text"></i> Full Name</label>
                <input type="text" name="fname" class="form-control" id="fname" placeholder="ชื่อ - นามสกุล" required <?php if($dbError) echo 'disabled'; ?>>
            </div>
            <div class="mb-3 position-relative">
                <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="อีเมล" required <?php if($dbError) echo 'disabled'; ?>>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label"><i class="bi bi-key"></i> Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="รหัสผ่าน" required minlength="6" <?php if($dbError) echo 'disabled'; ?>>
            </div>
            <div class="mb-2 position-relative">
                <label for="cpassword" class="form-label"><i class="bi bi-key-fill"></i> Confirm Password</label>
                <input type="password" name="cpassword" class="form-control" id="cpassword" placeholder="ยืนยันรหัสผ่าน" required minlength="6" <?php if($dbError) echo 'disabled'; ?>>
            </div>
            <div class="d-flex align-items-center mt-4">
                <button type="submit" class="btn btn-primary flex-fill me-2" <?php if($dbError) echo 'disabled'; ?>>
                    <i class="bi bi-person-check"></i> Register
                </button>
                <a href="login.php" class="btn btn-link"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
