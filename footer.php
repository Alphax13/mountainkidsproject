<footer class="modern-footer">
    <div class="footer-wave">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
        </svg>
    </div>

    <div class="container-balanced">
        <div class="footer-header-center">
            <h2 class="brand-title">เพื่อเด็กบนภู<span>เพื่อนครูบนดอย</span></h2>
            <p class="brand-subtitle">ร่วมเป็นส่วนหนึ่งในการสร้างโอกาสทางการศึกษา และพัฒนาคุณภาพชีวิตเด็กในพื้นที่ห่างไกล</p>
            <div class="social-links">
                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="footer-content-spread">
            <div class="footer-col align-left">
                <h3>ชมเว็บไซต์</h3>
                <ul class="horizontal-links">
                    <li><a href="index2.php">หน้าแรก</a></li>
                    <li><a href="donate.php">บริจาคสิ่งของ</a></li>
                    <li><a href="events.php">กิจกรรมอาสา</a></li>
                    <li><a href="login.php">เข้าสู่ระบบ</a></li>
                </ul>
            </div>

            <div class="footer-col align-right">
                <h3>ติดต่อเรา</h3>
                <div class="contact-spread">
                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <span>094 997 8287</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>75/91 หมู่ 11 ต. คลองหนึ่ง อ.คลองหลวง จ.ปทุมธานี 12120</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 เพื่อเด็กบนภู เพื่อนครูบนดอย. All Rights Reserved.</p>
        </div>
    </div>
</footer>






<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;700&display=swap');

:root {
    --primary-gradient: linear-gradient(135deg, #274a83 0%, #1a1b66 100%);
    --dark-blue: #1e293b;
    --text-muted: #64748b;
}

.modern-footer {
    background: #fff;
    position: relative;
    padding-top: 100px; /* ขยับหัวข้อลงมาหน่อยให้ดูบาลานซ์ */
    font-family: 'Kanit', sans-serif;
    text-align: center;
}

.footer-wave {
    position: absolute;
    top: 0;
    width: 100%;
    line-height: 0;
}
.footer-wave .shape-fill { fill: #f1f5f9; }

/* ขยาย container ให้กว้างขึ้นเพื่อสลัดเนื้อหาออกข้าง */
.container-balanced {
    max-width: 1200px; 
    margin: 0 auto;
    padding: 0 40px;
}

.footer-header-center {
    margin-bottom: 30px;
}

.brand-title {
    font-size: 2rem; /* ปรับขนาดให้พอดี ไม่เทอะทะ */
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 10px;
}

.brand-title span {
    display: block;
    color: var(--dark-blue);
    -webkit-text-fill-color: var(--dark-blue);
    font-size: 1.1rem;
    font-weight: 300;
    letter-spacing: 2px;
}

.brand-subtitle {
    color: var(--text-muted);
    max-width: 700px;
    margin: 0 auto 20px;
    font-weight: 300;
    font-size: 0.95rem;
}

.footer-divider {
    border: 0;
    border-top: 1px solid #f1f5f9;
    margin: 20px 0 40px;
}

/* หัวใจของการจัดสมดุลออกข้าง */
.footer-content-spread {
    display: flex;
    justify-content: space-between; /* ผลักซ้าย-ขวา */
    align-items: flex-start;
    gap: 50px;
    margin-bottom: 40px;
}

.footer-col {
    flex: 1;
}

.footer-col h3 {
    font-size: 1rem;
    margin-bottom: 20px;
    color: var(--dark-blue);
    font-weight: 500;
}

/* ปรับเมนูให้เรียงแนวนอน (สลัดออกข้าง) */
.horizontal-links {
    list-style: none;
    padding: 0;
    display: flex;
    justify-content: center; /* หรือเปลี่ยนเป็น flex-start ถ้าอยากให้ชิดซ้ายสุด */
    gap: 25px;
    flex-wrap: wrap;
}

.horizontal-links a {
    text-decoration: none;
    color: var(--text-muted);
    font-weight: 300;
    font-size: 0.9rem;
    transition: 0.3s;
}

.horizontal-links a:hover { color: #3b82f6; }

/* ข้อมูลติดต่อแบบกระจายตัว */
.contact-spread {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-muted);
    font-size: 0.9rem;
    max-width: 400px;
}

.contact-item i { color: #3b82f6; flex-shrink: 0; }

/* Social Icons */
.social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 15px;
}

.social-links a {
    width: 36px;
    height: 36px;
    background: #f1f5f9;
    color: var(--dark-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: 0.3s;
    text-decoration: none;
}

.social-links a:hover {
    background: var(--primary-gradient);
    color: #fff;
    transform: translateY(-3px);
}

.footer-bottom {
    padding: 25px 0;
    border-top: 1px solid #f1f5f9;
    color: #94a3b8;
    font-size: 0.8rem;
}

/* Responsive: เมื่อจอกลาง/เล็ก ให้กลับมาซ้อนกัน */
@media (max-width: 900px) {
    .footer-content-spread {
        flex-direction: column;
        align-items: center;
        gap: 40px;
    }
}

@media (max-width: 600px) {
    .horizontal-links {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
