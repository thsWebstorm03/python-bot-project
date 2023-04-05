<?php
session_start();
if(isset($_SESSION['id'])) {
    // Set user status to offline
    require_once('../db-config.php');
    $sql = 'UPDATE tbl_administrators SET is_online = 0 WHERE id = ' . $_SESSION['id'];
    $result = $conn->query($sql);
    // Destroy user session
    unset($_SESSION['id']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);
}
session_destroy();
echo 'success';
?>