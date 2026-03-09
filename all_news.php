<?php
session_start();
// --- 1. ส่วนเชื่อมต่อฐานข้อมูล (คงเดิม) ---
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");


// --- 2. ฟังก์ชันจัดการหมวดหมู่ (คงเดิม) ---
function get_clean_group($cat_raw) {
    $cat = trim($cat_raw);
    if (mb_strpos($cat, 'ยาสามัญ') !== false) return 'ยาสามัญประจำบ้าน';
    if (mb_strpos($cat, 'เสื้อผ้า') !== false) return 'เสื้อผ้ามือสอง';
    if (mb_strpos($cat, 'ข้าวสาร') !== false || mb_strpos($cat, 'อาหาร') !== false) return 'ข้าวสารและอาหารแห้ง';
    if (mb_strpos($cat, 'เรียน') !== false || mb_strpos($cat, 'การเรียน') !== false) return 'อุปกรณ์การเรียน';
    if (mb_strpos($cat, 'กีฬา') !== false) return 'อุปกรณ์กีฬา';
    if (mb_strpos($cat, 'ของเล่น') !== false || mb_strpos($cat, 'ตุ๊กตา') !== false) return 'ของเล่นและตุ๊กตา';
    if (mb_strpos($cat, 'ทำความสะอาด') !== false) return 'ผลิตภัณฑ์ทำความสะอาด';
    if (mb_strpos($cat, 'ภายในบ้าน') !== false) return 'เครื่องใช้ภายในบ้าน';
    if (mb_strpos($cat, 'ขนม') !== false) return 'ขนมและของว่าง';
    return trim(preg_replace('/^[0-9]+\.\s?/', '', $cat));
}

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

// --- 3. ดึงข้อมูลและประมวลผล (คงเดิม) ---
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

// --- 4. การเรียงลำดับให้เหมือนรูปภาพ (คงเดิม) ---
$defined_order = [
    'ข้าวสารและอาหารแห้ง',
    'เครื่องใช้ภายในบ้าน',
    'ผลิตภัณฑ์ทำความสะอาด',
    'ของเล่นและตุ๊กตา',
    'อุปกรณ์กีฬา',
    'อุปกรณ์การเรียน',
    'เสื้อผ้ามือสอง',
    'ยาสามัญประจำบ้าน'
];

