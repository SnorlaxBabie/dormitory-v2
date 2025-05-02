<?php 
header('Content-Type: application/json');
require_once '../appconfig.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
require_once '../config/sessions.php';

$func = new Funcshits();
$event_action = $_POST['event_action'];
$response = [
    'bool' => true,
    'msg' => ""
];

if($event_action == "sendMessage"){

    $response = [
        'bool' => !true,
        'msg' => "Failed to send message.<br>"
    ];

    $xparams = [
        'sender_id'    => $_SESSION['usr_lvl'] == 'USER' ? $_POST['tenant'] : $_POST['landlord'],
        'receiver_id'  => $_SESSION['usr_lvl'] == 'USER' ? $_POST['landlord'] : $_POST['tenant'],
        'subject'      => trim($_POST['subject']),
        'content'      => $_POST['content']
    ];

    if ($_POST['subject'] == 'Others') {
        $xparams['other'] = $_POST['other'];
    }

    if($_POST['landlord'] == $_SESSION['usr_cde']){
        $xparams['status'] = 1;
    }



    $result = $func->InsertRecord($connect, "messages", $xparams);

    if($result['bool']){
        $response = [
            'bool' => !false,
            'msg' => "Send message.<br>",
            'sender' => $_POST['landlord'],
            'subject' => '',
            'fullname' => '',
            'usrcde' => $_POST['tenant']
        ];
    }
}
if($event_action == "fetchMessage"){
    $landlord_usrcde = $_POST['landlord'] == null ? $_SESSION['usr_cde'] : $_POST['landlord'];
    $tenant_usrcde = $_POST['tenant'] == null ? $_POST['user'] : $_POST['tenant'];
    $query = "SELECT xmsg.*, usr1.usr_fname AS sender_first_name, usr2.usr_fname AS receiver_first_name 
              FROM messages xmsg
              JOIN userfile usr1 ON xmsg.sender_id = usr1.usr_cde
              JOIN userfile usr2 ON xmsg.receiver_id = usr2.usr_cde
              WHERE (xmsg.sender_id = ? AND xmsg.receiver_id = ?) OR (xmsg.sender_id = ? AND xmsg.receiver_id = ?)
              ORDER BY xmsg.created_at DESC";
    $stmt = $connect->prepare($query);

    $stmt->execute([$tenant_usrcde,$landlord_usrcde,$landlord_usrcde,$tenant_usrcde]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $xparams = [
        'status' => 1
    ];

    $func->UpdateRecord($connect,"messages","WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)",$xparams,[$tenant_usrcde,$landlord_usrcde,$landlord_usrcde,$tenant_usrcde]);

    $response = [
        'bool' => true,
        'msg' => $messages
    ];

}

if($event_action == "sendEmail"){
    $response = [
        'bool' => !true,
        'msg'  => "Failed to send email."
    ];
    $xparams = strtolower($_POST['usrlvl']) == 'admin' ? $_POST['tenant_usrcde'] : $_POST['landlord_usrcde'];
    // $recipientEmail = 'test@gmail.com'; //! for testing purpose
    // $recipientName = 'Jonathan'; //! for testing purpose
    $recipientEmail = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$xparams])['usr_email'];

    $recipientName  = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$xparams]);
    $crn            = $recipientName['usr_fname'] .' '.$recipientName['usr_lname'];
    $subject        = $_POST['subject'] == 'Others' ? $_POST['reason'] : $_POST['subject'];
    $message        = $_POST['message'];

    $result         = $func->sendEmailNotification($recipientEmail, $crn, $subject, $subject, $message);
    
    if($result['bool']){
        $response = [
            'bool' => true,
            'msg'  => "Email has been sent in ".$recipientEmail.""
        ];
    }
}

if($event_action == "fetchUsers"){
    $messages = [];
    $result = $func->FetchAll($connect, "userfile", "*", "WHERE usr_lvl <> 'ADMIN'");
    
    foreach ($result as $key => $value) {
        $userMessages = $func->FetchSingle($connect, "messages", "WHERE (sender_id = ? OR receiver_id = ?)", [$value['usr_cde'], $value['usr_cde']], "ORDER BY created_at DESC");
        // if($userMessages){
            $messages[] = [
                'subject'   => $userMessages['subject'],
                'preview'   => $userMessages['content'],
                'sender'    => $userMessages['sender_id'],
                'status'    => $userMessages['status'],
                'fullname'  => $value['usr_fname'] .' '.$value['usr_lname'],
                'isNull'    => $value['usr_cde'],
                'timestamp' => $func->formateDate2($userMessages['created_at']),
            ];
        // }
    }

    $params_query = "SELECT SUM(unread_count) 
                    AS total_unread_count 
                    FROM (SELECT u.usr_cde,COUNT(m.status) 
                    AS unread_count 
                    FROM userfile u 
                    JOIN messages m 
                    ON u.usr_cde = m.sender_id 
                    WHERE m.status = 0 
                    AND u.usr_lvl <> 'ADMIN' 
                    GROUP BY u.usr_cde) 
                    AS unread_counts";
    $stmt = $connect->prepare($params_query);
    $stmt->execute();
    $totalunread = $stmt->fetch(2);

    $response = [
        'bool' => true,
        'msg' => $messages,
        'unread' => $totalunread
    ];
}


echo json_encode($response);
?>