<?php
$pageTitle = 'Contact Us - Gyps Lanka Ceiling & Interior';
require_once __DIR__ . '/includes/header.php';

$sitePhone = get_setting('phone', '077 777 3441');
$siteEmail = get_setting('email', 'gypslanka@gmail.com');
$siteAddress = get_setting('address', 'No. 124, High Level Road, Maharagama, Sri Lanka');
$workingHours = get_setting('working_hours', 'Mon - Sat: 8:00 AM - 6:00 PM');
$whatsapp = get_setting('whatsapp', '94777773441');

// Google Maps dynamic settings from Admin Dashboard
$gmapsEmbedUrl = clean_gmaps_embed_url(get_setting('gmaps_embed_url', ''));
$gmapsDirections = get_setting('gmaps_directions_url', 'https://maps.google.com/?q=Colombo+Sri+Lanka');

// Check for non-AJAX redirect success overlay
$sessionOverlay = $_SESSION['contact_success_overlay'] ?? null;
unset($_SESSION['contact_success_overlay']);
?>

<!-- Contact Hero Header -->
<section class="page-hero-section">
    <div class="container">
        <div class="hero-badge" style="justify-content:center;">
            <span>&mdash;</span> REACH OUT TO US
        </div>
        <h1 class="page-hero-title">
            Contact <span class="text-gold">Gypslanka</span>
        </h1>
        <p class="page-hero-desc">
            Get in touch for ceiling consultations, product inquiries, contractor wholesale orders, or site visits across Sri Lanka.
        </p>
    </div>
</section>

<!-- Contact Info & Form -->
<section style="padding: 70px 0; background: #fff;">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Cards -->
            <div>
                <h2 style="font-size:2rem; font-weight:800; color:var(--primary-navy); margin-bottom:15px;">
                    Let's Discuss Your Project
                </h2>
                <p style="color:#64748b; line-height:1.7; margin-bottom:30px;">
                    Whether you are building a new luxury home, renovating your apartment ceiling, or managing a commercial fitout, our team is ready to assist you.
                </p>

                <div style="display:flex; flex-direction:column; gap:20px; margin-bottom:35px;">
                    <!-- Phone -->
                    <div style="display:flex; gap:16px; align-items:flex-start; padding:18px; background:var(--bg-light); border-radius:var(--radius-md); border:1px solid var(--border-light);">
                        <div style="width:46px; height:46px; border-radius:50%; background:rgba(212,175,55,0.15); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <div style="font-size:0.8rem; text-transform:uppercase; font-weight:700; color:#64748b;">Direct Hotline</div>
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $sitePhone) ?>" style="font-size:1.1rem; font-weight:700; color:var(--primary-navy);">
                                <?= htmlspecialchars($sitePhone) ?>
                            </a>
                            <div style="font-size:0.75rem; color:#94a3b8;">Available 7 Days a week</div>
                        </div>
                    </div>

                    <!-- WhatsApp -->
                    <div style="display:flex; gap:16px; align-items:flex-start; padding:18px; background:var(--bg-light); border-radius:var(--radius-md); border:1px solid var(--border-light);">
                        <div style="width:46px; height:46px; border-radius:50%; background:#dcfce7; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0;">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <div style="font-size:0.8rem; text-transform:uppercase; font-weight:700; color:#64748b;">Instant WhatsApp Chat</div>
                            <a href="https://wa.me/<?= $whatsapp ?>?text=Hello%20Gyps%20Lanka,%20I%20am%20interested%20in%20a%20ceiling%20project" target="_blank" style="font-size:1.1rem; font-weight:700; color:#16a34a;">
                                Chat on WhatsApp &rarr;
                            </a>
                            <div style="font-size:0.75rem; color:#94a3b8;">Fast quotes and floor plan sharing</div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div style="display:flex; gap:16px; align-items:flex-start; padding:18px; background:var(--bg-light); border-radius:var(--radius-md); border:1px solid var(--border-light);">
                        <div style="width:46px; height:46px; border-radius:50%; background:rgba(59,130,246,0.15); color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <div style="font-size:0.8rem; text-transform:uppercase; font-weight:700; color:#64748b;">Official Email</div>
                            <a href="mailto:<?= htmlspecialchars($siteEmail) ?>" style="font-size:1.05rem; font-weight:700; color:var(--primary-navy);">
                                <?= htmlspecialchars($siteEmail) ?>
                            </a>
                        </div>
                    </div>

                    <!-- Address -->
                    <div style="display:flex; gap:16px; align-items:flex-start; padding:18px; background:var(--bg-light); border-radius:var(--radius-md); border:1px solid var(--border-light);">
                        <div style="width:46px; height:46px; border-radius:50%; background:rgba(212,175,55,0.15); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div style="font-size:0.8rem; text-transform:uppercase; font-weight:700; color:#64748b;">Office &amp; Workshop</div>
                            <div style="font-size:0.95rem; font-weight:600; color:var(--primary-navy);">
                                <?= htmlspecialchars($siteAddress) ?>
                            </div>
                            <div style="font-size:0.8rem; color:#64748b; margin-top:3px;">
                                <i class="fa-solid fa-clock"></i> <?= htmlspecialchars($workingHours) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div style="background:#fff; border-radius:var(--radius-lg); padding:35px; border:1px solid var(--border-light); box-shadow:var(--shadow-md); position:relative;">
                <h3 style="font-size:1.6rem; font-weight:800; color:var(--primary-navy); margin-bottom:8px;">
                    Send Us a Message
                </h3>
                <p style="color:#64748b; font-size:0.95rem; margin-bottom:25px;">
                    Fill out the form below and an engineer from Gyps Lanka will reach out within 24 hours.
                </p>

                <div id="contactFormStatus" style="display:none; margin-bottom:20px;"></div>

                <form id="contactPageForm" action="quote_process.php" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="c_name">Your Name *</label>
                        <input type="text" id="c_name" name="name" class="form-control" placeholder="Ruwan Jayasinghe" required>
                    </div>

                    <div class="modal-form-grid">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="c_phone">Phone Number *</label>
                            <input type="tel" id="c_phone" name="phone" class="form-control" placeholder="077 777 3441" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="c_email">Email Address</label>
                            <input type="email" id="c_email" name="email" class="form-control" placeholder="ruwan@example.com">
                        </div>
                    </div>

                    <div class="modal-form-grid">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="c_service">Service Needed</label>
                            <select id="c_service" name="service" class="form-control">
                                <option value="Gypsum False Ceilings">Gypsum False Ceilings</option>
                                <option value="LED & Cove Lighting">LED & Cove Lighting</option>
                                <option value="Gypsum Board Works">Gypsum Board Works</option>
                                <option value="Interior Finishing">Interior Finishing</option>
                                <option value="Custom Designs & More">Custom Designs & More</option>
                                <option value="Ceiling Materials & Boards">Ceiling Materials Purchase</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="c_loc">Location / District *</label>
                            <input type="text" id="c_loc" name="location" class="form-control" placeholder="e.g. Colombo, Kandy, Galle" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="c_size">Estimated Area / Floor Size</label>
                        <input type="text" id="c_size" name="room_size" class="form-control" placeholder="e.g. 500 sq ft or 3 rooms">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="c_msg">Your Project Details / Message *</label>
                        <textarea id="c_msg" name="message" class="form-control" rows="4" placeholder="Tell us what you have in mind..." required></textarea>
                    </div>

                    <button type="submit" id="contactSubmitBtn" class="btn btn-gold btn-lg" style="width:100%; justify-content:center;">
                        <span id="contactBtnText">Send Inquiry Now</span>
                        <i id="contactBtnIcon" class="fa-solid fa-paper-plane" style="margin-left:6px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Google Map Sri Lanka (Configured via Admin Dashboard) -->