$all_names = []; $all_rec = []; $all_tar = []; $all_colors = [];
foreach($defined_order as $name) {
    if (isset($temp_groups[$name])) {
        $all_names[] = $name;
        $all_rec[] = $temp_groups[$name]['t_rec'];
        $all_tar[] = $temp_groups[$name]['t_tar'];
        $all_colors[] = getColorMapping($name);
    }
}

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
    <title>ข่าวประชาสัมพันธ์ทั้งหมด - เราทำอะไรบ้าง</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600&family=Sarabun:wght@700&display=swap" rel="stylesheet">
    
 <style>
        :root {
            --primary-color: #00507b;
            --secondary-color: #00796b;
            --accent-color: #c45b00;
            --bg-light: #f4fbfd;
            --text-dark: #2c3e50;
            --text-muted: #475569;
            --white: #ffffff;
            --shadow-premium: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        body {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(0, 80, 123, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(196, 91, 0, 0.03) 0px, transparent 50%);
            background-attachment: fixed;
            font-family: 'IBM Plex Sans Thai', sans-serif;
            color: var(--text-dark);
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* --- Header Section --- */
        .header-box {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.4rem;
            font-family: 'Sarabun', sans-serif;
            color: #152d6d;
            margin-bottom: 10px;
        }

        .underline {
            width: 70px;
            height: 5px;
            background: var(--accent-color);
            margin: 0 auto 25px;
            border-radius: 10px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            text-decoration: none;
            color: #475569;
            background: #fff;
            border-radius: 12px;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #f1f5f9;
            transform: translateX(-5px);
        }

        /* --- Grid & Card --- */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px 25px;
        }

        .card {
            background: var(--white);
            border-radius: 30px;
            padding: 20px;
            box-shadow: var(--shadow-premium);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.02);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        /* --- Facebook Style Image Gallery --- */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            margin-bottom: 20px;
            cursor: pointer;
            border-radius: 20px;
            overflow: hidden;
        }

        .image-gallery img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        /* ถ้ามี 3 รูปขึ้นไป ให้รูปแรกกว้างเต็ม */
        .gallery-3plus img:first-child {
            grid-column: span 2;
            height: 200px;
        }

        /* ถ้ามีรูปเดียว */
        .gallery-single img {
            grid-column: span 2;
            height: 240px;
        }

        .more-photos-overlay {
            position: relative;
            height: 120px;
        }

        .more-photos-overlay::after {
            content: attr(data-count);
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        /* --- Card Content --- */
        .card-content {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .event-tag {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 10px;
            font-weight: 500;
        }

        .card-content h3 {
            color: #92400e;
            font-size: 19px;
            font-family: 'Sarabun', sans-serif;
            margin-bottom: 12px;
            line-height: 1.4;
            min-height: 54px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-content p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-footer {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
            font-size: 13px;
            color: #94a3b8;
        }

        /* --- Modal Popup --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.95);
            overflow-y: auto;
            padding: 50px 20px;
        }

        .modal-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            padding: 30px;
            position: relative;
        }

        .modal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }

        .modal-grid img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .close-modal {
            position: fixed;
            top: 25px; right: 35px;
            color: white;
            font-size: 35px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .close-modal:hover { transform: scale(1.2); }

        

        /* Responsive */
        @media (max-width: 1024px) { .card-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .card-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<?php include 'menu_volunteer.php'; ?>

<div class="container">
    <div class="header-box">
        <div class="section-title">
            <h2>ข่าวประชาสัมพันธ์ทั้งหมด</h2>
            <div class="underline"></div>
        </div>
        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> กลับหน้าหลัก
        </a>
    </div>

    <div class="card-grid">
        <?php
        $sql_pr = "SELECT pr.*, e.event_name 
                   FROM news_update pr
                   LEFT JOIN events e ON pr.event_id = e.event_id 
                   WHERE pr.status = 1 
                   ORDER BY pr.news_id DESC";

        $result_pr = $conn->query($sql_pr);

        if ($result_pr && $result_pr->num_rows > 0) {
            while ($row = $result_pr->fetch_assoc()) {
                $current_id = $row['news_id'];

                // 🎯 ดึงรูปภาพทั้งหมดของข่าวนี้
                $sql_imgs = "SELECT image_path FROM news_images WHERE news_id = $current_id";
                $res_imgs = $conn->query($sql_imgs);
                $img_list = [];
                while($img_row = $res_imgs->fetch_assoc()) {
                    $img_list[] = 'img/' . $img_row['image_path'];
                }

                $total_imgs = count($img_list);
                $display_limit = 5; // โชว์ใน Card สูงสุด 5 รูป
                
                // ตัดสินใจเลือก Layout Class
                $layout_class = 'gallery-single';
                if ($total_imgs >= 3) $layout_class = 'gallery-3plus';
                else if ($total_imgs == 2) $layout_class = 'gallery-2';
        ?>

        <div class="card">
            <div class="image-gallery <?php echo $layout_class; ?>" 
                 onclick='openPhotoModal(<?php echo json_encode($img_list); ?>)'>
                
                <?php if ($total_imgs > 0): ?>
                    <?php foreach (array_slice($img_list, 0, $display_limit) as $index => $src): ?>
                        
                        <?php if ($index === 4 && $total_imgs > 5): ?>
                            <div class="more-photos-overlay" data-count="+<?php echo ($total_imgs - 4); ?>">
                                <img src="<?php echo $src; ?>">
                            </div>
                        <?php else: ?>
                            <img src="<?php echo $src; ?>" alt="รูปกิจกรรม">
                        <?php endif; ?>

                    <?php endforeach; ?>
                <?php else: ?>
                    <img src="img/default.jpg" alt="ไม่มีรูปภาพ">
                <?php endif; ?>
            </div>

            <div class="card-content">
                <span class="event-tag">
                    <i class="fas fa-tag"></i>
                    <?php echo !empty($row['event_name']) ? htmlspecialchars($row['event_name']) : 'กิจกรรมทั่วไป'; ?>
                </span>

                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['detail']); ?></p>
                
                <div class="card-footer">
                    <i class="far fa-calendar-alt"></i> 
                    <?php echo date('d/m/Y', strtotime($row['created_at'] ?? 'now')); ?>
                </div>
            </div>
        </div>

        <?php
            }
        } else {
            echo "<p style='text-align:center; grid-column: span 3;'>ยังไม่มีข่าวประชาสัมพันธ์ในขณะนี้</p>";
        }
        ?>
    </div>
</div>

<div id="photoModal" class="modal">
    <span class="close-modal" onclick="closePhotoModal()">&times;</span>
    <div class="modal-container">
        <h2 style="font-family:'Sarabun'; color:#152d6d; margin-bottom:25px; text-align:center;">รูปภาพกิจกรรมทั้งหมด</h2>
        <div id="modalGrid" class="modal-grid"></div>
    </div>
</div>

<script>
function openPhotoModal(images) {
    if (!images || images.length === 0) return;
    
    const modal = document.getElementById('photoModal');
    const grid = document.getElementById('modalGrid');
    
    grid.innerHTML = ''; // ล้างค่าเก่า
    
    images.forEach(src => {
        const imgDiv = document.createElement('div');
        imgDiv.innerHTML = `<img src="${src}" alt="รูปกิจกรรม">`;
        grid.appendChild(imgDiv);
    });
    
    modal.style.display = "block";
    document.body.style.overflow = "hidden"; // ปิดสกรอลล์หน้าหลัก
}

function closePhotoModal() {
    document.getElementById('photoModal').style.display = "none";
    document.body.style.overflow = "auto";
}

// ปิดเมื่อคลิกนอกกล่อง
window.onclick = function(event) {
    const modal = document.getElementById('photoModal');
    if (event.target == modal) closePhotoModal();
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>