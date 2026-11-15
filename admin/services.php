<?php
$pageTitle = 'Services Management';
require_once __DIR__ . '/includes/admin_header.php';

$services = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM services ORDER BY display_order ASC");
        $services = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Services list error: " . $e->getMessage());
    }
}
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3><i class="fa-solid fa-screwdriver-wrench" style="color:var(--adm-gold); margin-right:8px;"></i> Ceiling &amp; Interior Services (<?= count($services) ?>)</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted); margin:0;">Edit titles, descriptions, images, bullet points, and icons for services across the site.</p>
        </div>
        <a href="service_add.php" class="btn btn-gold btn-sm" style="border-radius:6px; display:inline-flex; align-items:center; gap:6px;">
            <i class="fa-solid fa-circle-plus"></i> Add New Service
        </a>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="data-table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:70px;">Image</th>
                        <th>Service Title</th>
                        <th style="width:110px;">Icon</th>
                        <th>Short Description</th>
                        <th style="width:70px; text-align:center;">Order</th>
                        <th style="width:160px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $srv): ?>
                        <tr>
                            <td>
                                <a href="service_edit.php?id=<?= $srv['id'] ?>">
                                    <img src="../<?= htmlspecialchars($srv['image'] ?: 'assets/images/service-1.jpg') ?>" class="table-thumb" alt="" style="width:55px; height:42px; object-fit:cover; border-radius:6px; border:1px solid #cbd5e1;">
                                </a>
                            </td>
                            <td>
                                <a href="service_edit.php?id=<?= $srv['id'] ?>" style="font-weight:700; color:var(--adm-navy); text-decoration:none;">
                                    <?= htmlspecialchars($srv['title']) ?>
                                </a>
                                <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                                    Slug: <code><?= htmlspecialchars($srv['slug']) ?></code>
                                </div>
                            </td>
                            <td>
                                <span style="display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; color:#334155;">
                                    <i class="fa-solid fa-<?= htmlspecialchars($srv['icon_svg'] ?: 'layer-group') ?>" style="color:var(--adm-gold);"></i>
                                    <?= htmlspecialchars($srv['icon_svg'] ?: 'layers') ?>
                                </span>
                            </td>
                            <td style="color:#475569; max-width:320px; font-size:0.85rem; line-height:1.45;">
                                <?= htmlspecialchars($srv['short_desc'] ?? '') ?>
                            </td>
                            <td style="text-align:center; font-weight:700; color:var(--adm-navy);">
                                <?= (int)$srv['display_order'] ?>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; align-items:center; gap:6px;">
                                    <a href="service_edit.php?id=<?= $srv['id'] ?>" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; background:#fff; padding:5px 12px; font-size:0.8rem; border-radius:6px; text-decoration:none;" title="Edit Service">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="../services.php#<?= htmlspecialchars($srv['slug']) ?>" target="_blank" class="btn-icon" title="View on site" style="display:inline-flex;">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                    <a href="service_delete.php?id=<?= $srv['id'] ?>" onclick="return confirm('Are you sure you want to delete \'<?= htmlspecialchars(addslashes($srv['title'])) ?>\'?');" class="btn-icon danger" title="Delete Service" style="display:inline-flex;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:#64748b;">
                                No services found. Click "Add New Service" to create one.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
