<?php
@include 'config.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;


if (isset($_POST['add_to_wishlist'])) {
    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_STRING);
    $p_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $p_price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $p_image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);

    $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
    $check_wishlist_numbers->execute([$p_name, $user_id]);

    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->execute([$p_name, $user_id]);

    if ($check_wishlist_numbers->rowCount() > 0) {
        $message[] = 'Already added to wishlist!';
    } elseif ($check_cart_numbers->rowCount() > 0) {
        $message[] = 'Already added to cart!';
    } else {
        // Corrected the number of arguments in the execute function
        $insert_wishlist = $conn->prepare("INSERT INTO `wishlist` (user_id, product_id, name, price, image) VALUES (?, ?, ?, ?, ?)");
        $insert_wishlist->execute([$user_id, $product_id, $p_name, $p_price, $p_image]);
        $message[] = 'Added to wishlist!';
    }
}
if(isset($_POST['add_to_cart'])){

    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_STRING);
    $p_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $p_price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $p_qty = filter_var($_POST['p_qty'], FILTER_SANITIZE_STRING);
    $p_image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);
    
    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->execute([$p_name, $user_id]);
 
    if($check_cart_numbers->rowCount() > 0){
       $message[] = 'Already added to cart!';
    }else{
 
       $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
       $check_wishlist_numbers->execute([$p_name, $user_id]);
 
       if($check_wishlist_numbers->rowCount() > 0){
          $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
          $delete_wishlist->execute([$p_name, $user_id]);
       }
 
       $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, product_id, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
       $insert_cart->execute([$user_id, $product_id, $p_name, $p_price, $p_qty, $p_image]);
       $message[] = 'Added to cart!';
    }
 
 } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>

    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'header.php'; ?>

<div class="home-bg">
    <section class="home">
        <div class="content">
            <span>PAAYO!</span>
            <h3>चिटिक्क घर, सजिलो सेवा, मात्र एक क्लिकमै!</h3>
            <p>Where you get the best</p>
            <a href="about.php" class="btn">About Us</a>
        </div>
    </section>
</div>

<section class="home-category">
    <h1 class="title">Browse by Category</h1>
    <div class="box-container">
        <div class="box">
            <img src="images/4.png" alt="Sofa">
            <h3>Sofa Cleaning</h3>
            <p>Deep clean to freshen and restore your sofa.</p>
            <a href="category.php?category=Sofa Clean" class="btn">Sofa Cleaning</a>
        </div>

        <div class="box">
            <img src="images/1.png" alt="Carpet">
            <h3>Carpet Cleaning</h3>
            <p>Deep clean for soft, fresh carpets.</p>
            <a href="category.php?category=Carpet Clean" class="btn">Carpet Cleaning</a>
        </div>

        <div class="box">
            <img src="images/2.png" alt="Curtain">
            <h3>Curtain Cleaning</h3>
            <p>Clean, fresh, and dust-free curtains.</p>
            <a href="category.php?category=Curtain Clean" class="btn">Curtain Cleaning</a>
        </div>

        <div class="box">
            <img src="images/3.png" alt="Home">
            <h3>House Cleaning</h3>
            <p>Spotless and hygienic home care.</p>
            <a href="category.php?category=Home Clean" class="btn">House Cleaning</a>
        </div>
    </div>
</section>

<section class="products">
    <h1 class="title">Latest Services</h1>
    <div class="box-container">
    <?php
        $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 3");
        $select_products->execute();
        if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <form action="" method="POST" class="box">
        <div class="price">Rs.<span><?= htmlspecialchars($fetch_products['price']); ?></span>/-</div>
        <a href="view_page.php?product_id=<?= htmlspecialchars($fetch_products['product_id']); ?>" class="fas fa-eye"></a>
        <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="<?= htmlspecialchars($fetch_products['name']); ?>">
        <div class="product-details" style="padding: 10px;">
            <div class="name" style="font-size: 2.1em; margin-bottom: 10px;">
                <?= htmlspecialchars($fetch_products['name']); ?>
            </div>
            <div class="details" style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 1.7em; color: #666; margin-right: 20px;">
                    Service Area: <?= htmlspecialchars($fetch_products['stock']); ?>
                </span>
                <span style="font-size: 1.7em; color: #006400;">
                    Provider: <?= htmlspecialchars($fetch_products['vendor_name']); ?>
                </span>
            </div>
        </div>        
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($fetch_products['product_id']); ?>">
        <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
        <input type="hidden" name="price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
        <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
        <input type="submit" value="Book Now" class="btn" name="add_to_cart">
    </form>
    <?php
            }
        } else {
            echo '<p class="empty">No products added yet!</p>';
        }
    ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
