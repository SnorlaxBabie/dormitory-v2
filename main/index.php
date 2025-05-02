<?php 
    require_once '../include/std_header.php';

?>

<article class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
        <center>
            <img src="../assets/logo.png" alt="logo" style="max-width: 200px; width: 100%;">
            <h5 class="text-center">Dormitory Management System</h4>
            <h6 class="text-center mb-4">Palanginan, Iba, Zambales</h6>
        </center>
        
        <!-- Navigation Buttons -->
        <ul class="nav nav-tabs mb-3" id="loginTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="landlord-tab" data-bs-toggle="tab" data-bs-target="#landlord" type="button" role="tab" aria-controls="landlord" aria-selected="true">Landlord</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tenant-tab" data-bs-toggle="tab" data-bs-target="#tenant" type="button" role="tab" aria-controls="tenant" aria-selected="false">Tenant</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <form id="myTable">
            <div class="tab-content" id="loginTabContent">
                <!-- Landlord Login Form -->
                    <div class="tab-pane fade show active" id="landlord" role="tabpanel" aria-labelledby="landlord-tab">
                        <fieldset>
                            <legend>Login as Landlord</legend>
                            <div class="mb-3">
                            <small>Username</small>
                                <input type="text" class="form-control form-control-sm" id="landlord-username" name="landlord[username]">
                            </div>
                            <div class="mb-3">
                            <small>Password</small>
                                <input type="password" class="form-control form-control-sm" id="landlord-password" name="landlord[password]">
                            </div>
                            <input type="button" class="btn btn-sm btn-primary w-100" value="Login" onclick="Login('landlord')"">
                        </fieldset>
                    </div>
                <!-- Tenant Login Form -->
                    <div class="tab-pane fade" id="tenant" role="tabpanel" aria-labelledby="tenant-tab">
                        <fieldset>
                            <legend>Login as Tenant</legend>
                            <div class="mb-3">
                                <small>Username</small>
                                <input type="text" class="form-control form-control-sm" id="tenant-username" name="tenant[username]">
                            </div>
                            <div class="mb-3">
                                <small>Password</small>
                                <input type="password" class="form-control form-control-sm" id="tenant-password" name="tenant[password]">
                            </div>
                            <input type="button" class="btn btn-sm btn-primary w-100" value="Login" onclick="Login('tenant')"">
                        </fieldset>
                    </div>
                <div class="text-center mt-4">
                    <a href="main_register.php" class="btn btn-sm btn-outline-secondary w-100">Register</a>
                </div>
            </div>
        </form>

    </div>
    </article>


    <script>
        $(document).ready(function () {
            $('.nav-link').on('click', clearInputs);
        });

        function Login(xpar) {
            var formData = $(`#myTable`).serialize();
            var xparams = `${formData}&event_action=login_data&xpar=${xpar}`;
            ajaxWithBlocking({
                type: "post",
                url: "main_register_ajax.php",
                data: xparams,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if(!response.bool){
                        alertify.alert(response.msg);
                    }else{
                        if(response.usrlvl == 'admin'){
                            alertify.alert(response.msg, function() {
                                route('main');
                            })
                        }else{
                            alertify.alert(response.msg, function() {
                                route('tenant_main');
                            })
                        }
                       
                    }
                }
            });
        }

        function clearInputs() {
            $('#landlord-username').val('');
            $('#landlord-password').val('');

            $('#tenant-username').val('');
            $('#tenant-password').val('');
        }



    </script>
<?php 
require_once '../include/std_footer.php';
?>