<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

// Handle General Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $keys = [
        'site_name', 'site_tagline', 'phone', 'whatsapp', 'email', 'website', 
        'location_country', 'address', 'working_hours', 'facebook_url', 
        'instagram_url', 'tiktok_url', 'youtube_url',
        'gmaps_embed_url', 'gmaps_directions_url'
    ];

    if ($db) {
        try {
            $stmt = $db->prepare("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (:k, :v) 
                ON DUPLICATE KEY UPDATE setting_value = :v2
            ");
            foreach ($keys as $k) {
                if ($k === 'gmaps_embed_url') {
                    $val = clean_gmaps_embed_url($_POST[$k] ?? '');
                } else {
                    $val = sanitize($_POST[$k] ?? '');
                }
                $stmt->execute([':k' => $k, ':v' => $val, ':v2' => $val]);
            }
            set_flash('success', 'Website and location settings have been updated successfully.');
            header("Location: settings.php");
            exit;
        } catch (PDOException $e) {
            set_flash('error', 'Settings update failed: ' . $e->getMessage());
        }
    }
}

// Handle SMTP / Email Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_smtp'])) {
    $smtpKeys = [
        'smtp_enabled', 'smtp_host', 'smtp_port', 'smtp_encryption',
        'smtp_username', 'smtp_from_email', 'smtp_from_name',
        'contact_recipient_email'
    ];

    if ($db) {
        try {
            $stmt = $db->prepare("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (:k, :v) 
                ON DUPLICATE KEY UPDATE setting_value = :v2
            ");

            foreach ($smtpKeys as $k) {
                $val = sanitize($_POST[$k] ?? '');
                $stmt->execute([':k' => $k, ':v' => $val, ':v2' => $val]);
            }

            // Update password only if a new one was typed
            if (!empty($_POST['smtp_password'])) {
                $pVal = trim($_POST['smtp_password']);
                $stmt->execute([':k' => 'smtp_password', ':v' => $pVal, ':v2' => $pVal]);
            }

            set_flash('success', 'SMTP email settings updated successfully.');
            header("Location: settings.php");
            exit;
        } catch (PDOException $e) {
            set_flash('error', 'SMTP update failed: ' . $e->getMessage());
        }
    }
}

// Handle Send Test Email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_test_email'])) {
    $testTo = sanitize($_POST['test_email_recipient'] ?? 'info@gypslanka.com');
    $result = send_test_smtp_email($testTo);
    if ($result['success']) {
        set_flash('success', "Test email sent successfully via SMTP to {$testTo}!");
    } else {
        set_flash('error', "SMTP Test failed: " . ($result['error'] ?: 'Could not connect. Please verify host, port, username, and password.'));
    }
    header("Location: settings.php");
    exit;
}

// Handle Admin Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPass = trim($_POST['current_password'] ?? '');
    $newPass     = trim($_POST['new_password'] ?? '');
    $confirmPass = trim($_POST['confirm_password'] ?? '');

    if (empty($currentPass) || empty($newPass)) {
        set_flash('error', 'Please fill in all password fields.');
    } elseif ($newPass !== $confirmPass) {
        set_flash('error', 'New password and confirmation do not match.');
    } elseif (strlen($newPass) < 6) {
        set_flash('error', 'New password must be at least 6 characters long.');
    } else {
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
                $stmt->execute([':id' => $_SESSION['gyps_admin_id'] ?? 1]);
                $user = $stmt->fetch();

                if ($user && password_verify($currentPass, $user['password'])) {
                    $newHash = password_hash($newPass, PASSWORD_BCRYPT);
                    $upStmt = $db->prepare("UPDATE users SET password = :p WHERE id = :id");
                    $upStmt->execute([':p' => $newHash, ':id' => $_SESSION['gyps_admin_id'] ?? 1]);
                    set_flash('success', 'Admin password changed successfully!');
                    header("Location: settings.php");
                    exit;
                } else {
                    set_flash('error', 'Current password is incorrect.');
                }
            } catch (PDOException $e) {
                set_flash('error', 'Password update error: ' . $e->getMessage());
            }
        }
    }
}

