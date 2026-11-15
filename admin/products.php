<?php
$pageTitle = 'Manage Products';
require_once __DIR__ . '/includes/admin_header.php';

$products = [];
if ($db) {
    try {
        $stmt = $db->query("
            SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC
        ");
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Admin products list error: " . $e->getMessage());
    }
}
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>All Products (<?= count($products) ?>)</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Add, edit, and manage products displayed on your website.</p>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <input type="text" id="tableSearch" placeholder="Filter products..." class="form-control" style="width:220px; padding:8px 12px; font-size:0.85rem;">
            <a href="product_add.php" class="btn btn-gold btn-sm" style="border-radius:6px;">
                <i class="fa-solid fa-plus"></i> Add Product
            </a>
        </div>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="data-table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Image</th>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock Status</th>
                        <th>Featured</th>
                        <th style="width:110px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <img src="../<?= htmlspecialchars($p['image'] ?: 'assets/images/logo.jpg') ?>" class="table-thumb" alt="">
                            </td>
                            <td>
                                <strong style="color:var(--adm-navy);"><?= htmlspecialchars($p['name']) ?></strong>
                                <?php if (!empty($p['sku'])): ?>
                                    <div style="font-size:0.78rem; color:#64748b;">SKU: <?= htmlspecialchars($p['sku']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
                            <td style="font-weight:700;">
                                <?= format_price($p['price'], $p['price_label']) ?>
                            </td>
                            <td>
                                <?php 
                                    $st = $p['stock_status'];
                                    if ($st === 'in_stock') echo '<span class="badge badge-success">In Stock</span>';
                                    elseif ($st === 'low_stock') echo '<span class="badge badge-warning">Low Stock</span>';
                                    elseif ($st === 'out_of_stock') echo '<span class="badge badge-danger">Out of Stock</span>';
                                    else echo '<span class="badge badge-info">On Order</span>';
                                ?>
                            </td>
                            <td>
                                <?php if ($p['is_featured']): ?>
                                    <span style="color:#eab308; font-size:0.9rem;" title="Featured on Homepage">
                                        <i class="fa-solid fa-star"></i> Featured
                                    </span>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size:0.85rem;">Standard</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <div class="action-btns" style="justify-content:flex-end;">
                                    <a href="../product_detail.php?id=<?= $p['id'] ?>" target="_blank" class="btn-icon" title="View Live Product">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn-icon" title="Edit Product">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="product_delete.php?id=<?= $p['id'] ?>" onclick="return confirmDelete('Are you sure you want to permanently delete \'<?= addslashes(htmlspecialchars($p['name'])) ?>\'?');" class="btn-icon delete" title="Delete Product">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:35px; color:#64748b;">
                                No products found. Click "Add Product" to create your first listing.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

