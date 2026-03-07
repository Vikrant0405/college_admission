<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Upload Documents - Student';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_document'])) {
    $application_id = (int)$_POST['application_id'];
    $document_type = $_POST['document_type'];
    
    if ($application_id > 0 && !empty($_FILES['document']['name'])) {
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
        $file_ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_types)) {
            $error = 'Invalid file type. Only PDF, JPG, PNG allowed.';
        } elseif ($_FILES['document']['size'] > 5 * 1024 * 1024) {
            $error = 'File size must be less than 5MB.';
        } else {
            $file_name = time() . '_' . basename($_FILES['document']['name']);
            $target_dir = '../uploads/documents/';
            
            if (move_uploaded_file($_FILES['document']['tmp_name'], $target_dir . $file_name)) {
                $stmt = $conn->prepare("INSERT INTO documents (application_id, user_id, document_type, file_name, file_path, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                $stmt->bind_param("iisss", $application_id, $user_id, $document_type, $file_name, $target_dir . $file_name);
                
                if ($stmt->execute()) {
                    $success = 'Document uploaded successfully.';
                } else {
                    $error = 'Failed to save document.';
                }
            } else {
                $error = 'Failed to upload file.';
            }
        }
    } else {
        $error = 'Please select a file.';
    }
}

// Get user's applications
$applications = $conn->query("SELECT a.id, a.application_number, c.name as course_name 
    FROM applications a 
    JOIN courses c ON a.course_id = c.id 
    WHERE a.user_id = $user_id AND a.status != 'draft'");

// Get uploaded documents
$documents = $conn->query("SELECT d.*, a.application_number, c.name as course_name 
    FROM documents d 
    JOIN applications a ON d.application_id = a.id 
    JOIN courses c ON a.course_id = c.id 
    WHERE d.user_id = $user_id 
    ORDER BY d.created_at DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Upload Documents</h1>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Upload Form -->
            <div class="card mb-4">
                <div class="card-header">Upload New Document</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="upload_document" value="1">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Application</label>
                                <select name="application_id" class="form-select" required>
                                    <option value="">Select Application</option>
                                    <?php while ($app = $applications->fetch_assoc()): ?>
                                    <option value="<?php echo $app['id']; ?>">
                                        <?php echo $app['application_number'] . ' - ' . $app['course_name']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Document Type</label>
                                <select name="document_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="photo">Passport Photo</option>
                                    <option value="aadhar">Aadhar Card</option>
                                    <option value="marksheet_10">10th Marksheet</option>
                                    <option value="marksheet_12">12th Marksheet</option>
                                    <option value="transfer_certificate">Transfer Certificate</option>
                                    <option value="income_certificate">Income Certificate</option>
                                    <option value="caste_certificate">Caste Certificate</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">File (PDF/JPG/PNG - Max 5MB)</label>
                                <input type="file" name="document" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </form>
                </div>
            
            <!-- Uploaded Documents -->
            <div class="card">
                <div class="card-header">Uploaded Documents</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>App No.</th>
                                    <th>Document Type</th>
                                    <th>File</th>
                                    <th>Status</th>
                                    <th>Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($documents && $documents->num_rows > 0): ?>
                                    <?php while ($doc = $documents->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $doc['application_number']; ?></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $doc['document_type'])); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL . 'uploads/documents/' . $doc['file_name']; ?>" 
                                               target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $doc['status'] === 'verified' ? 'success' : ($doc['status'] === 'rejected' ? 'danger' : 'warning'); 
                                            ?>">
                                                <?php echo ucfirst($doc['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($doc['created_at'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No documents uploaded</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </main>
    </div>

<?php include '../includes/footer.php'; ?>
