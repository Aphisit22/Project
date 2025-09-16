<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];

if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);
    $stmt = $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND user_id=? AND status IN ('pending', 'approved')");
    $stmt->execute([$cancel_id, $user_id]);
    $_SESSION['alert_popup'] = "ยกเลิกการจองสำเร็จ";
    header("Location: dashboard.php");
    exit;
}

include 'navbar.php';

$stmt = $pdo->prepare("SELECT bookings.*, rooms.name AS room_name 
                        FROM bookings 
                        JOIN rooms ON bookings.room_id = rooms.id 
                        WHERE bookings.user_id = ? 
                        ORDER BY bookings.date DESC");
$stmt->execute([$user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลการจองทั้งหมด
$stmt = $pdo->prepare("SELECT bookings.*, users.username, rooms.name AS room_name 
                        FROM bookings 
                        JOIN users ON bookings.user_id = users.id 
                        JOIN rooms ON bookings.room_id = rooms.id 
                        ORDER BY bookings.date DESC");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ประวัติการจอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=1">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .dashboard-header, .admin-header {
            background: linear-gradient(90deg, #6c63ff 60%, #0d6efd 100%);
            color: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(108, 99, 255, 0.10);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-header h2, .admin-header h2 {
            font-weight: 700;
            font-size: 2.2rem;
            text-shadow: 1px 1px 8px #23102333;
        }

        .dashboard-header p, .admin-header p {
            font-size: 1.15rem;
            color: #f3f3f3;
        }

        .table-dashboard, .table-admin {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(108, 99, 255, 0.08);
            overflow: hidden;
        }

        .table-dashboard th, .table-admin th {
            background: linear-gradient(90deg, #6c63ff 60%, #0d6efd 100%);
            color: #fff;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .table-dashboard td, .table-admin td {
            vertical-align: middle;
            font-size: 1.05rem;
        }

        .badge {
            font-size: 1rem;
            padding: .5em 1em;
            border-radius: 1rem;
        }

        .btn-cancel {
            border-radius: 2rem;
            font-weight: 500;
            padding: .4em 1.2em;
        }

        @media (max-width: 576px) {
            .dashboard-header, .admin-header {
                padding: 1.2rem .7rem;
                border-radius: 1rem;
            }

            .table-dashboard, .table-admin {
                border-radius: .7rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="dashboard-header mb-4 text-center">
            <span class="display-5"><i class="bi bi-person-badge"></i></span>
            <h2 class="mt-2 mb-2">ประวัติการจองของคุณ</h2>
            <p>ดูประวัติการจองห้องเรียนและห้องประชุมของคุณ</p>
        </div>

        <div class="card table-dashboard mb-2">
            <div class="card-body p-0">
                <?php if (count($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>ห้อง</th>
                                    <th>วันที่</th>
                                    <th>เวลาเริ่ม</th>
                                    <th>เวลาสิ้นสุด</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['room_name']) ?></td>
                                        <td><?= $row['date'] ?></td>
                                        <td><?= $row['start_time'] ?></td>
                                        <td><?= $row['end_time'] ?></td>
                                        <td>
                                            <?php
                                            if ($row['status'] == 'pending') {
                                                echo '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>รออนุมัติ</span>';
                                            } elseif ($row['status'] == 'approved') {
                                                echo '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>อนุมัติแล้ว</span>';
                                            } elseif ($row['status'] == 'cancelled') {
                                                echo '<span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>ยกเลิกการจอง</span>';
                                            } else {
                                                echo '<span class="badge bg-danger"><i class="bi bi-x-octagon me-1"></i>ถูกปฏิเสธ</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (in_array($row['status'], ['pending', 'approved'])): ?>
                                                <a href="#" class="btn btn-cancel btn-danger btn-sm cancel-btn"
                                                    data-cancel="<?= $row['id'] ?>">
                                                    <i class="bi bi-x-circle"></i> ยกเลิก
                                                </a>
                                            <?php elseif ($row['status'] == 'cancelled'): ?>
                                                <span class="text-muted"><i class="bi bi-x-circle"></i> ยกเลิกแล้ว</span>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="bi bi-x-octagon"></i> ไม่สามารถยกเลิกได้</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-calendar-x display-6 mb-2"></i>
                        <div>ยังไม่มีการจอง</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="admin-header text-center mb-4">
            <span class="display-5"><i class="bi bi-person-lines-fill"></i></span>
            <h2 class="mt-2 mb-2">รายการการจองห้องทั้งหมด</h2>
            <p>เช็คเวลาห้องว่างได้ที่นี่</p>
        </div>

        <div class="card table-admin mb-5">
            <div class="card-body p-0">
                <?php if (count($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>ห้อง</th>
                                    <th>วันที่</th>
                                    <th>เวลาเริ่ม</th>
                                    <th>เวลาสิ้นสุด</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $row): ?>
                                    <tr>    
                                        <td><?= htmlspecialchars($row['room_name']) ?></td>
                                        <td><?= $row['date'] ?></td>
                                        <td><?= $row['start_time'] ?></td>
                                        <td><?= $row['end_time'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-calendar-x display-6 mb-2"></i>
                        <div>ไม่มีคำขอจอง</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
<?php
if (isset($_SESSION['alert_popup'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?= htmlspecialchars($_SESSION['alert_popup']) ?>',
                confirmButtonText: 'ตกลง',
                timer: 2000,
                timerProgressBar: true
            });
        });
    </script>
    <?php unset($_SESSION['alert_popup']); ?>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.cancel-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const cancelId = this.getAttribute('data-cancel');
                Swal.fire({
                    title: 'ยืนยันการยกเลิก?',
                    text: "คุณแน่ใจว่าต้องการยกเลิกการจองนี้?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ยกเลิก!',
                    cancelButtonText: 'ไม่'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = '?cancel_id=' + cancelId;
                    }
                });
            });
        });
    });
</script>

</html>