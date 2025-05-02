<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$sql = "SELECT * FROM standardparameter";
$stmt = $connect->prepare($sql);
$stmt->execute([]);
$parameter = $stmt->fetch(2);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once '../include/std_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-4">
                <div>
                    <h4 class="fw-bold text-primary mb-1">Configuration Settings</h4>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-gradient bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cogs fa-lg me-2"></i>
                        <h5 class="m-0 fw-bold">System Configuration</h5>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="alert alert-warning border-warning d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Important:</strong> Please ensure that all configurations are set correctly. Incorrect settings may lead to system malfunctions. Fields marked with <i class="fas fa-shield-alt text-danger"></i> require special attention.
                        </div>
                    </div>

                    <form id="system_form">
                        <div class="row g-4">
                            <!-- Basic Information Section -->
                            <div class="col-12 mb-3">
                                <h6 class="text-primary border-bottom pb-2">Basic Information</h6>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="hidden" class="form-control" id="recid" name="system[recid]" placeholder="recid" value="<?php echo $parameter['recid'];?>">
                                    <input type="text" class="form-control" id="title" name="system[title]" placeholder="Title" value="<?php echo $parameter['title'];?>">
                                    <label for="title">Title</label>
                                    <div class="form-text"><i class="fas fa-info-circle"></i> Used for system notifications</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="companyname" name="system[companyname]" placeholder="Company Name" value="<?php echo $parameter['companyname'];?>">
                                    <label for="companyname">Company Name</label>
                                    <div class="form-text"><i class="fas fa-info-circle"></i> Used for system notifications</div>
                                    <!-- <small class="text-muted">This will appear on all system-generated documents</small> -->
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="companyemail" name="system[companyemail]" placeholder="Company Email" value="<?php echo $parameter['companyemail'];?>">
                                    <label for="companyemail">Company Email</label>
                                    <div class="form-text"><i class="fas fa-info-circle"></i> Used for system notifications</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="companyaddress" name="system[companyaddress]" placeholder="Company Address" value="<?php echo $parameter['companyaddress'];?>">
                                    <label for="companyaddress">Company Address</label>
                                    <div class="form-text"><i class="fas fa-info-circle"></i> Used for system notifications</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="companycontactnum" name="system[companycontactnum]" placeholder="Company Contact No." value="<?php echo $parameter['companycontactnum'];?>">
                                    <label for="companycontactnum">Company Contact No.</label>
                                    <div class="form-text"><i class="fas fa-info-circle"></i> Used for system notifications</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="duedate" name="system[duedate]" placeholder="Enter due date" value="<?php echo $parameter['days_due_date']; ?>">
                                    <label for="duedate">Additional Days for Due Date</label>
                                    <small id="dueDateHelp" class="form-text">
                                        <i class="fas fa-info-circle"></i> Auto-calculated due date based on the start lease date and additional days.
                                    </small>
                                    <small id="exampleHelp" class="form-text">
                                        <i class="fas fa-info-circle"></i> Example: Start Lease: <strong>2024-01-01</strong> & Add Days: <strong>30</strong> → Due Date: <strong>2024-01-31</strong>
                                    </small>
                                </div>
                            </div>


                            <!-- Sensitive Configuration Section -->
                            <div class="col-12 mt-4">
                                <h6 class="text-danger border-bottom pb-2">
                                    <i class="fas fa-shield-alt"></i> Sensitive Configuration
                                </h6>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border-danger" id="online_logo" name="system[online_logo]" placeholder="Company Online Logo" value="<?php echo $parameter['online_logo'];?>">
                                    <label for="online_logo">Company Logo URL <i class="fas fa-shield-alt text-danger"></i></label>
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle"></i> Ensure the logo URL is accessible and secure (HTTPS)
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3 position-relative">
                                    <input type="password" class="form-control border-danger" id="gcash_api" name="system[gcash_api]" placeholder="GCash Secret API" value="<?php echo $parameter['gcash_api'];?>">
                                    <label for="gcash_api">G-Cash API Key <i class="fas fa-shield-alt text-danger"></i></label>
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" onclick="togglePassword('gcash_api')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle"></i> Modifying this may affect payment processing
                                    </div>
                                </div>
                            </div>

                            <!-- Email Configuration Section -->
                            <div class="col-12 mt-4">
                                <h6 class="text-danger border-bottom pb-2">
                                    <i class="fas fa-envelope"></i> Email Configuration
                                </h6>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control border-danger" id="gmail_username" name="system[gmail_username]" placeholder="g-mail username for email notification" value="<?php echo $parameter['gmail_username'];?>">
                                    <label for="gmail_username">GMAIL SMTP <i class="fas fa-shield-alt text-danger"></i></label>
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle"></i> Changes will affect system email notifications
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3 position-relative">
                                    <input type="password" class="form-control border-danger" id="gmail_password" name="system[gmail_password]" placeholder="g-mail smtp password" value="<?php echo $parameter['gmail_password'];?>">
                                    <label for="gmail_password">GMAIL SMTP Password <i class="fas fa-shield-alt text-danger"></i></label>
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" onclick="togglePassword('gmail_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle"></i> Changing this will affect email functionality
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between gap-2 mt-4 border-top pt-4">
                            <div class="text-danger">
                                <!-- <i class="fas fa-exclamation-triangle"></i> Changes to sensitive fields require admin approval -->
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>


<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        button.classList.remove('fa-eye');
        button.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        button.classList.remove('fa-eye-slash');
        button.classList.add('fa-eye');
    }
}

$(document).ready(function() {
    $('#system_form').on('submit', function(e) {
        e.preventDefault();
        
        const sensitiveFields = ['gcash_api', 'gmail_username', 'gmail_password'];
        let sensitiveChanged = false;
        
        sensitiveFields.forEach(field => {
            if ($(`#${field}`).val() !== '<?php echo $parameter[$field];?>') {
                sensitiveChanged = true;
            }
        });
        
        if (sensitiveChanged) {
            alertify.confirm('Warning: You have modified sensitive configuration fields. These changes may affect system functionality. Are you sure you want to proceed?',function(){
                var formData = new FormData($("#system_form")[0]);
                formData.append("event_action", "save_parameter"); 
                ajaxWithBlocking({
                    type: "POST",
                    url: "advanced_settings_ajax.php",
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if(response.bool){
                            alertify.alert(response.msg,function(){
                                window.location.reload();
                            })
                        }else{
                            alertify.alert(response.msg);
                        }
                    }
                });
            })
        }

    });

    $('.border-danger').focus(function() {
        const fieldName = $(this).attr('placeholder');
        $(this).popover({
            content: `Warning: Modifying ${fieldName} may affect system functionality. Proceed with caution.`,
            trigger: 'manual',
            placement: 'top'
        }).popover('show');
    }).blur(function() {
        $(this).popover('hide');
    });
});
</script>

<?php require_once '../include/std_footer.php'; ?>