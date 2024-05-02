<?php
    include 'config/db.php';
    session_unset();
    session_destroy();
    header('location:admin_login.php');
?>