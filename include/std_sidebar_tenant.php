<?php 
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
$func = new Funcshits();

$currentPage = basename($_SERVER['PHP_SELF']);

$usrlvl = $_SESSION['usr_lvl'] == 'ADMIN' ? 'Landlord' : 'Tenant';
$fullname = $_SESSION['full_name'];
$usrcde =  $_SESSION['usr_cde'];

$chk = $func->FetchSingle($connect,"userfile","WHERE usr_cde =?",[$usrcde]);

$display = "";
if($chk['usr_status'] == 0 && $chk['roomnum'] == null){
    $display = "hidden";
}
?>

<style>
    .sidebar {
        min-height: 100vh;
        /* background-color: #0062c4; */
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
        color: white;
    }
    .nav-link {
        color: #fff;
    }
    .nav-link:hover {
        background-color: #fff;
    }
    .active {
        background-color: #0c85ff;
    }
    .card-dashboard {
        transition: transform 0.2s;
    }
    .card-dashboard:hover {
        transform: translateY(-5px);
    }
</style>

<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="../assets/logo.png" style="width: 200px;"alt="Logo" class="rounded-circle">
            <!-- <img src="../main/upload/" alt="Logo" class="rounded-circle"> -->
                <h5 class="text-white">Dormitory System</h5>
                <!-- <h5 class="text-white">Iba, Zambales</h5> -->
                <!-- <h5 class="text-white"><?php echo $fullname ?></h5> -->
                <!-- <small class="text-white"><?php echo $usrlvl ?></small> -->
                <hr>
        </div>


        <ul class="nav flex-column" id="sidebarLinks">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'tenant_main.php') ? 'active' : ''; ?>" id="tenant_main" href="#" onclick="go_to('tenant_main')">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'tenant_profile.php') ? 'active' : ''; ?>" id="tenant_profile" href="#" onclick="go_to('tenant_profile')">
                    <i class="fas fa-user-friends me-2"></i>
                    My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'tenant_staff.php') ? 'active' : ''; ?>" id="tenant_staff" href="#" onclick="go_to('tenant_staff')">
                    <i class="fas fa-user-shield me-2"></i>
                    Staff
                </a>
            </li>
            <li class="nav-item <?php echo $display; ?>">
                <a class="nav-link <?php echo ($currentPage == 'tenant_request.php') ? 'active' : ''; ?>" id="tenant_request" href="#" onclick="go_to('tenant_request')">
                    <i class="fas fa-door-open me-2"></i>
                    Request
                </a>
            </li>
            <li class="nav-item <?php echo $display; ?>">
                <a class="nav-link <?php echo ($currentPage == 'tenant_paymentprocess.php') ? 'active' : ''; ?>" id="tenant_paymentprocess" href="#" onclick="go_to('tenant_paymentprocess')">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Payment processing
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'tenant_notice.php') ? 'active' : ''; ?>" id="tenant_notice" href="#" onclick="go_to('tenant_notice')">
                    <i class="fas fa-bell me-2"></i>
                    Send Notices
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="goLogout()">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>


<script>
    $(document).ready(function () {
    });

    function goLogout(){
        alertify.confirm('Are you sure you want to logout?',function() {
            var xparams = `event_action=go_logout`
            ajaxWithBlocking({
                type: "POST",
                url: "main_register_ajax.php",
                data: xparams,
                dataType: "json",
                success: function (response) {
                    if(response.bool){
                        alertify.alert(response.msg,function (){
                            route('index')
                        });
                    }
                }
            });
        },function() {
            // do nothing
        }).set('title', 'Logout Confirmation').set('labels', {ok:'Yes', cancel:'No'});


    }

    function go_to(xpar,e){
        switch(xpar){
            case 'tenant_main':
                route('tenant_main')
                break;
            case 'tenant_profile':
                route('tenant_profile')
                break;
            case 'tenant_staff':
                route('tenant_staff')
                break;
            case 'tenant_request':
                route('tenant_request')
                break;
            case 'tenant_paymentprocess':
                route('tenant_paymentprocess')
                break;
            case 'landlord_trackrequest':
                route('landlord_trasckrequest')
                break;
            case 'tenant_notice':
                route('tenant_notice')
                break;
            case 'landlord_roomsetup':
                route('landlord_rosomsetup')
                break;
            default:
                break;
        }
    }
</script>