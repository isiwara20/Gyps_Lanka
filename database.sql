-- ==============================================================
-- GYPS LANKA - Ceiling & Interior Solutions
-- Database Export for phpMyAdmin
-- Database Name: `gyps_lanka_db`
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `gyps_lanka_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gyps_lanka_db`;

-- --------------------------------------------------------
-- Table structure for `users` (Admin Authentication)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin User: admin / admin123
INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`) VALUES
(1, 'admin', '$2y$10$y1MBukr98UhG/oh4KstXd.ddMqzjx0aTWxzpI4tDOodpfyAtWP946', 'Gyps Lanka Admin', 'gypslanka@gmail.com')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- --------------------------------------------------------
-- Table structure for `categories`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `display_order`) VALUES
(1, 'Gypsum Ceiling Boards', 'gypsum-ceiling-boards', 'High quality standard, moisture-resistant, and fire-rated gypsum boards for ceilings and partitions.', 1),
(2, 'Channel Frameworks & Profiles', 'channel-frameworks-profiles', 'Galvanized main tees, cross tees, wall angles, and stud framing for long-lasting structural support.', 2),
(3, 'LED & Cove Lighting Systems', 'led-cove-lighting-systems', 'Concealed LED profiles, strip lights, and warm cove illumination kits.', 3),
(4, 'Gypsum Cornices & Medallions', 'gypsum-cornices-medallions', 'Decorative gypsum crown mouldings, center rosettes, and ornamental trims.', 4),
(5, 'Compounds & Finishing Plaster', 'compounds-finishing-plaster', 'USG Boral all-purpose joint compounds, jointing tapes, and smooth ceiling finishes.', 5),
(6, 'Fasteners & Accessories', 'fasteners-accessories', 'Drywall bugle head screws, ceiling wire hangers, wall plugs, and access panels.', 6)
ON DUPLICATE KEY UPDATE `name`=`name`;

-- --------------------------------------------------------
-- Table structure for `products`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL UNIQUE,
  `sku` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `price_label` varchar(50) DEFAULT 'LKR',
  `short_desc` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock_status` enum('in_stock','low_stock','out_of_stock','on_order') DEFAULT 'in_stock',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_prod_cat` (`category_id`),
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `price`, `price_label`, `short_desc`, `description`, `image`, `stock_status`, `is_featured`) VALUES
(1, 1, 'Elephant Gypsum Board (9mm Standard)', 'elephant-gypsum-board-9mm', 'GYP-EL-09', 2450.00, 'LKR / Sheet', 'Premium Elephant Brand standard 9mm gypsum board for false ceilings and drywalls (4ft x 6ft).', 'Engineered for smooth finishes, high sag resistance, and effortless cutting. Elephant brand gypsum boards are the industry choice across Sri Lanka for residential living rooms, modern bedrooms, and commercial ceiling installations. Dimension: 4ft x 6ft x 9mm.', 'assets/images/uploads/products/elephant_gypsum.jpg', 'in_stock', 1),
(2, 1, 'Saint-Gobain Gyproc MR Moisture Resistant (12mm)', 'saint-gobain-gyproc-mr-12mm', 'GYP-SG-MR12', 3850.00, 'LKR / Sheet', 'Water & moisture resistant gypsum board ideal for kitchens, washrooms, and damp ceiling areas.', 'Saint-Gobain Gyproc Moisture Resistant board incorporates special water-repellent additives in the core and green water-resistant paper liners. Perfect for coastal areas in Sri Lanka, ceiling borders near bathrooms, and luxury kitchen islands. Dimension: 4ft x 8ft x 12.5mm.', 'assets/images/uploads/products/saint_gobain_mr.jpg', 'in_stock', 1),
(3, 1, 'Boral FireStop Fire-Rated Board (15mm)', 'boral-firestop-board-15mm', 'GYP-BOR-FS15', 4650.00, 'LKR / Sheet', 'Class-A fire rated gypsum board offering up to 2 hours of passive fire protection for commercial ceilings.', 'Designed for commercial buildings, hotel corridors, server rooms, and residential kitchens requiring certified fire barrier performance. Glass fiber reinforced core with pink heavy-duty liner. 4ft x 8ft x 15mm.', 'assets/images/uploads/products/boral_firestop.jpg', 'in_stock', 0),
(4, 2, 'Heavy-Duty Galvanized Main Channel (0.50mm)', 'heavy-duty-galvanized-main-channel', 'CHN-MC-50', 680.00, 'LKR / Length (12ft)', 'Electro-galvanized high-tensile steel main ceiling runner channel.', 'Anti-corrosive zinc coating designed to withstand tropical Sri Lankan humidity. Provides rigid load-bearing ceiling framework for multi-tiered suspended gypsum ceilings. Length: 12 Feet (3.66m).', 'assets/images/uploads/products/main_channel.jpg', 'in_stock', 1),
(5, 2, 'Galvanized Cross T & Wall Angle Trim', 'galvanized-cross-t-wall-angle', 'CHN-CT-WA', 420.00, 'LKR / Length (10ft)', 'Precision engineered wall perimeter angles and intermediate furring cross channels.', 'Ensures dead-flat ceiling alignment, perimeter anchoring against masonry walls, and seamless jointing without sagging. Length: 10 Feet.', 'assets/images/uploads/products/cross_tee.jpg', 'in_stock', 0),
(6, 3, 'Concealed LED Cove Aluminum Channel with Diffuser', 'concealed-led-cove-aluminum-channel', 'LED-AL-PRF', 1850.00, 'LKR / 2-Meter Bar', 'Anodized aluminum profile with milky opal polycarbonate diffuser for spotless linear cove lighting.', 'Designed specifically for false ceiling drop edges, pelmets, and cove lighting. Eliminates LED diode hotspots, resulting in the signature soft warm glowing ceiling look featured in modern architectural designs.', 'assets/images/uploads/products/led_cove_profile.jpg', 'in_stock', 1),
(7, 3, 'Warm White 3000K Architectural Cove LED Strip (24V)', 'warm-white-3000k-cove-led-strip', 'LED-ST-3000K', 4200.00, 'LKR / 5M Roll', 'Ultra-bright 120 LED/meter high CRI>90 warm amber strip light for luxury ceiling coves.', '24V constant voltage design ensures no voltage drop or dimming across long ceiling perimeters. Emits an inviting 3000K warm golden hue that accents gypsum multi-step ceilings perfectly.', 'assets/images/uploads/products/led_strip_light.jpg', 'in_stock', 1),
(8, 4, 'Gypsum Decorative Cornice Moulding (4 Inch Classic)', 'gypsum-decorative-cornice-moulding-4in', 'COR-GYP-4C', 850.00, 'LKR / 8ft Length', 'Handcrafted pure gypsum plaster crown moulding for classic wall-to-ceiling transitions.', 'Reinforced with natural jute fibers for crack-free durability and razor-sharp decorative detailing. Easy to paint and seamless when finished with joint compound.', 'assets/images/uploads/products/gypsum_cornice.jpg', 'in_stock', 0),
(9, 5, 'USG Boral All-Purpose Joint Compound (28kg Bucket)', 'usg-boral-all-purpose-joint-compound', 'CMP-BOR-28KG', 3600.00, 'LKR / Bucket', 'Ready-mixed professional grade joint compound for taping, topping, and skim coating.', 'Ultra-smooth application, excellent bond strength, minimal shrinkage, and easy sanding to a mirror-flat ceiling finish. Ready to use directly from the bucket.', 'assets/images/uploads/products/joint_compound.jpg', 'in_stock', 1),
(10, 6, 'Bugle Head Drywall Screws (3.5 x 25mm / 35mm - 1000 Pcs)', 'bugle-head-drywall-screws-1000pcs', 'SCR-DW-25MM', 1450.00, 'LKR / Box', 'Black phosphated corrosion-resistant self-piercing drywall screws for metal stud framing.', 'Countersunk bugle head prevents paper tearing while sitting flush with gypsum surface for effortless patching. Box of 1000 pieces.', 'assets/images/uploads/products/drywall_screws.jpg', 'in_stock', 0),
(11, 1, 'Acoustical Vinyl-Faced Ceiling Tiles (600x600mm)', 'acoustical-vinyl-faced-ceiling-tiles', 'ACT-6060-VF', 340.00, 'LKR / Tile', 'Washable vinyl laminated gypsum ceiling tile with aluminum foil backing for exposed T-grid systems.', 'Sound attenuating, light reflecting, and easy to clean. Ideal for modern commercial office spaces, clinics, retail shops, and educational institutions.', 'assets/images/uploads/products/acoustic_tiles.jpg', 'in_stock', 0),
(12, 6, 'Concealed Gypsum Ceiling Access Panel (450x450mm)', 'concealed-gypsum-ceiling-access-panel', 'ACC-PNL-45', 4800.00, 'LKR / Unit', 'Hidden touch-latch ceiling inspection hatch with gypsum board door inlay.', 'Blends invisibly into flat gypsum ceilings while granting easy access to air-conditioning units, ceiling wiring, and plumbing valves. Push-to-open latch mechanism.', 'assets/images/uploads/products/access_panel.jpg', 'in_stock', 1)
ON DUPLICATE KEY UPDATE `name`=`name`;

-- --------------------------------------------------------
-- Table structure for `projects`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` (`id`, `title`, `category`, `location`, `image`, `description`, `is_featured`, `display_order`) VALUES
(1, 'Luxury Living Room Tiered Cove Ceiling', 'Residential', 'Colombo 07', 'assets/images/uploads/projects/project-1.jpg', 'Multi-layered recessed gypsum ceiling with warm golden LED cove perimeter lighting and crystal chandelier center accent.', 1, 1),
(2, 'Modern Minimalist Blue Cove Ceiling', 'Residential', 'Nawala', 'assets/images/uploads/projects/project-2.jpg', 'Dual-color concealed LED lighting with recessed gypsum dropped tray and integrated AC vent concealment.', 1, 2),
(3, 'Geometric Stepped Ceiling with Warm Glow', 'Commercial', 'Kandy City', 'assets/images/uploads/projects/project-3.jpg', 'Custom geometric double-square dropped gypsum ceiling with high-efficiency 3000K cove lighting for a boutique boardroom.', 1, 3),
(4, 'Contemporary Master Hall Gypsum Ceiling', 'Residential', 'Battaramulla', 'assets/images/uploads/projects/project-4.jpg', 'Spacious open-concept living hall ceiling featuring acoustic perimeter borders and high-CRI downlight integration.', 1, 4),
(5, 'Executive Suite Luxury Lighting Ceiling', 'Commercial', 'Rajagiriya', 'assets/images/uploads/projects/project-5.jpg', 'Full penthouse interior ceiling renovation with automated cove lighting scenes and concealed drapery pockets.', 1, 5),
(6, 'Modern Dining Room Accent Ceiling', 'Residential', 'Mount Lavinia', 'assets/images/uploads/projects/project-6.jpg', 'Architectural floating ceiling cloud with ambient halo illumination and concealed sound system.', 1, 6)
ON DUPLICATE KEY UPDATE `title`=`title`;

