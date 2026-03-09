<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600&family=Sarabun:wght@700&display=swap" rel="stylesheet">

<style>
    /* 🎨 กำหนดตัวแปรสีพื้นฐาน (Light Mode) */
    :root {
        --primary: #10b981;
        --primary-dark: #047857;
        --primary-light: #d1fae5;
        
        --bg-sidebar: #ffffff;
        --bg-body: #f8fafc;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --nav-hover: #f0fdf4;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* 🌙 ตัวแปรสีสำหรับ Dark Mode */
    [data-theme="dark"] {
        --bg-sidebar: #111827; /* สีเทาเข้มเกือบดำ */
        --bg-body: #757779;    /* สีดำสนิท */
        --text-main: #f1f5f9;  /* ตัวหนังสือขาว */
        --text-muted: #94a3b8; /* ตัวหนังสือเทาอ่อน */
        --border-color: #1f2937;
        --nav-hover: #1f2937;
        --primary-light: #064e3b;
    }

    /* 🏰 Sidebar Setup */
    body {
        background-color: var(--bg-body);
        color: var(--text-main);
        transition: background-color 0.3s, color 0.3s;
        margin: 0;
    }

    .sidebar {
        width: 260px;
        height: 100vh;
        background: var(--bg-sidebar);
        padding: 30px 20px;
        position: fixed;
        top: 0;
        left: 0;
        border-right: 1px solid var(--border-color);
        z-index: 1000;
        font-family: 'IBM Plex Sans Thai', sans-serif;
        display: flex;
        flex-direction: column;
        transition: background-color 0.3s, border-color 0.3s;
    }

    /* 🏷️ Brand & Theme Toggle */
    .brand-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        padding: 0 10px;
    }

    .brand {
        font-size: 20px;
        font-weight: 800;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'Sarabun', sans-serif;
        text-decoration: none;
    }

    /* 🌗 Theme Toggle Button */
    .theme-toggle {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--bg-sidebar);
        color: var(--text-main);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .theme-toggle:hover {
        background: var(--nav-hover);
        transform: scale(1.05);
    }

    /* 📋 Menu Section */
    .menu-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 20px 0 10px 10px;
    }

    /* 🔗 Navigation Links */
    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        border-radius: 12px;
        color: var(--text-muted);
        text-decoration: none;
        margin-bottom: 5px;
        font-weight: 500;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-link i {
        width: 20px;
        font-size: 18px;
        text-align: center;
    }

    .nav-link:hover {
        background: var(--nav-hover);
        color: var(--primary);
    }

    .nav-link.active {
        background: var(--primary);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    /* 🚪 Logout Section */
    .logout-box {
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn-logout:hover {
        background: #fef2f2;
        color: #ef4444 !important;
    }

    @media (min-width: 992px) {
        .admin-layout {
            margin-left: 260px;
            padding: 40px;
        }
    }
</style>

<aside class="sidebar">
    <div class="brand-wrapper">
        <a href="dashboard.php" class="brand">
            <i class="fas fa-leaf"></i> 
            <span>Admin</span>
        </a>
        <button class="theme-toggle" id="theme-toggle" title="สลับโหมดกลางวัน/กลางคืน">
            <i class="fas fa-moon" id="theme-icon"></i>
        </button>
    </div>
    
    <div class="menu-label">แดชบอร์ด</div>
    <nav>
        <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-chart-pie"></i> หน้าหลัก
        </a>
    </nav>

    <div class="menu-label">การจัดการสิ่งของ</div>
    <nav>
        <a href="admin_history.php" class="nav-link <?php echo ($current_page == 'admin_history.php') ? 'active' : ''; ?>">
            <i class="fas fa-hand-holding-heart"></i> รายการบริจาค
        </a>
        <a href="show_join_event.php" class="nav-link <?php echo ($current_page == 'show_join_event.php') ? 'active' : ''; ?>">
            <i class="fas fa-box-open"></i> รายงานการเข้าร่วมกิจกรรมจิตอาสา
        </a>
    </nav>

    <div class="menu-label">ระบบสมาชิก</div>
    <nav>
        <a href="showuser.php" class="nav-link <?php echo ($current_page == 'showuser.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> ผู้ใช้งานทั้งหมด
        </a>
 </nav>
       <div class="menu-label">เพิ่มข้อมูล</div> 
        <a href="addevent.php" class="nav-link <?php echo ($current_page == 'addevent.php') ? 'active' : ''; ?>">
            <i class="fas fa-calendar-plus"></i> เพิ่มกิจกรรม
        </a>
        <a href="add_public_relations.php" class="nav-link <?php echo ($current_page == 'add_public_relations.php') ? 'active' : ''; ?>">
            <i class="fas fa-calendar-plus"></i> เพิ่มข่าวประชาสัมพันธ์ประชาสัมพันธ์
        </a>
 </nav>
        <a href="index2.php" class="nav-link <?php echo ($current_page == 'index2.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> หน้าแรก
        </a>
    
        <a href="logout.php" class="nav-link btn-logout">
            <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
        </a>
    </div>
</aside>

<script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.documentElement; // ใช้ documentElement สำหรับดึง data-theme

    // 1. ตรวจสอบค่าที่บันทึกไว้ใน LocalStorage
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // ตั้งค่าเริ่มต้นตามที่บันทึกไว้
    if (currentTheme === 'dark') {
        body.setAttribute('data-theme', 'dark');
        themeIcon.classList.replace('fa-moon', 'fa-sun');
    }

    // 2. ฟังก์ชันสลับโหมด
    themeToggle.addEventListener('click', () => {
        let theme = body.getAttribute('data-theme');
        
        if (theme === 'dark') {
            body.setAttribute('data-theme', 'light');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('theme', 'light');
        } else {
            body.setAttribute('data-theme', 'dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('theme', 'dark');
        }
    });
</script>