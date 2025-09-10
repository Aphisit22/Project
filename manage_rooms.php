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


<!-- filepath: c:\xampp\htdocs\room\manage_rooms.php -->
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการห้อง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=1">
    <style>
        .page-header {
            background: linear-gradient(90deg, #6c63ff 60%, #0d6efd 100%);
            color: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(108,99,255,0.10);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            margin-bottom: 2rem;
        }
        .page-header h2 {
            font-weight: 700;
            font-size: 2.2rem;
            text-shadow: 1px 1px 8px #23102333;
        }
        .form-control, .form-select {
            border-radius: 1rem;
        }
        .btn-primary, .btn-success, .btn-warning, .btn-danger, .btn-secondary {
            border-radius: 2rem;
            font-weight: 600;
        }
        .table-rooms {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(108,99,255,0.08);
            overflow: hidden;
        }
        .table-rooms th {
            background: linear-gradient(90deg, #6c63ff 60%, #0d6efd 100%);
            color: #fff;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .table-rooms td {
            vertical-align: middle;
            font-size: 1.05rem;
        }
        .room-img {
            max-width: 100px;
            max-height: 70px;
            border-radius: 0.7rem;
            box-shadow: 0 2px 8px rgba(108,99,255,0.10);
        }
        .modal-content {
            border-radius: 1.2rem;
        }
        @media (max-width: 576px) {
            .page-header {
                padding: 1.2rem .7rem;
                border-radius: 1rem;
            }
            .table-rooms {
                border-radius: .7rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="page-header text-center mb-4">
            <span class="display-5"><i class="bi bi-gear"></i></span>
            <h2 class="mt-2 mb-2">จัดการห้อง</h2>
            <p>เพิ่ม แก้ไข หรือ ลบห้องเรียนและห้องประชุม</p>
        </div>

        <!-- ฟอร์มเพิ่มห้อง -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">ชื่อห้อง</label>
                        <input type="text" name="name" class="form-control" placeholder="ชื่อห้อง" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-primary">ความจุ</label>
                        <input type="number" name="capacity" class="form-control" placeholder="ความจุ" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-primary">สิ่งอำนวยความสะดวก</label>
                        <input type="text" name="facilities" class="form-control" placeholder="สิ่งอำนวยความสะดวก">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">รูปภาพ</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" name="add_room" class="btn btn-primary px-4"><i class="bi bi-plus-circle me-1"></i> เพิ่มห้อง</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ตารางห้อง -->
        <div class="card table-rooms shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
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
                                            <img src="<?= htmlspecialchars($room['image']) ?>" alt="room image" class="room-img border">
                                        <?php else: ?>
                                            <span class="text-muted">ไม่มีรูป</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($room['name']) ?></td>
                                    <td><span class="badge bg-primary"><?= $room['capacity'] ?> คน</span></td>
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
                                                                <label class="form-label">รูปภาพ</label>
                                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">ชื่อห้อง</label>
                                                                <input type="text" name="edit_name" class="form-control" value="<?= htmlspecialchars($room['name']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">ความจุ</label>
                                                                <input type="number" name="edit_capacity" class="form-control" value="<?= $room['capacity'] ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">สิ่งอำนวยความสะดวก</label>
                                                                <input type="text" name="edit_facilities" class="form-control" value="<?= htmlspecialchars($room['facilities']) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" name="edit_room" class="btn btn-success px-4">บันทึก</button>
                                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
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
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>