<?php
session_start();
// 1. เชื่อมต่อฐานข้อมูล
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
$conn->set_charset("utf8mb4");

// ตรวจสอบว่ามี Session ของ Admin หรือไม่
// แก้ไข 'role' และ 'admin' ให้ตรงกับที่คุณเก็บใน Database/ตอน Login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // ถ้าไม่มีสิทธิ์ ให้ส่งไปหน้า Login พร้อมส่งข้อความแจ้งเตือน (ถ้ามี)
    header("Location: login.php?error=no_permission");
    exit();
}

// ถ้าผ่านเงื่อนไขด้านบน แสดงว่าคือ Admin... โค้ดข้างล่างนี้จะทำงานปกติ
$current_page = basename($_SERVER['PHP_SELF']);

// ฟังก์ชันสำหรับแปลงวันที่ (เหมือนเดิม)
function formatThaiDateShort($dateString) {
    if (!$dateString || $dateString == '0000-00-00') return "";
    $months = [
        1 => "ม.ค.", 2 => "ก.พ.", 3 => "มี.ค.", 4 => "เม.ย.", 5 => "พ.ค.", 6 => "มิ.ย.",
        7 => "ก.ค.", 8 => "ส.ค.", 9 => "ก.ย.", 10 => "ต.ค.", 11 => "พ.ย.", 12 => "ธ.ค."
    ];
    $time = strtotime($dateString);
    if (!$time) return "";
    $d = date('j', $time);
    $m = $months[(int)date('n', $time)];
    $y = date('Y', $time) + 543;
    return "[$d $m $y]";
}

// รับค่าการคัดกรองจาก URL
$current_event = isset($_GET['event']) ? $_GET['event'] : 'all';

// 2. ดึงข้อมูลสถิติสำหรับ Cards
$where_stat = "WHERE d.status = 'Approved'";
if ($current_event !== 'all') {
    $where_stat .= " AND e.event_name = '" . $conn->real_escape_string($current_event) . "'";
}

$stat_users = $conn->query("SELECT COUNT(DISTINCT d.user_id) as total FROM donations d LEFT JOIN events e ON d.event_id = e.event_id $where_stat")->fetch_assoc();
$stat_events = $conn->query("SELECT COUNT(*) as total FROM events")->fetch_assoc();
$last_update = date('H:i');

// 3. ดึงรายชื่อกิจกรรมทั้งหมด (เหมือนเดิม)
$event_list_res = $conn->query("SELECT * FROM events GROUP BY event_name ORDER BY event_id DESC");

// 4. *** จุดที่แก้ไข: ปรับ SQL สำหรับโครงสร้างตารางใหม่ ***
// ตัด LEFT JOIN donation_details dd ออก และดึง item_id/quantity จาก d ได้เลย
$sql = "SELECT d.*, u.first_name, u.last_name, u.profile_image_path, 
                COALESCE(e.event_name, 'คลังส่วนกลาง') AS target_name,
                di.item_name,
                CONCAT(di.item_name, ' (', d.quantity, ')') as items_list
         FROM donations d 
         JOIN users u ON d.user_id = u.id 
         LEFT JOIN events e ON d.event_id = e.event_id 
         LEFT JOIN donation_items di ON d.item_id = di.item_id";

if ($current_event !== 'all') {
    $sql .= " WHERE e.event_name = '" . $conn->real_escape_string($current_event) . "'";
}

// เนื่องจาก 1 แถวคือ 1 ไอเทมอยู่แล้ว เราไม่จำเป็นต้อง GROUP_CONCAT ยกเว้นคุณอยากจะรวม 
// แต่ตามโครงสร้างใหม่ที่อาจารย์ให้ทำ แนะนำให้โชว์แยกบรรทัดจะแม่นยำที่สุดครับ
$sql .= " ORDER BY d.donation_date DESC"; 
$result = $conn->query($sql);

