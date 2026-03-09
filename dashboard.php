<?php
session_start();
// ตรวจสอบว่ามี Session ของ Admin หรือไม่
// แก้ไข 'role' และ 'admin' ให้ตรงกับที่คุณเก็บใน Database/ตอน Login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // ถ้าไม่มีสิทธิ์ ให้ส่งไปหน้า Login พร้อมส่งข้อความแจ้งเตือน (ถ้ามี)
    header("Location: login.php?error=no_permission");
    exit();
}
// --- 1. การเชื่อมต่อฐานข้อมูล ---
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
$conn->set_charset("utf8mb4");

// --- 2. ดึงสถิติจำนวนสมาชิก ---
$total_members = 0;
$res_m = $conn->query("SELECT COUNT(*) as total FROM users");
if ($res_m) {
    $total_members = $res_m->fetch_assoc()['total'];
}

// --- 3. ฟังก์ชันกลาง สำหรับรวมกลุ่มหมวดหมู่ ---
function get_clean_group($cat_raw) {
    $cat = trim($cat_raw);
    if (mb_strpos($cat, 'ยาสามัญ') !== false) return 'ยาสามัญประจำบ้าน';
    if (mb_strpos($cat, 'เสื้อผ้า') !== false) return 'เสื้อผ้ามือสอง';
    if (mb_strpos($cat, 'ข้าวสาร') !== false || mb_strpos($cat, 'อาหารแห้ง') !== false) return 'ข้าวสารและอาหารแห้ง';
    if (mb_strpos($cat, 'เรียน') !== false || mb_strpos($cat, 'การเรียน') !== false) return 'อุปกรณ์การเรียน';
    if (mb_strpos($cat, 'กีฬา') !== false) return 'อุปกรณ์กีฬา';
    if (mb_strpos($cat, 'ของเล่น') !== false || mb_strpos($cat, 'ตุ๊กตา') !== false) return 'ของเล่นและตุ๊กตา';
    if (mb_strpos($cat, 'ทำความสะอาด') !== false) return 'ผลิตภัณฑ์ทำความสะอาด';
    if (mb_strpos($cat, 'ภายในบ้าน') !== false) return 'เครื่องใช้ภายในบ้าน';
    if (mb_strpos($cat, 'ขนม') !== false) return 'ขนมและของว่าง';
    return trim(preg_replace('/^[0-9]+\.\s?/', '', $cat));
}

// ฟังก์ชันกำหนดสีตามหมวดหมู่
function getColorMapping($name) {
    $map = [
        'ยาสามัญประจำบ้าน' => '#10b981',
        'เสื้อผ้ามือสอง' => '#f43f5e',
        'ข้าวสารและอาหารแห้ง' => '#3b82f6',
        'อุปกรณ์การเรียน' => '#f59e0b',
        'อุปกรณ์กีฬา' => '#6366f1',
        'ของเล่นและตุ๊กตา' => '#ec4899',
        'ผลิตภัณฑ์ทำความสะอาด' => '#84cc16',
        'เครื่องใช้ภายในบ้าน' => '#8b5cf6',
        'ขนมและของว่าง' => '#06b6d4'
    ];
    return $map[$name] ?? '#94a3b8';
}

// --- 4. เตรียมข้อมูลสำหรับกราฟหลัก (Gap Analysis) ---
$sql_main = "SELECT i.sub_category, t.target_quantity, t.current_received 
             FROM event_item_targets t
             JOIN donation_items i ON t.item_id = i.item_id
             WHERE i.is_active = 1";
$result_main = $conn->query($sql_main);

$temp_groups = [];
while($row = $result_main->fetch_assoc()) {
    $group_name = get_clean_group($row['sub_category']);
    if (!isset($temp_groups[$group_name])) {
        $temp_groups[$group_name] = ['t_tar' => 0, 't_rec' => 0];
    }
    $temp_groups[$group_name]['t_tar'] += (float)$row['target_quantity'];
    $temp_groups[$group_name]['t_rec'] += (float)$row['current_received'];
}

