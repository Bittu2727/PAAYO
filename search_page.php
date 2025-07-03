<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Add to wishlist
if (isset($_POST['add_to_wishlist'])) {
    $pid = filter_var($_POST['product_id'], FILTER_SANITIZE_STRING);
    $p_name = filter_var($_POST['p_name'], FILTER_SANITIZE_STRING);
    $p_price = filter_var($_POST['p_price'], FILTER_SANITIZE_STRING);
    $p_image = filter_var($_POST['p_image'], FILTER_SANITIZE_STRING);

    $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
    $check_wishlist_numbers->execute([$p_name, $user_id]);

    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->execute([$p_name, $user_id]);

    if ($check_wishlist_numbers->rowCount() > 0) {
        $message[] = 'Already added to wishlist!';
    } elseif ($check_cart_numbers->rowCount() > 0) {
        $message[] = 'Already Booked!';
    } else {
        $insert_wishlist = $conn->prepare("INSERT INTO `wishlist` (user_id, product_id, name, price, image) VALUES (?, ?, ?, ?, ?)");
        $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
        $message[] = 'Added to wishlist!';
    }
}

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $pid = filter_var($_POST['product_id'], FILTER_SANITIZE_STRING);
    $p_name = filter_var($_POST['p_name'], FILTER_SANITIZE_STRING);
    $p_price = filter_var($_POST['p_price'], FILTER_SANITIZE_STRING);
    $p_image = filter_var($_POST['p_image'], FILTER_SANITIZE_STRING);

    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->execute([$p_name, $user_id]);

    if ($check_cart_numbers->rowCount() > 0) {
        $message[] = 'Booked!';
    } else {
        $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
        $check_wishlist_numbers->execute([$p_name, $user_id]);

        if ($check_wishlist_numbers->rowCount() > 0) {
            $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
            $delete_wishlist->execute([$p_name, $user_id]);
        }

        $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, product_id, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
        $message[] = 'Added Booking!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="search-form">
    <form action="" method="POST" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">

        <!-- Search Input -->
        <input type="text" class="box" name="search_box" placeholder="Search services..." style="flex: 1 1 200px; max-width: 300px;">

        <!-- Filter Button with Icon -->
        <button type="button" onclick="toggleCityDropdown()" style="display: flex; align-items: center; gap: 8px; padding: 8px 14px; border: 2px solid #dc3545; border-radius: 6px; background-color: white; color: #dc3545; font-weight: 600; cursor: pointer;">
            <i class="fas fa-filter"></i>
            <span>Sort</span>
        </button>

        <!-- City Dropdown -->
        <select name="filter_city" id="cityDropdown" style="display: none; width: 180px; padding: 6px 10px; font-size: 0.95rem; border: 2px solid #dc3545; border-radius: 6px; background-color: white; color: #333;">
            <option value="">Filter by City</option>
            <option value="Kathmandu">Kathmandu</option>
            <option value="Lalitpur">Lalitpur</option>
            <option value="Bhaktapur">Bhaktapur</option>
            <option value="Pokhara">Pokhara</option>
            <option value="Butwal">Butwal</option>
            <option value="Biratnagar">Biratnagar</option>
        </select>

        <!-- Submit Button -->
        <input type="submit" name="search_btn" value="Search" class="btn">
    </form>
</section>

<section class="products" style="padding-top: 0; min-height: 100vh;">
    <div class="box-container">

    <?php
    if (isset($_POST['search_btn'])) {
        $search_box = filter_var($_POST['search_box'], FILTER_SANITIZE_STRING);
        $filter_city = $_POST['filter_city'] ?? '';

        if (!empty($filter_city)) {
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE (name LIKE ? OR category LIKE ?) AND stock = ?");
            $select_products->execute(["%{$search_box}%", "%{$search_box}%", $filter_city]);
        } else {
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ? OR category LIKE ?");
            $select_products->execute(["%{$search_box}%", "%{$search_box}%"]);
        }

        if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <form action="" class="box" method="POST">
        <div class="price">Rs.<span><?= htmlspecialchars($fetch_products['price']); ?></span>/-</div>
        <a href="view_page.php?product_id=<?= htmlspecialchars($fetch_products['product_id']); ?>" class="fas fa-eye"></a>
        <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="">
        <div class="product-details" style="padding: 10px;">
            <div class="name" style="font-size: 2.1em; margin-bottom: 10px;">
                <?= htmlspecialchars($fetch_products['name']); ?>
            </div>
            <div class="details" style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 1.7em; color: #666;">Service Area: <?= htmlspecialchars($fetch_products['stock']); ?></span>
                <span style="font-size: 1.7em; color: #006400;">Provider: <?= htmlspecialchars($fetch_products['vendor_name']); ?></span>
            </div>
        </div>
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($fetch_products['product_id']); ?>">
        <input type="hidden" name="p_name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
        <input type="hidden" name="p_price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
        <input type="hidden" name="p_image" value="<?= htmlspecialchars($fetch_products['image']); ?>">

        <input type="submit" value="Book Now" class="btn" name="add_to_cart">
    </form>
    <?php
            }
        } else {
            echo '<p class="empty">No results found!</p>';
        }
    }
    ?>

    </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
<script>
function toggleCityDropdown() {
    const dropdown = document.getElementById('cityDropdown');
    dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'inline-block' : 'none';
}
</script>

</body>
</html>
