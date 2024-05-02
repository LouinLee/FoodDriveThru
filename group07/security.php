<?php
    function register($name, $pass, $cpass, $conn){
        $message = [];
        $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
        $select_admin->execute([$name]);

        if ($select_admin->rowCount() > 0) {
            $message[] = 'Username already exists!';
        } else {
            if ($pass != $cpass) {
                $message[] = 'Confirm password does not match!';
            } else {
                $insert_admin = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
                $insert_admin->execute([$name, $cpass]);
                $message[] = 'New admin registered!';
                header("location: index.php");
                exit;
            }
        }
        return $message;
    }
    function login($name, $pass, $conn){
        hash('sha256',$pass);
        $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
        $select_admin->execute([$name, $pass]);
  
        if ($select_admin->rowCount() > 0) {
           $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
           $_SESSION['admin_id'] = $fetch_admin_id['id'];
           header('location:admin/model/home.php');
        } else {
           $message[] = 'Incorrect username or password!';
        }
    }
    function checkLogin($admin_id, $conn){
        //AUthorization - Access COntrol
        //Check whether the admin_id is logged in or not
        if(!isset($_SESSION['admin_id'])) //IF user session is not set
        {
            //admin_id is not logged in
            //Redirect to login page with message
            $_SESSION['no-login-message'] = "<div class='error text-center'>Please login to access Admin Panel.</div>";
            //Redirect to Login Page
            header('location: ../../admin_login.php');
        }
    }
?>