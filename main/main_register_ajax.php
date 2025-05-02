<?php 
header('Content-Type: application/json');
require_once '../appconfig.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$params_query = "SELECT * FROM standardparameter";
$stmt = $connect->prepare($params_query);
$stmt->execute();
$parameters = $stmt->fetch(2);

$event_action = $_POST['event_action'];

$response = [
    'bool' => true,
    'msg' => ''
];

if ($event_action == "save_data"){
    $register = $_POST['save'];
    $require_field = [
        'First Name'            => $register['first_name'],
        'Last Name'             => $register['last_name'],
        'Personal Contact No.'  => $register['contact'],
        'Email Address'         => $register['email'],
        'Username'              => $register['username'],
        'Password'              => $register['password'],
        'Confirm Password'      => $register['confirmpasword'],
        'Barangay'              => $register['barangay'],
        'Municipality'          => $register['municipality'],
        'Province'              => $register['province'],
        'Full Name'             => $register['eci_fullname'],
        'Relationship'          => $register['eci_relationship'],
        'Emergency Contact No.' => $register['eci_contact'],
        'Home No.'              => $register['eci_homenum'],
        'Work No.'              => $register['eci_worknum'],
        'Address'               => $register['eci_address'],
        'Deposit'               => $register['deposit'],
        'Room No.'              => $register['roomnum'],
        'Bedspace No.'          => $register['bedspace'],
        'Start of Lease'        => $register['startlease'],
        'End of Lease'          => $register['endlease']
    ];
    $err_msg = Standard::isFieldRequired($require_field);
    $chkemail = $func->FetchSingle($connect,"userfile","WHERE usr_email = ?",[$register['email']])['usr_email'];
    $chkusername = $func->FetchSingle($connect,"userfile","WHERE usr_name = ?",[$register['username']],"","*",!true)['usr_name'];

    if(count($chkusername)){
        $response['bool'] = false;
        $response['msg'] .= "Username is already exists.<br>";
    }

    if(count($chkemail)){
        $response['bool'] = false;
        $response['msg'] .= "Email address is already exists.<br>";
    }

    if (isset($register['password']) && isset($register['confirmpasword'])) {
        if ($register['password'] !== $register['confirmpasword']) {
            $response['bool'] = false;
            $response['msg'] .= "Password and Confirm password does not match.<br>";
        }
    }

    if(empty($register['termsCheck'])){
        $response['bool'] = false;
        $response['msg'] .= "Terms and Condition is required.<br>";
    }

    if(count($err_msg) > 0){
        $response['bool'] = false;
        foreach ($err_msg as $field => $msg) {
            $response['msg'] .= $msg . "<br>";
        }
    }else{
        if($response['bool']){
            $xuniqueCode = $func->genUniqueNumber($register['first_name']);
            $xparams = [];
            $xparams = [
                'usr_cde'              => $xuniqueCode,
                'usr_fname'            => $register['first_name'],
                'usr_mname'            => $register['middle_name'],
                'usr_lname'            => $register['last_name'],
                'usr_sex'              => $register['gender'],
                'usr_contactnum'       => $register['contact'],
                'usr_email'            => $register['email'],
                'usr_name'             => $register['username'],
                'usr_pwd'              => sha1($register['password']),
                'usr_brgy'             => $register['barangay'],
                'usr_municipality'     => $register['municipality'],
                'usr_province'         => $register['province'],
                'usr_havemedcondition' => Standard::isPostEmpty($register['haveCondition']) ? 0 : 1,
                'eci_fullname'         => $register['eci_fullname'],
                'eci_relationship'     => $register['eci_relationship'],
                'eci_contactnum'       => $register['eci_contact'],
                'eci_homenum'          => $register['eci_homenum'],
                'eci_worknum'          => $register['eci_worknum'],
                'eci_address'          => $register['eci_address'],
                'usr_status'           => 0, // default 0 until landlord is approved your request
                'deposit'              => null, // default null until landlord is approved your request
                'roomnum'              => null, // default null until landlord is approved your request
                'balance'              => null, // default null until landlord is approved your request
                'bedspacenum'          => null, // default null until landlord is approved your request
                'startlease'           => null, // default null until landlord is approved your request
                'endlease'             => null, // default null until landlord is approved your request
                'roomid'               => null, // default null until landlord is approved your request
                'due_date'             => $func->calculateDueDate($func->formateDate($register['startlease'],'Y-m-d'),$parameters['days_due_date']),
                'trmscheck'            => Standard::isPostEmpty($register['termsCheck']) ? 0 : 1
            ];


            $xparams2 = [
                'usr_status'           => 0,
                'usr_cde'              => $xuniqueCode,
                'deposit'              => $register['deposit'],
                'roomnum'              => $register['roomnum'],
                'balance'              => $register['roomamount'],
                'bedspacenum'          => $register['bedspace'],
                'startlease'           => $func->formateDate($register['startlease'],'Y-m-d'),
                'endlease'             => $func->formateDate($register['endlease'],'Y-m-d'),
                'roomid'               => $register['roomid'],
            ];

            $response = Standard::regexValidation($xparams);

            if($response['bool']){
                Standard::InsertRecord($connect, "roompendingrequest", $xparams2);
                $response = Standard::InsertRecord($connect, "userfile", $xparams);

                $response['bool'] = true;
                $response['msg'] = "Registration success." . "<br>";
            }else{
                $response['bool'] = false;
                $response['msg'] = $response['msg'] . "<br>";
            }
        }else{
            $response['bool'] = false;
            $response['msg'] = $response['msg'] . "<br>";    
        }

    }
}

