<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โครงการเพื่อเด็กบนภู เพื่อนครูบนดอย</title>
    <link rel="stylesheet" href="activity1.2.css">
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
    max-width: 1200px; /* ใช้ max-width แทน width เพื่อความยืดหยุ่น */
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

/* Gallery Grid: รองรับมือถืออัตโนมัติ */
.gallery {
    display: grid;
    /* แถวละ 3 ในจอใหญ่ และปรับลดอัตโนมัติเมื่อจอเล็กลง */
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

/* Card Item */
.item {
    background-color: #ffffff;
    border-radius: 16px; /* มนขึ้นให้ดูทันสมัย */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05); /* เงาบางๆ นุ่มๆ */
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    border: 1px solid rgba(0,0,0,0.05);
}

/* Hover Effect: ยกตัวขึ้นและเพิ่มเงา */
.item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

/* Image Styling */
.item img {
    width: 100%;
    height: 250px; 
    object-fit: cover;
    display: block;
    transition: transform 0.6s ease;
}

/* เอฟเฟกต์รูปซูมเล็กน้อยเมื่อ Hover */
.item:hover img {
    transform: scale(1.08);
}

/* Responsive สำหรับหน้าจอขนาดเล็ก */
@media (max-width: 768px) {
    h1 { font-size: 2rem; }
    .container { margin: 20px auto; }
}
</style>
<body>
    <?php include 'menu_volunteer.php'; ?>
    <div class="container">
        <h1>ชมรูปภาพกิจกรรมของโครงการ</h1>
        <h2>ทางโครงการได้จัดกิจกรรมต่างๆให้เด็กๆและชาวบ้าน ไม่ว่าจะเป็นการจัดกีฬาสี กิจกรรมวันพ่อ กิจกรรมสร้างความสุขช่วงกลางคืน ดูหนังกลางแปลง ปล่อยโคมลอย จุดพลุ การแสดง เนื่องจากหมู่บ้านอยู่พื้นที่ห่างไกลจึงไม่มีไฟฟ้า "ชาวบ้านบางคนไม่เคยมีโอกาสได้เห็นแสงสีที่งดงาม"  </h2>
        <div class="gallery">
            <div class="item">
                <img src="img/g1.jpg" alt="สิ่งของที่บริจาค 1">
            </div>
            <div class="item">
                <img src="img/g2.jpg" alt="สิ่งของที่บริจาค 2">
            </div>
            <div class="item">
                <img src="img/g3.jpg" alt="สิ่งของที่บริจาค 3">
            </div>
            <div class="item">
                <img src="img/g4.jpg" alt="สิ่งของที่บริจาค 4">
            </div>
            <div class="item">
                <img src="img/g5.jpg" alt="สิ่งของที่บริจาค 5">
            </div>
            <div class="item">
                <img src="img/g6.jpg" alt="สิ่งของที่บริจาค 6">
            </div>
            <div class="item">
                <img src="img/g7.jpg" alt="สิ่งของที่บริจาค 7">
            </div>
            <div class="item">
                <img src="img/g8.jpg" alt="สิ่งของที่บริจาค 8">
            </div>
            <div class="item">
                <img src="img/g9.jpg" alt="สิ่งของที่บริจาค 9">
            </div>
            <div class="item">
                <img src="img/g10.jpg" alt="สิ่งของที่บริจาค 10">
            </div>
            <div class="item">
                <img src="img/g11.jpg" alt="สิ่งของที่บริจาค 11">
            </div>
            <div class="item">
                <img src="img/g12.jpg" alt="สิ่งของที่บริจาค 12">
            </div>
            <div class="item">
                <img src="img/g13.jpg" alt="สิ่งของที่บริจาค 13">
            </div>
            <div class="item">
                <img src="img/g14.jpg" alt="สิ่งของที่บริจาค 14">
            </div>
            <div class="item">
                <img src="img/g15.jpg" alt="สิ่งของที่บริจาค 14">
            </div>
            <div class="item">
                <img src="img/g16.jpg" alt="สิ่งของที่บริจาค 15">
            </div>
            <div class="item">
                <img src="img/g17.jpg" alt="สิ่งของที่บริจาค 16">
            </div>
            <div class="item">
                <img src="img/g18.jpg" alt="สิ่งของที่บริจาค 18">
            </div>
            <div class="item">
                <img src="img/g19.jpg" alt="สิ่งของที่บริจาค 19">
            </div>
            <div class="item">
                <img src="img/g20.jpg" alt="สิ่งของที่บริจาค 20">
            </div>
            <div class="item">
                <img src="img/g21.jpg" alt="สิ่งของที่บริจาค 21">
            </div>
            <div class="item">
                <img src="img/g22.jpg" alt="สิ่งของที่บริจาค 22">
            </div>
            <div class="item">
                <img src="img/g23.jpg" alt="สิ่งของที่บริจาค 23">
            </div>
            <div class="item">
                <img src="img/g24.jpg" alt="สิ่งของที่บริจาค 24">
            </div>
            <div class="item">
                <img src="img/g25.jpg" alt="สิ่งของที่บริจาค 25">
            </div>
            <div class="item">
                <img src="img/g26.jpg" alt="สิ่งของที่บริจาค 26">
            </div>
            <div class="item">
                <img src="img/g27.jpg" alt="สิ่งของที่บริจาค 27">
            </div>
            <div class="item">
                <img src="img/g28.jpg" alt="สิ่งของที่บริจาค 28">
            </div>
            <div class="item">
                <img src="img/g29.jpg" alt="สิ่งของที่บริจาค 29">
            </div>
            <div class="item">
                <img src="img/g30.jpg" alt="สิ่งของที่บริจาค 22">
            </div>
        </div>
    </div>
</body>
<?php include 'footer.php'; ?>
</html>



..........................................................................................................................................


