<?php
include 'includes/header.php'; 
include 'includes/db.php';

$id = $_GET['id'] ?? 1;

// Example data
$house = ['id'=>$id,'title'=>'Cozy Apartment','price'=>500,'location'=>'Addis Ababa','description'=>'Nice apartment in city center'];
?>

<h2><?php echo $house['title']; ?></h2>
<p>Price: $<?php echo $house['price']; ?></p>
<p>Location: <?php echo $house['location']; ?></p>
<p>Description: <?php echo $house['description']; ?></p>

<a href="houses.php">Back to houses</a>

<?php include 'includes/footer.php'; ?>