<?php
    require_once '../model/functions.php';
    require_once '../../config/db.php';
    require_once '../../security.php';
    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);
    $messages = [];

    if (isset($_POST['update'])) {
        $productId = sanitizeInput($_POST['pid']);
        $name = sanitizeInput($_POST['name']);
        $price = sanitizeInput($_POST['price']);
        $category = sanitizeInput($_POST['category']);
        $image = sanitizeInput($_FILES['image']['name']);
    
        $result = updateProduct($productId, $name, $price, $category, $image, $conn);
        if ($result === true) {
            // Product updated successfully, redirect to products.php
            header('location: ../views/products.php');
            exit;
        } else {
            // Error occurred, display error message
            $messages[] = $result['error'];
        }
    }

    if (!empty($messages)) {
        $message = implode('<br>', $messages);
        echo '<div class="message"><span>' . htmlspecialchars($message) . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <script src="../js/admin_script.js"></script>
    <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>
<section class="update-product">
    <h1 class="heading">Update Product</h1>
    <?php updateForm($conn); ?>
</section>
</body>
</html>