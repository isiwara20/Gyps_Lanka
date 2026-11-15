<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = sanitize($_POST['title'] ?? '');
    $slug         = sanitize($_POST['slug'] ?? '');
    $iconSvg      = sanitize($_POST['icon_svg'] ?? 'layers');
    $displayOrder = (int)($_POST['display_order'] ?? 10);
    $shortDesc    = sanitize($_POST['short_desc'] ?? '');
    $fullDesc     = sanitize($_POST['full_desc'] ?? '');
    $features     = sanitize($_POST['features'] ?? '');

    if (empty($title)) {
        set_flash('error', 'Service title cannot be empty.');
    } else {
        if (empty($slug)) {
            $slug = url_slug($title);
        }

        $imagePath = 'assets/images/service-1.jpg';
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = upload_image_file($_FILES['service_image'], 'services');
            if ($uploadRes['success']) {
                $imagePath = $uploadRes['path'];
            } else {
                set_flash('error', 'Image upload failed: ' . $uploadRes['error']);
            }
        } elseif (!empty($_POST['custom_image_path'])) {
            $imagePath = sanitize($_POST['custom_image_path']);
        }

        if ($db) {
            try {
                // Ensure unique slug
                $chk = $db->prepare("SELECT id FROM services WHERE slug = :slug");
                $chk->execute([':slug' => $slug]);
                if ($chk->fetch()) {
                    $slug .= '-' . time();
                }

                $stmt = $db->prepare("
                    INSERT INTO services (title, slug, icon_svg, short_desc, full_desc, features, image, display_order)
                    VALUES (:title, :slug, :icon_svg, :short_desc, :full_desc, :features, :image, :display_order)
                ");
                $stmt->execute([
                    ':title'         => $title,
                    ':slug'          => $slug,
                    ':icon_svg'      => $iconSvg,
                    ':short_desc'    => $shortDesc,
                    ':full_desc'     => $fullDesc,
                    ':features'      => $features,
                    ':image'         => $imagePath,
                    ':display_order' => $displayOrder
                ]);

                set_flash('success', 'New service "' . $title . '" added successfully!');
                header("Location: services.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database insert error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Add New Service';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3><i class="fa-solid fa-plus-circle" style="color:var(--adm-gold); margin-right:8px;"></i> Add New Service</h3>
            <p style="font-size:0.82rem; color:var(--adm-muted); margin:0;">Create a new service to present across your website and services page.</p>
        </div>
        <a href="services.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; background:#fff; border-radius:6px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Services
        </a>
    </div>

    <div class="card-body">
        <form action="service_add.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <!-- Service Title -->
                <div class="form-group">
                    <label class="form-label" for="srv_title">Service Title *</label>
                    <input type="text" id="srv_title" name="title" class="form-control" placeholder="e.g. Acoustic Wood Slats &amp; Ceilings" required>
                    <small style="color:#64748b; font-size:0.75rem;">Main heading shown across all website sections.</small>
                </div>

                <!-- URL Slug -->
                <div class="form-group">
                    <label class="form-label" for="srv_slug">URL Slug / Section Anchor (Optional)</label>
                    <input type="text" id="srv_slug" name="slug" class="form-control" placeholder="Leave blank to auto-generate">
                    <small style="color:#64748b; font-size:0.75rem;">Used for direct anchor links (e.g. <code>services.php#acoustic-wood-slats</code>).</small>
                </div>
            </div>

            <div class="form-grid">
                <!-- Icon Selector -->
                <div class="form-group">
                    <label class="form-label" for="srv_icon">Icon Identifier</label>
                    <select id="srv_icon" name="icon_svg" class="form-control">
                        <option value="layers" selected>layers (Layered Ceiling / Levels)</option>
                        <option value="lightbulb">lightbulb (LED &amp; Cove Lighting)</option>
                        <option value="grid">grid (Drywall Partitions &amp; Boards)</option>
                        <option value="sofa">sofa (Interior Finishing &amp; Salons)</option>
                        <option value="star">star (Custom 3D Designs &amp; Luxury)</option>
                        <option value="wand-magic-sparkles">wand-magic-sparkles (Bespoke Craftsmanship)</option>
                        <option value="paint-roller">paint-roller (Skim Coating &amp; Painting)</option>
                        <option value="cubes">cubes (Materials &amp; Framework)</option>
                    </select>
                    <small style="color:#64748b; font-size:0.75rem;">Visual icon displayed on the homepage cards.</small>
                </div>

                <!-- Display Order -->
                <div class="form-group">
                    <label class="form-label" for="srv_order">Display Order</label>
                    <input type="number" id="srv_order" name="display_order" class="form-control" value="6" min="0" max="999">
                    <small style="color:#64748b; font-size:0.75rem;">Controls the order (1 comes first, then 2, 3, etc.).</small>
                </div>
            </div>

            <!-- Short Description -->
            <div class="form-group">
                <label class="form-label" for="srv_short_desc">Short Description (Homepage Card Summary)</label>
                <textarea id="srv_short_desc" name="short_desc" class="form-control" rows="2" placeholder="Brief 1-2 sentence summary for homepage cards..."></textarea>
                <small style="color:#64748b; font-size:0.75rem;">Shown inside the service card on the Home Page.</small>
            </div>

            <!-- Detailed Full Description -->
            <div class="form-group">
                <label class="form-label" for="srv_full_desc">Detailed Full Description (Services Page)</label>
                <textarea id="srv_full_desc" name="full_desc" class="form-control" rows="5" placeholder="Comprehensive service overview, techniques, materials used, benefits..."></textarea>
                <small style="color:#64748b; font-size:0.75rem;">Displayed as the main paragraph on <code>services.php</code>.</small>
            </div>

            <!-- Features / Bullet Points -->
            <div class="form-group">
                <label class="form-label" for="srv_features">
                    Key Features / Bullet Points
                    <span style="font-weight:normal; color:#64748b; font-size:0.8rem;">(One item per line)</span>
                </label>
                <textarea id="srv_features" name="features" class="form-control" rows="4" placeholder="Multi-Tiered Cove Ceilings&#10;Floating Island Clouds&#10;Dropped Perimeter Trays&#10;Chandelier Centerpieces"></textarea>
                <small style="color:#64748b; font-size:0.75rem;">Each line becomes a bullet point with a gold checkmark on <code>services.php</code>.</small>
            </div>

            <!-- Service Image -->
            <div class="form-group">
                <label class="form-label">Service Feature Image</label>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px;">
                    <label class="form-label" style="font-size:0.85rem;" for="service_img_upload">Upload Image File (JPG, PNG, WEBP)</label>
                    <input type="file" id="service_img_upload" name="service_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    
                    <div style="margin-top:10px;">
                        <label class="form-label" style="font-size:0.8rem;" for="custom_path">Or Default Image Path</label>
                        <input type="text" id="custom_path" name="custom_image_path" class="form-control" value="assets/images/service-1.jpg" placeholder="assets/images/service-1.jpg">
                    </div>
                    <small style="color:#64748b; font-size:0.75rem; display:block; margin-top:6px;">
                        Recommended image size: <strong>800 &times; 600px</strong> or <strong>1200 &times; 800px</strong> for sharp, crisp visuals.
                    </small>
                </div>
            </div>

            <div class="form-actions" style="margin-top:25px; display:flex; gap:12px;">
                <button type="submit" class="btn btn-gold" style="border-radius:6px; padding:12px 30px; font-weight:700;">
                    <i class="fa-solid fa-plus-circle"></i> Create Service
                </button>
                <a href="services.php" class="btn btn-outline-gold" style="border-radius:6px; padding:12px 22px; color:var(--adm-navy); border-color:#cbd5e1; background:#fff;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

