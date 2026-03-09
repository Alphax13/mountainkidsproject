<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_pk_id = $_SESSION['user_id']; 
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
$conn->set_charset("utf8mb4");

// --- Logic อัปโหลดรูปโปรไฟล์ (เหมือนเดิม) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $target_dir = "uploads/profiles/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    $ext = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = $user_pk_id . '_' . time() . "." . $ext;
    $target_file = $target_dir . $new_filename;
    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("UPDATE users SET profile_image_path = ? WHERE id = ?");
        $stmt->bind_param("si", $target_file, $user_pk_id);
        $stmt->execute();
        header("Location: history.php?success=1"); exit();
    }
}

function get_profile_image($path, $name = "User") {
    if (!empty($path) && file_exists($path)) return $path . "?v=" . time();
    return "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=f1f5f9&color=19729c&size=200&bold=true";
}

function formatThaiDate($dateStr) {
    if (empty($dateStr)) return '-';
    $d = new DateTime($dateStr);
    $th_m = [1=>'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    return $d->format('j') . " " . $th_m[(int)$d->format('m')] . " " . ($d->format('Y') + 543);
}

$user_data = $conn->query("SELECT * FROM users WHERE id = $user_pk_id")->fetch_assoc();
$confirmed_events = $conn->query("SELECT T3.* FROM join_event T2 JOIN events T3 ON T2.event_id = T3.event_id WHERE T2.user_id = $user_pk_id AND T2.status = 1 ORDER BY T3.event_date DESC")->fetch_all(MYSQLI_ASSOC);

// --- จุดที่แก้ไข: ปรับ Query การบริจาคใหม่ ---
// เรา Join กับ donation_items เพื่อเอาชื่อของมาโชว์ใน Query เดียวเลย
$res_don = $conn->query("SELECT d.*, e.event_name, i.item_name,
    CASE 
        WHEN d.status = 'Approved' THEN 'ยืนยันแล้ว' 
        WHEN d.status = 'Rejected' THEN 'ไม่ผ่านการอนุมัติ' 
        ELSE 'รอตรวจสอบ' 
    END AS status_text 
    FROM donations d 
    LEFT JOIN events e ON d.event_id = e.event_id 
    LEFT JOIN donation_items i ON d.item_id = i.item_id 
    WHERE d.user_id = $user_pk_id 
    ORDER BY d.donation_date DESC");

$my_donations = [];
while ($don = $res_don->fetch_assoc()) {
    // เนื่องจากโครงสร้างใหม่ 1 แถว = 1 ไอเทม เราจึงไม่ต้องวนลูป Query ซ้อนอีก
    // แต่เพื่อไม่ให้โค้ดส่วนแสดงผล (HTML) พัง เราจะจำลอง Array 'items' ให้เหมือนเดิม
    $don['items'] = [
        [
            'item_name' => $don['item_name'],
            'quantity' => $don['quantity']
        ]
    ];
    $my_donations[] = $don;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Anuphan:wght@300;400;600;700&display=swap" rel="stylesheet">
    
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


        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(217, 119, 6, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 10%, rgba(25, 114, 156, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, #ffffff 0%, transparent 100%);
            z-index: -1;
        }

        .main-wrapper {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 48px;
            animation: appAppear 1s cubic-bezier(0.19, 1, 0.22, 1);
        }

        @keyframes appAppear {
            from { opacity: 0; transform: translateY(30px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
:root {
    /* สีหลัก */
    --accent: #19729c; 
    --accent-dark: #0f4d6a;
    --accent-gradient: linear-gradient(135deg, #19729c 0%, #4facfe 100%);
    --warm-orange: #d97706; 
    --brand: #19729c;
    --brand-soft: rgba(25, 114, 156, 0.1);
    --text-title: #1e293b;
    --text-light: #64748b;

    /* ตั้งค่าความโค้งใหม่แบบระบุชัดเจน */
    --radius-xl: 40px; /* สำหรับกล่องใหญ่ */
    --radius-lg: 30px; /* สำหรับการ์ด */
    --radius-md: 20px; /* สำหรับปุ่มหรือไอเทมย่อย */
}

/* ส่วนของ Sidebar (กล่องซ้าย) */
.sidebar {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(25, 114, 156, 0.1);
    
    /* ปรับโค้งตรงนี้ */
    border-radius: 40px !important; 
    
    padding: 48px 32px;
    text-align: center;
    height: fit-content;
    position: sticky;
    top: 40px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
    overflow: hidden; /* บังคับให้เนื้อหาโค้งตามกล่อง */
}

/* กล่องเนื้อหาขวา (Content Group) */
.content-group-box {
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    
    /* ปรับโค้งตรงนี้ */
    border-radius: 40px !important;
    
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    gap: 32px;
}

/* การ์ดรายการย่อย (Luxury Card) */
.luxury-card {
    background: #ffffff;
    
    /* ปรับโค้งตรงนี้ */
    border-radius: 30px !important;
    
    padding: 32px;
    border: 1px solid rgba(241, 245, 249, 0.8);
    transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.02);
}

.luxury-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(25, 114, 156, 0.1);
}

/* สถิติย่อยด้านใน */
.stat-item {
    background: #ffffff;
    padding: 20px 10px;
    
    /* ปรับโค้งตรงนี้ */
    border-radius: 25px !important;
    
    border: 1px solid #f1f5f9;
    transition: 0.3s;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
}

/* รูปโปรไฟล์แบบ Squircle (โค้งมนพิเศษ) */
.profile-image {
    width: 160px; 
    height: 160px; 
    border-radius: 50px; /* ปรับให้ดูโค้งนวลกว่าวงกลม */
    object-fit: cover; 
    border: 8px solid #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

/* ชิปรายการสิ่งของ */
.chip {
    background: #f8fafc;
    padding: 10px 20px;
    border-radius: 18px !important; /* ปรับโค้งตรงนี้ */
    font-size: 0.9rem;
    color: var(--text-title);
    font-weight: 600;
    border: 1px solid #f1f5f9;
}
        .sidebar {
            background: rgba(239, 253, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgb(38, 42, 250);
            border-radius: var(--radius-lg);
            padding: 48px 32px;
            text-align: center;
            height: fit-content;
            position: sticky;
            top: 40px;
            box-shadow: 0 20px 50px rgba(25, 114, 156, 0.05);
        }

        .profile-container { position: relative; margin-bottom: 32px; }
        .profile-image {
            width: 160px; height: 160px; border-radius: 60px;
            object-fit: cover; border: 8px solid #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .profile-container:hover .profile-image { transform: scale(1.05) rotate(2deg); }

        .cam-overlay {
            position: absolute; bottom: 0; right: 20px;
            background: var(--brand); width: 44px; height: 44px;
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            color: white; cursor: pointer; border: 4px solid #fff; transition: 0.3s;
        }
        .cam-overlay:hover { transform: scale(1.1); background: var(--warm-orange); }

        .user-info h2 { font-weight: 800; color: var(--text-title); margin: 0; font-size: 1.6rem; }
        .user-info p { color: var(--text-light); font-size: 0.9rem; margin: 8px 0 32px; }

        .dash-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 32px; }
        .stat-item {
            background: #fff; padding: 20px 10px; border-radius: var(--radius-md);
            border: 1px solid #f1f5f9; transition: 0.3s;
        }
        .stat-item:hover { border-color: var(--brand); transform: translateY(-3px); }
        .stat-item span { display: block; font-weight: 800; font-size: 1.5rem; color: var(--text-title); }
        .stat-item small { font-weight: 700; font-size: 0.65rem; color: var(--brand); text-transform: uppercase; }

        /* จัดระเบียบกล่องเนื้อหาฝั่งขวา */
        .content-section { display: flex; flex-direction: column; gap: 32px; }
        
        .content-group-box {
            background: rgba(161, 200, 252, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: var(--radius-lg);
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .section-header {
            display: flex; align-items: center; gap: 16px; margin-bottom: 30px;
            border-bottom: 2px solid var(--brand-soft); padding-bottom: 15px;
        }
        .section-header .icon-wrap {
            width: 48px; height: 48px; background: #fff;
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            color: var(--brand); font-size: 1.2rem; box-shadow: 0 8px 16px rgba(0,0,0,0.03);
        }
        .section-header h3 { font-weight: 800; font-size: 1.5rem; color: var(--text-title); margin: 0; }

        .luxury-card {
            background: #fff; border-radius: var(--radius-lg); padding: 32px;
            border: 1px solid rgba(241, 245, 249, 0.8); transition: 0.4s;
            position: relative; overflow: hidden; margin-bottom: 16px;
        }
        .luxury-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(25, 114, 156, 0.08); }

        .badge { padding: 8px 16px; border-radius: 12px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 8px; }
        .badge-Approved { background: #e0f2fe; color: #0369a1; }
        .badge-Pending { background: #fff7ed; color: #c2410c; }
        .badge-Rejected { background: #fef2f2; color: #b91c1c; }

        .item-row { display: flex; flex-wrap: wrap; gap: 10px; margin: 24px 0; }
        .chip { background: #f8fafc; padding: 10px 20px; border-radius: 14px; font-size: 0.9rem; color: var(--text-title); font-weight: 600; border: 1px solid #f1f5f9; }
        .chip b { color: var(--warm-orange); margin-left: 8px; }

        .proof-gallery { display: flex; gap: 16px; margin-top: 24px; }
        .proof-img { width: 80px; height: 80px; border-radius: 20px; object-fit: cover; cursor: pointer; transition: 0.4s; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .proof-img:hover { transform: scale(1.1) rotate(3deg); }

        .logout-link { text-decoration: none; color: #ef4444; font-weight: 700; padding: 16px; border-radius: 20px; display: block; transition: 0.3s; margin-top: 10px; text-align: center; }
        .logout-link:hover { background: #fff1f1; }

        @media (max-width: 992px) {
            .main-wrapper { grid-template-columns: 1fr; margin: 20px auto; }
            .sidebar { position: relative; top: 0; padding: 32px; }
            .content-group-box { padding: 24px; }
        }
    </style>
</head>
<body>

<?php include 'menu_volunteer.php'; ?>

<div class="main-wrapper">
    <aside class="sidebar">
        <div class="profile-container">
            <img src="<?php echo get_profile_image($user_data['profile_image_path'], $user_data['first_name']); ?>" class="profile-image" id="profile-preview">
            <label for="p_input" class="cam-overlay">
                <i class="fas fa-camera"></i>
            </label>
        </div>
        
        <div class="user-info">
            <h2><?php echo htmlspecialchars($user_data['first_name']); ?></h2>
            <p><?php echo htmlspecialchars($user_data['email']); ?></p>
        </div>

        <div class="dash-stats">
            <div class="stat-item">
                <small>Events</small>
                <span><?php echo count($confirmed_events); ?></span>
            </div>
            <div class="stat-item">
                <small>Donated</small>
                <span><?php echo count($my_donations); ?></span>
            </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="u_form" style="display:none;">
            <input type="file" name="profile_image" id="p_input" onchange="this.form.submit()">
        </form>
        
        <a href="logout.php" class="logout-link">
            <i class="fas fa-power-off me-2"></i> ออกจากระบบ
        </a>
    </aside>

    <div class="content-section">
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'volunteer'): ?>
        <div class="content-group-box">
            <div class="section-header">
                <div class="icon-wrap"><i class="fas fa-bolt"></i></div>
                <h3>การเข้าร่วมกิจกรรม</h3>
            </div>
            
            <div class="event-list">
                <?php foreach($confirmed_events as $ev): ?>
                <div class="luxury-card" style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div style="font-weight:700; font-size:1.15rem; color:var(--text-title); margin-bottom:4px;">
                            <?php echo htmlspecialchars($ev['event_name']); ?>
                        </div>
                        <div style="font-size:0.85rem; color:var(--text-light); font-weight:600;">
                            <i class="far fa-calendar-check me-2"></i> เข้าร่วมเมื่อวันที่ <?php echo formatThaiDate($ev['event_date']); ?>
                           
                        </div>
                        
                    </div>
                    <div class="badge badge-Approved">
                        <i class="fas fa-check-double"></i> สำเร็จแล้ว
                    </div>
                </div>
                <?php endforeach; if(empty($confirmed_events)) echo "<p class='text-center py-4'>ยังไม่มีประวัติการเข้าร่วม</p>"; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="content-group-box">
            <div class="section-header">
                <div class="icon-wrap"><i class="fas fa-heart"></i></div>
                <h3>ประวัติการบริจาค</h3>
            </div>

            <div class="donation-list">
                <?php foreach($my_donations as $don): ?>
                <div class="luxury-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <span style="font-family:'Plus Jakarta Sans'; font-weight:800; color:var(--brand); font-size:0.85rem; background:var(--brand-soft); padding:4px 12px; border-radius:8px;">#DON-<?php echo $don['donation_id']; ?></span>
                            <h4 style="margin:16px 0 6px; font-size:1.3rem; font-weight:800; color:var(--text-title); letter-spacing:-0.5px;"><?php echo htmlspecialchars($don['event_name'] ?: 'บริจาคทั่วไป'); ?></h4>
                            <p style="margin:0; font-size:0.85rem; color:var(--text-light); font-weight:600;"><i class="far fa-clock me-1"></i> <?php echo formatThaiDate($don['donation_date']); ?></p>
                        </div>
                        <div class="badge badge-<?php echo $don['status']; ?>">
                            <i class="fas <?php echo $don['status'] == 'Approved' ? 'fa-certificate' : ($don['status'] == 'Rejected' ? 'fa-circle-xmark' : 'fa-hourglass-half'); ?>"></i>
                            <?php echo $don['status_text']; ?>
                        </div>
                    </div>

                    <div class="item-row">
                        <?php foreach($don['items'] as $it): ?>
                            <div class="chip">
                                <?php echo htmlspecialchars($it['item_name']); ?> <b>x<?php echo $it['quantity']; ?></b>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if(!empty($don['image_path_1']) || !empty($don['image_path_2'])): ?>
                    <div class="proof-gallery">
                        <?php if($don['image_path_1']): ?>
                            <img src="<?php echo $don['image_path_1']; ?>" class="proof-img" onclick="zoomImage(this.src)">
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; if(empty($my_donations)) echo "<p class='text-center py-4'>ยังไม่มีประวัติการบริจาค</p>"; ?>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function zoomImage(url) {
        Swal.fire({
            imageUrl: url,
            imageWidth: 'auto',
            imageAlt: 'Proof',
            showConfirmButton: false,
            background: 'rgba(255,255,255,0.9)',
            backdrop: `blur(15px)`,
            showCloseButton: true,
            customClass: { popup: 'rounded-[32px] border-none shadow-none' }
        });
    }

    <?php if(isset($_GET['success'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: 'อัปเดตรูปโปรไฟล์เรียบร้อยแล้ว',
        timer: 2000,
        showConfirmButton: false,
        customClass: { popup: 'rounded-[24px]' }
    });
    <?php endif; ?>
</script>
<?php include 'footer.php'; ?>
</body>
</html>