<?php
$pageTitle = 'Ceiling Products & Materials';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$categories = [];
$products = [];
$selectedCat = sanitize($_GET['category'] ?? '');
$searchQuery = sanitize($_GET['q'] ?? '');

if ($db) {
    try {
        $catStmt = $db->query("SELECT * FROM categories ORDER BY display_order ASC");
        $categories = $catStmt->fetchAll();

        $query = "SELECT p.*, c.name AS category_name, c.slug AS category_slug 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE 1=1";
        $params = [];

        if (!empty($selectedCat)) {
            $query .= " AND c.slug = :cat";
            $params[':cat'] = $selectedCat;
        }

        if (!empty($searchQuery)) {
            $query .= " AND (p.name LIKE :search OR p.short_desc LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $searchQuery . '%';
        }

        $query .= " ORDER BY p.is_featured DESC, p.id ASC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Products query error: " . $e->getMessage());
    }
}
?>

<!-- Products Header Banner -->
<section class="page-hero-section">
    <div class="container">
        <h1 class="page-hero-title">
            Ceiling &amp; Gypsum <span class="text-gold">Products</span>
        </h1>
        <p class="page-hero-desc" style="margin-bottom:25px;">
            Certified high-performance gypsum boards, galvanized metal frameworks, LED cove profiles, and finishing materials for residential and commercial projects across Sri Lanka.
        </p>

        <!-- Search Bar -->
        <form action="products.php" method="GET" style="max-width:500px; margin:0 auto; display:flex; gap:10px;">
            <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search boards, channels, LED profiles..." class="form-control" style="background:#fff; border-radius:50px; padding-left:20px;">
            <?php if (!empty($selectedCat)): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCat) ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-gold" style="border-radius:50px; padding:10px 24px;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>
</section>

<!-- Products Main Content -->
<section style="padding: 60px 0; background: var(--bg-light);">
    <div class="container">
        <!-- Category Filter Pills -->
        <div style="display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin-bottom:40px;">
            <a href="products.php" class="btn btn-sm <?= empty($selectedCat) ? 'btn-gold' : 'btn-outline-gold' ?>" style="<?= empty($selectedCat) ? '' : 'color:var(--primary-navy); border-color:#cbd5e1; background:#fff;' ?>">
                All Categories
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="products.php?category=<?= urlencode($cat['slug']) ?>" class="btn btn-sm <?= ($selectedCat === $cat['slug']) ? 'btn-gold' : 'btn-outline-gold' ?>" style="<?= ($selectedCat === $cat['slug']) ? '' : 'color:var(--primary-navy); border-color:#cbd5e1; background:#fff;' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php foreach ($products as $prod): ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <img src="<?= htmlspecialchars($prod['image'] ?: 'assets/images/logo.jpg') ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                        <?php 
                            $stockClass = 'stock-in';
                            $stockText = 'In Stock';
                            if ($prod['stock_status'] === 'low_stock') { $stockClass = 'stock-low'; $stockText = 'Low Stock'; }
                            if ($prod['stock_status'] === 'out_of_stock') { $stockClass = 'stock-out'; $stockText = 'Out of Stock'; }
                            if ($prod['stock_status'] === 'on_order') { $stockClass = 'stock-low'; $stockText = 'On Order'; }
                        ?>
                        <span class="product-badge-stock <?= $stockClass ?>"><?= $stockText ?></span>
                    </div>
                    <div class="product-body">
                        <div class="product-cat"><?= htmlspecialchars($prod['category_name'] ?? 'Materials') ?></div>
                        <h4 class="product-title"><?= htmlspecialchars($prod['name']) ?></h4>
                        <p class="product-desc"><?= htmlspecialchars(truncate_text($prod['short_desc'] ?: $prod['description'], 90)) ?></p>
                        <div class="product-footer">
                            <div class="product-price">
                                <?= format_price($prod['price'], $prod['price_label']) ?>
                            </div>
                            <a href="product_detail.php?id=<?= $prod['id'] ?>" class="btn btn-gold btn-sm">
                                View Details <i class="fa-solid fa-arrow-right" style="font-size:0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding:60px 20px; background:#fff; border-radius:12px; border:1px solid #e2e8f0;">
                <i class="fa-solid fa-box-open" style="font-size:3rem; color:var(--gold); margin-bottom:15px;"></i>
                <h3 style="font-size:1.4rem; font-weight:700; color:var(--primary-navy); margin-bottom:8px;">No products found</h3>
                <p style="color:#64748b; margin-bottom:20px;">Try adjusting your category selection or search keywords.</p>
                <a href="products.php" class="btn btn-gold">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

