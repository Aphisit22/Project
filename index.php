<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=1">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- HERO SECTION -->
    <header class="hero-bg text-white text-center py-5 mb-5">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3"><i class="bi bi-calendar2-check"></i> ระบบจองห้องเรียนและห้องประชุม</h1>
            <p class="lead mb-4">จองห้องเรียนหรือห้องประชุมได้อย่างสะดวก รวดเร็ว และปลอดภัย</p>
            <a href="register.php" class="btn btn-warning cta-btn fw-bold me-2 shadow-hover"><i class="bi bi-person-plus"></i> สมัครสมาชิก</a>
            <?php
            $booking_link = (isset($_SESSION['user_id'])) ? 'booking.php' : 'login.php';
            ?>
            <a href="<?= $booking_link ?>" class="btn btn-light cta-btn fw-bold shadow-hover">
                <i class="bi bi-calendar-plus"></i> จองห้องเลย
            </a>
        </div>
    </header>

    <!-- FEATURE SECTION -->
    <section class="container mb-5">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 shadow-hover">
                    <div class="card-body">
                        <div class="feature-icon mb-3"><i class="bi bi-search"></i></div>
                        <h5 class="card-title fw-bold">ค้นหาห้องว่าง</h5>
                        <p class="card-text">ตรวจสอบห้องว่างได้แบบเรียลไทม์ พร้อมรายละเอียดครบถ้วน</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 shadow-hover">
                    <div class="card-body">
                        <div class="feature-icon mb-3"><i class="bi bi-clock-history"></i></div>
                        <h5 class="card-title fw-bold">จองง่าย รวดเร็ว</h5>
                        <p class="card-text">เลือกวันและเวลาที่ต้องการจองได้ทันที พร้อมระบบแจ้งเตือนสถานะ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 shadow-hover">
                    <div class="card-body">
                        <div class="feature-icon mb-3"><i class="bi bi-shield-check"></i></div>
                        <h5 class="card-title fw-bold">ปลอดภัยและเชื่อถือได้</h5>
                        <p class="card-text">ระบบอนุมัติการจองโดยผู้ดูแล ป้องกันการจองซ้ำซ้อน</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW TO USE SECTION -->
    <section class="container mb-5">
        <h2 class="text-center fw-bold mb-4"><i class="bi bi-lightbulb"></i> วิธีใช้งานระบบ</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="howto-step mb-3 d-flex align-items-center">
                    <div class="me-3 display-6 text-primary"><i class="bi bi-person-plus"></i></div>
                    <div>
                        <span class="fw-bold">1. สมัครสมาชิก</span> <br>
                        กรอกข้อมูลเพื่อสมัครสมาชิกเข้าสู่ระบบ
                    </div>
                </div>
                <div class="howto-step mb-3 d-flex align-items-center">
                    <div class="me-3 display-6 text-success"><i class="bi bi-box-arrow-in-right"></i></div>
                    <div>
                        <span class="fw-bold">2. เข้าสู่ระบบ</span> <br>
                        ล็อกอินด้วยชื่อผู้ใช้และรหัสผ่านที่สมัครไว้
                    </div>
                </div>
                <div class="howto-step mb-3 d-flex align-items-center">
                    <div class="me-3 display-6 text-warning"><i class="bi bi-calendar-plus"></i></div>
                    <div>
                        <span class="fw-bold">3. จองห้อง</span> <br>
                        เลือกห้อง วันที่ และเวลาที่ต้องการจอง
                    </div>
                </div>
                <div class="howto-step d-flex align-items-center">
                    <div class="me-3 display-6 text-info"><i class="bi bi-check-circle"></i></div>
                    <div>
                        <span class="fw-bold">4. รอการอนุมัติ</span> <br>
                        ตรวจสอบสถานะการจองและรอการอนุมัติจากผู้ดูแลระบบ
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="assets/images/room.jfif" alt="Classroom" class="img-fluid rounded shadow-lg">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> เกี่ยวกับระบบ</h2>
                <p class="fs-5">ระบบนี้ออกแบบมาเพื่อให้ผู้ใช้งานสามารถจองห้องเรียนหรือห้องประชุมได้โดยง่าย มีการตรวจสอบความพร้อมใช้งานแบบเรียลไทม์ และระบบการอนุมัติจากผู้ดูแลระบบ</p>
                <ul class="list-unstyled fs-5">
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>ดูห้องว่างได้ทันที</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>จองได้ตามวันที่และเวลาที่ต้องการ</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>ติดตามสถานะการจองได้ตลอดเวลา</li>
                </ul>
            </div>
        </div>
    </section>

    <footer class="border-top mt-5">
        <div class="container py-4 text-white">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="fw-bold mb-3"><i class="bi bi-building-check me-2"></i>Room Booking</h5>
                    <p class="small mb-0">
                        ระบบจองห้องเรียนและห้องประชุมออนไลน์<br>
                        สะดวก รวดเร็ว และปลอดภัยสำหรับทุกคน
                    </p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6 class="fw-bold mb-3"><i class="bi bi-link-45deg me-1"></i> ลิงก์ที่เกี่ยวข้อง</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white text-decoration-none"><i class="bi bi-house-door me-1"></i> หน้าแรก</a></li>
                        <li><a href="booking.php" class="text-white text-decoration-none"><i class="bi bi-calendar-plus me-1"></i> จองห้อง</a></li>
                        <li><a href="register.php" class="text-white text-decoration-none"><i class="bi bi-person-plus me-1"></i> สมัครสมาชิก</a></li>
                        <li><a href="login.php" class="text-white text-decoration-none"><i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-envelope me-1"></i> ติดต่อเรา</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt me-1"></i> <span class="text-white-50">[ที่อยู่ : Moon]</span></li>
                        <li><i class="bi bi-telephone me-1"></i> <span class="text-white-50">[เบอร์โทรศัพท์ : 020-2000000]</span></li>
                        <li><i class="bi bi-envelope-at me-1"></i> <span class="text-white-50">[อีเมล : zcv2175@gmail.com]</span></li>
                    </ul>
                </div>
            </div>
            <hr class="border-light opacity-50 my-4">
            <div class="text-center small text-white-50">
                &copy; 2025 ระบบจองห้องเรียนและห้องประชุม | All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>