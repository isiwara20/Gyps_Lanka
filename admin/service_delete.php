<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $db = get_db();
    if ($db) {
        try {
            // Count existing services to prevent deleting all
            $count = (int)$db->query("SELECT COUNT(*) FROM services")->fetchColumn();
            if ($count <= 1) {
                set_flash('error', 'Cannot delete: At least one service must remain in the system.');
                header("Location: services.php");
                exit;
            }

            $stmt = $db->prepare("SELECT title, image FROM services WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $service = $stmt->fetch();

            if ($service) {
                // Delete uploaded file if it was uploaded to uploads folder
                if (!empty($service['image']) && strpos($service['image'], 'assets/images/uploads/services/') !== false) {
                    $filePath = __DIR__ . '/../' . $service['image'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }

                $delStmt = $db->prepare("DELETE FROM services WHERE id = :id");
                $delStmt->execute([':id' => $id]);

                set_flash('success', 'Service "' . htmlspecialchars($service['title']) . '" has been deleted.');
            } else {
                set_flash('error', 'Service not found.');
            }
        } catch (PDOException $e) {
            set_flash('error', 'Database deletion error: ' . $e->getMessage());
        }
    }
}

header("Location: services.php");
exit;

