<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';

// require_once '../appconfig.php';
// echo '<pre>';var_dump('hereee 1',!isset($_SESSION['usr_cde']) && !isset($_SESSION['is_logged_in']));die();
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
                <h4>My Staff</h5>
            </div>

            <!-- Recent Orders Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Staff Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <input type="button" class="btn btn-sm btn-success px-3 py-0 mb-2" value="Add" onclick="addStaff()">
                    <!-- <table class="table table-bordered" width="100%" cellspacing="0"> -->
                    <table id="staffTable" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="20%">Name</th>
                                    <th width="5%">Age</th>
                                    <th width="10%">Contact</th>
                                    <th width="10%">Position</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM stafffile";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([]);
                                $xres_staff = $stmt->fetchAll();

                                foreach ($xres_staff as $staff) {
                                    echo "<tr>";
                                    echo "<td>{$staff['staffname']}</td>";
                                    echo "<td>{$staff['staffage']}</td>";
                                    echo "<td>{$staff['staffcontact']}</td>";
                                    echo "<td>{$staff['staffposition']}</td>";
                                    // Set colspan in the Action column
                                    echo "<td class='text-center'> 
                                        <input type='button' class='btn btn-sm btn-info' value='View' onclick='onView({$staff['recid']})'>
                                        <input type=\"button\" class=\"btn btn-sm btn-primary\" value=\"Edit\" onclick=\"addStaff({$staff['recid']}, 'edit')\">
                                        <input type='button' class='btn btn-sm btn-danger' value='Delete' onclick='onDel({$staff['recid']})'>
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

<div id="add_staff" style="display:none;">
    <form id="my_table">
        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Name</small></label>
                <input type="text" class="form-control form-control-sm" id="staff_name" name="save[staff_name]" >
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-6">
                <label class="form-label"><small>Age</small></label>
                <input type="text" class="form-control form-control-sm" id="staff_age" name="save[staff_age]" >
            </div>
            <div class="col-md-6">
                <label class="form-label"><small>Contact No.</small></label>
                <input type="text" class="form-control form-control-sm" id="staff_contact" name="save[staff_contact]" >
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Email Address</small></label>
                <input type="text" class="form-control form-control-sm" id="staff_email" name="save[staff_email]" >
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Position</small></label>
                <input type="text" class="form-control form-control-sm" id="staff_position" name="save[staff_position]" >
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Profile Image</small></label>
                <input type="file" class="form-control form-control-sm" id="staff_image" name="save[staff_image]" accept=".jpg, .jpeg, .png">
            </div>
        </div>
    </form>
</div>
<div id="view_staff" style="display:none;">
    <div class="row mb-1">

        <div class="col-md-6 d-flex justify-content-center align-items-center">
            <img id="staff_image_preview" src="https://via.placeholder.com/80" alt="Profile Image" class="img-thumbnail" 
                style="width: 300px; height: 300px; object-fit: cover;">
        </div>

        <div class="col-md-6">
            <div class="row mb-1">
                <div class="col-md-12">
                    <label class="form-label"><small>Name</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_staff_name" name="save[view_staff_name]" readonly disabled>
                </div>
            </div>

            <div class="row mb-1">
                <div class="col-md-6">
                    <label class="form-label"><small>Age</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_staff_age" name="save[view_staff_name]" readonly disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><small>Contact No.</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_staff_contact" name="save[view_staff_name]" readonly disabled>
                </div>
            </div>

            <div class="row mb-1">
                <div class="col-md-12">
                    <label class="form-label"><small>Email Address</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_staff_email" name="save[view_staff_name]" readonly disabled>
                </div>
            </div>

            <div class="row mb-1">
                <div class="col-md-12">
                    <label class="form-label"><small>Position</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_staff_position" name="save[view_staff_name]" readonly disabled>
                </div>
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

        $("#add_staff").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            height: 500,
            title: "Add Staff",
            resizable: false,
        });
        
        // $("#add_staff").dialog({
        //     autoOpen: false,
        //     modal: true,
        //     width: 500,
        //     height: 500,
        //     title: "Add Staff",
        //     resizable: false,
        //     buttons: {
        //         "Save": function() {
        //             var formData = new FormData($("#my_table")[0]);
        //             formData.append("event_action", "save_data");
        //             ajaxWithBlocking({
        //                 type: "post",
        //                 url: "landlord_staff_ajax.php",
        //                 data: formData,
        //                 contentType: false,
        //                 processData: false,
        //                 dataType: "json",
        //                 success: function (response) {
        //                     if(!response.bool){
        //                         alertify.alert(response.msg);
        //                     }else{
        //                         alertify.alert(response.msg, function(){
        //                             window.location.reload();
        //                         });
        //                     }
        //                 }
        //             });
        //         },
        //         Close: function() {
        //             $(this).dialog("close");
        //         }
        //     }
        // });

        $("#view_staff").dialog({
            autoOpen: false,
            modal: true,
            width: 800,
            height: 450,
            title: "Staff Details",
            resizable: false,
            buttons: {
                Close: function() {
                    $(this).dialog("close");
                }
            }
        });

    });

    function addStaff(recid,xpar){
        console.log(recid,xpar);
        if(xpar == 'edit'){
            ajaxWithBlocking({
                type: "post",
                url: "landlord_staff_ajax.php",
                data: `recid=${recid}&event_action=view_data`,
                dataType: "json",
                success: function (response) {
                    if(!response.bool){
                        alertify.alert(response.msg);
                    }else{
                        $('#staff_name').val(response.name);
                        $('#staff_age').val(response.age);
                        $('#staff_contact').val(response.contact);
                        $('#staff_email').val(response.email);
                        $('#staff_position').val(response.position);
                    }
                }
            })
        }
        $('#add_staff').dialog('open');
        $("#add_staff").dialog("option", "buttons", {
            "Save": function() {
                var formData = new FormData($("#my_table")[0]);
                formData.append("event_action", "save_data");
                formData.append("recid", recid);
                formData.append("par", xpar);
                ajaxWithBlocking({
                    type: "post",
                    url: "landlord_staff_ajax.php",
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
        $('#view_staff').dialog('open');
        ajaxWithBlocking({
            type: "post",
            url: "landlord_staff_ajax.php",
            data: `recid=${recid}&event_action=view_data`,
            dataType: "json",
            success: function (response) {
                if(!response.bool){
                    alertify.alert(response.msg);
                }else{
                    const imagePath = `upload/${response.image}`;
                    $('#staff_image_preview').attr('src', imagePath);
                    $('#view_staff_name').val(response.name);
                    $('#view_staff_age').val(response.age);
                    $('#view_staff_contact').val(response.contact);
                    $('#view_staff_email').val(response.email);
                    $('#view_staff_position').val(response.position);
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