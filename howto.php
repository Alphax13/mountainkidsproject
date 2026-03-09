<button id="howToBtn" style="background: #10b981; color: white; border: none; padding: 10px 22px; border-radius: 10px; cursor: pointer; font-family: 'Sarabun', sans-serif; display: flex; align-items: center; gap: 10px; font-weight: 600; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: 0.3s;">
    <i class="fas fa-question-circle"></i> วิธีใช้งานระบบ
</button>

<div id="howToModal" class="custom-modal">
    <div class="modal-content-wrapper">
        <div class="modal-header">
            <h2 style="margin:0; font-size: 1.25rem;"><i class="fas fa-book-open"></i> คู่มือการใช้งาน Admin Hub</h2>
            <span class="close-modal">&times;</span>
        </div>
        
        <div class="modal-body">
            <div class="container-manual">
                
                <section class="howto-section">
                    <h2><i class="fas fa-chart-line"></i> 0. แผงควบคุม (Dashboard)</h2>
                    <p>หน้าวิเคราะห์ภาพรวมแบบ Real-time เพื่อดูความต้องการของแต่ละโครงการ</p>
                    <ul>
                        <li><strong>Gap Analysis:</strong> กราฟเส้นสีแดงแสดงเป้าหมาย และแท่งสีแสดงจำนวนที่ได้รับจริง</li>
                        <li><strong>สีเทา:</strong> แสดงปริมาณส่วนต่างที่ยังไม่ได้รับ (ส่วนที่ยังขาดแคลน)</li>
                        <li><strong>การแยกหมวดหมู่:</strong> กราฟนี้จะแสดงข้อมูลสีบ่งชี้ถึงประเภทสิ่งของ เพื่อการวิเคราะห์สมดุลทรัพยากรที่ได้รับ</li>
                        <li><strong>สรุปยอด:</strong> ตรวจสอบจำนวนสมาชิกที่ลงทะเบียนทั้งหมดในระบบได้ทันทีที่มุมขวาบน</li>
                        <li><strong>ความคืบหน้า:</strong> แสดงเปอร์เซ็นต์ความสำเร็จและแถบสถานะแยกตามประเภทสิ่งของ</li>
                    </ul>
                    <div class="howto-image-box">
                        <img src="img/1d.png" alt="Dashboard" class="manual-img">
                        <p><strong>กราฟแสดงผล:</strong> กราฟนี้จะแสดงข้อมูลการบริจาคในแต่ละประเภท</p>
                    </div>
                    <div class="howto-image-box">
                        <img src="img/2d.png" alt="Dashboard" class="manual-img">
                        <p><strong>การแยกหมวดหมู่:</strong> กราฟนี้จะแสดงข้อมูลการบริจาคในแต่ละประเภทตามสีของหมวดหมู่ที่กำหนด</p>
                    </div>
                </section>

                <section class="howto-section">
                    <h2><i class="fas fa-expand-alt"></i> หลักการขยายกราฟตามความต้องการ</h2>
                    <p>ใช้สำหรับขยายกราฟในหน้า Dashboard ตามความต้องการของผู้บริหาร เชื่อมโยงกับหน้าเพิ่มกิจกรรม</p>
                    <ul>
                        <li><strong>เมื่อเพิ่มกิจกรรมใหม่:</strong> กราฟจะขยายขึ้นตามจำนวนกิจกรรมที่มีอยู่</li>
                    </ul>
                    <div class="howto-image-box"><img src="img/11d.png" class="manual-img"></div>
                    <div class="howto-image-box"><img src="img/13d.png" class="manual-img"></div>
                    <div class="howto-image-box"><img src="img/12d.png" class="manual-img"></div>
                    <div class="howto-image-box"><img src="img/14d (1).gif" class="manual-img"></div>
                    <div class="howto-image-box"><img src="img/15d.gif" class="manual-img"></div>
                </section>

                <section class="howto-section">
                    <h2><i class="fas fa-clipboard-list"></i> 2. รายงานกิจกรรมจิตอาสา</h2>
                    <p>ตรวจสอบรายชื่อและสถานะอาสาสมัครในแต่ละโครงการ</p>
                    <ul>
                        <li><strong>Verified User:</strong> สังเกตแถบสีเขียวใต้ชื่อสมาชิกที่ยืนยันตัวตนแล้ว</li>
                        <li><strong>ช่องค้นหา:</strong> ใช้ช่องค้นหาเพื่อค้นหากิจกรรมได้</li>
                    </ul>
                    <div class="howto-image-box"><img src="img/22d.gif" class="manual-img"></div>
                </section>

                <section class="howto-section">
                    <h2><i class="fas fa-users-cog"></i> 3. ระบบจัดการสมาชิก</h2>
                    <p>บริหารจัดการข้อมูลสมาชิกและสิทธิ์การใช้งาน</p>
                    <ul>
                        <li><strong>View Details:</strong> <span style="color:#3b82f6;">คลิกที่แถวรายชื่อ</span> เพื่อเปิดป๊อปอัพดูที่อยู่และข้อมูลติดต่อ</li>
                        <li><strong>Manage:</strong> สามารถมาติกอนุมัติของบริจาคที่สมาชิกส่งเข้ามาได้</li>
                    </ul>
                    <div class="howto-image-box"><img src="img/23d.gif" class="manual-img"></div>
                    <div class="howto-image-box"><img src="img/24d.gif" class="manual-img"></div>
                </section>

                <section class="howto-section">
                    <h2><i class="fas fa-plus-circle"></i> 4. การเพิ่มกิจกรรมและคลังบริจาค</h2>
                    <p>ขั้นตอนการสร้างโครงการและตั้งเป้าหมายสิ่งของ</p>
                    <ul>
                        <li><strong>Tags:</strong> พิมพ์ไฮไลท์กิจกรรมคั่นด้วยเครื่องหมายจุลภาค ( , ) เพื่อสร้างป้ายคำ</li>
                        <li><strong>Target:</strong> ระบุจำนวนที่ต้องการในช่องเป้าหมาย</li>
                    </ul>
                    <div class="howto-image-box"><img src="img/41d.png" class="manual-img"></div>
                    <div class="howto-image-box"><img src="img/42d.gif" class="manual-img"></div>
                </section>

                <section class="howto-section">
                    <h2><i class="fas fa-user-plus"></i> 5. จัดการสมาชิก</h2>
                    <p>ขั้นตอนการเพิ่มสมาชิกใหม่และจัดการสิทธิ์การใช้งาน</p>
                    <ul>
                        <li><strong>Add User:</strong> จัดการข้อมูลสมาชิกใหม่ในฟอร์ม</li>
                        <li><strong>เพิ่มสมาชิก:</strong> ใช้ปุ่ม "เพิ่มสมาชิก" เพื่อเพิ่มสมาชิกใหม่</li>
                    </ul>
                    <div class="howto-image-box"><img src="img/31.png" class="manual-img"></div>
                    <div class="howto-image-box"><img src="img/32d.png" class="manual-img"></div>
                    <div class="howto-image-box"><img src="img/33d.gif" class="manual-img"></div>
                </section>

                <section class="howto-section">
                    <h2><i class="fas fa-user-plus"></i> 5.หน้าเพิ่มข่าวประชาสัมพันธ์</h2>
                    <p>เพิ่มข่าวประชาสัมพันธ์ใหม่ และรายละเอียดข่าว</p>
                    <ul>
                        <li><strong>ข่าวประชาสัมพันธ์:</strong> จัดการข้อมูลข่าวประชาสัมพันธ์</li>
                        <li><strong>เพิ่มข่าว:</strong> ใช้ปุ่ม "เพิ่มข่าว" เพื่อเพิ่มข่าวประชาสัมพันธ์ใหม่</li>
                    </ul>
                    <div class="howto-image-box"><img src="img/news1.gif" class="manual-img"></div>
                    
                </section>


            </div>
        </div>
    </div>
