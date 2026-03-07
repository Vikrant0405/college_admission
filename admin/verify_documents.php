<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Verify Documents - Admin';

// Get pending documents
$pending_docs = $conn->query("SELECT d.*, u.email, a.application_number 
    FROM documents d 
    JOIN users u ON d.user_id = u.id 
    JOIN applications a ON d.application_id = a.id 
    WHERE d.status = 'pending' 
    ORDER BY d.created_at DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Verify Documents</h1>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="documentsTable">
                            <thead>
                                <tr>
                                    <th>App No.</th>
                                    <th>Student</th>
                                    <th>Document Type</th>
                                    <th>File</th>
                                    <th>Uploaded</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($pending_docs && $pending_docs->num_rows > 0): ?>
                                    <?php while ($doc = $pending_docs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($doc['application_number']); ?></td>
                                        <td><?php echo htmlspecialchars($doc['email']); ?></td>
                                        <td><?php echo htmlspecialchars($doc['document_type']); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>uploads/documents/<?php echo $doc['file_name']; ?>" 
                                               target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($doc['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="verifyDoc(<?php echo $doc['id']; ?>, 1)">
                                                <i class="fas fa-check"></i> Verify
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="rejectDoc(<?php echo $doc['id']; ?>)">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No pending documents</td>
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

<script>
function verifyDoc(docId, status) {
    if(confirm('Verify this document?')) {
        window.location.href = 'verify_document_action.php?id=' + docId + '&action=verify';
    }
}

function rejectDoc(docId) {
    var reason = prompt('Enter rejection reason:');
    if (reason) {
        window.location.href = 'verify_document_action.php?id=' + docId + '&action=reject&reason=' + encodeURIComponent(reason);
    }
}
</script>

<?php include '../includes/footer.php'; ?>

