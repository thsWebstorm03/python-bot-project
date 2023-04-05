<?php
    require_once('../db-config.php');
    // Request: POST
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = 'SELECT * FROM tbl_administrators WHERE email = "' . $email . '" AND password = "' . $password . '"';
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_array($result);
        if ($row['permission'] == false) {
            echo 'not_allowed';
            exit(1);
        }
        session_start();
        $_SESSION['id'] = $row['id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['email'] = $row['email'];
        $sql = 'UPDATE tbl_administrators SET last_signed_in = NOW(), is_online = 1 WHERE id = ' . $row['id'];
        $conn->query($sql);
        echo 'success';
    } else {
        echo 'failed';
    }
?>