-- --------------------------------------------------------
-- Table structure for `services`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `short_desc` text DEFAULT NULL,
  `full_desc` longtext DEFAULT NULL,
  `features` text DEFAULT NULL,
  `icon_svg` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` (`id`, `title`, `slug`, `short_desc`, `full_desc`, `features`, `icon_svg`, `image`, `display_order`) VALUES
(1, 'Gypsum False Ceilings', 'gypsum-false-ceilings', 'Custom multi-tiered, dropped, and floating gypsum ceilings tailored to your space.', 'We specialize in designing and constructing world-class gypsum false ceilings for modern homes, luxury residences, hotels, and executive corporate offices. Our team uses top-tier galvanized channel framing and Elephant / Saint-Gobain gypsum boards for flawless structural integrity and immaculate finishing.', 'Multi-Tiered Cove Ceilings\nFloating Island Clouds\nDropped Perimeter Trays\nChandelier Centerpieces', 'layers', 'assets/images/service-1.jpg', 1),
(2, 'LED & Cove Lighting', 'led-cove-lighting', 'Concealed cove lighting, ambient halo effects, and architectural profile light installations.', 'Enhance your ceilings with energy-efficient, warm LED cove lighting that creates an inviting, luxurious ambiance. We integrate aluminum channels, seamless diffusers, and smart dimmer controllers so your ceiling glows without harsh glare or visible hot spots.', 'Concealed Cove Illumination\nFlush Aluminum Profile Lines\nAnti-Glare COB Downlights\nSmart Dimming Controls', 'lightbulb', 'assets/images/service-2.jpg', 2),
(3, 'Gypsum Board Works', 'gypsum-board-works', 'Drywall partitions, acoustic ceiling solutions, and moisture-resistant board installations.', 'From acoustic drywall room dividers to fire-rated partition walls and water-resistant bathroom ceiling installations, we deliver robust drywall and gypsum carpentry solutions that meet rigorous safety standards and provide superior thermal and acoustic comfort.', 'Acoustic Room Dividers\nMoisture Resistant Boards\nCommercial Grid T-Bar Ceilings\nFirestop Safety Barriers', 'grid', 'assets/images/service-3.jpg', 3),
(4, 'Interior Finishing', 'interior-finishing', 'Comprehensive ceiling skimming, seamless joint tape finishing, and luxury interior design.', 'A flawless ceiling begins with expert plastering and surface preparation. We use multi-coat USG Boral joint compounds and fiberglass joint tapes to guarantee crack-free joints, satin-smooth paint surfaces, and integrated architectural details.', 'Level-5 Mirror Finish Skimming\nAnti-Crack Joint Reinforcement\nConcealed AC Vent Pockets\nCurtain Pelmet Integration', 'sofa', 'assets/images/service-4.jpg', 4),
(5, 'Custom Designs & More', 'custom-designs-and-more', 'Bespoke geometric designs, fluted panel walls, ceiling clouds, and custom 3D details.', 'Bring your dream interior to life with custom 3D gypsum wall panels, curved architectural ceiling clouds, fluted wood-look gypsum accents, and TV feature walls crafted by our master artisans.', '3D Fluted Wall Panels\nGypsum Cornice Moulding\nCurved & Circular Ceilings\nTV Feature Backdrops', 'star', 'assets/images/service-5.jpg', 5)
ON DUPLICATE KEY UPDATE `title`=`title`;

-- --------------------------------------------------------
-- Table structure for `inquiries` (Quote Requests & Contacts)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `service` varchar(100) DEFAULT NULL,
  `room_size` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','contacted','completed','cancelled') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inquiries` (`id`, `name`, `phone`, `email`, `service`, `room_size`, `location`, `message`, `status`) VALUES
