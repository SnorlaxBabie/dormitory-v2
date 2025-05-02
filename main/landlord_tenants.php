<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_02.php';

$func = new Funcshits();


$year_qry = "SELECT 
                YEAR(startlease) AS YEAR,
                COUNT(*) AS total,
                SUM(CASE WHEN MONTH(startlease) = MONTH(CURDATE()) THEN 1 ELSE 0 END) AS NEW,
                SUM(CASE WHEN vacated = 1 THEN 1 ELSE 0 END) AS vacated,
                COUNT(*) - SUM(CASE WHEN vacated = 1 THEN 1 ELSE 0 END) AS active
            FROM 
                userfile
            WHERE 
                startlease IS NOT NULL
            GROUP BY 
                YEAR
            ORDER BY 
                YEAR";
$yr_stmt = $connect->prepare($year_qry);
$yr_stmt->execute();
$yearlyData1 = $yr_stmt->fetchAll(2);


$month_qry = "SELECT 
                MONTHNAME(startlease) AS MONTH,
                SUM(CASE WHEN MONTH(startlease) = MONTH(CURDATE()) THEN 1 ELSE 0 END) AS NEW,
                SUM(CASE WHEN vacated = 1 AND YEAR(vacated_date) = YEAR(CURDATE()) THEN 1 ELSE 0 END) AS vacated,
                COUNT(*) - SUM(CASE WHEN vacated = 1 THEN 1 ELSE 0 END) AS active
            FROM 
                userfile
            WHERE 
                YEAR(startlease) = YEAR(CURDATE())
            GROUP BY 
                MONTH(startlease)
            ORDER BY 
                MONTH(startlease)";
$m_stmt = $connect->prepare($month_qry);
$m_stmt->execute();
$month_data = $m_stmt->fetchAll(2);

$years          = array_column($yearlyData1, 'YEAR');
$totalTenants   = array_column($yearlyData1, 'total');
$newTenants     = array_column($yearlyData1, 'NEW');
$vacatedTenants = array_column($yearlyData1, 'vacated');
$activeLeases   = array_column($yearlyData1, 'active');

