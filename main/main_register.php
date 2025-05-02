<?php 
    require_once '../include/std_header.php';
?>
<style>


    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden; 
    }

    .card {
        overflow-y: auto; 
        max-height: 95vh; 
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
    }

    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #ddd;
        border-radius: 4px;
        z-index: 1000;
    }

    .ui-autocomplete .autocomplete-item {
        padding: 5px;
        font-size: 12px;
    }

    .ui-autocomplete .autocomplete-item:hover {
        background-color: #3860a1;
        cursor: pointer;
    }

    .ui-autocomplete .autocomplete-item.no-results {
        color: #000;
        text-align: center;
    }

    .autocomplete-item {
        padding: 8px;
        cursor: pointer;
    }

    .autocomplete-description {
        font-size: 0.9em;
        color: black;
        display: block;
    }

</style>    
    <div class="card shadow p-3">
        <center>
            <img src="../assets/logo.png" alt="logo" style="max-width: 100px; width: 100%;">
            <h4 class="text-center">Dormitory Management System</h5>
            <h5 class="text-center mb-4">Palanginan, Iba, Zambales</h5>
        </center>
        <form id="myForm">
            <hr>
                <fieldset>
                    <legend>Personal Information</legend>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            <small>First Name</small>
                            <input type="text" class="form-control form-control-sm" id="first_name" name="save[first_name]" >
                        </div>
                        <div class="col-md-4">
                            <small>Middle Name</small> <small><span class="text-muted">(optional)</span></small>
                            <input type="text" class="form-control form-control-sm" id="middle_name" name="save[middle_name]">
                        </div>
                        <div class="col-md-4">
                        <small>Last Name</small>
                            <input type="text" class="form-control form-control-sm" id="last_name" name="save[last_name]" >
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-4">
                            <div class="mb-1">
                            <small>Gender</small>
                                <select class="form-select form-select-sm" id="gender" name="save[gender]" >
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Prefer not to say">Prefer not to say</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                        <small>Contact No.</small>
                            <input type="text" class="form-control form-control-sm" id="contact" name="save[contact]">
                        </div>
                        <div class="col-md-4">
                        <small>Email Address</small>
                            <input type="email" class="form-control form-control-sm" id="email" name="save[email]" >
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-4">
                        <small>Username</small>
                            <input type="text" class="form-control form-control-sm" id="username" name="save[username]">
                        </div>
                        <div class="col-md-4">
                        <small>Password</small>
                            <input type="password" class="form-control form-control-sm" id="password" name="save[password]">
                        </div>
                        <div class="col-md-4">
                        <small>Confirm password</small>
                            <input type="password" class="form-control form-control-sm" id="confirmpasword" name="save[confirmpasword]" >
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-4">
                        <small>Barangay</small>
                            <input type="text" class="form-control form-control-sm" id="barangay" name="save[barangay]" >
                        </div>
                        <div class="col-md-4">
                        <small>Municipality</small>
                            <input type="text" class="form-control form-control-sm" id="municipality" name="save[municipality]">
                        </div>
                        <div class="col-md-4">
                        <small>Province</small>
                            <input type="text" class="form-control form-control-sm" id="province" name="save[province]" >
                        </div>
                    </div>

                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" id="haveCondition" name="save[haveCondition]" >
                        <small>Do you have a medical any medical conditions</small>
                    </div>
                </fieldset>
            <hr>
                <fieldset>
                    <legend>Emergency Contact Information</legend>
                    <small><span class="text-muted">(This person will be notified in case of emergency)</span></small>
                    <div class="row mb-1 mt-3">
                        <div class="col-md-4">
                        <small>Full Name</small>
                            <input type="text" class="form-control form-control-sm" id="eci_fullname" name="save[eci_fullname]" >
                        </div>
                        <div class="col-md-4">
                        <small>Relationship</small>
                            <input type="text" class="form-control form-control-sm" id="eci_relationship" name="save[eci_relationship]">
                        </div>

                        <div class="col-md-4">
                        <small>Contact No.</small>
                            <input type="text" class="form-control form-control-sm" id="eci_contact" name="save[eci_contact]" >
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-6">
                        <small>Home No.</small>
                            <input type="text" class="form-control form-control-sm" id="eci_homenum" name="save[eci_homenum]">
                        </div>
                        <div class="col-md-6">
                        <small>Work No.</small>
                            <input type="text" class="form-control form-control-sm" id="eci_worknum" name="save[eci_worknum]" >
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                        <small>Address</small>
                            <textarea id="eci_address" name="save[eci_address]" class="form-control form-control-sm" rows="3" placeholder="Enter address here..."></textarea>
                        </div>
                    </div>
                </fieldset>
            <hr>
                <fieldset>
                    <div class="row mb-1">
                        <div class="col-md-4">
                        <small>Deposit</small>
                            <input type="number" class="form-control form-control-sm" id="deposit" name="save[deposit]" >
                            <input type="hidden" class="form-control form-control-sm" id="roomamount" name="save[roomamount]" >
                            <input type="hidden" class="form-control form-control-sm" id="roomid" name="save[roomid]" >
                        </div>
                        <div class="col-md-2">
                        <small>Room No.</small>
                            <input type="text" class="form-control form-control-sm" id="roomnum" name="save[roomnum]">
                        </div>
                        <div class="col-md-2">
                        <small>Bedspace No.</small>
                            <input type="text" class="form-control form-control-sm" id="bedspace" name="save[bedspace]" readonly>
                        </div>
                        <div class="col-md-2">
                        <small>Start of Lease</small>
                            <!-- <input type="date" class="form-control form-control-sm" id="startlease" name="save[startlease]" > -->
                            <div class="input-wrapper float-end mx-2" style="width:200px">
                                <input type="checkbox" id="curr_date" class="internal-checkbox">
                                <input type="text" class="form-control form-control-sm amount pickdate" id="pickdate" name="save[startlease]" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                        <small>End of Lease</small>
                            <!-- <input type="date" class="form-control form-control-sm" id="endlease" name="save[endlease]" > -->
                             <div class="input-wrapper float-end mx-2" style="width:200px">
                                <input type="checkbox" id="curr_date1" class="internal-checkbox">
                                <input type="text" class="form-control form-control-sm amount pickdate" id="pickdate1" name="save[endlease]" readonly>
                            </div>
                        </div>
                    </div>
                </fieldset>
            <hr>
            <div class="row mb-1 align-items-center">
                <div class="col-md-8">
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" id="termsCheck" name="save[termsCheck]">
                        <label class="form-check-label" for="termsCheck">
                            <small>I agree to the <a href="#" onclick="openTerms()">Terms and Conditions</a></small>
                        </label>
                        <!-- <small class="d-block mt-2">
                            By submitting this registration form, you agree to the following terms:
                        </small> -->
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <input type="button" class="btn btn-sm p-0 px-4 btn-secondary" value="Back" onclick="goExit()">
                    <input type="button" class="btn btn-sm p-0 px-4 btn-primary text-white" value="Save" onclick="goSave()">
                </div>
            </div>
        </form>
    </div>


    <div id="terms_and_conditions" style="display:none;">
        <ol>
            <li><strong>Accuracy of Information:</strong> You confirm that all details provided are true and accurate. False information may result in rejection or termination of the rental agreement.</li>
            <li><strong>Use of Personal Information:</strong> Your personal data will be used for processing your registration and managing your tenancy. It will not be shared with third parties without your consent, unless required by law.</li>
            <li><strong>Medical Conditions:</strong> You agree to update the management on any medical condition changes for safety purposes.</li>
            <li><strong>Emergency Contact:</strong> You allow us to contact your emergency contact in urgent situations, confirming their consent to this.</li>
            <li><strong>Payment Obligations:</strong> If approved, you are responsible for timely rent payments as per the agreement.</li>
            <li><strong>Termination:</strong> Management may cancel your registration or deny services for violations or false information.</li>
            <li><strong>Consent to Communication:</strong> You agree to receive emails or messages related to your registration and payments.</li>
            <li><strong>Governing Law:</strong> This agreement is governed by the laws of Iba, Zambales.</li>
        </ol>
    </div>


