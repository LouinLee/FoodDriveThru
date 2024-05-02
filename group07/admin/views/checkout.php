<?php
    $payment_result = null; // Initialize $payment_result variable
    include '../../config/db.php';
    require_once '../../security.php';
    require_once '../model/functions.php';
    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);
    $grand_total = orderTotal($admin_id, $conn);

    // Check if payment amount is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_amount'])) {
        $payment_amount = $_POST['payment_amount'];
        if ($payment_amount >= $grand_total) {
            // Display payment result
            $change = $payment_amount - $grand_total;
            $payment_result = [
                'total' => $grand_total,
                'payment_amount' => $payment_amount,
                'change' => $change
            ];
        } else {
            $error_message = "Payment amount must be equal to or greater than the total amount.";
        }
    }

    // Check if complete payment button is clicked
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complete_payment'])) {
        completePayment($admin_id, $conn);
        header('location: checkout.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../../css/admin_style.css">
    <link rel="stylesheet" href="../../css/checkout.css">
    <style>
        input{
            border: 1px solid lightgrey;
            text-align: right;
            font-weight: bold;
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Adjust the values for horizontal offset, vertical offset, blur radius, and color */
        }
    </style>
</head>
<body>
    <section>
        <div class="return">
            <a href="cart.php"><h1>Return to Cart</h1></a>
        </div>
    </section>
    <section class="checkout">
        <h2>Checkout</h2>
        <br>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <?php checkResult($admin_id, $grand_total, $conn, $payment_result);?>
            </table>
        </div>
    </section>
</body>
</html>
