<?php
// Close database connection if it exists
if (isset($conn) && $conn) {
    // Connection kept open for persistent use
}
?>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h5>College Admission System</h5>
                        <p>A comprehensive platform for managing college admissions efficiently.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo BASE_URL; ?>pages/about.php" class="text-white">About Us</a></li>
                            <li><a href="<?php echo BASE_URL; ?>pages/contact.php" class="text-white">Contact</a></li>
                            <li><a href="<?php echo BASE_URL; ?>pages/faq.php" class="text-white">FAQ</a></li>
                            <li><a href="<?php echo BASE_URL; ?>pages/help.php" class="text-white">Help & Support</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Contact Us</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-map-marker-alt me-2"></i>123 Education Lane, City</li>
                            <li><i class="fas fa-phone me-2"></i>+91 1234567890</li>
                            <li><i class="fas fa-envelope me-2"></i>admissions@college.edu</li>
                        </ul>
                    </div>
                </div>
                <hr style="border-color: rgba(255,255,255,0.2);">
                <div class="text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> College Admission System. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>js/main.js"></script>
    
    <?php if (isset($extra_js)): ?>
    <?php echo $extra_js; ?>
    <?php endif; ?>
    
    <script>
        // Load notifications count
        <?php if (isset($_SESSION['user_id'])): ?>
        function loadNotificationCount() {
            $.ajax({
                url: '<?php echo BASE_URL; ?>ajax/get_notifications.php',
                type: 'GET',
                data: { count_only: true },
                success: function(response) {
                    $('#notification-count').text(response);
                }
            });
        }
        loadNotificationCount();
        setInterval(loadNotificationCount, 30000); // Refresh every 30 seconds
        <?php endif; ?>
    </script>
</body>
</html>