<script>
    $(document).ready(function() {
        $("#terms_and_conditions").dialog({
            autoOpen: false,
            modal: true,
            width: 600,
            height: 600,
            title: "Terms and Conditions",
            resizable: false,
            buttons: {
                Close: function() {
                    $(this).dialog("close");
                }
            }
        });

        initAutocomplete();
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#roomnum, .ui-autocomplete').length) {
                $("#roomnum").autocomplete('destroy');
                initAutocomplete();
            }
        });
    });


    const openTerms = () =>{
        $('#terms_and_conditions').dialog('open');
    }

    const goExit = () =>{
        route('index')
    }

    const goSave = () =>{
        var formData = $('#myForm').serialize();
        var xparams = `${formData}&event_action=save_data`
        $.blockUI();
        ajaxWithBlocking({
            type: "post",
            url: "main_register_ajax.php",
            data: xparams,
            dataType: "json",
            success: function (response) {
                console.log(response);
                if(!response.bool){
                    alertify.alert(response.msg);
                }else{
                    alertify.alert(response.msg,function (){
                        route('index')
                    });
                }
            }
        });
    }

    function initAutocomplete() {
        $("#roomnum").autocomplete({
            position: {
                my: "left top",
                at: "left bottom",
                collision: "flip flip"
            },
            source: function(request, response) {
                if (request.term.length > 0) {
                    $.ajax({
                        url: 'register_autocomplete.php',
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
                    response([]);
                }
            },
            minLength: 1,
            delay: 300,
            select: function(event, ui) {
                if (ui.item.label !== "No results found" && ui.item.label !== "Error occurred") {
                    $(this).val(ui.item.label);
                    $('#bedspace').val(ui.item.value);
                    $('#roomamount').val(ui.item.amount);
                    $('#roomid').val(ui.item.roomid);
                }
                return false;
            },
            open: function(event, ui) {
                const autocompleteWidget = $(this).autocomplete("widget");
                const inputPosition = $(this).offset();
                const windowHeight = $(window).height();
                const dropdownHeight = autocompleteWidget.height();
                const inputHeight = $(this).outerHeight();
                const spaceBelow = windowHeight - (inputPosition.top + inputHeight);

                if (spaceBelow < dropdownHeight && inputPosition.top > dropdownHeight) {
                    autocompleteWidget.css({
                        "top": (inputPosition.top - dropdownHeight) + "px"
                    });
                }

                const dropdownWidth = autocompleteWidget.width();
                const windowWidth = $(window).width();
                const spaceRight = windowWidth - inputPosition.left;
                
                if (spaceRight < dropdownWidth) {
                    autocompleteWidget.css({
                        "left": (windowWidth - dropdownWidth - 10) + "px"
                    });
                }
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            return $("<li>")
                .addClass(item.class || "")
                .data("item.autocomplete", item)
                .append(
                    $("<div>")
                        .addClass("autocomplete-item" + (item.class ? " " + item.class : ""))
                        .append($("<strong>").text(item.label))
                        .append($("<span>").addClass("autocomplete-description") .text(" - " + item.value))
                        .append($("<span>").addClass("autocomplete-amount") .text(" ₱" + item.amount))
                )
                .appendTo(ul);
        };

        $("#roomnum").on('input', function() {
            if ($(this).val().trim() === "") {
                $('#bedspace').val('');
                $('#roomamount').val('');
                $('#roomid').val('');
            }
        });
    }

</script>

<?php 
require_once '../include/std_footer.php';
?>