<?php
// --- 1. การเชื่อมต่อและ Logic (คงเดิมแต่ปรับปรุงความปลอดภัยเล็กน้อย) ---
ini_set('display_errors', 1); error_reporting(E_ALL);
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_received'])) {
    $items_donated = $_POST['items'] ?? [];
    $conn->begin_transaction();
    try {
        foreach ($items_donated as $item_id => $qty) {
            $qty = (float)$qty;
            if ($qty > 0) {
                $sql1 = "UPDATE event_item_targets SET current_received = current_received + ? WHERE event_id = ? AND item_id = ?";
                $stmt1 = $conn->prepare($sql1);
                $stmt1->bind_param("dii", $qty, $event_id, $item_id);
                $stmt1->execute();

                $sql2 = "UPDATE donation_items SET total_received = total_received + ? WHERE item_id = ?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("di", $qty, $item_id);
                $stmt2->execute();
            }
        }
        $conn->commit();
        $status = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $status = "error";
    }
}

$items_data = [];
if ($event_id > 0) {
    $sql = "SELECT i.item_name, i.unit, i.category, t.target_quantity, t.current_received, t.item_id
            FROM event_item_targets AS t
            INNER JOIN donation_items AS i ON t.item_id = i.item_id
            WHERE t.event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) { $items_data[] = $row; }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกการรับบริจาค | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Anuphan:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-navy: #1c355e;
            --accent-blue: #3b82f6;
            --soft-blue: #eff6ff;
            --glass-white: rgba(255, 255, 255, 0.9);
            --navy-grad: linear-gradient(135deg, #1c355e 0%, #3b82f6 100%);
            
        }

        body { 
            font-family: 'Anuphan', sans-serif; 
            background: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, #e0f2fe 0, transparent 50%), 
                radial-gradient(at 100% 100%, #f1f5f9 0, transparent 50%);
            min-height: 100vh;
            color: #1e293b;
        }

        .header-section {
            background: white;
            padding: 30px 0;
            margin-bottom: 40px;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .item-tile { 
            background: var(--glass-white); 
            border-radius: 24px;
            border: 1px solid #ffffff !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden;
            height: 100%;
        }
        
        .item-tile:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 25px -5px rgba(28, 53, 94, 0.1) !important;
            border-color: var(--accent-blue) !important;
        }

        /* ปรับปรุง Progress Bar จากเขียวเป็นน้ำเงิน */
        .progress { 
            height: 10px !important; 
            background: #e2e8f0; 
            border-radius: 20px; 
            overflow: hidden; 
        }
        .progress-bar { 
            background: var(--navy-grad); 
            border-radius: 20px;
        }

        /* Badge หมวดหมู่ */
        .category-badge {
            background: var(--soft-blue);
            color: var(--accent-blue);
            border: 1px solid #dbeafe;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 8px;
        }

        /* ส่วนกรอกตัวเลข */
        .qty-box {
            background: #f1f5f9;
            border-radius: 16px;
            padding: 8px;
            border: 1px solid #e2e8f0;
        }

        .btn-qty {
            width: 38px; height: 38px;
            background: white;
            border: none;
            border-radius: 12px;
            color: var(--primary-navy);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: 0.2s;
        }
        .btn-qty:hover {
            background: var(--primary-navy);
            color: white;
        }

        .qty-input {
            font-size: 1.25rem;
            color: var(--primary-navy);
            font-weight: 700;
            width: 70px !important;
        }

        /* ปุ่มยืนยัน */
        .btn-submit-main {
            background: var(--navy-grad);
            border: none;
            padding: 16px 48px;
            border-radius: 20px;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 10px 20px rgba(28, 53, 94, 0.2);
            transition: 0.3s;
        }
        .btn-submit-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(28, 53, 94, 0.3);
            filter: brightness(1.1);
        }
        
    </style>
</head>
<body>



<div class="container pb-5">
    <?php if (!empty($items_data)): ?>
        <form method="POST">
            <div class="row g-4">
                <?php foreach ($items_data as $row): 
                    $percent = ($row['target_quantity'] > 0) ? ($row['current_received'] / $row['target_quantity']) * 100 : 0;
                    $percent = min(100, round($percent));
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="item-tile p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="category-badge mb-2 d-inline-block text-uppercase"><?= htmlspecialchars($row['category']) ?></span>
                                <h5 class="fw-700 mb-0 text-dark"><?= htmlspecialchars($row['item_name']) ?></h5>
                            </div>
                            
                        </div>
                        
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%"></div>
                        </div>
                        
                        <div class="row g-0 mb-4 py-2 border-top border-bottom border-light">
                            <div class="col-6 border-end text-center">
                                <span class="d-block small text-muted">เป้าหมาย</span>
                                <span class="fw-bold text-dark"><?= number_format($row['target_quantity']) ?></span> <small><?= $row['unit'] ?></small>
                            </div>
                            <div class="col-6 text-center">
                                <span class="d-block small text-muted">รับแล้ว</span>
                                <span class="fw-bold" style="color: var(--primary-navy);"><?= number_format($row['current_received']) ?></span> <small><?= $row['unit'] ?></small>
                            </div>
                        </div>

                        <div class="qty-box d-flex align-items-center justify-content-between">
                            <button type="button" class="btn-qty minus"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" name="items[<?= $row['item_id'] ?>]" 
                                   class="form-control border-0 bg-transparent text-center qty-input" 
                                   value="0" min="0">
                            <button type="button" class="btn-qty plus"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            
        </form>
    <?php else: ?>
        <div class="card border-0 rounded-4 shadow-sm p-5 text-center">
            <div class="mb-4">
                <i class="fa-solid fa-folder-open fa-4x text-light"></i>
            </div>
            <h4 class="fw-bold text-secondary">ไม่พบรายการเป้าหมาย</h4>
            <p class="text-muted">กรุณาเลือกกิจกรรมที่มีการกำหนดรายการสิ่งของไว้แล้ว</p>
            <div class="mt-3">
                <a href="manage_events.php" class="btn btn-primary rounded-pill px-4">ไปที่หน้าจัดการกิจกรรม</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ระบบปุ่มบวก ลบ (ปรับปรุงให้รองรับปุ่มได้ดีขึ้น)
    document.querySelectorAll('.plus').forEach(btn => {
        btn.onclick = function() {
            let input = this.parentElement.querySelector('input');
            input.value = parseInt(input.value) + 1;
        }
    });
    document.querySelectorAll('.minus').forEach(btn => {
        btn.onclick = function() {
            let input = this.parentElement.querySelector('input');
            if(parseInt(input.value) > 0) input.value = parseInt(input.value) - 1;
        }
    });

    // แจ้งเตือนสถานะ
    <?php if($status === "success"): ?>
        Swal.fire({ 
            icon: 'success', 
            title: 'สำเร็จ!', 
            text: 'ยอดบริจาคถูกเพิ่มเข้ากิจกรรมและคลังกลางเรียบร้อย', 
            confirmButtonColor: '#1c355e',
            borderRadius: '20px'
        }).then(() => { window.location.reload(); });
    <?php elseif($status === "error"): ?>
        Swal.fire({ 
            icon: 'error', 
            title: 'เกิดข้อผิดพลาด', 
            text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่',
            confirmButtonColor: '#1c355e'
        });
    <?php endif; ?>
</script>

</body>
</html>