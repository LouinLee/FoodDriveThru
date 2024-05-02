<?php
    include '../../config/db.php';
    require_once '../../security.php';
    require_once '../model/functions.php';
    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);
    $messages = '';
    if (isset($_POST['add_category'])) {
        $title = $_POST['title'];
        $title = filter_var($title, FILTER_SANITIZE_STRING);
        $event = 'category';
        checkUniq($conn, $event, $title);
        $image = $_FILES['image']['name'];
        $message = addCategory($title, $image, $conn);
    }
    if (isset($_GET['delete'])) {
        $delete_id = $_GET['delete'];
        $message = deleteCategory($delete_id, $conn);
    }
    if(isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
    }

    if(isset($message)) {
        foreach($message as $msg) {
            echo '<div class="message"><span>' . $msg . '</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>category</title>
    <script src="../js/admin_script.js"></script>
    <link rel="stylesheet" href="../../css/admin_style.css">
</head>
<body>
    <section class="add-products">
        <form method="POST" enctype="multipart/form-data">
            <h3>add category</h3>
            <input type="text" name="title" placeholder="Category name" class="box" require>
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
            <input type="submit" name="add_category" value="Add Category" class="btn">
            <input type="reset" name="reset" value="Reset" class="delete-btn">
        </form>
    </section>
    <section class="show-products" style="padding-top: 0;">
        <div class="box-container">
            <?php
                showCategory($conn);
            ?>
        </div>
    </section>
</body>
</html>
