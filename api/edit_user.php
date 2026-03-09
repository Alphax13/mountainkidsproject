<?php
// ---------------- PHP Logic (คงเดิมแต่ปรับปรุงความปลอดภัย) ----------------
$servername = "147.50.254.50";
$username = "admin_itpsru";
$password = "azdhhkVFWpWv7LBZqUju";
$dbname = "admin_itpsru";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection Error");

session_start();

if (!isset($_GET['id'])) die("ไม่พบรหัสผู้ใช้");
$user_id = $_GET['id'];

// ดึงข้อมูลเดิม
$stmt = $conn->prepare("SELECT first_name, username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $username, $role);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = $_POST['first_name'];
    $new_username = $_POST['username'];
    $new_role = $_POST['role'];
    $new_password = $_POST['password'];

    if ($new_password !== "") {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET first_name=?, username=?, role=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $new_name, $new_username, $new_role, $hashed, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name=?, username=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $new_name, $new_username, $new_role, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: showuser.php?edit=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Emerald Collective</title>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@300;800&family=Sarabun:wght@300;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --emerald: #10b981;
            --dark-green: #064e3b;
            --glass: rgba(255, 255, 255, 0.85);
            --glow: rgba(16, 185, 129, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; cursor: none; }

        body {
            font-family: 'Sarabun', sans-serif;
            background: #f0fdf4;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-image: radial-gradient(circle at 20% 20%, #dcfce7 0%, transparent 40%),
                              radial-gradient(circle at 80% 80%, #f0fdf4 0%, transparent 40%);
        }

        /* --- Custom Cursor --- */
        #cursor {
            position: fixed; width: 15px; height: 15px; background: var(--emerald);
            border-radius: 50%; pointer-events: none; z-index: 9999;
            transition: transform 0.15s ease-out, background 0.3s;
        }

        /* --- Background Particles --- */
        #canvas-bg { position: fixed; top: 0; left: 0; z-index: -1; }

        /* --- Main Box (Glassmorphism) --- */
        .box {
            background: var(--glass);
            backdrop-filter: blur(20px);
            padding: 50px;
            border-radius: 40px;
            width: 500px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(6, 78, 59, 0.1);
            position: relative;
            transform-style: preserve-3d;
            animation: slideIn 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        h2 {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 35px; font-weight: 800; color: var(--dark-green);
            margin-bottom: 35px; text-align: center; letter-spacing: -1px;
        }

        /* --- Form Inputs --- */
        .input-group { position: relative; margin-bottom: 25px; }
        
        label { 
            display: block; margin-bottom: 8px; font-weight: 600; 
            color: var(--dark-green); font-size: 14px; padding-left: 5px;
        }

        input, select {
            width: 100%; padding: 15px 20px; border-radius: 18px;
            border: 2px solid transparent; background: white;
            font-size: 16px; transition: 0.3s; color: var(--dark-green);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        input:focus {
            outline: none; border-color: var(--emerald);
            box-shadow: 0 0 20px var(--glow); transform: translateY(-2px);
        }

        /* --- Password Strength Bar --- */
        .strength-meter {
            height: 4px; width: 0%; background: #ef4444;
            margin-top: 8px; border-radius: 2px; transition: 0.5s;
        }

        /* --- Buttons --- */
        .btn-save {
            width: 100%; padding: 18px; border: none; border-radius: 20px;
            background: var(--dark-green); color: white;
            font-size: 18px; font-weight: 800; margin-top: 15px;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }

        .btn-save:hover {
            background: var(--emerald);
            box-shadow: 0 15px 30px var(--glow);
        }

        .back-link {
            display: block; text-align: center; margin-top: 20px;
            color: var(--emerald); text-decoration: none; font-weight: 600; font-size: 14px;
        }

    </style>
</head>
<body>

<div id="cursor"></div>
<canvas id="canvas-bg"></canvas>

<div class="box js-tilt" data-tilt>
    <h2>Edit <span style="color: var(--emerald);">Profile.</span></h2>
    
    <form method="POST" id="editForm">
        <div class="input-group">
            <label><i class="fa-regular fa-user"></i> ชื่อจริง</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
        </div>

        <div class="input-group">
            <label><i class=""></i> ชื่อผู้ใช้ </label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>

        <div class="input-group">
            <label><i class="fa-solid fa-shield-halved"></i> ตำแหน่ง</label>
            <select name="role">
                <option value="admin" <?php if($role=="admin") echo "selected"; ?>>แอดมิน</option>
                <option value="volunteer" <?php if ($role=="volunteer") echo "selected"; ?>>อาสาสมัคร</option>
                <option value="user" <?php if($role=="user") echo "selected"; ?>>ผู้บริจาค</option>
            </select>
        </div>

        <div class="input-group">
            <label><i class="fa-solid fa-key"></i> รหัสผ่านใหม่</label>
            <input type="password" id="pw-input" name="password" placeholder="เว้นว่างไว้หากไม่เปลี่ยน">
            <div class="strength-meter" id="strength-bar"></div>
        </div>

        <button type="submit" class="btn-save magnetic">
            <i class="fa-solid fa-cloud-arrow-up"></i> บันทึกข้อมูล
        </button>

        <a href="showuser.php" class="back-link">ยกเลิกและกลับหน้าเดิม</a>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
<script>
    // 1. Custom Cursor Logic
    const cursor = document.getElementById('cursor');
    document.addEventListener('mousemove', e => {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
    });

    document.querySelectorAll('input, button, select, a').forEach(el => {
        el.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(4)';
            cursor.style.background = 'rgba(16, 185, 129, 0.2)';
        });
        el.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            cursor.style.background = '#10b981';
        });
    });

    // 2. Password Strength Real-time
    const pwInput = document.getElementById('pw-input');
    const bar = document.getElementById('strength-bar');
    
    pwInput.addEventListener('input', () => {
        const val = pwInput.value;
        let strength = 0;
        if (val.length > 5) strength += 30;
        if (/[A-Z]/.test(val)) strength += 30;
        if (/[0-9]/.test(val)) strength += 40;

        bar.style.width = strength + '%';
        if (strength < 40) bar.style.background = '#ef4444';
        else if (strength < 70) bar.style.background = '#f59e0b';
        else bar.style.background = '#10b981';
    });

    // 3. Magnetic Button Effect
    const magneticBtn = document.querySelector('.magnetic');
    magneticBtn.addEventListener('mousemove', (e) => {
        const rect = magneticBtn.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        magneticBtn.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
    });
    magneticBtn.addEventListener('mouseleave', () => {
        magneticBtn.style.transform = `translate(0px, 0px)`;
    });

    // 4. Background Canvas Particles
    const canvas = document.getElementById('canvas-bg');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    let particles = [];
    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 5 + 1;
            this.speedX = Math.random() * 1 - 0.5;
            this.speedY = Math.random() * 1 - 0.5;
        }
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.size > 0.2) this.size -= 0.01;
        }
        draw() {
            ctx.fillStyle = 'rgba(16, 185, 129, 0.1)';
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (particles.length < 50) particles.push(new Particle());
        for (let i = 0; i < particles.length; i++) {
            particles[i].update();
            particles[i].draw();
            if (particles[i].size <= 0.2) {
                particles.splice(i, 1); i--;
            }
        }
        requestAnimationFrame(animate);
    }
    animate();

    // 5. Initialize Tilt
    VanillaTilt.init(document.querySelectorAll(".js-tilt"), {
        max: 5,
        speed: 400,
        glare: true,
        "max-glare": 0.2,
    });
</script>

</body>
</html>