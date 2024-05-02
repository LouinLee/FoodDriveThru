<?php
    include '../../config/db.php';
    require_once '../../security.php';
    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);
    include '../partials/admin_header.php';

    if (isset($_GET['food_id'])) {
        $food_id = $_GET['food_id'];
        $show_products = $conn->prepare("SELECT * FROM `products` WHERE id=$food_id");
        $show_products->execute();
        $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
        if ($show_products->rowCount() == 1) {
            $title = $fetch_products['name'];
            $price = $fetch_products['price'];
            $image_name = $fetch_products['image'];
        } else {
            //Food not Availabe
            //REdirect to Home Page
            header('location: orders.php');
        }
    } else {
        //Redirect to homepage
        header('location: orders.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Important to make website responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Views Orders</title>
    <link rel="stylesheet" href="../../css/admin_style.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <section>
        <div class="return">
            <a href="../views/foods.php"><h1>Return</h1></a>
        </div>
    </section>
    <!-- fOOD sEARCH Section Starts Here -->
    <section class="food-search">
        <div class="containerOrder">
            
            <h2 class="text-center">Fill this form to confirm your order.</h2>

            <form action="" method="POST" class="order">
                <fieldset>
                    <legend>Selected Food</legend>
                    <div class="selectedFood">
                        <img src="../../uploaded_img/<?=$image_name?>" alt="<?=$title?>" class="img-responsive img-curve">
                    </div>
                    <div class="sselectedFoodDescription">
                        <h3><?=$title?></h3>
                        <input type="hidden" name="food" value="<?php echo $title; ?>">
                        <p class="food-price">$<?=$price?></p>
                        <input type="hidden" name="price" value="<?php echo $price; ?>">
                        <div class="order-label">Quantity</div>
                        <input type="number" name="qty" class="input-responsive" value="1" min="0" required>
                    </div>
                    <input type="submit" name="submit" value="Add To Cart" class="btn btn-primary">
                </fieldset>
            </form>                        
        </div>
    </section>
    <section class="footer">
        <div class="container text-center">
            <p>All rights reserved. Designed By <a href="#">Louin Liman</a></p>
        </div>
    </section>
</body>
</html>