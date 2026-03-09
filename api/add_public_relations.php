<?php
session_start(); // 1. ต้องมีบรรทัดนี้เพื่อดึงค่าจากการล็อกอิน

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

// เช็คว่าล็อกอินหรือยัง? ถ้ายังให้ดีดไปหน้า Login (ป้องกัน Error)
if (!isset($_SESSION['user_id'])) {
    die("กรุณาล็อกอินก่อนใช้งานครับ <a href='login.php'>ไปหน้าล็อกอิน</a>");
}

// 1. ดึงรายชื่อกิจกรรมมาแสดงใน Dropdown
$sql_get_events = "SELECT event_id, event_name FROM events WHERE is_active = 1 ORDER BY event_date DESC";
$events_result = $conn->query($sql_get_events);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $detail = $_POST['detail'];
    
    // 2. แก้จากเลข 1 เป็น ID ของคนที่ล็อกอินอยู่จริง
    $user_id = $_SESSION['user_id']; 

    // --- ส่วนบันทึกข้อมูลข่าว ---
    $sql_news = "INSERT INTO news_update (user_id, event_id, title, detail, status) VALUES (?, ?, ?, ?, 1)";
    $stmt_news = $conn->prepare($sql_news);
    $stmt_news->bind_param("iiss", $user_id, $event_id, $title, $detail);

    if ($stmt_news->execute()) {
        $inserted_id = $conn->insert_id; 
        
        // --- ส่วนบันทึกรูปภาพ ---
        $upload_dir = "img/";
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

        if (!empty($_FILES['images']['name'][0])) {
            $sql_img = "INSERT INTO news_images (news_id, image_path) VALUES (?, ?)";
            $stmt_img = $conn->prepare($sql_img);

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $new_name = "news_" . uniqid() . "_" . $key . "." . $ext;
                    $target = $upload_dir . $new_name;

                    if (move_uploaded_file($tmp_name, $target)) {
                        $stmt_img->bind_param("is", $inserted_id, $new_name);
                        $stmt_img->execute();
                    }
                }
            }
        }
        echo "<script>alert('บันทึกข่าวเรียบร้อย!'); window.location='index.php';</script>";
    } else {
        die("บันทึกไม่สำเร็จเพราะ: " . $stmt_news->error);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข่าวประชาสัมพันธ์ - Admin Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Anuphan:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { --primary-color: #1e293b; --accent-color: #3b82f6; }
        body { font-family: 'Anuphan', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .admin-card { 
            background: white; 
            border-radius: 24px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.05); 
            border: 1px solid #e2e8f0; 
            padding: 45px; 
            margin-top: 40px; 
        }
        .form-label { font-weight: 600; color: #475569; margin-bottom: 8px; }
        .form-control, .form-select { 
            border-radius: 12px; 
            padding: 12px; 
            border: 1.5px solid #e2e8f0; 
            transition: 0.3s;
        }
        .btn-main { 
            background: var(--primary-color); 
            color: white; 
            border-radius: 12px; 
            padding: 15px; 
            border: none; 
            transition: 0.3s; 
            font-size: 1.1rem;
        }
        .btn-main:hover { background: #000; transform: translateY(-2px); color: white; }
        .preview-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); 
            gap: 15px; 
            margin-top: 15px; 
        }
        .preview-item { 
            border: 2px dashed #cbd5e1; 
            border-radius: 16px; 
            padding: 15px; 
            text-align: center; 
            background: #f1f5f9; 
            position: relative;
        }
        .preview-item img { width: 100%; height: 110px; object-fit: cover; border-radius: 10px; display: none; margin-bottom: 10px; }
        .placeholder-icon { font-size: 28px; color: #94a3b8; margin-bottom: 8px; display: block; }
    </style>
</head>
<body>
<?php include 'menu_admin.php'; ?>
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="admin-card">
                <div class="text-center mb-5">
                    <h2 style="font-weight: 700; letter-spacing: -1px;">
                        <i class="fas fa-bullhorn me-3 text-primary"></i>เผยแพร่ข่าวประชาสัมพันธ์
                    </h2>
                    <p class="text-muted">สร้างข่าวสารและประมวลภาพกิจกรรมได้ไม่จำกัดรูปภาพ</p>
                </div>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-primary"><i class="fas fa-link me-2"></i>เลือกกิจกรรมที่เกี่ยวข้อง</label>
                            <select name="event_id" class="form-select" required>
                                <option value="">-- เลือกกิจกรรมอาสา --</option>
                                <?php 
                                if ($events_result && $events_result->num_rows > 0) {
                                    while($ev = $events_result->fetch_assoc()) {
                                        echo "<option value='".$ev['event_id']."'>📌 ".htmlspecialchars($ev['event_name'])."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">หัวข้อข่าวสาร</label>
                            <input type="text" name="title" class="form-control" required placeholder="เช่น สรุปกิจกรรมอาสา">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">รายละเอียดเนื้อหา</label>
                        <textarea name="detail" class="form-control" rows="5" placeholder="อธิบายเนื้อหาข่าว..."></textarea>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label text-primary fw-bold mb-0">
                                <i class="fas fa-images me-2"></i>คลังภาพกิจกรรม
                            </label>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addMoreImage()">
                                <i class="fas fa-plus me-1"></i> เพิ่มช่องรูปภาพ
                            </button>
                        </div>
                        
                        <div class="preview-grid" id="image-container">
                            <div class="preview-item">
                                <i class="fas fa-cloud-upload-alt placeholder-icon" id="icon-0"></i>
                                <img id="img-0" src="">
                                <input type="file" name="images[]" class="form-control form-control-sm" 
                                       accept="image/*" onchange="preview(this, 0)">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-main w-100 fw-bold">
                                <i class="fas fa-paper-plane me-2"></i> บันทึกข้อมูลและเผยแพร่ข่าว
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="index.php" class="btn btn-outline-secondary w-100" style="border-radius: 12px; padding: 15px;">ยกเลิก</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let imageCount = 1;
function addMoreImage() {
    const container = document.getElementById('image-container');
    const div = document.createElement('div');
    div.className = 'preview-item';
    div.innerHTML = `
        <i class="fas fa-cloud-upload-alt placeholder-icon" id="icon-${imageCount}"></i>
        <img id="img-${imageCount}" src="">
        <input type="file" name="images[]" class="form-control form-control-sm" 
               accept="image/*" onchange="preview(this, ${imageCount})">
    `;
    container.appendChild(div);
    imageCount++;
}

function preview(input, index) {
    const file = input.files[0];
    const previewImg = document.getElementById('img-' + index);
    const icon = document.getElementById('icon-' + index);
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            icon.style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}
</script>
</body>
</html>