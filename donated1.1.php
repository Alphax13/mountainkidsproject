<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โครงการเพื่อเด็กบนภู เพื่อนครูบนดอย</title>
    <link rel="stylesheet" href="donated1.1.css">
</head>
<style>
    
/* ปรับปรุงพื้นฐานให้ดูสะอาดตา */
body {
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: #f8f9fa; /* สีเทาอ่อนแบบสะอาด */
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1100px; /* ใช้ max-width แทน width เพื่อความยืดหยุ่น */
    margin: 40px auto;
    padding: 0 20px;
}

/* ปรับแต่ง Header */
h1 {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3436;
    margin-bottom: 5px;
    letter-spacing: -1px;
}

h2 {
    text-align: center;
    font-size: 1.1rem;
    font-weight: 400;
    color: #636e72;
    margin-bottom: 40px;
}

/* Gallery Grid: รองรับมือถือ */
.gallery {
    display: grid;
    /* แถวละ 3 ในจอคอม, 2 ในแท็บเล็ต, 1 ในมือถือ */
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

/* Card Item */
.item {
    background-color: #ffffff;
    border-radius: 15px; /* มนขึ้นให้ดูทันสมัย */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05); /* เงาบางๆ นุ่มๆ */
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    border: 1px solid #eee;
}

/* Hover Effect */
.item:hover {
    transform: translateY(-10px); /* ยกตัวขึ้นแทนการขยาย */
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
}

/* Image Styling */
.item img {
    width: 100%;
    height: 250px; /* เพิ่มความสูงรูปเล็กน้อย */
    object-fit: cover;
    display: block;
    transition: transform 0.5s ease;
}

/* แถม: เอฟเฟกต์รูปซูมเล็กน้อยเมื่อ Hover */
.item:hover img {
    transform: scale(1.1);
}

/* Responsive สำหรับหน้าจอขนาดเล็ก */
@media (max-width: 768px) {
    h1 { font-size: 2rem; }
    .container { margin: 20px auto; }
}
</style>
<?php include 'menu_volunteer.php'; ?>
<body>
    <div class="container">
        <h1>ชมรูปภาพสิ่งของที่โครงการนำไปบริจาค</h1>
        <h2>ของที่ได้รับบริจาคจากผู้ร่วมโครงการและผู้ใหญ่ใจดี ขอขอบคุณทุกท่านที่ร่วมบริจาคสิ่งของให้กับเด็กๆในพื้นที่ห่างไกล ไม่ว่าจะเป็นอุปกรณ์การเรียน เสื้อผ้า ของใช้จำเป็น อาหาร หรือของเล่น ทุกสิ่งล้วนมีค่าต่อพวกเขา
</h2>
        <div class="gallery">
            <div class="item">
                <img src="img/do.1.jpg" alt="สิ่งของที่บริจาค 1">
            </div>
            <div class="item">
                <img src="img/do.2.jpg" alt="สิ่งของที่บริจาค 2">
            </div>
            <div class="item">
                <img src="img/do.3.jpg" alt="สิ่งของที่บริจาค 3">
            </div>
            <div class="item">
                <img src="img/do.4.jpg" alt="สิ่งของที่บริจาค 4">
            </div>
            <div class="item">
                <img src="img/do.5.jpg" alt="สิ่งของที่บริจาค 5">
            </div>
            <div class="item">
                <img src="img/do.6.jpg" alt="สิ่งของที่บริจาค 6">
            </div>
            <div class="item">
                <img src="img/do.7.jpg" alt="สิ่งของที่บริจาค 7">
            </div>
            <div class="item">
                <img src="img/do.8.jpg" alt="สิ่งของที่บริจาค 8">
            </div>
            <div class="item">
                <img src="img/do.9.jpg" alt="สิ่งของที่บริจาค 9">
            </div>
            <div class="item">
                <img src="img/do.10.jpg" alt="สิ่งของที่บริจาค 10">
            </div>
            <div class="item">
                <img src="img/do.11.jpg" alt="สิ่งของที่บริจาค 11">
            </div>
            <div class="item">
                <img src="img/do.12.jpg" alt="สิ่งของที่บริจาค 12">
            </div>
            <div class="item">
                <img src="img/do.13.jpg" alt="สิ่งของที่บริจาค 13">
            </div>
            <div class="item">
                <img src="img/do.14.jpg" alt="สิ่งของที่บริจาค 14">
            </div>
            <div class="item">
                <img src="img/do.15.jpg" alt="สิ่งของที่บริจาค 15">
            </div>
            <div class="item">
                <img src="img/do.16.jpg" alt="สิ่งของที่บริจาค 16">
            </div>
            <div class="item">
                <img src="img/do.17.jpg" alt="สิ่งของที่บริจาค 17">
            </div>
            <div class="item">
                <img src="img/do.18.jpg" alt="สิ่งของที่บริจาค 18">
            </div>
            <div class="item">
                <img src="img/do.19.jpg" alt="สิ่งของที่บริจาค 19">
            </div>
            <div class="item">
                <img src="img/do.20.jpg" alt="สิ่งของที่บริจาค 20">
            </div>
            <div class="item">
                <img src="img/do.21.jpg" alt="สิ่งของที่บริจาค 21">
            </div>
            <div class="item">
                <img src="img/do.22.jpg" alt="สิ่งของที่บริจาค 22">
            </div>
            <div class="item">
                <img src="img/do.23.jpg" alt="สิ่งของที่บริจาค 23">
            </div>
            <div class="item">
                <img src="img/do.24.jpg" alt="สิ่งของที่บริจาค 24">
            </div>
            <div class="item">
                <img src="img/do.25.jpg" alt="สิ่งของที่บริจาค 25">
            </div>
            <div class="item">
                <img src="img/do.26.jpg" alt="สิ่งของที่บริจาค 26">
            </div>
            <div class="item">
                <img src="img/do.27.jpg" alt="สิ่งของที่บริจาค 27">
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
</div>
</body>
</html>




