<?php
   include '../../config/db.php';
   require_once '../model/functions.php';
   require_once '../../security.php';
   $admin_id = $_SESSION['admin_id'];
   checkLogin($admin_id, $conn);
   $sn = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   <link rel="stylesheet" href="../../css/admin_style.css">
   <link rel="stylesheet" href="../../css/style.css">
   <style>
      .sectionButton {
         height: fit-content;
         width: 100%;
         text-align: center;
      }

      .sectionButton a {
         width: 200px;
         margin: 0 auto;
         display: inline-block;
      }

      .sectionButton button {
         background-color: blue;
         border: none; /* Remove border */
         border-radius: 0.5rem;
         cursor: pointer;
         width: 200px;
         font-size: 1.8rem;
         color: var(--white);
         padding: 1.2rem 3rem;
         text-transform: capitalize;
         text-align: center;
      }

      .sectionButton button:hover {
         background-color: darkblue;
      }

      .errorMessage{
         width: fit-content;
         height: fit-content;
         margin: 0 auto;
      }
   </style>
</head>
<body>
   <section>
      <div class="sectionButton">
         <a href="foods.php"><button>New Orders</button></a>
      </div>
   </section>
   <section class="orders">
      <div class="box-container">
         <h1 class="title">Orders</h1>
         <table  style="margin: 0 auto;" class="content-table">
         <tr>
            <th>Order Number:</th>
            <th>Food</th>
            <th>Price</th>
            <th>Qty.</th>
            <th>Total</th>
            <th>Status</th>
            <th>Order Date</th>
         </tr>
            <?php
               showOrders($admin_id,$conn)
            ?>
         </table>
      </div>
   </section>
</body>
</html>