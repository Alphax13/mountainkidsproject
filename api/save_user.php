<?php
require 'auth.php';
checkRole(['admin']);

// 1. เชื่อมต่อฐานข้อมูล
$servername = "147.50.254.50";
$username = "admin_itpsru";
$password = "azdhhkVFWpWv7LBZqUju";
$dbname = "admin_itpsru";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// 2. รับค่าจาก Method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าและตัดช่องว่างซ้าย-ขวา
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $province = trim($_POST['province']);
    $address = trim($_POST['address']);
    $role = $_POST['role'];
    
    // เข้ารหัสรหัสผ่าน
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // [เพิ่มเติม] ตรวจสอบค่าว่างพื้นฐาน
    if (empty($user) || empty($email) || empty($_POST['password'])) {
        header("Location: add_user.php?status=empty");
        exit();
    }

    // 3. ตรวจสอบว่า Username หรือ Email ซ้ำไหม
    $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ss", $user, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        header("Location: add_user.php?status=duplicate");
        exit();
    }

    // 4. บันทึกข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO users (username, password, email, first_name, last_name, phone, province, address, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $user, $pass, $email, $fname, $lname, $phone, $province, $address, $role);

    if ($stmt->execute()) {
        // *** แก้ไขจุดนี้: ส่งกลับไปหน้า showuser พร้อม status=success ***
        header("Location: showuser.php?status=success");
    } else {
        header("Location: add_user.php?status=error");
    }

    $stmt->close();
    $stmt_check->close();
} else {
    header("Location: add_user.php");
}

$conn->close();
?>