<?php
// ---------------- เชื่อมต่อฐานข้อมูล (คงเดิม) ----------------
$servername = "147.50.254.50"; $username = "admin_itpsru"; $password = "azdhhkVFWpWv7LBZqUju"; $dbname = "admin_itpsru";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

session_start();
$message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, first_name, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $first_name, $role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['role'] = $role;

            if ($role == "admin") { header("Location: dashboard.php"); } 
            elseif ($role == "user") { header("Location: index.php"); } 
            elseif ($role == "volunteer") { header("Location: index2.php"); }
            exit();
        } else { $message = "รหัสผ่านไม่ถูกต้อง"; }
    } else { $message = "ไม่พบชื่อผู้ใช้นี้ในระบบ"; }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Premium Volunteer</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-dark: #022c22;
            --primary: #065f46;
            --accent: #d97706;
            --bg-light: #fafaf9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif;
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            width: 100%;
        }

        .side-image{
            flex: 1.2;
            background:linear-gradient(rgba(2, 44, 34, 0.4), rgba(2, 44, 34, 0.8)), 
            url('img/about.21.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 60px;
            color: white;
        }

        .side-image h1 {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .side-image p {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
            max-width: 500px;
        }

        .side-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: white;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
        }

        .brand-logo {
            font-size: 2rem;
            color: var(--primary-dark);
            font-weight: 800;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo i { color: var(--primary); }

        h2 { font-size: 1.8rem; font-weight: 700; color: #1c1917; margin-bottom: 10px; }
        .subtitle { color: #78716c; margin-bottom: 40px; font-size: 0.95rem; }

        .form-group { margin-bottom: 25px; }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #44403c;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 16px;
            border: 1px solid #e7e5e4;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #fff;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(6, 95, 70, 0.05);
        }

        /* --- เพิ่ม CSS ลืมรหัสผ่าน --- */
        .forgot-password-link {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 20px;
        }
        .forgot-password-link a {
            font-size: 0.85rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background-color: var(--primary-dark);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(2, 44, 34, 0.15);
        }

        .error-msg {
            color: #b91c1c;
            background: #fef2f2;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            border: 1px solid #fee2e2;
        }

        .footer-links {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9rem;
            color: #78716c;
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .side-image { display: none; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="side-image">
            <h1>เพื่อเด็กบนภู<br>เพื่อครูบนดอย</h1>
            <p>ร่วมส่งต่อโอกาสและการศึกษาให้กับพื้นที่ห่างไกล ด้วยพลังของอาสาสมัครเช่นคุณ</p>
        </div>

        <div class="side-form">
            <div class="login-box">
                <div class="brand-logo">
                    <i class="fas fa-mountain"></i> Login
                </div>
                
                <h2>ยินดีต้อนรับกลับ</h2>
                <p class="subtitle">กรุณาเข้าสู่ระบบ</p>

                <?php if($message != ''): ?>
                    <div class="error-msg">
                        <i class="fas fa-circle-info"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>ชื่อผู้ใช้งาน</label>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>

                    <div class="form-group">
                        <label>รหัสผ่าน</label>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="forgot-password-link">
                        <a href="forgot_password.php">ลืมรหัสผ่าน?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        เข้าสู่ระบบ
                    </button>
                </form>

                <div class="footer-links">
                    ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิกที่นี่</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>