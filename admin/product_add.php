<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

$categories = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM categories ORDER BY display_order ASC, name ASC");
        $categories = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
    }
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

    $slug = url_slug($name);

    if (empty($name)) {
        set_flash('error', 'Product name is required.');
    } elseif ($categoryId <= 0) {
        set_flash('error', 'Please select a valid category.');
    } else {
        // Handle image upload
        $imagePath = 'assets/images/uploads/products/elephant_gypsum.jpg'; // default fallback
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = upload_image_file($_FILES['product_image'], 'products');
            if ($uploadRes['success']) {
                $imagePath = $uploadRes['path'];
            } else {
                set_flash('error', 'Image upload failed: ' . $uploadRes['error']);
                header("Location: product_add.php");
                exit;
            }
        }

        if ($db) {
            try {
                // Ensure slug is unique
                $checkStmt = $db->prepare("SELECT id FROM products WHERE slug = :s");
                $checkStmt->execute([':s' => $slug]);
                if ($checkStmt->fetch()) {
                    $slug .= '-' . time();
                }

                $stmt = $db->prepare("
                    INSERT INTO products (category_id, name, slug, sku, price, price_label, short_desc, description, image, stock_status, is_featured)
                    VALUES (:cat_id, :name, :slug, :sku, :price, :price_label, :short_desc, :desc, :image, :stock, :featured)
                ");
                $stmt->execute([
                    ':cat_id'      => $categoryId,
                    ':name'        => $name,
                    ':slug'        => $slug,
                    ':sku'         => $sku,
                    ':price'       => $price,
                    ':price_label' => $priceLabel,
                    ':short_desc'  => $shortDesc,
                    ':desc'        => $description,
                    ':image'       => $imagePath,
                    ':stock'       => $stockStatus,
                    ':featured'    => $isFeatured,
                ]);

                set_flash('success', 'Product "' . htmlspecialchars($name) . '" has been successfully added to catalog!');
                header("Location: products.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Add New Product';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Add New Product</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Publish a new gypsum board, channel, LED profile, or accessory.</p>
        </div>
        <a href="products.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <div class="card-body">
        <form action="product_add.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <!-- Product Name -->
                <div class="form-group">
                    <label class="form-label" for="p_name">Product Name *</label>
                    <input type="text" id="p_name" name="name" class="form-control" placeholder="e.g. Elephant Gypsum Board (9mm Standard)" required>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label class="form-label" for="p_category">Category *</label>
                    <select id="p_category" name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SKU -->
                <div class="form-group">
                    <label class="form-label" for="p_sku">Product SKU / Code</label>
                    <input type="text" id="p_sku" name="sku" class="form-control" placeholder="e.g. GYP-EL-09">
                </div>

                <!-- Price & Unit -->
                <div class="form-group">
                    <label class="form-label" for="p_price">Price (LKR)</label>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <input type="number" step="0.01" min="0" id="p_price" name="price" class="form-control" placeholder="e.g. 2450.00">
                        <input type="text" name="price_label" class="form-control" placeholder="Unit (e.g. LKR / Sheet, LKR / Box)" value="LKR / Sheet">
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="form-group">
                    <label class="form-label" for="p_stock">Stock Availability</label>
                    <select id="p_stock" name="stock_status" class="form-control">
                        <option value="in_stock" selected>In Stock (Available)</option>
                        <option value="low_stock">Low Stock (Limited Quantity)</option>
                        <option value="out_of_stock">Out of Stock</option>
                        <option value="on_order">On Order / Pre-order</option>
                    </select>
                </div>

                <!-- Featured Toggle -->
                <div class="form-group" style="display:flex; flex-direction:column; justify-content:center;">
                    <label class="form-label">Homepage Display</label>
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:0.92rem; margin-top:6px;">
                        <input type="checkbox" name="is_featured" value="1" style="width:18px; height:18px; accent-color:var(--adm-gold);">
                        <span>Feature this product on homepage storefront</span>
                    </label>
                </div>

                <!-- Image Upload -->
                <div class="form-group full">
                    <label class="form-label" for="p_img">Product Photo</label>
                    <input type="file" id="p_img" name="product_image" class="form-control preview-input" data-preview="imgPreviewBox" accept="image/*">
                    <div style="font-size:0.8rem; color:#64748b; margin-top:4px;">Supported formats: JPG, PNG, WEBP (Max 10MB). High resolution photo recommended.</div>
                    
                    <div class="image-preview-box" id="imgPreviewBox">
                        <span style="font-size:0.8rem; color:#94a3b8; text-align:center; padding:10px;">Image Preview</span>
                    </div>
                </div>

                <!-- Short Description -->
                <div class="form-group full">
                    <label class="form-label" for="p_short">Short Summary (Shown in product cards)</label>
                    <input type="text" id="p_short" name="short_desc" class="form-control" placeholder="Brief 1-line description of dimensions or main feature">
                </div>

                <!-- Detailed Description -->
                <div class="form-group full">
                    <label class="form-label" for="p_desc">Detailed Specifications &amp; Features</label>
                    <textarea id="p_desc" name="description" class="form-control" rows="5" placeholder="Include dimensions (e.g. 4ft x 6ft x 9mm), material specifications, fire ratings, water resistance, and application instructions..."></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="products.php" class="btn btn-outline-gold" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-gold" style="border-radius:6px; padding:12px 28px;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Save &amp; Publish Product
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

