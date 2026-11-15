<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$db = get_db();
$product = null;
$related = [];

if ($db && $id > 0) {
    try {
        $stmt = $db->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        if ($product) {
            $relStmt = $db->prepare("
                SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = :cat_id AND p.id != :id 
                LIMIT 3
            ");
            $relStmt->execute([':cat_id' => $product['category_id'], ':id' => $id]);
            $related = $relStmt->fetchAll();
        }
    } catch (PDOException $e) {
        error_log("Product detail query error: " . $e->getMessage());
    }
}

if (!$product) {
    header("Location: products.php");
    exit;
}

$pageTitle = $product['name'];
require_once __DIR__ . '/includes/header.php';

$whatsappNumber = get_setting('whatsapp', '94777773441');
$waText = urlencode("Hello Gyps Lanka, I would like to inquire about: " . $product['name'] . " (SKU: " . ($product['sku'] ?? 'N/A') . ")");
$waUrl = "https://wa.me/{$whatsappNumber}?text={$waText}";
?>

<!-- Breadcrumbs -->
<div style="background:#fff; border-bottom:1px solid #e2e8f0; padding:12px 0;">
    <div class="container" style="font-size:0.85rem; color:#64748b; display:flex; align-items:center; gap:8px;">
        <a href="index.php">Home</a>
        <i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i>
        <a href="products.php">Products</a>
        <i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i>
        <a href="products.php?category=<?= urlencode($product['category_slug'] ?? '') ?>"><?= htmlspecialchars($product['category_name'] ?? 'Materials') ?></a>
        <i class="fa-solid fa-angle-right" style="font-size:0.75rem;"></i>
        <span style="color:var(--primary-navy); font-weight:600;"><?= htmlspecialchars($product['name']) ?></span>
    </div>
</div>

<section style="padding: 60px 0; background: #fff;">
    <div class="container">
        <div class="product-detail-grid">
            <!-- Product Image -->
            <div style="border-radius:var(--radius-lg); overflow:hidden; border:1px solid var(--border-light); box-shadow:var(--shadow-md); background:#f8fafc; padding:20px; text-align:center;">
                <img src="<?= htmlspecialchars($product['image'] ?: 'assets/images/logo.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-height:420px; margin:0 auto; object-fit:contain; border-radius:12px;">
            </div>

            <!-- Product Details -->
            <div>
                <div style="font-size:0.82rem; font-weight:700; color:var(--gold-dark); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">
                    <?= htmlspecialchars($product['category_name'] ?? 'Gypsum Materials') ?>
                </div>
                <h1 class="product-detail-title">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>

                <div style="display:flex; align-items:center; gap:15px; margin-bottom:20px;">
                    <div class="product-price-box">
                        <?= format_price($product['price'], $product['price_label']) ?>
                    </div>
                    <?php 
                        $stockClass = 'stock-in';
                        $stockText = 'In Stock';
                        if ($product['stock_status'] === 'low_stock') { $stockClass = 'stock-low'; $stockText = 'Low Stock'; }
                        if ($product['stock_status'] === 'out_of_stock') { $stockClass = 'stock-out'; $stockText = 'Out of Stock'; }
                        if ($product['stock_status'] === 'on_order') { $stockClass = 'stock-low'; $stockText = 'On Order'; }
                    ?>
                    <span class="product-badge-stock <?= $stockClass ?>" style="position:static;"><?= $stockText ?></span>
                    <?php if (!empty($product['sku'])): ?>
                        <span style="font-size:0.85rem; color:#64748b; font-weight:500;">SKU: <?= htmlspecialchars($product['sku']) ?></span>
                    <?php endif; ?>
                </div>

                <div style="font-size:1.02rem; color:#475569; line-height:1.7; margin-bottom:30px; border-top:1px solid #e2e8f0; padding-top:20px;">
                    <?= nl2br(htmlspecialchars($product['description'] ?: $product['short_desc'])) ?>
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:15px; margin-bottom:35px;">
                    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-gold" style="padding:14px 28px; font-size:1rem;">
                        <i class="fa-brands fa-whatsapp" style="font-size:1.2rem;"></i> Inquire via WhatsApp
                    </a>
                    <a href="#quoteModal" class="btn btn-outline-gold open-quote-modal" data-service="<?= htmlspecialchars($product['name']) ?>" style="color:var(--primary-navy); border-color:var(--gold); padding:14px 28px;">
                        <i class="fa-solid fa-file-invoice"></i> Request Quote / Quantity
                    </a>
                </div>

                <!-- Product Guarantees -->
                <div class="product-guarantees-grid">
                    <div style="text-align:center;">
                        <i class="fa-solid fa-certificate text-gold" style="font-size:1.4rem; margin-bottom:6px;"></i>
                        <div style="font-size:0.85rem; font-weight:700; color:var(--primary-navy);">SLS Standard</div>
                        <div style="font-size:0.75rem; color:#64748b;">Genuine Quality</div>
                    </div>
                    <div style="text-align:center;">
                        <i class="fa-solid fa-truck-fast text-gold" style="font-size:1.4rem; margin-bottom:6px;"></i>
                        <div style="font-size:0.85rem; font-weight:700; color:var(--primary-navy);">Island-wide</div>
                        <div style="font-size:0.75rem; color:#64748b;">Site Delivery</div>
                    </div>
                    <div style="text-align:center;">
                        <i class="fa-solid fa-tags text-gold" style="font-size:1.4rem; margin-bottom:6px;"></i>
                        <div style="font-size:0.85rem; font-weight:700; color:var(--primary-navy);">Bulk Rates</div>
                        <div style="font-size:0.75rem; color:#64748b;">Contractor Pricing</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related)): ?>
        <div style="margin-top:70px; border-top:1px solid #e2e8f0; padding-top:50px;">
            <h3 style="font-size:1.8rem; font-weight:800; color:var(--primary-navy); margin-bottom:25px;">
                Related <span class="text-gold">Products</span>
            </h3>
            <div class="products-grid" style="grid-template-columns:repeat(3, 1fr);">
                <?php foreach ($related as $rel): ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <img src="<?= htmlspecialchars($rel['image'] ?: 'assets/images/logo.jpg') ?>" alt="<?= htmlspecialchars($rel['name']) ?>">
                    </div>
                    <div class="product-body">
                        <h4 class="product-title"><?= htmlspecialchars($rel['name']) ?></h4>
                        <div class="product-footer">
                            <div class="product-price"><?= format_price($rel['price'], $rel['price_label']) ?></div>
                            <a href="product_detail.php?id=<?= $rel['id'] ?>" class="btn btn-gold btn-sm">View</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