(1, 'Ruwan Jayasinghe', '077 123 4567', 'ruwan.jaya@gmail.com', 'Gypsum False Ceilings', '650 sq ft', 'Colombo 05', 'Need a full multi-tiered gypsum false ceiling with warm white cove lighting for our new living room and dining area. Please call me for site inspection.', 'new'),
(2, 'Kamal Perera', '071 987 6543', 'kamal.p@hotmail.com', 'LED & Cove Lighting', '1200 sq ft', 'Kandy', 'Renovating an office floor. Need quotation for grid ceiling and custom cove lighting.', 'contacted')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- --------------------------------------------------------
-- Table structure for `settings`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Gyps Lanka'),
('site_tagline', 'Ceiling & Interior Solutions - Turning Ceilings Into Art.'),
('phone', '077 777 3441'),
('phone_display', '077 777 3441'),
('whatsapp', '94777773441'),
('email', 'gypslanka@gmail.com'),
('website', 'gypslanka.com'),
('location_country', 'Sri Lanka'),
('address', 'No. 124, High Level Road, Maharagama, Sri Lanka'),
('facebook_url', 'https://facebook.com/gypslanka'),
('instagram_url', 'https://instagram.com/gypslanka'),
('tiktok_url', 'https://tiktok.com/@gypslanka'),
('youtube_url', 'https://youtube.com/@gypslanka'),
('working_hours', 'Mon - Sat: 8:00 AM - 6:00 PM'),
('gmaps_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.58586025134!2d79.82118635!3d6.9218335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a7003%3A0x320b2e4d32d3838d!2sColombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1700000000000!5m2!1sen!2slk'),
('gmaps_directions_url', 'https://maps.google.com/?q=Colombo+Sri+Lanka'),
('smtp_enabled', '1'),
('smtp_host', 'mail.gypslanka.com'),
('smtp_port', '465'),
('smtp_encryption', 'ssl'),
('smtp_username', 'support@gypslanka.com'),
('smtp_password', ''),
('smtp_from_email', 'support@gypslanka.com'),
('smtp_from_name', 'Gyps Lanka Support'),
('contact_recipient_email', 'info@gypslanka.com')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