$months                = array_column($month_data, 'MONTH');
$newMonthlyTenants     = array_column($month_data, 'NEW');
$vacatedMonthlyTenants = array_column($month_data, 'vacated');
$activeMonthlyLeases   = array_column($month_data, 'active');
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        require_once '../include/std_sidebar.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h4>Tenants</h4>
            </div>
            <!-- Tenants Information Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="m-0 font-weight-bold text-primary">Tenants Information</h6>
                        </div>
                        <div class="col">
                            <a href="pdf_tenants.php" class="btn btn-sm btn-primary float-end" target="_blank"><i class="fas fa-print"></i> Print Report</a>
                            <!-- <div class="input-wrapper float-end mx-2" style="width:200px">
                                <input type="checkbox" id="curr_date" class="internal-checkbox">
                                <input type="text" class="form-control form-control-sm amount" id="pickdate" name="save[schedule]" readonly>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tenantTable" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <!-- <th>Age</th> -->
                                    <th>Gender</th>
                                    <th>Username</th>
                                    <!-- <th>Email Address</th> -->
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <!-- <th>Deposit</th>
                                    <th>Room No.</th>
                                    <th>Bedspace No.</th>
                                    <th>Start of Lease</th>
                                    <th>End of Lease</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM userfile WHERE usr_lvl ='USER'";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([]);
                                $xres_tenant = $stmt->fetchAll();

                                foreach ($xres_tenant as $tenant_list) {
                                    $usrstatus = $tenant_list['usr_status'] == 1 ? "Active" : "Inactive";
                                    echo "<tr>";
                                    echo "<td>".$tenant_list['usr_fname'] .' '. $tenant_list['usr_mname'] .' '. $tenant_list['usr_lname']."</td>";
                                    // echo "<td>".number_format($tenant_list['age'],0)."</td>";
                                    echo "<td>".$tenant_list['usr_sex']."</td>";
                                    echo "<td>".$tenant_list['usr_name']."</td>";
                                    echo "<td>".$tenant_list['usr_contactnum']."</td>";
                                    echo "<td>".$tenant_list['usr_brgy'].', '.$tenant_list['usr_municipality'].', '.$tenant_list['usr_province']."</td>";
                                    echo "<td>
                                            <span class='badge bg-" . ($usrstatus == 'Active' ? 'success' : 'danger') . "'>".$usrstatus."</span>
                                        </td>";
                                    // echo "<td>".number_format($tenant_list['deposit'],2)."</td>"; //! uncommend if needed, comment out ko masyadong crowded table
                                    // echo "<td>".$tenant_list['roomnum']."</td>"; //! uncommend if needed, comment out ko masyadong crowded table
                                    // echo "<td>".$tenant_list['bedspacenum']."</td>"; //! uncommend if needed, comment out ko masyadong crowded table
                                    // echo "<td>".$func->formateDate($tenant_list['startlease'],'m-d-Y')."</td>"; //! uncommend if needed, comment out ko masyadong crowded table
                                    // echo "<td>".$func->formateDate($tenant_list['endlease'],'m-d-Y')."</td>"; //! uncommend if needed, comment out ko masyadong crowded table
                                    echo "<td class='text-center'> 
                                            <input type='button' class='btn btn-sm btn-info' value='View' onclick='onView(\"{$tenant_list['usr_cde']}\")'>
                                            <input type='button' class='btn btn-sm btn-primary' value='Edit' onclick='onEdit(\"{$tenant_list['usr_cde']}\",\"edit\")'>
                                        </td>";
                                    echo "</tr>";

                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-2">
                    <a href="#" class="btn btn-sm btn-primary float-end" onclick="printChart()"><i class="fas fa-print"></i> Print Report</a>
                </div>
                <div class="col-lg-6">
                    <!-- Tenants Yearly Analysis -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">Tenants Yearly Analysis</h6>
                                </div>
                                <div class="col">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="tenantsYearlyChart" width="400" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                     <!-- Monthly Tenants Analysis -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">Monthly Tenants Analysis</h6>
                                </div>
                                <div class="col">
                                    <!-- <a href="pdf_monthly_tenant.php" class="btn btn-sm btn-primary float-end" target="_blank"><i class="fas fa-print"></i> Print Report</a> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="tenantsMonthlyChart" width="400" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Tenants Report -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="m-0 font-weight-bold text-primary">Tenants Report</h6>
                        </div>
                        <div class="col">
                            <a href="pdf_monthly_tenant.php" class="btn btn-sm btn-primary float-end" target="_blank"><i class="fas fa-print"></i> Print Report</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>New Tenants</th>
                                <th>Vacated Tenants</th>
                                <th>Active Leases</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($month_data as $data): ?>
                                <tr>
                                    <td><?php echo $data['MONTH']; ?></td>
                                    <td><?php echo number_format($data['NEW']); ?></td>
                                    <td><?php echo number_format($data['vacated']); ?></td>
                                    <td><?php echo number_format($data['active']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
           
        </main>
    </div>
</div>
<div id="edit_tenant" style="display:none;">
    <form id="edit_tenant_form">
        <div class="row mb-1">
            <div class="col-md-4">
                <label class="form-label"><small>Name</small></label>
                <input type="text" class="form-control form-control-sm" id="edit_name" name="name" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Gender</small></label>
                <!-- <input type="text" class="form-control form-control-sm" id="gender" name="gender"> -->
                <select class="form-control form-control-sm" id="edit_gender" name="gender" readonly>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Prefer no to say">Prefer no to say</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Username</small></label>
                    <input type="text" class="form-control form-control-sm" id="edit_username" name="username" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Contact No.</small></label>
                <input type="text" class="form-control form-control-sm" id="edit_contactnum" name="contactnum" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Address</small></label>
                <input type="text" class="form-control form-control-sm" id="edit_address" name="address" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Status</small></label>
                <input type="text" class="form-control form-control-sm" id="edit_status" name="status" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Deposit</small></label>
                <input type="text" class="form-control form-control-sm amount" id="edit_deposit" name="deposit" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Room No.</small></label>
                <input type="text" class="form-control form-control-sm" id="edit_roomnum" name="roomnum" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Bedspace</small></label>
                <input type="text" class="form-control form-control-sm" id="edit_bedspace" name="bedspace" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Start Lease</small></label>
                <!-- <input type="date" class="form-control form-control-sm" id="edit_endlease" name="startlease"> -->
                <div class="input-wrapper">
                    <!-- <input type="checkbox" id="curr_date" class="internal-checkbox curr_date"> -->
                    <input type="text" class="form-control form-control-sm pickdate" id="edit_startlease" name="edt_startlease" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>End Lease</small></label>
                <!-- <input type="date" class="form-control form-control-sm" id="edit_endlease" name="endlease"> -->
                <div class="input-wrapper">
                    <!-- <input type="checkbox" id="curr_date" class="internal-checkbox curr_date"> -->
                    <input type="text" class="form-control form-control-sm pickdate" id="edit_endlease" name="edt_endlease" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Vacated</small></label>
                <div class="input-wrapper">
                    <input type="checkbox" id="curr_vacated" class="internal-checkbox curr_date">
                    <input type="text" class="form-control form-control-sm pickdate" id="edit_vacated" name="vacated" readonly>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="view_tenant" style="display:none;">
        <div class="row mb-1">
            <div class="col-md-4">
                <label class="form-label"><small>Name</small></label>
                <input type="text" class="form-control form-control-sm" id="name" name="name" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Gender</small></label>
                <!-- <input type="text" class="form-control form-control-sm" id="gender" name="gender" readonly> -->
                <select class="form-control form-control-sm" id="gender" name="gender" readonly disabled>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Prefer no to say">Prefer no to say</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Username</small></label>
                    <input type="text" class="form-control form-control-sm" id="username" name="username" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Contact No.</small></label>
                <input type="text" class="form-control form-control-sm" id="contactnum" name="contactnum" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Address</small></label>
                <input type="text" class="form-control form-control-sm" id="address" name="address" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Status</small></label>
                <input type="text" class="form-control form-control-sm" id="status" name="status" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Deposit</small></label>
                <input type="text" class="form-control form-control-sm amount" id="deposit" name="deposit" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Room No.</small></label>
                <input type="text" class="form-control form-control-sm" id="roomnum" name="roomnum" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Bedspace</small></label>
                <input type="text" class="form-control form-control-sm" id="bedspace" name="bedspace" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Start Lease</small></label>
                <!-- <input type="text" class="form-control form-control-sm" id="startlease" name="startlease" readonly> -->
                <div class="input-wrapper">
                    <!-- <input type="checkbox" id="curr_date" class="internal-checkbox curr_date" disabled> -->
                    <input type="text" class="form-control form-control-sm pickdate" id="startlease" name="startlease"disabled readonly>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>End Lease</small></label>
                <!-- <input type="text" class="form-control form-control-sm" id="endlease" name="endlease" readonly> -->
                <div class="input-wrapper">
                    <!-- <input type="checkbox" id="curr_date" class="internal-checkbox curr_date" disabled> -->
                    <input type="text" class="form-control form-control-sm pickdate" id="endlease" name="endlease"disabled readonly>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label"><small>Vacated</small></label>
                <div class="input-wrapper">
                    <input type="checkbox" id="view_vacated" class="internal-checkbox curr_date" disabled readonly>
                    <input type="text" class="form-control form-control-sm pickdate" id="vacated_date" name="vacated" disabled readonly>
                </div>
            </div>
        </div>

</div>
<script>
    $(document).ready(function () {
        const $picked_date = $('#edit_vacated');
        const $curr_date = $('#curr_vacated');

        $picked_date.change(function() {
            if ($picked_date.val()) { 
                $curr_date.prop('checked', true);
            } else {
                $curr_date.prop('checked', false);
            }
        });

        $curr_date.change(function() {
            if (this.checked) {
                const today = new Date();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const year = today.getFullYear();
                const formattedDate = `${month}-${day}-${year}`;
                $picked_date.val(formattedDate);
                $picked_date.prop('readonly', true);
            } else {
                $picked_date.val('');
                $picked_date.prop('readonly', false);
            }
        });

        $("#edit_startlease").datepicker({
            dateFormat: "mm-dd-yy",
            minDate: 0 
        });

        $("#edit_endlease").datepicker({
            dateFormat: "mm-dd-yy",
            minDate: 0 
        });
        $('#tenantTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
        });

        $("#edit_tenant").dialog({
            autoOpen: false,
            modal: true,
            width: 930,
            height: 430,
            title: "Edit Details",
            resizable: false,
        });
        $("#view_tenant").dialog({
            autoOpen: false,
            modal: true,
            width: 930,
            height: 430,
            title: "Tenant Details",
            resizable: false,
        });

        var ctxYearly = document.getElementById('tenantsYearlyChart').getContext('2d');
        var tenantsYearlyChart = new Chart(ctxYearly, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($years); ?>,
                datasets: [
                    {
                        label: 'Total Tenants',
                        data: <?php echo json_encode($totalTenants); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: false
                    },
                    {
                        label: 'New Tenants',
                        data: <?php echo json_encode($newTenants); ?>,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        fill: false
                    },
                    {
                        label: 'Vacated Tenants',
                        data: <?php echo json_encode($vacatedTenants); ?>,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 2,
                        fill: false
                    },
                    {
                        label: 'Active Leases',
                        data: <?php echo json_encode($activeLeases); ?>,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 2,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Count'
                        }
                    }
                }
            }
        });

        var ctxMonthly = document.getElementById('tenantsMonthlyChart').getContext('2d');
        var tenantsMonthlyChart = new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: 'New Tenants',
                        data: <?php echo json_encode($newMonthlyTenants); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    },
                    {
                        label: 'Vacated Tenants',
                        data: <?php echo json_encode($vacatedMonthlyTenants); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    },
                    {
                        label: 'Active Leases',
                        data: <?php echo json_encode($activeMonthlyLeases); ?>,
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Count'
                        }
                    }
                }
            }
        });
    });


