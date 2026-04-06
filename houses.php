<?php 
include 'includes/header.php'; 
include 'includes/db.php';

// Example data for testing
$houses = [
    ['id'=>1,'title'=>'Cozy Apartment','price'=>500,'location'=>'Addis Ababa'],
    ['id'=>2,'title'=>'Family House','price'=>1000,'location'=>'Bole']
];
?>

<h2>Available Houses</h2>
<ul>
<?php foreach($houses as $house): ?>
    <li>
        <a href="house-details.php?id=<?php echo $house['id']; ?>">
            <?php echo $house['title']; ?>
        </a> - $<?php echo $house['price']; ?> - <?php echo $house['location']; ?>
    </li>
<?php endforeach; ?>
</ul>

<?php include 'includes/footer.php'; ?>