<?php
include 'includes/header.php';
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $houseId = (int)($_POST['house_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($houseId > 0 && $name !== '' && $email !== '' && $message !== '') {
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $stmt = $conn->prepare('INSERT INTO inquiries (house_id, user_id, name, email, message, status, created_at) VALUES (?, ?, ?, ?, ?, "Pending", NOW())');
        $stmt->bind_param('iisss', $houseId, $userId, $name, $email, $message);
        $stmt->execute();
        $stmt->close();
        echo '<div class="container py-5"><div class="alert alert-success">Your inquiry has been submitted.</div></div>';
        include 'includes/footer.php';
        exit;
    }
}

header('Location: houses.php');
exit;
