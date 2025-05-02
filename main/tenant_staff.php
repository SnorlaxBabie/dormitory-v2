<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        require_once '../include/std_sidebar_tenant.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h4>My Staff</h5>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Staff Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="staffTable" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 30;">Name</th>
                                    <th style="width: 10%;">Age</th>
                                    <th style="width: 20%;">Contact</th>
                                    <th style="width: 25%;">Position</th>
                                    <th style="width: 5%;">Action</th>
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
                                    echo "<td>".$staff['staffname']."</td>";
                                    echo "<td>".$staff['staffage']."</td>";
                                    echo "<td>".$staff['staffcontact']."</td>";
                                    echo "<td>".$staff['staffposition']."</td>";
                                    echo "<td class='text-center'> 
                                        <input type='button' class='btn btn-sm btn-primary px-4' value='View' onclick='onView(".$staff['recid'].")'>
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

        $('#staffTable').DataTable({
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

    const onView = (recid) => {
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

</script>

<?php 
require_once '../include/std_footer.php';
?>