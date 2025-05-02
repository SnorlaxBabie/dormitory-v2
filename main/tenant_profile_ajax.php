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

if($event_action == "save_pasword"){
    $par = $_POST['par'];
    $profile = $_POST['profile'];

    if($par == 'changepass'){
        $params['usr_pwd'] = sha1($profile['password']);
        $result = $func->UpdateRecord($connect,"userfile","WHERE usr_cde = ?",$params,[$_POST['usrcde']]);

        if($result['bool']){
            $response['bool'] = true;
            $response['msg'] .= "Change password successful.<br>";
        }
    }else{
        $require_field = [
            'First Name'     => $profile['firstName'],
            'Last Name'      => $profile['lastName'],
            'Gender'         => $profile['sex'],
            'Email'          => $profile['email'],
            'Contact No.'    => $profile['phone'],
            'Barangay'       => $profile['barangay'],
            'Municipality'   => $profile['municipality'],
            'Province'       => $profile['province'],
            'Emergency Name' => $profile['efullname'],
            'Relationship'   => $profile['erelationship'],
            'Address'        => $profile['eaddress'],
            'Contact No.'    => $profile['econtactnum'],
            'Home No.'       => $profile['ehome'],
            'Work No.'       => $profile['ework'],
        ];

        $err_msg = Standard::isFieldRequired($require_field);

        if (count($err_msg) > 0) {
            $response['bool'] = false;
            foreach ($err_msg as $field => $msg) {
                $response['msg'] .= $msg . "<br>";
            }
        }

        if ($response['bool']) {

            $params = [
                'usr_fname'        => $profile['firstName'],
                'usr_mname'        => $profile['middleName'],
                'usr_lname'        => $profile['lastName'],
                'usr_sex'          => $profile['sex'],
                'usr_contactnum'   => $profile['phone'],
                'usr_email'        => $profile['email'],
                'usr_brgy'         => $profile['barangay'],
                'usr_municipality' => $profile['municipality'],
                'usr_province'     => $profile['province'],
                'eci_fullname'     => $profile['efullname'],
                'eci_relationship' => $profile['erelationship'],
                'eci_address'      => $profile['eaddress'],
                'eci_contactnum'   => $profile['econtactnum'],
                'eci_homenum'      => $profile['ehome'],
                'eci_worknum'      => $profile['ework']
            ];

            $result = $func->UpdateRecord($connect,"userfile","WHERE usr_cde = ?",$params,[$_POST['usrcde']]);
    
            if($result['bool']){
                $response['bool'] = true;
                $response['msg'] .= "Update successful.<br>";
            }
        }
    }
}




echo json_encode($response);
?>