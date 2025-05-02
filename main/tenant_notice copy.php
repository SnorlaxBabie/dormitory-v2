<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';

// echo '<pre>';var_dump('hereee 1',$_SESSION);die();
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
    padding: 1rem;
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
    background-color: #f8f9fc;
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
            <div class="welcome-section shadow mt-3">
                <h4 class="mb-1">Welcome Back, <?php echo $_SESSION['first_name'] ?>!</h4>
                <p class="mb-0">Here's what's happening in your community today</p>
            </div>

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
                                    Hello! How can I help you today?
                                </div>
                                <div class="message-time">10:30 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Hi, I'd like to request a parking space assignment for my new car.
                                </div>
                                <div class="message-time">10:32 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                            <div class="message-bubble received">
                                <div class="message-content">
                                    I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                </div>
                                <div class="message-time">10:35 AM</div>
                            </div>
                            <div class="message-bubble sent">
                                <div class="message-content">
                                    Sure! It's a Toyota Camry, license plate ABC123.
                                </div>
                                <div class="message-time">10:36 AM</div>
                            </div>
                        </div>

                            <div class="reply-box">
                                <select class="form-control subject" aria-label="Select message subject">
                                    <option selected disabled>Choose a subject</option>
                                    <option value="general_inquiry">General Inquiry</option>
                                    <option value="maintenance_request">Maintenance Request</option>
                                    <option value="payment_issue">Payment Issue</option>
                                    <option value="parking_request">Parking Request</option>
                                    <option value="other">Other</option>
                                </select>
                                    <textarea class="form-control"name="textmessage" id="textmessage" placeholder="Send Message here..."></textarea>
                                    <button type="button" class="btn-send">
                                        <i class="fas fa-paper-plane me-2"></i>Send
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- <div class="container-fluid">
                    <div class="message-container">
                        <div class="message-list-card">
                            <div class="message-list-header">
                                <h5 class="m-0">
                                    <i class="fas fa-inbox me-2"></i>Messages
                                </h5>
                                <span class="badge bg-warning">3 unread</span>
                            </div>
                            <div class="message-list">
                                <div class="message-item unread">
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
                                </div>
                            </div>
                        </div>

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
                                            Hello! How can I help you today?
                                        </div>
                                        <div class="message-time">10:30 AM</div>
                                    </div>
                                    <div class="message-bubble sent">
                                        <div class="message-content">
                                            Hi, I'd like to request a parking space assignment for my new car.
                                        </div>
                                        <div class="message-time">10:32 AM</div>
                                    </div>
                                    <div class="message-bubble received">
                                        <div class="message-content">
                                            I'll check the available spaces and get back to you shortly. Could you please provide your vehicle details?
                                        </div>
                                        <div class="message-time">10:35 AM</div>
                                    </div>
                                    <div class="message-bubble sent">
                                        <div class="message-content">
                                            Sure! It's a Toyota Camry, license plate ABC123.
                                        </div>
                                        <div class="message-time">10:36 AM</div>
                                    </div>
                                </div>
                                
                                <div class="reply-box">
                                    <input type="text" class="form-control" placeholder="Type your message...">
                                    <button type="button" class="btn-send">
                                        <i class="fas fa-paper-plane me-2"></i>Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
        </main>
    </div>
</div>


<?php 
require_once '../include/std_footer.php';
?>        