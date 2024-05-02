<?php
    include '../../config/db.php';
    require_once '../model/functions.php';
    require_once '../../security.php';

    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);

    $message = '';

    if (isset($_POST['add_product'])) {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
        $image = $_FILES['image']['name'];

        $message = addProduct($name, $price, $category, $image, $conn);
    }

    if (isset($_GET['delete'])) {
        $delete_id = $_GET['delete'];
        $message = deleteProduct($delete_id, $conn);
    }

    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
    }

    if (is_array($message)) {
        $message = implode('<br>', $message);
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
    <title>Product</title>
    <script src="../js/admin_script.js"></script>
    <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>

<section class="add-products">
    <form action="" method="POST" enctype="multipart/form-data">
        <h3>add product</h3>
        <input type="text" required placeholder="Enter product name" name="name" maxlength="100" class="box">
        <input type="number" min="0" max="9999999999" required placeholder="Enter product price" name="price"
               onkeypress="if(this.value.length == 10) return false;" class="box">
        <select name="category" class="box" required>
            <option value="" disabled selected>Select category</option>
            <?php selectCategory($conn); ?>
        </select>
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
        <input type="submit" value="add product" name="add_product" class="btn">
    </form>
</section>

<section class="show-products" style="padding-top: 0;">
    <div class="box-container">
        <?php showProduct($conn) ?>
    </div>
</section>
</body>
</html>
