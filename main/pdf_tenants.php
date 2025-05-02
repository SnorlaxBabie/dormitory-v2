<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../vendor/autoload.php';
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
use Dompdf\Dompdf;
use Dompdf\Options;

$sql = "SELECT * FROM userfile WHERE usr_lvl ='USER'";
$stmt = $connect->prepare($sql);
$stmt->execute([]);
$xres_staff = $stmt->fetchAll(PDO::FETCH_ASSOC); 

$html = '<style>
            body {
                font-size: 12px; /* Reduced default font size */
                font-family: Arial, sans-serif;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            .table th, .table td {
                border: 1px solid #000;
                padding: 5px;
                text-align: left;
                font-size: 9px;
            }
            .badge {
                padding: 4px;
                border-radius: 3px;
                color: white;
                font-size: 9px;
            }
            .badge.bg-success {
                background-color: green;
            }
            .badge.bg-danger {
                background-color: red;
            }
         </style>';

$html .= '<h3 style="margin-top:-20px;">Tenant Report</h3>';
$html .= '<div class="table-responsive">';
$html .= '<table class="table" cellspacing="0">';
$html .= '<thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Email Address</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Status</th>
                <th>Deposit</th>
                <th>Room No.</th>
                <th>Bedspace No.</th>
                <th>Start of Lease</th>
                <th>End of Lease</th>
            </tr>
          </thead>
          <tbody>';

foreach ($xres_staff as $staff) {
    $usrstatus = $staff['usr_status'] == 1 ? "Active" : "Inactive";
    $html .= "<tr>";
    $html .= "<td>".$staff['usr_fname'] .' '. $staff['usr_mname'] .' '. $staff['usr_lname']."</td>";
    $html .= "<td>".number_format($staff['age'],0)."</td>";
    $html .= "<td>".$staff['usr_sex']."</td>";
    $html .= "<td>".$staff['usr_email']."</td>";
    $html .= "<td>".$staff['usr_contactnum']."</td>";
    $html .= "<td>".$staff['usr_brgy'].', '.$staff['usr_municipality'].', '.$staff['usr_province']."</td>";
    $html .= "<td>".$usrstatus."</td>";
    $html .= "<td>".number_format($staff['deposit'],2)."</td>";
    $html .= "<td>".$staff['roomnum']."</td>";
    $html .= "<td>".$staff['bedspacenum']."</td>";
    $html .= "<td>".$func->formateDate($staff['startlease'],'m-d-Y')."</td>";
    $html .= "<td>".$func->formateDate($staff['endlease'],'m-d-Y')."</td>";
    $html .= "</tr>";
}

$html .= '</tbody>';
$html .= '</table>';
$html .= '</div>';

$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('marginTop', 20);
$options->set('marginRight', 20);
$options->set('marginBottom', 20);
$options->set('marginLeft', 20);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
ob_end_clean(); 
$dompdf->render();

$dompdf->stream("tenant_report.pdf", ["Attachment" => false]);
?>