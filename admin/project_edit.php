<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project = null;

if ($db && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $project = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching project: " . $e->getMessage());
    }
}

if (!$project) {
    set_flash('error', 'Project not found.');
    header("Location: projects.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = sanitize($_POST['title'] ?? '');
    $category    = sanitize($_POST['category'] ?? 'Residential');
    $location    = sanitize($_POST['location'] ?? 'Colombo');
    $description = sanitize($_POST['description'] ?? '');
    $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;
    $order       = (int)($_POST['display_order'] ?? 0);

    if (empty($title)) {
        set_flash('error', 'Project title cannot be empty.');
    } else {
        $imagePath = $project['image'];
        if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = upload_image_file($_FILES['project_image'], 'projects');
            if ($uploadRes['success']) {
                $imagePath = $uploadRes['path'];
            }
        }

        if ($db) {
            try {
                $stmt = $db->prepare("
                    UPDATE projects 
                    SET title = :title, category = :category, location = :location, 
                        image = :image, description = :description, is_featured = :is_featured, display_order = :display_order
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':title'         => $title,
                    ':category'      => $category,
                    ':location'      => $location,
                    ':image'         => $imagePath,
                    ':description'   => $description,
                    ':is_featured'   => $isFeatured,
                    ':display_order' => $order,
                    ':id'            => $id
                ]);

                set_flash('success', 'Project updated successfully!');
                header("Location: projects.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Edit Project';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Edit Project: <?= htmlspecialchars($project['title']) ?></h3>
        </div>
        <a href="projects.php" class="btn btn-outline-gold btn-sm" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">
            <i class="fa-solid fa-arrow-left"></i> Back to Projects
        </a>
    </div>

    <div class="card-body">
        <form action="project_edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="proj_title">Project Title *</label>
                    <input type="text" id="proj_title" name="title" class="form-control" value="<?= htmlspecialchars($project['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="proj_cat">Category *</label>
                    <select id="proj_cat" name="category" class="form-control">
                        <option value="Residential" <?= ($project['category'] === 'Residential') ? 'selected' : '' ?>>Residential</option>
                        <option value="Commercial" <?= ($project['category'] === 'Commercial') ? 'selected' : '' ?>>Commercial</option>
                        <option value="Office Fitout" <?= ($project['category'] === 'Office Fitout') ? 'selected' : '' ?>>Office Fitout</option>
                        <option value="Hotel &amp; Hospitality" <?= ($project['category'] === 'Hotel & Hospitality') ? 'selected' : '' ?>>Hotel &amp; Hospitality</option>
                        <option value="Custom Lighting" <?= ($project['category'] === 'Custom Lighting') ? 'selected' : '' ?>>Custom Lighting</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="proj_loc">Location</label>
                    <input type="text" id="proj_loc" name="location" class="form-control" value="<?= htmlspecialchars($project['location'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="proj_order">Display Order</label>
                    <input type="number" id="proj_order" name="display_order" class="form-control" value="<?= $project['display_order'] ?>">
                </div>

                <div class="form-group full">
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:0.92rem;">
                        <input type="checkbox" name="is_featured" value="1" <?= $project['is_featured'] ? 'checked' : '' ?> style="width:18px; height:18px; accent-color:var(--adm-gold);">
                        <span>Show on Homepage "Our Recent Projects" Section</span>
                    </label>
                </div>

                <div class="form-group full">
                    <label class="form-label" for="proj_img">Change Photo (Leave blank to keep existing)</label>
                    <input type="file" id="proj_img" name="project_image" class="form-control preview-input" data-preview="projPreview" accept="image/*">
                    <div class="image-preview-box" id="projPreview">
                        <img src="../<?= htmlspecialchars($project['image']) ?>" alt="">
                    </div>
                </div>

                <div class="form-group full">
                    <label class="form-label" for="proj_desc">Project Description</label>
                    <textarea id="proj_desc" name="description" class="form-control" rows="3"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="projects.php" class="btn btn-outline-gold" style="color:var(--adm-navy); border-color:#cbd5e1; border-radius:6px; background:#fff;">Cancel</a>
                <button type="submit" class="btn btn-gold" style="border-radius:6px; padding:12px 28px;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

