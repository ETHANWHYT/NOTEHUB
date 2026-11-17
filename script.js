// ============================================================================
// NOTEHUB - Simple JavaScript (script.js)
// ============================================================================

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile navigation menu toggle
    const navToggle = document.getElementById('navToggle');
    const navLinks = document.getElementById('navLinks');

    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function () {
            navLinks.classList.toggle('active');
        });
    }

    // 2. Client-side file size check (5 MB)
    const fileInput = document.getElementById('note_file');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.size > 5 * 1024 * 1024) {
                alert('File is too large! Maximum allowed size is 5 MB.');
                this.value = '';
            }
        });
    }

    // 3. Password match check on registration
    const regForm = document.getElementById('registerForm');
    if (regForm) {
        regForm.addEventListener('submit', function (e) {
            const pass = document.getElementById('password').value;
            const confirmPass = document.getElementById('confirm_password').value;

            if (pass.length < 6) {
                alert('Password must be at least 6 characters long.');
                e.preventDefault();
                return false;
            }

            if (pass !== confirmPass) {
                alert('Passwords do not match. Please re-enter.');
                e.preventDefault();
                return false;
            }
        });
    }
});

// 4. Confirm before deleting a note
function confirmDelete() {
    return confirm('Are you sure you want to delete this note?');
}
