<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=1">
    <style>
        body {
            background: linear-gradient(120deg, #0d6efd 60%, #6c63ff 100%);
            min-height: 100vh;
        }
        .login-card {
            max-width: 400px;
            margin: 40px auto;
            background: #fff;
            border-radius: 1.2rem;
            box-shadow: 0 0.5rem 2rem rgba(0,0,0,0.10);
            padding: 2.5rem 2rem 2rem 2rem;
        }
        .login-card .form-label {
            font-weight: 500;
        }
        .login-card .btn-primary {
            font-size: 1.1rem;
            padding: 0.5rem 2rem;
            border-radius: 2rem;
        }
        .login-card .btn-secondary {
            border-radius: 2rem;
        }
        .login-icon {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 1rem;
            filter: drop-shadow(0 2px 8px #6c63ff88);
        }
        .login-title {
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 1.5rem;
            text-shadow: 1px 1px 8px #6c63ff33;
        }
        .login-card .form-control:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 0 0.2rem rgba(108,99,255,.15);
        }
        .login-card .alert {
            border-radius: 1rem;
        }
        .login-links {
            margin-top: 1.5rem;
            text-align: center;
        }
        .login-links a {
            color: #6c63ff;
            text-decoration: underline;
            margin: 0 0.5rem;
        }
        @media (max-width: 576px) {
            .login-card {
                padding: 1.2rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="login-card">
            <div class="text-center">
                <span class="login-icon"><i class="bi bi-person-circle"></i></span>
                <h2 class="login-title">เข้าสู่ระบบ</h2>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" name="username" id="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="d-grid gap-2 mb-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ</button>
                </div>
                <div class="d-grid gap-2 mb-2">
                    <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> กลับหน้าหลัก</a>
                </div>
            </form>
            <div class="login-links">
                <span>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></span>
            </div>
        </div>
    </div>
</body>
</html>