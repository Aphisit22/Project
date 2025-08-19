<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';
include 'navbar.php';

$user_id = $_SESSION['user_id'];

if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);

    $stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND user_id=? AND status IN ('pending', 'approved')");
    $stmt->bind_param('ii', $cancel_id, $user_id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT bookings.*, rooms.name AS room_name 
                        FROM bookings 
                        JOIN rooms ON bookings.room_id = rooms.id 
                        WHERE bookings.user_id = ? 
                        ORDER BY bookings.date DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แดชบอร์ด</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=1">
</head>
<body>
    <div class="container mt-5">
        <h2>แดชบอร์ดของคุณ</h2>
        <p>ประวัติการจองห้อง</p>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
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
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['room_name']) ?></td>
                            <td><?= $row['date'] ?></td>
                            <td><?= $row['start_time'] ?></td>
                            <td><?= $row['end_time'] ?></td>
                            <td>
                                <?php
                                if ($row['status'] == 'pending') {
                                    echo '<span class="badge bg-warning">รออนุมัติ</span>';
                                } elseif ($row['status'] == 'approved') {
                                    echo '<span class="badge bg-success">อนุมัติแล้ว</span>';
                                } elseif ($row['status'] == 'cancelled') {
                                    echo '<span class="badge bg-secondary">ยกเลิกการจอง</span>';
                                } else {
                                    echo '<span class="badge bg-danger">ถูกปฏิเสธ</span>';
                                }
                                ?>
                            </td>

                            <td>
                                <?php if (in_array($row['status'], ['pending', 'approved'])): ?>
                                    <a href="?cancel_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('คุณแน่ใจว่าต้องการยกเลิกการจองนี้?');">
                                        ยกเลิกการจอง
                                    </a>
                                <?php elseif ($row['status'] == 'cancelled'): ?>
                                    <span class="text-muted">ยกเลิกแล้ว</span>
                                <?php else: ?>
                                    <span class="text-muted">ไม่สามารถยกเลิกได้</span>
                                <?php endif; ?>
                            </td>


                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>ยังไม่มีการจอง</p>
        <?php endif; ?>
    </div>
</body>

</html>