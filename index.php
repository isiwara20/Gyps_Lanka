<?php
$pageTitle = 'Ceiling & Interior Solutions - Turning Ceilings Into Art';
require_once __DIR__ . '/includes/header.php';

// Fetch dynamic products, projects, and services from database
$db = get_db();
$featuredProducts = [];
$recentProjects = [];
$servicesList = [];

if ($db) {
    try {
        // Featured Products
        $stmt = $db->query("
            SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_featured = 1 
            ORDER BY p.id ASC 
            LIMIT 4
        ");
        $featuredProducts = $stmt->fetchAll();

        // Recent Projects
        $stmt = $db->query("
            SELECT * FROM projects 
            WHERE is_featured = 1 
            ORDER BY display_order ASC, id ASC 
            LIMIT 8
        ");
        $recentProjects = $stmt->fetchAll();

        // Services
        $stmt = $db->query("
            SELECT * FROM services 
            ORDER BY display_order ASC 
            LIMIT 5
        ");
        $servicesList = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database query error on index: " . $e->getMessage());
    }
}

// Fallback services if database query is empty
if (empty($servicesList)) {
    $servicesList = [
        ['title' => 'Gypsum False Ceilings', 'image' => 'assets/images/service-1.jpg', 'icon' => 'fa-layer-group', 'slug' => 'gypsum-false-ceilings', 'short_desc' => 'Multi-tiered, dropped, and custom cove ceilings.'],
        ['title' => 'LED & Cove Lighting', 'image' => 'assets/images/service-2.jpg', 'icon' => 'fa-lightbulb', 'slug' => 'led-cove-lighting', 'short_desc' => 'Concealed warm cove illumination and profile lights.'],
        ['title' => 'Gypsum Board Works', 'image' => 'assets/images/service-3.jpg', 'icon' => 'fa-border-all', 'slug' => 'gypsum-board-works', 'short_desc' => 'Drywalls, partitions, and sound insulation boards.'],
        ['title' => 'Interior Finishing', 'image' => 'assets/images/service-4.jpg', 'icon' => 'fa-couch', 'slug' => 'interior-finishing', 'short_desc' => 'Mirror-smooth skim coating and architectural finishing.'],
        ['title' => 'Custom Designs & More', 'image' => 'assets/images/service-5.jpg', 'icon' => 'fa-star', 'slug' => 'custom-designs-and-more', 'short_desc' => 'Curved clouds, 3D wall features, and bespoke accents.']
    ];
}

$heroSlides = get_hero_slides();
$slideCount = count($heroSlides);
?>

<!-- ==============================================================
     HERO SECTION WITH FULL-WIDTH BACKGROUND IMAGE SLIDER (2026 Edition)
     ============================================================== -->
<section class="hero-slider-section" id="heroSliderSection">
    <!-- Full-Width Background Slides Layer -->
    <div class="hero-bg-slider">
        <?php foreach ($heroSlides as $idx => $slide): ?>
            <div class="hero-bg-slide <?= ($idx === 0) ? 'active' : '' ?>" 
                 style="background-image: url('<?= htmlspecialchars($slide['image']) ?>');" 
                 data-slide="<?= $idx ?>"></div>
        <?php endforeach; ?>
    </div>

    <!-- Luxury Dark Gradient Vignette Overlay -->
    <div class="hero-overlay"></div>

    <!-- Ambient Floating Light Glows -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="container hero-slider-container">
        <div class="hero-text-slides">
            <?php foreach ($heroSlides as $idx => $slide): ?>
                <div class="hero-slide <?= ($idx === 0) ? 'active' : '' ?>" data-slide="<?= $idx ?>">
                    <?php if (!empty($slide['badge'])): ?>
                        <div class="hero-badge">
                            <span>&mdash;</span> <?= htmlspecialchars($slide['badge']) ?>
                        </div>
                    <?php endif; ?>

                    <h1 class="hero-title">
                        <?= $slide['title'] ?>
                    </h1>

                    <?php if (!empty($slide['description'])): ?>
                        <p class="hero-desc">
                            <?= htmlspecialchars($slide['description']) ?>
                        </p>
                    <?php endif; ?>

                    <div class="hero-btns">
                        <?php if (!empty($slide['btn_text'])): ?>
                            <a href="<?= htmlspecialchars($slide['btn_link'] ?: '#quoteModal') ?>" 
                               class="btn btn-gold <?= (strpos($slide['btn_link'], '#quoteModal') !== false) ? 'open-quote-modal' : '' ?>"
                               <?= (!empty($slide['badge'])) ? 'data-service="' . htmlspecialchars($slide['badge']) . '"' : '' ?>>
                                <?= htmlspecialchars($slide['btn_text']) ?> <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($slide['btn2_text'])): ?>
                            <a href="<?= htmlspecialchars($slide['btn2_link'] ?: '#projects') ?>" class="btn btn-outline-light">
                                <?= htmlspecialchars($slide['btn2_text']) ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($slide['highlight_tag'])): ?>
                        <div class="hero-slide-badge">
                            <i class="fa-solid fa-gem text-gold"></i> <?= htmlspecialchars($slide['highlight_tag']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Slider Controls: Arrows & Indicator Dots -->
        <div class="hero-controls">
            <button type="button" class="hero-arrow-btn" id="heroPrevBtn" aria-label="Previous Slide">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="hero-dots-wrap">
                <?php foreach ($heroSlides as $idx => $slide): ?>
                    <span class="hero-dot <?= ($idx === 0) ? 'active' : '' ?>" data-slide="<?= $idx ?>"></span>
                <?php endforeach; ?>
                <span class="hero-counter" id="heroCounter">01 / <?= sprintf('%02d', $slideCount) ?></span>
            </div>

            <button type="button" class="hero-arrow-btn" id="heroNextBtn" aria-label="Next Slide">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

    </div>
</section>

<!-- ==============================================================
     FLOATING QUICK HIGHLIGHTS RIBBON
     ============================================================== -->
<section class="ribbon-section reveal-on-scroll reveal-up">
    <div class="container">
        <div class="ribbon-card">
            <div class="ribbon-item">
                <div class="ribbon-icon"><i class="fa-solid fa-layer-group"></i></div>
                <div class="ribbon-text">Gypsum<br>False Ceilings</div>
            </div>
            <div class="ribbon-item">
                <div class="ribbon-icon"><i class="fa-solid fa-lightbulb"></i></div>
                <div class="ribbon-text">LED &amp; Cove<br>Lighting</div>
            </div>
            <div class="ribbon-item">
                <div class="ribbon-icon"><i class="fa-solid fa-house-chimney"></i></div>
                <div class="ribbon-text">Residential &amp;<br>Commercial</div>
            </div>
            <div class="ribbon-item">
                <div class="ribbon-icon"><i class="fa-solid fa-border-all"></i></div>
                <div class="ribbon-text">Gypsum<br>Board Works</div>
            </div>
            <div class="ribbon-item">
                <div class="ribbon-icon"><i class="fa-solid fa-star"></i></div>
                <div class="ribbon-text">Custom Designs<br>&amp; More</div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================================
     ABOUT GYPSLANKA SECTION + ANIMATED STATS
     ============================================================== -->
<section class="about-section" id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-visual reveal-on-scroll reveal-left">
                <div class="about-img-box">
                    <img src="assets/images/about-ceiling.jpg" alt="About Gyps Lanka Ceilings">
                </div>
                <div class="about-badge-exp">
                    <div class="num">12+</div>
                    <div class="label">Years of Craft</div>
                </div>
            </div>

            <div class="about-content reveal-on-scroll reveal-right">
                <h2>About <span class="text-gold">Gypslanka</span></h2>
                <p>
                    Gypslanka is a professional gypsum ceiling and interior solutions provider in Sri Lanka, delivering modern, stylish and high-quality ceiling designs for residential, commercial and office spaces.
                </p>

                <div class="about-features">
                    <div class="feature-box">
                        <i class="fa-solid fa-award"></i>
                        <h4>High Quality<br>Materials</h4>
                    </div>
                    <div class="feature-box">
                        <i class="fa-solid fa-users"></i>
                        <h4>Experienced<br>Team</h4>
                    </div>
                    <div class="feature-box">
                        <i class="fa-solid fa-handshake"></i>
                        <h4>Reliable<br>Service</h4>
                    </div>
                    <div class="feature-box">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <h4>On-time<br>Completion</h4>
                    </div>
                </div>

                <a href="about.php" class="btn btn-outline-gold" style="color:var(--primary-navy); border-color:var(--gold);">
                    Read More About Us <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- 2026 Animated Stats Counter Banner -->
        <div class="stats-banner reveal-on-scroll reveal-up delay-2">
            <div class="stats-counter-grid">
                <div class="stat-counter-item">
                    <div class="stat-number" data-target="12" data-suffix="+">0</div>
                    <div class="stat-label">Years of Excellence</div>
                </div>
                <div class="stat-counter-item">
                    <div class="stat-number" data-target="850" data-suffix="+">0</div>
                    <div class="stat-label">Ceiling Projects Completed</div>
                </div>
                <div class="stat-counter-item">
                    <div class="stat-number" data-target="100" data-suffix="%">0</div>
                    <div class="stat-label">Sag-Free Quality Guarantee</div>
                </div>
                <div class="stat-counter-item">
                    <div class="stat-number" data-target="24" data-suffix="/7">0</div>
                    <div class="stat-label">Customer Support &amp; Quotes</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================================
     OUR SERVICES SECTION
     ============================================================== -->
<section class="services-section" id="services">
    <div class="container">
        <div class="section-header reveal-on-scroll reveal-up">
            <h2>Our <span class="gold-underline">Services</span></h2>
            <p>A complete range of gypsum and interior solutions to enhance your space.</p>
        </div>

        <div class="services-grid">
            <?php foreach ($servicesList as $idx => $srv): 
                $rawIcon = !empty($srv['icon_svg']) ? $srv['icon_svg'] : (!empty($srv['icon']) ? $srv['icon'] : 'layers');
                $iconMap = [
                    'layers'              => 'fa-layer-group',
                    'lightbulb'           => 'fa-lightbulb',
                    'grid'                => 'fa-border-all',
                    'sofa'                => 'fa-couch',
                    'star'                => 'fa-star',
                    'wand-magic-sparkles' => 'fa-wand-magic-sparkles',
                    'paint-roller'        => 'fa-paint-roller',
                    'cubes'               => 'fa-cubes'
                ];
                $icon = $iconMap[$rawIcon] ?? (strpos($rawIcon, 'fa-') === 0 ? $rawIcon : 'fa-' . $rawIcon);
            ?>
            <div class="service-card reveal-on-scroll reveal-up delay-<?= ($idx % 5) + 1 ?>">
                <div class="service-card-img">
                    <img src="<?= htmlspecialchars($srv['image'] ?: 'assets/images/service-1.jpg') ?>" alt="<?= htmlspecialchars($srv['title']) ?>">
                </div>
                <div class="service-card-body">
                    <div class="service-icon-circle">
                        <i class="fa-solid <?= $icon ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($srv['title']) ?></h3>
                    <p><?= htmlspecialchars($srv['short_desc'] ?? 'Premium ceiling and interior craftsmanship.') ?></p>
                    <a href="services.php#<?= htmlspecialchars($srv['slug']) ?>" style="color:var(--gold); font-size:0.82rem; font-weight:700; margin-top:auto; display:inline-flex; align-items:center; gap:5px;">
                        Explore <i class="fa-solid fa-arrow-right" style="font-size:0.75rem;"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==============================================================
     OUR PRODUCTS SECTION (ADMIN-MANAGED STOREFRONT)
     ============================================================== -->
<section class="products-section" id="products">
    <div class="container">
        <div class="projects-top reveal-on-scroll reveal-up">
            <div class="projects-top-left">
                <h2>Ceiling &amp; Gypsum <span class="text-gold">Products</span></h2>
                <p>Top-grade certified gypsum boards, channels, LED systems &amp; accessories. Managed live via Admin Dashboard.</p>
            </div>
            <a href="products.php" class="btn btn-outline-gold" style="color:var(--primary-navy); border-color:var(--gold);">
                View All Products <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="products-grid">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $idx => $product): ?>
                <div class="product-card reveal-on-scroll reveal-up delay-<?= ($idx % 4) + 1 ?>">
                    <div class="product-thumb">
                        <img src="<?= htmlspecialchars($product['image'] ?: 'assets/images/logo.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php 
                            $stockClass = 'stock-in';
                            $stockText = 'In Stock';
                            if ($product['stock_status'] === 'low_stock') { $stockClass = 'stock-low'; $stockText = 'Low Stock'; }
                            if ($product['stock_status'] === 'out_of_stock') { $stockClass = 'stock-out'; $stockText = 'Out of Stock'; }
                            if ($product['stock_status'] === 'on_order') { $stockClass = 'stock-low'; $stockText = 'On Order'; }
                        ?>
                        <span class="product-badge-stock <?= $stockClass ?>"><?= $stockText ?></span>
                    </div>
                    <div class="product-body">
                        <div class="product-cat"><?= htmlspecialchars($product['category_name'] ?? 'Gypsum Materials') ?></div>
                        <h4 class="product-title"><?= htmlspecialchars($product['name']) ?></h4>
                        <p class="product-desc"><?= htmlspecialchars(truncate_text($product['short_desc'] ?: $product['description'], 85)) ?></p>
                        <div class="product-footer">
                            <div class="product-price">
                                <?= format_price($product['price'], $product['price_label']) ?>
                            </div>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-gold btn-sm">
                                View <i class="fa-solid fa-arrow-right" style="font-size:0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <img src="assets/images/uploads/products/elephant_gypsum.jpg" alt="Elephant Gypsum Board">
                        <span class="product-badge-stock stock-in">In Stock</span>
                    </div>
                    <div class="product-body">
                        <div class="product-cat">Gypsum Boards</div>
                        <h4 class="product-title">Elephant Gypsum Board (9mm)</h4>
                        <p class="product-desc">Standard 4ft x 6ft high-strength gypsum board for ceilings and walls.</p>
                        <div class="product-footer">
                            <div class="product-price">LKR 2,450.00</div>
                            <a href="products.php" class="btn btn-gold btn-sm">View</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ==============================================================
     OUR RECENT PROJECTS INTERACTIVE CAROUSEL SLIDER (Matching UI)
     ============================================================== -->
<section class="projects-section" id="projects">
    <div class="container">
        <div class="projects-top reveal-on-scroll reveal-up">
            <div class="projects-top-left">
                <h2>Our <span class="text-gold">Recent Projects</span></h2>
                <p>A look at some of our completed ceiling and interior projects.</p>
            </div>
            <a href="projects.php" class="btn btn-outline-gold" style="color:var(--primary-navy); border-color:var(--gold);">
                View All Projects <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- Carousel Outer Shell with Arrows -->
        <div class="carousel-outer reveal-on-scroll reveal-up delay-1">
            <button type="button" class="carousel-nav-btn prev-btn" id="projPrevBtn" aria-label="Previous Projects">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="projects-carousel-track" id="projectsCarouselTrack">
                <?php if (!empty($recentProjects)): ?>
                    <?php foreach ($recentProjects as $proj): ?>
                    <div class="project-card">
                        <div class="project-img-box">
                            <img src="<?= htmlspecialchars($proj['image']) ?>" alt="<?= htmlspecialchars($proj['title']) ?>">
                            <span class="project-tag"><?= htmlspecialchars($proj['category']) ?></span>
                        </div>
                        <div class="project-body">
                            <h4><?= htmlspecialchars($proj['title']) ?></h4>
                            <div class="location">
                                <i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> 
                                <?= htmlspecialchars($proj['location'] ?? 'Sri Lanka') ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="project-card">
                        <div class="project-img-box">
                            <img src="assets/images/project-1.jpg" alt="Luxury Living Room Tiered Cove Ceiling">
                            <span class="project-tag">Residential</span>
                        </div>
                        <div class="project-body">
                            <h4>Luxury Living Room Tiered Cove Ceiling</h4>
                            <div class="location"><i class="fa-solid fa-location-dot text-gold"></i> Colombo 07</div>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img-box">
                            <img src="assets/images/project-2.jpg" alt="Modern Minimalist Blue Cove Ceiling">
                            <span class="project-tag">Residential</span>
                        </div>
                        <div class="project-body">
                            <h4>Modern Minimalist Blue Cove Ceiling</h4>
                            <div class="location"><i class="fa-solid fa-location-dot text-gold"></i> Nawala</div>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img-box">
                            <img src="assets/images/project-3.jpg" alt="Geometric Stepped Ceiling">
                            <span class="project-tag">Commercial</span>
                        </div>
                        <div class="project-body">
                            <h4>Geometric Stepped Ceiling with Warm Glow</h4>
                            <div class="location"><i class="fa-solid fa-location-dot text-gold"></i> Kandy City</div>
                        </div>
                    </div>
                    <div class="project-card">
                        <div class="project-img-box">
                            <img src="assets/images/project-4.jpg" alt="Contemporary Master Hall Ceiling">
                            <span class="project-tag">Residential</span>
                        </div>
                        <div class="project-body">
                            <h4>Contemporary Master Hall Gypsum Ceiling</h4>
                            <div class="location"><i class="fa-solid fa-location-dot text-gold"></i> Battaramulla</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" class="carousel-nav-btn next-btn" id="projNextBtn" aria-label="Next Projects">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Carousel Pagination Dots -->
            <div class="carousel-dots">
                <span class="carousel-dot active" data-index="0"></span>
                <span class="carousel-dot" data-index="1"></span>
                <span class="carousel-dot" data-index="2"></span>
                <span class="carousel-dot" data-index="3"></span>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================================
     CLIENT TESTIMONIALS AUTO-SWAP SLIDER
     ============================================================== -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header reveal-on-scroll reveal-up">
            <h2>What Our <span class="gold-underline">Clients Say</span></h2>
            <p>Real feedback from homeowners, commercial builders, and architects across Sri Lanka.</p>
        </div>

        <div class="testimonial-swap-container reveal-on-scroll reveal-up delay-1" data-interval="5000">
            <div class="testimonial-swap-card">
                <div class="testimonial-quote-watermark"><i class="fa-solid fa-quote-right"></i></div>
                <div class="testimonial-slides-wrapper">
                    
                    <!-- Testimonial 1 -->
                    <div class="testimonial-slide-item active">
                        <div class="test-stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="test-quote-text">
                            "Gyps Lanka transformed our living room in Colombo 07 into a luxury suite. The double cove lighting and the crystal chandelier placement are breathtaking. The team was punctual, neat, and completed the work on schedule."
                        </p>
                        <div class="test-client-profile">
                            <div class="test-client-left">
                                <div class="test-avatar-ring">RJ</div>
                                <div class="test-client-info">
                                    <h4>Ruwan Jayasinghe</h4>
                                    <div class="test-client-loc"><i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> Colombo 07 • Residential Villa</div>
                                </div>
                            </div>
                            <span class="test-verified-badge"><i class="fa-solid fa-circle-check"></i> Verified Client</span>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="testimonial-slide-item">
                        <div class="test-stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="test-quote-text">
                            "As an architect, finding contractors who execute complex geometric stepped ceilings with zero sagging is rare. Gyps Lanka's laser leveling and framing quality in our Nawala commercial project was world-class."
                        </p>
                        <div class="test-client-profile">
                            <div class="test-client-left">
                                <div class="test-avatar-ring">DS</div>
                                <div class="test-client-info">
                                    <h4>Arch. Dinesh Senanayake</h4>
                                    <div class="test-client-loc"><i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> Nawala • Commercial &amp; Architecture</div>
                                </div>
                            </div>
                            <span class="test-verified-badge"><i class="fa-solid fa-circle-check"></i> Verified Architect</span>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="testimonial-slide-item">
                        <div class="test-stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="test-quote-text">
                            "I had severe heat issues on our top floor in Kandy. Gyps Lanka installed moisture-resistant boards with thermal insulation and warm cove lighting. The room is now noticeably cooler, quieter, and looks stunning."
                        </p>
                        <div class="test-client-profile">
                            <div class="test-client-left">
                                <div class="test-avatar-ring">NP</div>
                                <div class="test-client-info">
                                    <h4>Dr. Nirmal Perera</h4>
                                    <div class="test-client-loc"><i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> Kandy City • Residence Owner</div>
                                </div>
                            </div>
                            <span class="test-verified-badge"><i class="fa-solid fa-circle-check"></i> Verified Client</span>
                        </div>
                    </div>

                    <!-- Testimonial 4 -->
                    <div class="testimonial-slide-item">
                        <div class="test-stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="test-quote-text">
                            "We contracted Gyps Lanka for a 4-storey commercial office ceiling project in Battaramulla. From acoustic sound dampening to integrated AC diffuser slots, their engineering precision and 5-year warranty gave us complete peace of mind."
                        </p>
                        <div class="test-client-profile">
                            <div class="test-client-left">
                                <div class="test-avatar-ring">CW</div>
                                <div class="test-client-info">
                                    <h4>Chaminda Wickramasinghe</h4>
                                    <div class="test-client-loc"><i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> Battaramulla • Commercial Plaza</div>
                                </div>
                            </div>
                            <span class="test-verified-badge"><i class="fa-solid fa-circle-check"></i> Verified Client</span>
                        </div>
                    </div>

                    <!-- Testimonial 5 -->
                    <div class="testimonial-slide-item">
                        <div class="test-stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p class="test-quote-text">
                            "From the first 3D mockup consultation to handover took just 4 days! The ambient hidden LED cove finish is immaculate without a single visible seam or screw mark. Highly recommended for modern Sri Lankan homes!"
                        </p>
                        <div class="test-client-profile">
                            <div class="test-client-left">
                                <div class="test-avatar-ring">KA</div>
                                <div class="test-client-info">
                                    <h4>Kavindi Alwis</h4>
                                    <div class="test-client-loc"><i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> Thalawathugoda • Modern Home</div>
                                </div>
                            </div>
                            <span class="test-verified-badge"><i class="fa-solid fa-circle-check"></i> Verified Client</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Slider Controls (Prev / Dots / Next) -->
            <div class="testimonial-nav-controls">
                <button type="button" class="test-nav-btn test-prev-btn" aria-label="Previous Testimonial">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="test-dots-group">
                    <span class="test-dot active" data-index="0"></span>
                    <span class="test-dot" data-index="1"></span>
                    <span class="test-dot" data-index="2"></span>
                    <span class="test-dot" data-index="3"></span>
                    <span class="test-dot" data-index="4"></span>
                </div>
                <button type="button" class="test-nav-btn test-next-btn" aria-label="Next Testimonial">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================================
     WHY CHOOSE GYPSLANKA? SECTION
     ============================================================== -->
<section class="why-section">
    <div class="container why-grid">
        <div class="why-left reveal-on-scroll reveal-left">
            <h2>Why Choose <span class="text-gold">Gypslanka?</span></h2>
            <p>
                We combine creativity, quality and expert craftsmanship to deliver modern interior solutions that match your style, space and budget.
            </p>
            <a href="#quoteModal" class="btn btn-gold open-quote-modal">
                Start Your Project <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="why-features reveal-on-scroll reveal-right">
            <div class="why-feature-card">
                <div class="why-icon"><i class="fa-regular fa-gem"></i></div>
                <h4>Modern &amp; Stylish Designs</h4>
                <p>Architectural ceiling concepts tailored to enhance your interior aesthetics.</p>
            </div>
            <div class="why-feature-card">
                <div class="why-icon"><i class="fa-solid fa-gears"></i></div>
                <h4>Durable &amp; Long Lasting</h4>
                <p>Heavy duty galvanized framing and premium grade gypsum boards that resist sagging.</p>
            </div>
            <div class="why-feature-card">
                <div class="why-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                <h4>Affordable Solutions</h4>
                <p>Transparent square-foot pricing with zero hidden fees and free on-site inspections.</p>
            </div>
            <div class="why-feature-card">
                <div class="why-icon"><i class="fa-solid fa-headset"></i></div>
                <h4>Dedicated Customer Support</h4>
                <p>End-to-end guidance from 3D conceptualization to handover and warranty support.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==============================================================
     CALL TO ACTION BANNER (Matching Mockup)
     ============================================================== -->
<section class="cta-section">
    <div class="container">
        <div class="cta-banner reveal-on-scroll reveal-scale">
            <div class="cta-content">
                <h2>Let's Build Your <span class="text-gold-art">Dream Interior</span></h2>
                <p>Get a free consultation and quotation for your gypsum ceiling and interior project today.</p>
            </div>
            <div class="cta-actions">
                <a href="#quoteModal" class="btn btn-gold open-quote-modal">
                    <i class="fa-brands fa-whatsapp" style="font-size:1.15rem;"></i> Get a Free Quote Now <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $sitePhone) ?>" class="cta-phone-pill">
                    <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($sitePhone) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Floating WhatsApp Quick Button -->
<a href="https://wa.me/<?= htmlspecialchars(get_setting('whatsapp', '94777773441')) ?>?text=Hello%20Gyps%20Lanka,%20I%20would%20like%20to%20get%20a%20free%20quote%20for%20a%20ceiling%20project" target="_blank" class="floating-whatsapp" aria-label="Chat on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
    <span>Chat with Us</span>
</a>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