$custom_order = [
    'ข้าวสารและอาหารแห้ง', 'ยาสามัญประจำบ้าน', 'เสื้อผ้ามือสอง',
    'อุปกรณ์การเรียน', 'อุปกรณ์กีฬา', 'ของเล่นและตุ๊กตา',
    'ผลิตภัณฑ์ทำความสะอาด', 'เครื่องใช้ภายในบ้าน', 'ขนมและของว่าง'
];

$all_names = []; $all_rec = []; $all_tar = []; $all_gaps = []; $all_colors = [];
foreach($custom_order as $target_name) {
    if (isset($temp_groups[$target_name])) {
        $all_names[] = $target_name; 
        $all_rec[] = $temp_groups[$target_name]['t_rec'];
        $all_tar[] = $temp_groups[$target_name]['t_tar'];
        $gap_val = $temp_groups[$target_name]['t_tar'] - $temp_groups[$target_name]['t_rec'];
        $all_gaps[] = ($gap_val > 0) ? $gap_val : 0;
        $all_colors[] = getColorMapping($target_name);
        unset($temp_groups[$target_name]);
    }
}

// --- 5. ดึงข้อมูลกิจกรรม (Events) ---
$sql_sep_events = "SELECT event_id, event_name FROM events ORDER BY event_date DESC";
$res_sep_events = $conn->query($sql_sep_events);
$sep_event_list = [];

while($se = $res_sep_events->fetch_assoc()) {
    $eid = (int)$se['event_id'];
    $sql_items = "SELECT i.sub_category, t.target_quantity, t.current_received 
                  FROM event_item_targets t 
                  JOIN donation_items i ON t.item_id = i.item_id 
                  WHERE t.event_id = $eid"; 
    $res_it = $conn->query($sql_items);
    
    $event_groups = [];
    $e_rec_sum = 0; $e_tar_sum = 0;
    while($it = $res_it->fetch_assoc()) { 
        $g_name = get_clean_group($it['sub_category']);
        if (!isset($event_groups[$g_name])) {
            $event_groups[$g_name] = [
                'clean_name' => $g_name, 
                'target_quantity' => 0, 
                'current_received' => 0,
                'color' => getColorMapping($g_name)
            ];
        }
        $event_groups[$g_name]['target_quantity'] += (float)$it['target_quantity'];
        $event_groups[$g_name]['current_received'] += (float)$it['current_received'];
        $e_rec_sum += (float)$it['current_received'];
        $e_tar_sum += (float)$it['target_quantity'];
    }
    $sorted_event_data = [];
    foreach($all_names as $n) { if(isset($event_groups[$n])) $sorted_event_data[] = $event_groups[$n]; }
    $sep_event_list[] = [ 'name' => $se['event_name'], 'id' => $eid, 'e_percent' => ($e_tar_sum > 0) ? round(($e_rec_sum / $e_tar_sum) * 100) : 0, 'e_rec' => $e_rec_sum, 'e_tar' => $e_tar_sum, 'data' => $sorted_event_data ];
}

