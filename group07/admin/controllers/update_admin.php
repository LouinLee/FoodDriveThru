<?php
    include '../../config/db.php';
    require_once '../../security.php';
    require_once '../model/functions.php';

    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);

    $message = '';

    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];
    
        // Sanitize input data
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
        $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
        $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);
    
        // Update admin profile
        updateAdmin($admin_id, $name, $old_pass, $new_pass, $confirm_pass, $conn, $message);
    
        // Output error message, if any
        if (!empty($message)) {
            echo '<div class="message"><span>' . $message . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
        } else {
            $_SESSION['message'] = $message;
            header('location: ../views/admin_accounts.php');
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile update</title>
    <script src="../js/admin_script.js"></script>
    <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>
    <section class="form-container">
        <form action="" method="POST">
            <h3>update profile</h3>
            <input type="text" name="name" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?= displayProfile($admin_id,$conn); ?>">
            <input type="password" name="old_pass" maxlength="20" placeholder="enter your old password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="new_pass" maxlength="20" placeholder="enter your new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="confirm_pass" maxlength="20" placeholder="confirm your new password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="update now" name="submit" class="btn">
        </form>
    </section>
</body>
</html>
