<?php 
header('Content-Type: application/json');
require_once '../appconfig.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$event_action = $_POST['event_action'];

$response = [
    'bool' => true,
    'msg' => ''
];

if($event_action == "save_data"){
    $announcement = $_POST['save'];
    $require_field = [
        'Title'         => $announcement['title'],
        'Description'   => $announcement['content'],
        'Schedule From' => $announcement['start_date'],
        'Schedule To'   => $announcement['end_date']
    ];

    if($announcement['start_date'] > $announcement['end_date']){
        $response['bool'] = false;
        $response['msg'] .= "The Schedule From cannot be greater than the Schedule To." . "<br>";
    }

    $err_msg = Standard::isFieldRequired($require_field);

    if(count($err_msg) > 0){
        $response['bool'] = false;
        foreach ($err_msg as $field => $msg) {
            $response['msg'] .= $msg . "<br>";
        }
    }else{
        $xparams = [];

        $xparams = [
            'title'      => $announcement['title'],
            'content'    => $announcement['content'],
            'usr_cde'    => $_SESSION['usr_cde'],
            'start_date' => $func->formateDate($announcement['start_date'],'Y-m-d'),
            'end_date'   => $func->formateDate($announcement['end_date'],'Y-m-d')
        ];
        // $response = Standard::regexValidation($xparams);
     
        if(!$response['bool']){
            $response['bool'] = false;
            $response['msg'] = $response['msg'] . "<br>";
        }else{

            if($_POST['par'] == 'edit'){
                $response = $func->UpdateRecord($connect, "announcements","WHERE recid = ?", $xparams,[$_POST['recid']],!true);
            }else{
                $response = $func->InsertRecord($connect, "announcements", $xparams);
            }
            
            $xmsg = $_POST['par'] == 'edit' ? 'update' : 'saved';
            if($response['bool']){
                $response['msg'] = "Announcement {$xmsg} successfully.<br>";
            }
        }
    }
}

if($event_action == "view_data"){
    $response['bool'] = true;
    $response['msg'] = "No Data Found" . "<br>";

    $result = $func->FetchSingle($connect,"announcements","WHERE recid = ?",[$_POST['recid']]);

    if(count($result['recid']) > 0){
        $response['bool'] = true;
        $response['msg'] = '';
        $response['title'] = $result['title'];
        $response['content'] = $result['content'];
        $response['start_date'] = $func->formateDate($result['start_date'],'m-d-Y');
        $response['end_date'] = $func->formateDate($result['end_date'],'m-d-Y');
    }
}

if($event_action == "del_data"){
    $response['bool'] = false;
    $response['msg'] = "Unable to delete this user" . "<br>";
    $result = $func->DeleteRecord($connect,"announcements","WHERE recid = ?",[$_POST['recid']]);
    
    if($result['bool']){
        $response['bool'] = true;
        $response['msg'] = "Announcement has been successfuly deleted." . "<br>";
    }
}

echo json_encode($response);
?>