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
            <h3>Ceiling &amp; Interior Services (<?= count($services) ?>)</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Overview of the 5 services presented across the website and homepage cards.</p>
        </div>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="data-table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:70px;">Image</th>
                        <th>Service Title</th>
                        <th>Short Description</th>
                        <th>Order</th>
                        <th style="width:90px; text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $srv): ?>
                    <tr>
                        <td>
                            <img src="../<?= htmlspecialchars($srv['image']) ?>" class="table-thumb" alt="">
                        </td>
                        <td>
                            <strong style="color:var(--adm-navy);"><?= htmlspecialchars($srv['title']) ?></strong>
                            <div style="font-size:0.78rem; color:#64748b;">Slug: <code><?= htmlspecialchars($srv['slug']) ?></code></div>
                        </td>
                        <td style="color:#475569; max-width:350px;">
                            <?= htmlspecialchars($srv['short_desc']) ?>
                        </td>
                        <td><?= $srv['display_order'] ?></td>
                        <td style="text-align:right;">
                            <a href="../services.php#<?= $srv['slug'] ?>" target="_blank" class="btn-icon" title="View on site" style="display:inline-flex;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

