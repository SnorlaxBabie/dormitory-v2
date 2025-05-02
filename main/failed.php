<?php 
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';
$func = new Funcshits();
?>


<script>
    $(document).ready(function () {
        alertify.alert('Payment Failed!', function() {
            window.location.href = 'tenant_paymentprocess.php';
        });
    });
</script>
<?php 
require_once '../include/std_footer.php';
?>