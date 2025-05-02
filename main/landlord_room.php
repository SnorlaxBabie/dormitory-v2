<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$pending_count = $func->FetchAll($connect,"roompendingrequest","count(*) as count");

$qry = "SELECT count(*) as count FROM roompendingrequest WHERE request_status <> 1";
$stmt = $connect->prepare($qry);
$stmt->execute();
$count_pendingrequest = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        require_once '../include/std_sidebar.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h4>Room Tracking</h4>
            </div>

            <!-- Recent Orders Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Room Tracking</h6>
                </div>
                <div class="card-body">
                    <!-- <input type="button" class="btn btn-sm btn-success mb-2" value="New Room" onclick="new_room()"> -->
                    <div class="position-relative d-inline-block">
                        <input type="button" class="btn btn-sm btn-primary mb-2" value="Pending Request" onclick="pending_request()">
                        <span id="pendingCount" class="badge bg-danger position-absolute top-0 start-100 translate-middle"><?php echo number_format($count_pendingrequest['count'],0) ?></span>
                    </div>
                    <div class="table-responsive">
                    <table id="tenant_table" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Room No.</th>
                                    <th>Capacity</th>
                                    <th>Current Tenants</th>
                                    <th>Status</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT * FROM roomfile";
                                    $stmt = $connect->prepare($sql);
                                    $stmt->execute([]);
                                    $xres_tenant = $stmt->fetchAll();

                                    foreach ($xres_tenant as $tenant_res) {
                                        if ($tenant_res['current_tenants'] >= $tenant_res['roomcapacity']) {
                                            $tenant_res['roomstat'] = 'occupied';
                                        } else {
                                            $tenant_res['roomstat'] = 'available';
                                        }

                                        echo "<tr>";
                                        echo "<td>{$tenant_res['roomnum']}</td>";
                                        echo "<td>{$tenant_res['roomcapacity']}</td>";
                                        echo "<td>{$tenant_res['current_tenants']}</td>";

                                        echo "<td>
                                                <span class='badge bg-" . 
                                                    ($tenant_res['roomstat'] == 'available' ? 'success' : 
                                                    ($tenant_res['roomstat'] == 'occupied' ? 'warning' : 
                                                    'info')) . 
                                                "'>".strtoupper($tenant_res['roomstat'])."</span>
                                            </td>";

                                        echo "<td class='text-center'> 
                                                <input type='button' class='btn btn-sm btn-primary' value='View' onclick='onView(\"{$tenant_res['roomid']}\")'>
                                            </td>";
                                        echo "</tr>";
                                    }

                                ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="pending_req" style="display:none;">
    <form id="pending_request_form">
        <table class="table table-bordered" id="pending_request">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Address</th>
                    <th>Gender</th>
                    <th>Room No.</th>
                    <th>Bed Space No.</th>
                    <th>Deposit</th>
                    <th>Start Lease</th>
                    <th>End Lease</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </form>
</div>
<div id="view_room" style="display: none;">
    <table class="table table-bordered" id="roomdata">
        <thead>
            <tr>
                <th>Tenant Name</th>
                <th>Sex</th>
                <th>Email</th>
                <th>Contact No.</th>
                <th>Address</th>
                <th>Deposit</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Start of Lease</th>
                <th>End of Lease</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>




<script>
    $(document).ready(function() {

        $('#tenant_table').DataTable({
            paging: true,
            searching: true,
            ordering: true,
        });

        $('.nav-link[data-bs-toggle="collapse"]').on('click', function(e) {
            e.preventDefault();
            const targetId = $(this).attr('href');
            const $targetElement = $(targetId);
            $('.collapse.show').not($targetElement).removeClass('show active').slideUp(300);
        });

        $('#sidebarLinks .nav-link').on('click', function() {
            $('#sidebarLinks .nav-link').removeClass('active');
            $(this).addClass('active');
        });

        $("#pending_req").dialog({
            autoOpen: false,
            modal: true,
            width: 1200,
            height: 500,
            title: "Tenants Room Pending Request",
            resizable: false,
        });

        $("#view_room").dialog({
            autoOpen: false,
            modal: true,
            width: 1500,
            height: 360,
            title: "Details",
            resizable: false,
            buttons: {
                Close: function() {
                    $(this).dialog("close");
                }
            }
        });

        $(document).on('change', '.action-select', function() {
            var action = $(this).val();
            var usrcde = $(this).data('user-id');
            var roonum = $(this).data('room-num');
            var roomid = $(this).data('roomid');
            var recid = $(this).data('recid');

            if (action) {
                var xparams = `event_action=approver&action=${action}&usrcde=${usrcde}&roomnum=${roonum}&roomid=${roomid}&recid=${recid}`;
                $.ajax({
                    type: "POST",
                    url: "landlord_room_ajax.php",
                    data: xparams,
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        if(response.bool){
                            fetch_pending_request();
                        }else{
                            alertify.alert(response.msg, function() {
                                fetch_pending_request();
                            });
                        }
                    }
                });
            }
        });


    });

    function fetch_pending_request(){
        ajaxWithBlocking({
            type: "post",
            url: "landlord_room_ajax.php",
            data: `event_action=view_pending_request`,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!response.bool){
                    alertify.alert(response.msg);
                }else{
                    $("#pending_request tbody").empty();
                    $("#pending_request tbody").append(response.html);
                }
            }
        });
    }

    function pending_request(){
        fetch_pending_request();
        $('#pending_req').dialog('open');
        $("#pending_req").dialog("option", "buttons", {
            "Close": function() {
                $(this).dialog("close");
                window.location.reload();
            }
        });
    }

    function onView(roomid){
        console.log(roomid);
        ajaxWithBlocking({
            type: "post",
            url: "landlord_room_ajax.php",
            data: `roomid=${roomid}&event_action=view_data`,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!response.bool){
                    alertify.alert(response.msg);
                }else{
                    $('#view_room').dialog('open');
                    $("#roomdata tbody").empty();
                    $("#roomdata tbody").append(response.html);
                }
            }
        })
    }

    function onDel(recid){
        alertify.confirm("Are you sure want to delete this data?",function (){
            console.log('Yes hakdog');
            var xparams = `event_action=del_data&recid=${recid}`
            ajaxWithBlocking({
                type: "post",
                url: "landlord_staff_ajax.php",
                data: xparams,
                dataType: 'json',
                success: function (response) {
                        if(response.bool){
                            alertify.alert(response.msg,function (){
                                window.location.reload();
                            })
                        }else{
                            alertify.alert(response.msg,function (){
                                window.location.reload();
                            })
                        }
                }
            })
        })
    }
</script>

<?php 
require_once '../include/std_footer.php';
?>