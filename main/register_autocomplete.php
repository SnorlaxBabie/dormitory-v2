<?php 

header('Content-Type: application/json');
// require_once '../include/std_header.php';
// require_once '../config/sessions.php';
require_once '../appconfig.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
$func = new Funcshits();
$search = isset($_POST['term']) ? trim($_POST['term']) : '';

if (empty($search)) {
    $qry = "SELECT rf.roomid,rf.roomnum, rf0.bedspacenum,rf0.amount
            FROM roomfile rf
            JOIN roomfile0 rf0 ON rf.roomnum = rf0.roomnum
            ORDER BY rf.roomnum ASC
            LIMIT 10";
    $stmt = $connect->prepare($qry);
    $stmt->execute();
    $result = $stmt->fetchAll(2);

} else {
    $xparams = ['%' . $search . '%'];
    $qry = "SELECT rf.roomid,rf.roomnum, rf0.bedspacenum,rf0.amount
        FROM roomfile rf
        JOIN roomfile0 rf0 ON rf.roomnum = rf0.roomnum
        WHERE rf.roomnum like ?
        ORDER BY rf.roomnum ASC
        LIMIT 10";
    $stmt = $connect->prepare($qry);
    $stmt->execute($xparams);
    $result = $stmt->fetchAll(2);
}

if (empty($result)) {
    echo json_encode([['label' => 'No results found', 'value' => '', 'class' => 'no-results']]);
    exit;
}

$formattedResult = array_map(function($item) {
    return [
            'label' => $item['roomnum'], 
            'value' => $item['bedspacenum'],
            'amount' => $item['amount'],
            'roomid' => $item['roomid']
    ];
}, $result);

echo json_encode($formattedResult);
?>