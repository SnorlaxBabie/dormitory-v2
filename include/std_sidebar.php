<?php 
require_once '../config/sessions.php';
$currentPage = basename($_SERVER['PHP_SELF']);

$usrlvl = $_SESSION['usr_lvl'] == 'ADMIN' ? 'Landlord' : 'Tenant';
$fullname = $_SESSION['full_name'];
$usrcde =  $_SESSION['usr_cde'];

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
                <a class="nav-link <?php echo ($currentPage == 'main.php') ? 'active' : ''; ?>" id="main" href="#" onclick="go_to('main')">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'landlord_tenants.php') ? 'active' : ''; ?>" id="mytenant" href="#" onclick="go_to('landlord_tenants')">
                    <i class="fas fa-user-friends me-2"></i>
                    My Tenants
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'landlord_staff.php') ? 'active' : ''; ?>" id="mystaff" href="#" onclick="go_to('landlord_staff')">
                    <i class="fas fa-user-shield me-2"></i>
                    My Staff
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'landlord_.php') ? 'active' : ''; ?>" id="room" href="#" onclick="go_to('landlord_room')">
                    <i class="fas fa-door-open me-2"></i>
                    Rooms
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'landlord_notice.php') ? 'active' : ''; ?>" id="notice" href="#" onclick="go_to('landlord_notice')">
                    <i class="fas fa-bell me-2"></i>
                    Send Notices
                </a>
            </li>
            <!-- Tracking Dropdown -->
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" data-bs-toggle="collapse" href="#tracking" aria-expanded="false">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Tracking
                    <i class="fas fa-chevron-right ms-auto" id="arrowIconTracking"></i>
                </a>
                <div class="collapse" id="tracking">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'landlord_trackrequest.php') ? 'active' : ''; ?>" id="trackrequest" href="#" onclick="go_to('landlord_trackrequest')">
                                <i class="fas fa-paper-plane me-2"></i>
                                Request Tracking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'landlord_trackpayment.php') ? 'active' : ''; ?>" id="trackpayment" href="#" onclick="go_to('landlord_trackpayment')">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Payment Tracking
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- Room Setup -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'landlord_roomsetup.php') ? 'active' : ''; ?>" id="notice" href="#" onclick="go_to('landlord_roomsetup')">
                    <i class="fas fa-bed"></i>
                    Room Setup
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'payment_history.php') ? 'active' : ''; ?>" id="notice" href="#" onclick="go_to('payment_history')">
                    <i class="fas fa-history"></i>
                    Payment History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'announcement.php') ? 'active' : ''; ?>" id="notice" href="#" onclick="go_to('announcement')">
                    <i class="fas fa-bullhorn card-title-icon"></i>
                    Announcements
                </a>
            </li>

            <!-- <li class="nav-item">
                <a class="nav-link d-flex align-items-center" data-bs-toggle="collapse" href="#manageDropdown" aria-expanded="false">
                    <i class="fas fa-cog me-2"></i>
                    Advanced Settings
                    <i class="fas fa-chevron-right ms-auto" id="arrowIconManage"></i>
                </a>
                <div class="collapse" id="manageDropdown">
                    <ul class="nav flex-column ms-3"> -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage == 'advanced_settings.php') ? 'active' : ''; ?>" id="advanced_settings" href="advanced_settings.php" onclick="go_to('advanced_settings')">
                                <i class="fas fa-cog me-2"></i>
                                Advanced Settings
                            </a>
                        </li>
                    <!-- </ul>
                </div>
            </li> -->

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
        $('#manageDropdown').on('show.bs.collapse', function () {
            $('#arrowIconManage').removeClass('fa-chevron-right').addClass('fa-chevron-down'); 
        });

        $('#manageDropdown').on('hide.bs.collapse', function () {
            $('#arrowIconManage').removeClass('fa-chevron-down').addClass('fa-chevron-right'); 
        });

        $('#tracking').on('show.bs.collapse', function () {
            $('#arrowIconTracking').removeClass('fa-chevron-right').addClass('fa-chevron-down'); 
        });

        $('#tracking').on('hide.bs.collapse', function () {
            $('#arrowIconTracking').removeClass('fa-chevron-down').addClass('fa-chevron-right'); 
        });
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
            case 'main':
                route('main')
                break;
            case 'landlord_tenants':
                route('landlord_tenants')
                break;
            case 'landlord_staff':
                route('landlord_staff')
                break;
            case 'landlord_room':
                route('landlord_room')
                break;
            case 'landlord_notice':
                route('landlord_notice')
                break;
            case 'landlord_trackrequest':
                route('landlord_trackrequest')
                break;
            case 'landlord_trackpayment':
                route('landlord_trackpayment')
                break;
            case 'landlord_roomsetup':
                route('landlord_roomsetup')
                break;
            case 'advanced_settings':
                route('advanced_settings')
                break;
            case 'payment_history':
                route('payment_history')
                break;
            case 'announcement':
                route('announcement')
                break;
            default:
                break;
        }
    }
</script>