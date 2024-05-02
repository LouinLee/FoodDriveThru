<?php
    include '../../config/db.php';
    require_once '../../security.php';
    require_once '../model/functions.php';
    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);
    $grand_total = orderTotal($admin_id, $conn);

    // Check if delete button is clicked
    if(isset($_POST['delete'])) {
        $cart_id = $_POST['delete'];
        deleteCartItem($cart_id, $conn);
        // Redirect to prevent form resubmission
        header("Location: cart.php");
        exit();
    }

    // Check if delete all button is clicked
    if(isset($_POST['delete_all'])) {
        deleteAllCartItems($admin_id, $conn);
        // Redirect to prevent form resubmission
        header("Location: cart.php");
        exit();
    }

    // Check if update button is clicked
    if(isset($_POST['update'])) {
        $cart_id = $_POST['cart_id'];
        $quantity = $_POST['qty'];
        if ($quantity <= 0) {
            // If quantity is 0 or less, delete the item from the cart
            deleteCartItem($cart_id, $conn);
        } else {
            // Otherwise, update the quantity
            updateCartItem($cart_id, $quantity, $conn);
        }
        // Redirect to prevent form resubmission
        header("Location: cart.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CART</title>
    <link rel="stylesheet" href="../../css/admin_style.css">
    <link rel="stylesheet" href="../../css/cart.css">
</head>
<body>
    <section>
        <div class="return">
            <a href="../views/foods.php"><h1>Return</h1></a>
        </div>
    </section>
    <section class="show-cart" style="padding-top: 0;">
        <div class="cart-total">
            <h3>Total order : <span>$<?= orderTotal($admin_id, $conn); ?></span></h3>
        </div>
        <br>
        <?php showCart($admin_id, $conn) ?>
        <br>
        <div class="more-btn">
            <a href="checkout.php" class="btn <?= (orderTotal($admin_id, $conn) > 1) ? '' : 'disabled'; ?>">checkout</a>
            <form action="" method="post">
                <button type="submit" class="delete-btn <?= (orderTotal($admin_id, $conn) > 1) ? '' : 'disabled'; ?>" name="delete_all" onclick="return confirm('delete all items from cart?');">delete all</button> <!-- Use orderTotal() function here -->
            </form>
            <a href="foods.php" class="btn">continue shopping</a>
        </div>
    </section>
</body>
</html>
