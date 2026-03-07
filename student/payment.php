 <?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Payments - Student';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_payment'])) {
    $application_id = (int)$_POST['application_id'];
    $payment_method = $_POST['payment_method'];
    
    // Get application details
    $app = $conn->query("SELECT a.*, c.fees FROM applications a JOIN courses c ON a.course_id = c.id WHERE a.id = $application_id AND a.user_id = $user_id")->fetch_assoc();
    
    if ($app) {
        $transaction_id = 'TXN' . time() . rand(1000, 9999);
        $amount = $app['fees'];
        
        // Insert payment record
        $stmt = $conn->prepare("INSERT INTO payments (application_id, user_id, amount, payment_method, transaction_id, payment_status) VALUES (?, ?, ?, ?, ?, 'completed')");
        $stmt->bind_param("iidss", $application_id, $user_id, $amount, $payment_method, $transaction_id);
        
        if ($stmt->execute()) {
            // Update application
            $conn->query("UPDATE applications SET is_payment_done = 1, payment_amount = $amount, payment_transaction_id = '$transaction_id' WHERE id = $application_id");
            $success = 'Payment successful! Transaction ID: ' . $transaction_id;
        } else {
            $error = 'Payment failed. Please try again.';
        }
    } else {
        $error = 'Invalid application.';
    }
}

// Get user's pending payments
$pending_apps = $conn->query("SELECT a.id, a.application_number, c.name as course_name, c.fees 
    FROM applications a 
    JOIN courses c ON a.course_id = c.id 
    WHERE a.user_id = $user_id AND a.status != 'draft' AND a.is_payment_done = 0");

// Get payment history
$payments = $conn->query("SELECT p.*, a.application_number, c.name as course_name 
    FROM payments p 
    JOIN applications a ON p.application_id = a.id 
    JOIN courses c ON a.course_id = c.id 
    WHERE p.user_id = $user_id 
    ORDER BY p.created_at DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Payments</h1>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Make Payment -->
            <?php if ($pending_apps && $pending_apps->num_rows > 0): ?>
            <div class="card mb-4">
                <div class="card-header">Make Payment</div>
                <div class="card-body">
                    <?php while ($app = $pending_apps->fetch_assoc()): ?>
                    <div class="border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5><?php echo $app['application_number']; ?></h5>
                                <p class="mb-0"><?php echo $app['course_name']; ?></p>
                            </div>
                            <div class="text-end">
                                <h4 class="text-primary">₹<?php echo number_format($app['fees'], 2); ?></h4>
                                <form method="POST" class="mt-2">
                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                    <input type="hidden" name="make_payment" value="1">
                                    <select name="payment_method" class="form-select form-select-sm d-inline-block w-auto" required>
                                        <option value="">Select Method</option>
                                        <option value="upi">UPI</option>
                                        <option value="card">Debit/Credit Card</option>
                                        <option value="netbanking">Net Banking</option>
                                    </select>
                                    <button type="submit" class="btn btn-success btn-sm">Pay Now</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Payment History -->
            <div class="card">
                <div class="card-header">Payment History</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Application</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($payments && $payments->num_rows > 0): ?>
                                    <?php while ($pay = $payments->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $pay['transaction_id']; ?></td>
                                        <td><?php echo $pay['application_number']; ?></td>
                                        <td><?php echo $pay['course_name']; ?></td>
                                        <td>₹<?php echo number_format($pay['amount'], 2); ?></td>
                                        <td><?php echo ucfirst($pay['payment_method']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $pay['payment_status'] === 'completed' ? 'success' : ($pay['payment_status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst($pay['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($pay['created_at'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No payment history</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

