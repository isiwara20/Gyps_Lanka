# GYPS LANKA — Ceiling & Interior Solutions
> **"Turning Ceilings Into Art."**  
> Professional Gypsum Ceiling & Interior Solutions for Homes, Offices and Commercial Spaces in Sri Lanka.

---

## 🌟 Overview & UI Fidelity

This website has been built to match the provided UI design for **GYPS LANKA**, with an **Admin Dashboard** allowing live management of products, portfolio projects, quote requests, and company settings.

### 🎨 Homepage Design Sections:
1. **Top Announcement Bar**:
   - Location: *Sri Lanka*
   - Email: *gypslanka@gmail.com*
   - Social links: *Facebook, Instagram, TikTok, YouTube*
2. **Main Navigation**:
   - Circular official Gyps Lanka Logo with gold rim
   - Menu items: *Home, About Us, Services (with dropdown), Our Projects, Products, Contact Us*
   - Action buttons: *077 777 3441* and *gypslanka.com*
3. **Hero Showcase**:
   - Tagline badge: *— CEILING & INTERIOR SOLUTIONS*
   - Headline: *Turning Ceilings Into Art.*
   - Action buttons: *Get a Free Quote →* and *▶ View Our Projects*
   - High-end cove ceiling imagery with gold lighting accents
4. **Floating Quick Highlights Ribbon**:
   - Gypsum False Ceilings
   - LED & Cove Lighting
   - Residential & Commercial
   - Gypsum Board Works
   - Custom Designs & More
5. **About Gypslanka**:
   - Ceilings visual with *12+ Years of Craft* badge
   - 4 Value Badges: *High Quality Materials, Experienced Team, Reliable Service, On-time Completion*
6. **Our Services**:
   - Deep luxury navy container with gold underline
   - 5 interactive cards with ceiling photography, icons, and hover animations
7. **Products Storefront & Catalog**:
   - Live products fetched dynamically from MySQL / phpMyAdmin
   - Displaying Elephant Gypsum Boards, Saint-Gobain MR, LED Cove Aluminum Profiles, Channels, Screws, and Accessories
8. **Our Recent Projects**:
   - Portfolio showcase of completed residential and commercial works
9. **Why Choose Gypslanka?**:
   - Modern & Stylish Designs, Durable & Long Lasting, Affordable Solutions, Dedicated Customer Support
10. **Call-To-Action Banner**:
    - *"Let's Build Your Dream Interior"* with *Get a Free Quote Now* and *077 777 3441*
11. **Interactive Free Quote Modal**:
    - Allows clients to request custom estimates, floor plan inspections, and select required ceiling solutions
12. **Footer**:
    - Logo, quick links, services directory, contact details, copyright bar

---

## 🚀 Quick Start Guide (XAMPP & phpMyAdmin)

### Step 1: Start XAMPP
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.

### Step 2: Initialize Database (Choose Option A or Option B)

#### Option A: One-Click Auto Setup (Easiest)
Simply open your web browser and navigate to:
```
http://localhost/Gyps_Lanka/setup.php
```
Click **"Run Auto-Setup Now"**. The installer will automatically create `gyps_lanka_db` in MySQL and populate all tables, products, categories, projects, and the admin account!

#### Option B: Via phpMyAdmin
1. Open your browser and visit: `http://localhost/phpmyadmin/`
2. Click **New** in the left sidebar to create a database named `gyps_lanka_db` (or simply go to the **Import** tab).
3. Click **Import** in the top navigation.
4. Click **Choose File** and select `database.sql` located inside:
   ```
   D:\Chandila\XAMP\htdocs\Gyps_Lanka\database.sql
   ```
5. Click **Import** at the bottom of the page.

---

## 🔐 Admin Dashboard

The website features an administration console for managing your products catalog, portfolio projects, quote requests, and settings:

- **Admin URL**: `http://localhost/Gyps_Lanka/admin/`
- **Username**: `admin`
- **Password**: `admin123`

### 🛠️ Admin Capabilities:
- **Product Management**:
  - Add new products with name, category, SKU, price (LKR), stock status, specifications, and photo upload.
  - Edit or update existing products.
  - Delete products (automatically cleans up uploaded files from disk).
  - Feature products on the homepage storefront with one click.
- **Category Management**:
  - Add, edit, or reorganize product categories (e.g. *Gypsum Ceiling Boards, Channel Frameworks, LED Profiles, Cornices, Compounds*).
- **Project Showcase**:
  - Add photos and descriptions of completed ceiling projects for the homepage and portfolio.
- **Customer Quote Inquiries**:
  - View real-time quote requests submitted by customers through the website modal and contact form.
  - Track statuses (*New*, *Contacted*, *Completed*).
  - One-click direct WhatsApp button to reply immediately to customer inquiries.
- **Website & Company Settings**:
  - Update hotline numbers, WhatsApp number, email, address, working hours, and social media links live across the entire website.
  - Change administrator password.

---

## 📂 Project Structure

```
Gyps_Lanka/
├── admin/
│   ├── includes/
│   │   ├── admin_header.php        # Admin sidebar navigation & auth
│   │   └── admin_footer.php
│   ├── categories.php              # Category manager
│   ├── index.php                   # Dashboard analytics & stats
│   ├── inquiries.php               # Customer quote requests manager
│   ├── login.php                   # Secure admin login
│   ├── logout.php                  # Session logout
│   ├── product_add.php             # Add new product form
│   ├── product_delete.php          # Delete product action
│   ├── product_edit.php            # Edit product form
│   ├── products.php                # Product catalog list
│   ├── project_add.php             # Add project showcase
│   ├── project_delete.php          # Delete project
│   ├── project_edit.php            # Edit project
│   ├── projects.php                # Projects gallery list
│   ├── services.php                # Services list overview
│   └── settings.php                # Website contact & brand settings
├── assets/
│   ├── css/
│   │   ├── admin.css               # Admin dashboard theme
│   │   └── style.css               # Main luxury navy/gold theme matching UI
│   ├── js/
│   │   ├── admin.js                # Image previews, table filtering, modals
│   │   └── main.js                 # Frontend interactions & quote modal
│   └── images/
│       ├── logo.jpg                # Gyps Lanka official high-res logo
│       ├── hero-ceiling.jpg        # Hero luxury ceiling image
│       ├── about-ceiling.jpg       # About ceiling image
│       ├── service-1..5.jpg        # 5 authentic service images
│       ├── project-1..4.jpg        # 4 authentic project images
│       └── uploads/                # User uploaded product & project photos
├── config/
│   ├── db.php                      # PDO database connection & self-healing auto-setup
│   └── functions.php               # Helpers, sanitization, image uploader, auth
├── includes/
│   ├── footer.php                  # Footer matching UI mockup
│   ├── header.php                  # Top bar & navigation matching UI mockup
│   └── quote_modal.php             # Free Quote popup modal
├── about.php                       # About Us page
├── contact.php                     # Contact Us page with map & quote form
├── database.sql                    # Full MySQL database dump for phpMyAdmin
├── index.php                       # Homepage strictly matching provided UI
├── product_detail.php              # Single product details & WhatsApp order
├── products.php                    # Full products catalog with filters & search
├── projects.php                    # Completed projects portfolio
├── quote_process.php               # Processes frontend quote inquiries
├── services.php                    # Detailed 5 services showcase
├── setup.php                       # 1-Click database auto-installer
└── README.md                       # Documentation
```

---

## 📞 Support & Contacts
- **Hotline**: 077 777 3441
- **Email**: gypslanka@gmail.com
- **Website**: gypslanka.com
- **Location**: Sri Lanka

