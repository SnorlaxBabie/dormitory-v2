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
                <h4>Payment History</h5>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Transactions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="paymentTable" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Transaction ID</th>
                                    <th style="width: 20%;">Date</th>
                                    <th style="width: 20%;">Amount</th>
                                    <th style="width: 20%;">Payment Method</th>
                                    <th style="width: 15%;">Status</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // $sql = "SELECT * FROM payment_transactions ORDER BY transaction_date DESC";
                                // $stmt = $connect->prepare($sql);
                                // $stmt->execute([]);
                                // $xres_payments = $stmt->fetchAll();

                                // foreach ($xres_payments as $payment) {
                                //     echo "<tr>";
                                //     echo "<td>".$payment['transaction_id']."</td>";
                                //     echo "<td>".$payment['transaction_date']."</td>";
                                //     echo "<td>$".number_format($payment['amount'], 2)."</td>";
                                //     echo "<td>".$payment['payment_method']."</td>";
                                //     echo "<td>".$payment['status']."</td>";
                                //     echo "<td class='text-center'> 
                                //         <input type='button' class='btn btn-sm btn-primary px-4' value='Details' onclick='onViewPayment(".$payment['id'].")'>
                                //     </td>";
                                //     echo "</tr>";
                                // }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="payment_details" style="display:none;">
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="row mb-2">
                <div class="col-md-12">
                    <label class="form-label"><small>Transaction ID</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_transaction_id" readonly disabled>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label"><small>Amount</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_amount" readonly disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><small>Date</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_transaction_date" readonly disabled>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <label class="form-label"><small>Payment Method</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_payment_method" readonly disabled>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row mb-2">
                <div class="col-md-12">
                    <label class="form-label"><small>Status</small></label>
                    <input type="text" class="form-control form-control-sm" id="view_status" readonly disabled>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <label class="form-label"><small>Description</small></label>
                    <textarea class="form-control" id="view_description" rows="4" readonly disabled></textarea>
                </div>
            </div>
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

        $("#payment_details").dialog({
            autoOpen: false,
            modal: true,
            width: 800,
            height: 450,
            title: "Payment Transaction Details",
            resizable: false,
            buttons: {
                Close: function() {
                    $(this).dialog("close");
                }
            }
        });
    });

    const onViewPayment = (paymentId) => {
        $('#payment_details').dialog('open');
        ajaxWithBlocking({
            type: "post",
            url: "payment_ajax_handler.php", 
            data: `payment_id=${paymentId}&event_action=view_details`,
            dataType: "json",
            success: function (response) {
                if(!response.bool){
                    alertify.alert(response.msg);
                }else{
                    $('#view_transaction_id').val(response.transaction_id);
                    $('#view_amount').val('$' + parseFloat(response.amount).toFixed(2));
                    $('#view_transaction_date').val(response.transaction_date);
                    $('#view_payment_method').val(response.payment_method);
                    $('#view_status').val(response.status);
                    $('#view_description').val(response.description);
                }
            }
        })
    }
</script>

<?php 
require_once '../include/std_footer.php';
?>