<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $db = get_db();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT title, image FROM projects WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $proj = $stmt->fetch();

            if ($proj) {
                if (!empty($proj['image']) && strpos($proj['image'], 'assets/images/uploads/projects/gyps_') !== false) {
                    $filePath = __DIR__ . '/../' . $proj['image'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }

                $delStmt = $db->prepare("DELETE FROM projects WHERE id = :id");
                $delStmt->execute([':id' => $id]);

                set_flash('success', 'Project "' . htmlspecialchars($proj['title']) . '" deleted.');
            }
        } catch (PDOException $e) {
            set_flash('error', 'Database error: ' . $e->getMessage());
        }
    }
}

header("Location: projects.php");
exit;

