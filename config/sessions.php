<?php 
session_start();
if (!isset($_SESSION['usr_cde']) && !isset($_SESSION['is_logged_in'])) {
    header("Location: index.php");
    exit();
}
?>