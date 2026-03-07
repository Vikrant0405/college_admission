// College Admission System - Main JavaScript

// ===== Document Ready =====
$(document).ready(function() {
    // Initialize components
    initSidebar();
    initTooltips();
    initFileUpload();
    initDataTables();
    initDatePickers();
    initFaqAccordion();
    initConfirmDialogs();
});

// ===== Sidebar Toggle =====
function initSidebar() {
    $('#sidebar-toggle').on('click', function() {
        $('.sidebar').toggleClass('collapsed');
        $('.main-content').toggleClass('expanded');
    });
}

// ===== Tooltips =====
function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// ===== File Upload =====
function initFileUpload() {
    $('.document-upload-area').each(function() {
        var $uploadArea = $(this);
        var $fileInput = $uploadArea.find('input[type="file"]');
        
        $uploadArea.on('click', function() {
            $fileInput.trigger('click');
        });
        
        $uploadArea.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });
        
        $uploadArea.on('dragleave', function() {
            $(this).removeClass('dragover');
        });
        
        $uploadArea.on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            var files = e.originalEvent.dataTransfer.files;
            handleFileUpload(files, $uploadArea);
        });
        
        $fileInput.on('change', function() {
            handleFileUpload(this.files, $uploadArea);
        });
    });
}

function handleFileUpload(files, $uploadArea) {
    if (files.length > 0) {
        var file = files[0];
        var maxSize = 5 * 1024 * 1024; // 5MB
        var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        
        if (file.size > maxSize) {
            showAlert('File size exceeds 5MB limit', 'error');
            return;
        }
        
        if (!allowedTypes.includes(file.type)) {
            showAlert('Invalid file type. Allowed: JPG, PNG, PDF', 'error');
            return;
        }
        
        // Show preview
        var reader = new FileReader();
        reader.onload = function(e) {
            var isImage = file.type.includes('image');
            var previewHtml = '<div class="document-preview">';
            
            if (isImage) {
                previewHtml += '<img src="' + e.target.result + '" alt="Preview">';
            } else {
                previewHtml += '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAA7AAAAOwBeShxvQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAGdSURBVFiF7ZY9TsNAEIW/DRFCNFRUQhRuwAKs0FJQcgQrEAKKhoKSIxghIKCgoECCI1gCdmA72+PYgW/2Ye1EihQpUqT4L6JT3dNd3dN9O6KqGYvFYrFYLBaL/0BUnY6Ojo6Ojo6Ojn9NVacedXR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHT8a6LqdHR0dHR0dHR8A/wBOv4Z9d8aq4IAAAAASUVORK5CYII=" alt="PDF">';
            }
            
            previewHtml += '<div class="document-info">';
            previewHtml += '<div class="document-name">' + file.name + '</div>';
            previewHtml += '<div class="document-size">' + formatFileSize(file.size) + '</div>';
            previewHtml += '</div></div>';
            
            $uploadArea.html(previewHtml);
            $uploadArea.append('<input type="hidden" name="file_data" value="' + e.target.result + '">');
        };
        reader.readAsDataURL(file);
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// ===== Data Tables =====
function initDataTables() {
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }
}

// ===== Date Pickers =====
function initDatePickers() {
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        endDate: 'now',
        autoclose: true,
        todayHighlight: true
    });
}

// ===== FAQ Accordion =====
function initFaqAccordion() {
    $('.faq-question').on('click', function() {
        var $item = $(this).parent();
        var $answer = $item.find('.faq-answer');
        
        if ($item.hasClass('active')) {
            $item.removeClass('active');
            $answer.slideUp();
        } else {
            $('.faq-item').removeClass('active');
            $('.faq-answer').slideUp();
            $item.addClass('active');
            $answer.slideDown();
        }
    });
}

// ===== Confirm Dialogs =====
function initConfirmDialogs() {
    $('.confirm-delete').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });
}

// ===== Show Alert =====
function showAlert(message, type) {
    var alertClass = 'alert-info';
    if (type === 'success') alertClass = 'alert-success';
    if (type === 'error') alertClass = 'alert-danger';
    if (type === 'warning') alertClass = 'alert-warning';
    
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';
    
    $('.alert-container').html(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

// ===== Loading Spinner =====
function showLoading() {
    var loadingHtml = '<div class="loading-overlay">' +
        '<div class="loading-content">' +
        '<div class="spinner"></div>' +
        '<p class="mt-3">Loading...</p>' +
        '</div>' +
        '</div>';
    $('body').append(loadingHtml);
}

function hideLoading() {
    $('.loading-overlay').remove();
}

// ===== Form Validation =====
function validateForm(formId) {
    var isValid = true;
    var $form = $('#' + formId);
    
    $form.find('[required]').each(function() {
        if ($(this).val() === '' || $(this).val() === null) {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    return isValid;
}

// ===== Clear Form =====
function clearForm(formId) {
    $('#' + formId)[0].reset();
    $('#' + formId).find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
}

// ===== Number Only Input =====
$('.number-only').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// ===== Character Only Input =====
$('.character-only').on('input', function() {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
});

// ===== Email Validation =====
function isValidEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// ===== Phone Validation =====
function isValidPhone(phone) {
    var re = /^[0-9]{10,15}$/;
    return re.test(phone);
}

// ===== Refresh Page =====
function refreshPage() {
    location.reload();
}

// ===== Redirect =====
function redirectTo(url) {
    window.location.href = url;
}

// ===== Scroll to Top =====
function scrollToTop() {
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}

// ===== Export to Excel =====
function exportToExcel(tableId, filename) {
    var table = document.getElementById(tableId);
    var csv = [];
    var rows = table.querySelectorAll('tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        for (var j = 0; j < cols.length; j++) {
            row.push('"' + cols[j].innerText + '"');
        }
        csv.push(row.join(','));
    }
    
    var csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    var downloadLink = document.createElement('a');
    downloadLink.download = filename + '.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

// ===== Print Element =====
function printElement(elementId) {
    var content = document.getElementById(elementId).innerHTML;
    var printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<link rel="stylesheet" href="css/bootstrap.min.css">');
    printWindow.document.write('<link rel="stylesheet" href="css/style.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// ===== Countdown Timer =====
function startCountdown(elementId, targetDate) {
    var countdown = setInterval(function() {
        var now = new Date().getTime();
        var distance = targetDate - now;
        
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById(elementId).innerHTML = 
            days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's ';
        
        if (distance < 0) {
            clearInterval(countdown);
            document.getElementById(elementId).innerHTML = 'EXPIRED';
        }
    }, 1000);
}

