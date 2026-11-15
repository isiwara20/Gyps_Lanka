<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slide = null;

if ($db && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM hero_slides WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $slide = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching slide: " . $e->getMessage());
    }
}

if (!$slide) {
    set_flash('error', 'Banner slide not found.');
    header("Location: hero_slides.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title'] ?? '');
    $badge        = sanitize($_POST['badge'] ?? 'CEILING & INTERIOR SOLUTIONS');
    $description  = trim($_POST['description'] ?? '');
    $btnText      = sanitize($_POST['btn_text'] ?? 'Get a Free Quote');
    $btnLink      = sanitize($_POST['btn_link'] ?? '#quoteModal');
    $btn2Text     = sanitize($_POST['btn2_text'] ?? 'View Our Projects');
    $btn2Link     = sanitize($_POST['btn2_link'] ?? '#projects');
    $highlightTag = sanitize($_POST['highlight_tag'] ?? '');
    $displayOrder = (int)($_POST['display_order'] ?? 1);
    $isActive     = isset($_POST['is_active']) ? 1 : 0;

    if (empty($title)) {
        set_flash('error', 'Slide title/headline cannot be empty.');
    } else {
        $imagePath = $slide['image'];

        // Check if user uploaded a replacement banner photo
        if (isset($_FILES['slide_image']) && $_FILES['slide_image']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = upload_image_file($_FILES['slide_image'], 'banners');
            if ($uploadRes['success']) {
                $imagePath = $uploadRes['path'];

                // Optional: remove old uploaded image if it was in uploads/banners
                if (!empty($slide['image']) && strpos($slide['image'], 'assets/images/uploads/banners/') !== false) {
                    $oldFilePath = __DIR__ . '/../' . $slide['image'];
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
            } else {
                set_flash('error', 'Image upload failed: ' . $uploadRes['error']);
                header("Location: hero_slide_edit.php?id=" . $id);
                exit;
            }
        }

        if ($db) {
            try {
                $stmt = $db->prepare("
                    UPDATE hero_slides 
                    SET badge = :badge, title = :title, description = :description, image = :image,
                        btn_text = :btn_text, btn_link = :btn_link, btn2_text = :btn2_text, btn2_link = :btn2_link,
                        highlight_tag = :highlight_tag, display_order = :display_order, is_active = :is_active
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':badge'         => $badge,
                    ':title'         => $title,
                    ':description'   => $description,
                    ':image'         => $imagePath,
                    ':btn_text'      => $btnText,
                    ':btn_link'      => $btnLink,
                    ':btn2_text'     => $btn2Text,
                    ':btn2_link'     => $btn2Link,
                    ':highlight_tag' => $highlightTag,
                    ':display_order' => $displayOrder,
                    ':is_active'     => $isActive,
                    ':id'            => $id,
                ]);

                set_flash('success', 'Banner slide updated successfully!');
                header("Location: hero_slides.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Edit Banner Slide';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Edit Banner Slide #<?= (int)$slide['id'] ?></h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Update the background ceiling photo, headlines, and links for this homepage slide.</p>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <a href="hero_slide_delete.php?id=<?= $slide['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this slide permanently?');" style="border-radius:6px;">
                <i class="fa-solid fa-trash"></i> Delete
            </a>
            <a href="hero_slides.php" class="btn btn-outline-gold btn-sm" style="border-radius:6px; background:#fff; color:var(--adm-navy); border-color:#cbd5e1;">
                <i class="fa-solid fa-arrow-left"></i> Back to Slides
            </a>
        </div>
    </div>

    <div class="card-body">
        <form action="hero_slide_edit.php?id=<?= $slide['id'] ?>" method="POST" enctype="multipart/form-data">
            <div style="display:grid; grid-template-columns: 1.4fr 1fr; gap:30px;">
                <!-- Left: Content Fields -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Upper Badge / Category <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="badge" class="form-control" value="<?= htmlspecialchars($slide['badge'] ?? '') ?>" required>
                        <small style="color:#64748b; font-size:0.75rem;">Small uppercase gold line above the main title.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Slide Main Headline <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($slide['title']) ?>" required>
                        <small style="color:#64748b; font-size:0.75rem;">Tip: Wrap words in <code>&lt;span class="text-gold-art"&gt;word&lt;/span&gt;</code> to render them in gold cursive script.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description Paragraph</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($slide['description'] ?? '') ?></textarea>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Primary Button Text</label>
                            <input type="text" name="btn_text" class="form-control" value="<?= htmlspecialchars($slide['btn_text'] ?? 'Get a Free Quote') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Primary Button Link</label>
                            <input type="text" name="btn_link" class="form-control" value="<?= htmlspecialchars($slide['btn_link'] ?? '#quoteModal') ?>">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Secondary Button Text</label>
                            <input type="text" name="btn2_text" class="form-control" value="<?= htmlspecialchars($slide['btn2_text'] ?? 'View Our Projects') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Secondary Button Link</label>
                            <input type="text" name="btn2_link" class="form-control" value="<?= htmlspecialchars($slide['btn2_link'] ?? '#projects') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Featured Badge Tag (Bottom Micro Tag)</label>
                        <input type="text" name="highlight_tag" class="form-control" value="<?= htmlspecialchars($slide['highlight_tag'] ?? '') ?>">
                    </div>
                </div>

                <!-- Right: Image Upload & Order -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Banner Background Photo</label>
                        <div style="border:2px dashed #cbd5e1; border-radius:10px; padding:18px; text-align:center; background:#f8fafc; cursor:pointer;" onclick="document.getElementById('slideImageInput').click();">
                            <div id="imagePreviewBox" style="width:100%; height:190px; border-radius:8px; overflow:hidden; background:#0b1728; display:flex; align-items:center; justify-content:center; margin-bottom:12px; border:1px solid #e2e8f0; position:relative;">
                                <img id="previewImg" src="../<?= htmlspecialchars($slide['image']) ?>" alt="Current Banner" style="width:100%; height:100%; object-fit:cover;">
                                <div style="position:absolute; bottom:6px; right:6px; background:rgba(0,0,0,0.7); color:#fff; font-size:0.7rem; padding:3px 8px; border-radius:4px;">
                                    Click to change photo
                                </div>
                            </div>
                            <input type="file" id="slideImageInput" name="slide_image" accept="image/*" style="display:none;" onchange="previewBanner(this)">
                            <button type="button" class="btn btn-outline-gold btn-sm" style="font-size:0.8rem; border-radius:5px;">
                                <i class="fa-solid fa-camera"></i> Replace Image File
                            </button>
                            <div style="font-size:0.72rem; color:#94a3b8; margin-top:6px;">Leave empty to keep existing image.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="<?= (int)$slide['display_order'] ?>" min="1" max="99">
                        <small style="color:#64748b; font-size:0.75rem;">Rotation order (1, 2, 3...).</small>
                    </div>

                    <div class="form-group" style="margin-top:20px;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:0.9rem; font-weight:600; color:#0f172a;">
                            <input type="checkbox" name="is_active" value="1" <?= ((int)$slide['is_active'] === 1) ? 'checked' : '' ?> style="width:18px; height:18px; accent-color:var(--adm-navy);">
                            Active (Show immediately on homepage slider)
                        </label>
                    </div>

                    <div style="margin-top:30px;">
                        <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:12px; font-size:0.95rem; border-radius:6px;">
                            <i class="fa-solid fa-check"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewBanner(input) {
    const previewImg = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

