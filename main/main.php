<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();

$qry = "SELECT SUM(current_tenants) AS total_occupied FROM roomfile";
$stmt = $connect->prepare($qry);
$stmt->execute();
$occupied = $stmt->fetch(PDO::FETCH_ASSOC)['total_occupied'];

$qry = "SELECT (SUM(roomcapacity) - SUM(current_tenants)) AS vacant_rooms FROM roomfile;";
$stmt = $connect->prepare($qry);
$stmt->execute();
$vacant = $stmt->fetch(PDO::FETCH_ASSOC)['vacant_rooms'];

$qry = "SELECT COUNT(*) AS tenant_request FROM tenantrequest WHERE reqstatus = 'Pending'";
$stmt = $connect->prepare($qry);
$stmt->execute();
$request = $stmt->fetch(PDO::FETCH_ASSOC)['tenant_request'];

$qry = "SELECT SUM(balance) AS unpaid FROM userfile";
$stmt = $connect->prepare($qry);
$stmt->execute();
$unpaid = $stmt->fetch(PDO::FETCH_ASSOC)['unpaid'];

$qry = "SELECT SUM(amount_paid) AS paid FROM payments";
$stmt = $connect->prepare($qry);
$stmt->execute();
$paid = $stmt->fetch(PDO::FETCH_ASSOC)['paid'];

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
                <h4>Dashboard</h4>
                <!-- <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                    </div>
                </div> -->
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2 card-dashboard">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                         Occupied</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($occupied,0) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2 card-dashboard">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Vacant</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($vacant,0) ?></div>
                                </div>
                                <div class="col-auto">
                                <i class="fas fa-door-open fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2 card-dashboard">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Tenant Request</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($request,0) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2 card-dashboard">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Lease Status</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">---</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xl-12 col-md-6 mb-4">
                    <div class="row h-100">
                        <div class="col-xl-2 col-md-6 mb-4 d-flex flex-column">
                            <div class="row flex-fill">

                                <div class="col-12 mb-3">
                                    <div class="card border-left-warning shadow h-100 py-2 card-dashboard">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Total Collected</div>
                                                    <div class="h1 mb-0 font-weight-bold text-gray-800"><?php echo '₱' . number_format($paid, 2); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card border-left-warning shadow h-100 py-1 card-dashboard">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Pending / Unpaid</div>
                                                    <div class="h1 mb-0 font-weight-bold text-gray-800"><?php echo '₱'. number_format($unpaid,2) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-3 d-flex"> 
                            <div class="card flex-fill shadow mb-2">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Payment Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <div id="paymentChart" style="width: 100%; height: 400px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-md-6 mb-3 d-flex"> 
                        <div class="card flex-fill shadow">
                            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Payment Activity</h6></div>
                                <div class="card-body d-flex flex-column">
                                    <div class="flex-fill">
                                        <div style="max-height: 410px; overflow-y: auto;"> 
                                            <table class="table table-bordered table-hover" id="paymentActivityTable">
                                                <thead>
                                                    <tr>
                                                        <th>Transaction ID</th>
                                                        <th>Room No.</th>
                                                        <th>Date</th>
                                                        <th>Balance</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Method</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php

                                                        $sql = "SELECT * FROM payments ORDER BY created_at DESC";
                                                        $stmt = $connect->prepare($sql);
                                                        $stmt->execute([]);
                                                        $payment = $stmt->fetchAll(2);

                                                        foreach ($payment as $xres_payment) {
                                                            $color = $xres_payment['status'] == 'Overdue' ? "bg-danger" : ($xres_payment['status'] == 'Partial' ? "bg-warning" : "bg-success");
                                                            echo "<tr>";
                                                            echo "<td>".$xres_payment['transaction_id'] .' '. $xres_payment['usr_mname'] .' '. $xres_payment['usr_lname']."</td>";
                                                            echo "<td>".$xres_payment['roomnum']."</td>";
                                                            echo "<td>".$func->formateDate($xres_payment['payment_date'],'m-d-Y')."</td>";
                                                            echo "<td class='amount'>".'₱'.number_format($xres_payment['balance'],2)."</td>";
                                                            echo "<td class='amount'>".'₱'.number_format($xres_payment['amount_paid'],2)."</td>";
                                                            echo "<td><span class='badge ".$color."'>".$xres_payment['status']."</span></td>";
                                                            echo "<td>".$xres_payment['method']."</td>";
                                                            // echo "<td>
                                                            //         <span class='badge bg-" . ($usrstatus == 'Active' ? 'success' : 'danger') . "'>".$usrstatus."</span>
                                                            //     </td>";
                                                            // echo "<td>".number_format($xres_payment['deposit'],2)."</td>";
                                                            // echo "<td>".$xres_payment['roomnum']."</td>";
                                                            // echo "<td>".$xres_payment['bedspacenum']."</td>";
                                                            // echo "<td>".$func->formateDate($xres_payment['startlease'],'m-d-Y')."</td>";
                                                            // echo "<td>".$func->formateDate($xres_payment['endlease'],'m-d-Y')."</td>";
                                                            echo "</tr>";
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

 

<script>
    $(document).ready(function() {
        var paid = "<?php echo $paid; ?>";
        var unpaid = "<?php echo $unpaid; ?>";
        const paymentData = [
            { value: paid, name: 'Paid' },
            { value: unpaid, name: 'Unpaid' }
        ];

        renderPieChart('paymentChart', paymentData);
        
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

    });

    function renderPieChart(e, data) {
        const chartDom = $('#' + e)[0];
        const myChart = echarts.init(chartDom);

        const option = {
            title: {
                text: 'Payment Status Overview',
                subtext: 'Paid vs Unpaid',
                left: 'center',
                textStyle: {
                    fontSize: 18,
                    fontWeight: 'bold',
                    color: '#333'
                },
                subtextStyle: {
                    fontSize: 14,
                    color: '#777'
                }
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c} ({d}%)'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: data.map(item => item.name),
                textStyle: {
                    color: '#333',
                },
            },
            series: [
                {
                    name: 'Payment Status',
                    type: 'pie',
                    radius: '50%',
                    center: ['50%', '50%'],
                    data: data,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    },
                    itemStyle: {
                        shadowColor: 'rgba(0, 0, 0, 0.5)',
                        shadowBlur: 10,
                    },
                    label: {
                        show: true,
                        formatter: '{b}: ({d}%)',
                        // formatter: '{b}: {c} ({d}%)',
                        textStyle: {
                            color: '#000',
                        },
                    },
                    labelLine: {
                        show: true,
                        length: 10,
                        length2: 10
                    },
                    animation: true,
                    animationDuration: 1000,
                    animationEasing: 'cubicOut',
                }
            ],
        };
        myChart.setOption(option);
    }
</script>

<?php 
require_once '../include/std_footer.php';
?>        