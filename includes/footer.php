        </main>
    <?php if (isset($_SESSION['user_id'])): ?>
        </div>
    <?php endif; ?>
    <footer class="<?php echo isset($_SESSION['user_id']) ? 'app-footer' : 'auth-footer'; ?>">
        <p>&copy; <?php echo date('Y'); ?> ThesisTrack - Award Winning Thesis Management System.</p>
        <p>Developer: Seifudin Hassan  | Focused on Academic Excellence</p>
    </footer>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
    // alerts lasts 4 seconds
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }

        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 4000);
        });

        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const sidebarLinks = document.querySelectorAll('.sidebar .nav-item, .sidebar .logout-link, .sidebar .sidebar-brand');

        const setSidebarOpen = (isOpen) => {
            document.body.classList.toggle('sidebar-open', isOpen);
            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                sidebarToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
            }
        };

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                setSidebarOpen(!document.body.classList.contains('sidebar-open'));
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => setSidebarOpen(false));
        }

        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => setSidebarOpen(false));
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                setSidebarOpen(false);
            }
        });
    });
    </script>
</body>
</html>
