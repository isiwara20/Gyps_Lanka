<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$error = '';

if (is_admin_logged_in()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $db = get_db();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
                $stmt->execute([':u' => $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['gyps_admin_logged_in'] = true;
                    $_SESSION['gyps_admin_id'] = $user['id'];
                    $_SESSION['gyps_admin_name'] = $user['name'];
                    $_SESSION['gyps_admin_username'] = $user['username'];
                    header("Location: index.php");
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'Database connection failed. Please ensure MySQL is running in XAMPP.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Gyps Lanka</title>
    <link rel="icon" type="image/jpeg" href="../assets/images/logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: radial-gradient(circle at center, #142848 0%, #0b1728 70%, #060e19 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 18px;
            width: 100%;
            max-width: 440px;
            padding: 40px 35px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(212, 175, 55, 0.4);
        }
        .login-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-brand img {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            border: 2px solid var(--gold);
            margin: 0 auto 12px;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.4);
        }
        .login-brand h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--primary-navy);
        }
        .login-brand p {
            font-size: 0.85rem;
            color: #64748b;
        }
        @media (max-width: 480px) {
            body {
                padding: 12px;
            }
            .login-card {
                padding: 28px 18px;
                border-radius: 14px;
            }
            .login-brand h2 {
                font-size: 1.35rem;
            }
            .login-brand img {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-brand">
        <img src="../assets/images/logo.jpg" alt="Gyps Lanka">
        <h2>GYPS<span class="text-gold">LANKA</span></h2>
        <p>Ceiling &amp; Interior Admin Dashboard</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="flash-alert flash-error" style="margin-bottom: 20px;">
            <div><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <div style="position:relative;">
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter admin username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:25px;">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter admin password" required value="">
        </div>

        <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-size: 1rem;">
            Sign In to Dashboard <i class="fa-solid fa-arrow-right-to-bracket" style="margin-left: 6px;"></i>
        </button>
    </form>

    <div style="text-align:center; margin-top:24px;">
        <a href="../index.php" style="font-size:0.85rem; color:#64748b; display:inline-flex; align-items:center; gap:6px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Gyps Lanka Website
        </a>
    </div>
</div>

</body>
</html>

