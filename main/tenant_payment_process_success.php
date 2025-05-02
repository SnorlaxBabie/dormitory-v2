<?php 
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
require_once '../appconfig.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
require_once '../vendor/autoload.php';

$func = new Funcshits();
$params_query = "SELECT * FROM standardparameter";
$stmt = $connect->prepare($params_query);
$stmt->execute();
$parameters = $stmt->fetch(2);
$skey = $parameters['gcash_api'];
use GuzzleHttp\Client;

$client = new Client();

$payment_intent_id = $_GET['payment_intent_id'];
try {
    $response = $client->request('GET', "https://api.paymongo.com/v1/payment_intents/$payment_intent_id", [
        'headers' => [
            'accept' => 'application/json',
            'authorization' => 'Basic ' . base64_encode($skey . ':'),
        ],
    ]);

    $paymentIntentDetails = json_decode($response->getBody(), true);
    $status = $paymentIntentDetails['data']['attributes']['status'];
    $payment_id = $paymentIntentDetails['data']['attributes']['payments'][0]['id'];
    $amount = $paymentIntentDetails['data']['attributes']['payments'][0]['attributes']['amount'] / 100;


// echo '<pre>';var_dump('hereee 1',$_SESSION);die();
// $status === 'succeeded' ? header('Location: success.php') : header('Location: failed.php');

    if ($status === 'succeeded') {

        $xparams = [
            'transaction_id' => trim($payment_id),
            'usr_cde'        => trim($_SESSION['usr_cde']),
            'roomid'         => trim($_SESSION['roomid']),
            'roomnum'        => trim($_SESSION['roomnum']),
            'payment_date'   => date('Y-m-d'),
            'due_date'       => $_SESSION['due_date'], //? change to due date, this date today is for testing purposes only
            'prev_balance'   => $_SESSION['prev_balance'],
            'balance'        => $_SESSION['balance'],
            'amount_paid'    => $_SESSION['amount_paid'],
            'status'         => $_SESSION['status'],
            'method'         => $_SESSION['method'],
        ];

        $upt_params = [
            'balance' => $_SESSION['balance'],
        ];

        $func->UpdateRecord($connect, "userfile","WHERE usr_cde = ?", $upt_params,[$_SESSION['usr_cde']],!true);
        $func->InsertRecord($connect, "payments", $xparams);


        // unset($_SESSION['roomnum']); // To clear 'roomnum'
        // unset($_SESSION['prev_balance']); // To clear 'prev_balance'
        header('Location: success.php');
    } else {
        header('Location: failed.php');
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>