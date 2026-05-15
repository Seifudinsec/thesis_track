    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> ThesisTrack - Award Winning Thesis Management System.</p>
        <p>Student Developer Version | Focused on Academic Excellence</p>
    </footer>

    <script>
    // Auto-dismiss alerts after 4 seconds
    document.addEventListener('DOMContentLoaded', () => {
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
