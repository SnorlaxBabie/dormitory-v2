<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$tenant_usrcde = $_SESSION['usr_cde'];
$usr_lvl = $_SESSION['usr_lvl'];
$landlord_usrcde = $func->FetchSingle($connect,"userfile","WHERE usr_lvl = 'ADMIN'")['usr_cde'];

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
        /* margin-bottom: 0.25rem; */
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
        require_once '../include/std_sidebar.php'; 
        ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Welcome Section -->
            <!-- <div class="welcome-section shadow mt-3">
                <h4 class="mb-1">Welcome Back, <?php echo $_SESSION['first_name'] ?>!</h4>
            </div> -->

                <div class="container-fluid">
                    <div class="message-container">
                        <div class="message-list-card">
                            <div class="message-list-header">
                                <h5 class="m-0">
                                    <i class="fas fa-inbox me-2"></i>Messages
                                </h5>
                                <?php 
                                    // if($totalunread['total_unread_count'] != null){ 
                                ?>
                                    <span class="badge bg-warning unreadcount"></span>
                                <!-- <span class="badge bg-warning unreadcount"><?php echo $totalunread['total_unread_count']; ?> unread</span> -->
                                <?php
                                    // } 
                                ?>
                            </div>
                            <div class="message-list">
                                <!-- <div class="message-item unread">
                                    <div class="message-subject">Maintenance Request #123</div>
                                    <div class="message-preview">Your maintenance request has been received and...</div>
                                    <div class="message-meta">
                                        <span>Landlord</span>
                                        <span class="status-badge unread">New</span>
                                        <span>2h ago</span>
                                    </div>
                                </div>
                                <div class="message-item active">
                                    <div class="message-subject">Parking Space Assignment</div>
                                    <div class="message-preview">Thank you for submitting your parking space request...</div>
                                    <div class="message-meta">
                                        <span>You</span>
                                        <span class="status-badge read">Sent</span>
                                        <span>Yesterday</span>
                                    </div>
                                </div>
                                <div class="message-item">
                                    <div class="message-subject">Building Security Update</div>
                                    <div class="message-preview">We're updating the building's security system...</div>
                                    <div class="message-meta">
                                        <span>Landlord</span>
                                        <span class="status-badge read">Read</span>
                                        <span>Oct 15</span>
                                    </div>
                                </div> -->
                            </div>
                        </div>

                        <div class="message-compose-card">
                            <div class="message-compose-header">
                                <h5 class="m-0">
                                    <i class="fas fa-comments me-2"></i>
                                    <span id="conversationwith"></span>
                                </h5>
                            </div>
                            <div class="message-compose-body">
                                <div class="conversation-container">
                                    <!-- <div class="message-bubble received">
                                        <div class="message-content">
                                        </div>
                                        <div class="message-time"></div>
                                    </div>
                                    <div class="message-bubble sent">
                                        <div class="message-content">
                                        </div>
                                        <div class="message-time"></div>
                                    </div> -->
                                </div>
                                <div class="reply-box" id="mainchat" style="display: none;">
                                    <select class="form-control subject" aria-label="Select message subject" id="subject" name="subject">
                                        <option value="Lease Updates">Lease Updates</option>
                                        <option value="Due Date Reminders">Due Date Reminders</option>
                                        <option value="Request Confirmation">Request Confirmation</option>
                                        <option value="Payment Reminders">Payment Reminders</option>
                                    </select>
                                        <textarea class="form-control"name="textmessage" id="textmessage" placeholder="Send Message here..."></textarea>
                                        <input type="button" id="btnsen" class="btn-send" name="message" value="Send" onclick="sendMessage()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="selected_tenant" name="selected_tenant" value="">
        </main>
    </div>
</div>

