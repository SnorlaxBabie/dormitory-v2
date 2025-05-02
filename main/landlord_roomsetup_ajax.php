<?php 
header('Content-Type: application/json');
require_once '../appconfig.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$event_action = $_POST['event_action'];
$response['bool'] = true;
$response['msg'] .= "";

if($event_action == "save_data"){
    $room = $_POST['save'];
    $roomnum = $room['roomnum'];
    $capacity = $room['capacity'];

    $require_field = [
        'Room No.' => $roomnum,
        'Capacity' => $capacity
    ];

    $err_msg = Standard::isFieldRequired($require_field);
    $chkroom = $func->FetchSingle($connect,"roomfile","WHERE roomnum = ?",[$roomnum])['roomnum'];

    if (!empty($chkroom)) {
        $response['bool'] = false;
        $response['msg'] .= "Room No. already exists.<br>";
    }

    if ($capacity > 4) {
        $response['bool'] = false;
        $response['msg'] .= "Max room capacity is 4.<br>";
    }

    if (count($err_msg) > 0) {
        $response['bool'] = false;
        foreach ($err_msg as $field => $msg) {
            $response['msg'] .= $msg . "<br>";
        }
    }

    if ($response['bool']) {
        $roomid = $func->genUniqueNumber("ROOM");
        $xparams = [
            'roomid'       => trim($roomid),
            'roomnum'      => trim($roomnum),
            'roomcapacity' => trim($capacity)
        ];

        $result = $func->InsertRecord($connect, "roomfile", $xparams);

        if($result['bool']){
            $response['bool'] = !false;
            $response['msg'] .= "Room successfully added.<br>";
        }
    }
}

if($event_action == "save_bedspace"){
    $room = $_POST['bedspace'];
    $roomnum = $room['roomnum'];
    $bedspacenum = $room['bedspacenum'];
    $rate = $room['amount'];
    
    $require_field = [
        'Room No.' => $roomnum,
        'Bed Space No.' => $bedspacenum,
        'Amount' => $rate
    ];

    $err_msg = Standard::isFieldRequired($require_field);
    $chkroom = $func->FetchSingle($connect,"roomfile0","WHERE bedspacenum = ? AND roomnum = ?",[$bedspacenum,$roomnum])['bedspacenum'];
    $xchkcount = $func->FetchAll($connect,"roomfile0","roomnum","WHERE roomnum = ?",[$roomnum]);
    $xgetroom = $func->FetchSingle($connect,"roomfile","WHERE roomnum = ?",[$roomnum]);

    if (empty($xgetroom['roomnum'])) { // check room if valid
        $response['bool'] = false;
        $response['msg'] .= "Room No. ".$roomnum." does not exist in Room Master File.<br>";
    }
    
    if (!empty($chkroom)) {
        $response['bool'] = false;
        $response['msg'] .= "Bed Space No. ".$bedspacenum." is already exists in Room no. ".$roomnum.".<br>";
    }

    if(count($xchkcount) > 3){
        $response['bool'] = false;
        $response['msg'] .= "Max bed per room is 4.<br>";
    }

    if (count($err_msg) > 0) {
        $response['bool'] = false;
        foreach ($err_msg as $field => $msg) {
            $response['msg'] .= $msg . "<br>";
        }
    }

    if ($response['bool']) {

        $xparams = [
            'roomid'          => trim($xgetroom['roomid']),
            'roomnum'         => trim($xgetroom['roomnum']),
            'roomstat'        => trim($xgetroom['roomstat']),
            'current_tenants' => 0,
            'amount' => $rate,
            'bedspacenum'     => $bedspacenum
        ];
        $result = $func->InsertRecord($connect, "roomfile0", $xparams);

        if($result['bool']){
            $response['bool'] = !false;
            $response['msg'] .= "Room successfully added.<br>";
        }
    }
}

echo json_encode($response);
?>