function printChart() {
    var canvas1 = document.getElementById('tenantsYearlyChart');
    var canvas2 = document.getElementById('tenantsMonthlyChart');
    var imgData1 = canvas1.toDataURL('image/png');
    var imgData2 = canvas2.toDataURL('image/png');

    fetch('pdf_yrly_analysis.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            image1: imgData1, 
            image2: imgData2 
        })
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        window.open(url, '_blank');
        window.URL.revokeObjectURL(url);
    })
    .catch(err => console.error('erro generating PDF:', err));
}

function onView(usrcde){
    $('#view_tenant').dialog('open');
    $("#name").val('');
    $("#gender").val('');
    $("#username").val('');
    $("#contactnum").val('');
    $("#address").val('');
    $("#status").val('');
    $("#deposit").val('');
    $("#roomnum").val('');
    $("#bedspace").val('');
    $("#startlease").val('');
    $("#endlease").val('');
    $("#vacated_date").val('');
    var xparams = `event_action=view_tenant&usrcde=${usrcde}`
    ajaxWithBlocking({
        type: "POST",
        url: "landlord_tenant_ajax.php",
        data: xparams,
        dataType: "json",
        success: function (response) {
            if(response.bool){
                $("#name").val(response.data.name);
                $("#gender").val(response.data.gender);
                $("#username").val(response.data.username);
                $("#contactnum").val(response.data.contactnum);
                $("#address").val(response.data.address);
                $("#status").val(response.data.status);
                $("#deposit").val('₱'+response.data.deposit);
                $("#roomnum").val(response.data.roomnum);
                $("#bedspace").val(response.data.bedspacenum);
                $("#startlease").val(response.data.startlease);
                $("#endlease").val(response.data.endlease);
                $("#vacated_date").val(response.data.vacated_date);
            }
            if(response.data.vacated_date != 'Invalid date'){
                $('#view_vacated').prop('checked', true);
            }else{
                $("#vacated_date").val('');
                $('#view_vacated').prop('checked', !true);
            }
        }
    });

    $("#view_tenant").dialog("option", "buttons", {
        "Close": function() {
            $(this).dialog("close");
        }
    });
}

