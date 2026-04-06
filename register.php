<?php
include 'includes/header.php'; 
include 'includes/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user = $_POST['username'];
    // In real app, save user to DB
    $_SESSION['user'] = $user;
    header('Location: index.php');
}
?>
<h2>Register</h2>
<form method="post">
    Username: <input type="text" name="username" required><br><br>
    <button type="submit">Register</button>
</form>
<?php include 'includes/footer.php'; ?>