-- --------------------------------------------------------
-- Table structure for `hero_slides` (Home Page Banner Slider)
-- --------------------------------------------------------
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

INSERT INTO `hero_slides` (`id`, `badge`, `title`, `description`, `image`, `btn_text`, `btn_link`, `btn2_text`, `btn2_link`, `highlight_tag`, `display_order`, `is_active`) VALUES
(1, 'CEILING & INTERIOR SOLUTIONS', 'Turning Ceilings Into <span class="text-gold-art">Art.</span>', 'Professional Gypsum Ceiling & Interior Solutions for Homes, Offices and Commercial Spaces across Sri Lanka.', 'assets/images/hero-ceiling.jpg', 'Get a Free Quote', '#quoteModal', 'View Our Projects', '#projects', 'Featured Project: Multi-Tiered Luxury Cove Ceiling', 1, 1),
(2, 'ARCHITECTURAL LIGHTING', 'Modern Minimalist <span class="text-gold-art">Cove Illumination.</span>', 'Concealed LED linear channels and soft ambient halo lighting engineered with zero glare and seamless ceiling transitions.', 'assets/images/project-2.jpg', 'Explore Lighting', '#quoteModal', 'View Lighting Specs', 'services.php#led-lighting', 'Featured Project: Concealed Dual-Tone Ambient LED', 2, 1),
(3, 'COMMERCIAL & RESIDENTIAL', 'Geometric Stepped <span class="text-gold-art">Ceiling Trays.</span>', 'Precision dropped box clouds and acoustical gypsum drywall solutions designed for modern offices, villas, and boutique spaces.', 'assets/images/project-3.jpg', 'Book Inspection', '#quoteModal', 'Browse Commercial Works', 'projects.php', 'Featured Project: Stepped Boardroom Tray', 3, 1),
(4, 'BESPOKE CRAFTSMANSHIP', 'Master Living Room <span class="text-gold-art">Ceiling Salons.</span>', 'Multi-tier dropped ceilings with crystal chandelier centerpieces, crown moulding, and Level-5 mirror-smooth plaster skim finish.', 'assets/images/project-1.jpg', 'Get Custom Quote', '#quoteModal', 'View Completed Salons', 'projects.php', 'Featured Project: Luxury High-Ceiling Salon', 4, 1)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);