<section style="position:relative; height: 420px; width: 100%;">
    <iframe 
        src="<?= htmlspecialchars($gmapsEmbedUrl) ?>" 
        width="100%" 
        height="100%" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>

    <div class="gmaps-floating-badge">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--gold); font-weight:700;">
            <i class="fa-solid fa-location-dot"></i> Visit Our Location
        </div>
        <div style="font-weight:700; color:var(--primary-navy); font-size:0.95rem; margin-top:2px;">
            <?= htmlspecialchars($siteAddress) ?>
        </div>
        <?php if (!empty($gmapsDirections)): ?>
        <div style="margin-top:8px;">
            <a href="<?= htmlspecialchars($gmapsDirections) ?>" target="_blank" class="btn btn-gold btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-size:0.78rem; padding:6px 14px; border-radius:4px;">
                <i class="fa-solid fa-diamond-turn-right"></i> Open in Google Maps
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Success Message Overlay Modal -->
<div id="contactSuccessModal" class="contact-success-overlay" style="<?= ($sessionOverlay) ? 'display:flex;' : 'display:none;' ?>" role="dialog" aria-modal="true">
    <div class="contact-success-backdrop" id="closeSuccessBackdrop"></div>
    <div class="contact-success-box">
        <button type="button" class="success-modal-close" id="closeSuccessBtn" aria-label="Close">&times;</button>
        
        <div class="success-icon-wrap">
            <div class="success-icon-circle">
                <i class="fa-solid fa-check"></i>
            </div>
        </div>

        <div class="success-badge-top">
            <i class="fa-solid fa-envelope-circle-check"></i> Dispatched via SMTP to info@gypslanka.com
        </div>

        <h3 class="success-modal-title">Message Sent Successfully!</h3>

        <p class="success-modal-desc" id="successModalDesc">
            <?= $sessionOverlay ? htmlspecialchars($sessionOverlay['message']) : 'Thank you! Your message has been sent successfully. An engineer from Gyps Lanka will reach out within 24 hours.' ?>
        </p>

        <div class="success-summary-card">
            <div class="summary-item">
                <span class="lbl"><i class="fa-solid fa-user"></i> Client Name:</span>
                <strong id="summaryClientName"><?= $sessionOverlay ? htmlspecialchars($sessionOverlay['name']) : '' ?></strong>
            </div>
            <div class="summary-item">
                <span class="lbl"><i class="fa-solid fa-phone"></i> Contact Hotline:</span>
                <strong id="summaryClientPhone"><?= $sessionOverlay ? htmlspecialchars($sessionOverlay['phone']) : '' ?></strong>
            </div>
            <div class="summary-item">
                <span class="lbl"><i class="fa-solid fa-screwdriver-wrench"></i> Service:</span>
                <strong id="summaryClientService"><?= $sessionOverlay ? htmlspecialchars($sessionOverlay['service']) : '' ?></strong>
            </div>
            <div class="summary-item">
                <span class="lbl"><i class="fa-solid fa-location-dot"></i> Location:</span>
                <strong id="summaryClientLoc"><?= $sessionOverlay ? htmlspecialchars($sessionOverlay['location']) : '' ?></strong>
            </div>
            <div class="summary-item" style="border-bottom:none; padding-bottom:0;">
                <span class="lbl"><i class="fa-solid fa-clock"></i> Engineer Response:</span>
                <span style="color:#16a34a; font-weight:700;"><i class="fa-solid fa-bolt"></i> Within 24 Hours</span>
            </div>
        </div>

        <div class="success-modal-actions">
            <button type="button" class="btn btn-gold" id="successDoneBtn" style="flex:1; justify-content:center;">
                <i class="fa-solid fa-check"></i> Done
            </button>
            <a href="https://wa.me/<?= $whatsapp ?>?text=Hello%20Gyps%20Lanka,%20I%20just%20sent%20a%20contact%20inquiry%20via%20your%20website" target="_blank" class="btn btn-whatsapp" style="flex:1; justify-content:center;">
                <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm   = document.getElementById('contactPageForm');
    const submitBtn     = document.getElementById('contactSubmitBtn');
    const btnText       = document.getElementById('contactBtnText');
    const btnIcon       = document.getElementById('contactBtnIcon');
    const statusBox     = document.getElementById('contactFormStatus');

    const successModal  = document.getElementById('contactSuccessModal');
    const closeBtn      = document.getElementById('closeSuccessBtn');
    const doneBtn       = document.getElementById('successDoneBtn');
    const backdrop      = document.getElementById('closeSuccessBackdrop');

    const modalDesc     = document.getElementById('successModalDesc');
    const sumName       = document.getElementById('summaryClientName');
    const sumPhone      = document.getElementById('summaryClientPhone');
    const sumService    = document.getElementById('summaryClientService');
    const sumLoc        = document.getElementById('summaryClientLoc');

    function openSuccessModal(data) {
        if (data) {
            if (modalDesc && data.message) modalDesc.textContent = data.message;
            if (sumName && data.client_name) sumName.textContent = data.client_name;
            if (sumPhone && data.client_phone) sumPhone.textContent = data.client_phone;
            if (sumService && data.service) sumService.textContent = data.service;
            if (sumLoc && data.location) sumLoc.textContent = data.location;
        }
        if (successModal) {
            successModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSuccessModal() {
        if (successModal) {
            successModal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    if (closeBtn) closeBtn.addEventListener('click', closeSuccessModal);
    if (doneBtn) doneBtn.addEventListener('click', closeSuccessModal);
    if (backdrop) backdrop.addEventListener('click', closeSuccessModal);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && successModal && successModal.style.display === 'flex') {
            closeSuccessModal();
        }
    });

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Set loading state
            submitBtn.disabled = true;
            btnText.textContent = 'Sending Message...';
            btnIcon.className = 'fa-solid fa-circle-notch fa-spin';
            if (statusBox) statusBox.style.display = 'none';

            const formData = new FormData(contactForm);
            formData.append('ajax', '1');

            fetch('quote_process.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                submitBtn.disabled = false;
                btnText.textContent = 'Send Inquiry Now';
                btnIcon.className = 'fa-solid fa-paper-plane';

                if (result.success) {
                    contactForm.reset();
                    openSuccessModal(result);
                } else {
                    if (statusBox) {
                        statusBox.innerHTML = '<div class="flash-alert flash-error" style="margin:0;"><i class="fa-solid fa-circle-exclamation"></i> ' + (result.error || 'Failed to send. Please try again.') + '</div>';
                        statusBox.style.display = 'block';
                    } else {
                        alert(result.error || 'Failed to send message.');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                // Fallback submit naturally if network/JSON parser fails
                contactForm.submit();
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
