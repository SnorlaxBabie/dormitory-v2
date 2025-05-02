<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$tenant_usrcde = $_SESSION['usr_cde'];
$usrlvl = $_SESSION['usr_lvl'];
$landlord_usrcde = $func->FetchSingle($connect,"userfile","WHERE usr_lvl = 'ADMIN'")['usr_cde'];

// $query = "SELECT xmsg.*, usr1.usr_fname AS sender_first_name, usr2.usr_fname AS receiver_first_name 
//           FROM messages xmsg
//           JOIN userfile usr1 ON xmsg.sender_id = usr1.usr_cde
//           JOIN userfile usr2 ON xmsg.receiver_id = usr2.usr_cde
//           WHERE (xmsg.sender_id = ? AND xmsg.receiver_id = ?) OR (xmsg.sender_id = ? AND xmsg.receiver_id = ?)
//           ORDER BY xmsg.created_at DESC";
// $stmt = $connect->prepare($query);
// $stmt->execute([$tenant_usrcde,$landlord_usrcde,$landlord_usrcde,$tenant_usrcde]);
// $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .welcome-section {
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }
    .message-container {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 1rem;
        margin: 1rem 0;
    }

    .message-list-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        height: calc(100vh - 100px);
        display: flex;
        flex-direction: column;
    }

    .message-list-header {
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px 10px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .message-list {
        overflow-y: auto;
        flex-grow: 1;
    }

    .message-item {
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        cursor: pointer;
        transition: all 0.2s;
    }

    .message-item:hover {
        background-color: #f8f9fc;
        transform: translateX(5px);
    }

    .message-item.unread {
        background-color: #fff8e6;
    }

    .message-item.active {
        background-color: #e8eeff;
        border-left: 4px solid #4e73df;
    }

    .message-subject {
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 0.25rem;
    }

    .message-preview {
        color: #858796;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .message-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: #858796;
    }

    .message-compose-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        /* height: calc(100vh - 100px); */
        display: flex;
        flex-direction: column;
    }

    .message-compose-header {
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px 10px 0 0;
        display: flex;
        align-items: center;
    }

    .message-compose-body {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    /* .conversation-container {
        flex-grow: 1;
        overflow-y: auto;
        margin-bottom: 1rem;
    } */

    .message-bubble {
        max-width: 80%;
        margin-bottom: 1rem;
        padding: 0.7rem;
        border-radius: 1rem;
        position: relative;
    }

    .message-bubble.sent {
        background-color: #4e73df;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 0.25rem;
    }

    .message-bubble.received {
        background-color: #dadada;
        color: #5a5c69;
        margin-right: auto;
        border-bottom-left-radius: 0.25rem;
    }

    .message-time {
        font-size: 0.75rem;
        margin-top: 0.5rem;
        opacity: 0.8;
    }

    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #6e707e;
        background-color: #fff;
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        transition: border-color 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #bac8f3;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .reply-box {
        display: flex;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e3e6f0;
    }

    .btn-send {
        background: #4e73df;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.35rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        white-space: nowrap;
    }

    .btn-send:hover {
        background: #224abe;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.unread {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-badge.read {
        background-color: #e3e6f0;
        color: #858796;
    }
    .conversation-container {
        display: flex;
        flex-direction: column-reverse; /* Start displaying from the bottom */
        max-height: 60vh;
        overflow-y: auto;
        margin-bottom: 1rem;
    }

    .subject{
        width: 250px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php 
        require_once '../include/std_sidebar_tenant.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Welcome Section -->
            <!-- <div class="welcome-section shadow mt-3">
                <h4 class="mb-1">Welcome Back, <?php echo $_SESSION['first_name'] ?>!</h4>
            </div> -->

            <div class="container-fluid">
                <div class="message-compose-card">
                    <div class="message-compose-header">
                        <h5 class="m-0">
                            <i class="fas fa-comments me-2"></i>
                            Conversation with Landlord
                        </h5>
                    </div>
                    <div class="message-compose-body">
                        <div class="conversation-container">
                            <div class="message-bubble received">
                                <div class="message-content">
                                </div>
                                <div class="message-time"></div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                </div>
                                <div class="message-time"></div>
                            </div>
                        </div>
                        <!-- <div class="conversation-container">
                            <?php foreach ($messages as $message): ?>
                                <div class="message-bubble <?php echo ($message['sender_id'] == $tenant_usrcde) ? 'sent' : 'received'; ?>">
                                        <div class="message-content">
                                            <?php echo htmlspecialchars($message['content']); ?>
                                        </div>
                                    <div class="message-time"><?php echo date("F j, Y h:i A", strtotime($message['created_at'])); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div> -->
                            <div id="otherReasonContainer" style="display: none;">
                                <input type="text" class="form-control" id="otherReason" name="otherReason" placeholder="Please enter subject...">
                            </div>
                            <div class="reply-box">
                                <select class="form-control subject" aria-label="Select message subject" id="subject" name="subject" onchange="toggleOtherField()">
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Maintenance Request">Maintenance Request</option>
                                    <option value="Payment Issue">Payment Issue</option>
                                    <option value="Others">Other</option>
                                </select>
                                    <textarea class="form-control"name="textmessage" id="textmessage" placeholder="Send Message here..."></textarea>
                                    <input type="button" id="btnsen" class="btn-send" name="message" value="Send" onclick="sendMessage()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>


<script>
    $(document).ready(function () {
        setInterval(fetchMessage, 900); 
        // fetchMessage();
    });

    const toggleOtherField =  () => {
        $('#otherReason').val('');
        const subjectValue = $('#subject').val();
        $('#otherReasonContainer').toggle(subjectValue === 'Others');
    }

    const sendMessage = () => {
        const message = $('#textmessage').val();
        const reason = $('#otherReason').val();
        const subject = $('#subject').val();
        var tenant_usrcde = "<?php echo $tenant_usrcde;?>"
        var landlord_usrcde = "<?php echo $landlord_usrcde;?>" 
        if (!message || message.trim() === '') {
            alertify.alert("Please input a message before sending.");
            return;
        }

        if (subject === 'Others' && (!reason || reason.trim() === '')) {
            alertify.alert("Please specify a subject.");
            return;
        }

        var xparams = `event_action=sendMessage&content=${message}&subject=${subject}&landlord=${landlord_usrcde}&tenant=${tenant_usrcde}&other=${reason}`
        ajaxWithBlocking({
            type: "POST",
            url: "tenant_notice_ajax.php",
            data: xparams,
            dataType: "json",
            success: function (response) {
                if(response.bool){
                    $('#textmessage').val('');
                    $('#otherReason').val('');
                    sendEmail(subject,reason,message) 
                }
            }
        });
    };

    const fetchMessage = () => {
        var tenant_usrcde = "<?php echo $tenant_usrcde;?>"
        var landlord_usrcde = "<?php echo $landlord_usrcde;?>"
        var xparams = `event_action=fetchMessage&landlord=${landlord_usrcde}&tenant=${tenant_usrcde}`
        $.ajax({
            type: "POST",
            url: "tenant_notice_ajax.php",
            data: xparams,
            dataType: "json",
            success: function (response) {
                if (response.bool) {
                    displayMessages(response.msg);
                }
            }
        });
    }

    const displayMessages = (messages) => {
        const $message_container = $('.conversation-container');
        $message_container.empty();
        messages.forEach(message => {
            const $messageDiv = $('<div>').addClass('message-bubble');
            message.sender_id === "<?php echo $tenant_usrcde;?>" ? $messageDiv.addClass('sent') : $messageDiv.addClass('received');
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const messageDate = new Date(message.created_at);
            const datetime = messageDate.toLocaleDateString(undefined, options);
            // const time = messageDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            $messageDiv.html(`
                <div class="message-content">${message.content}</div>
                <div class="message-time">${datetime}</div>
            `);
            $message_container.append($messageDiv);
        });
    };

    const sendEmail = (subject,reason,message) => {
        var tenant_usrcde = "<?php echo $tenant_usrcde;?>"
        var landlord_usrcde = "<?php echo $landlord_usrcde;?>"
        var usrlvl = "<?php echo $usrlvl;?>"
        
        var xparams = `event_action=sendEmail&subject=${subject}&reason=${reason}&message=${message}&tenant_usrcde=${tenant_usrcde}&landlord_usrcde=${landlord_usrcde}&usrlvl=${usrlvl}`
        ajaxWithBlocking({
            type: "post",
            url: "tenant_notice_ajax.php",
            data: xparams,
            dataType: "json",
            success: function (response) {
                if(response.bool){
                    alertify.success(response.msg);
                }
            }
        });
    }

</script>

<?php 
require_once '../include/std_footer.php';
?>        