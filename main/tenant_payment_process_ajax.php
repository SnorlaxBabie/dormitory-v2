<?php 
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
require_once '../appconfig.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
require_once '../vendor/autoload.php';

use GuzzleHttp\Client;
$func = new Funcshits();
$params_query = "SELECT * FROM standardparameter";
$stmt = $connect->prepare($params_query);
$stmt->execute();
$parameters = $stmt->fetch(2);
$skey = $parameters['gcash_api'];
$event_action = $_POST['event_action'];

$response = [
    'bool' => true,
    'msg' => ""
];

if($event_action == "save_data"){
    $method = $_POST['paymentMethod'];
    $get_val = $func->FetchSingle($connect,"userfile","WHERE usr_cde = ?",[$_SESSION['usr_cde']]);

    $require_field = [
        'Amount' => $_POST['amount']
        // 'Payment Method' => $_POST['paymentMethod']
    ];

    $err_msg = Standard::isFieldRequired($require_field);

    if (isset($_FILES['save']['name']['proofpayment']) && $_FILES['save']['error']['proofpayment'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['save']['tmp_name']['proofpayment'];
        $fileName = $_FILES['save']['name']['proofpayment'];
        $fileSize = $_FILES['save']['size']['proofpayment'];
        $fileType = $_FILES['save']['type']['proofpayment'];

        $uploadFileDir = './upload/proofpayment/';
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
            $response['msg'] = "" . "<br>";
        } else {
            $response['bool'] = false;
            $response['msg'] = 'error uploaded file.' . "<br>";
        }
    }
        
    if($_POST['amount'] < 100){
        $response['bool'] = false;
        $response['msg'] .= "Minimum amount is 100.<br>";
    }
    
    if ($_POST['amount'] > $get_val['balance']) {
        $response['bool'] = false;
        $response['msg'] .= "The amount entered exceeds your available balance of <strong>" . number_format($get_val['balance'], 2) . "</strong>. Please enter a value less than or equal to your balance.<br>";
    }

    if (count($err_msg) > 0) {
        $response['bool'] = false;
        foreach ($err_msg as $field => $msg) {
            $response['msg'] .= $msg . "<br>";
        }
    }

    $due_date = $func->formateDate($get_val['due_date'],'Y-m-d');
    $date_today = date('Y-m-d');
    $new_bal = $get_val['balance'] - $_POST['amount'];
    $status = $date_today > $due_date ? "Overdue" : ($new_bal != 0 ? "Partial" : "Paid");

    if($method == 'G-Cash'){
        $amt = $_POST['amount'] * 100;
        $cemail = $parameters['companyemail'];
        $fullname = $_SESSION['full_name'];
        $email = $_SESSION['user_email'];
        $contact = $get_val['usr_contactnum'];
        $brgy = $get_val['usr_brgy'];
        $municipality = $get_val['usr_municipality'];
        $province = $get_val['usr_province'];

        $res = GCASH_FUNC($amt,$cemail,$fullname,$email,$contact,$brgy,$municipality,$province,$skey);

        if(!empty($res['checkout_url'])){

            $_SESSION['roomid']         = $get_val['roomid'];
            $_SESSION['roomnum']        = $get_val['roomnum'];
            $_SESSION['due_date']       = $due_date;
            $_SESSION['prev_balance']   = $get_val['balance'];
            $_SESSION['balance']        = $new_bal;
            $_SESSION['amount_paid']    = $_POST['amount'];
            $_SESSION['status']         = $status;
            $_SESSION['method']         = $_POST['paymentMethod'];

            $checkout_url = $res['checkout_url'];
            $response['bool'] = true;
            $response['msg'] = '';
            $response['url'] = $checkout_url;
            echo json_encode($response);
            return $response;
        }
    }

    if ($response['bool']) {
        $requestid = $func->genUniqueNumber("PAY");
        $newFileName = $newFileName ? $newFileName : null;
        $xparams = [
            'transaction_id' => trim($requestid),
            'usr_cde'        => trim($get_val['usr_cde']),
            'roomid'         => trim($get_val['roomid']),
            'roomnum'        => trim($get_val['roomnum']),
            'payment_date'   => $date_today,
            'due_date'       => $due_date,
            'prev_balance'   => $get_val['balance'],
            'balance'        => $new_bal,
            'amount_paid'    => trim($_POST['amount']),
            'status'         => $status,
            'method'         => $_POST['paymentMethod'],
            'proofpayment'   => $newFileName, //
        ];

        $upt_params = [
            'balance' => $new_bal,
        ];

        $edit_upt_params = [
            'proofpayment' => $newFileName,
        ];

        if($_POST['par'] == 'edit'){
            $result = $func->UpdateRecord($connect, "payments","WHERE transaction_id = ?", $edit_upt_params,[$_POST['transacid']],!true);
        }else{
            $func->UpdateRecord($connect, "userfile","WHERE usr_cde = ?", $upt_params,[$get_val['usr_cde']],!true);
            $result = $func->InsertRecord($connect, "payments", $xparams);
        }
         
        $msg = $_POST['par'] == 'edit' ? "Proof of payment successfuly save." : "Payment successfully added.";

        if($result['bool']){
            $response['bool'] = !false;
            $response['url'] = '';
            $response['msg'] .= "".$msg."<br>";
        }
    }
}

