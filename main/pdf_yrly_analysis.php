<?php
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$data = json_decode(file_get_contents('php://input'), true);
$imageData1 = $data['image1'] ?? null;
$imageData2 = $data['image2'] ?? null;

if (!$imageData1 || !$imageData2) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Image data missing';
    exit();
}

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$html = '<h3 style="margin-top:-20px;">Tenants Yearly Analysis</h3>';
$html .= '<img src="' . htmlspecialchars($imageData1) . '" alt="Tenants Yearly Chart" style="width: 100%; height: auto; max-width: 1000px;" />';
$html .= '<h3 style="margin-top:20px;">Tenants Monthly Analysis</h3>';
$html .= '<img src="' . htmlspecialchars($imageData2) . '" alt="Tenants Monthly Chart" style="width: 100%; height: auto; max-width: 1000px;" />';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'Portrait');
$dompdf->render();

header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=tenants_analysis_report.pdf");
echo $dompdf->output();
exit();
