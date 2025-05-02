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

if($event_action == "view_tenant"){
    $response = [
        'bool' =>!true,
        'msg' => ''
    ];

    $usrcde = $_POST['usrcde'];
    $chkuser = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$usrcde]);

    if(count($chkuser['usr_cde']) > 0){
        $xparams = [
            'name'        => $chkuser['usr_fname']." ".$chkuser['usr_lname'],
            'gender'      => $chkuser['usr_sex'],
            'username'    => $chkuser['usr_name'],
            'contactnum'  => $chkuser['usr_contactnum'],
            'address'     => $chkuser['usr_brgy'] ." ".$chkuser['usr_municipality'].", ".$chkuser['usr_province'],
            'status'      => $chkuser['usr_status'] == 1 ? 'Active' : 'Inactive',
            'deposit'     => number_format($chkuser['deposit'],2),
            'roomnum'     => $chkuser['roomnum'],
            'bedspacenum' => $chkuser['bedspacenum'],
            'startlease'  => $func->formateDate($chkuser['startlease'],'m-d-Y'),
            'endlease'    => $func->formateDate($chkuser['endlease'],'m-d-Y'),
            'vacated_date'    => $func->formateDate($chkuser['vacated_date'],'m-d-Y')
        ];

        $response = [
            'bool' => !false,
            'msg' => '',
            'data' => $xparams
        ];
    }
}
if($event_action == "save_data"){
    $response = [
        'bool' => !true,
        'msg' => ''
    ];

    $startlease = $func->formateDate($_POST['edt_startlease'],'Y-m-d');
    $endlease = $func->formateDate($_POST['edt_endlease'],'Y-m-d');
    $vacated = $func->formateDate($_POST['vacated'],'Y-m-d');

    $xparams = [
        'startlease' => $startlease,
        'endlease' => $endlease
    ];

    if($vacated != "Invalid date"){
        $xparams['vacated'] = 1;
        $xparams['usr_status'] = 0;
        $xparams['vacated_date'] = $vacated;
    }else{
        $xparams['vacated'] = 0;
        $xparams['usr_status'] = 1;
        $xparams['vacated_date'] = null;
    }

    $result = $func->UpdateRecord($connect,"userfile","WHERE usr_cde = ?",$xparams,[$_POST['usrcde']]);

    if($result['bool']){
        $response['bool'] = !false;
        $response['msg'] .= 'Tenant successfully updated.<br>';
    }
}

echo json_encode($response);
?>