function onEdit(usrcde,par){
    $("#edit_name").val('');
    $("#edit_gender").val('');
    $("#edit_username").val('');
    $("#edit_contactnum").val('');
    $("#edit_address").val('');
    $("#edit_status").val('');
    $("#edit_deposit").val('');
    $("#edit_roomnum").val('');
    $("#edit_bedspace").val('');
    $("#edit_startlease").val('');
    $("#edit_endlease").val('');
    $("#edit_vacated").val('');

    if(par == 'edit'){
        var xparams = `event_action=view_tenant&usrcde=${usrcde}`
        ajaxWithBlocking({
            type: "POST",
            url: "landlord_tenant_ajax.php",
            data: xparams,
            dataType: "json",
            success: function (response) {
                if(response.bool){
                    $("#edit_name").val(response.data.name);
                    $("#edit_gender").val(response.data.gender);
                    $("#edit_username").val(response.data.username);
                    $("#edit_contactnum").val(response.data.contactnum);
                    $("#edit_address").val(response.data.address);
                    $("#edit_status").val(response.data.status);
                    $("#edit_deposit").val(response.data.deposit);
                    $("#edit_roomnum").val(response.data.roomnum);
                    $("#edit_bedspace").val(response.data.bedspacenum);
                    $("#edit_startlease").val(response.data.startlease);
                    $("#edit_endlease").val(response.data.endlease);
                    $("#edit_vacated").val(response.data.vacated_date);
                }
                if(response.data.vacated_date != 'Invalid date'){
                    $('#curr_vacated').prop('checked', true);
                }else{
                    $("#edit_vacated").val('');
                    $('#curr_vacated').prop('checked', !true);
                }
            }
        });
    }

    $('#edit_tenant').dialog('open');
    $("#edit_tenant").dialog("option", "buttons", {
        "Save": function() {
            var formData = new FormData($("#edit_tenant_form")[0]);
            formData.append("event_action", "save_data");
            formData.append("par", par);
            formData.append("usrcde", usrcde);
            ajaxWithBlocking({
                type: "post",
                url: "landlord_tenant_ajax.php",
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
        },
        "Close": function() {
            $(this).dialog("close");
        }
    });
}

</script>

<?php 
require_once '../include/std_footer.php';
?>