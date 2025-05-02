<?php 
class Standard
{
    public static function isPostEmpty($key){
        return !isset($key) || $key === null || trim($key) === '';
    }

    public static function isFieldRequired($required_fields){
        $error = [];

        foreach ($required_fields as $key => $field) {

            if (self::isPostEmpty($field)) {
                $error[$key] = "$key Should not be blank.";
            }
        }
        return $error;
    }

    public static function isValidInput($data){
        // $pattern = '/^[a-zA-Z0-9@&()+=ñÑ_ ,.?\/#-]+$/';
        // $pattern = '/^[a-zA-Z0-9ñÑ]+$/'; // alpha numeric
        // $pattern = '/^[a-zA-Z0-9ñÑ,.\-@$#%*=+\/]+$/';

        // return preg_match($pattern, $data) === 1;
        return true;
    }

    public static function regexValidation($data){
        $response = ['bool' => true, 'msg' => ''];
        foreach ($data as $key => $value) {
            if (!self::isValidInput($value)) {
                $response['bool'] = false;
                $response['msg'] = "Invalid input. Use Alphanumeric. @,/?#&()+=_ and ñ are allowed.<br>";
            }
        }

        return $response;
    }

    public static function InsertRecord(PDO $database, string $xtable, array $xparams, bool $debug = false): array {
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
}
?>