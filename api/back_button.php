<?php
$backPage = 'index.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'volunteer') {
        $backPage = 'user_profile.php';
    } elseif ($_SESSION['role'] === 'user') {
        $backPage = 'index.php';
    }
}
?>

<a href="<?= $backPage ?>" class="back-btn" title="ย้อนกลับ">
    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"></line>
        <polyline points="12 19 5 12 12 5"></polyline>
    </svg>
</a>

<style>
.back-btn {
    position: fixed;
    top: 20px;
    left: 20px;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #ffffff; /* สีขาวสะอาด */
    color: #1a1a1a;           /* สีไอคอนเข้ม */
    border-radius: 12px;      /* ขอบมนแบบทันสมัย (Squircle) */
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* เงาฟุ้งๆ นุ่มๆ */
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    border: 1px solid rgba(0, 0, 0, 0.05); /* เส้นขอบจางๆ เพิ่มมิติ */
}

.back-btn:hover {
    background-color: #1a1a1a; /* เปลี่ยนเป็นสีเข้มตอน Hover */
    color: #ffffff;
    transform: translateX(-5px); /* ขยับไปทางซ้ายเล็กน้อยให้ดูมี Interaction */
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.back-btn:active {
    transform: scale(0.95) translateX(-5px); /* ยุบลงเล็กน้อยตอนกด */
}
</style>