<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$usrcde = $_SESSION['usr_cde'];

// $sql = "SELECT 
//         MONTH(requests.requestsched) AS month,
//         COUNT(*) AS request_count 
//     FROM tenantrequest AS requests
//     GROUP BY MONTH(requests.requestsched)
//     ORDER BY month ASC";
// $stmt = $connect->prepare($sql);
// $stmt->execute();
// $requestsPerMonth = $stmt->fetchAll(PDO::FETCH_ASSOC);

// $months = [];
// $requestCounts = [];

// foreach ($requestsPerMonth as $data) {
//     $months[] = date("F", mktime(0, 0, 0, $data['month'], 10)); // Get month name
//     $requestCounts[] = $data['request_count'];
// }

?>


<style>
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

    /* SELECT2 */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #007bff;
        border-radius: 4px;
        height: 38px;
        padding: 5px 10px; 
        background-color: #fff; 
        transition: border-color 0.3s;
    }

    .select2-container--default .select2-selection--single:focus {
        border-color: #0056b3; 
        outline: none;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 25px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px; 
        right: 10px;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #007bff transparent;
        border-style: solid;
        border-width: 5px;
        content: '';
    }

    .select2-container--default .select2-results__option {
        padding: 10px; 
        color: #333;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff;
        color: white;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .select2-container .select2-selection--single {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        height: 35px !important;
        user-select: none;
        -webkit-user-select: none;
    }

    /* SELECT2 */
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.4/index.global.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.4/index.global.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        require_once '../include/std_sidebar_tenant.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h4>My Request</h5>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Request Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <input type="button" class="btn btn-sm btn-success px-3 mb-2" value="Create Request" onclick="create_request()">
                    <table id="requestTable" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="10%">Name</th>
                                    <th width="10%">Room No.</th>
                                    <th width="35%">Description</th>
                                    <th width="5%">Priority</th>
                                    <th width="5%">Schedule</th>
                                    <th width="10%">Assigned Staff</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM tenantrequest WHERE usr_cde = ?";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([$usrcde]);
                                $xres_request = $stmt->fetchAll();

                                foreach ($xres_request as $request) {
                                    $getuser = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$request['usr_cde']]);

                               
                                    $statusBadge = ($request['reqstatus'] === 'Pending') ? "<span class='badge bg-warning text-white'>".$request['reqstatus']."</span>" : (($request['reqstatus'] === 'Completed') ? "<span class='badge bg-success'>".$request['reqstatus']."</span>" : "<span class='badge bg-info'>".$request['reqstatus']."</span>");
                                    echo "<tr>";
                                    echo "<td>".$getuser['usr_fname']." ".$getuser['usr_lname']."</td>";
                                    echo "<td>".$request['roomnum']."</td>";
                                    echo "<td>
                                            <textarea class='form-control' rows='3' readonly>".$request['description']."</textarea>
                                          </td>";
                                    echo "<td>".$request['requestprio']."</td>";
                                    echo "<td>".$func->formateDate($request['requestsched'],'m-d-Y')."</td>";
                                    echo "<td>".$request['staffname']."</td>";
                                    // echo "<td>".$request['reqstatus']."</td>";
                                    echo "<td>".$statusBadge."</td>";
                                    echo "<td class='text-center'> 
                                            <input type='button' class='btn btn-sm btn-info' value='View' onclick='onView(\"{$request['requestid']}\")'>
                                            <input type=\"button\" class=\"btn btn-sm btn-primary\" value=\"Edit\" onclick=\"create_request('{$request['requestid']}', 'edit', '')\">
                                            <input type='button' class='btn btn-sm btn-danger' value='Delete' onclick='onDel(\"{$request['requestid']}\")'>
                                        </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
                <div id="calendar" class="mt-1"></div>
        </main>
    </div>