// Load current settings
$settings = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch (PDOException $e) {}
}

$pageTitle = 'Website & Company Settings';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display:grid; grid-template-columns: 1.6fr 1fr; gap:25px;">
    <!-- Company Information -->
    <div class="admin-card">
        <div class="card-header">
            <h3>Company &amp; Contact Details</h3>
        </div>
        <div class="card-body">
            <form action="settings.php" method="POST">
                <input type="hidden" name="update_settings" value="1">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Company / Brand Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? 'Gyps Lanka') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Brand Tagline</label>
                        <input type="text" name="site_tagline" class="form-control" value="<?= htmlspecialchars($settings['site_tagline'] ?? 'Ceiling & Interior Solutions - Turning Ceilings Into Art.') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hotline Phone Number *</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone'] ?? '077 777 3441') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">WhatsApp Number (e.g. 94777773441)</label>
                        <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($settings['whatsapp'] ?? '94777773441') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Official Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings['email'] ?? 'gypslanka@gmail.com') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Website Domain</label>
                        <input type="text" name="website" class="form-control" value="<?= htmlspecialchars($settings['website'] ?? 'gypslanka.com') ?>">
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Physical Workshop / Office Address</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($settings['address'] ?? 'No. 124, High Level Road, Maharagama, Sri Lanka') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Location Country / Region</label>
                        <input type="text" name="location_country" class="form-control" value="<?= htmlspecialchars($settings['location_country'] ?? 'Sri Lanka') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Working Hours</label>
                        <input type="text" name="working_hours" class="form-control" value="<?= htmlspecialchars($settings['working_hours'] ?? 'Mon - Sat: 8:00 AM - 6:00 PM') ?>">
                    </div>

                    <div class="form-group full">
                        <h4 style="font-size:0.95rem; font-weight:700; color:var(--adm-navy); margin:15px 0 5px;">
                            <i class="fa-solid fa-map-location-dot" style="color:var(--adm-gold);"></i> Google Maps Location (Contact Us Page)
                        </h4>
                        <p style="font-size:0.8rem; color:#64748b; margin-bottom:12px;">
                            Controls the interactive Google Map displayed at the bottom of the public <strong>Contact Us</strong> page.
                        </p>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Google Maps Embed URL or &lt;iframe&gt; Code</label>
                        <textarea name="gmaps_embed_url" id="adminGmapsInput" class="form-control" rows="3" placeholder="https://www.google.com/maps/embed?pb=... or paste &lt;iframe ...&gt;&lt;/iframe&gt;"><?= htmlspecialchars($settings['gmaps_embed_url'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.58586025134!2d79.82118635!3d6.9218335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a7003%3A0x320b2e4d32d3838d!2sColombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1700000000000!5m2!1sen!2slk') ?></textarea>
                        <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:6px; padding:10px 12px; font-size:0.75rem; color:#475569; margin-top:6px;">
                            <strong>How to get this link:</strong> Search your location on <a href="https://maps.google.com" target="_blank" style="color:var(--adm-gold); font-weight:700;">Google Maps</a> &rarr; Click <strong>Share</strong> &rarr; Select <strong>Embed a map</strong> &rarr; Click <strong>Copy HTML</strong> &rarr; Paste here. You can paste either the pure link or the full <code>&lt;iframe&gt;</code> code.
                        </div>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Google Maps "Get Directions" Link (Optional)</label>
                        <input type="text" name="gmaps_directions_url" class="form-control" placeholder="https://maps.google.com/?q=Gyps+Lanka" value="<?= htmlspecialchars($settings['gmaps_directions_url'] ?? 'https://maps.google.com/?q=Colombo+Sri+Lanka') ?>">
                        <small style="color:#64748b; font-size:0.75rem;">Opens in the Google Maps app for driving directions.</small>
                    </div>

                    <?php 
                    $currMapUrl = clean_gmaps_embed_url($settings['gmaps_embed_url'] ?? '');
                    if (!empty($currMapUrl)): 
                    ?>
                    <div class="form-group full">
                        <label class="form-label" style="display:flex; align-items:center; justify-content:space-between;">
                            <span>Current Map Live Preview:</span>
                            <span style="font-size:0.75rem; color:#16a34a; font-weight:600;"><i class="fa-solid fa-circle-check"></i> Connected to Contact Us Page</span>
                        </label>
                        <div style="border-radius:10px; overflow:hidden; height:200px; border:1px solid #e2e8f0; background:#0b1728;">
                            <iframe src="<?= htmlspecialchars($currMapUrl) ?>" width="100%" height="100%" style="border:0;" loading="lazy"></iframe>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group full">
                        <h4 style="font-size:0.95rem; font-weight:700; color:var(--adm-navy); margin:10px 0 5px;">Social Media Links</h4>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-brands fa-facebook" style="color:#1877f2;"></i> Facebook URL</label>
                        <input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? 'https://facebook.com/gypslanka') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-brands fa-instagram" style="color:#e1306c;"></i> Instagram URL</label>
                        <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? 'https://instagram.com/gypslanka') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-brands fa-tiktok"></i> TikTok URL</label>
                        <input type="url" name="tiktok_url" class="form-control" value="<?= htmlspecialchars($settings['tiktok_url'] ?? 'https://tiktok.com/@gypslanka') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-brands fa-youtube" style="color:#ff0000;"></i> YouTube URL</label>
                        <input type="url" name="youtube_url" class="form-control" value="<?= htmlspecialchars($settings['youtube_url'] ?? 'https://youtube.com/@gypslanka') ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-gold" style="border-radius:6px; padding:12px 28px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: SMTP Email Engine & Security -->
    <div>
        <!-- Email & SMTP Configuration -->
        <div class="admin-card">
            <div class="card-header">
                <div>
                    <h3>Email &amp; SMTP Engine</h3>
                    <p style="font-size:0.8rem; color:var(--adm-muted); margin:0;">Contact form emails sent to <strong>info@gypslanka.com</strong></p>
                </div>
            </div>
            <div class="card-body">
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:12px; margin-bottom:18px; font-size:0.82rem; color:#166534; line-height:1.5;">
                    <i class="fa-solid fa-circle-check" style="color:#16a34a; margin-right:4px;"></i>
                    Inquiries submitted on the website will be emailed to <strong><?= htmlspecialchars($settings['contact_recipient_email'] ?? 'info@gypslanka.com') ?></strong> from <strong><?= htmlspecialchars($settings['smtp_from_email'] ?? 'support@gypslanka.com') ?></strong>.
                </div>

                <form action="settings.php" method="POST">
                    <input type="hidden" name="update_smtp" value="1">

                    <div class="form-group">
                        <label class="form-label">Notification Recipient (To:)</label>
                        <input type="email" name="contact_recipient_email" class="form-control" value="<?= htmlspecialchars($settings['contact_recipient_email'] ?? 'info@gypslanka.com') ?>" required>
                        <small style="color:#64748b; font-size:0.75rem;">Where contact messages are delivered.</small>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                        <div class="form-group">
                            <label class="form-label">Sender Email (From:)</label>
                            <input type="email" name="smtp_from_email" class="form-control" value="<?= htmlspecialchars($settings['smtp_from_email'] ?? 'support@gypslanka.com') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sender Name</label>
                            <input type="text" name="smtp_from_name" class="form-control" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? 'Gyps Lanka Support') ?>" required>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1.4fr 1fr; gap:12px;">
                        <div class="form-group">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="smtp_host" class="form-control" placeholder="mail.gypslanka.com" value="<?= htmlspecialchars($settings['smtp_host'] ?? 'mail.gypslanka.com') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">SMTP Port</label>
                            <input type="number" name="smtp_port" class="form-control" value="<?= htmlspecialchars($settings['smtp_port'] ?? '465') ?>" required>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1.2fr; gap:12px;">
                        <div class="form-group">
                            <label class="form-label">Encryption</label>
                            <select name="smtp_encryption" class="form-control">
                                <option value="ssl" <?= (($settings['smtp_encryption'] ?? 'ssl') === 'ssl') ? 'selected' : '' ?>>SSL (Port 465)</option>
                                <option value="tls" <?= (($settings['smtp_encryption'] ?? 'ssl') === 'tls') ? 'selected' : '' ?>>TLS (Port 587)</option>
                                <option value="none" <?= (($settings['smtp_encryption'] ?? 'ssl') === 'none') ? 'selected' : '' ?>>None (Port 25)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">SMTP Status</label>
                            <select name="smtp_enabled" class="form-control">
                                <option value="1" <?= (($settings['smtp_enabled'] ?? '1') === '1') ? 'selected' : '' ?>>Enabled (Active)</option>
                                <option value="0" <?= (($settings['smtp_enabled'] ?? '1') === '0') ? 'selected' : '' ?>>Disabled</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" name="smtp_username" class="form-control" placeholder="support@gypslanka.com" value="<?= htmlspecialchars($settings['smtp_username'] ?? 'support@gypslanka.com') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">SMTP Password</label>
                        <input type="password" name="smtp_password" class="form-control" placeholder="<?= !empty($settings['smtp_password']) ? '•••••••••••• (Leave blank to keep)' : 'Enter mailbox password' ?>">
                        <small style="color:#64748b; font-size:0.75rem;">Password for your <code>support@gypslanka.com</code> email account.</small>
                    </div>

                    <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:11px; font-size:0.9rem; border-radius:6px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save SMTP Settings
                    </button>
                </form>

                <!-- Send Test Email -->
                <div style="margin-top:24px; padding-top:20px; border-top:1px dashed #cbd5e1;">
                    <h4 style="font-size:0.88rem; font-weight:700; color:var(--adm-navy); margin-bottom:10px;">
                        <i class="fa-solid fa-paper-plane" style="color:var(--adm-gold);"></i> Test SMTP Connection
                    </h4>
                    <form action="settings.php" method="POST" style="display:flex; gap:8px;">
                        <input type="hidden" name="send_test_email" value="1">
                        <input type="email" name="test_email_recipient" class="form-control" style="font-size:0.85rem;" placeholder="info@gypslanka.com" value="<?= htmlspecialchars($settings['contact_recipient_email'] ?? 'info@gypslanka.com') ?>" required>
                        <button type="submit" class="btn btn-outline-gold btn-sm" style="flex-shrink:0; background:#fff; color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px;">
                            Test
                        </button>
                    </form>
                    <div style="font-size:0.72rem; color:#94a3b8; margin-top:5px;">
                        Sends a test email to verify host and credentials.
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Account Security -->
        <div class="admin-card">
            <div class="card-header">
                <h3>Change Admin Password</h3>
            </div>
            <div class="card-body">
                <form action="settings.php" method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-group">
                        <label class="form-label" for="curr_pass">Current Password *</label>
                        <input type="password" id="curr_pass" name="current_password" class="form-control" placeholder="Enter existing password" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="new_pass">New Password *</label>
                        <input type="password" id="new_pass" name="new_password" class="form-control" placeholder="At least 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="conf_pass">Confirm New Password *</label>
                        <input type="password" id="conf_pass" name="confirm_password" class="form-control" placeholder="Re-enter new password" required>
                    </div>
                    <button type="submit" class="btn btn-outline-gold" style="width:100%; color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff; margin-top:10px;">
                        <i class="fa-solid fa-key"></i> Update Password
                    </button>
                </form>

                <div style="margin-top:25px; padding:12px; background:var(--adm-bg); border-radius:8px; border:1px solid var(--adm-border); font-size:0.8rem; color:var(--adm-muted);">
                    <i class="fa-solid fa-shield-halved" style="color:var(--adm-gold); margin-right:6px;"></i>
                    <strong>Security Tip:</strong> Use a combination of uppercase letters, numbers, and symbols to ensure your dashboard remains secure.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

