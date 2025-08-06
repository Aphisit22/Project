<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<style>
    .navbar-brand-highlight {
        background: rgba(255,255,255,0.18);
        border-radius: 1rem;
        padding: 0.3rem 1.5rem 0.3rem 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        text-shadow: 1px 1px 8px #00000033;
        font-size: 2rem;
        font-weight: bold;
        letter-spacing: 1px;
        transition: background 0.3s;
    }
    .navbar-brand-highlight:hover {
        background: rgba(255,255,255,0.28);
    }
    .navbar-brand i {
        font-size: 2.2rem;
        margin-right: 0.7rem;
        color: #ffffffff;
        filter: drop-shadow(0 2px 4px #0003);
    }
    @media (max-width: 991.98px) {
        .navbar-brand-highlight {
            font-size: 1.3rem;
            padding: 0.3rem 1rem 0.3rem 0.7rem;
        }
        .navbar-brand i {
            font-size: 1.5rem;
        }
    }
</style>
<nav class="navbar navbar-expand-lg navbar-dark shadow-lg" style="background: linear-gradient(90deg, #0d6efd 60%, #6c63ff 100%);">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center navbar-brand-highlight" href="index.php">
            <i class="bi bi-building-check"></i>
            Room Booking
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="btn btn-dark fw-bold" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>แดชบอร์ด</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light fw-bold" href="booking.php"><i class="bi bi-calendar-plus me-1"></i>จองห้อง</a>
                    </li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-warning px-3 fw-bold" href="manage_rooms.php"><i class="bi bi-gear me-1"></i>จัดการห้อง</a>
                        </li>
                        <li class="nav-item ms-lg-1">
                            <a class="btn btn-success px-3 fw-bold" href="admin_panel.php"><i class="bi bi-shield-lock me-1"></i>แอดมิน</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-danger px-3 fw-bold" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-light btn-lg fw-bold m-2 px-4 shadow-hover"><i class="bi bi-box-arrow-in-right me-1"></i>เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="btn btn-outline-light btn-lg fw-bold m-2 px-4 shadow-hover"><i class="bi bi-person-plus me-1"></i>สมัครสมาชิก</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>