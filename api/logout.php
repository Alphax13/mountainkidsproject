<?php
// 1. เริ่มเซสชัน (ต้องเรียก session_start() ก่อนการทำงานใดๆ กับ Session)
session_start();

// 2. ลบตัวแปรทั้งหมดใน Session (เป็นขั้นตอนที่ดีเพื่อความสะอาด)
$_SESSION = array();

// 3. ถ้ามีการใช้คุกกี้ Session (ซึ่งปกติแล้วจะใช้) ให้ทำลายคุกกี้นั้นด้วย
// Note: การใช้ฟังก์ชัน session_name() จะดึงชื่อคุกกี้ Session ที่ตั้งไว้
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. ทำลายเซสชันทั้งหมดอย่างสมบูรณ์
session_destroy();

// 5. ส่งผู้ใช้กลับไปยังหน้าล็อกอิน (สมมติชื่อไฟล์คือ login.php)
header("Location: login.php");
exit;
?>