</div>

<style>
/* จัดชิดซ้ายตามคำขอใหม่ */
.container-manual {
    text-align: left;
    background: white;
    padding: 30px;
    border-radius: 12px;
}
.container-manual ul {
    list-style-position: outside;
    padding-left: 20px;
}
.howto-section h2 { 
    color: #10b981; 
    border-left: 5px solid #10b981; 
    padding-left: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.howto-image-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 15px;
    margin-top: 15px;
    border-radius: 8px;
    text-align: left; /* ชิดซ้าย */
}
.manual-img {
    max-width: 100%;
    height: auto;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
/* CSS อื่นๆ ของ Modal คงเดิม... */
.custom-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); font-family: 'Sarabun', sans-serif; }
.modal-content-wrapper { background: #f1f5f9; margin: 30px auto; width: 90%; max-width: 850px; height: calc(100% - 60px); border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; animation: slideUp 0.4s ease; }
.modal-header { background: white; padding: 18px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
.modal-body { padding: 20px; overflow-y: auto; }
.close-modal { font-size: 28px; color: #94a3b8; cursor: pointer; }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<script>
/* Script ชุดเดิมของคุณ */
(function() {
    const modal = document.getElementById("howToModal");
    const btn = document.getElementById("howToBtn");
    const closeBtn = document.querySelector(".close-modal");
    btn.onclick = () => { modal.style.display = "block"; document.body.style.overflow = "hidden"; };
    closeBtn.onclick = () => { modal.style.display = "none"; document.body.style.overflow = "auto"; };
    window.onclick = (e) => { if (e.target == modal) { modal.style.display = "none"; document.body.style.overflow = "auto"; } };
})();
</script>