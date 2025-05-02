<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();

$user_data = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$_SESSION['usr_cde']]);
// echo '<pre>';var_dump('hereee 1',$user_data['usr_havemedcondition']);die();

?>
<style>
.profile-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #eee;
}
.profile-section {
    margin-bottom: 1.5rem;
}
.info-label {
    font-weight: 600;
    color: #4e73df;
}
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        require_once '../include/std_sidebar_tenant.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h4>My Profile</h4>
                <!-- <button class="btn btn-primary btn-sm" id="editProfileBtn">
                    <i class="fas fa-edit"></i> Edit Profile
                </button> -->
            </div>

            <div class="row">
                <!-- Profile Details -->
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-body">
                                <div class="profile-section">
                                    <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
                            <form id="myprofile">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">First Name</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[firstName]" id="firstName" value="<?php echo $user_data['usr_fname'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Middle Name</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[middleName]" id="middleName" value="<?php echo $user_data['usr_mname'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Last Name</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[lastName]" id="lastName" value="<?php echo $user_data['usr_lname'] ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Sex</label>
                                                <select class="form-control form-control-sm" name="profile[sex]" id="gender" value="<?php echo $user_data['usr_sex'] ?>">
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Prefer not to say">Prefer not to say</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Contact No.</label>
                                                <input type="tel" class="form-control form-control-sm" name="profile[phone]" id="phone" value="<?php echo $user_data['usr_contactnum'] ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Email</label>
                                                <input type="email" class="form-control form-control-sm" name="profile[email]" id="email" value="<?php echo $user_data['usr_email'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Barangay</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[barangay]" id="barangay" value="<?php echo $user_data['usr_brgy'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Municipality</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[municipality]" id="municipality" value="<?php echo $user_data['usr_municipality'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Province</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[province]" id="province" value="<?php echo $user_data['usr_province'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Username</label>
                                                <input type="text" class="form-control form-control-sm disabled" name="profile[username]" id="username" value="<?php echo $user_data['usr_name'] ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div>
                                                <label class="info-label">Password</label>
                                                <input type="password" class="form-control form-control-sm" name="profile[password]" id="password" value="<?php echo $user_data['usr_pwd'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div>
                                                <label class="info-label">Confirm password</label>
                                                <input type="password" class="form-control form-control-sm" name="profile[confirmpass]" id="confirmpass" value="<?php echo $user_data['usr_pwd'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div>
                                                <label class="info-label"></label>
                                                <div class="d-flex justify-content-end">
                                                    <input type="button" class="btn btn-success btn-sm" id="changepass" value="Change password" onclick="changepassword()">
                                                    <input type="button" class="btn btn-secondary btn-sm ms-2" id="cancelpass" value="Cancel" onclick="cancel_pass()">
                                                    <input type="button" class="btn btn-primary btn-sm ms-2" id="savepass" value="Save password" onclick="save_pasword('<?php echo $user_data['usr_cde']; ?>')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <input type="checkbox" class="form-check-input" name="profile[haveMedCondition]" id="haveMedCondition" <?php echo $user_data['usr_havemedcondition'] == 1 ? 'checked' : ''; ?>>
                                                <label class="info-label">Have a Medical Condition</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Emergency Contact -->
                                <div class="profile-section">
                                    <h6 class="border-bottom pb-2 mb-3">Emergency Contact Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Full name</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[efullname]" id="efullname" value="<?php echo $user_data['eci_fullname'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Relationship</label>
                                                <input type="tel" class="form-control form-control-sm" name="profile[erelationship]" id="erelationship" value="<?php echo $user_data['eci_relationship'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Address</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[eaddress]" id="eaddress" value="<?php echo $user_data['eci_address'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Contact No.</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[econtactnum]" id="econtactnum" value="<?php echo $user_data['eci_contactnum'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Home No.</label>
                                                <input type="tel" class="form-control form-control-sm" name="profile[ehome]" id="ehome" value="<?php echo $user_data['eci_homenum'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="info-label">Work No.</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[ework]" id="ework" value="<?php echo $user_data['eci_worknum'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                                <div class="profile-section">
                                    <h6 class="border-bottom pb-2 ">Other Details</h6>
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <div>
                                                <label class="info-label">Deposit</label>
                                                <input type="text" class="form-control form-control-sm amount" name="profile[deposit]" id="deposit" value="<?php echo number_format($user_data['deposit'],2) ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div>
                                                <label class="info-label">Room No.</label>
                                                <input type="tel" class="form-control form-control-sm" name="profile[roomnum]" id="roomnum"  value="<?php echo $user_data['roomnum'] ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div>
                                                <label class="info-label">Bed Space No.</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[bedspacenum]" id="bedspacenum"  value="<?php echo $user_data['bedspacenum'] ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div>
                                                <label class="info-label">Start of Lease</label>
                                                <input type="text" class="form-control form-control-sm" name="profile[startlease]" id="startlease"  value="<?php echo $func->formateDate($user_data['startlease'],'m-d-Y') ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div>
                                                <label class="info-label">End of Lease</label>
                                                <input type="tel" class="form-control form-control-sm" name="profile[endlease]" id="endlease"  value="<?php echo $func->formateDate($user_data['endlease'],'m-d-Y') ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="d-flex justify-content-end pt-3 pb-2 mb-3 border-top">
                                    <input type="button" class="btn btn-primary btn-sm" id="editbtn" value="Edit Profile" onclick="editprofile()">
                                    <input type="button" class="btn btn-secondary btn-sm ms-2" id="cancelbtn" value="Cancel" onclick="cancel()">
                                    <input type="button" class="btn btn-primary btn-sm ms-2" id="savebtn" value="Save Changes" onclick="save_changes('<?php echo $user_data['usr_cde']; ?>')">
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>



<script>
$(document).ready(function() {
    $('#cancelbtn,#savebtn').addClass('hidden');
    $('#cancelpass,#savepass').addClass('hidden');
    $('#haveMedCondition').addClass('disabled',true);
    $('#deposit').addClass('disabled',true);
    $('#roomnum').addClass('disabled',true);
    $('#bedspacenum').addClass('disabled',true);
    $('#startlease').addClass('disabled',true);
    $('#endlease').addClass('disabled',true);
    $('#password').addClass('disabled',true).prop('readonly',true);
    $('#confirmpass').addClass('disabled',true).prop('readonly',true);
    $('#haveMedCondition').on('click', function(event) {
        event.preventDefault();
    });
    disabledInput(fieldsToDisable, true);
});

const editprofile = () =>{
    disabledInput(fieldsToDisable, !true);
    $('#editbtn').addClass('hidden');
    $('#cancelbtn, #savebtn').removeClass('hidden');
}



const save_changes = (usrcde) => {
    var formData = new FormData($("#myprofile")[0]);
    formData.append("event_action", "save_pasword");
    console.log(formData);
    formData.append("par", "save");
    formData.append("usrcde", usrcde);
    ajaxWithBlocking({
        type: "POST",
        url: "tenant_profile_ajax.php",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
            if(!response.bool){
                alertify.alert(response.msg);
            } else {
                alertify.alert(response.msg, function() {
                    window.location.reload();
                });
            }
        }
    });
}


