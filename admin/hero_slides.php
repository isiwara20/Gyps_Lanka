<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();
$slides = get_hero_slides(true); // pass true to get all slides including inactive

$pageTitle = 'Hero Banner Slides';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Homepage Hero Banner Slides (<?= count($slides) ?>)</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Manage the full-width swapping background images, headlines, and call-to-actions on your homepage banner.</p>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <a href="../index.php" target="_blank" class="btn btn-outline-gold btn-sm" style="border-radius:6px; background:#fff; color:var(--adm-navy); border-color:#cbd5e1;">
                <i class="fa-solid fa-eye"></i> View Live Slider
            </a>
            <a href="hero_slide_add.php" class="btn btn-gold btn-sm" style="border-radius:6px;">
                <i class="fa-solid fa-plus"></i> Add New Slide
            </a>
        </div>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="data-table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:130px;">Banner Photo</th>
                        <th>Headline &amp; Content</th>
                        <th>Action Buttons</th>
                        <th style="width:70px; text-align:center;">Order</th>
                        <th style="width:90px; text-align:center;">Status</th>
                        <th style="width:140px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($slides)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">
                            No banner slides found. <a href="hero_slide_add.php" style="color:var(--adm-gold); font-weight:600;">Add your first slide &rarr;</a>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($slides as $idx => $slide): ?>
                        <tr>
                            <td>
                                <div style="position:relative; width:120px; height:68px; border-radius:6px; overflow:hidden; border:1px solid #e2e8f0; background:#0b1728;">
                                    <img src="../<?= htmlspecialchars($slide['image']) ?>" alt="" style="width:100%; height:100%; object-fit:cover;">
                                    <span style="position:absolute; bottom:3px; left:3px; background:rgba(0,0,0,0.65); color:#fff; font-size:0.65rem; padding:2px 5px; border-radius:3px; font-weight:700;">
                                        Slide #<?= $idx + 1 ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($slide['badge'])): ?>
                                    <span style="display:inline-block; font-size:0.7rem; font-weight:700; color:var(--adm-gold); letter-spacing:1px; text-transform:uppercase; margin-bottom:4px;">
                                        <?= htmlspecialchars($slide['badge']) ?>
                                    </span>
                                <?php endif; ?>
                                <div style="font-weight:700; color:var(--adm-navy); font-size:0.95rem; line-height:1.3; margin-bottom:4px;">
                                    <?= strip_tags($slide['title']) ?>
                                </div>
                                <div style="font-size:0.8rem; color:#64748b; max-width:380px; line-height:1.4;">
                                    <?= htmlspecialchars(truncate_text($slide['description'] ?? '', 110)) ?>
                                </div>
                                <?php if (!empty($slide['highlight_tag'])): ?>
                                    <div style="font-size:0.75rem; color:#0b1728; background:#f1f5f9; padding:2px 8px; border-radius:4px; display:inline-block; margin-top:5px; border-left:2px solid var(--adm-gold);">
                                        <i class="fa-solid fa-gem" style="color:var(--adm-gold); font-size:0.7rem;"></i> <?= htmlspecialchars($slide['highlight_tag']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size:0.8rem; margin-bottom:4px;">
                                    <strong style="color:#0f172a;">Btn 1:</strong> <?= htmlspecialchars($slide['btn_text'] ?? 'Get a Free Quote') ?>
                                    <div style="font-size:0.72rem; color:#94a3b8;"><code><?= htmlspecialchars($slide['btn_link'] ?? '#quoteModal') ?></code></div>
                                </div>
                                <?php if (!empty($slide['btn2_text'])): ?>
                                    <div style="font-size:0.8rem;">
                                        <strong style="color:#0f172a;">Btn 2:</strong> <?= htmlspecialchars($slide['btn2_text']) ?>
                                        <div style="font-size:0.72rem; color:#94a3b8;"><code><?= htmlspecialchars($slide['btn2_link'] ?? 'projects.php') ?></code></div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <span style="display:inline-block; font-weight:700; padding:4px 10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; font-size:0.85rem;">
                                    <?= (int)$slide['display_order'] ?>
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <?php if ((int)$slide['is_active'] === 1): ?>
                                    <span class="stock-badge in_stock" style="font-size:0.75rem;">Active</span>
                                <?php else: ?>
                                    <span class="stock-badge out_of_stock" style="font-size:0.75rem;">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; align-items:center; gap:6px;">
                                    <a href="hero_slide_edit.php?id=<?= $slide['id'] ?>" class="btn btn-outline-gold btn-sm" title="Edit Slide" style="border-radius:4px; padding:6px 10px; font-size:0.8rem;">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="hero_slide_delete.php?id=<?= $slide['id'] ?>" class="btn btn-danger btn-sm" title="Delete Slide" style="border-radius:4px; padding:6px 10px; font-size:0.8rem;" onclick="return confirm('Are you sure you want to delete this banner slide?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

