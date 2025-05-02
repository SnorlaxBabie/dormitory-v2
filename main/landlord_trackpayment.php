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
                <h4>Payment</h4>
            </div>
            <!-- Tenants Payment Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="m-0 font-weight-bold text-primary">Payment Tracking</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="current" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Transaction #</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Payment Date</th>
                                    <th>Due Date</th>
                                    <th>Payment Method</th>
                                    <th>Contact #</th>
                                    <th>Address</th>
                                    <th class="amount">Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM payments ORDER BY created_at DESC LIMIT 0,1";
                                $stmt = $connect->prepare($sql);
                                $stmt->execute([]);
                                $xres_tenant = $stmt->fetchAll();

                                foreach ($xres_tenant as $tenant_list) {
                                    $chkusr = $func->FetchSingle($connect,"userfile","WHERE usr_cde =?",[$tenant_list['usr_cde']]);
                                    echo "<tr>";
                                    echo "<td>".$tenant_list['transaction_id']."</td>";
                                    echo "<td>".$chkusr['usr_fname'] .' '. $chkusr['usr_mname'] .' '. $chkusr['usr_lname']."</td>";
                                    echo "<td>".$chkusr['usr_email']."</td>";
                                    echo "<td>".$func->formatDateTime($tenant_list['created_at'])."</td>";
                                    echo "<td>".$func->formatDate3($tenant_list['due_date'])."</td>";
                                    echo "<td>".$tenant_list['method']."</td>";
                                    echo "<td>".$chkusr['usr_contactnum']."</td>";
                                    echo "<td>".$chkusr['usr_brgy'].', '.$chkusr['usr_municipality'].', '.$chkusr['usr_province']."</td>";
                                    echo "<td class='amount'>"."₱".number_format($tenant_list['balance'],2)."</td>";
                                    echo "<td class='text-center'>
                                    <div class='d-flex justify-content-center'>
                                    <span class='badge bg-" . ($tenant_list['status'] == 'Partial' ? 'warning' : ($tenant_list['status'] == 'Overdue' ? 'danger' : 'success')) . "'>".$tenant_list['status']."</span>
                                    </div>
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

<script>
    $(document).ready(function () {
        console.log('%chakdog', 'color: #ff0000; font-weight: bold; font-size: 14px;');
        $('#current').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: false,
            pageLength: 10 
        });

        $("#view_tenant").dialog({
            autoOpen: false,
            modal: true,
            width: 930,
            height: 430,
            title: "Tenant Details",
            resizable: false,
        });
    });

</script>

<?php 
require_once '../include/std_footer.php';
?>