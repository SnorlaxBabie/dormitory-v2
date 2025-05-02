<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$sql = "SELECT * FROM tenantrequest ORDER BY created_at DESC";
$stmt = $connect->prepare($sql);
$stmt->execute([]);
$tenant_request = $stmt->fetchAll();
$staffList = $func->FetchAll($connect, "stafffile");
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .nav-tabs .nav-link {
        color: #495057; 
        background-color: #f8f9fa; 
        border: 1px solid #dee2e6;
        border-radius: 0.25rem 0.25rem 0 0;
        padding: 0.5rem 1rem;
    }

    .nav-tabs .nav-link.active {
        color: #fff;
        background-color: #007bff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .nav-tabs .nav-link:hover {
        color: #0056b3;
        background-color: #e2e6ea;
        border-color: #dee2e6;
    }

    #calendar {
        height: 650px;
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: .25rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Style for calendar days */
    .fc .fc-daygrid-day {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .fc-daygrid-day:hover {
        background-color: #f0f0f0;
        border-radius: .25rem;
    }

    .fc .fc-daygrid-event:hover {
        background-color: #0056b3; 
        transform: scale(1.05);
    }

    .fc-daygrid-event-harness {
        margin-bottom: 5px; 
    }


    .fc-popover-body {
        max-height: 400px;
        overflow-y: auto; 
        overflow-x: hidden;
        padding: 10px;
        background-color: #f9f9f9;
        border: 1px solid #dee2e6; 
        border-radius: .25rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); 
    }


    .fc-popover-header {
        font-weight: bold; 
        background-color: #007bff !important; 
        color: white;
        padding: 10px; 
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
    }

 
    .fc-daygrid-day.fc-day-today {
        background-color: #e6f7ff;
        border: 1px solid #007bff; 
        border-radius: .25rem;
    }

</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.4/index.global.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.4/index.global.min.js"></script>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        require_once '../include/std_sidebar.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h4>Request</h4>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="card-header">
                            <!-- <a href="#" class="btn btn-sm btn-primary float-end" onclick="printChart()"><i class="fas fa-print"></i> Print Report</a> -->
                            <h6 class="m-0 font-weight-bold text-primary">Maintenance & Repair Request</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="maintenanceRequestPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>




            <div class="card shadow mt-2">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="maintenanceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab" aria-controls="requests" aria-selected="true">
                                Table
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="board-tab" data-bs-toggle="tab" data-bs-target="#board" type="button" role="tab" aria-controls="board" aria-selected="false">
                                Board
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="divcalendar-tab" data-bs-toggle="tab" data-bs-target="#divcalendar" type="button" role="tab" aria-controls="divcalendar" aria-selected="false">
                                Calendar
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="maintenanceTabContent">
                        <!-- Tab 1: Table tracking see request all tenant -->
                        <div class="tab-pane fade show active" id="requests" role="tabpanel" aria-labelledby="requests-tab">
                            <div class="table-responsive">
                                <table id="tenantTable" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="15%">Name</th>
                                            <th width="8%">Room No.</th>
                                            <!-- <th width="1%">Description</th> -->
                                            <th width="5%">Priority</th>
                                            <th width="6%">Schedule</th>
                                            <th width="5%">Lease Status</th>
                                            <th width="5%">Assigned Staff</th>
                                            <th width="5%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($tenant_request as $request_list) {
                                            $chk = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$request_list['usr_cde']]);
                                            $lease = $func->LeaseStatus($chk['startlease'],$chk['endlease']) == "Active" ? "success" : ($func->LeaseStatus($chk['startlease'],$chk['endlease']) == "Pending" ? "warning" : "danger");
                                            echo "<tr>";
                                            echo "<td>".$chk['usr_fname'] .' '. $chk['usr_lname']."</td>";
                                            echo "<td>".$request_list['roomnum']."</td>";
                                            // echo "<td><textarea rows='2' cols='50' disabled readonly>".$request_list['description']."</textarea></td>";
                                            echo "<td>".$request_list['requestprio']."</td>";
                                            echo "<td>".$func->formateDate($request_list['requestsched'],'m-d-Y')."</td>";
                                            echo "<td><small><span class='badge bg-" . $lease . "'>" . ucfirst($func->LeaseStatus($chk['startlease'],$chk['endlease'])) . "</span></small></td>";

                                            echo "<td><select class='form-select form-select-sm' name='staffname' 
                                                        onchange='assigned_staff(\"{$request_list['requestid']}\", this.value)'>";
                                            echo "<option value=''></option>";
                                        
                                            foreach ($staffList as $staff) {
                                                $selected = ($staff['staffname'] == $request_list['staffname']) ? "selected" : "";
                                                echo "<option value='" . $staff['staffname'] . "' $selected>" . $staff['staffname'] . "</option>";
                                            }
                                            
                                            echo "</select></td>";

                                            echo "<td><select class='form-select form-select-sm' name='approval' 
                                                        onchange='onApproval(\"{$request_list['requestid']}\", this.value)'>";
                                            $statuses = ["Pending", "In Progress", "Completed"];
                                            
                                            foreach ($statuses as $status) {
                                                $selected = ($status == $request_list['reqstatus']) ? "selected" : "";
                                                echo "<option value='$status' $selected>$status</option>";
                                            }
                                            echo "</select></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Board -->
                        <div class="tab-pane fade" id="board" role="tabpanel" aria-labelledby="board-tab">
                            <div class="row px-3 py-3"></div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="divcalendar" role="tabpanel" aria-labelledby="divcalendar-tab">
                    <div id="calendar" class="mt-1"></div>
            </div>

        </main>

    </div>
