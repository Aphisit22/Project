<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';
include 'navbar.php';

// โหลดห้องทั้งหมด
$rooms = $conn->query("SELECT * FROM rooms");

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // ตรวจสอบการจองซ้ำ
    $stmt = $conn->prepare("SELECT * FROM bookings 
                            WHERE room_id = ? AND date = ? 
                            AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))");
    $stmt->bind_param('isssssss', $room_id, $date, $end_time, $end_time, $start_time, $start_time, $start_time, $end_time);
    $stmt->execute();
    $conflict = $stmt->get_result();

    if ($conflict->num_rows > 0) {
        $message = "<div class='alert alert-danger'>ช่วงเวลานี้ถูกจองแล้ว กรุณาเลือกเวลาอื่น</div>";
    } else {
        // บันทึกการจอง
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, date, start_time, end_time, status) 
                                VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param('iisss', $_SESSION['user_id'], $room_id, $date, $start_time, $end_time);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>ส่งคำขอจองสำเร็จ รอการอนุมัติ</div>";
        } else {
            $message = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการจอง</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จองห้อง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>จองห้อง</h2>
        <?= $message ?>

        <form method="POST">
            <div class="mb-4">
                <label for="room_id" class="form-label fw-bold">เลือกห้อง</label>
                <select name="room_id" id="room_id" class="form-select mb-3" required onchange="showRoomImage()">
                    <option value="">เลือกห้อง</option>
                    <?php while ($room = $rooms->fetch_assoc()): ?>
                        <option
                            value="<?= $room['id'] ?>"
                            data-image="<?= htmlspecialchars($room['image']) ?>"
                            data-name="<?= htmlspecialchars($room['name']) ?>"
                            data-capacity="<?= $room['capacity'] ?>"
                            data-facilities="<?= htmlspecialchars($room['facilities']) ?>">
                            <?= htmlspecialchars($room['name']) ?> (ความจุ <?= $room['capacity'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>

                <div id="roomImageContainer" class="text-center p-3 border rounded bg-light" style="display:none;">
                    <img id="roomImage" src="" alt="Room Image" class="img-fluid rounded border shadow-sm mb-3" style="max-height:220px;">
                    <div id="roomDetails" class="mt-2" style="display:none;">
                        <h5 id="roomName" class="mb-2"></h5>
                        <span id="roomCapacity" class="badge bg-primary mb-2"></span><br>
                        <span id="roomFacilities" class="text-muted"></span>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label fw-bold">วันที่</label>
                <input type="date" name="date" id="date" class="form-control" required min="<?= date('Y-m-d') ?>">
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <label for="start_time" class="form-label fw-bold">เวลาเริ่ม</label>
                    <input type="time" name="start_time" id="start_time" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label fw-bold">เวลาสิ้นสุด</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" required>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success px-4">ส่งคำขอจอง</button>
                <a href="dashboard.php" class="btn btn-secondary px-4">กลับ</a>
            </div>
        </form>

        <script>
            function showRoomImage() {
                const select = document.getElementById('room_id');
                const option = select.options[select.selectedIndex];
                const value = option.value;
                const image = option.getAttribute('data-image');
                const name = option.getAttribute('data-name');
                const capacity = option.getAttribute('data-capacity');
                const facilities = option.getAttribute('data-facilities');
                const container = document.getElementById('roomImageContainer');
                const imgTag = document.getElementById('roomImage');
                const details = document.getElementById('roomDetails');
                const roomName = document.getElementById('roomName');
                const roomCapacity = document.getElementById('roomCapacity');
                const roomFacilities = document.getElementById('roomFacilities');

                if (value && image) {
                    imgTag.src = image;
                    container.style.display = 'block';
                    details.style.display = 'block';
                    roomName.textContent = name;
                    roomCapacity.textContent = 'ความจุ: ' + capacity + ' คน';
                    roomFacilities.textContent = 'สิ่งอำนวยความสะดวก: ' + facilities;
                } else {
                    container.style.display = 'none';
                    imgTag.src = '';
                    details.style.display = 'none';
                }
            }
            window.onload = showRoomImage;
        </script>
</body>

</html>