function getItemsDetailByEvent($conn, $event_id) {
    $event_id = (int)$event_id;
    return $conn->query("SELECT i.item_name, t.current_received, t.target_quantity, i.unit, i.sub_category FROM event_item_targets t JOIN donation_items i ON t.item_id = i.item_id WHERE t.event_id = $event_id");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดผู้บริหาร - ระบบจัดการสิ่งของบริจาค</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=IBM+Plex+Sans+Thai:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        :root { --sidebar-width: 280px; --primary: #6366f1; --card-radius: 24px; }
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', 'IBM Plex Sans Thai', sans-serif; color: #1e293b; margin: 0; }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; transition: 0.3s; }
        @media (max-width: 991px) { .main-content { margin-left: 0; padding: 20px; } }
        .page-title { font-weight: 800; letter-spacing: -1.5px; font-size: 2.2rem; margin-bottom: 5px; color: #0f172a; }
        .stat-badge { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; padding: 25px 35px; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.3); display: inline-flex; flex-direction: column; align-items: flex-start; }
        
        .bootstrap-card { border: none; border-radius: 8px; color: white; padding: 20px; margin-bottom: 15px; position: relative; overflow: hidden; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); transition: 0.3s; }
        .bootstrap-card .card-body-icon { position: absolute; z-index: 0; top: -10px; right: -10px; font-size: 4rem; opacity: 0.2; transform: rotate(15deg); }
        .card-footer-link { background: rgba(0,0,0,0.1); margin: 20px -20px -20px; padding: 10px 20px; font-size: 0.8rem; text-decoration: none; color: rgba(255,255,255,0.8); display: flex; justify-content: space-between; align-items: center; }

        .card-modern { background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.7); border-radius: var(--card-radius); padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .target-alert { background: #fff1f2; border-radius: 50px; padding: 8px 18px; color: #e11d48; font-weight: 700; font-size: 13px; border: 1px solid #ffe4e6; }
        .hero-card { background: white; border-radius: 30px; padding: 30px; border: 1px solid #e2e8f0; transition: 0.4s; height: 100%; position: relative; overflow: hidden; }
        .big-percent { font-size: 3.5rem; font-weight: 800; line-height: 1; letter-spacing: -2px; }
        .btn-action { padding: 10px 20px; border-radius: 15px; border: none; font-weight: 700; color: white; width: 100%; margin-top: 15px; }
        .progress-thin { height: 8px; border-radius: 10px; background: #e2e8f0; overflow: hidden; }
        .progress-bar { height: 100%; transition: width 1s; }
        .compact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px; }
        @media (max-width: 768px) { .compact-grid { grid-template-columns: 1fr; } }
        .compact-box { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 16px; padding: 12px 16px; display: flex; flex-direction: column; }
    </style>
</head>
<body>

<?php if(file_exists('menu_admin.php')) include 'menu_admin.php'; ?>

<div class="main-content">
    <div class="row mb-5 align-items-center">
        <div class="col-md-7">
            <h1 class="page-title">แผงควบคุม <span style="color:var(--primary)">ผู้ดูแลระบบ</span></h1>
            <p class="text-muted fw-500">สรุปความคืบหน้าการจัดหาพัสดุบริจาค (รวมทั้งสิ้น 8 หมวดหมู่)</p>
            <?php if(file_exists('howto.php')) include 'howto.php'; ?>
        </div>
        <div class="col-md-5 text-lg-end">
            <div class="stat-badge">
                <small class="text-uppercase fw-700 opacity-75" style="font-size: 11px;">สมาชิกที่ลงทะเบียน</small>
                <span class="h1 fw-800 m-0"><?= number_format($total_members) ?> คน</span>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <?php 
        $icons = ['ข้าวสารและอาหารแห้ง' => 'fa-bowl-rice', 'ยาสามัญประจำบ้าน' => 'fa-medkit', 'เสื้อผ้ามือสอง' => 'fa-shirt', 'อุปกรณ์การเรียน' => 'fa-book', 'อุปกรณ์กีฬา' => 'fa-basketball', 'ของเล่นและตุ๊กตา' => 'fa-ghost', 'ผลิตภัณฑ์ทำความสะอาด' => 'fa-soap', 'เครื่องใช้ภายในบ้าน' => 'fa-house-laptop', 'ขนมและของว่าง' => 'fa-cookie'];
        $bg_colors = ['#0d6efd', '#ffc107', '#198754', '#dc3545', '#6610f2', '#6f42c1', '#fd7e14', '#20c997'];
        foreach($all_names as $idx => $name): 
            $bg = $bg_colors[$idx % count($bg_colors)]; $icon = $icons[$name] ?? 'fa-box';
        ?>
        <div class="col-xl-3 col-md-6">
            <div class="bootstrap-card" style="background-color: <?= $bg ?>;">
                <div class="card-body-icon"><i class="fas <?= $icon ?>"></i></div>
                <div class="fw-700 mb-1" style="font-size: 0.85rem; height: 2.4em; overflow: hidden;"><?= $name ?></div>
                <div class="h3 fw-800 m-0"><?= number_format($all_rec[$idx]) ?> / <?= number_format($all_tar[$idx]) ?></div>
                <div class="small opacity-75 mt-1">ได้รับ / เป้าหมาย</div>
                <a href="javascript:void(0)" class="card-footer-link"><span>รายละเอียด</span><i class="fas fa-angle-right"></i></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card-modern">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-800 m-0 text-dark"><i class="fas fa-chart-bar text-primary me-2"></i>วิเคราะห์ปริมาณความต้องการ (Gap Analysis)</h3>
            <span class="target-alert"><i class="fas fa-bullseye me-2"></i> เส้นเป้าหมาย</span>
        </div>
        <div style="height: 500px;"><canvas id="mainChart"></canvas></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="card-modern">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="fas fa-chart-pie text-primary fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-800 m-0">สัดส่วนสิ่งของที่ได้รับทั้งหมดแยกตามหมวดหมู่</h4>
                        <p class="text-muted small m-0">วิเคราะห์ว่าของที่ได้รับเข้ามาแล้ว หมวดไหนมีสัดส่วนมากที่สุด</p>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-6 text-center">
                        <div style="max-height: 400px; position: relative; display: inline-block; width: 100%;">
                            <canvas id="donutChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="list-group list-group-flush mt-3 mt-md-0">
                            <?php foreach($all_names as $idx => $name): 
                                $total_sum = array_sum($all_rec);
                                $percent = ($total_sum > 0) ? round(($all_rec[$idx] / $total_sum) * 100, 1) : 0;
                            ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <div style="width: 12px; height: 12px; border-radius: 50%; background: <?= $all_colors[$idx] ?>;" class="me-2"></div>
                                    <span class="fw-600 small"><?= $name ?></span>
                                </div>
                                <div class="text-end">
                                    <span class="fw-800 small"><?= number_format($all_rec[$idx]) ?></span>
                                    <span class="text-muted small ms-1">(<?= $percent ?>%)</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-modern mt-3" style="border-top: 5px solid #6366f1;">
        <h6 class="fw-800 mb-3"><i class="fas fa-info-circle me-2"></i>คำอธิบายสัญลักษณ์สีแยกตามหมวดหมู่สิ่งของ</h6>
        <div class="row g-3">
            <?php 
            $legend_items = ['ข้าวสารและอาหารแห้ง' => '#3b82f6', 'ยาสามัญประจำบ้าน' => '#10b981', 'เสื้อผ้ามือสอง' => '#f43f5e', 'อุปกรณ์การเรียน' => '#f59e0b', 'อุปกรณ์กีฬา' => '#6366f1', 'ของเล่นและตุ๊กตา' => '#ec4899', 'ผลิตภัณฑ์ทำความสะอาด' => '#84cc16', 'เครื่องใช้ภายในบ้าน' => '#8b5cf6', 'ขนมและของว่าง' => '#06b6d4'];
            foreach($legend_items as $l_name => $l_color): ?>
            <div class="col-6 col-md-3"><div class="d-flex align-items-center gap-2"><span style="width: 15px; height: 15px; background-color: <?= $l_color ?>; border-radius: 3px; display: inline-block;"></span><span class="small fw-600"><?= $l_name ?></span></div></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="swiper swiper-container mySwiper mb-4 mt-5">
        <div class="swiper-wrapper">
            <?php $gradients = [['from' => '#6366f1', 'to' => '#a855f7'], ['from' => '#f59e0b', 'to' => '#fbbf24'], ['from' => '#06b6d4', 'to' => '#3b82f6']];
            foreach($sep_event_list as $idx => $ev): $theme = $gradients[$idx % 3]; ?>
            <div class="swiper-slide"><div class="hero-card"><div class="big-percent" style="background: linear-gradient(135deg, <?= $theme['from'] ?>, <?= $theme['to'] ?>); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?= $ev['e_percent'] ?>%</div><h5 class="fw-800 text-dark mt-3 mb-1"><?= mb_substr($ev['name'], 0, 40) ?></h5><button class="btn-action btn-scroll-to" data-target="event_card_<?= $ev['id'] ?>" style="background: linear-gradient(135deg, <?= $theme['from'] ?>, <?= $theme['to'] ?>);">รายละเอียดกิจกรรม <i class="fas fa-arrow-right ms-2"></i></button></div></div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <?php foreach($sep_event_list as $ev): ?>
    <div class="card-modern" id="event_card_<?= $ev['id'] ?>">
        <div class="d-flex align-items-center justify-content-between mb-4"><h5 class="fw-700 m-0 text-dark"><?= htmlspecialchars($ev['name']) ?></h5><span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 fw-700">ความคืบหน้า <?= $ev['e_percent'] ?>%</span></div>
        <div class="row">
            <div class="col-12 mb-4"><div style="height: 400px;"><canvas id="chart_sep_<?= $ev['id'] ?>"></canvas></div></div>
            <div class="col-12"><div class="compact-grid">
                <?php $details = getItemsDetailByEvent($conn, $ev['id']); while($d = $details->fetch_assoc()): $percent = ($d['target_quantity'] > 0) ? ($d['current_received'] / $d['target_quantity']) * 100 : 0; $cat_name = get_clean_group($d['sub_category']); $bar_color = getColorMapping($cat_name); ?>
                <div class="compact-box"><div class="compact-header"><span class="fw-700 small"><?= htmlspecialchars($d['item_name']) ?></span><span class="fw-800 small"><?= number_format($d['current_received']) ?> / <?= number_format($d['target_quantity']) ?></span></div><div class="progress-thin"><div class="progress-bar" style="width: <?= min($percent, 100) ?>%; background-color: <?= $bar_color ?> !important;"></div></div></div>
                <?php endwhile; ?>
            </div></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
var swiper = new Swiper(".mySwiper", { slidesPerView: 1, spaceBetween: 25, pagination: { el: ".swiper-pagination", clickable: true }, breakpoints: { 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3 } } });
Chart.defaults.font.family = "'Plus Jakarta Sans', 'IBM Plex Sans Thai', sans-serif";

// Main Chart (Bar/Line)
new Chart(document.getElementById('mainChart'), {
    data: {
        labels: <?= json_encode($all_names) ?>,
        datasets: [
            { type: 'line', label: 'เป้าหมาย', data: <?= json_encode($all_tar) ?>, borderColor: '#f43f5e', borderWidth: 3, borderDash: [6, 4], pointStyle: 'circle', pointRadius: 6, order: 0 },
            { type: 'bar', label: 'ได้รับแล้ว', data: <?= json_encode($all_rec) ?>, backgroundColor: <?= json_encode($all_colors) ?>, borderRadius: 10, stack: 'combined', barThickness: 45, order: 1 },
            { type: 'bar', label: 'ส่วนที่ขาด', data: <?= json_encode($all_gaps) ?>, backgroundColor: '#eef2f6', borderRadius: 10, stack: 'combined', barThickness: 45, order: 2 }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false, scales: { y: { stacked: true, beginAtZero: true }, x: { stacked: true } }, plugins: { legend: { display: false } } }
});

// [เพิ่มใหม่] Donut Chart
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($all_names) ?>,
        datasets: [{
            data: <?= json_encode($all_rec) ?>,
            backgroundColor: <?= json_encode($all_colors) ?>,
            borderWidth: 0,
            hoverOffset: 20
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let value = context.raw;
                        let percent = Math.round((value / total) * 100);
                        return ` ${context.label}: ${value.toLocaleString()} (${percent}%)`;
                    }
                }
            }
        }
    }
});

<?php foreach($sep_event_list as $ev): ?>
new Chart(document.getElementById('chart_sep_<?= $ev['id'] ?>'), {
    type: 'bar',
    data: { labels: <?= json_encode(array_column($ev['data'], 'clean_name')) ?>, datasets: [ { label: 'ได้รับแล้ว', data: <?= json_encode(array_column($ev['data'], 'current_received')) ?>, backgroundColor: <?= json_encode(array_column($ev['data'], 'color')) ?>, borderRadius: 8, barThickness: 30 }, { label: 'เป้าหมาย', data: <?= json_encode(array_column($ev['data'], 'target_quantity')) ?>, backgroundColor: '#e2e8f0', borderRadius: 8, barThickness: 30 } ] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
});
<?php endforeach; ?>

document.querySelectorAll(".btn-scroll-to").forEach(btn => {
    btn.addEventListener("click", function () { document.getElementById(this.getAttribute("data-target")).scrollIntoView({ behavior: "smooth" }); });
});
</script>
</body>
</html>