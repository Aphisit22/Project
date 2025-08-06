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

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แผงควบคุมผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>แผงควบคุมผู้ดูแลระบบ</h2>
    <p>รายการคำขอจองทั้งหมด</p>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ผู้ใช้</th>
                    <th>ห้อง</th>
                    <th>วันที่</th>
                    <th>เวลาเริ่ม</th>
                    <th>เวลาสิ้นสุด</th>
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
                    <td>
                        <?php
                            if ($row['status'] == 'pending') echo '<span class="badge bg-warning">รออนุมัติ</span>';
                            elseif ($row['status'] == 'approved') echo '<span class="badge bg-success">อนุมัติแล้ว</span>';
                            else echo '<span class="badge bg-danger">ถูกปฏิเสธ</span>';
                        ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'pending'): ?>
                            <a href="?approve=<?= $row['id'] ?>" class="btn btn-sm btn-success">อนุมัติ</a>
                            <a href="?reject=<?= $row['id'] ?>" class="btn btn-sm btn-danger">ปฏิเสธ</a>
                        <?php else: ?>
                            <em>—</em>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>ไม่มีคำขอจอง</p>
    <?php endif; ?>
</div>
</body>
</html>
