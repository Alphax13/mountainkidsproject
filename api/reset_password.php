<?php
// --- 1. ส่วนเชื่อมต่อฐานข้อมูล ---
$servername = "147.50.254.50"; $username = "admin_itpsru"; $password = "azdhhkVFWpWv7LBZqUju"; $dbname = "admin_itpsru";
$conn = new mysqli($servername, $username, $password, $dbname);

$message = '';
$token = $_GET['token'] ?? ($_POST['token'] ?? '');

if (empty($token)) {
    die("ไม่พบรหัสอ้างอิงสำหรับการรีเซ็ตรหัสผ่าน");
}

// ตรวจสอบ Token ว่ามีจริงไหม
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุแล้ว");
}

// 2. เมื่อมีการกดปุ่มบันทึก
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "รหัสผ่านทั้งสองช่องไม่ตรงกัน";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); 

        $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE id = ?");
        $update->bind_param("si", $hashed_password, $user['id']);
        
        if ($update->execute()) {
            echo "<script>alert('เปลี่ยนรหัสผ่านสำเร็จ!'); window.location='login.php';</script>";
            exit();
        } else {
            $message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Premium Volunteer</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700&family=IBM+Plex+Sans+Thai:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary-navy: #1c355e;
            --secondary-blue: #3b82f6;
            --primary-gradient: linear-gradient(135deg, #1c355e 0%, #3b82f6 100%);
            --glass-bg: rgba(255, 255, 255, 0.9); 
            --text-main: #1e293b; 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', 'IBM Plex Sans Thai', sans-serif; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            /* ลูกเล่นพื้นหลังขยับได้ (Mesh Gradient) */
            background: linear-gradient(-45deg, #1c355e, #3b82f6, #0ea5e9, #f0f9ff); 
            background-size: 400% 400%; 
            animation: gradientBG 15s ease infinite; 
            overflow: hidden; 
            position: relative;
        }

        @keyframes gradientBG { 
            0% { background-position: 0% 50%; } 
            50% { background-position: 100% 50%; } 
            100% { background-position: 0% 50%; } 
        }

        /* ลูกเล่น Particles ลอยขึ้น */
        .circles { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: 0; }
        .circles li { position: absolute; display: block; list-style: none; width: 20px; height: 20px; background: rgba(255, 255, 255, 0.15); animation: animate 25s linear infinite; bottom: -150px; }
        .circles li:nth-child(1){ left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .circles li:nth-child(2){ left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .circles li:nth-child(3){ left: 70%; width: 30px; height: 30px; animation-delay: 4s; }

        @keyframes animate {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }

        .main-card { 
            background: var(--glass-bg); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px); 
            border-radius: 40px; 
            padding: 60px 45px; 
            width: 100%; 
            max-width: 480px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3); 
            border: 1px solid rgba(255, 255, 255, 0.3); 
            text-align: center; 
            position: relative; 
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .main-card:hover { transform: translateY(-5px); }

        .brand-icon { 
            width: 80px; height: 80px; 
            background: var(--primary-gradient); 
            border-radius: 25px; 
            display: flex; align-items: center; justify-content: center; 
            margin: -100px auto 30px; 
            font-size: 35px; color: white; 
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4); 
            transform: rotate(-10deg);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-10deg); }
            50% { transform: translateY(-10px) rotate(-5deg); }
        }

        h2 { 
            font-size: 2rem; font-weight: 800; 
            background: linear-gradient(to right, #1c355e, #3b82f6); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            margin-bottom: 12px; 
        }

        p.desc { color: #64748b; margin-bottom: 40px; font-weight: 500; }

        .input-box { position: relative; margin-bottom: 25px; text-align: left; }
        .input-box label { display: block; font-weight: 700; color: var(--primary-navy); margin-bottom: 8px; margin-left: 5px; font-size: 0.9rem; }

        .input-box input { 
            width: 100%; padding: 18px 50px; 
            border-radius: 20px; 
            border: 2px solid #e2e8f0; 
            background: #f8fafc; 
            font-size: 1rem; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            color: var(--text-main); 
        }

        .input-box input:focus { 
            outline: none; border-color: var(--secondary-blue); 
            background: white; 
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1); 
            transform: scale(1.02); 
        }

        .input-box i.field-icon { position: absolute; left: 20px; top: 48px; color: #94a3b8; font-size: 1.1rem; }
        .eye-toggle { position: absolute; right: 20px; top: 48px; cursor: pointer; color: #94a3b8; transition: 0.3s; padding: 5px; }

        .btn-update { 
            width: 100%; padding: 20px; 
            border-radius: 20px; border: none; 
            background: var(--primary-gradient); 
            color: white; font-size: 1.1rem; font-weight: 700; 
            cursor: pointer; transition: all 0.3s; 
            margin-top: 15px; 
            box-shadow: 0 10px 25px rgba(28, 53, 94, 0.3); 
        }

        .btn-update:hover { transform: scale(1.02); filter: brightness(1.1); letter-spacing: 0.5px; }

        .error-anim { background: #fff1f2; color: #e11d48; padding: 15px; border-radius: 20px; margin-bottom: 25px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px; animation: shake 0.5s ease; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 75% { transform: translateX(10px); } }

        .footer-link { margin-top: 35px; }
        .footer-link a { color: #64748b; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: 0.3s; }
        .footer-link a:hover { color: var(--secondary-blue); }
    </style>
</head>
<body>
    <ul class="circles">
        <li></li><li></li><li></li><li></li><li></li><li></li>
    </ul>

    <div class="main-card">
        <div class="brand-icon"><i class="fas fa-lock-open"></i></div>
        <h2>Reset รหัสผ่าน</h2>
        <p class="desc">ตั้งรหัสผ่านใหม่เพื่อเข้าใช้งานบัญชีอาสาของคุณ</p>

        <?php if(!empty($message)): ?>
            <div class="error-anim"><i class="fas fa-triangle-exclamation"></i> <?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="input-box">
                <label>รหัสผ่านใหม่</label>
                <i class="fas fa-key field-icon"></i>
                <input type="password" name="password" id="p1" placeholder="ระบุรหัสผ่านใหม่" required>
                <i class="fas fa-eye eye-toggle" onclick="viewPass('p1', this)"></i>
            </div>

            <div class="input-box">
                <label>ยืนยันรหัสผ่านอีกครั้ง</label>
                <i class="fas fa-shield-check field-icon"></i>
                <input type="password" name="confirm_password" id="p2" placeholder="ระบุรหัสผ่านอีกครั้ง" required>
                <i class="fas fa-eye eye-toggle" onclick="viewPass('p2', this)"></i>
            </div>

            <button type="submit" class="btn-update">
                ยืนยันการเปลี่ยนรหัสผ่าน <i class="fas fa-check-circle" style="margin-left: 10px;"></i>
            </button>
        </form>

        <div class="footer-link">
            <a href="login.php"><i class="fas fa-chevron-left"></i> กลับสู่หน้าเข้าสู่ระบบ</a>
        </div>
    </div>

    <script>
        function viewPass(id, btn) {
            const el = document.getElementById(id);
            if(el.type === "password") { 
                el.type = "text"; 
                btn.className = "fas fa-eye-slash eye-toggle"; 
            } else { 
                el.type = "password"; 
                btn.className = "fas fa-eye eye-toggle"; 
            }
        }
    </script>
</body>
</html>