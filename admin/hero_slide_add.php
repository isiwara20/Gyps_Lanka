<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

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
        set_flash('error', 'Slide title/headline is required.');
    } elseif (!isset($_FILES['slide_image']) || $_FILES['slide_image']['error'] !== UPLOAD_ERR_OK) {
        set_flash('error', 'Please upload a background banner image (JPG, PNG, or WEBP).');
    } else {
        $uploadRes = upload_image_file($_FILES['slide_image'], 'banners');
        if (!$uploadRes['success']) {
            set_flash('error', 'Banner upload failed: ' . $uploadRes['error']);
            header("Location: hero_slide_add.php");
            exit;
        }

        $imagePath = $uploadRes['path'];

        if ($db) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO hero_slides (badge, title, description, image, btn_text, btn_link, btn2_text, btn2_link, highlight_tag, display_order, is_active)
                    VALUES (:badge, :title, :description, :image, :btn_text, :btn_link, :btn2_text, :btn2_link, :highlight_tag, :display_order, :is_active)
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
                ]);

                set_flash('success', 'New banner slide added successfully!');
                header("Location: hero_slides.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Add Hero Banner Slide';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Add New Banner Slide</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Upload a high-resolution ceiling photo and configure the headlines and buttons for the homepage slider.</p>
        </div>
        <a href="hero_slides.php" class="btn btn-outline-gold btn-sm" style="border-radius:6px; background:#fff; color:var(--adm-navy); border-color:#cbd5e1;">
            <i class="fa-solid fa-arrow-left"></i> Back to Slides
        </a>
    </div>

    <div class="card-body">
        <form action="hero_slide_add.php" method="POST" enctype="multipart/form-data">
            <div style="display:grid; grid-template-columns: 1.4fr 1fr; gap:30px;">
                <!-- Left: Content Fields -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Upper Badge / Category <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="badge" class="form-control" placeholder="e.g. CEILING & INTERIOR SOLUTIONS" value="CEILING &amp; INTERIOR SOLUTIONS" required>
                        <small style="color:#64748b; font-size:0.75rem;">Small uppercase gold line above the main title.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Slide Main Headline <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder='e.g. Turning Ceilings Into &lt;span class="text-gold-art"&gt;Art.&lt;/span&gt;' required>
                        <small style="color:#64748b; font-size:0.75rem;">Tip: Wrap words in <code>&lt;span class="text-gold-art"&gt;word&lt;/span&gt;</code> to render them in gold cursive script.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description Paragraph</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief 1-2 sentence subtitle describing this ceiling service or feature..."></textarea>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Primary Button Text</label>
                            <input type="text" name="btn_text" class="form-control" value="Get a Free Quote">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Primary Button Link</label>
                            <input type="text" name="btn_link" class="form-control" value="#quoteModal">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Secondary Button Text</label>
                            <input type="text" name="btn2_text" class="form-control" value="View Our Projects">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Secondary Button Link</label>
                            <input type="text" name="btn2_link" class="form-control" value="projects.php">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Featured Badge Tag (Bottom Micro Tag)</label>
                        <input type="text" name="highlight_tag" class="form-control" placeholder="e.g. Featured Project: Multi-Tiered Luxury Cove Ceiling">
                    </div>
                </div>

                <!-- Right: Image Upload & Order -->
                <div>
                    <div class="form-group">
                        <label class="form-label">Banner Background Photo <span style="color:#ef4444;">*</span></label>
                        <div style="border:2px dashed #cbd5e1; border-radius:10px; padding:20px; text-align:center; background:#f8fafc; cursor:pointer;" onclick="document.getElementById('slideImageInput').click();">
                            <div id="imagePreviewBox" style="width:100%; height:180px; border-radius:8px; overflow:hidden; background:#0b1728; display:flex; align-items:center; justify-content:center; margin-bottom:12px; border:1px solid #e2e8f0;">
                                <div id="previewPlaceholder" style="color:#94a3b8; text-align:center;">
                                    <i class="fa-solid fa-cloud-arrow-up" style="font-size:2rem; color:var(--adm-gold); margin-bottom:8px;"></i>
                                    <div style="font-size:0.85rem; font-weight:600;">Click to select ceiling photo</div>
                                    <div style="font-size:0.75rem; color:#94a3b8;">Recommended: 1920x1080 or 1600x900 (Landscape)</div>
                                </div>
                                <img id="previewImg" src="" alt="Preview" style="display:none; width:100%; height:100%; object-fit:cover;">
                            </div>
                            <input type="file" id="slideImageInput" name="slide_image" accept="image/*" style="display:none;" onchange="previewBanner(this)" required>
                            <button type="button" class="btn btn-outline-gold btn-sm" style="font-size:0.8rem; border-radius:5px;">Choose Image File</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="1" min="1" max="99">
                        <small style="color:#64748b; font-size:0.75rem;">Order in which this slide appears in the auto-rotation (1, 2, 3...).</small>
                    </div>

                    <div class="form-group" style="margin-top:20px;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:0.9rem; font-weight:600; color:#0f172a;">
                            <input type="checkbox" name="is_active" value="1" checked style="width:18px; height:18px; accent-color:var(--adm-navy);">
                            Active (Show immediately on homepage slider)
                        </label>
                    </div>

                    <div style="margin-top:30px;">
                        <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:12px; font-size:0.95rem; border-radius:6px;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Publish Banner Slide
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
    const placeholder = document.getElementById('previewPlaceholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

