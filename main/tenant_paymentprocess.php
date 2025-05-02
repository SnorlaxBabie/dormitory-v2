<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
$func = new Funcshits();

// Fetch tenant balance from the database
$tenant_id = $_SESSION['usr_cde'];
$balanceQuery = "SELECT balance FROM userfile WHERE usr_cde = ?";
$stmt = $connect->prepare($balanceQuery);
$stmt->execute([$tenant_id]);
$tenant = $stmt->fetch(PDO::FETCH_ASSOC);
$outstanding_balance = $tenant['balance'];
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
                <h4 class="fw-bold">Payment Processing</h4>
            </div>

        <!-- Payment Form -->
        <!-- <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Record Payment</h6>
            </div>
            <div class="card-body">
                <form id="paymentForm">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control form-control-sm" id="amount" name="amount" placeholder="Enter amount">
                        </div>
                        <div class="col-md-4">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select class="form-select form-select-sm" id="paymentMethod" name="paymentMethod">
                                <option value="" selected disabled>Select Method</option>
                                <option value="Gcash">G-cash</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><small>Profile Image</small></label>
                            <input type="file" class="form-control form-control-sm" id="staff_image" name="save[staff_image]" accept=".jpg, .jpeg, .png">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm">Submit Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div> -->

        <!-- Payments Table -->
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Payment Transactions</h6>
                <div class="alert alert-info mb-0 py-0" role="alert">
                    <strong>Balance:</strong> 
                    <span id="outstandingBalance" class="fw-bold"><?php echo number_format($outstanding_balance, 2); ?></span>
                </div>
            </div>
            <div class="card-body">
            <input type="button" class="btn btn-sm btn-primary mb-2" value="Create payment" onclick="create_payment()">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="paymentTable">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Trasaction No.</th>
                                <th width="20%">Name</th>
                                <th width="10%">Amount</th>
                                <th width="10%">Due Date</th>
                                <th width="10%">Payment Date</th>
                                <th width="5%">Method</th>
                                <th width="5%">Status</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php
                                $sql = "SELECT * FROM payments WHERE usr_cde = ? ORDER BY created_at DESC";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([$_SESSION['usr_cde']]);
                                $xres_payment = $stmt->fetchAll();

                                foreach ($xres_payment as $payment) {
                                    $get_val = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$payment['usr_cde']]);
                                    $status = "";
                                    switch($payment['status']){
                                        case 'Overdue':
                                            $status = "<span class='badge bg-danger text-white'>".$payment['status']."</span>";
                                            break;
                                        case 'Partial':
                                            $status = "<span class='badge bg-warning text-white'>".$payment['status']."</span>";
                                            break;
                                        case 'Paid':
                                            $status = "<span class='badge bg-success text-white'>".$payment['status']."</span>";
                                            break;
                                    }
                                    echo "<tr>";
                                    echo "<td>".$payment['transaction_id']."</td>";
                                    echo "<td>".$get_val['usr_fname']." ".$get_val['usr_lname']."</td>";
                                    echo "<td class='amount'>".number_format($payment['amount_paid'],2)."</td>";
                                    echo "<td>".$func->formateDate($payment['due_date'],'m-d-Y')."</td>";
                                    echo "<td>".$func->formateDate($payment['payment_date'],'m-d-Y')."</td>";
                                    echo "<td>".$payment['method']."</td>";
                                    echo "<td>".$status."</td>";
                                    echo "<td class='text-center'> 
                                        <input type='button' class='btn btn-sm btn-info' value='View' onclick='onView(\"{$payment['transaction_id']}\")'>
                                        <input type='button' class='btn btn-sm btn-primary' value='Edit' onclick='create_payment(\"{$payment['transaction_id']}\", \"edit\")'>
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

