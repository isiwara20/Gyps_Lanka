<?php
$pageTitle = 'Our Services - Gyps Lanka Ceiling & Interior';
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$services = [];

if ($db) {
    try {
        $stmt = $db->query("SELECT * FROM services ORDER BY display_order ASC");
        $services = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Services fetch error on services.php: " . $e->getMessage());
    }
}

// Fallback if database is empty
if (empty($services)) {
    $services = [
        [
            'id' => 1,
            'title' => 'Gypsum False Ceilings',
            'slug' => 'gypsum-ceilings',
            'icon_svg' => 'layers',
            'image' => 'assets/images/service-1.jpg',
            'full_desc' => 'We design and construct breathtaking multi-tier, dropped, and floating ceiling systems for living rooms, bedrooms, dining halls, and executive offices. Utilizing Elephant and Saint-Gobain boards mounted on heavy-duty galvanized channel framing, our false ceilings ensure sag-free longevity, optimal acoustics, and thermal insulation.',
            'features' => "Multi-Tiered Cove Ceilings\nFloating Island Clouds\nDropped Perimeter Trays\nChandelier Centerpieces"
        ],
        [
            'id' => 2,
            'title' => 'LED & Cove Lighting',
            'slug' => 'led-lighting',
            'icon_svg' => 'lightbulb',
            'image' => 'assets/images/service-2.jpg',
            'full_desc' => 'Lighting makes the ceiling come alive. We install concealed cove lighting channels with milky diffusers that eliminate glare and harsh diode hotspots. Choose from warm white 3000K golden glows, natural 4000K daylight, or smart app-controlled tunable color scenes.',
            'features' => "Concealed Cove Illumination\nFlush Aluminum Profile Lines\nAnti-Glare COB Downlights\nSmart Dimming Controls"
        ],
        [
            'id' => 3,
            'title' => 'Gypsum Board Works & Partitions',
            'slug' => 'gypsum-board',
            'icon_svg' => 'grid',
            'image' => 'assets/images/service-3.jpg',
            'full_desc' => 'Professional drywall partitions, soundproofing acoustic boards, moisture-resistant ceiling linings for bathrooms and kitchens, and certified fire-rated enclosures for commercial buildings. Fast, clean, lightweight, and versatile.',
            'features' => "Acoustic Room Dividers\nMoisture Resistant Boards\nCommercial Grid T-Bar Ceilings\nFirestop Safety Barriers"
        ],
        [
            'id' => 4,
            'title' => 'Interior Finishing & Skim Coating',
            'slug' => 'interior-finishing',
            'icon_svg' => 'sofa',
            'image' => 'assets/images/service-4.jpg',
            'full_desc' => 'The true beauty of a ceiling is in its finish. Our skilled craftsmen apply multi-layer USG Boral joint compound, fiberglass tape reinforce all seams, and sand each panel to a smooth satin surface ready for premium paint application.',
            'features' => "Level-5 Mirror Finish Skimming\nAnti-Crack Joint Reinforcement\nConcealed AC Vent Pockets\nCurtain Pelmet Integration"
        ],
        [
            'id' => 5,
            'title' => 'Custom Designs & 3D Features',
            'slug' => 'custom-designs',
            'icon_svg' => 'star',
            'image' => 'assets/images/service-5.jpg',
            'full_desc' => 'Bespoke fluted wall paneling, curved serpentine ceiling trays, decorative crown moulding cornices, and integrated TV entertainment feature walls that make your interior stand out with bespoke luxury.',
            'features' => "3D Fluted Wall Panels\nGypsum Cornice Moulding\nCurved & Circular Ceilings\nTV Feature Backdrops"
        ]
    ];
}

// Icon mapper helper
function get_service_fa_icon($icon) {
    $map = [
        'layers'    => 'layer-group',
        'lightbulb' => 'lightbulb',
        'grid'      => 'border-all',
        'sofa'      => 'couch',
        'star'      => 'star',
        'wand-magic-sparkles' => 'wand-magic-sparkles',
        'paint-roller' => 'paint-roller',
        'cubes'     => 'cubes',
    ];
    if (isset($map[$icon])) return $map[$icon];
    if (strpos($icon, 'fa-') === 0) return substr($icon, 3);
    return !empty($icon) ? $icon : 'layer-group';
}
?>

<!-- Services Hero Header -->
<section class="page-hero-section">
    <div class="container">
        <div class="hero-badge" style="justify-content:center;">
            <span>&mdash;</span> EXPERT CEILING SOLUTIONS
        </div>
        <h1 class="page-hero-title">
            Our Comprehensive <span class="text-gold">Services</span>
        </h1>
        <p class="page-hero-desc">
            A complete suite of gypsum false ceiling, architectural cove lighting, and drywall solutions tailored for luxury and durability.
        </p>
    </div>
</section>

<!-- Detailed Services List -->
<section style="padding: 70px 0; background: #fff;">
    <div class="container" style="display:flex; flex-direction:column; gap:60px;">
        
        <?php foreach ($services as $idx => $srv): 
            $isEven = ($idx % 2 === 1);
            $faIcon = get_service_fa_icon($srv['icon_svg'] ?? '');
            $featureLines = array_filter(array_map('trim', explode("\n", $srv['features'] ?? '')));
            $imgOrder = $isEven ? 'order:2;' : '';
            $textOrder = $isEven ? 'order:1;' : '';
        ?>
        <div id="<?= htmlspecialchars($srv['slug']) ?>" class="service-detail-row">
            <!-- Service Image -->
            <div style="<?= $imgOrder ?> border-radius:var(--radius-lg); overflow:hidden; box-shadow:var(--shadow-md); border:1px solid var(--border-light);">
                <img src="<?= htmlspecialchars($srv['image'] ?: 'assets/images/service-1.jpg') ?>" alt="<?= htmlspecialchars($srv['title']) ?>" style="width:100%; height:340px; object-fit:cover;">
            </div>

            <!-- Service Details -->
            <div style="<?= $textOrder ?>">
                <div style="display:inline-flex; align-items:center; gap:8px; color:var(--gold); font-size:0.85rem; font-weight:700; text-transform:uppercase; margin-bottom:8px;">
                    <i class="fa-solid fa-<?= $faIcon ?>"></i> Service <?= sprintf('%02d', $idx + 1) ?>
                </div>
                <h2 class="service-detail-title"><?= htmlspecialchars($srv['title']) ?></h2>
                <p style="color:#475569; font-size:1.02rem; line-height:1.7; margin-bottom:18px;">
                    <?= nl2br(htmlspecialchars($srv['full_desc'] ?? $srv['short_desc'] ?? '')) ?>
                </p>

                <?php if (!empty($featureLines)): ?>
                <ul class="service-features-list">
                    <?php foreach ($featureLines as $feat): ?>
                    <li><i class="fa-solid fa-check text-gold" style="margin-right:8px;"></i> <?= htmlspecialchars($feat) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <a href="#quoteModal" class="btn btn-gold open-quote-modal" data-service="<?= htmlspecialchars($srv['title']) ?>">
                    Request Quotation <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
