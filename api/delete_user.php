<?php
// ---------------- ข้อมูลการเชื่อมต่อฐานข้อมูล ----------------
$servername = "147.50.254.50";
$username = "admin_itpsru";
$password = "azdhhkVFWpWv7LBZqUju";
$dbname = "admin_itpsru";

// ---------------- การสร้าง Object และเชื่อมต่อฐานข้อมูล ----------------
$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ปรับเป็น utf8mb4 เพื่อรองรับภาษาไทยที่สมบูรณ์ขึ้น
$conn->set_charset("utf8mb4");

// ---------------- ตรวจสอบและดำเนินการลบ ----------------
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // คำสั่ง SQL DELETE (คงเดิมเพราะปลอดภัยอยู่แล้ว)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id); 
    
    if ($stmt->execute()) {
        // --- สิ่งที่ปรับเพิ่ม ---
        // ส่งค่า status=success กลับไปที่หน้าหลัก (สมมติว่าหน้าหลักคือ manage_users.php หรือ index.php)
        // เพื่อให้หน้าโน้นใช้ SweetAlert โชว์ว่าลบสำเร็จแล้ว
        header("Location: showuser.php?status=success"); 
        exit();
    } else {
        // กรณีลบไม่สำเร็จ ส่งค่า error กลับไป
        header("Location: showuser.php?status=error");
        exit();
    }

    $stmt->close();
} else {
    // กรณี ID ไม่ถูกต้อง
    header("Location: showuser.php?status=invalid");
    exit();
}

$conn->close();
?>