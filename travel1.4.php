<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โครงการเพื่อเด็กบนภู เพื่อนครูบนดอย</title>
    <link rel="stylesheet" href="travel1.4.css">
</head>
<style>
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: #f9fafb; /* สีพื้นหลังเทาอ่อนแบบละมุน */
    color: #111827;
    margin: 0;
    padding: 0;
    line-height: 1.6;
}

.container {
    max-width: 1200px; /* ใช้ max-width เพื่อไม่ให้แผ่กว้างเกินไปในจอใหญ่ */
    margin: 0 auto;
    padding: 40px 20px;
}

h1 {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 8px;
    letter-spacing: -0.025em;
}

h2 {
    text-align: center;
    font-size: 1.125rem;
    font-weight: 400;
    color: #6b7280;
    margin-bottom: 40px;
}

/* ปรับ Gallery ให้ Responsive อัตโนมัติ */
.gallery {
    display: grid;
    /* แถวละ 3 รูปในคอมพิวเตอร์ และปรับลดเองเมื่อจอเล็กลงโดยอัตโนมัติ */
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    justify-content: center;
}

/* ปรับแต่ง Card Item */
.item {
    background-color: #ffffff;
    border-radius: 16px; /* ขอบมนขึ้นให้ดูทันสมัย */
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f3f4f6;
}

/* Hover Effect: ยกตัวขึ้นและเพิ่มเงา */
.item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Image Styling */
.item img {
    width: 100%;
    height: 250px; 
    object-fit: cover;
    display: block;
    transition: transform 0.6s ease; /* เตรียมไว้สำหรับการซูม */
}

/* ลูกเล่นซูมภาพข้างในเมื่อเอาเมาส์วาง */
.item:hover img {
    transform: scale(1.1);
}

/* สำหรับหน้าจอมือถือเล็กพิเศษ */
@media (max-width: 480px) {
    h1 { font-size: 2rem; }
    .container { padding: 20px 15px; }
}
</style>
<body>
    <div class="container">
        <h1>ชมรูปภาพการเดินทางไปบริจาคของบนดอย</h1>
        <h2>การเดินทางไปบริจาคของบนดอยเต็มไปด้วยความท้าทายและความประทับใจ ไม่ว่าจะเป็นเส้นทางที่คดเคี้ยว ลาดชัน หรือสภาพอากาศที่ไม่แน่นอน แต่ทุกย่างก้าวเต็มไปด้วยความตั้งใจและมุ่งมั่นที่จะส่งต่อสิ่งของและรอยยิ้มให้กับเด็กๆ ในพื้นที่ห่างไกล
         บรรยากาศการเดินทางตั้งแต่การขนของจนถึงการมอบให้ผู้รับ ทุกภาพล้วนสะท้อนถึงน้ำใจและความร่วมมือของทุกคนที่มีส่วนร่วมในโครงการนี้
</h2>
        <div class="gallery">
            <div class="item">
                <img src="img/travel1.jpg" alt="สิ่งของที่บริจาค 1">
            </div>
            <div class="item">
                <img src="img/travel32.jpg" alt="สิ่งของที่บริจาค 2">
            </div>
            <div class="item">
                <img src="img/travel3.jpg" alt="สิ่งของที่บริจาค 3">
            </div>
            <div class="item">
                <img src="img/travel36.jpg" alt="สิ่งของที่บริจาค 4">
            </div>
            <div class="item">
                <img src="img/travel5.jpg" alt="สิ่งของที่บริจาค 5">
            </div>
            <div class="item">
                <img src="img/travel6.jpg" alt="สิ่งของที่บริจาค 6">
            </div>
            <div class="item">
                <img src="img/travel7.jpg" alt="สิ่งของที่บริจาค 7">
            </div>
            <div class="item">
                <img src="img/travel8.jpg" alt="สิ่งของที่บริจาค 8">
            </div>
            <div class="item">
                <img src="img/travel9.jpg" alt="สิ่งของที่บริจาค 9">
            </div>
            <div class="item">
                <img src="img/travel10.jpg" alt="สิ่งของที่บริจาค 10">
            </div>
            <div class="item">
                <img src="img/travel11.jpg" alt="สิ่งของที่บริจาค 11">
            </div>
            <div class="item">
                <img src="img/travel12.jpg" alt="สิ่งของที่บริจาค 12">
            </div>
            <div class="item">
                <img src="img/travel13.jpg" alt="สิ่งของที่บริจาค 13">
            </div>
            <div class="item">
                <img src="img/travel14.jpg" alt="สิ่งของที่บริจาค 14">
            </div>
            <div class="item">
                <img src="img/travel15.jpg" alt="สิ่งของที่บริจาค 15">
            </div>
            <div class="item">
                <img src="img/travel16.jpg" alt="สิ่งของที่บริจาค 16">
            </div>
            <div class="item">
                <img src="img/travel17.jpg" alt="สิ่งของที่บริจาค 17">
            </div>
            <div class="item">
                <img src="img/travel18.jpg" alt="สิ่งของที่บริจาค 18">
            </div>
            <div class="item">
                <img src="img/travel19.jpg" alt="สิ่งของที่บริจาค 19">
            </div>
            <div class="item">
                <img src="img/travel20.jpg" alt="สิ่งของที่บริจาค 20">
            </div>
            <div class="item">
                <img src="img/travel21.jpg" alt="สิ่งของที่บริจาค 21">
            </div>
            <div class="item">
                <img src="img/travel22.jpg" alt="สิ่งของที่บริจาค 22">
            </div>
            <div class="item">
                <img src="img/travel23.jpg" alt="สิ่งของที่บริจาค 23">
            </div>
            <div class="item">
                <img src="img/travel24.jpg" alt="สิ่งของที่บริจาค 24">
            </div>
        </div>
    </div>
</body>
</html>









