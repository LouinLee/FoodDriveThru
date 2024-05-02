<?php
   include 'config/db.php';
   include_once 'security.php';

   if (isset($_POST['submit'])) {

      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $pass = sha1($_POST['pass']);
      $pass = filter_var($pass, FILTER_SANITIZE_STRING);
      $cpass = sha1($_POST['cpass']);
      $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

      register($name,$pass,$cpass,$conn);
   }

   if (isset($message)) {
      foreach ($message as $message) {
         echo '
         <div class="message">
            <span>' . $message . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
   <section class="form-container">
      <form action="" method="POST">
         <h3>register new</h3>
         <input type="text" name="name" maxlength="20" required placeholder="Enter your username" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="pass" maxlength="20" required placeholder="Enter your password" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="cpass" maxlength="20" required placeholder="Confirm your password" class="box"
            oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="submit" value="register now" name="submit" class="btn">
         <button type="button" onclick="goBack()" class="btn" style="background-color:red;">Return</button>
      </form>
   </section>
   <script>
      function goBack() {
         window.history.back();
      }
   </script>
   <script src="../js/admin_script.js"></script>
</body>
</html>