</div>
<div id="viewrequest" style="display:none;">
    <div class="row mb-1">
        <div class="col-md-12">
            <label class="form-label"><small>Description</small></label>
            <textarea name="save[tenantrequest]" id="view_tenantrequest" class="form-control form-control-sm" readonly disabled></textarea>
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-md-6">
            <label class="form-label"><small>Priority</small></label>
            <select class="form-control form-control-sm" id="view_tenantpriority" name="save[priority]" readonly disabled>
                <option value=""></option>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label"><small>Schedule</small></label>
            <div class="input-wrapper">
                <input type="checkbox" id="view_curr_date" class="internal-checkbox" readonly disabled>
                <input type="text" class="form-control form-control-sm amount" id="view_pickdate" name="save[schedule]" readonly disabled>
            </div>
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-md-12">
            <label class="form-label"><small>Assigned Staff</small></label>
            <input type="text" class="form-control form-control-sm" id="view_tenantassignedstaff" name="save[tenantassignedstaff]" readonly disabled>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        refetch_piechart();
        $('#board-tab').on('click', function() {
            fetchBoardData();
        });
        $('#divcalendar-tab').on('click', function() {
            fetchCalendar();
        });
        $("#viewrequest").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            height: 400,
            title: "View Request Details",
            resizable: false,
        });
    });

    function onApproval(reqid,value){
        var xparams = `event_action=onapproval&reqid=${reqid}&value=${value}`
        ajaxWithBlocking({
            type: "POST",
            url: "landlord_trackrequest_ajax.php",
            data: xparams,
            dataType: "json",
            success: function(response) {
                refetch_piechart();
            }
        });
    }

    function assigned_staff(reqid,value){
        var xparams = `event_action=onassign&reqid=${reqid}&value=${value}`
        ajaxWithBlocking({
            type: "POST",
            url: "landlord_trackrequest_ajax.php",
            data: xparams,
            dataType: "json",
        });
    }

    let myPieChart;
    function refetch_piechart() {
        var xparams = `event_action=getdata`
        ajaxWithBlocking({
            type: "POST",
            url: "landlord_trackrequest_ajax.php",
            dataType: "json",
            data: xparams,
            success: function(response) {
                console.log('dsada',response.msg.pending);
                console.log('dsada',response.msg.in_progress);
                console.log('dsada',response.msg.completed);
                const ctx = document.getElementById('maintenanceRequestPieChart').getContext('2d');
                if (myPieChart) {
                    myPieChart.destroy();
                }
                const maintenanceData = {
                    labels: ['Pending', 'In Progress', 'Completed'],
                    datasets: [{
                        data: [response.msg.pending,response.msg.in_progress,response.msg.completed
                        ],
                        backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a'],
                        borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                        borderWidth: 2
                    }]
                };

                const total = maintenanceData.datasets[0].data.reduce((a, b) => a + b, 0);

                const config = {
                    type: 'pie',
                    data: maintenanceData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: 20
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 12
                                    },
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const value = data.datasets[0].data[i];
                                                const percentage = ((value / total) * 100).toFixed(1);
                                                return {
                                                    text: `${label}: ${percentage}% (${value})`,
                                                    fillStyle: data.datasets[0].backgroundColor[i],
                                                    strokeStyle: data.datasets[0].backgroundColor[i],
                                                    lineWidth: 0,
                                                    hidden: isNaN(value) || value === 0,
                                                    index: i
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            },
                            datalabels: {
                                display: true,
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 14
                                },
                                formatter: (value) => {
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${percentage}%`;
                                },
                                anchor: 'center',
                                align: 'center',
                                offset: 0
                            }
                        }
                    }
                };

                // new Chart(ctx, config);
                myPieChart = new Chart(ctx, config);
            }
        });
    }

    function fetchBoardData() {
        var xparams = `event_action=refetch_board`
        ajaxWithBlocking({
            url: 'landlord_trackrequest_ajax.php',
            type: 'POST',
            data: xparams,
            dataType: 'json',
            success: function(response) {
                $('#board .row').empty();
                response.forEach(function(request) {
                    var prioClass = request.requestprio == "Low" ? "bg-success" : request.requestprio == "Medium" ? "bg-warning" : "bg-danger";
                    var statusClass = request.reqstatus == "Pending" ? "bg-warning" : request.reqstatus == "In Progress" ? "bg-primary" : "bg-success";

                    var cardHtml = `
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Request No. ${request.requestid}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td><small>Name</small>:</td>
                                                <td><small>${request.name}</small></td>
                                            </tr>
                                            <tr>
                                                <td><small>Description</small>:</td>
                                                <td>
                                                    <textarea class="form-control" rows="3" readonly>${request.description}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><small>Priority</small>:</td>
                                                <td><small><span class="badge ${prioClass}">${request.requestprio}</span></small></td>
                                            </tr>
                                            <tr>
                                                <td><small>Status</small>:</td>
                                                <td><small><span class="badge ${statusClass}">${request.reqstatus}</span></small></td>
                                            </tr>
                                            <tr>
                                                <td><small>Schedule</small>:</td>
                                                <td><small>${request.schedule}</small></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>`;
                        
                    $('#board .row').append(cardHtml);
                });
            }
        });
    }

    function fetchCalendar(){
        var calendarEl = $('#calendar')[0];
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                // var usrcde = "<?php echo $usrcde;?>";
                // var xparams = `event_action=get_request&usrcde=${usrcde}`
                var xparams = `event_action=get_request`
                ajaxWithBlocking({
                    url: 'tenant_request_ajax.php',  
                    method: 'POST',
                    data: xparams,
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        if (data.bool && data.events) {
                            successCallback(data.events);
                        } else {
                            failureCallback();
                        }
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            },
            eventContent: function(arg) {
                let status = arg.event.extendedProps.status;
                // let backgroundColor = status === 'Pending' ? '#ff9800' : (status === 'Completed' ? '#007bff':'#b30c0c');
                let backgroundColor = status === 'Pending' ? '#ff9800' : (status === 'Completed' ? '#4caf50' : '#2196f3');
                let style = `background-color: ${backgroundColor}; color: white; padding: 2px 6px; border-radius: 4px;`;
                
                return { 
                    html: `<div style="${style}">${arg.event.title}</div>`
                };
            },
            dateClick: function(info) {
                var clickedDate = new Date(info.dateStr);
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                const month = String(clickedDate.getMonth() + 1).padStart(2, '0');
                const day = String(clickedDate.getDate()).padStart(2, '0'); 
                const year = String(clickedDate.getFullYear());
                const formattedDate = `${month}-${day}-${year}`;
                
                if (clickedDate < today) {
                    // do nothing
                    console.log('You cannot add on past dates.');
                } else {
                    // do nothing
                    // create_request('', 'calendar', formattedDate); // this function is for tenant only
                }
            },
            eventClick: function(info) {
                // var usrcde = "<?php echo $usrcde;?>";
                var usrcde = info.event.extendedProps.usrcde;
                var requestid = info.event.extendedProps.requestid;
                console.log(info,usrcde)
                var xparams = `event_action=view_request&usrcde=${usrcde}&reqid=${requestid}`
                ajaxWithBlocking({
                    type: "POST",
                    url: 'tenant_request_ajax.php', 
                    data: xparams,
                    dataType: "json",
                    success: function (response) {
                        console.log(response)
                        $('#view_tenantrequest').val(response.description);
                        $('#view_tenantpriority').val(response.priority)
                        $('#view_curr_date').prop('checked',true);
                        $('#view_pickdate').val(response.schedule);
                        $('#view_tenantassignedstaff').val(response.staff);
                        $('#viewrequest').dialog('open');
                        $("#viewrequest").dialog("option", "buttons", {
                            "Close": function() {
                                $(this).dialog("close");
                            }
                        });
                    }
                });
            },
            dayMaxEvents: 5
        });
        calendar.render();
    }
    
</script>
<?php 
require_once '../include/std_footer.php';
?>