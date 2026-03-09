<?php
session_start();
// ตรวจสอบว่ามี Session ของ Admin หรือไม่
// แก้ไข 'role' และ 'admin' ให้ตรงกับที่คุณเก็บใน Database/ตอน Login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // ถ้าไม่มีสิทธิ์ ให้ส่งไปหน้า Login พร้อมส่งข้อความแจ้งเตือน (ถ้ามี)
    header("Location: login.php?error=no_permission");
    exit();
}

require 'auth.php';


// ถ้าผ่านเงื่อนไขด้านบน แสดงว่าคือ Admin... โค้ดข้างล่างนี้จะทำงานปกติ
$current_page = basename($_SERVER['PHP_SELF']);


$servername = "147.50.254.50"; $username = "admin_itpsru"; $password = "azdhhkVFWpWv7LBZqUju"; $dbname = "admin_itpsru";
$conn = new mysqli($servername, $username, $password, $dbname); 
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

$sql = "SELECT id, username, email, first_name, last_name, phone, province, address, role, created_at FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสมาชิก | ระบบปันสุข</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600;700&family=Sarabun:wght@400;600;700&display=swap');
        body { font-family: 'IBM Plex Sans Thai', sans-serif; background-color: #f0fdf4; margin: 0; }
        .admin-main-content { margin-left: 260px; padding: 40px; }
        .content-card { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        #userModal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: white; padding: 0; border-radius: 24px; max-width: 550px; width: 90%; position: relative; animation: zoomIn 0.2s ease-out; overflow: hidden; }
        @keyframes zoomIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .clickable-row:hover { cursor: pointer; background-color: #f0fdf4 !important; }
        @media (max-width: 1024px) { .admin-main-content { margin-left: 0; padding: 20px; } }
        .swal2-popup { font-family: 'IBM Plex Sans Thai', sans-serif !important; border-radius: 1.5rem !important; }
    </style>
</head>
<body>

    <?php include 'menu_admin.php'; ?>

    <div id="userModal">
        <div class="modal-content shadow-2xl">
            <div class="bg-emerald-600 p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fas fa-user-circle mr-2"></i>รายละเอียดสมาชิก</h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white"><i class="fas fa-times text-2xl"></i></button>
            </div>
            <div id="modalBody" class="p-8"></div>
            <div class="bg-slate-50 p-4 flex justify-end">
                <button onclick="closeModal()" class="bg-white border border-slate-200 text-slate-600 px-6 py-2 rounded-xl font-bold hover:bg-slate-100 transition">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>

    <main class="admin-main-content">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800" style="font-family: 'Sarabun';">👥 จัดการสมาชิก</h1>
                    <p class="text-slate-500 mt-1">คลิกที่แถวใดก็ได้เพื่อดูรายละเอียดข้อมูลทั้งหมด</p>
                </div>
                <a href="add_user.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all"><i class="fas fa-plus-circle mr-2"></i>เพิ่มสมาชิกใหม่</a>
            </header>

            <div class="content-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-sm font-bold">
                            <tr>
                                <th class="px-6 py-4">ชื่อ-นามสกุล</th>
                                <th>การติดต่อ</th>
                                <th>ที่อยู่ / จังหวัด</th>
                                <th>ประเภท</th>
                                <th class="text-center">เครื่องมือ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($row = $result->fetch_assoc()): 
                                $userData = json_encode($row, JSON_UNESCAPED_UNICODE);
                                $roleLabel = ($row['role'] == 'admin') ? 'ผู้ดูแล' : (($row['role'] == 'volunteer') ? 'อาสาสมัคร' : 'ผู้บริจาค');
                                $roleColor = ($row['role'] == 'admin') ? 'bg-green-100 text-green-700' : (($row['role'] == 'volunteer') ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600');
                            ?>
                            <tr class="clickable-row transition-colors group">
                                <td class="px-6 py-4" onclick='showUserDetail(<?php echo $userData; ?>)'>
                                    <div class="font-bold text-slate-800 group-hover:text-emerald-700"><?php echo htmlspecialchars($row["first_name"] . " " . $row["last_name"]); ?></div>
                                    <div class="text-xs text-slate-400">@<?php echo htmlspecialchars($row["username"]); ?></div>
                                </td>
                                <td onclick='showUserDetail(<?php echo $userData; ?>)'>
                                    <div class="text-xs text-slate-600">
                                        <div class="mb-1"><i class="far fa-envelope mr-1 text-emerald-500"></i> <?php echo htmlspecialchars($row["email"]); ?></div>
                                        <div><i class="fas fa-phone-alt mr-1 text-emerald-500"></i> <?php echo htmlspecialchars($row["phone"]); ?></div>
                                    </div>
                                </td>
                                <td onclick='showUserDetail(<?php echo $userData; ?>)'>
                                    <div class="text-xs text-slate-600 truncate max-w-[150px]"><?php echo htmlspecialchars($row["address"]); ?></div>
                                    <span class="text-emerald-600 font-bold text-[11px]">จ.<?php echo htmlspecialchars($row["province"]); ?></span>
                                </td>
                                <td onclick='showUserDetail(<?php echo $userData; ?>)'>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold <?php echo $roleColor; ?>"><?php echo $roleLabel; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors"><i class="fas fa-edit"></i></a>
                                        <button type="button" onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?>')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // 1. ตรวจสอบสถานะจาก URL เพื่อแสดงแจ้งเตือน (หลังบันทึก/ลบ)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'added') {
            Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', text: 'เพิ่มสมาชิกใหม่เรียบร้อยแล้ว', confirmButtonColor: '#10b981' });
        } else if (urlParams.get('status') === 'deleted') {
            Swal.fire({ icon: 'success', title: 'ลบข้อมูลสำเร็จ!', text: 'ลบสมาชิกออกจากระบบแล้ว', confirmButtonColor: '#10b981' });
        }

        // 2. ฟังก์ชันยืนยันการลบ
        function confirmDelete(userId, userName) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: `คุณแน่ใจหรือไม่ว่าต้องการลบคุณ ${userName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText:  'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete_user.php?id=${userId}`;
                }
            })
        }

        // 3. ฟังก์ชันแสดงรายละเอียด (Modal)
        function showUserDetail(user) {
            const modal = document.getElementById('userModal');
            const body = document.getElementById('modalBody');
            let roleName = user.role === 'admin' ? 'ผู้ดูแลระบบ' : (user.role === 'volunteer' ? 'อาสาสมัคร' : 'ผู้บริจาค');
            
            body.innerHTML = `
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2 flex items-center gap-4 bg-slate-50 p-4 rounded-2xl mb-2">
                         <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-2xl font-bold">${user.first_name.charAt(0)}</div>
                         <div>
                            <div class="text-xl font-bold text-slate-800">${user.first_name} ${user.last_name}</div>
                            <div class="text-sm text-slate-500">ชื่อผู้ใช้: ${user.username}</div>
                         </div>
                    </div>
                    <div><label class="text-xs font-bold text-slate-400">อีเมล</label><div class="text-slate-700">${user.email}</div></div>
                    <div><label class="text-xs font-bold text-slate-400">เบอร์โทรศัพท์</label><div class="text-slate-700">${user.phone || '-'}</div></div>
                    <div><label class="text-xs font-bold text-slate-400">ประเภท</label><div class="text-emerald-600 font-bold">${roleName}</div></div>
                    <div><label class="text-xs font-bold text-slate-400">วันที่สมัคร</label><div class="text-slate-700">${new Date(user.created_at).toLocaleDateString('th-TH')}</div></div>
                    <div class="col-span-2 border-t pt-4">
                        <label class="text-xs font-bold text-slate-400">ที่อยู่</label>
                        <div class="text-slate-700 mt-1">${user.address}</div>
                        <div class="inline-block mt-2 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-bold">จังหวัด: ${user.province}</div>
                    </div>
                </div>`;
            modal.style.display = 'flex';
        }

        function closeModal() { document.getElementById('userModal').style.display = 'none'; }
        window.onclick = function(event) { if (event.target == document.getElementById('userModal')) { closeModal(); } }
    </script>
</body>
</html>