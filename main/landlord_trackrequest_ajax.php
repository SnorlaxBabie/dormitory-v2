<?php 
header('Content-Type: application/json');
require_once '../appconfig.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$event_action = $_POST['event_action'];
$response['bool'] = true;
$response['msg'] .= "";

if($event_action == "onapproval"){
    $reqid = $_POST['reqid'];
    $status = $_POST['value'];

    $chk = $func->FetchSingle($connect,"tenantrequest","WHERE requestid = ?",[$reqid]);

    if(count($chk) > 0){
        $params['reqstatus'] = $status;
        $func->UpdateRecord($connect,"tenantrequest","WHERE requestid = ?",$params,[$reqid]);
    }
}

if($event_action == "onassign"){
    $reqid = $_POST['reqid'];
    $status = $_POST['value'];

    $chk = $func->FetchSingle($connect,"tenantrequest","WHERE requestid = ?",[$reqid]);

    if(count($chk) > 0){
        $params['staffname'] = $status;
        $func->UpdateRecord($connect,"tenantrequest","WHERE requestid = ?",$params,[$reqid]);
    }
}

if($event_action == "getdata"){
    $maintenance_query = "SELECT 
        SUM(CASE WHEN reqstatus = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN reqstatus = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN reqstatus = 'Completed' THEN 1 ELSE 0 END) as completed
    FROM tenantrequest";

    $maint_stmt = $connect->prepare($maintenance_query);
    $maint_stmt->execute();
    $maintenance_data = $maint_stmt->fetch(PDO::FETCH_ASSOC);
    $response['bool'] = true;
    $response['msg'] = $maintenance_data;
}

if($event_action == "refetch_board"){
    $sql = "SELECT * FROM tenantrequest ORDER BY created_at DESC";
    $stmt = $connect->prepare($sql);
    $stmt->execute([]);
    $tenant_request = $stmt->fetchAll();
    $response = []; 

    foreach ($tenant_request as $request) {

        $chk = $func->FetchSingle($connect, "userfile", "WHERE usr_cde = ?", [$request['usr_cde']]);
            $response[] = [
                'requestid' => $request['requestid'],
                'name' => $chk['usr_fname'] . ' ' . $chk['usr_lname'],
                'description' => $request['description'],
                'requestprio' => $request['requestprio'],
                'reqstatus' => $request['reqstatus'],
                'schedule' => $func->formateDate($request['requestsched'], 'm-d-Y'),
            ];
    }
    
}



echo json_encode($response);
?>
