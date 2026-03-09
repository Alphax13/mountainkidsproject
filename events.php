<?php
session_start();

// --- 1. Connection & Session ---
if (!isset($_SESSION['user_id'])) { 
    $volunteer_pk_id = 3; 
} else { 
    $volunteer_pk_id = $_SESSION['user_id']; 
}

$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
if ($conn->connect_error) die("Connection Error: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

// --- 2. Fetch User Name ---
$volunteer_name = "Guest User";
$stmt_v = $conn->prepare("SELECT first_name FROM users WHERE id = ?"); 
if ($stmt_v) {
    $stmt_v->bind_param("i", $volunteer_pk_id); 
    $stmt_v->execute();
    $res_v = $stmt_v->get_result(); 
    if($row = $res_v->fetch_assoc()) $volunteer_name = $row['first_name'];
    $stmt_v->close();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'volunteer') {
    // ถ้าไม่ใช่ ให้ส่งกลับไปหน้า Login หรือหน้าแรก
    header("Location: login.php"); 
    exit();
}
// --- 3. Thai Date Formatter ---
function formatThaiDate($dateStr) {
    if (empty($dateStr)) return '-';
    $date = new DateTime($dateStr);
    $thai_months = [1=>'ม.ค.', 2=>'ก.พ.', 3=>'มี.ค.', 4=>'เม.ย.', 5=>'พ.ค.', 6=>'มิ.ย.', 7=>'ก.ค.', 8=>'ส.ค.', 9=>'ก.ย.', 10=>'ต.ค.', 11=>'พ.ย.', 12=>'ธ.ค.'];
    return $date->format('d') . " " . $thai_months[(int)$date->format('m')] . " " . ($date->format('Y') + 543);
}

// --- 4. Process Confirmation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = (int)$_POST['event_id']; 
    if ($_POST['action'] === 'confirm') { 
        $sql = "INSERT INTO join_event (user_id, event_id, status, confirmed_at) 
                VALUES (?, ?, 1, NOW()) 
                ON DUPLICATE KEY UPDATE status = 1, confirmed_at = NOW()";
        $st = $conn->prepare($sql); 
        $st->bind_param("ii", $volunteer_pk_id, $event_id); 
        if($st->execute()) {
            echo "<script>alert('✨ ยืนยันร่วมเดินทางสำเร็จ!'); window.location.href='events.php';</script>"; 
        }
        exit;
    }
}

// --- 5. Data Mapping (Fallback) ---
$special_details = [
    'วันแม่แห่งชาติ' => [
        'date_range' => '12 – 14 สิงหาคม 2569',
        'activities' => ['บอกรักแม่', 'กำลังใจครูดอย', 'นาทีทอง', 'เกมมหาสนุก', 'การแสดงเด็กๆ', 'มอบสิ่งของบริจาค']
    ],
    'วันพ่อแห่งชาติ' => [
        'date_range' => '5 – 7 ธันวาคม 2569',
        'activities' => ['บอกรักพ่อ', 'กำลังใจครูดอย', 'ขยะแลกขนม', 'นาทีทอง', 'การแสดงของเด็กๆ', 'มอบสิ่งของรายบ้าน']
    ]
];

// --- 6. Fetch Events ---
$sql_e = "SELECT e.*, IFNULL(a.status, -1) as current_status 
          FROM events e 
          LEFT JOIN join_event a ON e.event_id = a.event_id AND a.user_id = ? 
          WHERE e.is_active = 1 
          AND e.event_id != 16 
          ORDER BY e.event_date ASC";
$st_e = $conn->prepare($sql_e); 
$st_e->bind_param("i", $volunteer_pk_id); 
$st_e->execute();
$events = $st_e->get_result()->fetch_all(MYSQLI_ASSOC);