if ($event_action == "login_data"){
    session_start();
    $landlord = $_POST['landlord'];
    $tenant   = $_POST['tenant'];
    $xpar = $_POST['xpar'];

    $username = $xpar == 'landlord' ? $landlord['username'] : $tenant['username'];
    $password = $xpar == 'landlord' ? $landlord['password'] : $tenant['password'];

    $hashed_password = sha1($password);

    $sql = "SELECT * FROM userfile WHERE usr_name = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$username]);
    $userfile = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['first_name'] = $userfile['usr_fname'];
    $_SESSION['last_name'] = $userfile['usr_lname'];
    $_SESSION['full_name'] = $userfile['usr_fname'].' '.$userfile['usr_lname'];
    $_SESSION['username'] = $userfile['usr_name'];
    $_SESSION['user_email'] = $userfile['usr_email'];
    $_SESSION['usr_lvl'] = $userfile['usr_lvl'];
    $_SESSION['usr_cde'] = $userfile['usr_cde']; 
    $_SESSION['is_logged_in'] = true;

    $is_login = validate_login($username, $hashed_password);
    if ($is_login && $xpar == "landlord" && $userfile['usr_lvl'] == "ADMIN") {
        $response['bool'] = true;
        $response['msg'] = "Login Successfully<br>";
        $response['usrlvl'] = "admin";
    } else {
        if ($is_login && $xpar == "tenant" && $userfile['usr_lvl'] != "ADMIN") {
            $response['bool'] = true;
            $response['msg'] = "Login Successfully<br>";
            $response['usrlvl'] = "user";
        }else{            
            $response['bool'] = false;
            $response['msg'] = "Invalid credentials.<br>";
        }
    }
}

if ($event_action == "go_logout"){
    session_start();
    session_unset();
    session_destroy();
    $response['bool'] = true;
    $response['msg'] = "Logout Success.<br>";
}

function validate_login($username, $hashed_password) {
    $stored_password = get_stored_password($username);
    return $stored_password === $hashed_password ? true : false;
}
function get_stored_password($username) {
    global $connect;
    $sql = "SELECT * FROM userfile WHERE usr_name = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$username]);
    $userfile = $stmt->fetch(PDO::FETCH_ASSOC);

    return $userfile['usr_pwd'];
}
echo json_encode($response);
?>