<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';

// require_once '../appconfig.php';
// echo '<pre>';var_dump('hereee 1',!isset($_SESSION['usr_cde']) && !isset($_SESSION['is_logged_in']));die();
?>


<style>
    .table-container {
        max-height: 1000px;
        overflow-y: auto;
        overflow-x: auto;
    }
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #ddd;
        border-radius: 4px;
        z-index: 1000; /* Ensure it appears above other elements */
    }

    .ui-autocomplete .autocomplete-item {
        padding: 5px; /* Add padding for better spacing */
        font-size: 12px; /* Increase font size for better readability */
    }

    .ui-autocomplete .autocomplete-item:hover {
        background-color: #3860a1; /* Change background on hover */
        cursor: pointer; /* Change cursor to pointer */
    }

    .ui-autocomplete .autocomplete-item.no-results {
        color: #000; /* Style for "No results found" message */
        text-align: center; /* Center-align the message */
    }
</style>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        require_once '../include/std_sidebar.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h4>Room Tracking</h5>
            </div>

            <!-- Recent Orders Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Room Tracking</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <input type="button" class="btn btn-sm btn-success mb-2" value="New Room" onclick="new_room()">
                    <input type="button" class="btn btn-sm btn-primary mb-2" value="Bed Setup" onclick="bed_space()">
                    <!-- <table class="table table-bordered" width="100%" cellspacing="0"> -->
                        <hr>
                    <div class="row">
                        <div class="col-xl-6">
                            <fieldset>
                                <legend>Room Master File</legend>
                                <hr>
                                <div class="table-container">
                                    <table id="newroom" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th width="40%">Room No.</th>
                                                <th width="5%">Capacity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sql = "SELECT * FROM roomfile";
                                                $stmt = $connect->prepare($sql);
                                                $stmt->execute([]);
                                                $xres_room = $stmt->fetchAll();

                                                foreach ($xres_room as $room) {
                                                    echo "<tr>";
                                                    echo "<td>".$room['roomnum']."</td>";
                                                    echo "<td>".$room['roomcapacity']."</td>";
                                                    echo "</tr>";
                                                }

                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-xl-6">
                            <fieldset>
                                <legend>Room & Bedspace No.</legend>
                                <hr>
                                <div class="table-container">
                                    <table id="bedspace" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th width="10%">Room No.</th>
                                                <th width="15%">Bed Space No.</th>
                                                <th width="10%">Rate</th>
                                                <th width="5%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sql = "SELECT * FROM roomfile0";
                                                $stmt = $connect->prepare($sql);
                                                $stmt->execute([]);
                                                $xres_bedspace = $stmt->fetchAll();

                                                foreach ($xres_bedspace as $bedspace) {
                                                    $xstatus = $bedspace['roomstat'] == 'available' ? 'success' : 'warning';

                                                    echo "<tr>";
                                                    echo "<td>".$bedspace['roomnum']."</td>";
                                                    echo "<td>".$bedspace['bedspacenum']."</td>";
                                                    echo "<td class='amount'>".number_format($bedspace['amount'],2)."</td>";

                                                    echo "<td>
                                                            <span class='badge bg-" . ($xstatus) . "'>".($bedspace['roomstat'])."</span>
                                                        </td>";

                                                    echo "</tr>";
                                                }

                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="new_room" style="display:none;">
    <form id="my_table">
        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Room No.</small></label>
                <input type="text" class="form-control form-control-sm" id="save_roomnum" name="save[roomnum]" >
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Capacity</small></label>
                <input type="text" class="form-control form-control-sm" id="save_capacity" name="save[capacity]" >
            </div>
        </div>
    </form>
</div>

<div id="bed_space" style="display:none;">
    <form id="frm_bedspace">
        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Room No.</small></label>
                <input type="text" class="form-control form-control-sm" id="bedspace_roomnum" name="bedspace[roomnum]"">
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Bed Space No.</small></label>
                <input type="text" class="form-control form-control-sm" id="bedspace_capacity" name="bedspace[bedspacenum]" >
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <label class="form-label"><small>Amount</small></label>
                <input type="number" class="form-control form-control-sm" id="bedspace_amount" name="bedspace[amount]" >
            </div>
        </div>
    </form>
</div>


<script>
    $(document).ready(function() {
        $('#newroom').DataTable({
            paging: true,
            searching: true,
            ordering: true,
        });
        $('#bedspace').DataTable({
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

        $("#new_room").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            height: 300,
            title: "Add New Room",
            resizable: false,
        });
        $("#bed_space").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            height: 400,
            title: "Room & Bed Space Setup",
            resizable: false,
        });

        initAutocomplete();
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#bedspace_roomnum, .ui-autocomplete').length) {
                $("#bedspace_roomnum").autocomplete('destroy');
                initAutocomplete();
            }
        });

    });

    function new_room(recid,xpar){
        $('#new_room').dialog('open');
        $("#new_room").dialog("option", "buttons", {
            "Save": function() {
                var formData = new FormData($("#my_table")[0]);
                formData.append("event_action", "save_data");
                formData.append("recid", recid);
                formData.append("par", xpar);
                ajaxWithBlocking({
                    type: "post",
                    url: "landlord_roomsetup_ajax.php",
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

    function bed_space(recid,xpar){
        $('#bedspace_roomnum').val('');
        $('#bedspace_capacity').val('');
        $('#bedspace_amount').val('');
        $('#bed_space').dialog('open');
        $("#bed_space").dialog("option", "buttons", {
            "Save": function() {
                var formData = new FormData($("#frm_bedspace")[0]);
                formData.append("event_action", "save_bedspace");
                formData.append("recid", recid);
                formData.append("par", xpar);
                ajaxWithBlocking({
                    type: "post",
                    url: "landlord_roomsetup_ajax.php",
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

    function initAutocomplete() {
        $("#bedspace_roomnum").autocomplete({
            source: function(request, response) {
                console.log('Search term:', request.term);
                if (request.term.length > 0) {
                    $.ajax({
                        url: 'landlord_ajax_autocomplete.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            if (Array.isArray(data) && data.length > 0) {
                                response(data);
                            } else {
                                response([{
                                    label: "No results found",
                                    value: "",
                                    class: "no-results"
                                }]);
                            }
                        },
                        error: function() {
                            response([{
                                label: "Error occurred",
                                value: "",
                                class: "error"
                            }]);
                        }
                    });
                } else {
                    response([]); // Empty response when no search term
                }
            },
            minLength: 1,
            delay: 300,
            select: function(event, ui) {
                if (ui.item.label !== "No results found" && ui.item.label !== "Error occurred") {
                    $(this).val(ui.item.value);
                }
                return false;
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            return $("<li>")
                .addClass(item.class || "")
                .data("item.autocomplete", item)
                .append(
                    $("<div>")
                        .addClass("autocomplete-item" + (item.class ? " " + item.class : ""))
                        .text(item.label)
                )
                .appendTo(ul);
        };
    }
</script>

<?php 
require_once '../include/std_footer.php';
?>