if($event_action == "view_data"){
    $response['bool'] = true;
    $response['msg'] = "No Data Found" . "<br>";

    $result = $func->FetchSingle($connect,"payments","WHERE transaction_id = ?",[$_POST['transac_id']]);


    if(count($result['recid']) > 0){
        $response['bool'] = true;
        $response['msg'] = '';
        $response['amount_paid'] = number_format($result['amount_paid'],2);
        $response['method'] = $result['method'];
        $response['proofpayment'] = $result['proofpayment'];
    }
}


function GCASH_FUNC($amt,$comp_email,$fullname,$email,$contactnum,$brgy,$municipality,$province,$skey){
    $client = new Client();
    $BASE_URL = 'http://' .$_SERVER['HTTP_HOST'];
    $FILE_NAME = '/tenant_payment_process_success.php';
    $STR_DIR = __DIR__;
    $PATH = str_replace('\\', '/', $STR_DIR);
    $PATH = str_replace($_SERVER['DOCUMENT_ROOT'], '', $PATH);
    $URL = $BASE_URL . $PATH . $FILE_NAME;
    try {
        $response = $client->request('POST', 'https://api.paymongo.com/v1/payment_intents', [
            'json' => [
                'data' => [
                    'attributes' => [
                        'amount' => $amt, // amountin centavos amount *  100
                        'payment_method_allowed' => ['gcash'],
                        'currency' => 'PHP',
                        'description' => 'Payment for balance', // payment description
                        'statement_descriptor' => $comp_email,
                        'capture_type' => 'automatic',
                        'hakdog' => 'automatic'
                    ]
                ]
            ],
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic ' . base64_encode($skey . ':'),
                'content-type' => 'application/json'
            ],
        ]);
        $paymentIntent = json_decode($response->getBody(), true);
        $paymentIntentId = $paymentIntent['data']['id'];

        $response = $client->request('POST', 'https://api.paymongo.com/v1/payment_methods', [
            'json' => [
                'data' => [
                    'attributes' => [
                        'type' => 'gcash',
                        'billing' => [
                            'name' => $fullname, 
                            'email' => $email,
                            'phone' => $contactnum,
                            'address' => [
                                'line1' => $brgy, // house no. include brgy address
                                'line2' => '',  // street address
                                'city' => $municipality,   // ex. san felipe
                                'state' => $province, // zambales
                                'postal_code' => '',
                                'country' => 'PH' // philippines
                            ]
                        ]
                    ]
                ]
            ],
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic ' . base64_encode($skey . ':'),
                'content-type' => 'application/json'
            ],
        ]);
    
        $paymentMethod = json_decode($response->getBody(), true);
    
        $paymentMethodId = $paymentMethod['data']['id'];

        $response = $client->request('POST', "https://api.paymongo.com/v1/payment_intents/$paymentIntentId/attach", [
            'json' => [
                'data' => [
                    'attributes' => [
                        'payment_method' => $paymentMethodId,
                        'return_url' => $URL,
                    ]
                ]
            ],
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic ' . base64_encode($skey . ':'),
                'content-type' => 'application/json'
            ],
        ]);
        
        $result = json_decode($response->getBody(), true);
        $checkoutUrl = $result['data']['attributes']['next_action']['redirect']['url'];
        $paymentId = $result['data']['attributes']['payments'][0]['id'] ?? null;

        return $response = [
            'checkout_url' => $checkoutUrl,
            'payment_id' => $paymentId,         // This will be in format: pay_XXXXXXXX
            'payment_intent_id' => $paymentIntentId,  // This will be in format: pi_XXXXXXXX
            'payment_method_id' => $paymentMethodId   // This will be in format: pm_XXXXXXXX
        ];
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
echo json_encode($response);
?>