$user_data = null;
if (isset($_SESSION['user_id'])) {
    $user_pk_id = $_SESSION['user_id'];
    $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $user_pk_id);
    $stmt_user->execute();
    $user_data = $stmt_user->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อาสาเพื่อนครูบนดอย | กิจกรรม</title>
    <link href="https://fonts.googleapis.com/css2?family=Anuphan:wght@300;400;500;600;700;800&family=IBM+Plex+Sans+Thai:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ===================================
           CSS VARIABLES
           =================================== */
        :root {
            --brand-blue: #19729c; 
            --brand-dark: #0a3d54;
            --brand-soft: #f4f9fc;
            --accent-orange: #f59e0b;
            --accent-green: #10b981;
            --accent-pink: #ec4899;
            --slate-600: #475569;
            --slate-700: #334155;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 10px 40px -10px rgba(0,0,0,0.08);
            --shadow-lg: 0 20px 50px -15px rgba(0,0,0,0.12);
            --shadow-xl: 0 25px 60px -20px rgba(0,0,0,0.15);
        }

        /* ===================================
           BASE STYLES
           =================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Anuphan', 'IBM Plex Sans Thai', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e8f4f8 100%);
            color: var(--brand-dark);
            line-height: 1.7;
            min-height: 100vh;
            position: relative;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(25, 114, 156, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(245, 158, 11, 0.02) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* ===================================
           LAYOUT
           =================================== */
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 80px 30px 100px; 
            position: relative;
            z-index: 1;
        }

        /* ===================================
           PAGE HEADER
           =================================== */
        .page-header { 
            margin-bottom: 60px;
            animation: fadeInDown 0.8s ease;
        }

        .page-header > p {
            color: var(--brand-blue);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-header > p::before {
            content: '';
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-blue), var(--accent-orange));
            border-radius: 10px;
        }

        .page-header h1 { 
            font-size: 3.5rem; 
            font-weight: 800; 
            margin: 0; 
            letter-spacing: -2px;
            color: var(--brand-dark);
            line-height: 1.1;
        }

        .page-header h1 span { 
            background: linear-gradient(135deg, var(--brand-blue), var(--accent-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-info { 
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px; 
            padding: 12px 24px; 
            background: var(--white); 
            border-radius: 50px; 
            font-size: 0.95rem;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
            border: 2px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .user-info:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--brand-blue);
        }

        .user-info i {
            color: var(--brand-blue);
            font-size: 1.1rem;
        }

        /* ===================================
           EVENT MASTER CARD
           =================================== */
        .event-master-card {
            background: var(--white);
            border-radius: 32px;
            margin-bottom: 35px;
            display: flex;
            overflow: hidden;
            border: 2px solid var(--gray-200);
            box-shadow: var(--shadow-md);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            animation: fadeInUp 0.6s ease backwards;
        }

        .event-master-card:nth-child(1) { animation-delay: 0.1s; }
        .event-master-card:nth-child(2) { animation-delay: 0.2s; }
        .event-master-card:nth-child(3) { animation-delay: 0.3s; }

        .event-master-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 0%, rgba(25, 114, 156, 0.02) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .event-master-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--brand-blue);
        }

        .event-master-card:hover::before {
            opacity: 1;
        }

        /* ===================================
           INFO PANE (Left Side)
           =================================== */
        .info-pane {
            flex: 1.2;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
            position: relative;
        }

        .date-label { 
            color: var(--white);
            background: linear-gradient(135deg, var(--brand-blue), #2196f3);
            font-weight: 700; 
            font-size: 0.85rem; 
            margin-bottom: 20px; 
            display: inline-block;
            padding: 8px 18px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(25, 114, 156, 0.3);
            animation: pulse 2s infinite;
        }

        .info-pane h2 { 
            font-size: 2.2rem; 
            font-weight: 800; 
            margin: 0 0 25px 0; 
            line-height: 1.3; 
            color: var(--brand-dark);
            letter-spacing: -0.5px;
        }

        .location-tag { 
            color: var(--slate-600); 
            font-size: 1rem; 
            display: flex; 
            align-items: flex-start; 
            gap: 12px;
            padding: 15px 20px;
            background: var(--gray-50);
            border-radius: 16px;
            border-left: 4px solid var(--brand-blue);
            transition: all 0.3s ease;
        }

        .location-tag:hover {
            background: var(--gray-100);
            transform: translateX(5px);
        }

        .location-tag i { 
            color: var(--brand-blue); 
            margin-top: 3px;
            font-size: 1.1rem;
        }

        /* ===================================
           ACTION PANE (Right Side)
           =================================== */
        .action-pane {
            flex: 1;
            background: linear-gradient(135deg, #e3f2fd 0%, #d4e1ee 100%);
            padding: 45px 40px;
            border-left: 3px solid var(--brand-blue);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .action-pane::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(25, 114, 156, 0.05) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .action-pane > * {
            position: relative;
            z-index: 1;
        }

        .action-pane h3 { 
            font-size: 1.15rem; 
            font-weight: 700; 
            margin: 0 0 30px 0; 
            display: flex; 
            align-items: center; 
            gap: 10px;
            color: var(--brand-dark);
        }

        /* ===================================
           ACTIVITY GRID
           =================================== */
        .activity-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 35px;
        }

        .act-chip {
            background: var(--white);
            padding: 14px 16px;
            border-radius: 16px;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--brand-dark);
            display: flex;
            align-items: center;
            gap: 10px;
            border: 2px solid transparent;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .act-chip::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--brand-blue), var(--accent-orange));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .act-chip:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-color: var(--brand-blue);
        }

        .act-chip:hover::before {
            opacity: 0.05;
        }

        .act-chip i { 
            font-size: 0.5rem; 
            color: var(--accent-orange);
        }

        .act-chip span {
            position: relative;
            z-index: 1;
        }

        /* ===================================
           BUTTONS & STATUS
           =================================== */
        .btn-action {
            width: 100%; 
            padding: 18px 24px; 
            border-radius: 18px; 
            font-weight: 700;
            font-size: 1.05rem; 
            border: none; 
            cursor: pointer; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 12px;
            position: relative;
            overflow: hidden;
        }

        .btn-action::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-action:active::before {
            width: 300px;
            height: 300px;
        }

        .btn-confirm { 
            background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand-blue) 100%);
            color: var(--white);
            box-shadow: 0 10px 25px rgba(25, 114, 156, 0.4);
        }

        .btn-confirm:hover { 
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(25, 114, 156, 0.5);
        }

        .btn-confirm:active {
            transform: translateY(-1px);
        }

        .btn-status-joined {
            background: var(--white);
            border: 3px dashed var(--accent-green);
            color: var(--accent-green);
            cursor: default;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .btn-status-joined:hover {
            transform: none;
        }

        .btn-status-joined i {
            animation: heartbeat 1.5s infinite;
        }

        /* ===================================
           ANIMATIONS
           =================================== */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 4px 12px rgba(25, 114, 156, 0.3);
            }
            50% {
                box-shadow: 0 4px 20px rgba(25, 114, 156, 0.5);
            }
        }

        @keyframes heartbeat {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            animation: fadeInUp 0.8s ease;
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--gray-300);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--slate-600);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--slate-600);
            font-size: 1rem;
        }

        /* ===================================
           RESPONSIVE DESIGN
           =================================== */
        @media (max-width: 1024px) {
            .container {
                padding: 60px 25px 80px;
            }

            .page-header h1 {
                font-size: 2.8rem;
            }

            .event-master-card {
                flex-direction: column;
                border-radius: 28px;
            }

            .info-pane {
                padding: 40px 35px;
            }

            .action-pane {
                border-left: none;
                border-top: 3px solid var(--brand-blue);
                padding: 35px 35px 40px;
            }

            .activity-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2.2rem;
            }

            .info-pane h2 {
                font-size: 1.8rem;
            }

            .activity-grid {
                gap: 10px;
            }

            .act-chip {
                padding: 12px 14px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 15px 60px;
            }

            .page-header h1 {
                font-size: 1.8rem;
            }

            .info-pane,
            .action-pane {
                padding: 30px 25px;
            }

            .btn-action {
                padding: 16px 20px;
                font-size: 0.95rem;
            }
        }

        /* ===================================
           SCROLL BEHAVIOR
           =================================== */
        html {
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--brand-blue);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--brand-dark);
        }
    </style>
