<?php
    function displayProfile($admin_id, $conn) {
        $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
        $select_profile->execute([$admin_id]);
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        
        // Check if admin profile exists
        if ($fetch_profile) {
            echo $fetch_profile['name'];
        } else {
            // Admin profile does not exist, redirect to logout page
            header("Location: ../../admin_logout.php");
            exit(); // Make sure to exit after redirection
        }
    }

    function adminQty($conn){
        $select_admins = $conn->prepare("SELECT COUNT(*) AS admin_count FROM `admin`");
        $select_admins->execute();
        $row = $select_admins->fetch();
        if ($row && isset($row['admin_count'])) {
            echo $row['admin_count'];
        } else {
            echo "No admin found.";
        }
    }

    function categoryQty($conn){
        $select_category = $conn->prepare("SELECT COUNT(*) AS category_count FROM `category`");
        $select_category->execute();
        $row = $select_category->fetch();
        if ($row && isset($row['category_count'])) {
            echo $row['category_count'];
        } else {
            echo "No category found.";
        }
    }

    function productQty($conn){
        $select_product = $conn->prepare("SELECT COUNT(*) AS product_count FROM `products`");
        $select_product->execute();
        $row = $select_product->fetch();
        if ($row && isset($row['product_count'])) {
            echo $row['product_count'];
        } else {
            echo "No products found.";
        }
    }

    function orderQty($conn){
        $select_product = $conn->prepare("SELECT MAX(order_number) AS max_order_number FROM `order_details`");
        $select_product->execute();
        $row = $select_product->fetch();
        
        if ($row && isset($row['max_order_number'])) {
            echo $row['max_order_number'];
        } else {
            echo "No orders found.";
        }
    }

    function checkUniq($conn, $event, $value){
        if($event === 'category' ){
            $select_category = $conn->prepare("SELECT * FROM `category` WHERE title=?");
            $select_category->execute([$value]);
            if($select_category->rowCount() > 0){
                $message[] = 'Category already exists!';
                return $message; // Return the $message array
            }
        }
        return []; // Return an empty array if no message is set
    }
    
    function addCategory($title, $image, $conn){
        $message = [];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../../uploaded_img/' . $image;
        $value = $title;
        $event = 'category';
        $uniqueMessage = checkUniq($conn, $event, $value);
        if(!empty($uniqueMessage)){
            return $uniqueMessage;
        }
        if ($image_size > 2000000) {
            $message[] = 'Image size is too large';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);
            $insert_category = $conn->prepare("INSERT INTO `category`(title, image) VALUES(?,?)");
            $insert_category->execute([$title, $image]);
            $message[] = 'New category added!';
        }
        return $message;
    }

    function deleteCategory($delete_id, $conn){
        // Check if the image is in use in the products or category database
        $image_in_use = false;
    
        // Check in the products database
        $check_products = $conn->prepare("SELECT COUNT(*) AS product_count FROM `products` WHERE image = (SELECT image FROM `category` WHERE id = ?)");
        $check_products->execute([$delete_id]);
        $product_count = $check_products->fetch(PDO::FETCH_ASSOC)['product_count'];
        if ($product_count > 0) {
            $image_in_use = true;
        }
        if ($image_in_use) {
            // Image is in use, do not unlink, proceed with deleting category from the database
            $delete_category = $conn->prepare("DELETE FROM `category` WHERE id = ?");
            $delete_category->execute([$delete_id]);
    
            if ($delete_category->rowCount() > 0) {
                $message = ['Category successfully deleted, but its associated image is still in use.'];
            } else {
                $message = ['There was an issue trying to delete the category.'];
            }
        } else {
            // Image is not in use, unlink the image file and then delete the category from the database
            $show_category = $conn->prepare("SELECT * FROM `category` WHERE id = ?");
            $show_category->execute([$delete_id]);
            $fetch_category = $show_category->fetch(PDO::FETCH_ASSOC);
            if ($fetch_category) {
                // Unlink the image file
                $image_path = '../../uploaded_img/' . $fetch_category['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                // Delete the category from the database
                $delete_category = $conn->prepare("DELETE FROM `category` WHERE id = ?");
                $delete_category->execute([$delete_id]);
    
                if ($delete_category->rowCount() > 0) {
                    $message = ['Category <strong>' . $fetch_category['title'] . '</strong> is deleted'];
                } else {
                    $message = ['There was an issue trying to delete the category.'];
                }
            } else {
                $message = ['Category not found.'];
            }
        }
        return $message;
    }    

    function showCategory($conn){
        $show_category = $conn->prepare("SELECT * FROM `category`");
        $show_category->execute();
        if ($show_category->rowCount() > 0) {
            while ($fetch_category = $show_category->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="box">
                    <img src="../../uploaded_img/<?= $fetch_category['image']; ?>" alt="">
                    <div class="categoryHeight">
                        <div class="name">Category:
                            <?= $fetch_category['title'] ?>
                        </div>
                    </div>
                    <div>
                        <div class="flex-btn">
                            <a href="../controllers/update_category.php?update=<?= $fetch_category['id']; ?>" class="option-btn">update</a>
                            <a href="category.php?delete=<?= $fetch_category['id']; ?>" class="delete-btn"
                                onclick="return confirm('Delete this category?');">delete</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">No category added yet!</p>';
        }
    }

    function updateCategory($category_id, $title, $image, $conn){
        $message = [];
    
        // Check if image is not empty
        if(!empty($image)){
            $image = filter_var($image, FILTER_SANITIZE_STRING);
            $image_size = $_FILES['image']['size'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_folder = '../../uploaded_img/' . $image;
    
            // Update only the image
            if ($image_size > 2000000) {
                $message[] = 'Image size is too large';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
                $update_category = $conn->prepare("UPDATE `category` SET image=? WHERE id=?");
                $update_category->execute([$image, $category_id]);
    
                // Check if image has been updated
                $message[] = 'Category Image is updated!';
    
                header('location: ../views/category.php');
                exit;
            }
        }
        
        // Update only the title
        $value = $title;
        $event = 'category';
        $uniqueMessage = checkUniq($conn, $event, $value);
    
        if(!empty($uniqueMessage)){
            $message[] = $uniqueMessage;
        } else {
            $update_category = $conn->prepare("UPDATE `category` SET title=? WHERE id=?");
            $update_category->execute([$title,$category_id]);
    
            if($value !== $title){
                $message[] = $value . ' category is updated into! <strong>' . $title . '</strong>';
            }
    
            header('location: ../views/category.php');
            exit;
        }
        
        return $message;
    }
    
    function selectCategory($conn) {
        $show_category = $conn->prepare("SELECT * FROM `category`");
        $show_category->execute();
        if ($show_category->rowCount() > 0) {
            while ($fetch_category = $show_category->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value=" . $fetch_category['id'] . ">" . $fetch_category['title'] . "</option>";
            }
        } else {
            echo '<p class="empty">No category added yet!</p>';
        }
    }
    
    function addProduct($name, $price, $category, $image, $conn) {
        $message = [];
    
        // Sanitize inputs
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $price = filter_var($price, FILTER_SANITIZE_STRING);
        $category = filter_var($category, FILTER_SANITIZE_STRING);
        $image = filter_var($image, FILTER_SANITIZE_STRING);
    
        // Validate inputs
        if (empty($name) || empty($price) || empty($category) || empty($image)) {
            $message[] = 'All fields are required.';
        }
    
        // Check if product name already exists in the same category
        $select_same_category = $conn->prepare("SELECT * FROM `products` WHERE name = ? AND category = ?");
        $select_same_category->execute([$name, $category]);
        if ($select_same_category->rowCount() > 0) {
            $message[] = 'Product name already exists in this category!';
        } else {
            // Check if product name exists in a different category
            $select_diff_category = $conn->prepare("SELECT * FROM `products` WHERE name = ? AND category != ?");
            $select_diff_category->execute([$name, $category]);
            if ($select_diff_category->rowCount() > 0) {
                $message[] = 'Product name already exists in a different category!';
            } else {
                // Check image size and type
                $image_size = $_FILES['image']['size'];
                if ($image_size > 2000000) {
                    $message[] = 'Image size is too large';
                }
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['image']['type'], $allowed_types)) {
                    $message[] = 'Only JPG, PNG, and GIF files are allowed';
                }
    
                // If no errors, proceed with adding the product
                if (empty($message)) {
                    $image_tmp_name = $_FILES['image']['tmp_name'];
                    $image_folder = '../../uploaded_img/' . $image;
    
                    // Move uploaded file
                    move_uploaded_file($image_tmp_name, $image_folder);
    
                    // Insert product into database
                    $insert_product = $conn->prepare("INSERT INTO `products`(name, category, price, image) VALUES(?,?,?,?)");
                    $insert_product->execute([$name, $category, $price, $image]);
    
                    $message[] = 'New product added!';
                }
            }
        }
    
        return $message;
    }
    
    function showProduct($conn){
        $show_products = $conn->prepare("SELECT * FROM `products`");
        $show_products->execute();
        $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
        if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                $p = $fetch_products['category'];
                $productCategory = $conn->prepare("SELECT title FROM `category` WHERE id=?");
                $productCategory->execute([$p]);
                $pCategory = $productCategory->fetch(PDO::FETCH_ASSOC);
                echo '<div class="box">';
                echo '<img src="../../uploaded_img/' .  $fetch_products['image'] . '"alt="">';
                echo '<div class="flex">';
                echo '<div class="price"><span>$</span>' . $fetch_products['price'] . '<span>/-</span>' . '</div>';
                echo '<div class="category">' . $pCategory['title'] . '</div>';
                echo '</div>';
                echo '<div class="name">' . $fetch_products['name'] . '</div>';
                echo '<div class="flex-btn">';
                echo '<a href="../controllers/update_product.php?update=' . $fetch_products['id'] . '" class="option-btn">update</a>';
                echo '<a href="products.php?delete=' .  $fetch_products['id'] . '" class="delete-btn" onclick="return confirm("delete this product?");">delete</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>no products added yet!</p>';
        }
    }

    function deleteProduct($delete_id, $conn){
        $message = [];

        // Select the product information
        $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $select_product->execute([$delete_id]);
        $product = $select_product->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $message[] = 'Product not found.';
            return $message;
        }

        // Check if the image is in use by other products
        $check_other_products = $conn->prepare("SELECT COUNT(*) AS product_count FROM `products` WHERE image = ?");
        $check_other_products->execute([$product['image']]);
        $product_count = $check_other_products->fetch(PDO::FETCH_ASSOC)['product_count'];

        if ($product_count <= 1) {
            // If the image is not used by any other product, delete it
            $image_path = '../../uploaded_img/' . $product['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Delete the product from the database
        $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
        if ($delete_product->execute([$delete_id])) {
            $message[] = 'Product deleted successfully.';
        } else {
            $message[] = 'Failed to delete product.';
        }

        return $message;
    }

    // Define a function to sanitize input
    function sanitizeInput($data){
        return filter_var($data, FILTER_SANITIZE_STRING);
    }

    function updateProduct($product_id, $name, $price, $category, $image, $conn){
        try {
            // Check if the provided name already exists in other products
            $checkNameStmt = $conn->prepare("SELECT COUNT(*) FROM `products` WHERE name = ? AND id != ?");
            $checkNameStmt->execute([$name, $product_id]);
            $nameExists = $checkNameStmt->fetchColumn();
    
            if ($nameExists > 0) {
                return ['error' => 'Name already exists in other products!'];
            } else {
                // Update product details
                $updateProductStmt = $conn->prepare("UPDATE `products` SET name = ?, category = ?, price = ? WHERE id = ?");
                $updateProductStmt->execute([$name, $category, $price, $product_id]);
    
                // Handle image update
                if (!empty($image)) {
                    $oldImageStmt = $conn->prepare("SELECT `image` FROM `products` WHERE id=?");
                    $oldImageStmt->execute([$product_id]);
                    $oldImage = $oldImageStmt->fetchColumn();
    
                    $image_size = $_FILES['image']['size'];
                    $image_tmp_name = $_FILES['image']['tmp_name'];
                    $image_folder = '../../uploaded_img/' . $image;
    
                    if ($image_size > 2000000) {
                        $message[] = 'Image size is too large!';
                    } else {
                        move_uploaded_file($image_tmp_name, $image_folder);
    
                        // Update image path in database
                        $updateImageStmt = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
                        $updateImageStmt->execute([$image, $product_id]);
    
                        // Delete old image if exists
                        if (!empty($oldImage)) {
                            unlink('../../uploaded_img/' . $oldImage);
                        }
                        $message[] = 'Image updated!';
                    }
                }
                return true;
            }
        } catch (Exception $e) {
            return ['error' => 'An error occurred: ' . $e->getMessage()];
        }
    }
    function updateForm($conn) {
        $update_id = $_GET['update'] ?? '';
        $show_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $show_products->execute([$update_id]);
        if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                echo '<form action="" method="POST" enctype="multipart/form-data">';
                echo '<input type="hidden" name="pid" value="' . htmlspecialchars($fetch_products['id']) . '">';
                echo '<input type="hidden" name="old_image" value="' . htmlspecialchars($fetch_products['image']) . '">';
                echo '<div class="form-image-preview">';
                echo '<img src="../../uploaded_img/' . htmlspecialchars($fetch_products['image']) . '" alt="Product Image" class="form-image">';
                echo '</div>';
                echo '<span>Update Name</span>';
                echo '<input type="text" id="name" required placeholder="Enter product name" name="name" maxlength="100" class="box" value="' . htmlspecialchars($fetch_products['name']) . '">';
                echo '<span>Update Price</span>';
                echo '<input type="number" id="price" min="0" max="9999999999" required placeholder="Enter product price" name="price" class="box" value="' . htmlspecialchars($fetch_products['price']) . '">';
                echo '<span>Update Category</span>';
                echo '<select id="category" name="category" class="box" required>';
                // Fetch all categories from the 'category' table
                $show_category = $conn->prepare("SELECT * FROM `category`");
                $show_category->execute();
                $currentCategory = htmlspecialchars($fetch_products['category']);
                if ($show_category->rowCount() > 0) {
                    while ($fetch_category = $show_category->fetch(PDO::FETCH_ASSOC)) {
                        // Skip the current category to avoid duplication
                        if ($currentCategory != $fetch_category['title']) {
                            echo '<option value="' . htmlspecialchars($fetch_category['id']) . '">' . htmlspecialchars($fetch_category['title']) . '</option>';
                        }
                    }
                }
                echo '</select>';
                echo '<span>Update Image</span>';
                echo '<input type="file" id="image" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">';
                echo '<div class="flex-btn">';
                echo '<input type="submit" value="update" class="btn" name="update">';
                echo '<a href="../views/products.php" class="option-btn">Go Back</a>';
                echo '</div>';
                echo '</form>';
            }
        } else {
            echo '<p class="empty">No products added yet!</p>';
        }
    }

    function adminForm($conn) {
        $admin_id = $_SESSION['admin_id'];
        $sn=0;
        $select_account = $conn->prepare("SELECT * FROM `admin`");
        $select_account->execute();
        if ($select_account->rowCount() > 0) {
           while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="box">';
            echo '<p> Admin ID : <span>' .  ++$sn . '</span> </p>';
            echo '<p> Username : <span>' . $fetch_accounts['name'] . '</span> </p>';
            echo '<div class="flex-btn">';
            echo '<a href="admin_accounts.php?delete=' . $fetch_accounts['id'] . '" class="delete-btn" onclick="return confirm(\'Delete this account?\');">delete</a>'; // Fixed onclick attribute
            if ($fetch_accounts['id'] == $admin_id) {
                echo '<a href="../controllers/update_admin.php" class="option-btn">Update</a>';
            }
            echo '</div>';
            echo '</div>';
           }
        } else {
            echo '<p class="empty">no accounts available</p>';
        }
    }

    function deleteAdmin($delete_id, $conn) {
        $delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
        $delete_admin->execute([$delete_id]);
        return true;
    }

    function updateAdmin($admin_id, $name, $old_pass, $new_pass, $confirm_pass, $conn, &$message) {    
        // Check if old password, new password, and confirm password are not empty
        if (!empty($old_pass) && !empty($new_pass) && !empty($confirm_pass)) {
            $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
    
            // Retrieve previous password and admin details from the database
            $select_old_pass = $conn->prepare("SELECT password FROM `admin` WHERE id = ?");
            $select_old_pass->execute([$admin_id]);
            $fetch_prev_pass = $select_old_pass->fetch(PDO::FETCH_ASSOC);
            $prev_pass = $fetch_prev_pass['password'];
    
            $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE id=?");
            $select_admin->execute([$admin_id]);
            $fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);
    
            // Check if old password matches the stored password
            $old_pass = sha1($old_pass);
            if ($old_pass !== $prev_pass) {
                $message = 'Old password does not match!';
            } else {
                // Check if admin profile remains unchanged
                if($name === $fetch_admin["name"] && $new_pass === $fetch_admin["password"]) {
                    $message = 'Admin profile remains the same.';
                    $_SESSION['message'] = $message;
                    header('location: ../views/admin_accounts.php');
                    return;
                } else {
                    // If old password is correct, proceed with updating username
                    if (!empty($name)) {
                        $select_name = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
                        $select_name->execute([$name]);
                        if ($select_name->rowCount() > 0) {
                            $message = 'Username already taken!';
                        } else {
                            $update_name = $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
                            $update_name->execute([$name, $admin_id]);
                            $message = $name . ' updated successfully';
                        }
                    }
                    // Check if new password matches the confirmed password
                    $new_pass = sha1($new_pass);
                    $confirm_pass = sha1($confirm_pass);
                    if ($new_pass !== $confirm_pass) {
                        $message = 'Confirm password does not match!';
                    } else {
                        // Update password if it's not empty
                        if ($new_pass !== $empty_pass) {
                            $update_pass = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
                            $update_pass->execute([$new_pass, $admin_id]);
                            $message = 'Password updated successfully!';
                            $_SESSION['message'] = $message;
                            header('location: ../views/admin_accounts.php');
                            exit;
                        } else {
                            $message = 'Please enter new password';
                        }
                    }
                }
            }
        } else {
            $message = 'All password fields are required!';
        }
    }
    function showOrders($admin_id, $conn){
        if ($admin_id == '') {
            echo '<p class="empty">Please login to see your orders</p>';
        } else {
            $select_orders = $conn->prepare("SELECT DISTINCT order_number FROM `order_details` WHERE admin_id = ?");
            $select_orders->execute([$admin_id]);
            
            if ($select_orders->rowCount() > 0) {
                while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                    $order_number = $fetch_orders['order_number'];
                    
                    // Retrieve order details for the current order number
                    $select_order_details = $conn->prepare("SELECT * FROM `order_details` WHERE admin_id = ? AND order_number = ?");
                    $select_order_details->execute([$admin_id, $order_number]);
                    
                    // Initialize variables to calculate total
                    $total = 0;
                    
                    // Initialize a flag to indicate whether it's the first row for this order number
                    $first_row = true;
                    
                    // Display order details
                    while ($fetch_order_details = $select_order_details->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>';
                        
                        // Check if it's the first row for this order number
                        if ($first_row) {
                            echo '<td rowspan="' . $select_order_details->rowCount() . '">' . $order_number . '</td>';
                            $first_row = false;
                        }
                        
                        echo '<td>' . $fetch_order_details['product_name'] . '</td>';
                        echo '<td>' . $fetch_order_details['quantity'] . '</td>';
                        echo '<td>' . $fetch_order_details['price'] . '</td>';
                        echo '<td>' . $fetch_order_details['total'] . '</td>';
                        
                        // Display status and order date
                        echo '<td>' . $fetch_order_details['status'] . '</td>';
                        echo '<td>' . $fetch_order_details['created_at'] . '</td>';
                        
                        echo '</tr>';
                        
                        // Increment total by the total price of this item
                        $total += $fetch_order_details['total'];
                    }
                    // Display the total price for all items in this order
                    echo '<tr>';
                    echo '<td colspan="4"><strong>Total</strong></td>';
                    echo '<td>' . $total . '</td>'; // Display the total price
                    echo '<td colspan="2"></td>'; // Remaining columns are left empty
                    echo '</tr>';
                }
            } else {
                echo '<div class="errorMessage">';
                echo '<p class="empty">No orders found</p>';
                echo '<br>';
                echo '</div>';
            }
        }
    }

    function showFoods($conn){
        $show_products = $conn->prepare("SELECT * FROM `products`");
        $show_products->execute();
    
        // Check whether the foods are available or not
        if($show_products->rowCount() > 0) {
            // Foods Available
            while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                // Get the Values
                $id = $fetch_products['id'];
                $title = $fetch_products['name'];
                $category = $fetch_products['category'];
                $price = $fetch_products['price'];
                $image = $fetch_products['image'];
    
                echo '<div class="food-menu-box">';
                echo '<div class="food-menu-img">';
                if($image==""){
                    echo "<div class='error'>Image not Available.</div>";
                } else {
                    echo '<img src="../../uploaded_img/' . $image . '" alt="' . $image . '" class="img-responsiveOrder img-curveOrder">';
                }
                echo '</div>';
                echo '<div class="food-menu-desc">';
                echo '<h4>' . $title . '</h4>';
                echo '<p class="food-price">₹' . $price . '</p>';
                echo '<form method="POST">';
                echo '<input type="hidden" name="id" value="'. $id .'">';
                echo '<p>Order Qty : <input type="number" min="0" max="99" name="qty" id="" placeholder=".."></p>'; // Allow quantity to be 0
                echo '<br>';
                echo '<input type="submit" name="submit" value="Add To Cart" class="btnOrder btn-primaryOrder">';
                echo '</form>'; 
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<div class='error'>Food not found.</div>";
        }
    }

    function searchFoods($conn, $search, $message){
        $show_products = $conn->prepare("SELECT * FROM `products` WHERE `name` LIKE '%$search%'");
        $show_products->execute();
    
        // Check whether the foods are available or not
        if($show_products->rowCount() > 0) {
            // Foods Available
            while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                // Get the Values
                $id = $fetch_products['id'];
                $title = $fetch_products['name'];
                $price = $fetch_products['price'];
                $image = $fetch_products['image'];
    
                echo '<div class="food-menu-box">';
                echo '<div class="food-menu-img">';
                // Check whether image available or not
                if($image==""){
                    echo "<div class='error'>Image not Available.</div>";
                } else {
                    echo '<img src="../../uploaded_img/' . $image . '" alt="' . $image . '" class="img-responsiveOrder img-curveOrder">';
                }
                echo '</div>';
                echo '<div class="food-menu-desc">';
                echo '<h4>' . $title . '</h4>';
                echo '<p class="food-price">₹' . $price . '</p>';
                echo '<form method="POST">';
                echo '<input type="hidden" name="id" value="'. $id .'">';
                echo '<p>Order Qty : <input type="number" min="1" max="99" name="qty" id="" placeholder=".."></p>';
                echo '<br>';
                echo '<input type="submit" name="submit" value="Add To Cart" class="btnOrder btn-primaryOrder">';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            // Food not Available
            echo "<div class='error'>Food not found.</div>";
        }
    }    
    
    function addOrder($admin_id, $product_id, $qty, $conn){
        $message = ""; // Initialize message variable
        // Check if quantity is greater than 0 before adding to cart
        if ($qty > 0) {
            $insert_cart = $conn->prepare("INSERT INTO `cart` (admin_id, product_id, quantity, created_at) VALUES(?,?,?,now())");
            $insert_cart->execute([$admin_id, $product_id, $qty]);
            if ($insert_cart == true) {
                $message = 'New Orders added!';
            } else {
                $message = 'New Orders failed!';
            }
        } else {
            // Quantity is 0, set message accordingly
            $message = 'Product quantity is 0, please try again!';
        }
        return $message;
    }

    function showCart($admin_id, $conn){
        $grand_total = 0;
        $show_cart = $conn->prepare("SELECT cart.id, cart.quantity, products.name, category.title as category_title, products.price, products.image FROM cart JOIN products ON cart.product_id = products.id JOIN category ON products.category = category.id WHERE cart.admin_id = ?");
        $show_cart->execute([$admin_id]);

        if ($show_cart->rowCount() > 0) {
            echo '<div class="box-container">';
            while ($fetch_cart = $show_cart->fetch(PDO::FETCH_ASSOC)) { 
                echo '<div class="box">';
                echo '<form action="cart.php" method="POST">';
                echo '<input type="hidden" name="cart_id" value="'. $fetch_cart['id'].'">';
                echo '<img src="../../uploaded_img/'. $fetch_cart['image'] .'" alt="">';
                echo '<div class="name">'. $fetch_cart['name'] .'</div>';
                echo '<br>';
                echo '<div class="category">'. $fetch_cart['category_title'] .'</div>'; // Display category title
                echo '<div class="price">';
                echo '<span>$</span>';
                echo $fetch_cart['price'] . '<span>/-</span>';
                echo '</div>';
                echo '<div class="qty">';
                echo '<label>Ordered : </label>';
                echo '<input type="number" name="qty" value="'. $fetch_cart['quantity'] .'" min="1">';
                echo '</div>';
                echo '<div class="flex-btn">';
                echo '<input type="submit" value="Update" name="update" class="option-btn">';
                echo '<button type="submit" name="delete" class="delete-btn" value="'. $fetch_cart['id'] .'" onclick="return confirm(\'Delete this product?\');">Delete</button>';
                echo '</div>';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p class="empty">No products added yet!</p>';
        }
    }

    // Function to delete a single item from the cart
    function deleteCartItem($cart_id, $conn) {
        $stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ?");
        $stmt->execute([$cart_id]);
        $quantity = $stmt->fetchColumn();

        if ($quantity > 0) {
            // If quantity is greater than 0, delete the item
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->execute([$cart_id]);
        }
    }
    
    function updateCartItem($cart_id, $quantity, $conn) {
        $updateCartStmt = $conn->prepare("UPDATE `cart` SET quantity=? WHERE id = ?");
        $updateCartStmt->execute([$quantity, $cart_id]);
    }

    function orderTotal($admin_id, $conn) {
        $total = 0;
    
        $show_cart = $conn->prepare("SELECT products.price, cart.quantity FROM cart JOIN products ON cart.product_id = products.id WHERE cart.admin_id = ?");
        $show_cart->execute([$admin_id]);
    
        while ($fetch_cart = $show_cart->fetch(PDO::FETCH_ASSOC)) {
            $total += $fetch_cart['price'] * $fetch_cart['quantity'];
        }
    
        return $total;
    }

    function deleteAllCartItems($admin_id, $conn) {
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE admin_id = ?");
        $delete_cart->execute([$admin_id]);
    }

    function checkResult($admin_id, $grand_total, $conn, $payment_result = null){
        $total_price = $grand_total;
        $show_cart = $conn->prepare("SELECT cart.id, cart.quantity, products.name, products.price FROM cart JOIN products ON cart.product_id = products.id WHERE cart.admin_id = ?");
        $show_cart->execute([$admin_id]);
    
        // Build table rows for cart items
        echo '<tbody>';
        while ($fetch_cart = $show_cart->fetch(PDO::FETCH_ASSOC)) {
            $subtotal = $fetch_cart['price'] * $fetch_cart['quantity'];
            $total_price += $subtotal;
            echo '<tr>';
            echo '<td>' . $fetch_cart['name'] . '</td>';
            echo '<td>$' . $fetch_cart['price'] . '</td>';
            echo '<td style="text-align: center;">' . $fetch_cart['quantity'] . '</td>';
            echo '<td>$' . number_format($subtotal, 2) . '</td>';
            echo '</tr>';
        }
        // Display payment section if payment result is set
        if(isset($payment_result)) {
            echo '<tr>';
            echo '<td colspan="3"><strong>Payment Amount:</strong></td>';
            echo '<td><strong>$' . number_format($payment_result['payment_amount'], 2) .'</strong></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="3"><strong>Change</strong>:</td>';
            echo '<td><strong>$' . number_format($payment_result['change'], 2) .'</strong></td>';
            echo '</tr>';
        }
        // Display the total at the top of the checkout section
        echo '<tr>';
        echo '<td colspan="3"><strong>Total:</strong></td>';
        echo '<td><strong>$'. number_format($grand_total, 2) .'</strong></td>';
        echo '</tr>';
        echo '</tbody>';
        // Display "Check Result" button
        if(!isset($payment_result)) {
            echo '<tbody>';
            echo '<tr>';
            echo '<td colspan="4">';
            echo '<form action="checkout.php" method="POST" id="payment_form">';
            echo '<div class="payment-input">';
            echo '<label for="payment_amount">Enter Payment Amount:</label>';
            echo '<input type="number" id="payment_amount" name="payment_amount" min="'. $grand_total .'" step="0.01" required>';
            echo '</div>';
            echo '<div class="checkout-btn">';
            echo '<button type="submit" class="btn" '. ($grand_total > 0 ? '' : 'disabled') .'>Check Result</button>';
            echo '</div>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
            echo '</tbody>';
        }
    
        // Display "Complete Payment" button
        if(isset($payment_result)) {
            echo '<tfoot>';
            echo '<tr>';
            echo '<td colspan="4">';
            echo '<form action="checkout.php" method="POST">';
            echo '<button type="submit" class="btn" name="complete_payment">Complete Payment</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
            echo '</tfoot>';
        }
    }    
    function completePayment($admin_id, $conn) {
        $order_number = getOrderNumber($conn);
        $select_cart = $conn->prepare("SELECT cart.*, products.name AS product_name, products.price FROM cart JOIN products ON cart.product_id = products.id WHERE cart.admin_id = ?");
        $select_cart->execute([$admin_id]);

        $max_order_id_stmt = $conn->prepare("SELECT MAX(id) AS max_id FROM `order_details`");
        $max_order_id_stmt->execute();
        $max_order_id_row = $max_order_id_stmt->fetch(PDO::FETCH_ASSOC);
        $max_order_id = $max_order_id_row['max_id'];
        $cart_count = $select_cart->rowCount();

        // Calculate order ID
        $order_id = $max_order_id + 1;
        // Loop through each item in the cart
        while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
            $product_name = $fetch_cart['product_name'];
            $quantity = $fetch_cart['quantity'];
            $status = 'complete';
            $price = $fetch_cart['price'];
            $total = $quantity * $price;

            // Insert into order_details table
            $insert_order_details = $conn->prepare("INSERT INTO `order_details` (order_number, admin_id, product_name, quantity, price, total, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_order_details->execute([$order_number, $admin_id, $product_name, $quantity, $price, $total, $status]);

            // Check if all items in the current cart have been processed
            if ($order_id > $max_order_id + $cart_count) {
                // Increment order number for the next cart
                $order_number++;
                // Reset order_id to start from the next batch
                $order_id = $max_order_id + 1;
            }
        }
        // Clear cart after completing payment
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE admin_id = ?");
        $delete_cart->execute([$admin_id]);

        // Return success message or true if needed
        return true;
    }
    
    function getOrderNumber($conn) {
        // Check if any orders exist
        $order_count_stmt = $conn->prepare("SELECT COUNT(*) AS order_count FROM `order_details`");
        $order_count_stmt->execute();
        $order_count = $order_count_stmt->fetch(PDO::FETCH_ASSOC)['order_count'];

        // If no orders exist, return 1, else return the next order number
        if ($order_count == 0) {
            return 1;
        } else {
            // Get the highest existing order number
            $max_order_number_stmt = $conn->prepare("SELECT MAX(order_number) AS max_order_number FROM `order_details`");
            $max_order_number_stmt->execute();
            $max_order_number = $max_order_number_stmt->fetch(PDO::FETCH_ASSOC)['max_order_number'];
            // Return the next order number
            return $max_order_number + 1;
        }
    }
    function registerInside($name, $pass, $cpass, $conn){
        $message = [];
        $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
        $select_admin->execute([$name]);

        if ($select_admin->rowCount() > 0) {
            $message[] = 'Username already exists!';
        } else {
            if ($pass != $cpass) {
                $message[] = 'Confirm password does not match!';
            } else {
                $insert_admin = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
                $insert_admin->execute([$name, $cpass]);
                $message[] = 'New admin registered!';
                exit;
            }
        }
        return $message;
    }

?>