<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'FAQ - Frequently Asked Questions';

// Get FAQs from database
$faqs = $conn->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY category, id");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="page-header">
                <div>
                    <h2>Frequently Asked Questions</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active">FAQ</li>
                        </ol>
                    </nav>
                </div>
            </div>
            
            <!-- Search -->
            <div class="search-filter mb-4">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" id="faqSearch" placeholder="Search for questions...">
                </div>
            </div>
            
            <!-- FAQ Categories -->
            <div class="row">
                <div class="col-md-12">
                    <?php if ($faqs && $faqs->num_rows > 0): ?>
                        <?php while ($faq = $faqs->fetch_assoc()): ?>
                        <div class="faq-item">
                            <div class="faq-question">
                                <span><?php echo htmlspecialchars($faq['question']); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <!-- Default FAQs -->
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How do I register for the admission portal?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Click on the "Register" button on the homepage. Fill in your details including name, email, and phone number. You'll receive an OTP for verification. After verification, you can complete your profile and start applying.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What documents are required for application?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Typically, you need:
                            <br>- Passport size photo
                            <br>- SSC/10th marksheet
                            <br>- HSC/12th marksheet
                            <br>- Transfer Certificate
                            <br>- Category certificate (if applicable)
                            <br>- Entrance exam scorecard (if applicable)</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How can I track my application status?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Login to your account and navigate to "Track Application" in the student dashboard. You can view the current status, document verification status, and timeline of your application.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What is the application fee?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>The application fee varies by course and college. The fee details are displayed on the course selection page before applying. Payment can be made through debit card, credit card, or net banking.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Can I apply for multiple courses?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, you can apply for multiple courses. Each application is processed separately. However, note that each application requires payment of the respective application fee.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How do I reset my password?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Click on "Forgot Password" on the login page. Enter your registered email address. You'll receive a password reset link. Click on the link and create a new password.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What should I do if my document is rejected?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>If your document is rejected, you'll receive a notification with the reason. Log in to your account, go to "Upload Documents", and re-upload the correct document. Make sure the document is clear and in the required format (PDF/JPG).</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Is the application fee refundable?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Application fees are generally non-refundable. However, in exceptional cases such as technical errors or duplicate payments, you can contact the support team for assistance.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contact Support -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-headset me-2"></i>Still have questions?
                </div>
                <div class="card-body text-center">
                    <p>If you couldn't find the answer to your question, please contact our support team.</p>
                    <a href="help.php" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // FAQ Search
    document.getElementById('faqSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<?php include '../includes/footer.php'; ?>

