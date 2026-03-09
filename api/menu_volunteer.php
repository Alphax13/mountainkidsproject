<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โครงการเพื่อเด็กบนภู เพื่อนครูบนดอย</title>
    <link href="https://fonts.googleapis.com/css2?family=Anuphan:wght@200;300;400;500;600;700&family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style-menu.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>

<nav>
    <div class="container nav-con">
        <div class="nav-left-side">
            <div class="logo">
                <a href="index2.php">
                    <img src="img/01.jpg" alt="โลโก้โครงการ" class="logo-img">
                </a>
            </div>
            <ul class="nav-menu">
                <li><a href="index2.php">หน้าแรก</a></li>
                <li><a href="donate.php">บริจาคสิ่งของ</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'volunteer'): ?>
                    <li><a href="events.php"><b>เข้าร่วมกิจกรรม</b></a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="nav-right-side">
            <div class="auth-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-avatar-container">
                        <img src="<?php echo htmlspecialchars($user_data['profile_image_path'] ?? 'uploads/profiles/default.png'); ?>" 
                             alt="Avatar" class="nav-avatar-img">
                    </div>
                    
                    <a href="history.php" class="user-link">ประวัติสมาชิก</a>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'volunteer'): ?>
                        <span class="divider">|</span>
                        <a href="user_profile.php"><b>ประวัติกิจกรรม</b></a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <span class="divider">|</span>
                        <a href="showuser.php"><b>สำหรับ Admin </b></a>
                    <?php endif; ?>

                    <span class="divider">|</span>
                    <a href="logout.php" class="btn-logout">ออกจากระบบ</a>

                <?php else: ?>
                    <a href="login.php" class="btn-login">เข้าสู่ระบบ</a>
                    <span class="divider">|</span>
                    <a href="register.php" class="user-link">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<style>
/* Global Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Anuphan', sans-serif;
    line-height: 1.7;
    letter-spacing: -0.02em;
}

/* Navigation Bar */
nav {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    height: 80px; 
    display: flex;
    align-items: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
}

.nav-con {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 25px;
}

.nav-left-side {
    display: flex;
    align-items: center;
    gap: 50px;
}

.logo img.logo-img {
    height: 52px;
    width: auto;
    display: block;
    transition: transform 0.4s ease;
}

.nav-menu {
    display: flex;
    list-style: none;
    gap: 30px;
}

.nav-menu a {
    text-decoration: none;
    color: #64748b;
    font-size: 0.95rem;
    font-weight: 500;
    position: relative;
    transition: 0.3s;
}

.nav-menu a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -5px;
    left: 0;
    background-color: #19729c;
    transition: width 0.3s ease;
}

.nav-menu a:hover {
    color: #19729c;
}

.nav-menu a:hover::after {
    width: 100%;
}

/* Auth Menu */
.auth-menu {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    padding: 5px 5px 5px 18px;
    border-radius: 50px;
    border: 1px solid #edf2f7;
}

.auth-menu a {
    text-decoration: none;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
}

.btn-logout {
    background: #fee2e2;
    color: #ef4444 !important;
    padding: 6px 15px;
    border-radius: 50px;
    transition: 0.3s;
}

.btn-logout:hover {
    background: #ef4444;
    color: #ffffff !important;
}

.divider {
    color: #e2e8f0;
}
/* 1. คอนเทนเนอร์ (กรอบวงกลม) */
.user-avatar-container {
    width: 35px;               /* ปรับขนาดความกว้างตามใจชอบ */
    height: 35px;              /* ต้องเท่ากับความกว้างเพื่อให้เป็นจัตุรัส */
    border-radius: 50%;        /* ทำให้เป็นวงกลม */
    overflow: hidden;          /* ตัดส่วนของรูปที่ล้นขอบวงกลมออก */
    display: inline-flex;      /* จัดให้อยู่บรรทัดเดียวกับข้อความ */
    align-items: center;       /* จัดรูปให้อยู่กึ่งกลางแนวตั้ง */
    justify-content: center;   /* จัดรูปให้อยู่กึ่งกลางแนวนอน */
    border: 2px solid #ffffff; /* เพิ่มขอบสีขาวให้ดูเด่นขึ้น */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* เพิ่มเงาจางๆ แบบใน Figma */
    vertical-align: middle;    /* จัดวางให้ตรงกับระดับบรรทัดตัวอักษร */
    margin-right: 8px;         /* ระยะห่างจากชื่อ "ประวัติสมาชิก" */
}

/* 2. ตัวรูปภาพข้างใน */
.user-avatar-container img {
    width: 100%;               /* ให้กว้างเต็มกรอบ */
    height: 100%;              /* ให้สูงเต็มกรอบ */
    object-fit: cover;         /* **สำคัญมาก** ทำให้รูปไม่เบี้ยวและถมเต็มพื้นที่ */
    object-position: center;   /* จัดให้จุดโฟกัสของรูปอยู่ตรงกลาง */
}
</style>