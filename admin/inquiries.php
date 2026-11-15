<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

// Handle status update
if (isset($_GET['status']) && isset($_GET['id'])) {
    $inqId = (int)$_GET['id'];
    $newStatus = sanitize($_GET['status']);
    if (in_array($newStatus, ['new', 'contacted', 'completed', 'cancelled'])) {
        try {
            $stmt = $db->prepare("UPDATE inquiries SET status = :s WHERE id = :id");
            $stmt->execute([':s' => $newStatus, ':id' => $inqId]);
            set_flash('success', 'Inquiry status updated to "' . ucfirst($newStatus) . '".');
            header("Location: inquiries.php");
            exit;
        } catch (PDOException $e) {
            set_flash('error', 'Status update error: ' . $e->getMessage());
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM inquiries WHERE id = :id");
            $stmt->execute([':id' => $delId]);
            set_flash('success', 'Inquiry record deleted.');
            header("Location: inquiries.php");
            exit;
        } catch (PDOException $e) {
            set_flash('error', 'Delete error: ' . $e->getMessage());
        }
    }
}

$inquiries = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM inquiries ORDER BY id DESC");
        $inquiries = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Inquiries error: " . $e->getMessage());
    }
}

$pageTitle = 'Customer Quote Inquiries';
require_once __DIR__ . '/includes/admin_header.php';
?>
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Quote Requests &amp; Inquiries (<?= count($inquiries) ?>)</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Inquiries submitted by customers through the website.</p>
        </div>
        <input type="text" id="tableSearch" placeholder="Search inquiries..." class="form-control" style="width:240px; padding:8px 12px; font-size:0.85rem;">
    </div>

    <div class="card-body" style="padding:0;">
        <div class="data-table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client Info</th>
                        <th>Service / Area</th>
                        <th>Message / Requirements</th>
                        <th>Status</th>
                        <th style="width:140px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inquiries)): ?>
                        <?php foreach ($inquiries as $inq): 
                            $cleanPhone = preg_replace('/[^0-9]/', '', $inq['phone']);
                            if (substr($cleanPhone, 0, 1) === '0') {
                                $cleanPhone = '94' . substr($cleanPhone, 1);
                            }
                            $waLink = "https://wa.me/" . $cleanPhone . "?text=" . urlencode("Hello " . $inq['name'] . ", this is Gyps Lanka following up on your ceiling inquiry for " . ($inq['service'] ?? 'our solutions') . ".");
                        ?>
                        <tr style="<?= $inq['status'] === 'new' ? 'background:rgba(212,175,55,0.05); font-weight:500;' : '' ?>">
                            <td style="white-space:nowrap; font-size:0.82rem; color:#64748b;">
                                <?= date('M d, Y', strtotime($inq['created_at'])) ?><br>
                                <?= date('h:i A', strtotime($inq['created_at'])) ?>
                            </td>
                            <td>
                                <strong style="color:var(--adm-navy);"><?= htmlspecialchars($inq['name']) ?></strong><br>
                                <span style="font-size:0.85rem; color:#475569; display:inline-flex; align-items:center; gap:5px;">
                                    <i class="fa-solid fa-phone" style="font-size:0.75rem; color:var(--adm-gold);"></i> <?= htmlspecialchars($inq['phone']) ?>
                                </span>
                                <?php if (!empty($inq['email'])): ?>
                                    <br><span style="font-size:0.8rem; color:#64748b;"><?= htmlspecialchars($inq['email']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($inq['service'] ?? 'General') ?></strong>
                                <?php if (!empty($inq['room_size'])): ?>
                                    <div style="font-size:0.8rem; color:#64748b;">Area: <?= htmlspecialchars($inq['room_size']) ?></div>
                                <?php endif; ?>
                                <div style="font-size:0.78rem; color:#64748b;"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($inq['location'] ?? 'Sri Lanka') ?></div>
                            </td>
                            <td>
                                <div style="font-size:0.88rem; color:#334155; max-width:320px; line-height:1.4;">
                                    <?= nl2br(htmlspecialchars($inq['message'])) ?>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column; gap:5px;">
                                    <?php if ($inq['status'] === 'new'): ?>
                                        <span class="badge badge-danger">New Request</span>
                                    <?php elseif ($inq['status'] === 'contacted'): ?>
                                        <span class="badge badge-warning">Contacted</span>
                                    <?php elseif ($inq['status'] === 'completed'): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Cancelled</span>
                                    <?php endif; ?>

                                    <!-- Quick Status Changer -->
                                    <select onchange="window.location.href='inquiries.php?id=<?= $inq['id'] ?>&status=' + this.value" style="font-size:0.75rem; padding:3px 6px; border-radius:4px; border:1px solid #cbd5e1; background:#fff; cursor:pointer;">
                                        <option value="" disabled selected>Change Status</option>
                                        <option value="new">Mark as New</option>
                                        <option value="contacted">Mark Contacted</option>
                                        <option value="completed">Mark Completed</option>
                                        <option value="cancelled">Mark Cancelled</option>
                                    </select>
                                </div>
                            </td>
                            <td style="text-align:right;">
                                <div class="action-btns" style="justify-content:flex-end;">
                                    <a href="<?= $waLink ?>" target="_blank" class="btn-icon" style="background:#25d366; color:#fff; border-color:#25d366;" title="Chat via WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $inq['phone']) ?>" class="btn-icon" title="Call Client">
                                        <i class="fa-solid fa-phone"></i>
                                    </a>
                                    <a href="inquiries.php?delete=<?= $inq['id'] ?>" onclick="return confirmDelete('Delete this inquiry record?');" class="btn-icon delete" title="Delete Inquiry">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:35px; color:#64748b;">No quote requests received yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

