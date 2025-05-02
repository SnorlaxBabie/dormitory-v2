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

if($event_action == "save_parameter"){
    $response = [
        'bool' => !true,
        'msg' => 'Failed to update parameter'
    ];
    $system = $_POST['system'];

    $xparams['title'] = $system['title'];
    $xparams['companyname'] = $system['companyname'];
    $xparams['companyemail'] = $system['companyemail'];
    $xparams['companyaddress'] = $system['companyaddress'];
    $xparams['companycontactnum'] = $system['companycontactnum'];
    $xparams['online_logo'] = $system['online_logo'];
    $xparams['gcash_api'] = $system['gcash_api'];
    $xparams['gmail_username'] = $system['gmail_username'];
    $xparams['gmail_password'] = $system['gmail_password'];
    $xparams['days_due_date'] = $system['duedate'];

    $result = $func->UpdateRecord($connect,"standardparameter","WHERE recid = ?",$xparams,[$system['recid']]);

    if($result['bool']){
        $response = [
            'bool' => true,
            'msg' => 'Standard System parameters updated successfully.<br>'
        ];
    }
}

echo json_encode($response);
?>