<?php
@include 'config.php'; // Database connection

// Assume $user_id is defined somewhere before this script
// Example: $user_id = $_SESSION['user_id'] ?? null;

if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<header class="header">
    <div class="flex">
        <a href="home.php" class="logo">
            <img src="paayo1 (1).png" alt="Sajilo Grocers Logo" class="logo-img">
        </a>

        <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="shop.php">Services</a>
            <a href="orders.php">Bookings</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <a href="search_page.php" class="fas fa-search"></a>
            <?php
            $cart_count = 0;
$wishlist_count = 0;
            if (isset($user_id)) {
                $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $count_cart_items->execute([$user_id]);
                    $cart_count = $count_cart_items->rowCount();

                $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
                $count_wishlist_items->execute([$user_id]);
                    $wishlist_count = $count_wishlist_items->rowCount();
            } else {
                $count_cart_items = new PDOStatement(); // Dummy object to avoid errors
                $count_wishlist_items = new PDOStatement(); // Dummy object to avoid errors
            }
            ?>
                <a href="cart.php" title="Bookings"><i class="fas fa-calendar-check"></i><span>(<?= htmlspecialchars($cart_count); ?>)</span></a>

        </div>

        <div class="profile">
            <?php
            if (isset($user_id)) {
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE user_id = ?");
                $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            }
            ?>
            <img src="uploaded_img/<?= htmlspecialchars($fetch_profile['image'] ?? 'default.png'); ?>" alt="Profile Image">
            <p><?= htmlspecialchars($fetch_profile['username'] ?? 'Guest'); ?></p>
            <a href="user_profile_update.php" class="btn">Update your Profile</a>
            <a href="logout.php" class="delete-btn">Logout</a>
            <div class="flex-btn">
                <a href="login.php" class="option-btn">Login</a>
                <a href="register.php" class="option-btn">Register</a>
            </div>
        </div>
    </div>
</header>
