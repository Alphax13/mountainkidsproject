<?php


function checkRole($roles = []) {

    // ยังไม่ได้ล็อกอิน
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // ไม่มีสิทธิ์
    if (!in_array($_SESSION['role'], $roles)) {
        echo "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
        exit();
    }
}
?>
