<?php
// ---------------- เชื่อมต่อฐานข้อมูล ----------------
$servername = "147.50.254.50"; $username = "admin_itpsru"; $password = "azdhhkVFWpWv7LBZqUju"; $dbname = "admin_itpsru";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error); }

$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $province = $_POST['province'];
    $address = $_POST['address'];
    $role = $_POST['role']; 

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, first_name, last_name, phone, province, address, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $user, $pass, $email, $first_name, $last_name, $phone, $province, $address, $role);
    
    if ($stmt->execute()) {
        header("Location: login.php?status=success");
        exit();
    } else { $error_message = "เกิดข้อผิดพลาด: " . $conn->error; }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างบัญชีใหม่ | ประสบการณ์ระดับพรีเมียม</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700&family=Noto+Sans+Thai:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --text-main: #1f2937;
            --text-sub: #6b7280;
            --bg-gray: #f9fafb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Thai', sans-serif;
            background-color: #fff;
            min-height: 100vh;
        }

        .split-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .content-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: #fff;
        }

        .form-container {
            width: 100%;
            max-width: 550px; /* ขยายกว้างขึ้นเล็กน้อยเพื่อรองรับการวางแบบคู่ */
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header { margin-bottom: 25px; }
        .form-header h1 {
            font-size: 30px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }
        .form-header p {
            font-size: 15px;
            color: var(--text-sub);
        }

        .input-group { margin-bottom: 18px; }
        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-main);
            transition: all 0.3s ease;
            background: var(--bg-gray);
            font-family: inherit;
        }

        textarea { resize: none; }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
        }

        .row { display: flex; gap: 16px; margin-bottom: 0; }
        .row .input-group { flex: 1; }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.4s;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(37, 99, 235, 0.2);
        }

        .login-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--text-sub);
        }
        .login-text a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .image-side {
            flex: 1;
            position: relative;
            display: block;
        }

        .image-bg {
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1500&q=80') center/cover no-repeat;
        }

        @media (max-width: 1024px) {
            .image-side { display: none; }
            .content-side { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="split-container">
        <section class="content-side">
            <div class="form-container">
                <header class="form-header">
                    <h1>สมัครสมาชิก</h1>
                    <p>ร่วมเป็นส่วนหนึ่งในสังคมแห่งการแบ่งปันระดับพรีเมียม</p>
                </header>

                <form method="POST">
                    <div class="input-group">
                        <label>คุณต้องการเข้าร่วมในฐานะ?</label>
                        <select name="role">
                            <option value="user">ผู้บริจาค </option>
                            <option value="volunteer">อาสาสมัคร </option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="input-group">
                            <label>ชื่อผู้ใช้งาน (Username)</label>
                            <input type="text" name="username" placeholder="Username" required>
                        </div>
                        <div class="input-group">
                            <label>อีเมล</label>
                            <input type="email" name="email" placeholder="email@email.com" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>รหัสผ่าน</label>
                        <input type="password" name="password" placeholder="อย่างน้อย 8 ตัวอักษร" required>
                    </div>

                    <div class="row">
                        <div class="input-group">
                            <label>ชื่อจริง</label>
                            <input type="text" name="first_name" placeholder="ชื่อ" required>
                        </div>
                        <div class="input-group">
                            <label>นามสกุล</label>
                            <input type="text" name="last_name" placeholder="นามสกุล" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-group">
                            <label>เบอร์โทรศัพท์</label>
                            <input type="text" name="phone" placeholder="08XXXXXXXX" required>
                        </div>
                        <div class="input-group">
                            <label>จังหวัด</label>
                            <select name="province" required>
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

                    <div class="input-group">
                        <label>รายละเอียดที่อยู่ (บ้านเลขที่, ถนน, ตำบล, อำเภอ)</label>
                        <textarea name="address" rows="3" placeholder="กรอกที่อยู่ปัจจุบันของคุณสำหรับจัดส่งของบริจาค" required></textarea>
                    </div>

                    <button type="submit" class="btn-primary">ยืนยันการลงทะเบียน</button>
                </form>

                <p class="login-text">
                    มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a>
                </p>
            </div>
        </section>

        <section class="image-side">
            <div class="image-bg"></div>
        </section>
    </div>

</body>
</html>