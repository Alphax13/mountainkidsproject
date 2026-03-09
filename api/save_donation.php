<?php
header('Content-Type: application/json');
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

// 1. จัดการรูปภาพ
$image_path_1 = "";
if (isset($_FILES['donation_image_1']) && $_FILES['donation_image_1']['error'] == 0) {
    $dir = "uploads/donations/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $filename = time() . "_" . $_FILES['donation_image_1']['name'];
    move_uploaded_file($_FILES['donation_image_1']['tmp_name'], $dir . $filename);
    $image_path_1 = $dir . $filename;
}

// 2. รับค่าพื้นฐาน
$user_id = (int)$_POST['user_id'];
$event_id = (int)$_POST['event_id'];
$items = $_POST['items'] ?? [];
$status = "Pending"; 

$conn->begin_transaction();
try {
    // *** จุดที่แก้ไขใหญ่: เตรียม Statement สำหรับบันทึกลงตาราง donations โดยตรง ***
    // เพิ่ม item_id และ quantity เข้าไปในคำสั่ง INSERT หลัก
    $stmt_donation = $conn->prepare("INSERT INTO donations (user_id, event_id, item_id, quantity, image_path_1, status) VALUES (?, ?, ?, ?, ?, ?)");
    
    // เตรียม Statement สำหรับอัปเดตยอดสะสมแยกตามกิจกรรม (เหมือนเดิม)
    $stmt_update = $conn->prepare("UPDATE event_item_targets SET current_received = current_received + ? WHERE event_id = ? AND item_id = ?");

    foreach ($items as $item_id => $qty) {
        $qty = (int)$qty;
        if ($qty > 0) {
            $item_id = (int)$item_id;
            
            // บันทึกข้อมูลการบริจาค (1 แถวต่อ 1 ไอเทม ตามโครงสร้างที่รวมตารางแล้ว)
            $stmt_donation->bind_param("iiiiss", $user_id, $event_id, $item_id, $qty, $image_path_1, $status);
            $stmt_donation->execute();

            // อัปเดตยอดที่ได้รับจริงเข้าไปในตารางเป้าหมายของกิจกรรม
            $stmt_update->bind_param("iii", $qty, $event_id, $item_id);
            $stmt_update->execute();
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => '✅ บันทึกข้อมูลลงตาราง Donations และอัปเดตยอดกิจกรรมสำเร็จ!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => '❌ ผิดพลาด: ' . $e->getMessage()]);
}
?>