const cancel = () => {
    disabledInput(fieldsToDisable, true);
    $('#cancelbtn, #savebtn').addClass('hidden');
    $('#editbtn').removeClass('hidden');
    $('#firstName').val("<?php echo $user_data['usr_fname']; ?>");
    $('#middleName').val("<?php echo $user_data['usr_mname']; ?>");
    $('#lastName').val("<?php echo $user_data['usr_lname']; ?>");
    $('#gender').val("<?php echo $user_data['usr_sex']; ?>");
    $('#phone').val("<?php echo $user_data['usr_contactnum']; ?>");
    $('#email').val("<?php echo $user_data['usr_email']; ?>");
    $('#barangay').val("<?php echo $user_data['usr_brgy']; ?>");
    $('#municipality').val("<?php echo $user_data['usr_municipality']; ?>");
    $('#province').val("<?php echo $user_data['usr_province']; ?>");
    $('#efullname').val("<?php echo $user_data['eci_fullname']; ?>");
    $('#erelationship').val("<?php echo $user_data['eci_relationship']; ?>");
    $('#eaddress').val("<?php echo $user_data['eci_address']; ?>");
    $('#econtactnum').val("<?php echo $user_data['eci_contactnum']; ?>");
    $('#ehome').val("<?php echo $user_data['eci_homenum']; ?>");
    $('#ework').val("<?php echo $user_data['eci_worknum']; ?>");

}

const changepassword = () => {
    $('#password').removeClass('disabled',true).prop('readonly',!true);
    $('#confirmpass').removeClass('disabled',true).prop('readonly',!true);
    $('#changepass').addClass('hidden');
    $('#cancelpass').removeClass('hidden');
    $('#savepass').removeClass('hidden');

    $('#password').val("");
    $('#confirmpass').val("");
}

const cancel_pass = () => {
    $('#changepass').removeClass('hidden');
    $('#cancelpass').addClass('hidden');
    $('#savepass').addClass('hidden');

    $('#password').addClass('disabled',true).prop('readonly',true);
    $('#confirmpass').addClass('disabled',true).prop('readonly',true);

    $('#password').val("<?php echo $user_data['usr_pwd']; ?>");
    $('#confirmpass').val("<?php echo $user_data['usr_pwd']; ?>");
}

const save_pasword = (usrcde) => {
    var pass = $('#password').val();
    var cpass = $('#confirmpass').val();

    if(pass == '' || pass == null || cpass == '' || cpass == null){
        alertify.alert("Password and Confirm password should not be blank.")
        return false;
    }

    if(pass != cpass){
        alertify.alert("Password and Confirm password does not match.")
        return false;
    }

    var formData = new FormData($("#myprofile")[0]);
    formData.append("event_action", "save_pasword");
    console.log(formData);
    formData.append("par", "changepass");
    formData.append("usrcde", usrcde);
    ajaxWithBlocking({
        type: "POST",
        url: "tenant_profile_ajax.php",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
            if(!response.bool){
                alertify.alert(response.msg);
            } else {
                alertify.alert(response.msg, function() {
                    window.location.reload();
                });
            }
        }
    });
}



const disabledInput = (fields, bool = true) => {
    fields.forEach(field => {
        $(`#${field}`).toggleClass('disabled', bool).prop('readonly', bool);
    });
};

const fieldsToDisable = [
    'firstName',
    'middleName',
    'lastName',
    'gender',
    'phone',
    'email',
    'barangay',
    'municipality',
    'province',
    // 'haveMedCondition',
    'efullname',
    'erelationship',
    'eaddress',
    'econtactnum',
    'ehome',
    'ework'
];

</script>

<?php 
require_once '../include/std_footer.php';
?>