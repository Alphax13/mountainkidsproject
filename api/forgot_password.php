<?php
// --- 1. การเชื่อมต่อฐานข้อมูล (คงเดิม) ---
$servername = "147.50.254.50"; 
$username = "admin_itpsru"; 
$password = "azdhhkVFWpWv7LBZqUju"; 
$dbname = "admin_itpsru";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$message = '';
$message_type = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));
        $update = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        
        if ($update->execute()) {
            $reset_link = "reset_password.php?token=" . $token;
            $message = "พบอีเมลของคุณแล้ว! <a href='$reset_link' style='color:#1c355e; font-weight:700; text-decoration:underline;'>คลิกที่นี่เพื่อตั้งรหัสผ่านใหม่</a>";
            $message_type = 'success';
        }
    } else {
        $message = "ไม่พบอีเมลนี้ในระบบอาสาสมัคร";
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recovery Access | Premium UI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700&family=IBM+Plex+Sans+Thai:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-navy: #1c355e;
            --secondary-blue: #3b82f6;
            --primary-gradient: linear-gradient(135deg, #1c355e 0%, #3b82f6 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'IBM Plex Sans Thai', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* --- ลูกเล่น 1: Animated Mesh Gradient Background --- */
            background: linear-gradient(-45deg, #1c355e, #3b82f6, #0ea5e9, #e0f2fe);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            padding: 20px;
            overflow: hidden;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- ลูกเล่น 2: Floating Particles (วงกลมลอยไปมา) --- */
        .circles {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden; z-index: 0;
        }
        .circles li {
            position: absolute; display: block; list-style: none;
            width: 20px; height: 20px; background: rgba(255, 255, 255, 0.2);
            animation: animate 25s linear infinite; bottom: -150px;
        }
        .circles li:nth-child(1){ left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .circles li:nth-child(2){ left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .circles li:nth-child(3){ left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
        @keyframes animate {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }

        /* --- ลูกเล่น 3: Glassmorphism Card พร้อม Glow Effect --- */
        .recovery-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 40px;
            padding: 60px 45px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .recovery-card:hover {
            transform: translateY(-10px); /* ลอยขึ้นเมื่อเอาเมาส์วาง */
        }

        .brand-icon {
            width: 80px; height: 80px;
            background: var(--primary-gradient);
            border-radius: 25px;
            display: flex; align-items: center; justify-content: center;
            margin: -100px auto 30px; /* ดันขึ้นไปครึ่งหนึ่งให้ดูเท่ */
            font-size: 32px; color: white;
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.5);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-5deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
        }

        h2 {
            font-size: 2.2rem; font-weight: 800;
            background: linear-gradient(to right, #1c355e, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .subtitle { color: #64748b; margin-bottom: 35px; }

        input {
            width: 100%; padding: 18px 20px 18px 55px;
            border-radius: 20px; border: 2px solid #e2e8f0;
            background: #f8fafc; font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none; border-color: #3b82f6;
            background: white; box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.1);
        }

        .btn-submit {
            width: 100%; padding: 20px; border-radius: 20px;
            border: none; background: var(--primary-gradient);
            color: white; font-size: 1.1rem; font-weight: 700;
            cursor: pointer; transition: 0.4s;
            box-shadow: 0 10px 20px rgba(28, 53, 94, 0.3);
        }

        .btn-submit:hover {
            letter-spacing: 1px;
            box-shadow: 0 15px 30px rgba(28, 53, 94, 0.4);
            filter: brightness(1.2);
        }

        /* --- Alert Messages --- */
        .message {
            padding: 15px; border-radius: 15px; margin-bottom: 25px;
            text-align: left; display: flex; align-items: center; gap: 10px;
        }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; animation: shake 0.5s; }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body>

    <ul class="circles">
        <li></li><li></li><li></li><li></li><li></li><li></li><li></li>
    </ul>

    <div class="recovery-card">
        <div class="brand-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        
        <h2>กู้คืนรหัสผ่าน</h2>
        <p class="subtitle">ระบบรักษาความปลอดภัย Premium Volunteer</p>

        <?php if($message != ''): ?>
            <div class="message <?php echo $message_type; ?>">
                <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="position: relative; margin-bottom: 25px; text-align: left;">
                <label style="font-weight:700; color:var(--primary-navy); display:block; margin-bottom:8px; margin-left:5px;">ระบุอีเมลผู้ใช้งาน</label>
                <i class="fas fa-envelope" style="position:absolute; left:22px; top:46px; color:#94a3b8;"></i>
                <input type="email" name="email" placeholder="yourname@email.com" required>
            </div>

            <button type="submit" class="btn-submit">
                ส่งลิงก์ยืนยันตัวตน <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div style="margin-top: 30px;">
            <a href="login.php" style="text-decoration:none; color:#64748b; font-weight:600; font-size:0.9rem;">
                <i class="fas fa-chevron-left"></i> กลับไปหน้าเข้าสู่ระบบ
            </a>
        </div>
    </div>

</body>
</html>