<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โครงการเพื่อเด็กบนภู เพื่อนครูบนดอย</title>
    <link rel="stylesheet" href="result1.3.css">
</head>
<style>
    
/* เลือกใช้ Font ที่ดูสะอาดตาและทันสมัย */
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: #f8fafc; /* สีเทาอมฟ้าอ่อนๆ ดูพรีเมียมกว่าเทาปกติ */
    color: #1e293b;
    margin: 0;
    padding: 0;
    line-height: 1.5;
}

.container {
    max-width: 1200px; /* กำหนดความกว้างสูงสุดแทนการใช้ % เพื่อไม่ให้แผ่กว้างเกินไปในจอใหญ่ */
    margin: 0 auto;
    padding: 40px 20px;
}

h1 {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
    letter-spacing: -0.025em;
}

h2 {
    text-align: center;
    font-size: 1.125rem;
    font-weight: 400;
    color: #64748b;
    margin-bottom: 48px;
}

/* ปรับ Gallery ให้รองรับ Responsive อัตโนมัติ */
.gallery {
    display: grid;
    /* แถวละ 3 รูปในคอมพิวเตอร์ และปรับลดเองเมื่อจอเล็กลง */
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 32px;
}

.item {
    background-color: #ffffff;
    border-radius: 16px; /* มนขึ้นให้ดูทันสมัยแบบยุคใหม่ */
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); /* จังหวะการเคลื่อนไหวที่ดูแพงขึ้น */
    border: 1px solid #f1f5f9;
}

