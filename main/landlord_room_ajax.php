<?php 
header('Content-Type: application/json');
require_once '../appconfig.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$event_action = $_POST['event_action'];

$response = [
    'bool' => true,
    'msg' => ''
];

if ($event_action == 'view_data') {
    $roomid = $_POST['roomid'];
    $xhtml = "";
    $response = [
        'bool' => false,
        'html' => $xhtml,
        'msg' => 'No Result Found'
    ];

    $inRoom = $func->FetchAll($connect, "roomfile1","*","WHERE roomid = ?",[$roomid]);
    if(count($inRoom) > 0){
        foreach ($inRoom as $insideRoom) {
            $new_data = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$insideRoom['usr_cde']]);
            $gender = $new_data['usr_sex'] == "Prefer not to say" ? "" : $new_data['usr_sex'];
            $xhtml .= '<tr>';
            $xhtml .= '<td>' . htmlspecialchars($new_data['usr_fname'] .' '.$new_data['usr_lname']) . '</td>'; // full name
            $xhtml .= '<td>' . htmlspecialchars($gender) . '</td>'; // gender
            $xhtml .= '<td>' . htmlspecialchars($new_data['usr_email']) . '</td>'; // email
            $xhtml .= '<td>' . htmlspecialchars($new_data['usr_contactnum']) . '</td>'; // contact number
            $xhtml .= '<td>' . htmlspecialchars($new_data['usr_brgy'] .', '.$new_data['usr_municipality'].', '.$new_data['usr_province']) . '</td>'; // address
            $xhtml .= '<td>' . htmlspecialchars(number_format($new_data['deposit'],2)) . '</td>'; // deposit
            $xhtml .= '<td>' . htmlspecialchars(number_format($new_data['paid'],2)) . '</td>'; // paid amount
            $xhtml .= '<td>' . htmlspecialchars(number_format($new_data['balance'],2)) . '</td>'; // pending balance
            $xhtml .= '<td>' . htmlspecialchars($func->formateDate($new_data['startlease'],"m-d-Y")) . '</td>';
            $xhtml .= '<td>' . htmlspecialchars($func->formateDate($new_data['endlease'],"m-d-Y")) . '</td>';
            $xhtml .= '</tr>';
        }
        // die();
        $response = [
            'bool' => true,
            'html' => $xhtml,
            'msg' => ''
        ];
    }

}

if ($event_action == 'view_room') {
    $roomid = $_POST['roomid'];
    $chkroom = $func->FetchSingle($connect,"roomfile0","WHERE roomid = ?",[$roomid]);
}

if ($event_action == 'view_pending_request') {
    $chk_pending = $func->FetchAll($connect,"roompendingrequest","*","WHERE request_status <> 1");

    if(count($chk_pending) > 0){
        foreach ($chk_pending as $pending) {

            $new_data = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$pending['usr_cde']]);
            $gender = $new_data['usr_sex'] == "Prefer not to say" ? "" : $new_data['usr_sex'];
            $xhtml .= '<tr>';
            $xhtml .= '<td>' . htmlspecialchars($new_data['usr_fname'] .' '.$new_data['usr_lname']) . '</td>';
            $xhtml .= '<td>' . htmlspecialchars($new_data['usr_brgy'] .', '.$new_data['usr_municipality'].', '.$new_data['usr_province']) . '</td>';
            $xhtml .= '<td>' . htmlspecialchars($gender) . '</td>';
            $xhtml .= '<td>' . htmlspecialchars($pending['roomnum']) . '</td>';
            $xhtml .= '<td>' . htmlspecialchars($pending['bedspacenum']) . '</td>';
            $xhtml .= '<td>' . htmlspecialchars(number_format($pending['deposit'],2)) . '</td>';
            $xhtml .= '<td>' . htmlspecialchars($func->formateDate($pending['startlease'],"m-d-Y")) . '</td>';
            $xhtml .= '<td>' . htmlspecialchars($func->formateDate($pending['endlease'],"m-d-Y")) . '</td>';

            $xhtml .= '<td>';
            $xhtml .= '<select class="action-select" data-user-id="' . htmlspecialchars($new_data['usr_cde']) . '" data-room-num="' . htmlspecialchars($pending['roomnum']) . '" data-roomid="' . htmlspecialchars($pending['roomid']) . '" data-recid="' . htmlspecialchars($pending['recid']) . '">';
            $xhtml .= '<option value="">Select</option>';
            $xhtml .= '<option value="approve">Approve</option>';
            $xhtml .= '</select>';
            $xhtml .= '</td>';

            $xhtml .= '</tr>';
        }
        $response = [
            'bool' => true,
            'html' => $xhtml,
            'msg' => ''
        ];
    }
}

