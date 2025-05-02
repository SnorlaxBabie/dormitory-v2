<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
$func = new Funcshits();
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
                <h4>Announcement</h4>
            </div>

            <!-- Recent Orders Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Announcement</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <input type="button" class="btn btn-sm btn-success px-3 py-0 mb-2" value="Add" onclick="add_announcement()">
                    <!-- <table class="table table-bordered" width="100%" cellspacing="0"> -->
                    <table id="staffTable" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="20%">Subject</th>
                                    <th width="40%">Description</th>
                                    <th width="10%">Start</th>
                                    <th width="10%">End</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM announcements";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([]);
                                $announcement = $stmt->fetchAll();

                                foreach ($announcement as $res_announcement) {
                                    echo "<tr>";
                                    echo "<td>".$res_announcement['title']."</td>";
                                    echo "<td><textarea rows='3' cols='100' readonly disabled>".$res_announcement['content']."</textarea></td>";
                                    echo "<td>".$func->formatDate4($res_announcement['start_date'])."</td>";
                                    echo "<td>".$func->formatDate4($res_announcement['end_date'])."</td>";
                                    echo "<td class='text-center'> 
                                        <input type='button' class='btn btn-sm btn-info' value='View' onclick='onView({$res_announcement['recid']})'>
                                        <input type=\"button\" class=\"btn btn-sm btn-primary\" value=\"Edit\" onclick=\"add_announcement({$res_announcement['recid']}, 'edit')\">
                                        <input type='button' class='btn btn-sm btn-danger' value='Delete' onclick='onDel({$res_announcement['recid']})'>
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

<div id="addannouncement" style="display:none;">
    <form id="my_table">
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label"><small>Title</small></label>
                <input type="text" class="form-control form-control-sm" id="title" name="save[title]" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label"><small>Description</small></label>
                <textarea rows="4" class="form-control" id="content" name="save[content]" required></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label"><small>Schedule From</small></label>
                <div class="input-wrapper">
                    <input type="text" class="form-control form-control-sm pickdate" id="pickdate_1" name="save[start_date]" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label"><small>Schedule To</small></label>
                <div class="input-wrapper">
                    <input type="text" class="form-control form-control-sm pickdate" id="pickdate_2" name="save[end_date]" readonly>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="view_announcement" style="display:none;">
    <div class="row mb-3">
        <div class="col-md-12">
            <label class="form-label"><small>Title</small></label>
            <input type="text" class="form-control form-control-sm" id="view_title" name="save[title]" disabled readonly>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label class="form-label"><small>Description</small></label>
            <textarea rows="4" class="form-control" id="view_content" name="save[content]" readonly disabled></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label"><small>Schedule From</small></label>
            <div class="input-wrapper">
                <input type="text" class="form-control form-control-sm pickdate" id="view_pickdate_1" name="save[start_date]" disabled readonly>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label"><small>Schedule To</small></label>
            <div class="input-wrapper">
                <input type="text" class="form-control form-control-sm pickdate" id="view_pickdate_2" name="save[end_date]"  disabled readonly>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $(document).ready(function() {
            $('#staffTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
            });
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

        $("#addannouncement").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            height: 500,
            title: "Add Announcement",
            resizable: false,
        });


        $("#view_announcement").dialog({
            autoOpen: false,
            modal: true,
            width: 800,
            height: 450,
            title: "Announcement Details",
            resizable: false,
            buttons: {
                Close: function() {
                    $(this).dialog("close");
                }
            }
        });

    });

    function add_announcement(recid,xpar){
        console.log(recid,xpar);
        if(xpar == 'edit'){
            ajaxWithBlocking({
                type: "post",
                url: "announcement_ajax.php",
                data: `recid=${recid}&event_action=view_data`,
                dataType: "json",
                success: function (response) {
                    if(!response.bool){
                        alertify.alert(response.msg);
                    }else{
                        $('#title').val(response.title);
                        $('#content').val(response.content);
                        $('#pickdate_1').val(response.start_date);
                        $('#pickdate_2').val(response.end_date);
                    }
                }
            })
        }

        $('#addannouncement').dialog('open');
        $("#addannouncement").dialog("option", "buttons", {
            "Save": function() {
                var formData = new FormData($("#my_table")[0]);
                formData.append("event_action", "save_data");
                formData.append("recid", recid);
                formData.append("par", xpar);
                ajaxWithBlocking({
                    type: "post",
                    url: "announcement_ajax.php",
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

    function onView(recid){
        $('#view_announcement').dialog('open');
        ajaxWithBlocking({
            type: "post",
            url: "announcement_ajax.php",
            data: `recid=${recid}&event_action=view_data`,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!response.bool){
                    alertify.alert(response.msg);
                }else{
                    $('#view_title').val(response.title);
                    $('#view_content').val(response.content);
                    $('#view_pickdate_1').val(response.start_date);
                    $('#view_pickdate_2').val(response.end_date);
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
                url: "announcement_ajax.php",
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