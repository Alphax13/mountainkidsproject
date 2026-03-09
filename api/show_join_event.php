<?php
session_start();

// 1. ตรวจสอบสิทธิ์ผู้ดูแลระบบ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?access_denied=1");
    exit();
}

// 2. เชื่อมต่อฐานข้อมูล
$servername = "147.50.254.50"; $username = "admin_itpsru"; $password = "azdhhkVFWpWv7LBZqUju"; $dbname = "admin_itpsru";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Database Connection Failed");
$conn->set_charset("utf8mb4");

// ฟังก์ชันแปลงวันที่เป็นภาษาไทย (ย่อ)
function thai_date_short($date) {
    $months = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
    $d = date("j", strtotime($date));
    $m = $months[date("n", strtotime($date))];
    $y = date("Y", strtotime($date)) + 543;
    return "$d $m $y";
}

// รับค่า Filter
$selected_event = isset($_GET['event_id']) ? $_GET['event_id'] : 'all';

// ดึงรายชื่อกิจกรรมทั้งหมด
$sql_events = "SELECT event_id, event_name, event_date FROM events ORDER BY event_date DESC";
$res_events = $conn->query($sql_events);
$events_list = [];
while ($row = $res_events->fetch_assoc()) { $events_list[] = $row; }

// ดึงข้อมูลรายงาน
$where_clause = ($selected_event !== 'all') ? " AND T2.event_id = " . intval($selected_event) : "";
$sql = "
    SELECT T1.first_name, T1.last_name, T1.id AS user_pk_id, T1.profile_image_path,
           T3.event_name, T3.event_date, T2.confirmed_at
    FROM users AS T1
    INNER JOIN join_event AS T2 ON T1.id = T2.user_id 
    INNER JOIN events AS T3 ON T2.event_id = T3.event_id
    WHERE T2.status = 1 $where_clause
    ORDER BY T3.event_date DESC, T2.confirmed_at DESC
