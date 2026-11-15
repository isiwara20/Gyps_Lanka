<?php
// ==============================================================
// GYPS LANKA - Helper Functions
// ==============================================================

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mailer.php';

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

function url_slug($string) {
    $string = preg_replace('~[^\pL\d]+~u', '-', $string);
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    $string = preg_replace('~[^-\w]+~', '', $string);
    $string = trim($string, '-');
    $string = preg_replace('~-+~', '-', $string);
    $string = strtolower($string);
    return empty($string) ? 'item-' . time() : $string;
}

function format_price($price, $label = 'LKR') {
    if ($price === null || $price === '' || $price <= 0) {
        return 'Contact for Price';
    }
    return 'LKR ' . number_format($price, 2) . ($label ? ' <span class="price-unit">(' . htmlspecialchars($label) . ')</span>' : '');
}

function truncate_text($text, $limit = 100) {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . '...';
}

function is_admin_logged_in() {
    return isset($_SESSION['gyps_admin_logged_in']) && $_SESSION['gyps_admin_logged_in'] === true;
}

function require_admin() {
    if (!is_admin_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

function set_flash($type, $message) {
    $_SESSION['flash_notification'] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash_notification'])) {
        $flash = $_SESSION['flash_notification'];
        unset($_SESSION['flash_notification']);
        return $flash;
    }
    return null;
}

function upload_image_file($file, $targetSubdir = 'products') {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid file parameter.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error code: ' . $file['error']];
    }

    if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
        return ['success' => false, 'error' => 'File size exceeds maximum limit (10MB).'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif'
    ];

    if (!array_key_exists($mime, $allowedMimes)) {
        return ['success' => false, 'error' => 'Invalid image format. Allowed formats: JPG, PNG, WEBP, GIF.'];
    }

    $ext = $allowedMimes[$mime];
    $filename = sprintf('%s_%s.%s', uniqid('gyps_', true), bin2hex(random_bytes(4)), $ext);
    
    $targetDir = __DIR__ . '/../assets/images/uploads/' . $targetSubdir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $destPath = $targetDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['success' => false, 'error' => 'Failed to save uploaded image.'];
    }

    return [
        'success' => true,
        'path' => 'assets/images/uploads/' . $targetSubdir . '/' . $filename
    ];
}

// ==============================================================
// HERO SLIDER / HOME BANNER MANAGEMENT
// ==============================================================
function get_default_hero_slides() {
    return [
        [
            'id'            => 1,
            'badge'         => 'CEILING & INTERIOR SOLUTIONS',
            'title'         => 'Turning Ceilings Into <span class="text-gold-art">Art.</span>',
            'description'   => 'Professional Gypsum Ceiling & Interior Solutions for Homes, Offices and Commercial Spaces across Sri Lanka.',
            'image'         => 'assets/images/hero-ceiling.jpg',
            'btn_text'      => 'Get a Free Quote',
            'btn_link'      => '#quoteModal',
            'btn2_text'     => 'View Our Projects',
            'btn2_link'     => '#projects',
            'highlight_tag' => 'Featured Project: Multi-Tiered Luxury Cove Ceiling',
            'display_order' => 1,
            'is_active'     => 1,
        ],
        [
            'id'            => 2,
            'badge'         => 'ARCHITECTURAL LIGHTING',
            'title'         => 'Modern Minimalist <span class="text-gold-art">Cove Illumination.</span>',
            'description'   => 'Concealed LED linear channels and soft ambient halo lighting engineered with zero glare and seamless ceiling transitions.',
            'image'         => 'assets/images/project-2.jpg',
            'btn_text'      => 'Explore Lighting',
            'btn_link'      => '#quoteModal',
            'btn2_text'     => 'View Lighting Specs',
            'btn2_link'     => 'services.php#led-lighting',
            'highlight_tag' => 'Featured Project: Concealed Dual-Tone Ambient LED',
            'display_order' => 2,
            'is_active'     => 1,
        ],
        [
            'id'            => 3,
            'badge'         => 'COMMERCIAL & RESIDENTIAL',
            'title'         => 'Geometric Stepped <span class="text-gold-art">Ceiling Trays.</span>',
            'description'   => 'Precision dropped box clouds and acoustical gypsum drywall solutions designed for modern offices, villas, and boutique spaces.',
            'image'         => 'assets/images/project-3.jpg',
            'btn_text'      => 'Book Inspection',
            'btn_link'      => '#quoteModal',
            'btn2_text'     => 'Browse Commercial Works',
            'btn2_link'     => 'projects.php',
            'highlight_tag' => 'Featured Project: Stepped Boardroom Tray',
            'display_order' => 3,
            'is_active'     => 1,
        ],
        [
            'id'            => 4,
            'badge'         => 'BESPOKE CRAFTSMANSHIP',
            'title'         => 'Master Living Room <span class="text-gold-art">Ceiling Salons.</span>',
            'description'   => 'Multi-tier dropped ceilings with crystal chandelier centerpieces, crown moulding, and Level-5 mirror-smooth plaster skim finish.',
            'image'         => 'assets/images/project-1.jpg',
            'btn_text'      => 'Get Custom Quote',
            'btn_link'      => '#quoteModal',
            'btn2_text'     => 'View Completed Salons',
            'btn2_link'     => 'projects.php',
            'highlight_tag' => 'Featured Project: Luxury High-Ceiling Salon',
            'display_order' => 4,
            'is_active'     => 1,
        ],
    ];
}

