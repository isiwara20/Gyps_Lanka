<?php
// ==============================================================
// GYPS LANKA - One-Click Database Setup & Diagnostics
// ==============================================================

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gyps_lanka_db');

$messages = [];
$status = 'ready';

if (isset($_POST['run_setup']) || isset($_GET['auto'])) {
    try {
        // Step 1: Connect to MySQL server
        $pdoServer = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $messages[] = ['type' => 'success', 'text' => 'Connected to MySQL server on ' . DB_HOST];

        // Step 2: Create database
        $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $messages[] = ['type' => 'success', 'text' => 'Database `' . DB_NAME . '` checked / created.'];

        // Step 3: Reconnect to gyps_lanka_db
        $pdoDb = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Step 4: Import database.sql
        $sqlPath = __DIR__ . '/database.sql';
        if (file_exists($sqlPath)) {
            $sql = file_get_contents($sqlPath);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            $count = 0;
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    try {
                        $pdoDb->exec($stmt);
                        $count++;
                    } catch (Exception $e) {
                        // ignore duplicate keys during re-run
                    }
                }
            }
            $messages[] = ['type' => 'success', 'text' => "Successfully imported {$count} database structures & sample records!"];
            $status = 'success';
        } else {
            $messages[] = ['type' => 'error', 'text' => 'database.sql file was not found.'];
            $status = 'error';
        }
    } catch (PDOException $e) {
        $messages[] = ['type' => 'error', 'text' => 'Connection failed: ' . $e->getMessage()];
        $status = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup | Gyps Lanka</title>
    <link rel="icon" type="image/jpeg" href="assets/images/logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: radial-gradient(circle at center, #142848 0%, #0b1728 70%, #060e19 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-card {
            background: #fff;
            border-radius: 20px;
            max-width: 600px;
            width: 100%;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            border: 1px solid rgba(212,175,55,0.4);
        }
    </style>
</head>
<body>

<div class="setup-card">
    <div style="text-align:center; margin-bottom:25px;">
        <img src="assets/images/logo.jpg" alt="Gyps Lanka" style="width:75px; height:75px; border-radius:50%; border:2px solid var(--gold); margin:0 auto 12px;">
        <h2 style="font-size:1.8rem; font-weight:800; color:var(--primary-navy);">GYPS<span class="text-gold">LANKA</span></h2>
        <p style="color:#64748b; font-size:0.92rem;">One-Click Database Setup &amp; phpMyAdmin Helper</p>
    </div>

    <?php if (!empty($messages)): ?>
        <div style="margin-bottom:20px;">
            <?php foreach ($messages as $msg): ?>
                <div class="flash-alert flash-<?= $msg['type'] ?>" style="margin-bottom:8px;">
                    <div>
                        <i class="fa-solid <?= $msg['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                        <?= htmlspecialchars($msg['text']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($status === 'success'): ?>
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:20px; border-radius:12px; margin-bottom:25px; text-align:center;">
            <i class="fa-solid fa-circle-check" style="color:#16a34a; font-size:2.4rem; margin-bottom:10px;"></i>
            <h3 style="font-size:1.3rem; font-weight:700; color:#166534; margin-bottom:6px;">Setup Complete!</h3>
            <p style="font-size:0.9rem; color:#15803d; line-height:1.5;">
                Your MySQL database <code>gyps_lanka_db</code> has been created and populated with products, categories, sample projects, and settings.
            </p>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px;">
            <a href="index.php" class="btn btn-gold" style="padding:14px; font-size:1rem;">
                <i class="fa-solid fa-globe"></i> Visit Website Homepage
            </a>
            <a href="admin/login.php" class="btn btn-outline-gold" style="color:var(--primary-navy); border-color:var(--gold); padding:14px;">
                <i class="fa-solid fa-lock"></i> Go to Admin Dashboard (admin / admin123)
            </a>
            <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=gyps_lanka_db" target="_blank" style="text-align:center; font-size:0.85rem; color:#64748b; margin-top:5px;">
                <i class="fa-solid fa-database"></i> Open in phpMyAdmin
            </a>
        </div>
    <?php else: ?>
        <p style="color:#475569; font-size:0.95rem; line-height:1.6; margin-bottom:20px;">
            This tool initializes the <code>gyps_lanka_db</code> database in your local MySQL / phpMyAdmin and imports all the products, services, recent projects, and default administrator credentials.
        </p>

        <form action="setup.php" method="POST">
            <input type="hidden" name="run_setup" value="1">
            <button type="submit" class="btn btn-gold" style="width:100%; padding:15px; font-size:1.05rem;">
                <i class="fa-solid fa-bolt"></i> Run Auto-Setup Now
            </button>
        </form>

        <div style="margin-top:25px; padding-top:20px; border-top:1px solid #e2e8f0; font-size:0.85rem; color:#64748b;">
            <strong>Manual Setup in phpMyAdmin:</strong>
            <ol style="margin-left:20px; margin-top:6px; line-height:1.6;">
                <li>Start <strong>Apache</strong> and <strong>MySQL</strong> in the XAMPP Control Panel.</li>
                <li>Open <code>http://localhost/phpmyadmin/</code></li>
                <li>Click <strong>Import</strong> tab.</li>
                <li>Choose <code>database.sql</code> from <code>Gyps_Lanka</code> directory and click <strong>Import</strong>.</li>
            </ol>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

