<?php
require_once __DIR__ . '/quote_modal.php';
$sitePhone = get_setting('phone', '077 777 3441');
$siteEmail = get_setting('email', 'gypslanka@gmail.com');
$siteAddress = get_setting('location_country', 'Sri Lanka');
$siteDomain = get_setting('website', 'gypslanka.com');
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Col 1: Brand Info -->
            <div class="footer-col footer-col-brand">
                <div class="footer-brand">
                    <img src="assets/images/logo.jpg" alt="Gyps Lanka">
                    <div>
                        <div class="footer-brand-title">GYPSLANKA</div>
                        <div class="footer-brand-subtitle">Ceiling & Interior Solutions</div>
                    </div>
                </div>
                <div class="footer-script">Turning Ceilings Into Art.</div>
                <p class="footer-brand-desc">
                    Premier gypsum ceiling and interior solutions in Sri Lanka. Designing and installing modern false ceilings for luxury homes, apartments, and corporate offices.
                </p>
                <div class="footer-admin-link">
                    <a href="admin/login.php">
                        <i class="fa-solid fa-lock"></i> Staff Portal / Admin
                    </a>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="footer-col footer-col-links">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fa-solid fa-angle-right"></i> Home</a></li>
                    <li><a href="about.php"><i class="fa-solid fa-angle-right"></i> About Us</a></li>
                    <li><a href="services.php"><i class="fa-solid fa-angle-right"></i> Our Services</a></li>
                    <li><a href="projects.php"><i class="fa-solid fa-angle-right"></i> Our Projects</a></li>
                    <li><a href="products.php"><i class="fa-solid fa-angle-right"></i> Ceiling Products</a></li>
                    <li><a href="contact.php"><i class="fa-solid fa-angle-right"></i> Contact Us</a></li>
                </ul>
            </div>

            <!-- Col 3: Services -->
            <div class="footer-col footer-col-services">
                <h4>Our Services</h4>
                <ul class="footer-links">
                    <li><a href="services.php#gypsum-ceilings"><i class="fa-solid fa-angle-right"></i> Gypsum False Ceilings</a></li>
                    <li><a href="services.php#led-lighting"><i class="fa-solid fa-angle-right"></i> LED & Cove Lighting</a></li>
                    <li><a href="services.php#gypsum-board"><i class="fa-solid fa-angle-right"></i> Gypsum Board Works</a></li>
                    <li><a href="services.php#interior-finishing"><i class="fa-solid fa-angle-right"></i> Interior Finishing</a></li>
                    <li><a href="services.php#custom-designs"><i class="fa-solid fa-angle-right"></i> Custom Designs & More</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact & Socials -->
            <div class="footer-col footer-col-contact">
                <h4>Contact Us</h4>
                <div class="footer-contact-list">
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <div>
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $sitePhone) ?>" class="footer-phone-link">
                                <?= htmlspecialchars($sitePhone) ?>
                            </a>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-envelope"></i>
                        <div>
                            <a href="mailto:<?= htmlspecialchars($siteEmail) ?>">
                                <?= htmlspecialchars($siteEmail) ?>
                            </a>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-globe"></i>
                        <div><?= htmlspecialchars($siteDomain) ?></div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div><?= htmlspecialchars($siteAddress) ?></div>
                    </div>
                </div>

                <div class="footer-social-wrap">
                    <div class="footer-social-title">Follow Us</div>
                    <div class="footer-socials">
                        <a href="<?= htmlspecialchars(get_setting('facebook_url', '#')) ?>" target="_blank" class="footer-social-btn" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="<?= htmlspecialchars(get_setting('instagram_url', '#')) ?>" target="_blank" class="footer-social-btn" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="<?= htmlspecialchars(get_setting('tiktok_url', '#')) ?>" target="_blank" class="footer-social-btn" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="<?= htmlspecialchars(get_setting('youtube_url', '#')) ?>" target="_blank" class="footer-social-btn" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Copyright -->
    <div class="footer-bottom">
        <div class="container">
            &copy; 2026 Gypslanka. All Rights Reserved. | Ceiling & Interior Solutions in Sri Lanka.
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script src="assets/js/main.js"></script>
</body>
</html>

