<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = sanitize($_POST['title'] ?? '');
    $category    = sanitize($_POST['category'] ?? 'Residential');
    $location    = sanitize($_POST['location'] ?? 'Colombo');
    $description = sanitize($_POST['description'] ?? '');
    $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;
    $order       = (int)($_POST['display_order'] ?? 0);

    if (empty($title)) {
        set_flash('error', 'Project title is required.');
    } else {
        $imagePath = 'assets/images/project-1.jpg';
        if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = upload_image_file($_FILES['project_image'], 'projects');
            if ($uploadRes['success']) {
                $imagePath = $uploadRes['path'];
            } else {
                set_flash('error', 'Upload error: ' . $uploadRes['error']);
                header("Location: project_add.php");
                exit;
            }
        }

        if ($db) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO projects (title, category, location, image, description, is_featured, display_order)
                    VALUES (:title, :category, :location, :image, :description, :is_featured, :display_order)
                ");
                $stmt->execute([
                    ':title'         => $title,
                    ':category'      => $category,
                    ':location'      => $location,
                    ':image'         => $imagePath,
                    ':description'   => $description,
                    ':is_featured'   => $isFeatured,
                    ':display_order' => $order
                ]);

                set_flash('success', 'Project "' . htmlspecialchars($title) . '" added successfully!');
                header("Location: projects.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Add Project Showcase';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Add Project Showcase</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Upload completed ceiling project photos.</p>
        </div>
        <a href="projects.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
            <i class="fa-solid fa-arrow-left"></i> Back to Projects
        </a>
    </div>

    <div class="card-body">
        <form action="project_add.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="proj_title">Project Title *</label>
                    <input type="text" id="proj_title" name="title" class="form-control" placeholder="e.g. Luxury Living Room Multi-Tiered Ceiling" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="proj_cat">Category *</label>
                    <select id="proj_cat" name="category" class="form-control">
                        <option value="Residential">Residential</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Office Fitout">Office Fitout</option>
                        <option value="Hotel &amp; Hospitality">Hotel &amp; Hospitality</option>
                        <option value="Custom Lighting">Custom Lighting</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="proj_loc">Location</label>
                    <input type="text" id="proj_loc" name="location" class="form-control" placeholder="e.g. Colombo 07, Nawala, Kandy">
                </div>

                <div class="form-group">
                    <label class="form-label" for="proj_order">Display Order</label>
                    <input type="number" id="proj_order" name="display_order" class="form-control" value="0">
                </div>

                <div class="form-group full">
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:0.92rem;">
                        <input type="checkbox" name="is_featured" value="1" checked style="width:18px; height:18px; accent-color:var(--adm-gold);">
                        <span>Show on Homepage "Our Recent Projects" Section</span>
                    </label>
                </div>

                <div class="form-group full">
                    <label class="form-label" for="proj_img">Project Photo *</label>
                    <input type="file" id="proj_img" name="project_image" class="form-control preview-input" data-preview="projPreview" accept="image/*" required>
                    <div class="image-preview-box" id="projPreview">
                        <span style="font-size:0.8rem; color:#94a3b8; text-align:center; padding:10px;">Photo Preview</span>
                    </div>
                </div>

                <div class="form-group full">
                    <label class="form-label" for="proj_desc">Project Description</label>
                    <textarea id="proj_desc" name="description" class="form-control" rows="3" placeholder="Brief details about ceiling design, lighting used, etc..."></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="projects.php" class="btn btn-outline-gold" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">Cancel</a>
                <button type="submit" class="btn btn-gold" style="border-radius:6px; padding:12px 28px;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Save Project
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

