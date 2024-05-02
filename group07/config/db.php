<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $db_name = 'mysql:host=localhost;dbname=admin_db';
    $user_name = 'root';
    $user_password = '';

    $conn = new PDO($db_name, $user_name, $user_password);
?>