function get_hero_slides($all = false) {
    $db = get_db();
    $defaults = get_default_hero_slides();
    if (!$db) {
        return $defaults;
    }

    try {
        // Auto-create table if it does not exist yet
        $db->exec("
            CREATE TABLE IF NOT EXISTS `hero_slides` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `badge` varchar(100) DEFAULT 'CEILING & INTERIOR SOLUTIONS',
              `description` text DEFAULT NULL,
              `image` varchar(255) NOT NULL,
              `btn_text` varchar(50) DEFAULT 'Get a Free Quote',
              `btn_link` varchar(255) DEFAULT '#quoteModal',
              `btn2_text` varchar(50) DEFAULT 'View Our Projects',
              `btn2_link` varchar(255) DEFAULT '#projects',
              `highlight_tag` varchar(150) DEFAULT NULL,
              `display_order` int(11) DEFAULT 0,
              `is_active` tinyint(1) DEFAULT 1,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed if table is empty
        $count = (int)$db->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn();
        if ($count === 0) {
            $insert = $db->prepare("
                INSERT INTO hero_slides (id, badge, title, description, image, btn_text, btn_link, btn2_text, btn2_link, highlight_tag, display_order, is_active)
                VALUES (:id, :badge, :title, :description, :image, :btn_text, :btn_link, :btn2_text, :btn2_link, :highlight_tag, :display_order, :is_active)
            ");
            foreach ($defaults as $s) {
                $insert->execute([
                    ':id'            => $s['id'],
                    ':badge'         => $s['badge'],
                    ':title'         => $s['title'],
                    ':description'   => $s['description'],
                    ':image'         => $s['image'],
                    ':btn_text'      => $s['btn_text'],
                    ':btn_link'      => $s['btn_link'],
                    ':btn2_text'     => $s['btn2_text'],
                    ':btn2_link'     => $s['btn2_link'],
                    ':highlight_tag' => $s['highlight_tag'],
                    ':display_order' => $s['display_order'],
                    ':is_active'     => $s['is_active'],
                ]);
            }
        }

        $sql = "SELECT * FROM hero_slides " . ($all ? "" : "WHERE is_active = 1 ") . "ORDER BY display_order ASC, id ASC";
        $stmt = $db->query($sql);
        $slides = $stmt->fetchAll();
        return !empty($slides) ? $slides : $defaults;
    } catch (Exception $e) {
        error_log("Error fetching hero slides: " . $e->getMessage());
        return $defaults;
    }
}

/**
 * Intelligent Google Maps Embed URL Cleaner
 * Handles raw URLs, full <iframe> tags, place links, and plain addresses
 */
function clean_gmaps_embed_url($input) {
    $default = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.58586025134!2d79.82118635!3d6.9218335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a7003%3A0x320b2e4d32d3838d!2sColombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1700000000000!5m2!1sen!2slk';

    if (empty($input)) {
        return $default;
    }

    $trimmed = trim($input);

    // If user pasted an entire iframe tag, extract the src attribute
    if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $trimmed, $matches)) {
        return htmlspecialchars_decode($matches[1]);
    }

    // Direct Google Maps embed URL
    if (strpos($trimmed, 'google.com/maps/embed') !== false) {
        return $trimmed;
    }

    // Google Maps URL with output=embed
    if (strpos($trimmed, 'http://') === 0 || strpos($trimmed, 'https://') === 0) {
        if (strpos($trimmed, 'maps.google.') !== false || strpos($trimmed, 'goo.gl') !== false) {
            if (strpos($trimmed, 'output=embed') !== false) {
                return $trimmed;
            }
            return $trimmed . (strpos($trimmed, '?') !== false ? '&' : '?') . 'output=embed';
        }
        return $trimmed;
    }

    // Plain text address or city name fallback
    return 'https://maps.google.com/maps?q=' . urlencode($trimmed) . '&output=embed';
}

