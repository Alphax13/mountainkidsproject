<?php
require 'auth.php';
checkRole(['admin']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสมาชิกใหม่ | ระบบปันสุข</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
            background-color: #f0fdf4; /* เขียวอ่อนเหมือนหน้าจัดการ */
            min-height: 100vh;
        }

        .admin-layout {
            margin-left: 260px; /* เว้นที่ให้เมนูฝั่งซ้าย */
            padding: 40px;
            transition: all 0.3s;
        }

        .form-card {
            background: white;
            border-radius: 24px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            padding: 30px;
            color: white;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .btn-submit {
            background: #059669;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: #047857;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.3);
        }

        .section-title {
            border-left: 4px solid #10b981;
            padding-left: 15px;
            margin-bottom: 25px;
            color: #1e293b;
            font-weight: 700;
        }

        @media (max-width: 1024px) {
            .admin-layout { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>

    <?php include 'menu_admin.php'; ?>

    <main class="admin-layout">
        <div class="container-fluid">
            <div class="mb-4">
                <a href="showuser.php" class="text-decoration-none text-emerald-600 fw-bold">
                    <i class="fas fa-arrow-left me-2"></i> กลับหน้าจัดการสมาชิก
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-9">
                    <div class="form-card">
                        <div class="form-header">
                            <h2 class="mb-1 fw-bold"><i class="fas fa-user-plus me-3"></i>เพิ่มสมาชิกเข้าสู่ระบบ</h2>
                            <p class="mb-0 opacity-75">กรุณากรอกข้อมูลให้ครบถ้วนเพื่อสร้างบัญชีผู้ใช้งานใหม่</p>
                        </div>

                        <form id="addUserForm" action="save_user.php" method="POST" class="p-4 p-md-5">
                            
                            <div class="section-title">ข้อมูลพื้นฐานบัญชี</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อผู้ใช้ (Username)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-at text-muted"></i></span>
                                        <input type="text" name="username" class="form-control border-start-0" placeholder="ตัวอย่าง: somchai_2024" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">รหัสผ่าน (Password)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="อย่างน้อย 6 ตัวอักษร" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อจริง (First Name)</label>
                                    <input type="text" name="first_name" class="form-control" placeholder="ระบุชื่อ" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">นามสกุล (Last Name)</label>
                                    <input type="text" name="last_name" class="form-control" placeholder="ระบุนามสกุล" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">อีเมล (Email)</label>
                                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">เบอร์โทรศัพท์ (Phone)</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="08x-xxx-xxxx" maxlength="10">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ประเภทสมาชิก (Role)</label>
                                    <select name="role" class="form-select" required>
                                        <option value="user" selected>ผู้บริจาค </option>
                                        <option value="volunteer">อาสาสมัคร</option>
                                        <option value="admin">ผู้ดูแลระบบ </option>
                                    </select>
                                </div>
                            </div>

                            <div class="section-title">ข้อมูลที่อยู่</div>
                            <div class="row g-4 mb-5">
                                <div class="col-md-12">
                                    <label class="form-label">ที่อยู่โดยละเอียด (Address)</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="เลขที่บ้าน, ถนน, แขวง/ตำบล..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">จังหวัด (Province)</label>
                                    <select name="province" class="form-select" required>
                                        <option value="" disabled selected>เลือกจังหวัด</option>
                                        <option value="">เลือกจังหวัด</option>
                                <option value="กรุงเทพมหานคร">กรุงเทพมหานคร</option>
                                <option value="กระบี่">กระบี่</option>
                                <option value="กาญจนบุรี">กาญจนบุรี</option>
                                <option value="กาฬสินธุ์">กาฬสินธุ์</option>
                                <option value="กำแพงเพชร">กำแพงเพชร</option>
                                <option value="ขอนแก่น">ขอนแก่น</option>
                                <option value="จันทบุรี">จันทบุรี</option>
                                <option value="ฉะเชิงเทรา">ฉะเชิงเทรา</option>
                                <option value="ชลบุรี">ชลบุรี</option>
                                <option value="ชัยนาท">ชัยนาท</option>
                                <option value="ชัยภูมิ">ชัยภูมิ</option>
                                <option value="ชุมพร">ชุมพร</option>
                                <option value="เชียงราย">เชียงราย</option>
                                <option value="เชียงใหม่">เชียงใหม่</option>
                                <option value="ตรัง">ตรัง</option>
                                <option value="ตราด">ตราด</option>
                                <option value="ตาก">ตาก</option>
                                <option value="นครนายก">นครนายก</option>
                                <option value="นครปฐม">นครปฐม</option>
                                <option value="นครพนม">นครพนม</option>
                                <option value="นครราชสีมา">นครราชสีมา</option>
                                <option value="นครศรีธรรมราช">นครศรีธรรมราช</option>
                                <option value="นครสวรรค์">นครสวรรค์</option>
                                <option value="นนทบุรี">นนทบุรี</option>
                                <option value="นราธิวาส">นราธิวาส</option>
                                <option value="น่าน">น่าน</option>
                                <option value="บึงกาฬ">บึงกาฬ</option>
                                <option value="บุรีรัมย์">บุรีรัมย์</option>
                                <option value="ปทุมธานี">ปทุมธานี</option>
                                <option value="ประจวบคีรีขันธ์">ประจวบคีรีขันธ์</option>
                                <option value="ปราจีนบุรี">ปราจีนบุรี</option>
                                <option value="ปัตตานี">ปัตตานี</option>
                                <option value="พระนครศรีอยุธยา">พระนครศรีอยุธยา</option>
                                <option value="พะเยา">พะเยา</option>
                                <option value="พังงา">พังงา</option>
                                <option value="พัทลุง">พัทลุง</option>
                                <option value="พิจิตร">พิจิตร</option>
                                <option value="พิษณุโลก">พิษณุโลก</option>
                                <option value="เพชรบุรี">เพชรบุรี</option>
                                <option value="เพชรบูรณ์">เพชรบูรณ์</option>
                                <option value="แพร่">แพร่</option>
                                <option value="ภูเก็ต">ภูเก็ต</option>
                                <option value="มหาสารคาม">มหาสารคาม</option>
                                <option value="มุกดาหาร">มุกดาหาร</option>
                                <option value="แม่ฮ่องสอน">แม่ฮ่องสอน</option>
                                <option value="ยโสธร">ยโสธร</option>
                                <option value="ยะลา">ยะลา</option>
                                <option value="ร้อยเอ็ด">ร้อยเอ็ด</option>
                                <option value="ระนอง">ระนอง</option>
                                <option value="ระยอง">ระยอง</option>
                                <option value="ราชบุรี">ราชบุรี</option>
                                <option value="ลพบุรี">ลพบุรี</option>
                                <option value="ลำปาง">ลำปาง</option>
                                <option value="ลำพูน">ลำพูน</option>
                                <option value="เลย">เลย</option>
                                <option value="ศรีสะเกษ">ศรีสะเกษ</option>
                                <option value="สกลนคร">สกลนคร</option>
                                <option value="สงขลา">สงขลา</option>
                                <option value="สตูล">สตูล</option>
                                <option value="สมุทรปราการ">สมุทรปราการ</option>
                                <option value="สมุทรสงคราม">สมุทรสงคราม</option>
                                <option value="สมุทรสาคร">สมุทรสาคร</option>
                                <option value="สระแก้ว">สระแก้ว</option>
                                <option value="สระบุรี">สระบุรี</option>
                                <option value="สิงห์บุรี">สิงห์บุรี</option>
                                <option value="สุโขทัย">สุโขทัย</option>
                                <option value="สุพรรณบุรี">สุพรรณบุรี</option>
                                <option value="สุราษฎร์ธานี">สุราษฎร์ธานี</option>
                                <option value="สุรินทร์">สุรินทร์</option>
                                <option value="หนองคาย">หนองคาย</option>
                                <option value="หนองบัวลำภู">หนองบัวลำภู</option>
                                <option value="อ่างทอง">อ่างทอง</option>
                                <option value="อำนาจเจริญ">อำนาจเจริญ</option>
                                <option value="อุดรธานี">อุดรธานี</option>
                                <option value="อุตรดิตถ์">อุตรดิตถ์</option>
                                <option value="อุทัยธานี">อุทัยธานี</option>
                                <option value="อุบลราชธานี">อุบลราชธานี</option>
                                        </select>
                                </div>
                            </div>

                            <div class="border-top pt-4 d-flex justify-content-between align-items-center">
                                <button type="reset" class="btn btn-light text-muted px-4 rounded-3">ล้างข้อมูล</button>
                                <button type="submit" class="btn btn-submit text-white px-5">
                                    <i class="fas fa-save me-2"></i> บันทึกข้อมูลสมาชิก
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            
            // ตรวจสอบความยาวรหัสผ่านเบื้องต้น
            if (password.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'รหัสผ่านสั้นเกินไป',
                    text: 'กรุณาตั้งรหัสผ่านอย่างน้อย 6 ตัวอักษร',
                    confirmButtonColor: '#10b981'
                });
                return;
            }

            // แสดง Loading
            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                html: 'โปรดรอสักครู่',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // ส่งฟอร์ม (ในที่นี้คือการ Submit ไปยังไฟล์ PHP จริง)
            // หมายเหตุ: ในระบบจริงจะใช้ Fetch API หรือปล่อยให้ Submit ปกติก็ได้
            this.submit();
        });

        // แจ้งเตือนเมื่อล้างข้อมูล
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'ล้างข้อมูลเรียบร้อยแล้ว',
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>