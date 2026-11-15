/**
 * GYPS LANKA - Admin Dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // Live Image Preview for File Inputs
    const fileInputs = document.querySelectorAll('input[type="file"].preview-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            const previewId = this.getAttribute('data-preview');
            const previewContainer = document.getElementById(previewId);
            if (!previewContainer) return;

            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Simple Table Search Filter
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.flash-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // ==============================================================
    // Logout Confirmation Modal Overlay
    // ==============================================================
    const logoutOverlay = document.getElementById('logoutModalOverlay');
    const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');
    const closeLogoutBtn = document.getElementById('closeLogoutModalBtn');
    const logoutBackdrop = document.getElementById('logoutModalBackdrop');
    const logoutTriggers = document.querySelectorAll('a[href$="logout.php"], #adminLogoutTrigger');

    function openLogoutModal(e) {
        if (e) e.preventDefault();
        if (logoutOverlay) {
            logoutOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLogoutModal() {
        if (logoutOverlay) {
            logoutOverlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    logoutTriggers.forEach(btn => {
        btn.addEventListener('click', openLogoutModal);
    });

    if (cancelLogoutBtn) {
        cancelLogoutBtn.addEventListener('click', closeLogoutModal);
    }

    if (closeLogoutBtn) {
        closeLogoutBtn.addEventListener('click', closeLogoutModal);
    }

    if (logoutBackdrop) {
        logoutBackdrop.addEventListener('click', closeLogoutModal);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && logoutOverlay && logoutOverlay.style.display === 'flex') {
            closeLogoutModal();
        }
    });
});

function confirmDelete(message = 'Are you sure you want to delete this item? This action cannot be undone.') {
    return confirm(message);
}