/* ปรับปรุง Hover Effect */
.item:hover {
    transform: translateY(-10px); /* ยกตัวขึ้นแทนการขยายใหญ่ */
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.item img {
    width: 100%;
    height: 240px; 
    object-fit: cover;
    display: block;
    transition: transform 0.6s ease;
}

/* เพิ่มลูกเล่นเมื่อเอาเมาส์วาง ภาพจะซูมเบาๆ */
.item:hover img {
    transform: scale(1.1);
}

/* ปรับแต่งสำหรับมือถือจอเล็ก */
@media (max-width: 640px) {
    h1 { font-size: 1.8rem; }
    .gallery { gap: 20px; }
}
</style>
<?php include 'menu_volunteer.php'; ?>
<body>
    <div class="container">
        <h1>ชมรูปภาพผลลัพธ์จากกิจกรรม</h1>
        <h2>การเดินทางไปบริจาคของทุกครั้งเต็มไปด้วยความร่วมมือและพลังบวกจากทุกคน ไม่ว่าจะเป็นการบริจาคสิ่งของ การช่วยเหลือ หรือการสร้างรอยยิ้มให้กัน ขอเชิญชมภาพบรรยากาศและผลลัพธ์ที่เกิดขึ้นจากน้ำใจของทุกท่าน ขอบคุณที่ร่วมเป็นส่วนหนึ่งของความประทับใจครั้งนี้  </h2>
        <div class="gallery">
            <div class="item">
                <img src="img/r1.jpg" alt="สิ่งของที่บริจาค 1">
            </div>
            <div class="item">
                <img src="img/r2.jpg" alt="สิ่งของที่บริจาค 2">
            </div>
            <div class="item">
                <img src="img/r3.jpg" alt="สิ่งของที่บริจาค 3">
            </div>
            <div class="item">
                <img src="img/r4.jpg" alt="สิ่งของที่บริจาค 4">
            </div>
            <div class="item">
                <img src="img/r5.jpg" alt="สิ่งของที่บริจาค 5">
            </div>
            <div class="item">
                <img src="img/r6.jpg" alt="สิ่งของที่บริจาค 6">
            </div>
            <div class="item">
                <img src="img/r8.jpg" alt="สิ่งของที่บริจาค 7">
            </div>
            <div class="item">
                <img src="img/r9.jpg" alt="สิ่งของที่บริจาค 8">
            </div>
            <div class="item">
                <img src="img/r10.jpg" alt="สิ่งของที่บริจาค 9">
            </div>
            <div class="item">
                <img src="img/r11.jpg" alt="สิ่งของที่บริจาค 10">
            </div>
            <div class="item">
                <img src="img/r12.jpg" alt="สิ่งของที่บริจาค 11">
            </div>
            <div class="item">
                <img src="img/r13.jpg" alt="สิ่งของที่บริจาค 12">
            </div>
            <div class="item">
                <img src="img/r14.jpg" alt="สิ่งของที่บริจาค 13">
            </div>
            <div class="item">
                <img src="img/r15.jpg" alt="สิ่งของที่บริจาค 14">
            </div>
            <div class="item">
                <img src="img/r16.jpg" alt="สิ่งของที่บริจาค 15">
            </div>
            <div class="item">
                <img src="img/r17.jpg" alt="สิ่งของที่บริจาค 16">
            </div>
            <div class="item">
                <img src="img/r18.jpg" alt="สิ่งของที่บริจาค 17">
            </div>
            <div class="item">
                <img src="img/r19.jpg" alt="สิ่งของที่บริจาค 18">
            </div>
            <div class="item">
                <img src="img/r20.jpg" alt="สิ่งของที่บริจาค 19">
            </div>
            <div class="item">
                <img src="img/r21.jpg" alt="สิ่งของที่บริจาค 20">
            </div>
            <div class="item">
                <img src="img/r22.jpg" alt="สิ่งของที่บริจาค 21">
            </div>
            <div class="item">
                <img src="img/r23.jpg" alt="สิ่งของที่บริจาค 22">
            </div>
            <div class="item">
                <img src="img/r24.jpg" alt="สิ่งของที่บริจาค 23">
            </div>
            <div class="item">
                <img src="img/r25.jpg" alt="สิ่งของที่บริจาค 24">
            </div>
            <div class="item">
                <img src="img/r26.jpg" alt="สิ่งของที่บริจาค 24">
            </div>
            <div class="item">
                <img src="img/r27.jpg" alt="สิ่งของที่บริจาค 25">
            </div>
            <div class="item">
                <img src="img/r28.jpg" alt="สิ่งของที่บริจาค 26">
            </div>
            <div class="item">
                <img src="img/r29.jpg" alt="สิ่งของที่บริจาค 27">
            </div>
            <div class="item">
                <img src="img/r30.jpg" alt="สิ่งของที่บริจาค 28">
            </div>
            <div class="item">
                <img src="img/r31.jpg" alt="สิ่งของที่บริจาค 29">
            </div>
            <div class="item">
                <img src="img/r32.jpg" alt="สิ่งของที่บริจาค 30">
            </div>
            <div class="item">
                <img src="img/r33.jpg" alt="สิ่งของที่บริจาค 31">
            </div>
            <div class="item">
                <img src="img/r34.jpg" alt="สิ่งของที่บริจาค 32">
            </div>
            <div class="item">
                <img src="img/r35.jpg" alt="สิ่งของที่บริจาค 33">
            </div>
            <div class="item">
                <img src="img/r36.jpg" alt="สิ่งของที่บริจาค 34">
            </div>
            <div class="item">
                <img src="img/r37.jpg" alt="สิ่งของที่บริจาค 35">
            </div>
            <div class="item">
                <img src="img/r38.jpg" alt="สิ่งของที่บริจาค 36">
            </div>
            <div class="item">
                <img src="img/r39.jpg" alt="สิ่งของที่บริจาค 37">
            </div>
            <div class="item">
                <img src="img/r40.jpg" alt="สิ่งของที่บริจาค 38">
            </div>
            <div class="item">
                <img src="img/r41.jpg" alt="สิ่งของที่บริจาค 39">
            </div>
            <div class="item">
                <img src="img/r42.jpg" alt="สิ่งของที่บริจาค 40">
            </div>
            <div class="item">
                <img src="img/r43.jpg" alt="สิ่งของที่บริจาค 40">
            </div>



        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>


