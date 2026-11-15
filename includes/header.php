<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/functions.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$sitePhone = get_setting('phone', '077 777 3441');
$siteEmail = get_setting('email', 'gypslanka@gmail.com');
$siteAddress = get_setting('location_country', 'Sri Lanka');
$siteDomain = get_setting('website', 'gypslanka.com');

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Gyps Lanka' : 'Gyps Lanka | Ceiling & Interior Solutions - Turning Ceilings Into Art' ?></title>
    <meta name="description" content="Professional Gypsum False Ceiling & Interior Solutions in Sri Lanka. Turning Ceilings into Art for Homes, Offices and Commercial Spaces.">
    <link rel="icon" type="image/jpeg" href="assets/images/logo.jpg">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Announcement / Top Bar -->
<header class="topbar">
    <div class="container topbar-content">
        <div class="topbar-left">
            <span class="topbar-item">
                <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($siteAddress) ?>
            </span>
            <span class="topbar-item">
                <i class="fa-solid fa-envelope"></i> 
                <a href="mailto:<?= htmlspecialchars($siteEmail) ?>"><?= htmlspecialchars($siteEmail) ?></a>
            </span>
        </div>
        <div class="topbar-right">
            <div class="topbar-socials">
                <a href="<?= htmlspecialchars(get_setting('facebook_url', '#')) ?>" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="<?= htmlspecialchars(get_setting('instagram_url', '#')) ?>" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="<?= htmlspecialchars(get_setting('tiktok_url', '#')) ?>" target="_blank" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                <a href="<?= htmlspecialchars(get_setting('youtube_url', '#')) ?>" target="_blank" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>
    </div>
</header>

<!-- Main Navigation -->
<nav class="navbar">
    <div class="container nav-container">
        <!-- Logo -->
        <a href="index.php" class="brand-logo">
            <img src="assets/images/logo.jpg" alt="Gyps Lanka Logo">
            <div class="brand-text">
                <span class="brand-title">GYPS<span>LANKA</span></span>
                <span class="brand-subtitle">Ceiling & Interior</span>
            </div>
        </a>

        <!-- Menu Links -->
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php" class="nav-link <?= ($currentPage === 'index') ? 'active' : '' ?>">Home</a></li>
            <li><a href="about.php" class="nav-link <?= ($currentPage === 'about') ? 'active' : '' ?>">About Us</a></li>
            <li class="nav-dropdown">
                <a href="services.php" class="nav-link <?= ($currentPage === 'services') ? 'active' : '' ?>">
                    Services <i class="fa-solid fa-chevron-down" style="font-size:0.75rem; margin-left:3px;"></i>
                </a>
                <div class="dropdown-menu">
                    <a href="services.php#gypsum-ceilings" class="dropdown-item">Gypsum False Ceilings</a>
                    <a href="services.php#led-lighting" class="dropdown-item">LED & Cove Lighting</a>
                    <a href="services.php#gypsum-board" class="dropdown-item">Gypsum Board Works</a>
                    <a href="services.php#interior-finishing" class="dropdown-item">Interior Finishing</a>
                    <a href="services.php#custom-designs" class="dropdown-item">Custom Designs & More</a>
                </div>
            </li>
            <li><a href="projects.php" class="nav-link <?= ($currentPage === 'projects') ? 'active' : '' ?>">Our Projects</a></li>
            <li><a href="products.php" class="nav-link <?= ($currentPage === 'products' || $currentPage === 'product_detail') ? 'active' : '' ?>">Products</a></li>
            <li><a href="contact.php" class="nav-link <?= ($currentPage === 'contact') ? 'active' : '' ?>">Contact Us</a></li>
            
            <!-- Mobile Action Items -->
            <li style="display:none;" class="mobile-only-link">
                <a href="admin/login.php" class="nav-link"><i class="fa-solid fa-lock"></i> Admin Login</a>
            </li>
        </ul>

        <!-- Action Buttons -->
        <div class="nav-actions">
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $sitePhone) ?>" class="phone-pill">
                <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($sitePhone) ?>
            </a>
            <a href="https://<?= htmlspecialchars($siteDomain) ?>" class="domain-pill">
                <i class="fa-solid fa-paper-plane"></i> <?= htmlspecialchars($siteDomain) ?>
            </a>
        </div>

        <!-- Mobile Hamburger Toggle -->
        <button type="button" class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</nav>

<!-- Flash Alerts if any -->
<?php if ($flash): ?>
<div class="container" style="margin-top: 15px;">
    <div class="flash-alert flash-<?= htmlspecialchars($flash['type']) ?>">
        <div>
            <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>" style="margin-right:8px;"></i>
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    </div>
</div>
<?php endif; ?>

