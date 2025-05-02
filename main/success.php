<?php 
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
$func = new Funcshits();
unset($_SESSION['roomid']);
unset($_SESSION['roomnum']);       
unset($_SESSION['due_date']);      
unset($_SESSION['prev_balance']);  
unset($_SESSION['balance']);       
unset($_SESSION['amount_paid']);   
unset($_SESSION['status']);        
unset($_SESSION['method']);
?>


<script>
    $(document).ready(function () {
        alertify.alert('Payment Success!', function() {
            window.location.href = 'tenant_paymentprocess.php';
        });
    });
</script>
<?php 
require_once '../include/std_footer.php';
?>