<?php
require_once '../include/std_header.php';
require_once '../config/sessions.php';
require_once '../functions/func_01.php';
require_once '../functions/func_02.php';

$func = new Funcshits();
$unreadqry = "SELECT 
    SUM(unread_count) AS total_unread_count
    FROM (
        SELECT 
            u.usr_cde,
            COUNT(m.status) AS unread_count
        FROM 
            userfile u
        JOIN 
            messages m ON u.usr_cde = m.sender_id
        WHERE 
            m.status = 0 AND u.usr_lvl = 'ADMIN'
        GROUP BY 
            u.usr_cde) AS unread_counts;";
$stmt = $connect->prepare($unreadqry);
$stmt->execute();

$total_unread_count = $stmt->fetch()['total_unread_count'];
?>

<style>
.card-dashboard {
    transition: transform 0.2s;
    border-radius: 10px;
    border: none;
}

.card-dashboard:hover {
    transform: translateY(-5px);
}

.stat-card {
    border-left: 4px solid;
    background: linear-gradient(45deg, #ffffff 0%, #f8f9fc 100%);
}

.stat-card.primary {
    border-left-color: #4e73df;
}

.stat-card.warning {
    border-left-color: #f6c23e;
}

.announcement-item {
    padding: 15px;
    border-left: 3px solid #4e73df;
    margin-bottom: 15px;
    background-color: #f8f9fc;
    border-radius: 5px;
    transition: all 0.3s;
}

.announcement-item:hover {
    transform: translateX(5px);
    background-color: #eaecf4;
}

.message-row {
    cursor: pointer;
    transition: background-color 0.2s;
}

.message-row:hover {
    background-color: #f8f9fc;
}

.message-row.unread {
    background-color: #fff8e6;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.read {
    background-color: #eaecf4;
    color: #858796;
}

.status-badge.unread {
    background-color: #fff3cd;
    color: #856404;
}

.card-title-icon {
    margin-right: 8px;
}

.announcement-date {
    font-size: 0.8rem;
    color: #858796;
}

.dashboard-header {
    margin-bottom: 2rem;
}

.welcome-section {
    background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
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
            <div class="welcome-section shadow mt-3">
                <h4 class="mb-2">Welcome Back, <?php echo $_SESSION['first_name'] ?>!</h4>
            </div>

            <div class="row mb-4">
                <!-- Announcements Card -->
                <div class="col-xl-12 col-md-12 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-bullhorn card-title-icon"></i>Announcements
                            </h6>
                        </div>
                        <div class="card-body" style="overflow-y: auto; max-height: 450px;">
                            <?php
                            $sql = "SELECT * 
                                                FROM announcements 
                                                WHERE start_date >= CURDATE() - INTERVAL 7 DAY 
                                                AND (end_date IS NULL OR end_date >= CURDATE())
                                                ORDER BY start_date ASC";
                            $stmt = $connect->prepare($sql);
                            $stmt->execute([]);
                            $announcements = $stmt->fetchAll(2);

                            if (!empty($announcements)) {
                                foreach ($announcements as $announcement) {
                                    $datefrom = date("M j, Y", strtotime($announcement['start_date']));
                                    $dateto = date("M j, Y", strtotime($announcement['end_date']));
                                    echo '<div class="announcement-item">';
                                    echo '    <div class="d-flex justify-content-between align-items-center mb-2">';
                                    echo '        <h6 class="mb-0">' . htmlspecialchars($announcement['title']) . '</h6>';
                                    echo '        <span class="announcement-date">' . $datefrom ." to " . $dateto . '</span>';
                                    echo '    </div>';
                                    echo '    <p class="mb-0">' . htmlspecialchars($announcement['content']) . '</p>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>No announcements available.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-envelope card-title-icon"></i>Recent Messages
                            </h6>
                            <a href="tenant_notice.php" class="btn btn-sm btn-primary">View All Messages</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="messagesTable">
                                    <thead>
                                        <tr>
                                            <th width="10%">Date</th>
                                            <th width="10%">From</th>
                                            <th width="20%">Subject</th>
                                            <th width="60%">Message</th>
                                            <th width="10%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $sql = "SELECT * FROM messages WHERE sender_id <> ? AND status = 0 and receiver_id = ? ORDER BY created_at DESC LIMIT 4";
                                            $stmt = $connect->prepare($sql);
                                            $stmt->execute([$_SESSION['usr_cde'],$_SESSION['usr_cde']]);
                                            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if (empty($messages)) {
                                                echo "<tr><td colspan='5' class='text-center'>NO UNREAD MESSAGE</td></tr>";
                                            } else {
                                                foreach ($messages as $message) {
                                                    $chk = $func->FetchSingle($connect, "userfile", "WHERE usr_cde = ? AND usr_lvl = 'USER'", [$message['receiver_id']]);

                                                    if ($chk['usr_cde'] != $_SESSION['usr_cde']) {
                                                        continue;
                                                    }

                                                    $status = $message['status'] == 1 ? 'read' : 'unread';

                                                    echo "<tr class='message-row ".$status."'>";
                                                    echo "<td>".date("M d, Y", strtotime($message['created_at']))."</td>";
                                                    echo "<td>Landlord</td>";
                                                    echo "<td>".$message['subject']."</td>";
                                                    echo "<td>".$message['content']."</td>";
                                                    echo "<td><span class='status-badge ".$status."'>".$status."</span></td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('.nav-link[data-bs-toggle="collapse"]').on('click', function(e) {
            e.preventDefault();
            const targetId = $(this).attr('href');
            const $targetElement = $(targetId);
            $('.collapse.show').not($targetElement).removeClass('show active').slideUp(300);
        });

        $('#sidebarLinks .nav-link').on('click', function() {
            $('#sidebarLinks .nav-link').removeClass('active');
            $(this).addClass('active');
        });

    });
</script>

<?php 
require_once '../include/std_footer.php';
?>        