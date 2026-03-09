<?php
// --- 1. Logic การบันทึกข้อมูล ---
ini_set('display_errors', 1); error_reporting(E_ALL);
$servername = "147.50.254.50"; $username = "admin_itpsru"; $password = "azdhhkVFWpWv7LBZqUju"; $dbname = "admin_itpsru";
$status = "";



// ถ้าผ่านเงื่อนไขด้านบน แสดงว่าคือ Admin... โค้ดข้างล่างนี้จะทำงานปกติ
$current_page = basename($_SERVER['PHP_SELF']);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

$items_query = "SELECT item_id, sub_category, item_name, unit FROM donation_items WHERE is_active = 1";
$items_result = $conn->query($items_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = trim($_POST['event_name'] ?? '');
    $event_date = trim($_POST['event_date'] ?? ''); 
    $event_time = trim($_POST['event_time'] ?? '');
    $venue      = trim($_POST['venue'] ?? ''); 
    $highlights = trim($_POST['highlights'] ?? ''); 
    $schedule_range = trim($_POST['schedule_range'] ?? '');

    // --- ส่วนที่เพิ่ม: จัดการไฟล์รูปภาพ ---
    $event_image = "";
    if (!empty($_FILES['event_image']['name'])) {
        $target_dir = "img/"; // ชื่อโฟลเดอร์ที่เก็บรูป (ต้องสร้างรอไว้)
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $file_extension = pathinfo($_FILES["event_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file)) {
            $event_image = $new_filename;
        }
    }

    $selected_items = $_POST['items'] ?? []; 
    $target_quantities = $_POST['targets'] ?? [];

    if (!empty($event_name) && !empty($event_date)) {
        $conn->begin_transaction();
        try {
            // แก้ไข SQL เพิ่มคอลัมน์ event_image (ตรวจสอบชื่อคอลัมน์ใน DB ให้ตรงกันด้วย)
            $sql = "INSERT INTO events (event_name, event_date, event_time, Location, highlights, schedule_range, event_image) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $event_name, $event_date, $event_time, $venue, $highlights, $schedule_range, $event_image);
            $stmt->execute();
            
            $new_event_id = $conn->insert_id;
            $stmt->close();

            if (!empty($selected_items)) {
                $sql_target = "INSERT INTO event_item_targets (event_id, item_id, target_quantity, current_received) VALUES (?, ?, ?, 0)";
                $stmt_target = $conn->prepare($sql_target);
                
                foreach ($selected_items as $item_id) {
                    $qty = (float)($target_quantities[$item_id] ?? 0);
                    if ($qty > 0) {
                        $stmt_target->bind_param("iid", $new_event_id, $item_id, $qty);
                        $stmt_target->execute();
                    }
                }
                $stmt_target->close();
            }

            $conn->commit();
            $status = "success";
        } catch (Exception $e) {
            $conn->rollback();
            $status = "error";
        }
    } else {
        $status = "missing";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างกิจกรรมใหม่ | Admin Hub</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@300;800&family=IBM+Plex+Sans+Thai:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --emerald: #10b981; 
            --dark-green: #064e3b;
            --slate: #64748b;
            --light-gray: #f8fafc;
            --white: #ffffff;
        }

        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
            background: #fbfcfb;
            color: var(--dark-green);
            margin: 0; padding: 0;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 60px 24px;
        }

        .header { margin-bottom: 40px; }
        .header p { 
            color: var(--emerald); font-weight: 800; text-transform: uppercase; 
            font-size: 0.85rem; margin-bottom: 8px; letter-spacing: 2px;
            font-family: 'Bricolage Grotesque', sans-serif;
        }
        .header h1 { 
            font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; margin: 0; 
            letter-spacing: -1.5px; font-family: 'Bricolage Grotesque', sans-serif;
        }
        .header h1 span { color: var(--emerald); }

        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
        }

        .form-card {
            background: var(--white);
            border-radius: 32px;
            padding: 40px;
            border: 1px solid #edf2f0;
            box-shadow: 0 15px 35px rgba(6, 78, 59, 0.03);
        }

        .input-group { margin-bottom: 20px; }
        .input-group label {
            display: block; font-weight: 600; font-size: 0.9rem;
            margin-bottom: 8px; color: var(--dark-green);
        }
        
        .input-group input, .input-group textarea {
            width: 100%; padding: 14px 18px; border-radius: 14px;
            border: 1.5px solid #eee; background: var(--light-gray);
            font-family: inherit; font-size: 0.95rem; transition: all 0.2s;
            box-sizing: border-box;
        }

        .input-group input:focus, .input-group textarea:focus {
            outline: none; border-color: var(--emerald);
            background: var(--white); box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.05);
        }

        .grid-inputs {
            display: grid; grid-template-columns: 1fr 1fr; gap: 15px;
        }

        .items-section {
            margin-top: 30px;
            border-top: 2px dashed #eee;
            padding-top: 20px;
        }
        .items-table-wrapper {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            margin-top: 10px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        .items-table th {
            background: #f8fafc;
            padding: 12px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .qty-input {
            width: 80px !important;
            padding: 8px !important;
            text-align: center;
        }

        .btn-save {
            width: 100%; padding: 18px; border-radius: 16px; border: none;
            background: var(--dark-green); color: var(--white); font-weight: 700;
            cursor: pointer; transition: all 0.3s ease; font-size: 1rem;
            margin-top: 20px;
        }
        .btn-save:hover { background: var(--emerald); transform: translateY(-2px); }

        .preview-box {
            background: #f0f4f2;
            padding: 25px;
            border-radius: 24px;
            margin-top: 20px;
        }
        .preview-box h4 { margin: 0 0 10px 0; font-size: 1rem; }
        .preview-tag {
            display: inline-block;
            background: var(--white);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 3px;
            border: 1px solid #e0eadd;
        }

        .next-icon {
            width: 50px; height: 50px; background-color: #f0f2f1;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; color: var(--dark-green);
            transition: 0.3s; margin: 40px auto 0;
        }
        .next-icon:hover { background: var(--dark-green); color: white; transform: translateX(-5px); }

        @media (max-width: 850px) {
            .admin-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include 'menu_admin.php'; ?>
<div class="container">
    <header class="header">
        
        <p>Admin Hub</p>
        <h1>เพิ่ม<span>กิจกรรม & คลังบริจาค.</span></h1>
    </header>

    <div class="admin-grid">
        <div class="admin-intro">
            <h2>รายละเอียดกิจกรรม</h2>
            <p>ระบุกิจกรรมเด่นๆ และเลือกสิ่งของจากคลังที่ต้องการรับบริจาคในรอบนี้</p>
            
            <div class="preview-box">
                <h4><i class="fa-solid fa-star" style="color: #fbbf24;"></i> ตัวอย่างไฮไลท์</h4>
                <div class="preview-tag">บอกรักแม่</div>
                <div class="preview-tag">นาทีทองแจกเสื้อผ้า</div>
                <div class="preview-tag">เกมมหาสนุก</div>
                <div class="preview-tag">เลี้ยงอาหารชาวบ้าน</div>
            </div>
        </div>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label>ชื่อกิจกรรมหลัก</label>
                    <input type="text" name="event_name" placeholder="เช่น อาสาครูดอยครั้งที่ 5" required>
                </div>

                <div class="input-group">
                    <label><i class="fa-regular fa-image"></i> รูปภาพปกกิจกรรม</label>
                    <input type="file" name="event_image" accept="image/*">
                </div>

                <div class="input-group">
                    <label>กำหนดการ (ช่วงวันที่)</label>
                    <input type="text" name="schedule_range" placeholder="เช่น 12 – 14 สิงหาคม 2569" required>
                </div>

                <div class="input-group">
                    <label>กิจกรรมไฮไลท์ (ใช้เครื่องหมายจุลภาค , คั่น)</label>
                    <textarea name="highlights" rows="2" placeholder="บอกรักแม่, เกมมหาสนุก"></textarea>
                </div>

                <div class="grid-inputs">
                    <div class="input-group">
                        <label>วันที่เริ่ม (ในระบบ)</label>
                        <input type="date" name="event_date" required>
                    </div>
                    <div class="input-group">
                        <label>สถานที่ (Location)</label>
                        <input type="text" name="venue" placeholder="ระบุสถานที่" required>
                    </div>
                </div>

                <div class="items-section">
                    <label style="font-weight: 700; color: var(--emerald);"><i class="fa-solid fa-boxes-stacked"></i> เลือกสิ่งของที่ต้องการรับบริจาค</label>
                    <div class="items-table-wrapper">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th width="50">เลือก</th>
                                    <th>รายการสิ่งของ</th>
                                    <th width="100">เป้าหมาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($items_result && $items_result->num_rows > 0): ?>
                                    <?php while($item = $items_result->fetch_assoc()): ?>
                                    <tr>
                                        <td align="center">
                                            <input type="checkbox" name="items[]" value="<?= $item['item_id'] ?>">
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($item['sub_category']) ?></strong><br>
                                            <small style="color: #64748b;"><?= htmlspecialchars($item['item_name']) ?></small>
                                        </td>
                                        <td>
                                            <input type="number" name="targets[<?= $item['item_id'] ?>]" class="qty-input" placeholder="0" min="0">
                                            <span style="font-size: 0.7rem; color: #94a3b8;"><?= htmlspecialchars($item['unit']) ?></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    บันทึกกิจกรรมและเปิดคลังบริจาค
                </button>
            </form>
        </div>
    </div>

    <a href="showuser.php" style="text-decoration: none;">
        <div class="next-icon">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if($status === "success"): ?>
        Swal.fire({ icon: 'success', title: 'บันทึกเรียบร้อย', text: 'กิจกรรมและเป้าหมายคลังถูกสร้างแล้ว', confirmButtonColor: '#10b981' });
    <?php elseif($status === "error"): ?>
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'กรุณาตรวจสอบคอลัมน์ event_image ในฐานข้อมูล' });
    <?php endif; ?>
</script>

</body>
</html>