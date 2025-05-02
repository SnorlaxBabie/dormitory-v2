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

if($event_action == "save_data"){
    $staff = $_POST['save'];
    $require_field = [
        'Name'        => $staff['staff_name'],
        'Age'         => $staff['staff_age'],
        'Contact No.' => $staff['staff_contact'],
        'Position'    => $staff['staff_email']
    ];

    if (isset($_FILES['save']['name']['staff_image']) && $_FILES['save']['error']['staff_image'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['save']['tmp_name']['staff_image'];
        $fileName = $_FILES['save']['name']['staff_image'];
        $fileSize = $_FILES['save']['size']['staff_image'];
        $fileType = $_FILES['save']['type']['staff_image'];

        $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes

        if ($fileSize > $maxFileSize) {
            echo json_encode(['success' => false, 'message' => 'File size exceeds 2MB.']);
            exit;
        }

        $uploadFileDir = './upload/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $timestamp = date('Ymd_His'); 
        $fileBaseName = pathinfo($fileName, PATHINFO_FILENAME);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        $newFileName = "{$fileBaseName}_{$timestamp}.{$fileExtension}";
        $dest_path = $uploadFileDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $response['bool'] = true;
            $response['msg'] = $response['msg'] . "<br>";
        } else {
            $response['bool'] = false;
            $response['msg'] = 'There was an error moving the uploaded file.' . "<br>";
        }
    } else {
        $response['bool'] = false;
        $newFileName = 'default_avatar.jpg';
    }

    $err_msg = Standard::isFieldRequired($require_field);

    if(count($err_msg) > 0){
        $response['bool'] = false;
        foreach ($err_msg as $field => $msg) {
            $response['msg'] .= $msg . "<br>";
        }
    }else{
        $xparams = [];
        $ximage = $func->FetchSingle($connect,"stafffile","WHERE recid = ?",[$_POST['recid']]);
        $newFileName = count($ximage['staffimage']) > 0 && empty($_FILES['save']['name']['staff_image']) ? $ximage['staffimage'] : $newFileName;
        $xparams = [
            'staffname'     => $staff['staff_name'],
            'staffage'      => $staff['staff_age'],
            'staffcontact'  => $staff['staff_contact'],
            'staffemail'    => $staff['staff_email'],
            'staffposition' => $staff['staff_position'],
            'staffimage'    => $newFileName
        ];
        
        $response = Standard::regexValidation($xparams);

        if(!$response['bool']){
            $response['bool'] = false;
            $response['msg'] = $response['msg'] . "<br>";
        }else{

            if($_POST['par'] == 'edit'){
                $response = $func->UpdateRecord($connect, "stafffile","WHERE recid = ?", $xparams,[$_POST['recid']],!true);
            }else{
                $response = $func->InsertRecord($connect, "stafffile", $xparams);
            }
            $xmsg = $_POST['par'] == 'edit' ? 'update' : 'saved';
            if($response['bool']){
                $response['msg'] = "Staff {$xmsg} successfully.<br>";
            }
        }
    }
}

if($event_action == "view_data"){
    $response['bool'] = true;
    $response['msg'] = "No Data Found" . "<br>";

    $result = $func->FetchSingle($connect,"stafffile","WHERE recid = ?",[$_POST['recid']]);
    if(count($result['recid']) > 0){
        $response['bool'] = true;
        $response['msg'] = '';
        $response['name'] = $result['staffname'];
        $response['age'] = $result['staffage'];
        $response['contact'] = $result['staffcontact'];
        $response['email'] = $result['staffemail'];
        $response['position'] = $result['staffposition'];
        $response['image'] = $result['staffimage'];
    }
}

if($event_action == "del_data"){
    $response['bool'] = false;
    $response['msg'] = "Unable to delete this user" . "<br>";
    $result = $func->DeleteRecord($connect,"stafffile","WHERE recid = ?",[$_POST['recid']]);
    
    if($result['bool']){
        $response['bool'] = true;
        $response['msg'] = "Data has been successfuly deleted." . "<br>";
    }
}

echo json_encode($response);
?>