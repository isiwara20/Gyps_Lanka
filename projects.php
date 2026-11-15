<?php
$pageTitle = 'Our Projects Portfolio - Gyps Lanka';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$projects = [];

if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM projects ORDER BY display_order ASC, id ASC");
        $projects = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Projects query error: " . $e->getMessage());
    }
}
?>

<!-- Projects Hero Header -->
<section class="page-hero-section">
    <div class="container">
        <div class="hero-badge" style="justify-content:center;">
            <span>&mdash;</span> MASTER PORTFOLIO
        </div>
        <h1 class="page-hero-title">
            Our Completed <span class="text-gold">Ceiling Projects</span>
        </h1>
        <p class="page-hero-desc">
            Explore some of our completed gypsum ceiling, cove lighting, and luxury interior projects executed across Sri Lanka.
        </p>
    </div>
</section>

<!-- Projects Gallery -->
<section style="padding: 70px 0; background:var(--bg-light);">
    <div class="container">
        <!-- Filter Buttons -->
        <div style="display:flex; justify-content:center; gap:12px; margin-bottom:45px; flex-wrap:wrap;">
            <button class="btn btn-gold filter-btn active" data-filter="all">All Projects</button>
            <button class="btn btn-outline-gold filter-btn" data-filter="Residential" style="color:var(--primary-navy); border-color:#cbd5e1; background:#fff;">Residential</button>
            <button class="btn btn-outline-gold filter-btn" data-filter="Commercial" style="color:var(--primary-navy); border-color:#cbd5e1; background:#fff;">Commercial</button>
        </div>

        <!-- Projects Grid -->
        <div class="projects-grid projects-gallery-grid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $proj): ?>
                <div class="project-card project-item" data-category="<?= htmlspecialchars($proj['category']) ?>">
                    <div class="project-img-box" style="height:240px;">
                        <img src="<?= htmlspecialchars($proj['image']) ?>" alt="<?= htmlspecialchars($proj['title']) ?>">
                        <span class="project-tag"><?= htmlspecialchars($proj['category']) ?></span>
                    </div>
                    <div class="project-body">
                        <div class="location" style="margin-bottom:6px;">
                            <i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> 
                            <?= htmlspecialchars($proj['location'] ?? 'Sri Lanka') ?>
                        </div>
                        <h4 style="font-size:1.1rem; margin-bottom:8px;"><?= htmlspecialchars($proj['title']) ?></h4>
                        <p style="font-size:0.85rem; color:#64748b; line-height:1.5;">
                            <?= htmlspecialchars($proj['description'] ?? 'Custom gypsum false ceiling installation.') ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="project-card project-item" data-category="Residential">
                    <div class="project-img-box" style="height:240px;">
                        <img src="assets/images/project-1.jpg" alt="Luxury Living Room Tiered Cove Ceiling">
                        <span class="project-tag">Residential</span>
                    </div>
                    <div class="project-body">
                        <div class="location"><i class="fa-solid fa-location-dot text-gold"></i> Colombo 07</div>
                        <h4>Luxury Living Room Tiered Cove Ceiling</h4>
                        <p>Multi-layered recessed gypsum ceiling with warm golden LED cove perimeter lighting.</p>
                    </div>
                </div>
                <div class="project-card project-item" data-category="Residential">
                    <div class="project-img-box" style="height:240px;">
                        <img src="assets/images/project-2.jpg" alt="Modern Minimalist Blue Cove Ceiling">
                        <span class="project-tag">Residential</span>
                    </div>
                    <div class="project-body">
                        <div class="location"><i class="fa-solid fa-location-dot text-gold"></i> Nawala</div>
                        <h4>Modern Minimalist Blue Cove Ceiling</h4>
                        <p>Dual-color concealed LED lighting with recessed gypsum dropped tray.</p>
                    </div>
                </div>
                <div class="project-card project-item" data-category="Commercial">
                    <div class="project-img-box" style="height:240px;">
                        <img src="assets/images/project-3.jpg" alt="Geometric Stepped Ceiling">
                        <span class="project-tag">Commercial</span>
                    </div>
                    <div class="project-body">
                        <div class="location"><i class="fa-solid fa-location-dot text-gold"></i> Kandy City</div>
                        <h4>Geometric Stepped Ceiling with Warm Glow</h4>
                        <p>Custom geometric double-square dropped gypsum ceiling for boutique boardroom.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to action -->
<section class="cta-section">
    <div class="container">
        <div class="cta-banner">
            <div class="cta-content">
                <h2>Want a ceiling design like these in your property?</h2>
                <p>Send us your floor plan or schedule an on-site visit anywhere in Sri Lanka.</p>
            </div>
            <div class="cta-actions">
                <a href="#quoteModal" class="btn btn-gold open-quote-modal">Get Free Project Estimate</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

