<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$db = get_db();

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $catName = sanitize($_POST['name'] ?? '');
    $catDesc = sanitize($_POST['description'] ?? '');
    $catOrder = (int)($_POST['display_order'] ?? 0);

    if (empty($catName)) {
        set_flash('error', 'Category name is required.');
    } else {
        $slug = url_slug($catName);
        if ($db) {
            try {
                $check = $db->prepare("SELECT id FROM categories WHERE slug = :s");
                $check->execute([':s' => $slug]);
                if ($check->fetch()) {
                    $slug .= '-' . time();
                }

                $stmt = $db->prepare("INSERT INTO categories (name, slug, description, display_order) VALUES (:n, :s, :d, :o)");
                $stmt->execute([':n' => $catName, ':s' => $slug, ':d' => $catDesc, ':o' => $catOrder]);
                set_flash('success', 'Category "' . htmlspecialchars($catName) . '" created successfully.');
                header("Location: categories.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($db && $delId > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->execute([':id' => $delId]);
            set_flash('success', 'Category deleted successfully.');
            header("Location: categories.php");
            exit;
        } catch (PDOException $e) {
            set_flash('error', 'Cannot delete category that still has products assigned.');
        }
    }
}

$categories = [];
if ($db) {
    try {
        $stmt = $db->query("
            SELECT c.*, COUNT(p.id) AS product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            GROUP BY c.id 
            ORDER BY c.display_order ASC, c.id ASC
        ");
        $categories = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Categories error: " . $e->getMessage());
    }
}

$pageTitle = 'Product Categories';
require_once __DIR__ . '/includes/admin_header.php';
?>
?>

<div style="display:grid; grid-template-columns: 1fr 1.6fr; gap:25px;">
    <!-- Add Category Form -->
    <div class="admin-card">
        <div class="card-header">
            <h3>Add New Category</h3>
        </div>
        <div class="card-body">
            <form action="categories.php" method="POST">
                <input type="hidden" name="add_category" value="1">
                <div class="form-group">
                    <label class="form-label" for="cat_name">Category Name *</label>
                    <input type="text" id="cat_name" name="name" class="form-control" placeholder="e.g. Acoustic Ceiling Panels" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="cat_desc">Description</label>
                    <textarea id="cat_desc" name="description" class="form-control" rows="3" placeholder="Brief summary of category..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="cat_order">Display Order</label>
                    <input type="number" id="cat_order" name="display_order" class="form-control" value="0">
                </div>
                <button type="submit" class="btn btn-gold" style="width:100%; border-radius:6px; margin-top:10px;">
                    <i class="fa-solid fa-plus"></i> Create Category
                </button>
            </form>
        </div>
    </div>

    <!-- Category List -->
    <div class="admin-card">
        <div class="card-header">
            <h3>Existing Categories</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="data-table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Order</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($cat['name']) ?></strong>
                                    <?php if (!empty($cat['description'])): ?>
                                        <div style="font-size:0.78rem; color:#64748b;"><?= htmlspecialchars(truncate_text($cat['description'], 60)) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                                <td>
                                    <span class="badge badge-info"><?= $cat['product_count'] ?> Products</span>
                                </td>
                                <td><?= $cat['display_order'] ?></td>
                                <td style="text-align:right;">
                                    <a href="categories.php?delete=<?= $cat['id'] ?>" onclick="return confirmDelete('Delete category \'<?= addslashes(htmlspecialchars($cat['name'])) ?>\'? Note: All linked products must be deleted or moved first.');" class="btn-icon delete" style="display:inline-flex;" title="Delete Category">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center; padding:25px; color:#64748b;">No categories created.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>