";
$result = $conn->query($sql);
$join_event_records = [];
if($result) {
    while ($row = $result->fetch_assoc()) { $join_event_records[] = $row; }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการเข้าร่วม | ระบบจัดการอาสาสมัคร</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body { font-family: 'IBM Plex Sans Thai', sans-serif; background: #f8fafc; color: #334155; }
        .dashboard-main { margin-left: 280px; padding: 40px; transition: 0.3s; }
        @media (max-width: 1024px) { .dashboard-main { margin-left: 0; padding: 20px; } }

        .stat-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; }
        .table-container { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; }
        .custom-table th { background: #f1f5f9; padding: 16px 20px; font-size: 13px; color: #475569; text-transform: uppercase; }
        .custom-table td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; }
        .avatar-box { width: 45px; height: 45px; border-radius: 12px; object-fit: cover; }
        
        .select2-container--default .select2-selection--single {
            height: 50px; border-radius: 12px; border-color: #e2e8f0; display: flex; align-items: center; padding-left: 10px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 48px; }
        
        @media print {
            #sidebar, .no-print { display: none !important; }
            .dashboard-main { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <?php include 'menu_admin.php'; ?>
    </div>

    <div class="dashboard-main">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight">รายงาน <span class="text-emerald-500">การเข้าร่วมกิจกรรม</span></h2>
                <div class="flex items-center gap-2 text-slate-400 mt-1">
                    <i class="far fa-clock"></i>
                    <span>ข้อมูลล่าสุด ณ วันที่ <?php echo thai_date_short(date('Y-m-d')); ?> เวลา <?php echo date('H:i'); ?> น.</span>
                </div>
            </div>
            <button onclick="window.print()" class="no-print bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl font-medium transition shadow-lg flex items-center gap-2">
                <i class="fas fa-print"></i> พิมพ์รายงานสรุป
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card flex items-center gap-5">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm"><i class="fas fa-id-card-alt"></i></div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">ยืนยันตัวตนแล้ว</p>
                    <p class="text-2xl font-black text-slate-800"><?php echo number_format(count($join_event_records)); ?> <span class="text-sm font-medium">คน</span></p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-5">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm"><i class="fas fa-layer-group"></i></div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">โครงการทั้งหมด</p>
                    <p class="text-2xl font-black text-slate-800"><?php echo number_format(count($events_list)); ?> <span class="text-sm font-medium">งาน</span></p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-5">
                <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm"><i class="fas fa-calendar-day"></i></div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">อัปเดตระบบ</p>
                    <p class="text-xl font-black text-slate-800"><?php echo thai_date_short(date('Y-m-d')); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm mb-8 no-print">
            <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-500 w-2 h-8 rounded-full"></div>
                    <span class="text-slate-700 font-bold text-lg">คัดกรองตามโครงการ</span>
                </div>
                <div class="flex-grow">
                    <select id="eventSelector" class="w-full">
                        <option value="all" <?php echo ($selected_event == 'all') ? 'selected' : ''; ?>>--- แสดงกิจกรรมและโครงการทั้งหมด ---</option>
                        <?php foreach ($events_list as $ev): ?>
                            <option value="<?php echo $ev['event_id']; ?>" <?php echo ($selected_event == $ev['event_id']) ? 'selected' : ''; ?>>
                                [<?php echo thai_date_short($ev['event_date']); ?>] - <?php echo htmlspecialchars($ev['event_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="overflow-x-auto">
                <table class="w-full custom-table text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="w-1/4">อาสาสมัคร</th>
                            <th class="w-1/3">ข้อมูลกิจกรรม</th>
                            <th>วันเวลาที่บันทึกระบบ</th>
                            <th class="text-center">ตรวจสอบ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (count($join_event_records) > 0): ?>
                            <?php foreach ($join_event_records as $record): 
                                $profile_img = $record['profile_image_path'] ?: 'uploads/profiles/default.png';
                                if (!file_exists($profile_img) || is_dir($profile_img)) $profile_img = 'https://ui-avatars.com/api/?name='.urlencode($record['first_name']).'&background=d1fae5&color=065f46';
                            ?>
                            <tr class="hover:bg-slate-50/80 transition group">
                                <td>
                                    <div class="flex items-center gap-4">
                                        <img src="<?php echo $profile_img; ?>" class="avatar-box shadow-sm group-hover:scale-110 transition duration-300">
                                        <div>
                                            <div class="font-bold text-slate-800"><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></div>
                                            <div class="text-[10px] bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full inline-block uppercase mt-1">จิตอาสา</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-700"><?php echo htmlspecialchars($record['event_name']); ?></div>
                                    <div class="flex items-center gap-2 text-xs text-slate-400 mt-1">
                                        <i class="far fa-calendar-alt text-emerald-500"></i>
                                        <span>จัดขึ้นเมื่อ: <?php echo thai_date_short($record['event_date']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-black text-slate-800"><?php echo date('H:i', strtotime($record['confirmed_at'])); ?> น.</div>
                                    <div class="text-xs text-slate-500 font-medium"><?php echo thai_date_short($record['confirmed_at']); ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center">
                                        <div class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center border border-emerald-100 group-hover:bg-emerald-500 group-hover:text-white transition">
                                            <i class="fas fa-check text-sm"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-32 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-folder-open text-slate-200 text-3xl"></i>
                                        </div>
                                        <p class="text-slate-400 font-medium">ไม่พบข้อมูลรายชื่อในเงื่อนไขที่เลือก</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-12 mb-8 text-center">
            <p class="text-slate-300 text-[10px] font-bold tracking-[0.5em] uppercase">Smart Volunteer Management System Report</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#eventSelector').select2({
                placeholder: "พิมพ์ค้นหาชื่อกิจกรรม...",
                width: '100%'
            });

            $('#eventSelector').on('change', function() {
                const eventId = $(this).val();
                window.location.href = `?event_id=${eventId}`;
            });
        });
    </script>
</body>
</html>