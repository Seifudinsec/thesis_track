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
    });
    </script>
</body>
</html>
