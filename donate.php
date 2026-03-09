<?php
session_start();

/* ===== ฟังก์ชันบังคับล็อกอิน ===== */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

/* ===== เรียกใช้ฟังก์ชัน (ต้องอยู่บนสุด) ===== */
requireLogin();

/* ===== เชื่อมต่อฐานข้อมูล ===== */
$conn = new mysqli("147.50.254.50", "admin_itpsru", "azdhhkVFWpWv7LBZqUju", "admin_itpsru");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

/* ===== User ID จาก Session ===== */
$user_id = $_SESSION['user_id'];

/* ===== ดึงรายการกิจกรรมที่กำลังเปิดใช้งาน ===== */
$events = $conn->query("SELECT event_id, event_name FROM events WHERE is_active = 1");

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
    <title>Premium Donation System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anuphan:wght@300;400;500;600;700&family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <style>

        :root {
            --primary-grad: linear-gradient(135deg, #054996 0%, #1021b9 100%);
            --body-bg: #f8fafc;
            --text-main: #1e293b;
            --glass: rgba(255, 255, 255, 0.95);
            --radius-custom: 24px;
        }

        body { 
            font-family: 'Anuphan', sans-serif; 
            background: var(--body-bg);
            background-image: radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%);
            min-height: 100vh;
            color: var(--text-main);
        }
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
        /* Typography */
        .hero-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: clamp(2.5rem, 6vw, 4rem);
            background: var(--primary-grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -2px;
        }

        /* Container & Cards */
        .master-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-custom);
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            padding: 3.5rem !important;
        }

        .section-step {
            margin-bottom: 3.5rem;
            position: relative;
        }

        .step-badge {
            background: var(--primary-grad);
            color: white;
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; font-weight: 700; margin-right: 12px;
            box-shadow: 0 4px 10px rgba(16, 61, 185, 0.3);
        }

        /* Form Elements */
        .fancy-select {
            border: 2px solid #e2e8f0;
            border-radius: 18px;
            padding: 1.1rem 1.5rem;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fancy-select:focus {
            border-color: #1067b9;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            transform: translateY(-2px);
        }

        /* Item Grid UI */
        #items-container { min-height: 200px; }
        
        .item-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            padding: 20px;
            transition: all 0.3s ease;
        }
        .item-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.05);
            border-color: #101bb9;
        }

        .premium-qty {
            display: flex; background: #f8fafc;
            border-radius: 14px; padding: 4px;
            border: 1px solid #e2e8f0;
        }

        .btn-qty {
            width: 36px; height: 36px; border: none;
            border-radius: 10px; background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: 0.2s;
        }
        .btn-qty:hover { background: #1e293b; color: white; }

        /* Upload Zone */
        .modern-upload {
            border: 2px dashed #cbd5e1;
            border-radius: var(--radius-custom);
            padding: 4rem 2rem;
            text-align: center;
            background: #fbfcfd;
            cursor: pointer;
            transition: 0.3s;
        }
        .modern-upload:hover {
            border-color: #1045b9;
            background: #f0fdf4;
        }

        #preview-img { 
            max-width: 100%; height: auto; border-radius: 18px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        /* Action Button */
        .glow-button {
            background: var(--primary-grad);
            border: none;
            border-radius: 18px;
            color: white; padding: 1.25rem;
            font-weight: 700; font-size: 1.1rem;
            box-shadow: 0 10px 20px rgba(16, 103, 185, 0.3);
            transition: 0.3s;
        }
        .glow-button:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(16, 103, 185, 0.4);
        }

        
        
    </style>
