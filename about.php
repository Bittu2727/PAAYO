<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">  
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="about">
   <div class="row">
      <div class="box">
         <img src="images/why.png" alt="">
         <h3>Why choose us?</h3>
         <p>We’re committed to making your home sparkle — effortlessly and affordably. 
            Our professional home cleaning services are designed to give you peace of mind and more time for the things that matter most. 
            Whether it’s refreshing your carpets, deep-cleaning sofas, or restoring curtains to their original beauty, 
            we’ve got your back (and your floors, windows, and furniture too)!</p>
         <a href="contact.php" class="btn">Contact us</a>
      </div>

      <div class="box">
         <img src="images/provide.jpg" alt="">
         <h3>What we provide?</h3>
         <p>At PAAYO!, we offer a wide range of professional home cleaning services designed to make your life easier and your space fresher. From deep home cleaning and bathroom sanitization to expert carpet, curtain, and sofa cleaning, our trained team ensures spotless results every time. We use eco-friendly, safe products that are gentle on your home and tough on dirt. Whether you need a one-time refresh or regular upkeep, our flexible scheduling and reliable service have you covered — all delivered by verified, courteous professionals you can trust.</p>
         <a href="shop.php" class="btn">Our services</a>
      </div>
   </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
