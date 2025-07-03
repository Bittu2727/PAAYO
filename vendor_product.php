<?php

@include 'config.php';

session_start();

$vendor_id = $_SESSION['vendor_id'] ?? null;

if (!$vendor_id) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['add_product'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $city = filter_var($_POST['stock'], FILTER_SANITIZE_STRING);  // keeping name as 'stock' because the DB column is still named so
    $image = $_FILES['image'];
    $image_name = filter_var($image['name'], FILTER_SANITIZE_STRING);
    $image_size = $image['size'];
    $image_tmp_name = $image['tmp_name'];
    $image_folder = 'uploaded_img/' . $image_name;

    if (!$price || $price <= 0) {
        $message[] = 'Invalid price!';
    }  else {
        // Validate image type
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($image['type'], $allowed_types)) {
            $message[] = 'Invalid image format!';
        } elseif ($image_size > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            // ftech db bata vendor name
            $get_vendor_name = $conn->prepare("SELECT username FROM `users` WHERE user_id = ?");
            $get_vendor_name->execute([$vendor_id]);
            $vendor = $get_vendor_name->fetch(PDO::FETCH_ASSOC);

            if ($vendor) {
                $vendor_name = $vendor['username'];

                // halyo service
                $insert_products = $conn->prepare("INSERT INTO `products` ( vendor_id, name, category, description, price, image, stock, vendor_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insert_products->execute([$vendor_id, $name, $category, $description, $price, $image_name, $city, $vendor_name]);

                if ($insert_products) {
                    // Move image file
                    move_uploaded_file($image_tmp_name, $image_folder);
                    $message[] = 'New service added!';
                } else {
                    $message[] = 'Failed to add service!';
                }
            } else {
                $message[] = 'Failed to retrieve provider name!';
            }
        }
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $select_delete_image = $conn->prepare("SELECT image FROM `products` WHERE product_id = ?");
    $select_delete_image->execute([$delete_id]);
    $fetch_delete_image = $select_delete_image->fetch(PDO::FETCH_ASSOC);

    if ($fetch_delete_image) {
        $image_path = 'uploaded_img/' . $fetch_delete_image['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $delete_products = $conn->prepare("DELETE FROM `products` WHERE product_id = ?");
    $delete_products->execute([$delete_id]);

    $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE product_id = ?");
    $delete_wishlist->execute([$delete_id]);

    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE product_id = ?");
    $delete_cart->execute([$delete_id]);

    header('Location: vendor_product.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'vendor_header.php'; ?>

<section class="add-products">
    <h1 class="title">Add New Service</h1>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="flex">
            <div class="inputBox">
                <input type="text" name="name" class="box" required placeholder="Enter Service name">
                <select name="category" class="box" required>
                    <option value="" selected disabled>Select category</option>
                    <option value="Sofa Cleaning">Sofa Cleaning</option>
                    <option value="Carpet Cleaning">Carpet Cleaning</option>
                    <option value="Curtain Cleaning">Curtain Cleaning</option>
                    <option value="House Cleaning">House Cleaning</option>
                </select>
            </div>
            <div class="inputBox">
                <input type="number" min="0" name="price" class="box" required placeholder="Enter Service price">
                <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
            </div>
        </div>
        <textarea name="description" class="box" required placeholder="Enter service description" cols="30" rows="10"></textarea>
        <select name="stock" class="box" required>
    <option value="" selected disabled>Select city</option>
    <option value="Kathmandu">Kathmandu</option>
    <option value="Lalitpur">Lalitpur</option>
    <option value="Bhaktapur">Bhaktapur</option>
    <option value="Pokhara">Pokhara</option>
    <option value="Biratnagar">Biratnagar</option>
    <option value="Butwal">Butwal</option>
</select>
<input type="submit" class="btn" value="Add Service" name="add_product">
    </form>
    <?php
    $message = [];
    
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '<p class="message">' . htmlspecialchars($msg) . '</p>';
        }
    }
    ?>
</section>

<section class="show-products">
    <h1 class="title">Services Added</h1>

    <div class="box-container">
        <?php
        $show_products = $conn->prepare("SELECT p.product_id, p.name AS name, p.category, p.description, p.price, p.image, p.stock, p.vendor_name
    FROM products p
    WHERE p.vendor_id = ?");
        $show_products->execute([$vendor_id]);

        if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="box">
            <div class="price">Rs. <?= htmlspecialchars($fetch_products['price']); ?>/-</div>
            <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="">
            <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
            <div class="cat"><?= htmlspecialchars($fetch_products['category']); ?></div>
            <div class="description"><?= htmlspecialchars($fetch_products['description']); ?></div>
            <div class="city">Service Area: <?= htmlspecialchars($fetch_products['stock']); ?></div>
            <div class="vendor"><?= htmlspecialchars($fetch_products['vendor_name']); ?></div>
            <div class="flex-btn">
                <a href="vendor_update_product.php?update=<?= htmlspecialchars($fetch_products['product_id']); ?>" class="option-btn">Update</a>
                <a href="vendor_product.php?delete=<?= htmlspecialchars($fetch_products['product_id']); ?>" class="delete-btn" onclick="return confirm('Delete this Service?');">Delete</a>
            </div>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">No services added yet!</p>';
        }
        ?>
    </div>
</section>

<script src="js/script.js"></script>

</body>
</html>
