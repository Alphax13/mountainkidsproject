<?php
session_start();
// --- 1. ส่วนเชื่อมต่อฐานข้อมูล (คงเดิม) ---
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");


// --- 2. ฟังก์ชันจัดการหมวดหมู่ (คงเดิม) ---
function get_clean_group($cat_raw) {
    $cat = trim($cat_raw);
    if (mb_strpos($cat, 'ยาสามัญ') !== false) return 'ยาสามัญประจำบ้าน';
    if (mb_strpos($cat, 'เสื้อผ้า') !== false) return 'เสื้อผ้ามือสอง';
    if (mb_strpos($cat, 'ข้าวสาร') !== false || mb_strpos($cat, 'อาหาร') !== false) return 'ข้าวสารและอาหารแห้ง';
    if (mb_strpos($cat, 'เรียน') !== false || mb_strpos($cat, 'การเรียน') !== false) return 'อุปกรณ์การเรียน';
    if (mb_strpos($cat, 'กีฬา') !== false) return 'อุปกรณ์กีฬา';
    if (mb_strpos($cat, 'ของเล่น') !== false || mb_strpos($cat, 'ตุ๊กตา') !== false) return 'ของเล่นและตุ๊กตา';
    if (mb_strpos($cat, 'ทำความสะอาด') !== false) return 'ผลิตภัณฑ์ทำความสะอาด';
    if (mb_strpos($cat, 'ภายในบ้าน') !== false) return 'เครื่องใช้ภายในบ้าน';
    if (mb_strpos($cat, 'ขนม') !== false) return 'ขนมและของว่าง';
    return trim(preg_replace('/^[0-9]+\.\s?/', '', $cat));
}

function getColorMapping($name) {
    $map = [
        'ยาสามัญประจำบ้าน' => '#10b981',
        'เสื้อผ้ามือสอง' => '#f43f5e',
        'ข้าวสารและอาหารแห้ง' => '#3b82f6',
        'อุปกรณ์การเรียน' => '#f59e0b',
        'อุปกรณ์กีฬา' => '#6366f1',
        'ของเล่นและตุ๊กตา' => '#ec4899',
        'ผลิตภัณฑ์ทำความสะอาด' => '#84cc16',
        'เครื่องใช้ภายในบ้าน' => '#8b5cf6',
        'ขนมและของว่าง' => '#06b6d4'
    ];
    return $map[$name] ?? '#94a3b8';
}

// --- 3. ดึงข้อมูลและประมวลผล (คงเดิม) ---
$sql_main = "SELECT i.sub_category, t.target_quantity, t.current_received 
             FROM event_item_targets t
             JOIN donation_items i ON t.item_id = i.item_id
             WHERE i.is_active = 1";
$result_main = $conn->query($sql_main);

$temp_groups = [];
while($row = $result_main->fetch_assoc()) {
    $group_name = get_clean_group($row['sub_category']);
    if (!isset($temp_groups[$group_name])) {
        $temp_groups[$group_name] = ['t_tar' => 0, 't_rec' => 0];
    }
    $temp_groups[$group_name]['t_tar'] += (float)$row['target_quantity'];
    $temp_groups[$group_name]['t_rec'] += (float)$row['current_received'];
}

// --- 4. การเรียงลำดับให้เหมือนรูปภาพ (คงเดิม) ---
$defined_order = [
    'ข้าวสารและอาหารแห้ง',
    'เครื่องใช้ภายในบ้าน',
    'ผลิตภัณฑ์ทำความสะอาด',
    'ของเล่นและตุ๊กตา',
    'อุปกรณ์กีฬา',
    'อุปกรณ์การเรียน',
    'เสื้อผ้ามือสอง',
    'ยาสามัญประจำบ้าน'
];

$all_names = []; $all_rec = []; $all_tar = []; $all_colors = [];
foreach($defined_order as $name) {
    if (isset($temp_groups[$name])) {
        $all_names[] = $name;
        $all_rec[] = $temp_groups[$name]['t_rec'];
        $all_tar[] = $temp_groups[$name]['t_tar'];
        $all_colors[] = getColorMapping($name);
    }
}

$user_data = null;
if (isset($_SESSION['user_id'])) {
    $user_pk_id = $_SESSION['user_id'];
    $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $user_pk_id);
    $stmt_user->execute();
    $user_data = $stmt_user->get_result()->fetch_assoc();
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โครงการเพื่อเด็กบนภู เพื่อนครูบนดอย</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>
<style>
   @import url('https://fonts.googleapis.com/css2?family=Anuphan:wght@200;300;400;500;600;700&display=swap');

:root {
    /* (ค่าตัวแปรเดิมของคุณ) */
    --primary-color: #00507b;    
    --secondary-color: #00796b;  
    --accent-color: #c45b00;     
    --bg-light: #f4fbfd;
    --bg-soft: #f6fffd;          
    --text-dark: #2c3e50;
    --text-muted: #3c5759;
    --white: #fffdf9;
    --shadow-soft: 0 10px 40px rgba(0,0,0,0.07);
    --shadow-strong: 0 20px 50px rgba(0,0,0,0.12);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    /* เปลี่ยนเป็น Anuphan */
    font-family: 'Anuphan', sans-serif;
    background-color: var(--bg-light);
    color: var(--text-dark);
    line-height: 1.7; 
    -webkit-font-smoothing: antialiased;
    /* ลด Letter Spacing ลงเล็กน้อยเพื่อให้ตัวอักษรเกาะกลุ่มกันดู Modern ขึ้น */
    letter-spacing: -0.02em; 
}

/* ปรับหัวข้อให้ดูเบาลงแต่คมชัด (ความลับของดีไซน์คลีนๆ คือไม่ใช้ตัวหนาจัด) */
h1, h2, h3 {
    font-weight: 600; 
    letter-spacing: -0.03em;
}