function get_img($path) {
    return (!empty($path) && file_exists($path)) ? $path : "https://ui-avatars.com/api/?name=User&background=d1fae5&color=065f46";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการบริจาค | Smart Donation</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Anuphan:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --sidebar-width: 280px; --primary-color: #059669; --bg-body: #f8fafc; --dark-navy: #1e293b; }
        body { font-family: 'Anuphan', sans-serif; background-color: var(--bg-body); margin: 0; }
        .wrapper { display: flex; }
        #sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; left: 0; top: 0; background: #fff; border-right: 1px solid #e2e8f0; z-index: 1000; }
        .main-content { flex: 1; margin-left: var(--sidebar-width); padding: 2.5rem; min-width: 0; }
        
        .filter-section { background: #fff; padding: 1.5rem; border-radius: 15px; border: 1px solid #e2e8f0; margin-bottom: 2rem; }
        .select2-container--default .select2-selection--single { height: 45px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; align-items: center; }
        
        .stat-card { background: #fff; border-radius: 15px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; height: 100%; }
        .icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .report-card { background: #fff; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; }
        .table thead th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; padding: 1.25rem; border: none; }
        .table tbody td { padding: 1.25rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .item-tag { background: #f0fdf4; color: #166534; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500; display: inline-block; margin: 2px; border: 1px solid #dcfce7; }
        .proof-thumb { width: 45px; height: 45px; border-radius: 8px; object-fit: cover; cursor: pointer; transition: 0.2s; }
        .proof-thumb:hover { transform: scale(1.1); }
        .status-pill { padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
    </style>
</head>
<body>

<div class="wrapper">
    <div id="sidebar">
        <?php if(file_exists("menu_admin.php")) include "menu_admin.php"; ?>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="fw-bold mb-1">รายงานกิจกรรมการบริจาค</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active">รายงานการบริจาค</li>
                    </ol>
                </nav>
            </div>
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fas fa-print me-2"></i> พิมพ์รายงาน
            </button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-success bg-opacity-10 text-success"><i class="fas fa-user-check"></i></div>
                    <div>
                        <div class="text-muted small">ผู้บริจาค (ในกลุ่มนี้)</div>
                        <div class="h4 fw-bold mb-0"><?= number_format($stat_users['total'] ?? 0) ?> <small class="fw-normal fs-6">คน</small></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="fas fa-tasks"></i></div>
                    <div>
                        <div class="text-muted small">จำนวนโครงการทั้งหมด</div>
                        <div class="h4 fw-bold mb-0"><?= number_format($stat_events['total'] ?? 0) ?> <small class="fw-normal fs-6">กิจกรรม</small></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="fas fa-sync-alt"></i></div>
                    <div>
                        <div class="text-muted small">ข้อมูลล่าสุด ณ เวลา</div>
                        <div class="h4 fw-bold mb-0"><?= $last_update ?> <small class="fw-normal fs-6">น.</small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-section shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <label class="fw-bold text-dark"><i class="fas fa-filter me-2"></i>เลือกกิจกรรม:</label>
                </div>
                <div class="col-md-6">
                    <select id="eventFilter" class="form-select select2-basic">
                        <option value="all" <?= ($current_event == 'all') ? 'selected' : '' ?>>--- แสดงกิจกรรมและโครงการทั้งหมด ---</option>
                        <?php 
                        if($event_list_res && $event_list_res->num_rows > 0):
                            $event_list_res->data_seek(0);
                            while($ev = $event_list_res->fetch_assoc()): 
                                // พยายามหาคอลัมน์วันที่ ไม่ว่าชื่อ start_date หรือ event_date
                                $raw_date = $ev['start_date'] ?? $ev['event_date'] ?? null;
                                $date_label = $raw_date ? formatThaiDateShort($raw_date) . " - " : "";
                                $display_text = $date_label . $ev['event_name'];
                        ?>
                                <option value="<?= urlencode($ev['event_name']) ?>" <?= ($current_event == $ev['event_name']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($display_text) ?>
                                </option>
                        <?php 
                            endwhile; 
                        endif;
                        ?>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark border p-2 px-3 rounded-pill">
                        พบข้อมูล: <strong><?= $result ? $result->num_rows : 0 ?></strong> รายการ
                    </span>
                </div>
            </div>
        </div>

        <div class="report-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>อาสาสมัคร</th>
                            <th width="30%">รายการสิ่งของ</th>
                            <th>เป้าหมายกิจกรรม</th>
                            <th>วันที่ / เวลา</th>
                            <th>หลักฐาน</th>
                            <th>สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= get_img($row['profile_image_path']) ?>" class="rounded-circle me-3 border" width="40" height="40">
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">ID: #<?= $row['user_id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        if($row['items_list']) {
                                            $items = explode(', ', $row['items_list']);
                                            foreach($items as $item) echo '<span class="item-tag">' . htmlspecialchars($item) . '</span>';
                                        } else {
                                            echo '<span class="text-muted italic">ไม่มีข้อมูล</span>';
                                        }
                                    ?>
                                </td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2"><?= htmlspecialchars($row['target_name']) ?></span></td>
                                <td>
                                    <div class="fw-bold"><?= date('d/m/Y', strtotime($row['donation_date'])) ?></div>
                                    <div class="text-muted small"><?= date('H:i', strtotime($row['donation_date'])) ?> น.</div>
                                </td>
                                <td>
                                    <?php if($row['image_path_1']): ?>
                                        <img src="<?= $row['image_path_1'] ?>" class="proof-thumb border" onclick="viewImg(this.src)">
                                    <?php else: ?>
                                        <span class="text-muted small">ไม่มีรูป</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-pill <?= ($row['status'] == 'Pending') ? 'bg-warning text-dark' : 'bg-success text-white' ?> shadow-sm">
                                        <i class="fas <?= ($row['status'] == 'Pending') ? 'fa-hourglass-start' : 'fa-check-circle' ?> me-1"></i>
                                        <?= ($row['status'] == 'Pending') ? 'รอยืนยัน' : 'อนุมัติแล้ว' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($row['status'] == 'Pending'): ?>
                                        <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm hover-scale" 
                                                onclick="manage(<?= $row['donation_id'] ?>, 'Approved')">
                                            <i class="fas fa-check me-1"></i> อนุมัติ
                                        </button>
                                    <?php else: ?>
                                        <span class="text-success small fw-bold">ยืนยันเสร็จสิ้น</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50">
                                    <p class="text-muted">ไม่พบข้อมูลการบริจาคในกิจกรรมนี้</p>
                                    <a href="?event=all" class="btn btn-outline-primary btn-sm rounded-pill">ดูทั้งหมด</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('.select2-basic').select2({
            placeholder: "ค้นหาชื่อกิจกรรม...",
            allowClear: true,
            width: '100%'
        });

        $('#eventFilter').on('change', function() {
            const val = $(this).val();
            window.location.href = `?event=${val}`;
        });
    });

    function viewImg(url) {
        Swal.fire({ 
            imageUrl: url, 
            imageAlt: 'หลักฐานการบริจาค', 
            showConfirmButton: false, 
            background: '#fff', 
            backdrop: `rgba(0,0,0,0.85)`,
            showCloseButton: true
        });
    }

    function manage(id, status) {
        Swal.fire({
            title: 'ยืนยันการตรวจสอบ?',
            text: "เมื่ออนุมัติแล้ว ยอดสิ่งของจะถูกนำไปคำนวณในเป้าหมายกิจกรรมทันที",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ยืนยันอนุมัติ',
            cancelButtonText: 'ยกเลิก'
        }).then((res) => {
            if (res.isConfirmed) {
                Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                window.location.href = `update_status.php?id=${id}&status=${status}`;
            }
        });
    }
</script>
</body>
</html>