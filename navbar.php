<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<style>
    nav {
        position: relative;
        background:
            radial-gradient(1200px 600px at -10% -20%, rgba(255, 255, 255, 0.10), transparent 40%),
            linear-gradient(135deg, var(--lux-primary) 0%, var(--lux-secondary) 55%, #8a7bff 100%);
        color: #fff;
        overflow: hidden;
    }

    .navbar-brand-highlight {
        background: rgba(255, 255, 255, 0.18);
        border-radius: 1rem;
        padding: 0.3rem 1.5rem 0.3rem 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.10);
        text-shadow: 1px 1px 8px #0003;
        font-size: 2rem;
        font-weight: bold;
        letter-spacing: 1px;
        transition: background 0.3s;
    }

    .navbar-brand-highlight:hover {
        background: rgba(255, 255, 255, 0.28);
    }

    .navbar-brand i {
        font-size: 2.2rem;
        margin-right: 0.7rem;
        color: #fff;
        filter: drop-shadow(0 2px 4px #0003);
    }

    /* Responsive & Hamburger menu */
    @media (max-width: 991.98px) {
        .navbar-nav {
            align-items: stretch !important;
        }

        .navbar-nav .nav-item {
            margin-bottom: 0.5rem;
            width: 100%;
        }

        .navbar-nav .btn,
        .navbar-nav .btn-lg {
            width: 100%;
            text-align: left;
            padding-left: 1.2rem;
            padding-right: 1.2rem;
            margin: 0.3rem 0.3rem 0.5rem;
            font-size: 1rem;
            border-radius: 0.7rem;
            box-shadow: 0 2px 8px #0001;
            transition: background 0.2s, box-shadow 0.2s;
        }

        .navbar-nav .btn-lg {
            font-size: 1.1rem;
            padding: 0.7rem 1.2rem;
        }

        .navbar-nav .ms-lg-1,
        .navbar-nav .ms-lg-2,
        .navbar-nav .ms-lg-3 {
            margin-left: 0 !important;
        }

        .navbar-collapse {
            background: rgba(30, 30, 60, 0.98);
            border-radius: 1rem;
            margin-top: 0.7rem;
            padding: 1.2rem 0.7rem;
            box-shadow: 0 4px 16px #0002;
            transition: all 0.35s cubic-bezier(.4, 2, .6, 1);
            opacity: 0;
            transform: translateY(-20px) scale(0.98);
            pointer-events: none;
            backdrop-filter: blur(8px);
        }

        .navbar-collapse.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
            background: rgba(30, 30, 60, 0.97);
            border-radius: 1.2rem;
            box-shadow: 0 12px 32px #0004;
        }

        .navbar {
            padding-bottom: 0.5rem;
        }
    }

    @media (max-width: 575.98px) {
        .navbar-brand-highlight {
            font-size: 1rem;
            padding: 0.2rem 0.7rem 0.2rem 0.5rem;
        }

        .navbar-brand i {
            font-size: 1.1rem;
        }

        .navbar-collapse {
            padding: 0.5rem 0.2rem;
        }
    }
</style>
<nav class="navbar navbar-expand-lg navbar-dark shadow-lg">
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
                        <a class="btn btn-dark fw-bold" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>ประวัติการจอง</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light fw-bold" href="booking.php"><i class="bi bi-calendar-plus me-1"></i>จองห้อง</a>
                    </li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-warning px-3 fw-bold" href="manage_rooms.php"><i class="bi bi-gear me-1"></i>จัดการห้อง</a>
                        </li>
                        <li class="nav-item ms-lg-1">
                            <a class="btn btn-success px-3 fw-bold" href="admin_panel.php"><i class="bi bi-shield-lock me-1"></i>แอดมิน</a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-primary px-3 fw-bold" href="register.php"><i class="bi bi-person-add me-1"></i>เพิ่มสมาชิก</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-danger px-3 fw-bold" href="logout.php" id="logoutBtn"><i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-light btn-lg fw-bold m-2 px-4 shadow-hover"><i class="bi bi-box-arrow-in-right me-1"></i>เข้าสู่ระบบ</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('logoutBtn')?.addEventListener('click', function (e) {
    e.preventDefault();

    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณต้องการออกจากระบบ",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, ออกจากระบบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "logout.php";
        }
    });
});
</script>
<?php if (isset($_SESSION['logout_success']) && $_SESSION['logout_success'] === true): ?>
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'ออกจากระบบสำเร็จ',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true
});
</script>
<?php 
    // ลบ flag หลังจากแสดง Toast
    unset($_SESSION['logout_success']);
endif; 
?>
