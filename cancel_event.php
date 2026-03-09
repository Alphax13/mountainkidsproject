<?php
// ไฟล์: cancel_event.php
session_start();
ob_clean(); // ล้าง output ที่อาจค้างอยู่
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['event_id'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
$user_id = (int)$_SESSION['user_id'];
$event_id = (int)$_POST['event_id'];

// ลบออกจากตาราง join_event
$stmt = $conn->prepare("DELETE FROM join_event WHERE user_id = ? AND event_id = ?");
$stmt->bind_param("ii", $user_id, $event_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลได้']);
}
$stmt->close();
$conn->close();