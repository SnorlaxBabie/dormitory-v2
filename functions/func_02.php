<?php 
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Funcshits {

    public function genUniqueNumber($prefix = '') {
        return $prefix . uniqid();
    }

    public function InsertRecord(PDO $database, string $xtable, array $xparams, bool $debug = false): array {
        $response = ['bool' => true, 'msg' => ''];
    
        $columns = implode(", ", array_map(function($column) {
            return "`$column`";
        }, array_keys($xparams)));
    
        $placeholders = implode(", ", array_fill(0, count($xparams), '?'));
        $sql = "INSERT INTO `$xtable` ($columns) VALUES ($placeholders)";
    
        if ($debug) {
            echo '<pre>';
            var_dump('Query:', $sql);
            var_dump('Parameters:', array_values($xparams));
            echo '</pre>';
            die();
        }
    
        try {
            $stmt = $database->prepare($sql);
            $stmt->execute(array_values($xparams));
        } catch (PDOException $e) {
            $response['bool'] = false;
            $response['msg'] = 'Database error: ' . $e->getMessage();
    
            if ($debug) {
                echo '<pre>';
                var_dump('Error:', $response['msg']);
                echo '</pre>';
            }
        }
    
        return $response;
    }

    public function FetchAll($connect, $table, $columns = "*", $where = "", $params = [], $order = "", $limit = "", $debug = false) {
        try {
            $sql = "SELECT $columns FROM $table";
            
            if (!empty($where)) {
                $sql .= " $where";
            }
    
            if (!empty($order)) {
                $sql .= " $order";
            }
    
            if (!empty($limit)) {
                $sql .= " $limit";
            }
    
            if ($debug) {
                echo "SQL Query: " . $sql . "<br>";
                echo "Parameters: " . json_encode($params) . "<br>";
            }

            $stmt = $connect->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if ($debug) {
                echo '<pre>';
                var_dump('Results:', $results);
                echo '</pre>';
            }
    
            return $results;
    
        } catch (PDOException $e) {
            if ($debug) {
                echo "Error: " . $e->getMessage();
            }
            return false;
        }
    }
    

    public function FetchSingle($connect, $table, $whereClause, $params = [], $order = "", $columns = "*", $debug = false) {
        try {
            $sql = "SELECT $columns FROM $table $whereClause";
            if (!empty($order)) {
                $sql .= " $order";
            }

            $stmt = $connect->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();

            if ($debug) {
                echo "SQL Query: " . $sql;
                echo "<pre>";
                print_r($result);
                echo "</pre>";
            }

            return $result;
    
        } catch (PDOException $e) {
            if ($debug) {
                echo "Error: " . $e->getMessage();
            }
            return false;
        }
    }
    
    public function UpdateRecord($connect, string $xtable, string $whereClause, array $xparams, array $conditions, bool $debug = false): array {
        $response = ['bool' => true, 'msg' => ''];

        $setClause = implode(", ", array_map(function($column) {
            return "`$column` = ?";
        }, array_keys($xparams)));

        $sql = "UPDATE `$xtable` SET $setClause $whereClause";
    
        if ($debug) {
            echo '<pre>';
            var_dump('Query:', $sql);
            var_dump('Parameters (Set values):', array_values($xparams));
            var_dump('Conditions:', $conditions);
            echo '</pre>';
            die();
        }

        $executeParams = array_merge(array_values($xparams), $conditions);
    
        try {
            $stmt = $connect->prepare($sql);
            $stmt->execute($executeParams);
        } catch (PDOException $e) {
            $response['bool'] = false;
            $response['msg'] = 'Database error: ' . $e->getMessage();
    
            if ($debug) {
                echo '<pre>';
                var_dump('Error:', $response['msg']);
                echo '</pre>';
            }
        }
    
        return $response;
    }
    
    public function DeleteRecord(PDO $database, string $xtable, string $whereClause, array $conditions, bool $debug = false): array {
        $response = ['bool' => true, 'msg' => ''];

        $sql = "DELETE FROM `$xtable` $whereClause";
        if ($debug) {
            echo '<pre>';
            var_dump('Query:', $sql);
            var_dump('Conditions:', $conditions);
            echo '</pre>';
            die();
        }
    
        try {
            $stmt = $database->prepare($sql);
            $stmt->execute($conditions);
        } catch (PDOException $e) {
            $response['bool'] = false;
            $response['msg'] = 'Database error: ' . $e->getMessage();
    
            if ($debug) {
                echo '<pre>';
                var_dump('Error:', $response['msg']);
                echo '</pre>';
            }
        }
    
        return $response;
    }
    
    public function formateDate($date, $format) {
        // $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        $dateTime = (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $date)) ? DateTime::createFromFormat('m-d-Y', $date) : DateTime::createFromFormat('Y-m-d', $date);
        if ($dateTime === false) {
            return "Invalid date";
        }
        return $dateTime->format($format);
    }

    public function formateDate2($date, $format = 'F j, Y g:i A') {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if ($dateTime === false) {
            return "Invalid date";
        }
        
        return $dateTime->format($format);
    }

    public function formatDate3($date) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);

        
        if ($dateTime === false) {
            return "Invalid date";
        }
        
        return $dateTime->format('F d, Y');
    }

    public function formatDateTime($date) {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if ($dateTime === false) {
            return "Invalid date";
        }

        // return $dateTime->format('F j, Y g:i A');
        return $dateTime->format('M j, Y g:i A');
    }

    public function formatDate4($date) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);

        if ($dateTime === false) {
            return "Invalid date";
        }

        return $dateTime->format('M j, Y');
    }

    public function calculateDueDate($startDate, $daysToAdd) {
        $date = new DateTime($startDate);
        $date->modify("+{$daysToAdd} days");
        return $date->format('Y-m-d');
    }
     
    public function LeaseStatus($startDate, $endDate) {
        $currentDate = new DateTime();
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
    
        if ($currentDate < $startDate) {
            return "Pending";
        } elseif ($currentDate >= $startDate && $currentDate <= $endDate) {
            return "Active";
        } else {
            return "Expired";
        }
    }

    public function sendEmailNotification($recipientEmail, $recipientName, $subject, $reason, $message) {
        global $connect;
        $mail = new PHPMailer(true);
        $params_query = "SELECT * FROM standardparameter";
        $stmt = $connect->prepare($params_query);
        $stmt->execute();
        $parameters = $stmt->fetch(2);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $parameters['gmail_username'];
            $mail->Password   = $parameters['gmail_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
    
            $mail->setFrom($parameters['gmail_username'], $parameters['companyname']); 
            $mail->addAddress($recipientEmail, $recipientName);
    
            // Content
            $mail->isHTML(true);
    
            $notificationReason = htmlspecialchars($reason);
            $notificationDetails = htmlspecialchars($message);
    
            $mail->Subject = $subject;
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        background-color: #f4f4f4;
                    }
                    .container {
                        max-width: 600px;
                        margin: 20px auto;
                        background-color: #ffffff;
                        border-radius: 10px;
                        padding: 30px;
                        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                    }
                    .logo {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .logo img {
                        max-width: 150px;
                        height: auto;
                    }
                    p {
                        line-height: 1.6;
                        color: #555;
                        margin: 10px 0;
                    }
                    .footer {
                        margin-top: 30px;
                        font-size: 0.9em;
                        color: #777;
                        border-top: 1px solid #eaeaea;
                        padding-top: 15px;
                    }
                    .highlight {
                        font-weight: bold;
                        color: #2980b9;
                    }
                    .note {
                        margin-top: 20px;
                        font-size: 0.85em;
                        color: #999;
                    }
                    .details {
                        margin-top: 20px;
                        padding: 15px;
                        background-color: #f9f9f9;
                        border-left: 5px solid #2980b9;
                    }
                    .details p {
                        margin: 5px 0;
                    }
                    @media (max-width: 600px) {
                        .container {
                            padding: 15px;
                        }
                        .logo img {
                            max-width: 100px;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="logo">
                        <img src="'.$parameters['online_logo'].'" alt="logo" border="0">
                    </div>
                    <h3>Hello ' . htmlspecialchars($recipientName) . ',</h3>
                    <p>
                        We want to notify you about <span class="highlight">' . $notificationReason . '</span>.
                    </p>
                    
                    <h4>Details of the Notification</h4>
                    <div class="details">
                        <p>' . $notificationDetails . '</p>
                    </div>
    
                    <p>Thank you for your attention.</p>
    
                    <div class="footer">
                        Regards,<br>
                        <strong>'.$parameters['companyname'].'</strong><br>
                        <a href="mailto:'.$parameters['companyemail'].'">'.$parameters['companyemail'].'</a><br>
                        '.$parameters['companyaddress'].'<br>
                        '.$parameters['companycontactnum'].'<br>
                    </div>
                    
                    <div class="note">
                        This email is system generated. Please do not reply.
                    </div>
                </div>
            </body>
            </html>
            ';
    
            $mail->send();
    
            return [
                'bool' => true,
                'msg' => 'Email sent successfully.'
            ];
        } catch (Exception $e) {
            return [
                'bool' => false,
                'msg' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"
            ];
        }
    }
    
}
?>