<div id="process_payment" style="display:none;">
    <form id="paymentForm">
        <div class="row mb-3">
            <div class="col-md-12 mb-2">
                <small>Amount</small>
                <input type="number" class="form-control form-control-sm" id="amount" name="amount" placeholder="Enter amount" required>
            </div>
            <div class="col-md-12 mb-2">
                <small>Payment Method</small>
                <select class="form-select form-select-sm" id="paymentMethod" name="paymentMethod" required>
                    <option value="Cash">Cash</option>
                    <option value="G-Cash">G-Cash</option>
                </select>
            </div>
            <div class="col-md-12 mb-2">
                <small>Screenshot (Optional)</small>
                <input type="file" class="form-control form-control-sm" id="proofpayment" name="save[proofpayment]" accept=".jpg, .jpeg, .png">
                <div class="form-text">
                    Please attach a screenshot as proof of payment (optional).
                </div>
            </div>
        </div>
    </form>
</div>


<div id="view_payment" style="display:none;">
    <div class="row mb-1">
        <div class="col-md-12 mb-2">
            <div class="row mb-1">
                <div class="col-md-6">
                    <label class="form-label"><small>Amount</small></label>
                    <input type="text" class="form-control form-control-sm amount" id="view_amount" name="save[view_amount]" readonly disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><small>Payment Method</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_method" name="save[view_method]" readonly disabled>
                </div>
            </div>
        </div>
        <label class="form-label"><small>Proof of payment</small></label>
        <div class="col-md-12 d-flex justify-content-center align-items-center">
            <img id="proofofpayment" src="https://via.placeholder.com/80" alt="Proof of payment" class="img-thumbnail" 
                style="width: 600px; height: 600px; object-fit: cover;">
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#paymentTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
        });

        $("#process_payment").dialog({
            autoOpen: false,
            modal: true,
            width: 430,
            height: 370,
            title: "Create Payment",
            resizable: false,
        });

        $("#view_payment").dialog({
            autoOpen: false,
            modal: true,
            width: 670,
            height: 850,
            title: "Payment Details",
            resizable: false,
            buttons: {
                Close: function() {
                    $(this).dialog("close");
                }
            }
        });
    });

    const create_payment = (transac_id,xpar) => {
        $('#amount').val('');
        $('#paymentMethod').val('Cash');
        $('#amount').removeClass('disabled').prop('readonly', !true);
        $('#paymentMethod').prop('disabled', !true);
        if(xpar == 'edit'){
            ajaxWithBlocking({
                type: "post",
                url: "tenant_payment_process_ajax.php",
                data: `event_action=view_data&transac_id=${transac_id}`,
                dataType: "json",
                success: function (response) {
                    if(!response.bool){
                        alertify.alert(response.msg);
                    }else{
                        $('#amount').addClass('disabled').prop('readonly', true);
                        $('#paymentMethod').prop('disabled', true);
                        $('#amount').val(response.amount_paid);
                        $('#paymentMethod').val(response.method);
                    }
                }
            })
        }
        $('#process_payment').dialog('open');
        $("#process_payment").dialog("option", "buttons", {
            "Submit": function() {
                var formData = new FormData($("#paymentForm")[0]);
                formData.append("event_action", "save_data");
                formData.append("transacid", transac_id);
                formData.append("par", xpar);
                ajaxWithBlocking({
                    type: "post",
                    url: "tenant_payment_process_ajax.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (response) {
                        if(!response.bool){
                            alertify.alert(response.msg);
                        } else {
                            if(response.url != ''){
                                window.location.href = response.url;
                            }else{
                                alertify.alert(response.msg, function() {
                                    window.location.reload();
                                });
                            }
                        }
                    }
                });
            },
            "Close": function() {
                $(this).dialog("close");
            }
        });
    }

    const onView = (transaction_id) => {
        $('#view_payment').dialog('open');
        ajaxWithBlocking({
            type: "post",
            url: "tenant_payment_process_ajax.php",
            data: `transac_id=${transaction_id}&event_action=view_data`,
            dataType: "json",
            success: function (response) {
                console.log(response);

                if(!response.bool){
                    alertify.alert(response.msg);
                }else{
                    const imagePath = `upload/proofpayment/${response.proofpayment}`;
                    $('#proofofpayment').attr('src', imagePath);
                    $('#view_amount').val(response.amount_paid);
                    $('#view_method').val(response.method);
                }
            }
        })
    }
</script>

<?php 
require_once '../include/std_footer.php';
?>