</div>
<div id="createrequest" style="display:none;">
    <form id="myrequest">
        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Description</small></label>
                <textarea name="save[tenantrequest]" id="tenantrequest" class="form-control form-control-sm"></textarea>
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-6">
                <label class="form-label"><small>Priority</small></label>
                <select class="form-control form-control-sm" id="tenantpriority" name="save[priority]">
                    <option value=""></option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label"><small>Schedule</small></label>
                <div class="input-wrapper">
                    <input type="checkbox" id="curr_date" class="internal-checkbox curr_date">
                    <input type="text" class="form-control form-control-sm amount pickdate" id="pickdate" name="save[schedule]" readonly>
                </div>
            </div>
        </div>
        <!-- <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Assigned Staff</small></label>
                <input type="text" class="form-control form-control-sm" id="tenantassignedstaff" name="save[tenantassignedstaff]">
            </div>
        </div> -->
    </form>
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
    $(document).ready(function() {
        $('#requestTable').DataTable({
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

        $('#tenantpriority').select2({
            minimumResultsForSearch: Infinity,
            placeholder: "Select Priority",
            allowClear: true
        });
        
        $("#createrequest").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            height: 320,
            title: "Create Request",
            resizable: false,
        });

        $("#viewrequest").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            height: 400,
            title: "View Request Details",
            resizable: false,
        });

        var calendarEl = $('#calendar')[0];
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(fetchInfo, successCallback) {
                var usrcde = "<?php echo $usrcde;?>";
                var xparams = `event_action=get_request&usrcde=${usrcde}`
                ajaxWithBlocking({
                    url: 'tenant_request_ajax.php',  
                    method: 'POST',
                    data: xparams,
                    dataType: 'json',
                    success: function(data) {
                        if (data.bool && data.events) {
                            successCallback(data.events);
                        }
                        //  else {
                        //     failureCallback();
                        // }
                    }
                    // error: function() {
                    //     failureCallback();
                    // }
                });
            },
            eventContent: function(arg) {
                // // Check the status in extendedProps
                let status = arg.event.extendedProps.status;
                // let backgroundColor = status === 'Pending' ? '#ff9800' : (status === 'Approved' ? '#007bff':'#b30c0c');
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
                    create_request('', 'calendar', formattedDate);
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
                // console.log('Event: ' + info.event.title + '\nDate: ' + info.event.start.toISOString());
            },
            dayMaxEvents: 5
        });
        calendar.render();





        // var months = <?php echo json_encode($months); ?>;
        // var requestCounts = <?php echo json_encode($requestCounts); ?>;

        // // Create the chart
        // var ctx = document.getElementById('requestsChart').getContext('2d');
        // var requestsChart = new Chart(ctx, {
        //     type: 'bar',
        //     data: {
        //         labels: months,
        //         datasets: [{
        //             label: 'Requests',
        //             data: requestCounts,
        //             backgroundColor: 'rgba(75, 192, 192, 0.6)', // Light blue color
        //             borderColor: 'rgba(75, 192, 192, 1)', // Darker border
        //             borderWidth: 1
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         scales: {
        //             y: {
        //                 beginAtZero: true,
        //                 ticks: {
        //                     stepSize: 1 // Adjust this based on your data
        //                 }
        //             }
        //         },
        //         plugins: {
        //             legend: {
        //                 display: true,
        //                 position: 'top'
        //             }
        //         }
        //     }
        // });

    });

    function onView(reqid){
        var usrcde = "<?php echo $usrcde;?>";
        var xparams = `event_action=view_request&usrcde=${usrcde}&reqid=${reqid}`
        $('#viewrequest').dialog('open');
        ajaxWithBlocking({
            type: "post",
            url: "tenant_request_ajax.php",
            data: xparams,
            dataType: "json",
            success: function (response) {
                $('#view_tenantrequest').val(response.description);
                $('#view_tenantpriority').val(response.priority);
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
        })
    }

    function onDel(reqid){
        alertify.confirm("Are you sure want to delete this data?",function (){
            console.log('Yes hakdog');
            var xparams = `event_action=del_data&reqid=${reqid}`
            ajaxWithBlocking({
                type: "post",
                url: "tenant_request_ajax.php",
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

    function create_request(reqid,xpar,date){
        $('#tenantrequest').val("");
        $('#tenantpriority').val("").trigger('change');
        $('#curr_date').prop('checked',!true);
        $('#pickdate').val("");
        $('#tenantassignedstaff').val("");
        
        var usrcde = "<?php echo $usrcde;?>";
        $('#pickdate').val('');
        $('#curr_date').prop('checked',!true);
        console.log(reqid,xpar,date);
        if(xpar == 'edit'){
            var xparams = `event_action=view_request&usrcde=${usrcde}&reqid=${reqid}`
            ajaxWithBlocking({
                type: "post",
                url: "tenant_request_ajax.php",
                data: xparams,
                dataType: "json",
                success: function (response) {
                    console.log(response.priority)
                    if(!response.bool){
                        alertify.alert(response.msg);
                    }else{
                        $('#tenantrequest').val(response.description);
                        $('#tenantpriority').val(response.priority).trigger('change');
                        $('#curr_date').prop('checked',true);
                        $('#pickdate').val(response.schedule);
                        $('#tenantassignedstaff').val(response.staff);
                    }
                }
            })
        }

        if(xpar == 'calendar'){
            $('#pickdate').val(date);
            $('#curr_date').prop('checked',true);
        }

        $('#createrequest').dialog('open');
        $("#createrequest").dialog("option", "buttons", {
            "Save": function() {
                var formData = new FormData($("#myrequest")[0]);
                formData.append("event_action", "save_data");
                formData.append("reqid", reqid);
                formData.append("par", xpar);
                formData.append("usrcde", usrcde);
                ajaxWithBlocking({
                    type: "post",
                    url: "tenant_request_ajax.php",
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