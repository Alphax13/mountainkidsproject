<?php
session_start();
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
$conn->set_charset("utf8mb4");

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];

    $allowed_status = ['Approved', 'Rejected'];
    if (!in_array($status, $allowed_status)) {
        header("Location: admin_history.php?msg=invalid_status");
        exit();
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE donation_id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();

        $conn->commit();
        
        // แก้ไขชื่อไฟล์ให้เป็น admin_history.php และลบตัว "a" ส่วนเกินออก
        header("Location: admin_history.php?msg=success");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_history.php?msg=error");
        exit();
    }
} else {
    header("Location: admin_history.php");
    exit();
}

?>