<?php
   include 'config/db.php';
   include_once 'security.php';
   if (isset($_POST['submit'])) {

      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $pass = sha1($_POST['pass']);
      $pass = filter_var($pass, FILTER_SANITIZE_STRING);
      
      login($name, $pass, $conn);
   }
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>
   <section class="form-container">
      <form action="" method="POST">
         <h3>Login</h3>
         <!-- <p>Username : <span>admin</span>, Password : <span>admin</span></p> -->
         <input type="text" name="name" maxlength="20" required placeholder="Enter your username" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="pass" maxlength="20" required placeholder="Enter your password" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="submit" value="login now" name="submit" class="btn">
         <a href="register_admin.php" class="btn" style="background-color: red;">Register</a>
      </form>
   </section>
</body>
</html>