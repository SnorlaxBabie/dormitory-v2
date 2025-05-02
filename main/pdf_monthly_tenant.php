<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../vendor/autoload.php'; 
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_02.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$month_qry = "SELECT 
                MONTHNAME(startlease) AS MONTH,
                SUM(CASE WHEN MONTH(startlease) = MONTH(CURDATE()) THEN 1 ELSE 0 END) AS NEW,
                SUM(CASE WHEN vacated = 1 AND YEAR(vacated_date) = YEAR(CURDATE()) THEN 1 ELSE 0 END) AS vacated,
                COUNT(*) - SUM(CASE WHEN vacated = 1 THEN 1 ELSE 0 END) AS active
            FROM 
                userfile
            WHERE 
                YEAR(startlease) = YEAR(CURDATE())
            GROUP BY 
                MONTH(startlease)
            ORDER BY 
                MONTH(startlease)";

$m_stmt = $connect->prepare($month_qry);
$m_stmt->execute();
$month_data = $m_stmt->fetchAll(PDO::FETCH_ASSOC); 



$html = '<h3 style="margin-top:-25px">Monthly Tenants Report</h3>';
$html .= '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
$html .= '<thead><tr><th>Month</th><th>New Tenants</th><th>Vacated Tenants</th><th>Active Leases</th></tr></thead>';

foreach ($month_data as $data) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($data['MONTH']) . '</td>'; 
    $html .= '<td>' . number_format($data['NEW']) . '</td>';
    $html .= '<td>' . number_format($data['vacated']) . '</td>';
    $html .= '<td>' . number_format($data['active']) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
ob_end_clean();
$dompdf->render();
$dompdf->stream("tenants_report.pdf", ["Attachment" => false]);
?>