<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/functions.php';

require_admin();

$adminPage = basename($_SERVER['PHP_SELF'], '.php');
$adminUser = $_SESSION['gyps_admin_name'] ?? 'Administrator';

// Get unread inquiries count
$db = get_db();
$newInquiriesCount = 0;
if ($db) {
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'");
        $newInquiriesCount = (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        $newInquiriesCount = 0;
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Admin - Gyps Lanka' : 'Admin Dashboard | Gyps Lanka' ?></title>
    <link rel="icon" type="image/jpeg" href="../assets/images/logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <img src="../assets/images/logo.jpg" alt="Gyps Lanka">
            <div>
                <div class="sidebar-brand-title">GYPS<span>LANKA</span></div>
                <div style="font-size:0.7rem; color:#64748b; text-transform:uppercase; letter-spacing:1px;">Admin Console</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Core Management</div>
            <a href="index.php" class="sidebar-link <?= ($adminPage === 'index') ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>

            <div class="nav-section-title">Products / Catalog</div>
            <a href="products.php" class="sidebar-link <?= ($adminPage === 'products' || $adminPage === 'product_edit') ? 'active' : '' ?>">
                <i class="fa-solid fa-boxes-stacked"></i> All Products
            </a>
            <a href="product_add.php" class="sidebar-link <?= ($adminPage === 'product_add') ? 'active' : '' ?>">
                <i class="fa-solid fa-square-plus"></i> Add New Product
            </a>
            <a href="categories.php" class="sidebar-link <?= ($adminPage === 'categories') ? 'active' : '' ?>">
                <i class="fa-solid fa-tags"></i> Categories
            </a>

            <div class="nav-section-title">Ceiling Showcase</div>
            <a href="hero_slides.php" class="sidebar-link <?= ($adminPage === 'hero_slides' || $adminPage === 'hero_slide_add' || $adminPage === 'hero_slide_edit') ? 'active' : '' ?>">
                <i class="fa-solid fa-sliders"></i> Hero Banners / Slides
            </a>
            <a href="projects.php" class="sidebar-link <?= ($adminPage === 'projects' || $adminPage === 'project_edit') ? 'active' : '' ?>">
                <i class="fa-solid fa-images"></i> Projects Gallery
            </a>
            <a href="project_add.php" class="sidebar-link <?= ($adminPage === 'project_add') ? 'active' : '' ?>">
                <i class="fa-solid fa-circle-plus"></i> Add Project
            </a>
            <a href="services.php" class="sidebar-link <?= ($adminPage === 'services' || $adminPage === 'service_edit' || $adminPage === 'service_add') ? 'active' : '' ?>">
                <i class="fa-solid fa-screwdriver-wrench"></i> Services Management
            </a>
            <a href="service_add.php" class="sidebar-link <?= ($adminPage === 'service_add') ? 'active' : '' ?>">
                <i class="fa-solid fa-plus-circle"></i> Add Service
            </a>

            <div class="nav-section-title">Inquiries &amp; Settings</div>
            <a href="inquiries.php" class="sidebar-link <?= ($adminPage === 'inquiries') ? 'active' : '' ?>">
                <i class="fa-solid fa-envelope-open-text"></i> Quote Inquiries
                <?php if ($newInquiriesCount > 0): ?>
                    <span class="badge-counter"><?= $newInquiriesCount ?></span>
                <?php endif; ?>
            </a>
            <a href="settings.php" class="sidebar-link <?= ($adminPage === 'settings') ? 'active' : '' ?>">
                <i class="fa-solid fa-gear"></i> Website Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../index.php" target="_blank" style="color:#94a3b8; font-size:0.85rem; display:flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Website
            </a>
            <a href="logout.php" id="adminLogoutTrigger" style="color:#ef4444; font-size:0.85rem; display:flex; align-items:center; gap:6px; cursor:pointer;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="topbar-page-title">
                <h1><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin Dashboard' ?></h1>
            </div>

            <div class="topbar-actions">
                <a href="../index.php" target="_blank" style="font-size:0.88rem; font-weight:600; color:var(--adm-navy); padding:8px 14px; border:1px solid var(--adm-border); border-radius:6px; display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-globe" style="color:var(--adm-gold);"></i> Live Site
                </a>
                <div class="admin-avatar">
                    <div class="avatar-circle">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <span><?= htmlspecialchars($adminUser) ?></span>
                </div>
            </div>
        </header>

        <!-- Flash notification container -->
        <div style="padding: 20px 30px 0;">
            <?php if ($flash): ?>
                <div class="flash-alert flash-<?= htmlspecialchars($flash['type']) ?>" style="padding:14px 20px; border-radius:8px; margin-bottom:10px; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>" style="margin-right:8px;"></i>
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="admin-content">

