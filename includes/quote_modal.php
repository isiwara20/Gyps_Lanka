<!-- GYPS LANKA - Free Quote Modal -->
<div class="modal-overlay" id="quoteModal">
    <div class="modal-box">
        <button type="button" class="modal-close" id="closeQuoteModal" aria-label="Close Modal">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-header">
            <h3>Get a Free <span class="text-gold">Quote</span></h3>
            <p>Tell us about your ceiling or interior project and get an instant estimate & site inspection schedule.</p>
        </div>

        <form action="quote_process.php" method="POST">
            <div class="form-group">
                <label class="form-label" for="quote_name">Full Name *</label>
                <input type="text" id="quote_name" name="name" class="form-control" placeholder="e.g. Ruwan Jayasinghe" required>
            </div>

            <div class="modal-form-grid">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="quote_phone">Phone / WhatsApp *</label>
                    <input type="tel" id="quote_phone" name="phone" class="form-control" placeholder="077 777 3441" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="quote_email">Email Address</label>
                    <input type="email" id="quote_email" name="email" class="form-control" placeholder="your@email.com">
                </div>
            </div>

            <div class="modal-form-grid">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="modalServiceSelect">Service / Solution</label>
                    <select id="modalServiceSelect" name="service" class="form-control">
                        <option value="Gypsum False Ceilings">Gypsum False Ceilings</option>
                        <option value="LED & Cove Lighting">LED & Cove Lighting</option>
                        <option value="Gypsum Board Works">Gypsum Board Works</option>
                        <option value="Interior Finishing">Interior Finishing</option>
                        <option value="Custom Designs & More">Custom Designs & More</option>
                        <option value="Ceiling Materials & Boards">Ceiling Materials & Boards</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="quote_location">City / Location *</label>
                    <input type="text" id="quote_location" name="location" class="form-control" placeholder="e.g. Colombo, Kandy" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="quote_size">Estimated Area / Room Size (Optional)</label>
                <input type="text" id="quote_size" name="room_size" class="form-control" placeholder="e.g. 20x15 ft or 300 sq ft">
            </div>

            <div class="form-group">
                <label class="form-label" for="quote_message">Project Details / Requirements</label>
                <textarea id="quote_message" name="message" class="form-control" rows="3" placeholder="Describe what you want (e.g. 2-tier dropped cove ceiling, chandelier provision, etc.)" required></textarea>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%; padding:14px; font-size:1.05rem;">
                Submit Quote Request <i class="fa-solid fa-paper-plane" style="margin-left:6px;"></i>
            </button>
        </form>
    </div>
</div>