</head>
<body>
<?php include 'menu_volunteer.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-11">
            
            <header class="text-center mb-5 animate__animated animate__fadeIn">
                <h1 class="hero-title mb-2">บริจาคสิ่งของ</h1>
                <p class="text-secondary fs-5 fw-light">เปลี่ยนสิ่งของของคุณเป็นรอยยิ้มของผู้อื่น</p>
            </header>

            <div class="master-card animate__animated animate__fadeInUp">
                <form id="donationForm">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">

                    <div class="section-step">
                        <h4 class="mb-4"><span class="step-badge">1</span>กิจกรรมที่เปิดรับบริจาค</h4>
                        <select class="form-select fancy-select" name="event_id" onchange="loadItems(this.value)" required>
                            <option value="">เลือกกิจกรรมที่ต้องการสนับสนุน...</option>
                            <?php while($ev = $events->fetch_assoc()): ?>
                                <option value="<?= $ev['event_id'] ?>"><?= htmlspecialchars($ev['event_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="section-step">
                        <h4 class="mb-4"><span class="step-badge">2</span>รายการสิ่งของที่เปิดรับ</h4>
                        <div id="items-container" class="row g-4">
                            <div class="col-12 text-center py-5 opacity-25">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <p>โปรดเลือกกิจกรรมเพื่อดูรายการสิ่งของที่เปิดรับ</p>
                            </div>
                        </div>
                    </div>

                    <div class="section-step">
                        <h4 class="mb-4"><span class="step-badge">3</span>ยืนยันการส่งมอบ/หลักฐานการส่งมอบ</h4>
                        <div class="modern-upload" id="upload-trigger">
                            <input type="file" id="file-input" name="donation_image_1" hidden accept="image/*" onchange="previewImage(this)" required>
                            
                            <div id="upload-placeholder">
                                <div class="bg-white d-inline-flex p-3 rounded-circle shadow-sm mb-3 text-success">
                                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                </div>
                                <h5 class="fw-bold">กดเพื่ออัปโหลดรูปภาพ</h5>
                                <p class="text-muted small mb-0">รองรับไฟล์ภาพ JPG, PNG (ไม่เกิน 5MB)</p>
                            </div>

                            <div id="preview-container" class="d-none">
                                <img id="preview-img" src="#" alt="Preview">
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="resetUpload()">
                                        <i class="fas fa-trash-alt me-2"></i>ลบรูปภาพ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn glow-button w-100">
                        <i class="fas fa-paper-plane me-2"></i> ยืนยันข้อมูลการบริจาค
                    </button>
                </form>
            </div>


        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // การจัดการรูปภาพ
    const uploadTrigger = document.getElementById('upload-trigger');
    const fileInput = document.getElementById('file-input');
    
    uploadTrigger.addEventListener('click', (e) => {
        if (e.target.tagName !== 'BUTTON' && e.target.parentElement.tagName !== 'BUTTON') {
            fileInput.click();
        }
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('upload-placeholder').classList.add('d-none');
                document.getElementById('preview-container').classList.remove('d-none');
                document.getElementById('preview-img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetUpload() {
        fileInput.value = '';
        document.getElementById('upload-placeholder').classList.remove('d-none');
        document.getElementById('preview-container').classList.add('d-none');
    }

    // โหลดรายการสิ่งของผ่าน AJAX
    async function loadItems(eventId) {
        if (!eventId) return;
        const container = document.getElementById('items-container');
        container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-success"></div></div>';

        try {
            const response = await fetch(`get_event_items.php?event_id=${eventId}`);
            const html = await response.text();
            container.innerHTML = html;
            
            // เพิ่ม Animation ให้กับ Item cards ที่โหลดมาใหม่
            container.querySelectorAll('.item-card').forEach((card, i) => {
                card.classList.add('animate__animated', 'animate__fadeInUp');
                card.style.animationDelay = `${i * 0.05}s`;
            });
        } catch (err) {
            container.innerHTML = '<div class="col-12 text-center text-danger">ไม่สามารถโหลดข้อมูลได้</div>';
        }
    }

    // จัดการปุ่ม เพิ่ม/ลด จำนวน
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-qty');
        if (!btn) return;
        const input = btn.parentElement.querySelector('input');
        if (btn.classList.contains('plus')) input.stepUp();
        if (btn.classList.contains('minus')) input.stepDown();
    });

    // ส่งฟอร์มผ่าน AJAX
    document.getElementById('donationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const originalText = btn.innerHTML;

        // UI Loading
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึกข้อมูล...';

        try {
            const response = await fetch('save_donation.php', {
                method: 'POST',
                body: new FormData(this)
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    text: data.message,
                    confirmButtonColor: '#3289b1',
                    customClass: { popup: 'rounded-4' }
                }).then(() => window.location.reload());
            } else {
                throw new Error(data.message);
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: err.message });
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".qty-box").forEach(box => {
        const minus = box.querySelector(".btn-minus");
        const plus  = box.querySelector(".btn-plus");
        const qtyEl = box.querySelector(".qty");

        plus.addEventListener("click", () => {
            let val = parseInt(qtyEl.textContent);
            qtyEl.textContent = val + 1;
        });

        minus.addEventListener("click", () => {
            let val = parseInt(qtyEl.textContent);
            if (val > 0) {
                qtyEl.textContent = val - 1;
            }
        });
    });

});
</script>
<?php include 'footer.php'; ?>
</body>
</html>