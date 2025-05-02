<?php 

header('Content-Type: application/json');
// require_once '../include/std_header.php';
// require_once '../config/sessions.php';
require_once '../appconfig.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
$func = new Funcshits();
$search = isset($_POST['term']) ? trim($_POST['term']) : '';

// If search is empty, still return results but limit them
if (empty($search)) {
    $result = $func->FetchAll($connect, "roomfile", "*", "", [], "ORDER BY roomnum ASC", "LIMIT 10");
} else {
    $xparams = ['%' . $search . '%'];
    $result = $func->FetchAll($connect, "roomfile", "*", "WHERE roomnum like ?", $xparams, "ORDER BY roomnum ASC", "LIMIT 10");
}

// Handle empty results
if (empty($result)) {
    echo json_encode([['label' => 'No results found', 'value' => '', 'class' => 'no-results']]);
    exit;
}

$formattedResult = array_map(function($item) {
    return ['label' => $item['roomnum'], 'value' => $item['roomnum']];
}, $result);

echo json_encode($formattedResult);
?>