if ($event_action == 'approver') {
    $usrcde  = $_POST['usrcde'];
    $roomnum = $_POST['roomnum'];
    $roomid = $_POST['roomid'];
    $par     = $_POST['action'];


    if(strtoupper($par) == 'APPROVE') {
        $isFull = false;
        $roomcapacity = $func->FetchSingle($connect,"roomfile","WHERE roomid = ?",[$roomid])['roomcapacity'];
        $old_tenant_count = $func->FetchSingle($connect,"roomfile","WHERE roomid = ?",[$roomid])['current_tenants'];
        if($roomcapacity == $old_tenant_count){
            $isFull = true;
        }

        $chk = $func->FetchSingle($connect,"roompendingrequest","WHERE usr_cde = ? AND roomid = ? AND roomnum = ?",[$usrcde,$roomid,$roomnum]);
        $isOccupied = $func->FetchSingle($connect,"roomfile0","WHERE roomid = ? AND roomnum = ?",[$roomid,$roomnum]);

        if(count($chk) > 0 && !$isFull && $isOccupied['roomstat'] != 'occupied'){
            $params = [
                'usr_status'  => 1,
                'roomid'      => $chk['roomid'],
                'roomnum'     => $chk['roomnum'],
                'deposit'     => $chk['deposit'],
                'balance'     => $chk['balance'],
                'bedspacenum' => $chk['bedspacenum'],
                'startlease'  => $chk['startlease'],
                'endlease'    => $chk['endlease']
            ];

            $result = $func->UpdateRecord($connect,"userfile","WHERE usr_cde = ?",$params,[$chk['usr_cde']]);

            if($result['bool']){
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                if($old_tenant_count + 1 == $roomcapacity){
                    $params2['roomstat'] = 'occupied';
                }

                $params2['current_tenants'] = $old_tenant_count + 1;
                $func->UpdateRecord($connect,"roomfile","WHERE roomid = ?",$params2,[$chk['roomid']]);
    
                $params3['usr_cde'] = $chk['usr_cde'];
                $params3['current_tenants'] = 1;
                $params3['roomstat'] = 'occupied';
                $func->UpdateRecord($connect,"roomfile0","WHERE roomid = ? AND roomnum = ? AND bedspacenum = ?",$params3,[$chk['roomid'],$chk['roomnum'],$chk['bedspacenum']]);

                $params4['roomid']          = $chk['roomid'];
                $params4['usr_cde']         = $chk['usr_cde'];
                $params4['roomnum']         = $chk['roomnum'];
                $params4['roomcapacity']    = $roomcapacity;
                $params4['current_tenants'] = 1;
                $params4['roomstat']        = 'occupied';
                $params4['startlease']      = $chk['startlease'];
                $params4['endlease']        = $chk['endlease'];
                $params4['bedspacename']    = $chk['bedspacenum'];

                $params4 = [
                    'roomid'          => $chk['roomid'],
                    'usr_cde'         => $chk['usr_cde'],
                    'roomnum'         => $chk['roomnum'],
                    'roomcapacity'    => $roomcapacity,
                    'current_tenants' => 1,
                    'roomstat'        => 'occupied',
                    'startlease'      => $chk['startlease'],
                    'endlease'        => $chk['endlease'],
                    'bedspacenum'    => $chk['bedspacenum'],
                ];

                $func->InsertRecord($connect,"roomfile1",$params4);

                $params5['request_status'] = 1;
                $func->UpdateRecord($connect,"roompendingrequest","WHERE roomid = ? AND roomnum = ? AND bedspacenum = ? AND recid = ?",$params5,[$chk['roomid'],$chk['roomnum'],$chk['bedspacenum'],$_POST['recid']]);

                //? EMAIL NOTIFICATION FOR REQUEST ROOM APPROVAL
                $recipientEmail = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$chk['usr_cde']])['usr_email'];
                $recipientName  = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$chk['usr_cde']]);
                $crn            = $recipientName['usr_fname'] .' '.$recipientName['usr_lname'];
                $subject        = "Room Request Approval";
                $message        = "Your request from ".$chk['roomnum']." and Bed Space No. ".$chk['bedspacenum']." is approved.";
                $result         = $func->sendEmailNotification($recipientEmail, $crn, $subject, $subject, $message);

                $response = [
                    'bool' => !false,
                    'msg' => ''
                ];
            }
        }

        if($isFull || $isOccupied['roomstat'] == 'occupied'){
            $response = [
                'bool' => false,
                'msg' => 'Room is not available.<br>'
            ];
        }

    }
}


echo json_encode($response);
?>