<?php
    include '../../config/db.php';
    include_once 'functions.php';
    require_once '../../security.php';
    $admin_id = $_SESSION['admin_id'];
    checkLogin($admin_id, $conn);

    // Initialize $message array to avoid errors
    $message = array();

    if (isset($message)) {
        foreach ($message as $msg) {
            echo '
            <div class="message">
                <span>' . $msg . '</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
            ';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrivethruSystem</title>
    <script src="home.js"></script>
    <script src="../js/admin_script.js"></script>
    <script>window.onload = loadIframeContent;</script>
    <link rel="stylesheet" href="../../css/admin_style.css">
    <style>a:hover{color:darkblue;}</style>
</head>
<body>
    <header class="header">
        <section class="flex">
            <a onclick="loadPage('../views/dashboard.php')" class="logo" style="cursor: pointer;">Admin<span>Panel</span></a>
            <nav class="navbar" style="cursor: pointer;">
                <a onclick="loadPage('../views/dashboard.php')">Home</a>
                <a onclick="loadPage('../views/admin_accounts.php')">Admin</a>
                <a onclick="loadPage('../views/category.php')">Category</a>
                <a onclick="loadPage('../views/products.php')">Product</a>
                <a onclick="loadPage('../views/orders.php')">Orders</a>
            </nav>
            <a href="../../admin_logout.php" onclick="return confirm('logout from this website?');" class="logout-btn">logout</a>
            <div class="profile">
                <p><?= displayProfile($admin_id,$conn); ?></p>
                <a href="update_profile.php" class="btn">update profile</a>
                <div class="flex-btn">
                    <a onclick="loadPage('../../admin_login.php')" class="option-btn">login</a>
                    <a onclick="loadPage('../../register_admin.php')" class="option-btn">register</a>
                </div>
            </div>
        </section>
    </header>
    <iframe id="iframeContent" width="100%" height="1000px" frameborder="0"></iframe>
<?php
    include '../partials/footer.php';
?>