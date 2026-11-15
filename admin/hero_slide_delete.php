<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $db = get_db();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT title, image FROM hero_slides WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $slide = $stmt->fetch();

            if ($slide) {
                // Delete physical image file if uploaded by user
                if (!empty($slide['image']) && strpos($slide['image'], 'assets/images/uploads/banners/') !== false) {
                    $filePath = __DIR__ . '/../' . $slide['image'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }

                $delStmt = $db->prepare("DELETE FROM hero_slides WHERE id = :id");
                $delStmt->execute([':id' => $id]);

                set_flash('success', 'Banner slide deleted successfully.');
            } else {
                set_flash('error', 'Banner slide not found.');
            }
        } catch (PDOException $e) {
            set_flash('error', 'Database error: ' . $e->getMessage());
        }
    }
}

header("Location: hero_slides.php");
exit;