.container {
    max-width: 1140px;
    margin: 0 auto;
    padding: 0 25px;
}

.container-narrow {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 20px;
}



/* 2. แถบเมนู - Modern & Elegant Version */
nav {
    background: rgba(255, 255, 255, 0.479); /* ขาวแบบโปร่งแสงเล็กน้อย */
    backdrop-filter: blur(10px); /* เอฟเฟกต์กระจกฝ้าสำหรับเว็บสมัยใหม่ */
    height: 70px; 
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
    max-width: 1200px; /* บีบพื้นที่ให้ดูพอดีสายตา */
    margin: 0 auto;
    padding: 0 25px;
}

/* ฝั่งซ้าย: Logo + Menu */
.nav-left-side {
    display: flex;
    align-items: center;
    gap: 50px; /* เพิ่มระยะห่างให้ดูโปร่ง */
}

.logo img.logo-img {
    height: 52px !important; /* ปรับเพิ่มจาก 42px เป็น 52px */
    width: auto !important;
    display: block !important;
    object-fit: contain;
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* เพิ่มเติม: ปรับความสูงของ nav ให้รองรับโลโก้ที่ใหญ่ขึ้นเล็กน้อย */
nav {
    height: 80px; /* ปรับจาก 70px เป็น 80px เพื่อให้โลโก้มีพื้นที่หายใจ */
    /* ... ส่วนที่เหลือคงเดิม ... */
}

/* เมนูหลัก */
.nav-menu {
    display: flex;
    list-style: none !important;
    gap: 30px;
    margin: 0;
    padding: 0;
}

.nav-menu a {
    text-decoration: none !important;
    color: #64748b;
    font-size: 0.95rem;
    font-weight: 500;
    position: relative; /* สำหรับทำเส้นใต้ตอน Hover */
    transition: 0.3s ease;
}

/* เอฟเฟกต์เส้นใต้ขีดนุ่มๆ */
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

/* ฝั่งขวา: เมนูสมาชิกแบบพรีเมียม */
.auth-menu {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    padding: 5px 5px 5px 18px; /* ปรับ Padding ให้ปุ่ม Logout เด่น */
    border-radius: 50px;
    border: 1px solid #edf2f7;
}

.auth-menu a {
    text-decoration: none !important;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
    transition: 0.2s;
}

.auth-menu .user-link:hover {
    color: #19729c;
}

/* ปรับปุ่มออกจากระบบให้เหมือนปุ่มกดจริงๆ */
.auth-menu .btn-logout {
    background: #fee2e2;
    color: #ef4444 !important;
    padding: 6px 15px;
    border-radius: 50px;
    transition: 0.3s;
}

.auth-menu .btn-logout:hover {
    background: #ef4444;
    color: #ffffff !important;
}

.divider {
    color: #e2e8f0;
    font-weight: 300;
}



/* 3. Hero Section (ปรับปรุงจำนวนบรรทัดและสีหัวข้อ) */
.maincontent {
    padding: 100px 0;
    background: linear-gradient(135deg, #f3eee7 0%, #defbf8 100%);
    min-height: 65vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.maincontent-con {
    max-width: 1100px; /* ขยายความกว้างรวมเพื่อให้มีพื้นที่ยืดตัวหนังสือ */
    margin: 0 auto;
    padding: 0 25px;
    text-align: center;
}

.maincontent-info {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.badge {
    background: rgba(21, 45, 109, 0.1);
    color: #152d6d;
    padding: 7px 18px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 25px;
    letter-spacing: 0.5px;
}

.maincontent-info h1 {
    font-size: clamp(2.2rem, 5vw, 3.2rem);
    font-weight: 800;
    line-height: 1.25;
    color: #07164c; /* สีน้ำเงินหลักสำหรับบรรทัดแรก */
    margin-bottom: 25px;
    letter-spacing: -1px;
}

/* ตั้งค่าสีส้มสำหรับบรรทัดที่สอง */
.maincontent-info h1 .highlight {
    color: #cf6800; /* สีส้มโทนเดียวกับปุ่มของคุณ */
    display: inline-block; /* ช่วยให้การจัดระยะแม่นยำขึ้น */
    margin-top: 5px;      /* เพิ่มระยะห่างระหว่างบรรทัดสีน้ำเงินกับส้มเล็กน้อยถ้าต้องการ */
}

.maincontent-info p {
    font-size: 1.15rem;
    color: #4a5568;
    line-height: 1.6; /* ปรับช่องไฟให้นิดนึงเพื่อความสวยงาม */
    margin-bottom: 35px;
    /* --- ปรับตรงนี้เพื่อให้เหลือ 2 บรรทัด --- */
    max-width: 900px; 
    width: 100%;
}

.hero-btns {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}

/* ส่วนปุ่ม 2 อัน */
.btn-primary, .btn-secondary {
    padding: 14px 32px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.05rem;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-width: 170px;
    
    /* --- ส่วนที่เพิ่มเพื่อลบเส้นใต้ --- */
    text-decoration: none; 
    display: inline-block; /* ช่วยให้ระยะ Padding และ Margin ทำงานแม่นยำขึ้น */
    text-align: center;
}

.btn-primary { 
    background: #19729c; 
    color: #ffffff; 
    border: 2px solid #19729c;
    box-shadow: 0 4px 15px rgba(13, 86, 143, 0.51);
}

.btn-primary:hover {
    background: #0d1e4a;
    border-color: #0d1e4a;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(21, 45, 109, 0.25);
    color: #ffffff; /* ป้องกันสีตัวอักษรเปลี่ยนตอน Hover */
}

.btn-secondary { 
    border: 2px solid #ff7043;
    color: #ff7043;
    background: transparent;
}

.btn-secondary:hover {
    background: #ff7043;
    color: #ffffff;
    transform: translateY(-3px);
    text-decoration: none; /* ย้ำอีกครั้งว่าไม่เอาเส้นใต้ตอน Hover */
}










/* ============================================================
   4. SERVICE SHOWCASE (เราทำอะไรบ้าง?) - VERSION: Click & Swipe
   ============================================================ */

.what-we-do { 
    padding: 100px 0; 
    background: var(--white);
}

.section-title { 
    text-align: center; 
    margin-bottom: 70px; 
}

.section-title h2 { 
    font-size: 2.4rem; 
    font-weight: 700; 
    color: #152d6d;
}

.underline { 
    width: 70px; 
    height: 5px; 
    background: var(--accent-color); 
    margin: 18px auto; 
    border-radius: 10px; 
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
    gap: 30px;
    justify-content: center; 
    max-width: 1200px;
    margin: 0 auto;
}

.card {
    background: #ffffff; 
    border-radius: 24px;
    padding: 30px 25px; 
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    align-items: center;      
    text-align: center;       
    transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    max-width: 380px; 
    margin: 0 auto;
    position: relative;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-strong);
}

.card-content {
    margin-bottom: 25px;
    min-height: 140px;        
    display: flex;
    flex-direction: column;
    justify-content: center;  
}

.card h3 { 
    color: #ab4506; 
    margin-bottom: 12px; 
    font-weight: 700;
    font-size: 1.35rem;
}

.card p {
    color: #5b5b5b;
    line-height: 1.6;
    font-size: 0.95rem;
}

/* --- ส่วนระบบ Slide รูปภาพ --- */
.image-side {
    width: 100%;
    position: relative; /* สำคัญ: เพื่อให้ปุ่มลูกศรเกาะกับรูป */
    border-radius: 18px;
    overflow: hidden;         
    margin-top: auto;         
}

.gallery, .slider {
    display: flex;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-snap-type: x mandatory; 
    scrollbar-width: none;    
    -ms-overflow-style: none;
    border-radius: 18px;
    scroll-behavior: smooth; /* ทำให้เวลาคลิกปุ่มแล้วรูปเลื่อนแบบนุ่มนวล */
}

.gallery::-webkit-scrollbar, .slider::-webkit-scrollbar { display: none; }

.gallery img, .slider img {
    flex: 0 0 100%;           
    width: 100%;
    height: 240px;            
    object-fit: cover;
    scroll-snap-align: center; 
}

/* --- เพิ่มปุ่มลูกศร (Navigation Arrows) --- */
.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.8);
    color: #152d6d;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: 0.3s;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.nav-btn:hover { background: #fff; color: #ab4506; }
.nav-btn.prev { left: 10px; }
.nav-btn.next { right: 10px; }

.slider-hint {
    font-size: 0.8rem;
    color: #94a3b8;
    text-align: center;
    margin-top: 15px;
    font-style: italic;
}





/* 5.(แนะนำ โครงการ) - ปรับแค่ระยะห่างให้แคบลง */
.content-block { padding: 80px 0; } /* ลด Padding รวมลงนิดหน่อย */

.content-block-inner {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 80px;
    align-items: center;
}

.content-block-inner.reverse { direction: rtl; }
.content-block-inner.reverse .text-side, 
.content-block-inner.reverse .image-side { direction: ltr; }

/* 1. ปรับ Tag ให้ชิดหัวข้อหลักมากขึ้น */
.tag { 
    color: var(--secondary-color); 
    font-weight: 700; 
    font-size: 0.9rem; 
    display: block; 
    margin-bottom: 2px; /* ลดจาก 5px เหลือ 2px */
}

/* 2. ปรับเส้นใต้ให้ชิดทั้งบนและล่าง */
.underline-left { 
    width: 50px; 
    height: 4px; 
    background: var(--accent-color); 
    /* margin: บน(5px) ขวา(0) ล่าง(12px) ซ้าย(0) */
    margin: 5px 0 12px 0; 
    border-radius: 10px; 
}

/* 3. ปรับเนื้อหาให้ขยับขึ้นมาหาเส้น */
.text-side p { 
    text-align: justify; 
    color: var(--text-muted); 
    font-size: 1.05rem;
    margin-top: 0; /* มั่นใจว่าไม่มี margin บนมาดัน */
    line-height: 1.6;
}






/* คอนเทนเนอร์หลักสำหรับคุมระยะลูกศร */
.slider-wrapper {
    position: relative;
    max-width: 1200px; /* เพิ่มจาก 1000px เป็น 1200px เพื่อให้มีที่ว่างด้านข้าง */
    margin: 0 auto;
    padding: 0 60px;   /* เพิ่มพื้นที่หายใจให้ลูกศร */
}

/* 1. เพิ่มความสูงที่แน่นอนให้กับตัวสไลด์ (สำคัญมาก) */
.swiper {
    width: 100%;
    /* เพิ่ม padding เผื่อไว้ให้เงาของ Card และ Pagination ไม่โดนตัด */
    padding: 20px 0 60px 0 !important; 
}

/* 2. ปรับจูนหน้าตา Card ในโหมด Desktop */
.activity-card-fancy {
    display: flex;
    min-height: 500px; /* กำหนดความสูงขั้นต่ำไว้เพื่อความชัวร์ */
    background: var(--white);
    border-radius: 24px;
    overflow: hidden;
    margin: 0 auto; /* จัดกลาง */
    /* ลบ margin-top: 40px; ออกถ้ามันทำให้สไลด์เบี้ยว */
}

/* 3. จัดการเรื่องรูปภาพ */
.activity-image {
    flex: 1;
    position: relative;
    min-width: 450px;
    min-height: 100%; /* ให้สูงเท่ากับตัวการ์ดเสมอ */
}

.activity-image img {
    position: absolute; /* ใช้ absolute เพื่อให้รูปคลุมพื้นที่ flex */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ปรับแต่งตำแหน่งลูกศร */
/* ดันลูกศรออกไปนอกขอบการ์ด */
.swiper-button-next {
    right: 5px; /* ปรับให้ชิดขอบ Wrapper ฝั่งขวา */
}

.swiper-button-prev {
    left: 5px;  /* ปรับให้ชิดขอบ Wrapper ฝั่งซ้าย */
}

.swiper-button-next:hover, 
.swiper-button-prev:hover {
    background: #ff6b6b;
    color: #fff;
}

/* ดันลูกศรออกไปข้างนอกขอบการ์ด */
.swiper-button-next {
    right: 0px; 
}

.swiper-button-prev {
    left: 0px;
}

/* ปรับขนาดลูกศรข้างใน */
.swiper-button-next::after, 
.swiper-button-prev::after {
    font-size: 20px;
    font-weight: bold;
}

/* ปรับจุดกลมๆ ด้านล่าง */
.swiper-pagination-bullet-active {
    background: #ff6b6b;
}

/* สำหรับมือถือ: ซ่อนลูกศรแล้วใช้การรูดหน้าจอแทน */
@media (max-width: 768px) {
    .slider-wrapper {
        padding: 0;
    }
    .swiper-button-next, 
    .swiper-button-prev {
        display: none;
    }
}




/* 7. กิจกรรม / ข่าวสาร (ปรับสมดุล Gap และหัวข้อกลาง) */
.content { 
    padding: 100px 0; 
    background: #fffdfc; 
}

/* จัดหัวข้อหลักให้อยู่กึ่งกลางสมบูรณ์ */
.content-title {
    text-align: center;
    margin-bottom: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.content-title h2 {
    font-size: 2.4rem;
    color: #152d6d;
    font-weight: 800;
    margin-bottom: 0;
}

/* เพิ่มเส้นใต้หัวข้อหลักเพื่อให้ดูมีสไตล์เหมือนส่วนอื่น */
.content-title::after {
    content: "";
    width: 70px;
    height: 5px;
    background: #d76800;
    margin-top: 15px;
    border-radius: 10px;
}

.content-con {
    display: grid;
    grid-template-columns: repeat(4, 1fr); 
    /* --- ปรับช่องว่าง (Gap) ให้ห่างขึ้นเพื่อความสมดุล --- */
    gap: 40px; 
    max-width: 9999px; 
    margin: 0 auto;
    padding: 0 40px; /* เพิ่ม Padding ซ้ายขวาเพื่อไม่ให้ช่องชิดขอบจอเกินไป */
}

.content-item {
    background: #ffffff;
    border-radius: 24px; /* ปรับให้โค้งมนรับกับ Gap ที่กว้างขึ้น */
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.content-item:hover { 
    transform: translateY(-12px);
    box-shadow: 0 20px 40px rgba(21, 45, 109, 0.12);
}

.content-item img { 
    width: 100%; 
    height: 190px; 
    object-fit: cover; 
}

.item-text { 
    padding: 25px 20px; 
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-grow: 1;
}

.item-text h4 { 
    color: #152d6d; 
    margin-bottom: 12px; 
    font-weight: 700;
    font-size: 1.15rem;
    line-height: 1.4;
}

.item-text p {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 20px;
}

.content-btn {
    display: inline-block;
    margin-top: auto;
    color: #d76800; 
    text-decoration: none;
    font-weight: 700;
    font-size: 0.95rem;
    transition: 0.3s;
}

/* ปรับปรุง Responsive ให้ยังดูดีในทุกหน้าจอ */
@media (max-width: 1300px) {
    .content-con {
        gap: 25px; /* ลด Gap ลงเล็กน้อยในจอขนาดกลาง */
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .content-con {
        grid-template-columns: 1fr;
        padding: 0 25px;
    }
}


/* ============================================================
   กิจกรรมถัดไป (Upcoming Activity) - Card แบบพิเศษ
   ============================================================ */

.upcoming-activity {
    padding: 100px 0;
    background-color: var(--bg-soft); /* ใช้สีพื้นหลังอ่อนๆ ตามธีมหลัก */
}

/* การจัดวาง Card หลัก */
.activity-card-fancy {
    display: flex;
    background: var(--white);
    border-radius: 24px; /* ปรับให้มนเท่ากับ Card ส่วนอื่น */
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(0, 0, 0, 0.03);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    margin-top: 40px;
    max-width: 1050px;
    margin-left: auto;
    margin-right: auto;
}

.activity-card-fancy:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-strong);
}

/* ส่วนรูปภาพ */
.activity-image {
    flex: 1;
    position: relative;
    min-width: 450px;
    overflow: hidden;
}

.activity-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.activity-card-fancy:hover .activity-image img {
    transform: scale(1.08);
}

/* ป้ายสถานะ กิจกรรมถัดไป */
.activity-status {
    position: absolute;
    top: 25px;
    left: 25px;
    background: var(--accent-color); /* ใช้สีส้ม Accent ตาม Root */
    color: var(--white);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(196, 91, 0, 0.3);
    z-index: 10;
    letter-spacing: 0.5px;
}

/* ส่วนเนื้อหาข้อมูล */
.activity-info-fancy {
    flex: 1.2;
    padding: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.activity-info-fancy h3 {
    font-size: 2.2rem;
    color: var(--primary-color); /* สีน้ำเงินหลัก */
    margin-bottom: 12px;
    line-height: 1.3;
}

.school-tagline {
    color: var(--secondary-color); /* สีเขียวหัวเป็ดตามธีม */
    font-weight: 500;
    margin-bottom: 35px;
    font-style: italic;
    border-left: 4px solid var(--accent-color);
    padding-left: 20px;
    font-size: 1.1rem;
}

/* ตารางข้อมูล สถานที่/เวลา */
.info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 25px;
    margin-bottom: 40px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 18px;
}

.info-item i {
    font-size: 1.3rem;
    color: var(--accent-color);
    margin-top: 4px;
}

.info-item strong {
    display: block;
    font-size: 1rem;
    color: var(--text-dark);
    margin-bottom: 2px;
}

.info-item span {
    color: var(--text-muted);
    font-size: 1rem;
    line-height: 1.6;
}

/* ปุ่มกดด้านล่าง */
.action-footer {
    display: flex;
    gap: 20px;
}

/* ใช้สไตล์เดียวกับ .btn-primary ที่คุณมี */
.activity-info-fancy .btn-main {
    background: var(--primary-color);
    color: var(--white);
    padding: 14px 28px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
    text-align: center;
    flex: 1;
    box-shadow: 0 4px 15px rgba(0, 80, 123, 0.2);
}

.activity-info-fancy .btn-main:hover {
    background: #07164c;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 80, 123, 0.3);
}

/* ใช้สไตล์เดียวกับ .btn-secondary ที่คุณมี */
.activity-info-fancy .btn-outline {
    border: 2px solid var(--accent-color);
    color: var(--accent-color);
    background: transparent;
    padding: 12px 28px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
    text-align: center;
    flex: 1;
}

.activity-info-fancy .btn-outline:hover {
    background: var(--accent-color);
    color: var(--white);
    transform: translateY(-3px);
}

/* --- Responsive สำหรับ Card นี้ --- */
@media (max-width: 992px) {
    .activity-card-fancy {
        flex-direction: column;
        margin: 0 20px;
    }
    
    .activity-image {
        min-width: 100%;
        height: 300px;
    }
    
    .activity-info-fancy {
        padding: 40px 30px;
    }
}

@media (max-width: 600px) {
    .activity-info-fancy h3 {
        font-size: 1.8rem;
    }
    
    .action-footer {
        flex-direction: column;
        gap: 12px;
    }
}












/* 8. ฟุตเตอร์ */
footer.contact {
    background: #e9f2fc; 
    padding: 60px 0 30px; 
    border-top: 1px solid rgba(0, 0, 0, 0.03);
    color: #334155;
}

.footer-flex {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    max-width: 1000px; 
    margin: 0 auto;
    padding: 0 20px;
}

/* --- ส่วนของเมนู (ฝั่งซ้าย) --- */
.footer-left-col h3 {
    font-size: 0.95rem; 
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 20px;
    color: #1e293b;
    font-weight: 700;
}

/* ลบจุดหน้าเมนูแบบถอนรากถอนโคน */
footer.contact ul, 
footer.contact li {
    list-style: none !important;      /* ลบจุดลิสต์ */
    list-style-type: none !important; /* ย้ำประเภทลิสต์ว่าไม่มี */
    background-image: none !important; /* ลบกรณีที่ใช้รูปภาพแทนจุด */
    padding: 0 !important;            /* ลบช่องว่างที่จุดเคยอยู่ */
    margin: 0 !important;             /* ลบระยะขอบ */
    text-indent: 0 !important;        /* ลบการย่อหน้า */
}

/* ปรับระยะห่างระหว่างบรรทัดเมนูใหม่หลังจากล้างค่า margin/padding */
footer.contact .footer-left-col ul li {
    margin-bottom: 12px !important;   /* ให้กลับมาห่างกันพอดีๆ */
}

/* ลบเส้นใต้ลิงก์ทุกจุดใน Footer */
footer.contact a {
    text-decoration: none !important; /* ลบเส้นใต้ออกแบบเด็ดขาด */
    box-shadow: none !important; /* เผื่อบางธีมใช้ shadow แทนเส้นใต้ */
    color: #64748b;
    font-size: 0.9rem;
    transition: 0.3s;
}

footer.contact a:hover {
    color: #1e293b;
    text-decoration: none !important; /* มั่นใจว่า hover แล้วเส้นไม่โผล่ */
}

/* --- ส่วนของ Contact Card (ฝั่งขวา) --- */
.contact-card {
    background: #ffffff;
    padding: 20px 25px;
    border-radius: 12px; 
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02); 
    border: 1px solid rgba(0, 0, 0, 0.05);
    width: 100%;
    max-width: 320px; 
}

.contact-card h3 {
    font-size: 0.9rem;
    margin-bottom: 15px;
    color: #1e293b;
}

.contact-row {
    display: flex;
    align-items: center; 
    gap: 12px;
    margin-bottom: 12px;
}

.contact-row i {
    font-size: 0.9rem;
    color: #64748b; 
    width: 20px;
    text-align: center;
}

/* ปรับสี Facebook ให้เป็นโทนเดียวกัน */
.fa-facebook, .fa-facebook-f {
    color: #64748b !important; 
}

.contact-row p {
    font-size: 0.85rem; 
    line-height: 1.4;
    color: #64748b;
    margin: 0;
}

/* ส่วนลิขสิทธิ์ */
.footer-bottom {
    margin-top: 50px;
    padding: 25px 0;
    border-top: 1px solid rgba(0, 0, 0, 0.05); /* เส้นคั่นบางๆ แบบหรูๆ */
    text-align: center;
}

.footer-bottom p {
    font-size: 0.8rem;
    color: #94a3b8;
    letter-spacing: 0.5px;
    word-spacing: 1px;
}





/* มือถือ: จัดทุกอย่างอยู่ตรงกลาง */
@media (max-width: 768px) {
    .footer-flex {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .contact-card {
        text-align: left; /* ในการ์ดยังคงชิดซ้ายเพื่อความสวยงาม */
    }

    .footer-left-col {
        margin-bottom: 30px;
    }
}

/* Responsive: เมื่อจอมือถือเล็ก ให้กลับมาจัดตรงกลาง */
@media (max-width: 768px) {
    .footer-flex {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 40px;
    }

    .footer-right-col {
        text-align: center;
    }
}

/* ปรับให้ดูดีในมือถือ */
@media (max-width: 768px) {
    .footer-flex {
        flex-direction: column; /* เปลี่ยนเป็นแนวตั้ง */
        text-align: center;
    }

    .footer-left {
        max-width: 100%;
        margin-bottom: 20px;
    }

    .footer-right {
        justify-content: center; /* จัดกลางในมือถือ */
        gap: 30px;
        width: 100%;
    }
    
    .footer-desc {
        margin: 0 auto;
    }
}


/* 9. Responsive Strategy (การตอบสนองทุกอุปกรณ์) */
/* ปรับการแสดงผลให้สมบูรณ์แบบทั้งบนสมาร์ทโฟนและแท็บเล็ต */
@media (max-width: 768px) {
    .maincontent-con, .content-block-inner {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 40px;
    }
    .maincontent-info h1 { font-size: 2.4rem; }
    .hero-btns { justify-content: center; }
    .nav-menu { display: none; }
    .content-block-inner.reverse { direction: ltr; }
    .underline-left { margin: 10px auto 25px auto; }
}

/* จัดระเบียบ Grid 3 ช่อง */
.card-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    padding: 20px 0;
}

/* สไตล์กล่องข้อความ (Card) */
.news-card {
    background: #fff;
    border-radius: 20px; /* ขอบมนตามรูปตัวอย่าง */
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.news-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}

/* ส่วนเนื้อหาภายใน */
.card-content {
    padding: 25px;
    text-align: center; /* จัดตัวอักษรกึ่งกลางตามตัวอย่าง */
    flex-grow: 1;
}

.event-tag {
    font-size: 0.8rem;
    color: #555;
    font-weight: 600;
    margin-bottom: 10px;
    display: block;
}

.news-title {
    color: #b44b2b; /* สีน้ำตาลส้มตามตัวอย่าง */
    font-size: 1.25rem;
    font-weight: 800;
    margin-bottom: 15px;
    line-height: 1.4;
}

.news-detail {
    font-size: 0.9rem;
    color: #777;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 3; /* ตัดคำให้อยู่แค่ 3 บรรทัดเพื่อให้คาร์ดเท่ากัน */
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ส่วนของรูปภาพ (บังคับให้เท่ากัน) */
.news-image-wrapper {
    width: 100%;
    height: 220px; /* บังคับความสูงรูปภาพให้เท่ากันทุกใบ */
    overflow: hidden;
    padding: 0 15px 15px 15px; /* เว้นระยะขอบรอบรูปตามตัวอย่าง */
}

.activity-img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* รูปจะถูกตัดให้พอดีกับกรอบ ไม่บีบเบี้ยว */
    border-radius: 15px; /* มนเฉพาะรูปภาพ */
}

/* Responsive สำหรับมือถือ */
@media (max-width: 992px) {
    .card-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .card-grid { grid-template-columns: 1fr; }
}

.btn-more-news {
    display: inline-block;
    padding: 12px 35px;
    background-color: #bb7d21;
    color: white;
    text-decoration: none;
    border-radius: 30px;
    font-weight: 500;
    transition: 0.3s;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}
.btn-more-news:hover {
    background-color: #202002;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
}

</style>
<body>
    <?php include 'menu_volunteer.php'; ?>
   
    <main>
        <section class="maincontent">
            <div class="container">
                <div class="maincontent-con">
                    <div class="maincontent-info">
                        <span class="badge">โครงการอาสา</span>
                        <h1>เพื่อเด็กบนภู <br><span class="highlight">เพื่อนครูบนดอย</span></h1>
                        <p>มุ่งช่วยเหลือการศึกษาของเด็กในพื้นที่ห่างไกลที่ขาดแคลนทรัพยากร
                           เพื่อให้เด็กๆ ได้รับโอกาสทางการศึกษาอย่างเท่าเทียม
                           พร้อมสนับสนุนคุณครูผ่านการบริจาคสิ่งของจำเป็นและอุปกรณ์การเรียน
                        </p>
                        <div class="hero-btns">
                            <a href="https://www.facebook.com/profile.php?id=100069644543492&locale=th_TH" class="btn-primary" target="_blank">เยี่ยมชมเพจโครงการ</a>
                            <a href="donation.html" class="btn-secondary">รายการสิ่งของที่รับบริจาค</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


<section class="what-we-do">
    <div class="container">
        <div class="section-title">
            <h2>เราทำอะไรบ้าง</h2>
            <div class="underline"></div>
        </div>

        <div class="grid">
            <div class="card-grid">

            <?php
            $sql_pr = "SELECT pr.*, e.event_name 
                       FROM news_update pr
                       LEFT JOIN events e ON pr.event_id = e.event_id 
                       WHERE pr.status = 1 
                       ORDER BY pr.news_id DESC 
                       LIMIT 6";

            $result_pr = $conn->query($sql_pr);

            if ($result_pr && $result_pr->num_rows > 0) {
                while ($row = $result_pr->fetch_assoc()) {
                    $current_id = $row['news_id'];
            ?>

            <div class="card">

                <div class="image-side">
                    <div class="slider">

                        <?php
                        $sql_img = "SELECT image_path FROM news_images WHERE news_id = $current_id";
                        $result_img = $conn->query($sql_img);

                        if ($result_img && $result_img->num_rows > 0) {
                            while ($img_row = $result_img->fetch_assoc()) {
                        ?>

                        <img src="img/<?php echo htmlspecialchars($img_row['image_path']); ?>" alt="รูปกิจกรรม">

                        <?php
                            }
                        } else {
                        ?>
                        <img src="img/default.jpg">
                        <?php } ?>

                    </div>
                </div>

                <div class="card-content">

                    <span class="event-tag">
                        <i class="fas fa-tag"></i>
                        <?php echo !empty($row['event_name']) ? htmlspecialchars($row['event_name']) : 'กิจกรรมทั่วไป'; ?>
                    </span>

                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>

                    <p>
                        <?php echo mb_strimwidth(htmlspecialchars($row['detail']),0,100,"..."); ?>
                    </p>

                </div>

            </div>

            <?php
                }
            } else {
                echo "<p>ยังไม่มีข่าวประชาสัมพันธ์</p>";
            }
            ?>

            </div>
        </div>
<div style="text-align: center; margin-top: 40px;">
    <a href="all_news.php" class="btn-more-news">
        ดูข่าวประชาสัมพันธ์ทั้งหมด <i class="fas fa-chevron-right"></i>
    </a>
</div>
    </div>
</section>

<style>
   .slider{
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.slider img{
    width:100%;
    height:200px;
    object-fit:cover;
    position:absolute;
    opacity:0;
    transition:opacity 0.8s;
}

.slider img.active{
    opacity:1;
}

.gallery-slider{
    display:flex;
    overflow-x:auto;
    scroll-behavior:smooth;
    gap:0px;
}

.gallery-slider img{
    width:100%;
    height:260px;
    object-fit:cover;
    flex-shrink:0;
}

</style>

       <script>
document.querySelectorAll('.slider').forEach(slider => {

    let images = slider.querySelectorAll('img');
    let index = 0;

    if(images.length > 0){
        images[0].classList.add('active');

        setInterval(()=>{

            images[index].classList.remove('active');

            index++;
            if(index >= images.length){
                index = 0;
            }

            images[index].classList.add('active');

        },3000);

    }

});
</script>

</section>
<section class="upcoming-activity">
    <div class="container">
        <div class="section-title">
            <h2>กิจกรรมอาสาที่กำลังจะมาถึง</h2>
            <div class="underline"></div>
        </div>

        <div class="slider-wrapper">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php
                    // 1. ดึงข้อมูลกิจกรรม (ตรวจสอบให้มั่นใจว่าใช้ SELECT * เพื่อดึง event_image มาด้วย)
                    $sql_events = "SELECT * FROM events WHERE is_active = 1 ORDER BY event_date DESC LIMIT 5";
                    $result_events = $conn->query($sql_events);

                    if ($result_events && $result_events->num_rows > 0) {
                        while($row = $result_events->fetch_assoc()) {
                            // จัดการเรื่องเดือนภาษาไทย
                            $date_val = strtotime($row['event_date']);
                            $months_th = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
                            $month_label = $months_th[(int)date("m", $date_val)];
                            
                            // --- ส่วนที่แก้ไข: ดึงรูปภาพจากคอลัมน์ event_image ---
                            $event_img = "img/default-event.jpg"; // รูปมาตรฐานกรณีไม่มีรูปในระบบ
                            
                            if (!empty($row['event_image'])) {
                                $file_path = "img/" . $row['event_image'];
                                // ตรวจสอบว่าไฟล์มีอยู่ในโฟลเดอร์จริงๆ หรือไม่
                                if (file_exists($file_path)) {
                                    $event_img = $file_path; 
                                }
                            }
                            // ----------------------------------------------
                    ?>
                    <div class="swiper-slide">
                        <div class="activity-card-fancy">
                            <div class="activity-image">
                                <img src="<?php echo $event_img; ?>" alt="<?php echo htmlspecialchars($row['event_name']); ?>">
                                <div class="activity-status"><?php echo $month_label; ?></div>
                            </div>
                            <div class="activity-info-fancy">
                                <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                                
                                <p class="school-tagline">
                                    "<?php echo !empty($row['highlights']) ? htmlspecialchars($row['highlights']) : 'ร่วมสร้างรอยยิ้มให้กับเด็กบนดอย'; ?>"
                                </p>

                                <div class="info-grid">
                                    <div class="info-item">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <div>
                                            <strong>สถานที่</strong> 
                                            <span><?php echo htmlspecialchars($row['Location']); ?></span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="fa-solid fa-calendar-check"></i>
                                        <div>
                                            <strong>กำหนดการ</strong> 
                                            <span><?php echo htmlspecialchars($row['schedule_range']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="action-footer">
                                    <a href="events.php?id=<?php echo $row['event_id']; ?>" class="btn-main">เข้าร่วมกิจกรรม</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        }
                    } else {
                        echo "<div class='swiper-slide'><p style='text-align:center;'>ยังไม่มีกิจกรรมใหม่ในขณะนี้</p></div>";
                    }
                    ?>
                </div>

                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
</section>


        <section class="content">
            <div class="container">
                <div class="content-title">
                    <h3>บันทึกการเดินทางแห่งการให้</h3>
                </div>

                <div class="content-con">
                    <div class="content-item">
                        <img src="img/11.1.jpg" alt="สิ่งของที่ได้รับการสนับสนุน">
                        <div class="item-text">
                            <h4>น้ำใจที่ส่งถึง</h4>
                            <p>ทุกแรงแบ่งปัน ทั้งเครื่องนุ่งห่มและอุปกรณ์การเรียน สู่โอกาสทางการศึกษาและคุณภาพชีวิตที่ดีขึ้น ช่วยเติมเต็มความหวังให้กับเด็กๆ และคุณครูในพื้นที่ห่างไกล</p>
                            <a href="donated1.1.php" class="content-btn">ชมรูปภาพ</a>
                        </div>
                    </div>

                    <div class="content-item">
                        <img src="img/11.2.jpg" alt="กิจกรรมของโครงการ">
                        <div class="item-text">
                            <h4>ช่วงเวลาแห่งความสุข</h4>
                            <p>ส่งต่อความสุข สร้างเสริมประสบการณ์ มุ่งเน้นสร้างรอยยิ้มให้ชุมชนผ่านกิจกรรมสันทนาการที่หลากหลาย</p>
                            <a href="activity1.2.php" class="content-btn">ชมรูปภาพ</a>
                        </div>
                    </div>

                    <div class="content-item">
                        <img src="img/11.3.jpg" alt="ผลลัพธ์จากกิจกรรม">
                        <div class="item-text">
                            <h4>รอยยิ้มที่ยั่งยืน</h4>
                            <p>โรงเรียนได้รับการพัฒนา เด็กๆมีอุปกรณ์การเรียนที่เหมาะสม และชุมชนมีคุณภาพชีวิตที่ดีขึ้นอย่างเห็นได้ชัด</p>
                            <a href="result1.3.php" class="content-btn">ชมรูปภาพ</a>
                        </div>
                    </div>

                    <div class="content-item">
                        <img src="img/11.4.jpg" alt="การเดินทางไปบริจาค">
                        <div class="item-text">
                            <h4>เส้นทางแห่งการให้</h4>
                            <p>ทุกการเดินทางคือการฝ่าฟันอุปสรรค เพื่อส่งต่อความหวัง ความอบอุ่น และรอยยิ้มให้แก่เด็กๆ บนดอย</p>
                            <a href="travel1.4.php" class="content-btn">ชมรูปภาพ</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>


<section class="content-block">
    <div class="container-narrow">
        <div class="content-block-inner">
            <div class="text-side">
                <span class="tag">ประวัติของโครงการ</span>
                <h2>แนะนำ <span class="highlight">โครงการ</span></h2>
                <div class="underline-left"></div>
                <p>โครงการ <strong>"เพื่อเด็กบนภู เพื่อนครูบนดอย"</strong> ก่อตั้งขึ้นในปี พ.ศ. 2556 ด้วยความตั้งใจที่ต้องการสร้างความเปลี่ยนแปลงให้กับเด็กๆและครูในพื้นที่ห่างไกลบนภูเขาการได้เห็นความลำบากของเด็กๆที่ขาดแคลนโอกาสทางการศึกษาและทรัพยากรที่จำเป็นทำให้เราตัดสินใจเริ่มต้นโครงการนี้ขึ้น</p>
            </div>
                <div class="image-side">
                    <button class="nav-btn prev" onclick="sideScroll(this, 'prev')">❮</button>
                    <button class="nav-btn next" onclick="sideScroll(this, 'next')">❯</button>
                    <div class="gallery-slider">
                        <img src="img/8.1.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/8.2.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/8.3.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/8.4.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/8.5.jpg" alt="กิจกรรมโครงการ">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content-block bg-soft"> <div class="container-narrow">
        <div class="content-block-inner reverse">
            <div class="text-side">
                <span class="tag">บันทึกความทรงจำ</span>
                <h2>จุดเริ่มต้นของ <span class="highlight">โครงการ</span></h2>
                <div class="underline-left"></div>
                <p>จากการเดินทางขึ้นดอยครั้งแรกในปี 2556 ได้สัมผัสถึงความยากลำบากของครูและเด็กๆทั้งการขาดอุปกรณ์การเรียนการเดินทางที่ยากลำบากและสภาพความเป็นอยู่ที่ขาดแคลนสิ่งเหล่านี้กลายเป็นแรงผลักดันให้เราเริ่มต้นโครงการเล็กๆที่เต็มไปด้วยหัวใจ</p>
            </div>
                <div class="image-side">
                    <button class="nav-btn prev" onclick="sideScroll(this, 'prev')">❮</button>
                    <button class="nav-btn next" onclick="sideScroll(this, 'next')">❯</button>
                    <div class="gallery-slider">
                        <img src="img/9.1.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/9.2.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/9.3.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/9.4.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/9.5.jpg" alt="กิจกรรมโครงการ">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content-block">
    <div class="container-narrow">
        <div class="content-block-inner">
            <div class="text-side">
                <span class="tag">ทุกคนคือส่วนหนึ่งของการเปลี่ยนแปลง</span>
                <h2>ก้าวไปข้างหน้า <span class="highlight">ด้วยกัน</span></h2>
                <div class="underline-left"></div>
                <p>ตลอดเวลากว่า 13 ปีที่ผ่านมาโครงการได้เติบโตขึ้นด้วยความร่วมมือจากผู้คนมากมายทั้งอาสาสมัครผู้สนับสนุนและชุมชนท้องถิ่นเรามุ่งมั่นที่จะสร้างความเปลี่ยนแปลงที่ยั่งยืนต่อไปเพื่อให้เด็กๆและชาวบ้านทุกคนมีอนาคตที่สดใสและเพื่อสนับสนุนครูที่ทุ่มเทอยู่เคียงข้างพวกเขาเสมอ</p>
            </div>
                <div class="image-side">
                    <button class="nav-btn prev" onclick="sideScroll(this, 'prev')">❮</button>
                    <button class="nav-btn next" onclick="sideScroll(this, 'next')">❯</button>
                    <div class="gallery-slider">
                        <img src="img/10.1.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/10.2.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/10.3.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/10.4.jpg" alt="กิจกรรมโครงการ">
                        <img src="img/10.5.jpg" alt="กิจกรรมโครงการ">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
      const swiper = new Swiper('.swiper', {
        loop: true, // วนลูปกลับมาอันแรก
        spaceBetween: 30, // ระยะห่างระหว่างการ์ด
        centeredSlides: true,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        // ปรับแต่งการแสดงผลตามหน้าจอ
        breakpoints: {
          320: { slidesPerView: 1 },
          1024: { slidesPerView: 1 }
        }
      });
    </script> 

<script>
function sideScroll(element, direction) {
    // เข้าถึงตัว slider ที่อยู่ใน Card เดียวกับปุ่มที่กด
    const container = element.parentElement.querySelector('.gallery-slider');
    // คำนวณความกว้างของตัว Slider เพื่อให้เลื่อนไปทีละ 1 รูปเต็มๆ
    const scrollAmount = container.clientWidth;

    if (direction === 'prev') {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}
</script>

</body>
</html>
<?php include 'footer.php'; ?>
</body>
</html>



