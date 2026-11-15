<?php
$pageTitle = 'Portfolio Projects';
require_once __DIR__ . '/includes/admin_header.php';

$projects = [];
if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM projects ORDER BY display_order ASC, id DESC");
        $projects = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Admin projects error: " . $e->getMessage());
    }
}
?>

<div class="admin-card">
    <div class="card-header">
        <div>
            <h3>Portfolio Projects (<?= count($projects) ?>)</h3>
            <p style="font-size:0.85rem; color:var(--adm-muted);">Manage ceiling project showcase shown on homepage and projects gallery.</p>
        </div>
        <a href="project_add.php" class="btn btn-gold btn-sm" style="border-radius:6px;">
            <i class="fa-solid fa-plus"></i> Add Project
        </a>
    </div>

    <div class="card-body" style="padding:0;">
        <div class="data-table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:70px;">Image</th>
                        <th>Project Title</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Featured</th>
                        <th style="width:110px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $proj): ?>
                        <tr>
                            <td>
                                <img src="../<?= htmlspecialchars($proj['image']) ?>" class="table-thumb" alt="">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($proj['title']) ?></strong>
                                <?php if (!empty($proj['description'])): ?>
                                    <div style="font-size:0.78rem; color:#64748b;"><?= htmlspecialchars(truncate_text($proj['description'], 70)) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($proj['category']) ?></span></td>
                            <td><?= htmlspecialchars($proj['location'] ?? 'Sri Lanka') ?></td>
                            <td>
                                <?php if ($proj['is_featured']): ?>
                                    <span style="color:#eab308; font-size:0.85rem;"><i class="fa-solid fa-star"></i> Featured</span>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size:0.85rem;">Standard</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <div class="action-btns" style="justify-content:flex-end;">
                                    <a href="project_edit.php?id=<?= $proj['id'] ?>" class="btn-icon" title="Edit Project">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="project_delete.php?id=<?= $proj['id'] ?>" onclick="return confirmDelete('Delete project \'<?= addslashes(htmlspecialchars($proj['title'])) ?>\'?');" class="btn-icon delete" title="Delete Project">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:#64748b;">No projects added yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

