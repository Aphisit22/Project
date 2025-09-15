<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include 'db.php';
include 'navbar.php';

// อัปเดตสถานะการจองเมื่อมีการยืนยัน/ปฏิเสธ
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'approved' WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

// ดึงข้อมูลการจองทั้งหมด
$stmt = $conn->prepare("SELECT bookings.*, users.username, rooms.name AS room_name 
                        FROM bookings 
                        JOIN users ON bookings.user_id = users.id 
                        JOIN rooms ON bookings.room_id = rooms.id 
                        ORDER BY bookings.date DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- filepath: c:\xampp\htdocs\room\admin_panel.php -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แผงควบคุมผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=1">
    <style>
        .admin-header {
            background: linear-gradient(90deg, #6c63ff 60%, #0d6efd 100%);
            color: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(108,99,255,0.10);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            margin-bottom: 2rem;
        }
        .admin-header h2 {
            font-weight: 700;
            font-size: 2.2rem;
            text-shadow: 1px 1px 8px #23102333;
        }
        .admin-header p {
            font-size: 1.15rem;
            color: #f3f3f3;
        }
        .table-admin {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(108,99,255,0.08);
            overflow: hidden;
        }
        .table-admin th {
            background: linear-gradient(90deg, #6c63ff 60%, #0d6efd 100%);
            color: #fff;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .table-admin td {
            vertical-align: middle;
            font-size: 1.05rem;
        }
        .badge {
            font-size: 1rem;
            padding: .5em 1em;
            border-radius: 1rem;
        }
        .btn-success, .btn-danger {
            border-radius: 2rem;
            font-weight: 500;
            padding: .4em 1.2em;
        }
        @media (max-width: 576px) {
            .admin-header {
                padding: 1.2rem .7rem;
                border-radius: 1rem;
            }
            .table-admin {
                border-radius: .7rem;
            }
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="admin-header text-center mb-4">
        <span class="display-5"><i class="bi bi-shield-lock"></i></span>
        <h2 class="mt-2 mb-2">แผงควบคุมผู้ดูแลระบบ</h2>
        <p>รายการคำขอจองทั้งหมด สามารถอนุมัติหรือปฏิเสธได้ที่นี่</p>
    </div>

    <div class="card table-admin mb-5">
        <div class="card-body p-0">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>ผู้ใช้</th>
                                <th>ห้อง</th>
                                <th>วันที่</th>
                                <th>เวลาเริ่ม</th>
                                <th>เวลาสิ้นสุด</th>
                                <th>เหตุผลการจอง</th>
                                <th>สถานะ</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['room_name']) ?></td>
                                <td><?= $row['date'] ?></td>
                                <td><?= $row['start_time'] ?></td>
                                <td><?= $row['end_time'] ?></td>
                                <td><?= $row['reason'] ?></td>
                                <td>
                                    <?php
                                        if ($row['status'] == 'pending') echo '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>รออนุมัติ</span>';
                                        elseif ($row['status'] == 'approved') echo '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>อนุมัติแล้ว</span>';
                                        elseif ($row['status'] == 'cancelled') echo '<span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>ยกเลิก</span>';
                                        else echo '<span class="badge bg-danger"><i class="bi bi-x-octagon me-1"></i>ถูกปฏิเสธ</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <a href="?approve=<?= $row['id'] ?>" class="btn btn-success btn-sm me-1">
                                            <i class="bi bi-check-lg"></i> อนุมัติ
                                        </a>
                                        <a href="?reject=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('คุณแน่ใจว่าต้องการปฏิเสธการจองนี้?');">
                                            <i class="bi bi-x-lg"></i> ปฏิเสธ
                                        </a>
                                    <?php else: ?>
                                        <em class="text-muted">—</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
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
</html>
