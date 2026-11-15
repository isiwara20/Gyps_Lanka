<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$service = null;

if ($db && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM services WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $service = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching service: " . $e->getMessage());
    }
}

if (!$service) {
    set_flash('error', 'Service not found.');
    header("Location: services.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = sanitize($_POST['title'] ?? '');
    $slug         = sanitize($_POST['slug'] ?? '');
    $iconSvg      = sanitize($_POST['icon_svg'] ?? 'layers');
    $displayOrder = (int)($_POST['display_order'] ?? 0);
    $shortDesc    = sanitize($_POST['short_desc'] ?? '');
    $fullDesc     = sanitize($_POST['full_desc'] ?? '');
    $features     = sanitize($_POST['features'] ?? '');

    if (empty($title)) {
        set_flash('error', 'Service title cannot be empty.');
    } else {
        if (empty($slug)) {
            $slug = url_slug($title);
        }

        $imagePath = $service['image'];
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
                // Verify slug uniqueness (excluding current service)
                $chk = $db->prepare("SELECT id FROM services WHERE slug = :slug AND id != :id");
                $chk->execute([':slug' => $slug, ':id' => $id]);
                if ($chk->fetch()) {
                    $slug .= '-' . $id;
                }

                $stmt = $db->prepare("
                    UPDATE services 
                    SET title = :title, 
                        slug = :slug, 
                        icon_svg = :icon_svg, 
                        short_desc = :short_desc, 
                        full_desc = :full_desc, 
                        features = :features, 
                        image = :image, 
                        display_order = :display_order
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':title'         => $title,
                    ':slug'          => $slug,
                    ':icon_svg'      => $iconSvg,
                    ':short_desc'    => $shortDesc,
                    ':full_desc'     => $fullDesc,
                    ':features'      => $features,
                    ':image'         => $imagePath,
                    ':display_order' => $displayOrder,
                    ':id'            => $id
                ]);

                set_flash('success', 'Service "' . $title . '" has been updated successfully!');
                header("Location: services.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database update error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Edit Service - ' . $service['title'];
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3><i class="fa-solid fa-pen-to-square" style="color:var(--adm-gold); margin-right:8px;"></i> Edit Service: <?= htmlspecialchars($service['title']) ?></h3>
            <p style="font-size:0.82rem; color:var(--adm-muted); margin:0;">Changes made here immediately update the Home Page and the Services Page.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="../services.php#<?= htmlspecialchars($service['slug']) ?>" target="_blank" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; background:#fff; border-radius:6px;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Preview on Site
            </a>
            <a href="services.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; background:#fff; border-radius:6px;">
                <i class="fa-solid fa-arrow-left"></i> Back to Services
            </a>
        </div>
    </div>

    <div class="card-body">
        <form action="service_edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <!-- Service Title -->
                <div class="form-group">
                    <label class="form-label" for="srv_title">Service Title *</label>
                    <input type="text" id="srv_title" name="title" class="form-control" value="<?= htmlspecialchars($service['title']) ?>" placeholder="e.g. Gypsum False Ceilings" required>
                    <small style="color:#64748b; font-size:0.75rem;">Main heading shown across all website sections.</small>
                </div>

                <!-- URL Slug -->
                <div class="form-group">
                    <label class="form-label" for="srv_slug">URL Slug / Section Anchor *</label>
                    <input type="text" id="srv_slug" name="slug" class="form-control" value="<?= htmlspecialchars($service['slug']) ?>" placeholder="e.g. gypsum-false-ceilings" required>
                    <small style="color:#64748b; font-size:0.75rem;">Used for direct links (e.g. <code>services.php#<?= htmlspecialchars($service['slug']) ?></code>).</small>
                </div>
            </div>

            <div class="form-grid">
                <!-- Icon Selector -->
                <div class="form-group">
                    <label class="form-label" for="srv_icon">Icon Identifier</label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <select id="srv_icon" name="icon_svg" class="form-control" style="flex:1;">
                            <option value="layers" <?= ($service['icon_svg'] === 'layers') ? 'selected' : '' ?>>layers (Layered Ceiling / Levels)</option>
                            <option value="lightbulb" <?= ($service['icon_svg'] === 'lightbulb') ? 'selected' : '' ?>>lightbulb (LED &amp; Cove Lighting)</option>
                            <option value="grid" <?= ($service['icon_svg'] === 'grid') ? 'selected' : '' ?>>grid (Drywall Partitions &amp; Boards)</option>
                            <option value="sofa" <?= ($service['icon_svg'] === 'sofa') ? 'selected' : '' ?>>sofa (Interior Finishing &amp; Salons)</option>
                            <option value="star" <?= ($service['icon_svg'] === 'star') ? 'selected' : '' ?>>star (Custom 3D Designs &amp; Luxury)</option>
                            <option value="wand-magic-sparkles" <?= ($service['icon_svg'] === 'wand-magic-sparkles') ? 'selected' : '' ?>>wand-magic-sparkles (Bespoke Craftsmanship)</option>
                            <option value="paint-roller" <?= ($service['icon_svg'] === 'paint-roller') ? 'selected' : '' ?>>paint-roller (Skim Coating &amp; Painting)</option>
                            <option value="cubes" <?= ($service['icon_svg'] === 'cubes') ? 'selected' : '' ?>>cubes (Materials &amp; Framework)</option>
                        </select>
                    </div>
                    <small style="color:#64748b; font-size:0.75rem;">Visual icon displayed on the homepage cards.</small>
                </div>

                <!-- Display Order -->
                <div class="form-group">
                    <label class="form-label" for="srv_order">Display Order</label>
                    <input type="number" id="srv_order" name="display_order" class="form-control" value="<?= (int)$service['display_order'] ?>" min="0" max="999">
                    <small style="color:#64748b; font-size:0.75rem;">Controls the order (1 comes first, then 2, 3, etc.).</small>
                </div>
            </div>

            <!-- Short Description -->
            <div class="form-group">
                <label class="form-label" for="srv_short_desc">Short Description (Homepage Card Summary)</label>
                <textarea id="srv_short_desc" name="short_desc" class="form-control" rows="2" placeholder="Brief summary for homepage cards..."><?= htmlspecialchars($service['short_desc'] ?? '') ?></textarea>
                <small style="color:#64748b; font-size:0.75rem;">Shown inside the service card on the Home Page.</small>
            </div>

            <!-- Detailed Full Description -->
            <div class="form-group">
                <label class="form-label" for="srv_full_desc">Detailed Full Description (Services Page)</label>
                <textarea id="srv_full_desc" name="full_desc" class="form-control" rows="5" placeholder="Comprehensive service overview, techniques, materials used, benefits..."><?= htmlspecialchars($service['full_desc'] ?? '') ?></textarea>
                <small style="color:#64748b; font-size:0.75rem;">Displayed as the main paragraph on <code>services.php</code>.</small>
            </div>

            <!-- Features / Bullet Points -->
            <div class="form-group">
                <label class="form-label" for="srv_features">
                    Key Features / Bullet Points
                    <span style="font-weight:normal; color:#64748b; font-size:0.8rem;">(One item per line)</span>
                </label>
                <textarea id="srv_features" name="features" class="form-control" rows="4" placeholder="Multi-Tiered Cove Ceilings&#10;Floating Island Clouds&#10;Dropped Perimeter Trays&#10;Chandelier Centerpieces"><?= htmlspecialchars($service['features'] ?? '') ?></textarea>
                <small style="color:#64748b; font-size:0.75rem;">Each line becomes a bullet point with a gold checkmark on <code>services.php</code>.</small>
            </div>

            <!-- Service Image -->
            <div class="form-group">
                <label class="form-label">Service Feature Image</label>
                <div style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px;">
                    <div style="flex-shrink:0; text-align:center;">
                        <img src="../<?= htmlspecialchars($service['image'] ?: 'assets/images/service-1.jpg') ?>" id="previewImg" alt="Service Preview" style="width:160px; height:110px; object-fit:cover; border-radius:8px; border:2px solid var(--adm-gold); box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                        <div style="font-size:0.72rem; color:#64748b; margin-top:4px;">Current Image</div>
                    </div>
                    <div style="flex:1; min-width:240px;">
                        <label class="form-label" style="font-size:0.85rem;" for="service_img_upload">Upload New Image File (JPG, PNG, WEBP)</label>
                        <input type="file" id="service_img_upload" name="service_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        
                        <div style="margin-top:10px;">
                            <label class="form-label" style="font-size:0.8rem;" for="custom_path">Or Image Path / URL</label>
                            <input type="text" id="custom_path" name="custom_image_path" class="form-control" value="<?= htmlspecialchars($service['image']) ?>" placeholder="assets/images/service-1.jpg">
                        </div>
                        <small style="color:#64748b; font-size:0.75rem; display:block; margin-top:6px;">
                            Recommended image size: <strong>800 &times; 600px</strong> or <strong>1200 &times; 800px</strong> for sharp, crisp visuals.
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top:25px; display:flex; gap:12px;">
                <button type="submit" class="btn btn-gold" style="border-radius:6px; padding:12px 30px; font-weight:700;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
                <a href="services.php" class="btn btn-outline-gold" style="border-radius:6px; padding:12px 22px; color:var(--adm-navy); border-color:#cbd5e1; background:#fff;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview update on file pick
document.getElementById('service_img_upload')?.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            document.getElementById('previewImg').src = evt.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

