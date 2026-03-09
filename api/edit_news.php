<?php
session_start();
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['user_id'])) { die("กรุณาล็อกอินก่อนครับ"); }

// 1. รับ ID ข่าวที่ต้องการแก้ไข
$news_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. ดึงข้อมูลข่าวเดิม
$sql_news = "SELECT * FROM news_update WHERE news_id = ?";
$stmt = $conn->prepare($sql_news);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$news_data = $stmt->get_result()->fetch_assoc();

if (!$news_data) { die("ไม่พบข้อมูลข่าวสาร"); }

// 3. ดึงรูปภาพเดิมของข่าวนี้
$sql_imgs = "SELECT * FROM news_images WHERE news_id = ?";
$stmt_imgs = $conn->prepare($sql_imgs);
$stmt_imgs->bind_param("i", $news_id);
$stmt_imgs->execute();
$old_images = $stmt_imgs->get_result()->fetch_all(MYSQLI_ASSOC);

// 4. ดึงรายชื่อกิจกรรม (เหมือนหน้าเพิ่ม)
$events_result = $conn->query("SELECT event_id, event_name FROM events WHERE is_active = 1");

// --- Logic เมื่อกดปุ่ม "บันทึกการแก้ไข" ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $detail = $_POST['detail'];

    // อัปเดตเนื้อหาข่าว
    $sql_update = "UPDATE news_update SET event_id=?, title=?, detail=? WHERE news_id=?";
    $stmt_up = $conn->prepare($sql_update);
    $stmt_up->bind_param("issi", $event_id, $title, $detail, $news_id);
    
    if ($stmt_up->execute()) {
        // จัดการรูปภาพใหม่ (ถ้ามีการอัปโหลดเพิ่ม)
        $upload_dir = "img/";
        if (!empty($_FILES['images']['name'][0])) {
            $sql_ins_img = "INSERT INTO news_images (news_id, image_path) VALUES (?, ?)";
            $stmt_ins_img = $conn->prepare($sql_ins_img);

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $new_name = "news_" . uniqid() . "_" . $key . "." . $ext;
                    if (move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                        $stmt_ins_img->bind_param("is", $news_id, $new_name);
                        $stmt_ins_img->execute();
                    }
                }
            }
        }
        echo "<script>alert('แก้ไขข้อมูลเรียบร้อย!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข่าวประชาสัมพันธ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ใช้สไตล์เดียวกับหน้าเพิ่มที่คุณส่งมา */
        body { font-family: 'Anuphan', sans-serif; background-color: #f8fafc; }
        .admin-card { background: white; border-radius: 24px; padding: 40px; margin-top: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .old-img-card { position: relative; display: inline-block; margin: 5px; }
        .old-img-card img { width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border: 2px solid #e2e8f0; }
        .btn-delete-img { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 7px; font-size: 12px; cursor: pointer; text-decoration: none; }
        .preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="admin-card">
                <h2 class="text-center mb-4"><i class="fas fa-edit me-2"></i> แก้ไขข่าวประชาสัมพันธ์</h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">กิจกรรมที่เกี่ยวข้อง</label>
                            <select name="event_id" class="form-select" required>
                                <?php while($ev = $events_result->fetch_assoc()): ?>
                                    <option value="<?= $ev['event_id'] ?>" <?= ($ev['event_id'] == $news_data['event_id']) ? 'selected' : '' ?>>
                                        📌 <?= htmlspecialchars($ev['event_name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">หัวข้อข่าวสาร</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($news_data['title']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รายละเอียดเนื้อหา</label>
                        <textarea name="detail" class="form-control" rows="5"><?= htmlspecialchars($news_data['detail']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block text-secondary"><i class="fas fa-images me-2"></i>รูปภาพปัจจุบัน (สามารถลบได้)</label>
                        <div class="p-3 border rounded bg-light">
                            <?php foreach ($old_images as $img): ?>
                                <div class="old-img-card" id="img-container-<?= $img['image_id'] ?>">
                                    <img src="img/<?= $img['image_path'] ?>">
                                    <a href="delete_img.php?img_id=<?= $img['image_id'] ?>&news_id=<?= $news_id ?>" 
                                       onclick="return confirm('ยืนยันลบรูปนี้?')" class="btn-delete-img">×</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <label class="form-label text-primary fw-bold">เพิ่มรูปภาพใหม่</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMoreImage()">+ เพิ่มช่อง</button>
                        </div>
                        <div class="preview-grid" id="image-container">
                            <div class="preview-item border p-2 text-center rounded bg-white">
                                <input type="file" name="images[]" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-dark w-100 p-3 rounded-3">บันทึกการเปลี่ยนแปลง</button>
                        </div>
                        <div class="col-md-4">
                            <a href="index.php" class="btn btn-outline-secondary w-100 p-3 rounded-3">ยกเลิก</a>
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
    div.className = 'preview-item border p-2 text-center rounded bg-white';
    div.innerHTML = `<input type="file" name="images[]" class="form-control form-control-sm" accept="image/*">`;
    container.appendChild(div);
    imageCount++;
}
</script>
</body>
</html>