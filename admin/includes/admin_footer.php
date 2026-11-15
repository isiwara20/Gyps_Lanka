        </div> <!-- /admin-content -->
    </main>
</div> <!-- /admin-layout -->

<!-- Logout Confirmation Modal Overlay -->
<div id="logoutModalOverlay" class="admin-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
    <div class="admin-modal-backdrop" id="logoutModalBackdrop"></div>
    <div class="admin-confirm-dialog">
        <button type="button" class="modal-close-icon" id="closeLogoutModalBtn" aria-label="Close">&times;</button>
        <div class="confirm-icon-box">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </div>
        <h3 id="logoutModalTitle">Confirm Logout</h3>
        <p>Are you sure you want to end your current session? You will need to enter your credentials to access the admin console again.</p>
        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-outline-gold btn-sm" id="cancelLogoutBtn" style="color:var(--adm-navy); border-color:#cbd5e1; background:#fff;">
                Cancel
            </button>
            <a href="logout.php" class="btn btn-danger btn-sm" id="confirmLogoutBtn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Yes, Log Out
            </a>
        </div>
    </div>
</div>

<script src="../assets/js/admin.js"></script>
</body>
</html>

