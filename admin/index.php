<?php
$pageTitle = 'Dashboard Overview';
require_once __DIR__ . '/includes/admin_header.php';

$totalProducts = 0;
$totalCategories = 0;
$totalProjects = 0;
$totalInquiries = 0;
$recentInquiries = [];
$recentProducts = [];

if ($db) {
    try {
        $totalProducts = (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $totalCategories = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        $totalProjects = (int)$db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
        $totalInquiries = (int)$db->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();

        // Recent Inquiries
        $stmt = $db->query("SELECT * FROM inquiries ORDER BY id DESC LIMIT 5");
        $recentInquiries = $stmt->fetchAll();

        // Recent Products
        $stmt = $db->query("
            SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC LIMIT 5
        ");
        $recentProducts = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
    }
}
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $totalProducts ?></h3>
            <p>Total Products</p>
        </div>
        <div class="stat-icon gold">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $totalCategories ?></h3>
            <p>Product Categories</p>
        </div>
        <div class="stat-icon blue">
            <i class="fa-solid fa-tags"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $totalProjects ?></h3>
            <p>Portfolio Projects</p>
        </div>
        <div class="stat-icon purple">
            <i class="fa-solid fa-images"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $totalInquiries ?></h3>
            <p>Quote Requests</p>
        </div>
        <div class="stat-icon green">
            <i class="fa-solid fa-envelope-open-text"></i>
        </div>
    </div>
</div>

<!-- Quick Actions Banner -->
<div style="background:#fff; border-radius:var(--adm-radius); padding:20px 24px; border:1px solid var(--adm-border); margin-bottom:30px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:15px;">
    <div>
        <h3 style="font-size:1.15rem; font-weight:700; color:var(--adm-navy); margin-bottom:4px;">Quick Actions</h3>
        <p style="color:var(--adm-muted); font-size:0.85rem;">Frequently used product and project management tasks.</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="product_add.php" class="btn btn-gold btn-sm" style="border-radius:6px;">
            <i class="fa-solid fa-plus"></i> Add New Product
        </a>
        <a href="hero_slides.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
            <i class="fa-solid fa-sliders"></i> Banner Slides
        </a>
        <a href="project_add.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
            <i class="fa-solid fa-camera"></i> Add Project
        </a>
        <a href="inquiries.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
            <i class="fa-solid fa-list-check"></i> View Inquiries
        </a>
    </div>
</div>

<!-- Tables 2-column layout -->
<div class="admin-dashboard-split">
    <!-- Recent Inquiries -->
    <div class="admin-card" style="margin-bottom:0;">
        <div class="card-header">
            <h3>Recent Quote Requests</h3>
            <a href="inquiries.php" style="font-size:0.82rem; color:var(--adm-gold); font-weight:700;">View All &rarr;</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="data-table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentInquiries)): ?>
                            <?php foreach ($recentInquiries as $inq): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($inq['name']) ?></strong><br>
                                    <span style="font-size:0.8rem; color:#64748b;"><?= htmlspecialchars($inq['phone']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($inq['service'] ?? 'General') ?></td>
                                <td><?= htmlspecialchars($inq['location'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($inq['status'] === 'new'): ?>
                                        <span class="badge badge-danger">New</span>
                                    <?php elseif ($inq['status'] === 'contacted'): ?>
                                        <span class="badge badge-warning">Contacted</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; padding:20px; color:#64748b;">No inquiries received yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="admin-card" style="margin-bottom:0;">
        <div class="card-header">
            <h3>Recently Added Products</h3>
            <a href="products.php" style="font-size:0.82rem; color:var(--adm-gold); font-weight:700;">View Catalog &rarr;</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="data-table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentProducts)): ?>
                            <?php foreach ($recentProducts as $prod): ?>
                            <tr>
                                <td style="display:flex; align-items:center; gap:10px;">
                                    <img src="../<?= htmlspecialchars($prod['image'] ?: 'assets/images/logo.jpg') ?>" class="table-thumb" alt="">
                                    <div>
                                        <a href="product_edit.php?id=<?= $prod['id'] ?>" style="font-weight:700; color:var(--adm-navy);">
                                            <?= htmlspecialchars($prod['name']) ?>
                                        </a>
                                        <div style="font-size:0.75rem; color:#64748b;"><?= htmlspecialchars($prod['sku'] ?? '') ?></div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($prod['category_name'] ?? 'Materials') ?></td>
                                <td style="font-weight:700;"><?= format_price($prod['price'], $prod['price_label']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; padding:20px; color:#64748b;">No products in database.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

