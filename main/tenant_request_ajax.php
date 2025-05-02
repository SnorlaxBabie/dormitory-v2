<?php 
header('Content-Type: application/json');
require_once '../appconfig.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();


$event_action = $_POST['event_action'];

$response = [
    'bool' => true,
    'msg' => ""
];

if($event_action == "save_data"){
    $request = $_POST['save'];
    $require_field = [
        'Description' => $request['tenantrequest'],
        'Priority' => $request['priority'],
        'Schedule' => $request['schedule']
    ];

    $err_msg = Standard::isFieldRequired($require_field);

    $chkuser = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$_POST['usrcde']]);
    // $xchkcount = $func->FetchAll($connect,"roomfile0","roomnum","WHERE roomnum = ?",[$roomnum]);
    // $xgetroom = $func->FetchSingle($connect,"roomfile","WHERE roomnum = ?",[$roomnum]);

    // if (empty($xgetroom['roomnum'])) { // check room if valid
    //     $response['bool'] = false;
    //     $response['msg'] .= "Room No. ".$roomnum." does not exist in Room Master File.<br>";
    // }

    if (count($err_msg) > 0) {
        $response['bool'] = false;
        foreach ($err_msg as $field => $msg) {
            $response['msg'] .= $msg . "<br>";
        }
    }

    if ($response['bool']) {
        $requestid = $func->genUniqueNumber("REQ");
        $xparams = [
            'requestid'    => trim($requestid),
            'roomid'       => trim($chkuser['roomid']),
            'usr_cde'      => trim($chkuser['usr_cde']),
            'roomnum'      => trim($chkuser['roomnum']),
            'requestprio'  => $request['priority'],
            'description'  => trim($request['tenantrequest']),
            'requestsched' => $func->formateDate($request['schedule'],'Y-m-d'),
            'staffname'    => trim($request['tenantassignedstaff']),
            'reqstatus'    => 'Pending',
        ];

        if($_POST['par'] == 'edit'){
            $result = $func->UpdateRecord($connect, "tenantrequest","WHERE requestid = ?", $xparams,[$_POST['reqid']],!true);
        }else{
            $result = $func->InsertRecord($connect, "tenantrequest", $xparams);
            //? EMAIL NOTIFICATION FOR REQUEST
            $recipientEmail = $func->FetchSingle($connect,"userfile","WHERE usr_lvl = 'ADMIN'")['usr_email'];
            $recipientName  = $func->FetchSingle($connect,"userfile","WHERE usr_lvl = 'ADMIN'",[$chkuser['usr_cde']]);
            $crn            = $recipientName['usr_fname'];
            $subject        = "Tenant Maintenant & Repair Request.";
            $message        = "".$request['tenantrequest']."";
            $result         = $func->sendEmailNotification($recipientEmail, $crn, $subject, $subject, $message);
        }

        if($result['bool']){
            $response['bool'] = !false;
            $response['msg'] .= "Request successfully added.<br>";
        }
    }

}

if ($event_action == "get_request") {
    $response = [
        'bool' => false,
        'msg' => "No result found",
        'events' => []
    ];

    $usrcde = $_POST['usrcde'];
    $get_tenant_request = $func->FetchAll($connect, "tenantrequest", "*");
    // $get_tenant_request = $func->FetchAll($connect, "tenantrequest", "*", "WHERE usr_cde = ?", [$usrcde]);
    if (!empty($get_tenant_request)) {
        $response['bool'] = true;
        $response['msg'] = "";

        foreach ($get_tenant_request as $tenantrequest) {
            $get_user = $func->FetchSingle($connect, "userfile", "WHERE usr_cde = ?", [$tenantrequest['usr_cde']]);
            $fullname = $get_user['usr_fname'] . ' ' . $get_user['usr_lname'];

            $response['events'][] = [
                'usrcde' => trim($get_user['usr_cde']),
                'title' => trim($fullname),
                'start' => $func->formateDate($tenantrequest['requestsched'],'Y-m-d'),
                'requestid' => $tenantrequest['requestid'],
                'status' => $tenantrequest['reqstatus'],
            ];
        }
    }

}

if ($event_action == "view_request") {
    $response = [
        'bool' => false,
        'msg' => "No result found"
    ];
    $usrcde = $_POST['usrcde'];
    $reqid = $_POST['reqid'];
    $getreq = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?", [$usrcde]);
    $fulname = $getreq['usr_fname'] . ' ' . $get_user['usr_lname'];
    $result = $func->FetchSingle($connect,"tenantrequest","WHERE usr_cde = ? AND requestid = ?", [$usrcde,$reqid]);
    if(!empty($result['requestid'])){
        $response = [
            'bool' => !false,
            'msg' => "success",
            'description' => trim($result['description']),
            'priority' => $result['requestprio'],
            'schedule' => $func->formateDate($result['requestsched'],'m-d-Y'),
            'staff' => trim($result['staffname'])
        ];
    }
}

if($event_action == "del_data"){
    $response['bool'] = false;
    $response['msg'] = "Unable to delete this user" . "<br>";
    $result = $func->DeleteRecord($connect,"tenantrequest","WHERE requestid = ?",[$_POST['reqid']]);
    
    if($result['bool']){
        $response['bool'] = true;
        $response['msg'] = "Data has been successfuly deleted." . "<br>";
    }
}





echo json_encode($response);
?>