</head>
<body>

<?php if (file_exists('menu_volunteer.php')) include 'menu_volunteer.php'; ?>

<div class="container">
    <header class="page-header">
        <h1>เข้าร่วม<span>กิจกรรม.</span></h1>
        <div class="user-info">
            <i class="fa-solid fa-user-circle"></i>
            <span>สวัสดีคุณ, <strong><?php echo htmlspecialchars($volunteer_name); ?></strong></span>
            
        </div>
    <a href="user_profile.php" style="margin-left: 20px; text-decoration: none;">
        <button style="padding: 10px 20px; background: var(--brand-blue); color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-user-pen"></i>
            <span>ตรวจสอยกิจกรรม</span>
        </button>
    </a>
    </header>

    <div class="events-list">
        <?php if (empty($events)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-calendar-xmark"></i>
                <h3>ยังไม่มีกิจกรรมในขณะนี้</h3>
                <p>กรุณากลับมาตรวจสอบอีกครั้งในภายหลัง</p>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): 
                $status = (int)$event['current_status'];
                $name = $event['event_name'];
                $display_date = !empty($event['schedule_range']) ? $event['schedule_range'] : formatThaiDate($event['event_date']);
                
                // ข้อมูลกิจกรรมย่อย
                $db_highlights = !empty($event['highlights']) ? explode(',', $event['highlights']) : [];
                $fallback_details = null;
                if (strpos($name, 'วันแม่') !== false) $fallback_details = $special_details['วันแม่แห่งชาติ']['activities'];
                if (strpos($name, 'วันพ่อ') !== false) $fallback_details = $special_details['วันพ่อแห่งชาติ']['activities'];
                
                $final_activities = !empty($db_highlights) ? $db_highlights : ($fallback_details ?? ['เตรียมกิจกรรมสนุกๆ', 'ร่วมแบ่งปันรอยยิ้ม']);
            ?>
                
                <div class="event-master-card">
                    
                    <div class="info-pane">
                        <span class="date-label">
                            <i class="fa-solid fa-calendar-days"></i>
                            <?php echo htmlspecialchars($display_date); ?>
                        </span>
                        <h2><?php echo htmlspecialchars($name); ?></h2>
                        <div class="location-tag">
                            <i class="fa-solid fa-map-marker-alt"></i>
                            <span><strong>สถานที่:</strong> <?php echo htmlspecialchars($event['Location']); ?></span>
                        </div>
                    </div>

                    <div class="action-pane">
                        <h3>
                            <i class="fa-solid fa-sparkles" style="color: var(--accent-orange);"></i>
                            ไฮไลท์กิจกรรม
                        </h3>
                        
                        <div class="activity-grid">
                            <?php foreach ($final_activities as $act): ?>
                                <div class="act-chip">
                                    <i class="fa-solid fa-circle"></i>
                                    <span><?php echo htmlspecialchars(trim($act)); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="action-btn-wrapper">
                            <?php if ($status === 1): ?>
                                <button class="btn-action btn-status-joined" disabled>
                                    <i class="fa-solid fa-heart"></i>
                                    <span>ยืนยันเข้าร่วมแล้ว</span>
                                </button>
                            <?php else: ?>
                                <form method="POST" style="width: 100%;">
                                    <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                    <input type="hidden" name="action" value="confirm">
                                    <button type="submit" class="btn-action btn-confirm">
                                        <span>ยืนยันร่วมเดินทาง</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

<script>
// Smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add intersection observer for scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe all event cards
document.querySelectorAll('.event-master-card').forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
    observer.observe(card);
});

// Form submission with loading state
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>กำลังดำเนินการ...</span>';
        }
    });
});
</script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>