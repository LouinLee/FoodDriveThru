<?php
   include '../../config/db.php';
   include_once '../../security.php';
   require_once '../model/functions.php';
   $admin_id = $_SESSION['admin_id'];
   checkLogin($admin_id, $conn);

   $message = ''; // Initialize $message variable
   if (isset($_GET['delete'])) {
      $delete_id = $_GET['delete'];
      $success = deleteAdmin($delete_id, $conn);
      if ($success) {
         // Set success message if deletion is successful
         $message = 'Admin account deleted successfully.';
      } else {
         // Set error message if deletion fails
         $message = 'Failed to delete admin account.';
      }
   }

   if (isset($_SESSION['message'])) {
      $message = $_SESSION['message'];
      unset($_SESSION['message']);
   }

   if (!empty($message)) {
      echo '<div class="message"><span>' . htmlspecialchars($message) . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="../js/admin_script.js"></script>
   <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>
   <section class="accounts">
      <h1 class="heading">All Admin</h1>
      <div class="box-container">
         <div class="box">
            <div class="minBoxSize">
               <p>Register new admin</p>
            </div>
            <a href="../controllers/register_admin.php" class="option-btn">Register</a>
         </div>
         <?php adminForm($conn) ?>
      </div>
   </section>
</body>
</html>
