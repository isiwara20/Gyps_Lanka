<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$categories = [];

if ($db && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        $catStmt = $db->query("SELECT * FROM categories ORDER BY display_order ASC, name ASC");
        $categories = $catStmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching product for edit: " . $e->getMessage());
    }
}

if (!$product) {
    set_flash('error', 'Product not found.');
    header("Location: products.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = sanitize($_POST['name'] ?? '');
    $categoryId   = (int)($_POST['category_id'] ?? 0);
    $sku          = sanitize($_POST['sku'] ?? '');
    $price        = !empty($_POST['price']) ? (float)$_POST['price'] : null;
    $priceLabel   = sanitize($_POST['price_label'] ?? 'LKR');
    $stockStatus  = sanitize($_POST['stock_status'] ?? 'in_stock');
    $isFeatured   = isset($_POST['is_featured']) ? 1 : 0;
    $shortDesc    = sanitize($_POST['short_desc'] ?? '');
    $description  = trim($_POST['description'] ?? '');

    if (empty($name)) {
        set_flash('error', 'Product name cannot be empty.');
    } elseif ($categoryId <= 0) {
        set_flash('error', 'Please select a valid category.');
    } else {
        $imagePath = $product['image'];
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = upload_image_file($_FILES['product_image'], 'products');
            if ($uploadRes['success']) {
                $imagePath = $uploadRes['path'];
            } else {
                set_flash('error', 'Image upload failed: ' . $uploadRes['error']);
                header("Location: product_edit.php?id=" . $id);
                exit;
            }
        }

        if ($db) {
            try {
                $stmt = $db->prepare("
                    UPDATE products 
                    SET category_id = :cat_id, name = :name, sku = :sku, price = :price, 
                        price_label = :price_label, short_desc = :short_desc, description = :desc, 
                        image = :image, stock_status = :stock, is_featured = :featured
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':cat_id'      => $categoryId,
                    ':name'        => $name,
                    ':sku'         => $sku,
                    ':price'       => $price,
                    ':price_label' => $priceLabel,
                    ':short_desc'  => $shortDesc,
                    ':desc'        => $description,
                    ':image'       => $imagePath,
                    ':stock'       => $stockStatus,
                    ':featured'    => $isFeatured,
                    ':id'          => $id
                ]);

                set_flash('success', 'Product "' . htmlspecialchars($name) . '" updated successfully!');
                header("Location: products.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Edit Product';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Edit Product: <?= htmlspecialchars($product['name']) ?></h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Update pricing, specifications, image or stock availability.</p>
        </div>
        <a href="products.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <div class="card-body">
        <form action="product_edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <!-- Product Name -->
                <div class="form-group">
                    <label class="form-label" for="p_name">Product Name *</label>
                    <input type="text" id="p_name" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label class="form-label" for="p_category">Category *</label>
                    <select id="p_category" name="category_id" class="form-control" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($product['category_id'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SKU -->
                <div class="form-group">
                    <label class="form-label" for="p_sku">Product SKU / Code</label>
                    <input type="text" id="p_sku" name="sku" class="form-control" value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
                </div>

                <!-- Price & Unit -->
                <div class="form-group">
                    <label class="form-label" for="p_price">Price (LKR)</label>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <input type="number" step="0.01" min="0" id="p_price" name="price" class="form-control" value="<?= htmlspecialchars($product['price'] ?? '') ?>">
                        <input type="text" name="price_label" class="form-control" value="<?= htmlspecialchars($product['price_label'] ?? 'LKR') ?>">
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="form-group">
                    <label class="form-label" for="p_stock">Stock Availability</label>
                    <select id="p_stock" name="stock_status" class="form-control">
                        <option value="in_stock" <?= ($product['stock_status'] === 'in_stock') ? 'selected' : '' ?>>In Stock (Available)</option>
                        <option value="low_stock" <?= ($product['stock_status'] === 'low_stock') ? 'selected' : '' ?>>Low Stock</option>
                        <option value="out_of_stock" <?= ($product['stock_status'] === 'out_of_stock') ? 'selected' : '' ?>>Out of Stock</option>
                        <option value="on_order" <?= ($product['stock_status'] === 'on_order') ? 'selected' : '' ?>>On Order / Pre-order</option>
                    </select>
                </div>

                <!-- Featured Toggle -->
                <div class="form-group" style="display:flex; flex-direction:column; justify-content:center;">
                    <label class="form-label">Homepage Display</label>
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:0.92rem; margin-top:6px;">
                        <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?> style="width:18px; height:18px; accent-color:var(--adm-gold);">
                        <span>Feature this product on homepage storefront</span>
                    </label>
                </div>

                <!-- Image Upload -->
                <div class="form-group full">
                    <label class="form-label" for="p_img">Change Product Photo (Leave blank to keep current photo)</label>
                    <input type="file" id="p_img" name="product_image" class="form-control preview-input" data-preview="imgPreviewBox" accept="image/*">
                    
                    <div class="image-preview-box" id="imgPreviewBox">
                        <img src="../<?= htmlspecialchars($product['image'] ?: 'assets/images/logo.jpg') ?>" alt="">
                    </div>
                </div>

                <!-- Short Description -->
                <div class="form-group full">
                    <label class="form-label" for="p_short">Short Summary (Shown in product cards)</label>
                    <input type="text" id="p_short" name="short_desc" class="form-control" value="<?= htmlspecialchars($product['short_desc'] ?? '') ?>">
                </div>

                <!-- Detailed Description -->
                <div class="form-group full">
                    <label class="form-label" for="p_desc">Detailed Specifications &amp; Features</label>
                    <textarea id="p_desc" name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="products.php" class="btn btn-outline-gold" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-gold" style="border-radius:6px; padding:12px 28px;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

