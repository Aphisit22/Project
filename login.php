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
            position: relative;
            background:
                radial-gradient(1200px 600px at -10% -20%, rgba(255, 255, 255, 0.10), transparent 40%),
                linear-gradient(135deg, var(--lux-primary) 0%, var(--lux-secondary) 55%, #8a7bff 100%);
            overflow: hidden;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
        }

        .register-wrapper {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(108, 99, 255, 0.15);
            padding: 2.5rem 2rem 2rem 2rem;
            margin: 40px auto;
            border: 1px solid #e0e7ff;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, #6c63ff55 60%, transparent 100%);
            z-index: 0;
        }

        .login-icon {
            font-size: 3.5rem;
            color: #6c63ff;
            margin-bottom: 1rem;
            filter: drop-shadow(0 2px 8px #0d6efd88);
            z-index: 1;
            position: relative;
        }

        .login-title {
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 1.5rem;
            text-shadow: 1px 1px 8px #6c63ff33;
            z-index: 1;
            position: relative;
        }

        .login-card .form-label {
            font-weight: 500;
            color: #0d6efd;
        }

        .login-card .form-control {
            border-radius: 1rem;
            font-size: 1.1rem;
        }

        .login-card .form-control:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 0 0.2rem rgba(108, 99, 255, .15);
        }

        .login-card .btn-primary {
            font-size: 1.1rem;
            padding: 0.6rem 2rem;
            border-radius: 2rem;
            background: linear-gradient(90deg, #0d6efd 60%, #6c63ff 100%);
            border: none;
            color: #fff;
            box-shadow: 0 2px 12px rgba(108, 99, 255, 0.10);
            font-weight: 600;
        }

        .login-card .btn-primary:hover {
            background: linear-gradient(90deg, #6c63ff 60%, #0d6efd 100%);
            color: #fff;
        }

        .login-card .btn-secondary {
            border-radius: 2rem;
            background: #fff;
            color: #6c63ff;
            border: 1px solid #6c63ff;
            font-weight: 600;
        }

        .login-card .btn-secondary:hover {
            background: #6c63ff;
            color: #fff;
        }

        .login-card .alert {
            border-radius: 1rem;
            font-size: 1rem;
            margin-bottom: 1.2rem;
        }

        .login-links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 1rem;
        }

        .login-links a {
            color: #6c63ff;
            text-decoration: underline;
            margin: 0 0.5rem;
            font-weight: 500;
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
    <div class="register-wrapper">
        <div class="login-card">
            <div class="text-center">
                <span class="login-icon"><i class="bi bi-person-plus"></i></span>
                <h2 class="login-title">เข้าสู่ระบบ</h2>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="d-grid gap-2 mb-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> เข้าสู่ระบบ</button>
                </div>
                <div class="d-grid gap-2 mb-2">
                    <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> กลับหน้าหลัก</a>
                </div>
            </form>
            <div class="login-links">
                <span>ไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></span>
            </div>
        </div>
    </div>
</body>

</html>