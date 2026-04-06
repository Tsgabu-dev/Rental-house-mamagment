<?php
include 'includes/header.php'; 
include 'includes/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user = $_POST['username'];
    $_SESSION['user'] = $user;
    header('Location: index.php');
}
?>
<h2>Login</h2>
<form method="post">
    Username: <input type="text" name="username" required><br><br>
    <button type="submit">Login</button>
</form>
<?php include 'includes/footer.php'; ?>