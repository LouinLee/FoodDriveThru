<?php
    include '../../config/db.php';
    require_once '../../security.php';
    include_once '../model/functions.php';
    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);

    if (isset($_POST['update'])) {
        $category_id = $_POST['cid'];
        $category_id = filter_var($category_id, FILTER_SANITIZE_STRING);
        $title = $_POST['title'];
        $title = filter_var($title, FILTER_SANITIZE_STRING);
        $image = $_FILES['image']['name'];
        $message = updateCategory($category_id, $title, $image, $conn);
        
        // Ensure $message is an array before merging with $_SESSION['message']
        if (!is_array($message)) {
            $message = [$message];
        }
        header('location: ../views/category.php');
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category</title>
    <link rel="stylesheet" href="../../css/admin_style.css">
</head>

<body>
    <section class="update-product">
        <?php
        $update_id = $_GET['update'];
        $show_category = $conn->prepare("SELECT * FROM `category` WHERE id = ?");
        $show_category->execute([$update_id]);
        if ($show_category->rowCount() > 0) {
            while ($fetch_category = $show_category->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <form action="" method="POST" enctype="multipart/form-data">
                    <h1 class="heading">update category</h1>
                    <input type="hidden" name="cid" value="<?= $fetch_category['id']; ?>" class="box">
                    <input type="text" name="title" value="<?= $fetch_category['title']; ?>" class="box" required>
                    <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
                    <input type="submit" name="update" value="update" class="btn">
                    <button type="button" onclick="goBack()" class="option-btn">go back</button>
                </form>
                <?php
            }
        } else {
            echo '<p class="empty">no category added yet!</p>';
        }
        ?>

    </section>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <script src="../js/admin_script.js"></script>

</body>

</html>