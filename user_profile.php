<?php
session_start();
$user_pk_id = $_SESSION['user_id']; 
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}
$conn->set_charset("utf8mb4");

// --- 2. Fetch User Data ---
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_pk_id);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc(); 
// --- 1. Connection & Session ---
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
$user_pk_id = $_SESSION['user_id']; 
// --- 3. Fetch Events ---
$sql_events = "SELECT e.*, IFNULL(a.status, -1) as current_status FROM events e 
               INNER JOIN join_event a ON e.event_id = a.event_id 
               WHERE a.user_id = ? 
               ORDER BY e.event_date DESC";
$stmt_ev = $conn->prepare($sql_events);
$stmt_ev->bind_param("i", $user_pk_id);
$stmt_ev->execute();
$upcoming_events = $stmt_ev->get_result()->fetch_all(MYSQLI_ASSOC);

// --- 4. Thai Date Formatter ---
function formatThaiDate($dateStr) {
    if (!$dateStr) return '-';
    $d = new DateTime($dateStr);
    $th_m = [1=>'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    return $d->format('j') . " " . $th_m[(int)$d->format('m')] . " " . ($d->format('Y') + 543);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Profile | Volunteer Hub</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=IBM+Plex+Sans+Thai:wght@300;400;500;600&display=swap" rel="stylesheet">

   <style>
        :root {
            /* สีจากโลโก้และปุ่มในรูป */
            --glass-bg: rgba(255, 255, 255, 0.82);
            --glass-border: rgba(255, 255, 255, 0.6);
            --accent: #19729c; /* สีน้ำเงินฟ้าหลัก */
            --accent-dark: #0f4d6a;
            --accent-gradient: linear-gradient(135deg, #19729c 0%, #4facfe 100%);
            --warm-orange: #d97706; /* สีส้มจาก "เพื่อนครูบนดอย" */
        }

        body {
            font-family: 'Plus Jakarta Sans', 'IBM Plex Sans Thai', sans-serif;
            /* ไล่สีพื้นหลังแบบในรูป (ฟ้าอ่อน-ขาว-นวล) */
            background-color: #f0fdfa;
            background-image: 
                radial-gradient(at 0% 0%, #fef3c7 0, transparent 40%), /* นวลๆ ฝั่งซ้าย */
                radial-gradient(at 100% 0%, #ccfbf1 0, transparent 50%), /* ฟ้าเขียวฝั่งขวา */
                radial-gradient(at 50% 100%, #ffffff 0, transparent 60%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #1e293b;
        }

        .main-wrapper {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            margin: 50px auto;
            padding: 40px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.05);
            opacity: 0; 
        }

        .event-info { min-width: 0; }
        .event-info h3 { 
            font-weight: 700; font-size: 1.3rem; margin-bottom: 8px; 
            color: #0f172a; /* สีตัวหนังสือเข้มให้อ่านง่าย */
            word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;
        }
        
        /* ใส่สีส้มให้กับหัวข้อประวัติกิจกรรมให้เหมือนในรูป */
        .col-lg-8 h2 {
            color: var(--warm-orange) !important;
        }

        .meta-item { 
            color: #64748b; font-size: 0.9rem; margin-right: 15px; display: inline-flex; align-items: center; gap: 5px; 
            max-width: 100%;
        }
        .location-text { word-break: break-all; }

        .avatar-outer {
            position: relative; width: 160px; height: 160px; margin: 0 auto 25px;
            padding: 8px; border: 2px dashed #cbd5e1; border-radius: 50%;
            animation: rotate-border 15s linear infinite;
        }
        @keyframes rotate-border { 100% { transform: rotate(360deg); } }
        .avatar-inner {
            width: 100%; height: 100%; border-radius: 50%; overflow: hidden;
            animation: rotate-border 15s linear infinite reverse; border: 3px solid white;
        }
        .avatar-inner img { width: 100%; height: 100%; object-fit: cover; }

        .btn-upload {
            position: absolute; bottom: 5px; right: 5px;
            background: var(--accent-gradient); color: white;
            border: 4px solid var(--glass-bg); border-radius: 50%;
            width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; box-shadow: 0 4px 10px rgba(25, 114, 156, 0.3); z-index: 10;
        }

        .event-glass-card {
            background: rgba(255, 255, 255, 0.9); border: 1px solid var(--glass-border);
            border-radius: 25px; padding: 25px; margin-bottom: 20px;
            display: flex; align-items: center; transition: 0.4s;
            border-left: 5px solid var(--accent);
            opacity: 0; transform: translateY(20px);
        }

        .date-badge {
            background: var(--accent-gradient); color: white; border-radius: 18px;
            min-width: 85px; padding: 15px 10px; text-align: center; margin-right: 25px;
            box-shadow: 0 10px 15px -3px rgba(25, 114, 156, 0.3);
        }

        .btn-cancel-glass {
            background: #f1f5f9; color: #64748b; border: none;
            padding: 12px 24px; border-radius: 15px; font-weight: 700; transition: 0.3s;
        }
        .btn-cancel-glass:hover { background: #fee2e2; color: #ef4444; }

        @media (max-width: 992px) {
            .event-glass-card { flex-direction: column; text-align: center; }
            .date-badge { margin: 0 0 20px 0; }
        }
    </style>
</head>
<body>
<?php include "menu_volunteer.php";?>

<form id="cancelForm" method="POST" action="cancel_event.php" style="display:none;">
    <input type="hidden" name="event_id" id="cancel_event_id">
</form>

<div class="container">
    <div class="main-wrapper shadow-lg">
        <div class="row g-5">
            <div class="col-lg-4 text-center">
                <div class="profile-card">
                    <div class="avatar-outer">
                        <div class="avatar-inner">
                            <img src="<?php echo htmlspecialchars($user_data['profile_image_path'] ?? 'uploads/profiles/default.png'); ?>" alt="Avatar">
                        </div>
                        <label for="profile_image_input" class="btn-upload shadow"><i class="fas fa-camera"></i></label>
                    </div>
                    <h2 style="font-weight: 800; color: #064e3b;"><?php echo htmlspecialchars($user_data['first_name']); ?></h2>
                    <p class="text-muted">จิตอาสา✨</p>
                    
                    <div class="mt-4">
                        <div class="info-pill mb-2 bg-white bg-opacity-50 p-3 rounded-4 text-start border">
                            <small class="text-success fw-bold d-block">EMAIL</small>
                            <span class="text-break"><?php echo htmlspecialchars($user_data['email']); ?></span>
                        </div>
                        <div class="info-pill bg-white bg-opacity-50 p-3 rounded-4 text-start border">
                            <small class="text-success fw-bold d-block">VOLUNTEER ID</small>
                            <span>#<?php echo str_pad($user_data['id'], 5, '0', STR_PAD_LEFT); ?></span>
                        </div>
                    </div>
                    <a href="history.php" class="btn btn-primary rounded-pill fw-bold mt-3 w-100">
                        <i class="fas fa-history me-2"></i> ดูประวัติเพิ่มเติม
                    </a>
                    <a href="logout.php" class="btn btn-link text-danger text-decoration-none fw-bold mt-4 js-logout">Log Out</a>
                </div>
            </div>

            <div class="col-lg-8 ps-lg-5">
                <h2 class="fw-bold mb-4" style="color: var(--accent-dark);">
                    <i class="fas fa-leaf me-2"></i> ประวัติกิจกรรม
                </h2>

                <?php if (count($upcoming_events) > 0): ?>
                    <?php foreach ($upcoming_events as $ev): 
                        $dateObj = new DateTime($ev['event_date']); 
                        $display_date = !empty($ev['schedule_range']) ? $ev['schedule_range'] : formatThaiDate($ev['event_date']);
                    ?>
                        <div class="event-glass-card">
                            <div class="date-badge">
                                <span class="fs-2 fw-bold d-block"><?php echo $dateObj->format('d'); ?></span>
                                <span class="d-block"><?php echo $dateObj->format('M'); ?></span>
                                <small><?php echo $dateObj->format('Y')+543; ?></small>
                            </div>
                            <div class="event-info flex-grow-1">
                                <h3 class="text-break"><?php echo htmlspecialchars($ev['event_name']); ?></h3>
                                <div class="d-flex flex-wrap">
                                    <span class="meta-item"><i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars($display_date); ?></span>
                                    <span class="meta-item"><i class="far fa-clock"></i> <?php echo htmlspecialchars($ev['event_time']); ?> น.</span>
                                    <span class="meta-item location-text">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ev['Location']); ?>
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <span class="badge rounded-pill bg-success bg-opacity-25 text-success fw-bold">Confirmed</span>
                                </div>
                            </div>
                            <div class="event-action">
                                <button type="button" class="btn-cancel-glass" onclick="handleCancel(<?php echo $ev['event_id']; ?>)">Cancel</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 opacity-50">
                        <i class="fas fa-seedling fa-3x mb-3"></i>
                        <p>ไม่มีประวัติกิจกรรมที่เข้าร่วม</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<script>
    window.onload = () => {
        gsap.to(".main-wrapper", { opacity: 1, duration: 1 });
        gsap.to(".event-glass-card", { opacity: 1, y: 0, stagger: 0.1, duration: 0.8 });
    };

    // Logout Function
    document.querySelector('.js-logout').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'ออกจากระบบ?', icon: 'question', showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#10b981'
        }).then((result) => { if (result.isConfirmed) window.location.href = this.href; });
    });

    // --- ส่วนแก้ไขใหม่: ใช้การส่งค่าแบบ AJAX ที่รองรับ PHP ทั่วไปได้ดีกว่า ---
    function handleCancel(eventId) {
        Swal.fire({
            title: 'ยกเลิกการเข้าร่วม?',
            text: 'คุณแน่ใจหรือไม่ว่าต้องการยกเลิกกิจกรรมนี้',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#10b981',
            confirmButtonText: 'ใช่, ยกเลิกกิจกรรม',
            cancelButtonText: 'ไม่ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ใช้การสร้าง Form Data แบบดั้งเดิม
                const formData = new FormData();
                formData.append('event_id', eventId);

                fetch('cancel_event.php', {
                    method: 'POST',
                    body: formData // ส่งแบบ FormData จะทำให้ PHP รับค่า $_POST ได้ง่ายที่สุด
                })
                .then(response => response.text()) // รับเป็น text มาก่อนเพื่อ debug ถ้าพัง
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            Swal.fire('สำเร็จ', 'ยกเลิกเรียบร้อย', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด', data.message, 'error');
                        }
                    } catch (e) {
                        console.error("Server Error Response:", text);
                        Swal.fire('Error', 'เซิร์ฟเวอร์ตอบกลับผิดรูปแบบ', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                });
            }
        });
    }
</script>
<?php include 'footer.php'; ?>
</body>
</html>