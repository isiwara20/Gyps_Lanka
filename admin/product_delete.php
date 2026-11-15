<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $db = get_db();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT name, image FROM products WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $prod = $stmt->fetch();

            if ($prod) {
                // Delete image if not sample placeholder
                if (!empty($prod['image']) && strpos($prod['image'], 'assets/images/uploads/products/gyps_') !== false) {
                    $filePath = __DIR__ . '/../' . $prod['image'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }

                $delStmt = $db->prepare("DELETE FROM products WHERE id = :id");
                $delStmt->execute([':id' => $id]);

                set_flash('success', 'Product "' . htmlspecialchars($prod['name']) . '" was successfully deleted.');
            } else {
                set_flash('error', 'Product not found.');
            }
        } catch (PDOException $e) {
            set_flash('error', 'Database error: ' . $e->getMessage());
        }
    }
}

header("Location: products.php");
exit;

