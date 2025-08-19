<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include 'db.php';
include 'navbar.php';

// เพิ่มห้อง
if (isset($_POST['add_room'])) {
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $facilities = $_POST['facilities'];

    // จัดการอัปโหลดรูปภาพ
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_name = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO rooms (name, capacity, facilities, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siss', $name, $capacity, $facilities, $image_path);
    $stmt->execute();
}


// แก้ไขห้อง
if (isset($_POST['edit_room'])) {
    $id = $_POST['room_id'];
    $name = $_POST['edit_name'];
    $capacity = $_POST['edit_capacity'];
    $facilities = $_POST['edit_facilities'];

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_name = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;

            // UPDATE พร้อมรูปใหม่
            $stmt = $conn->prepare("UPDATE rooms SET name=?, capacity=?, facilities=?, image=? WHERE id=?");
            $stmt->bind_param('sissi', $name, $capacity, $facilities, $image_path, $id);
        }
    } else {
        // UPDATE โดยไม่เปลี่ยนรูป
        $stmt = $conn->prepare("UPDATE rooms SET name=?, capacity=?, facilities=? WHERE id=?");
        $stmt->bind_param('sisi', $name, $capacity, $facilities, $id);
    }

    $stmt->execute();
}


// ลบห้อง
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

// แสดงรายชื่อห้อง
$result = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการห้อง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=1">
</head>

<body>
    <div class="container mt-5">
        <h2>จัดการห้อง</h2>

        <!-- ฟอร์มเพิ่มห้อง -->
        <form method="post" enctype="multipart/form-data" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="ชื่อห้อง" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="capacity" class="form-control" placeholder="ความจุ" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="facilities" class="form-control" placeholder="สิ่งอำนวยความสะดวก">
            </div>
            <div class="col-md-3">
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" name="add_room" class="btn btn-primary" style="width: 15%;">เพิ่มห้อง</button>
            </div>
        </form>

        <!-- ตารางห้อง -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>รูปภาพ</th>
                    <th>ชื่อห้อง</th>
                    <th>ความจุ</th>
                    <th>สิ่งอำนวยความสะดวก</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($room = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($room['image'])): ?>
                                <img src="<?= htmlspecialchars($room['image']) ?>" alt="room image" width="100">
                            <?php else: ?>
                                <span class="text-muted">ไม่มีรูป</span>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($room['name']) ?></td>
                        <td><?= $room['capacity'] ?></td>
                        <td><?= htmlspecialchars($room['facilities']) ?></td>
                        <td>
                            <!-- ปุ่มแก้ไข -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $room['id'] ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <!-- ปุ่มลบ -->
                            <a href="?delete=<?= $room['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('แน่ใจว่าต้องการลบห้องนี้?')">
                                <i class="bi bi-trash3"></i>
                            </a>


                            <!-- Modal แก้ไข -->
                            <div class="modal fade" id="editModal<?= $room['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">แก้ไขห้อง: <?= htmlspecialchars($room['name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="room_id" value="<?= $room['id'] ?>">

                                                <div class="mb-3">
                                                    <label>รูปภาพ</label>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                </div>
                                                <div class="mb-3">
                                                    <label>ชื่อห้อง</label>
                                                    <input type="text" name="edit_name" class="form-control" value="<?= htmlspecialchars($room['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>ความจุ</label>
                                                    <input type="number" name="edit_capacity" class="form-control" value="<?= $room['capacity'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>สิ่งอำนวยความสะดวก</label>
                                                    <input type="text" name="edit_facilities" class="form-control" value="<?= htmlspecialchars($room['facilities']) ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="edit_room" class="btn btn-success">บันทึก</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>