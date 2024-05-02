<?php
    include '../../config/db.php';
    require_once '../../security.php';
    require_once '../model/functions.php';
    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);

    // Initialize variables
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $message = "";

    // Check if form is submitted
    if (isset($_POST['submit'])) {
        $product_id = isset($_POST['id']) ? $_POST['id'] : '';
        $qty = isset($_POST['qty']) ? $_POST['qty'] : '';
        $message = addOrder($admin_id, $product_id, $qty, $conn);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Orders</title>
    <link rel="stylesheet" href="../../css/admin_style.css">
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        input{
            border: 1px solid lightgrey;
            text-align: right;
            font-weight: bold;
            font-size: 10px;
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Adjust the values for horizontal offset, vertical offset, blur radius, and color */
        }
        .inputsrch{
            text-align: left;
        }
    </style>
</head>
<body>
    <?php if(!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <section>
        <div class="containerLink">
            <a class="start" href="../views/foods.php"><h1>Return</h1></a>
            <a class="end" href="cart.php"><h1>CART</h1></a>
        </div>
    </section>
    <section class="food-search text-center">
        <div class="containerOrder">
            <form action="food-search.php" method="POST">
                <input class="inputsrch" type="search" name="search" value="<?php echo $search; ?>" placeholder="Search for Food.." required>
                <input type="submit" name="submit" value="Search" class="btnOrder btn-primaryOrder">
            </form>
        </div>
    </section>
    <div class="text-center">
        <h2>Foods on Your Search <a href="#">"<?= $search; ?>"</a></h2>
    </div>
    <section class="containerMenu">
        <h2 class="text-center">Food Menu</h2>
        <div class="food-menu">
            <?php searchFoods($conn,$search,$message); ?>
            <div class="clearfix"></div>
        </div>
    </section>
    <!-- fOOD Menu Section Ends Here -->
</body>
</html>