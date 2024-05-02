<?php
   require_once '../model/functions.php';
   require_once '../../security.php';
   include '../../config/db.php';
   $admin_id = $_SESSION['admin_id'];
   if (!isset($admin_id)) {
      header('location:../../security/admin_login.php');
   }
   $sn = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>
   <section class="dashboard">
      <h1 class="heading">dashboard</h1>
      <div class="box-container">
         <div class="box">
            <h3>Welcome!</h3>
            <p>
               <?= displayProfile($admin_id,$conn); ?>
            </p>
            <a href="../controllers/update_admin.php" class="btn">update profile</a>
         </div>
         <div class="box">
            <h3>Admins</h3>
            <p><?= adminQty($conn); ?></p>
            <a href="admin_accounts.php" class="btn">see admins</a>
         </div>
         <div class="box">
            <h3>Category</h3>
            <p><?= categoryQty($conn) ?></p>
            <a href="category.php" class="btn">see category</a>
         </div>
         <div class="box">
            <h3>Product</h3>
            <p><?= productQty($conn) ?></p>
            <a href="products.php" class="btn">see products</a>
         </div>
         <div class="box">
            <h3>Order</h3>
            <p><?= orderQty($conn) ?></p>
            <a href="orders.php" class="btn">see orders</a>
         </div>
      </div>
   </section>
   <script src="../js/admin_script.js"></script>
</body>
</html>