<script>
  $(document).ready(function () {
    // fetchMessage();
    fetchUser();
    // setInterval(fetchUser, 1000); 
  });
  const fetchUser = () => {
      var xparams = `event_action=fetchUsers`
      $.ajax({
          type: "POST",
          url: "tenant_notice_ajax.php",
          data: xparams,
          dataType: "json",
          success: function (response) {
            console.log(response.unread);

            if (response.bool) {
                $('.message-list').empty();
                $.each(response.msg, function(index, message) {
                    const statusClass = message.status == 0 ? 'unread' : 'read';
                    const messageItem = `
                        <div class="message-item ${statusClass}" data-isnull="${message.isNull}" data-sender="${message.sender}" data-fullname="${message.fullname}" data-subject="${message.subject}" data-index="${index}">
                            <div class="message-subject">${message.fullname}</div>
                            <div class="message-preview">${message.preview == null ? '' : message.preview}</div>
                            <div class="message-meta">
                                <!-- <span>${message.sender}</span> -->
                                <span>${message.timestamp == 'Invalid date' ? '' : message.timestamp}</span>
                                ${message.status == 0 ? `<span class="status-badge ${statusClass}">${statusClass.charAt(0).toUpperCase() + statusClass.slice(1)}</span>` : ''}
                            </div>
                        </div>
                    `;
                    $('.message-list').append(messageItem);
                    // $('.unreadcount').text(`${response.unread.total_unread_count} unread`);
                    if (response.unread.total_unread_count > 0) {
                        $('.unreadcount').text(`${response.unread.total_unread_count} unread`);
                    } else {
                        $('.unreadcount').text('');
                    }
                });

                $('.message-item').on('click', function() {
                    const sender = $(this).data('sender');
                    const subject = $(this).data('subject');
                    const index = $(this).data('index');
                    const fullname = $(this).data('fullname'); 
                    const usrcde = $(this).data('isnull'); // this usrcde is no conversation avaialable
                    $('#selected_tenant').val(usrcde);
                    console.log(usrcde)
                    fetchConversation(sender, subject,fullname,usrcde);
                });
            } else {
                $('.message-list').append('<div>No messages found.</div>');
            }
          }
      });
  }

  const fetchConversation = (sender, subject,fullname,usrcde) => {
      var xparams = `event_action=fetchMessage&sender=${sender}&subject=${subject}&user=${usrcde}`;
      ajaxWithBlocking({
          type: "POST",
          url: "tenant_notice_ajax.php",
          data: xparams,
          dataType: "json",
          success: function(response) {
            const conversationContainer = $('.conversation-container');
            console.log('dsa',response.msg,response.msg.length,response.bool)

              if (response.bool && response.msg.length !== 0) {
                  conversationContainer.empty();

                  $.each(response.msg, function(index, message) {
                      var usrcde = "<?php echo $tenant_usrcde; ?>";
                      var msg = message.sender_id == usrcde ? 'sent' : 'received'; 
                      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                      const messageDate = new Date(message.created_at);
                      const datetime = messageDate.toLocaleDateString(undefined, options);
                      const messageBubble = `
                          <div class="message-bubble ${msg}">
                              <div class="message-content">${message.content}</div>
                              <div class="message-time">${datetime}</div>
                          </div>
                      `;
                      conversationContainer.append(messageBubble);
                  });
              } else {
                  conversationContainer.empty();
                  $('.conversation-container').append('<div style="text-align:center;">No conversation found with this user.</div>');
              }
              $('#mainchat').show();
              $('.message-compose-header span').text(`Conversation with ${fullname}`);
          }
      });
  }

  const sendMessage = () => {
    const message = $('#textmessage').val();
    const reason = $('#otherReason').val();
    const subject = $('#subject').val();
    // var tenant_usrcde = "<?php echo $tenant_usrcde;?>"
    var tenant_usrcde = $('#selected_tenant').val();
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
                console.log(response)
                $('#textmessage').val('');
                $('#otherReason').val('');
                // fetchMessage();
                fetchUser();
                fetchConversation(response.sender, response.subject,response.fullname,response.usrcde);
                sendEmail(subject,reason,message)
            }
        }
    });
  };

  const sendEmail = (subject,reason,message) => {
    // var tenant_usrcde = "<?php echo $tenant_usrcde;?>"
    var tenant_usrcde = $('#selected_tenant').val();

    var landlord_usrcde = "<?php echo $landlord_usrcde;?>"
    var usrlvl = "<?php echo $usr_lvl;?>"
    
    var xparams = `event_action=sendEmail&subject=${subject}&reason=${reason}&message=${message}&tenant_usrcde=${tenant_usrcde}&landlord_usrcde=${landlord_usrcde}&usrlvl=${usrlvl}`
    ajaxWithBlocking({
        type: "post",
        url: "tenant_notice_ajax.php",
        data: xparams,
        dataType: "json",
        success: function (response) {
            if(response.bool){
                // alertify.success(response.msg);
                alertify.success(response.msg, { 
                    position: 'top-right',
                    duration: 1,
                    dismissOthers: true
                });
            }
        }
    });
  }
</script>
<?php 
require_once '../include/std_footer.php';
?>        