<?php

@include 'config.php';

session_start();

$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

if (!$admin_id) {
   header('location:login.php');
   exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="dashboard">

   <h1 class="title">Dashboard</h1>

   <div class="box-container">

      <div class="box">
      <?php
         $total_pendings = 0;
         $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE order_status = ?");
         $select_pendings->execute(['Pending']);
         while ($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)) {
            $total_pendings += $fetch_pendings['total_amount'];
         }
      ?>
      <h3>Rs. <?= htmlspecialchars($total_pendings); ?>/-</h3>
      <p>Total Pendings</p>
      <a href="admin_orders.php" class="btn">See Bookings</a>
      </div>

      <div class="box">
      <?php
         $total_completed = 0;
         $select_completed = $conn->prepare("SELECT * FROM `orders` WHERE order_status = ?");
         $select_completed->execute(['Completed']);
         while ($fetch_completed = $select_completed->fetch(PDO::FETCH_ASSOC)) {
            $total_completed += $fetch_completed['total_amount'];
         }
      ?>
      <h3>Rs.<?= htmlspecialchars($total_completed); ?>/-</h3>
      <p>Completed Services</p>
      <a href="admin_orders.php" class="btn">See Orders</a>
      </div>

      <div class="box">
      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders`");
         $select_orders->execute();
         $number_of_orders = $select_orders->rowCount();
      ?>
      <h3><?= htmlspecialchars($number_of_orders); ?></h3>
      <p>Orders Placed</p>
      <a href="admin_orders.php" class="btn">See Bookings</a>
      </div>

      <div class="box">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products`");
         $select_products->execute();
         $number_of_products = $select_products->rowCount();
      ?>
      <h3><?= htmlspecialchars($number_of_products); ?></h3>
      <p>Products Added</p>
      <a href="admin_products.php" class="btn">See Services</a>
      </div>

      <div class="box">
      <?php
         $select_users = $conn->prepare("SELECT * FROM `users` WHERE user_type = ?");
         $select_users->execute(['User']);
         $number_of_users = $select_users->rowCount();
      ?>
      <h3><?= htmlspecialchars($number_of_users); ?></h3>
      <p>Total Users</p>
      <a href="admin_users.php" class="btn">See Accounts</a>
      </div>

      <div class="box">
      <?php
         $select_admins = $conn->prepare("SELECT * FROM `users` WHERE user_type = ?");
         $select_admins->execute(['Admin']);
         $number_of_admins = $select_admins->rowCount();
      ?>
      <h3><?= htmlspecialchars($number_of_admins); ?></h3>
      <p>Total Admins</p>
      <a href="admin_users.php" class="btn">See Accounts</a>
      </div>

      <div class="box">
      <?php
         $select_accounts = $conn->prepare("SELECT * FROM `users`");
         $select_accounts->execute();
         $number_of_accounts = $select_accounts->rowCount();
      ?>
      <h3><?= htmlspecialchars($number_of_accounts); ?></h3>
      <p>Total Accounts</p>
      <a href="admin_users.php" class="btn">See Accounts</a>
      </div>

      <div class="box">
      <?php
         $select_messages = $conn->prepare("SELECT * FROM `messages`");
         $select_messages->execute();
         $number_of_messages = $select_messages->rowCount();
      ?>
      <h3><?= htmlspecialchars($number_of_messages); ?></h3>
      <p>Total Messages</p>
      <a href="admin_contacts.php" class="btn">See Messages</a>
      </div>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
