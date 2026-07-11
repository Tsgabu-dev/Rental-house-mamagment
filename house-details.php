<?php
include 'includes/header.php';
include 'includes/db.php';

$id = (int)($_GET['id'] ?? 0);
$house = null;
$message = '';

if ($id > 0) {
    $stmt = $conn->prepare('SELECT id, title, description, location, price, bedrooms, bathrooms, status, image_url FROM houses WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $house = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $messageText = trim($_POST['message'] ?? '');

    if ($id > 0 && $name !== '' && $email !== '' && $messageText !== '') {
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $stmt = $conn->prepare('INSERT INTO inquiries (house_id, user_id, name, email, phone, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, "Pending", NOW())');
        $stmt->bind_param('iissss', $id, $userId, $name, $email, $phone, $messageText);
        if ($stmt->execute()) {
            $message = 'Your inquiry has been sent successfully.';
        } else {
            $message = 'Unable to send your inquiry. Please try again.';
        }
        $stmt->close();
    } else {
        $message = 'Please fill in your name, email, and message.';
    }
}
?>

<div class="container py-5">
    <?php if (!$house): ?>
        <div class="alert alert-danger">House not found.</div>
        <a href="houses.php" class="btn btn-outline-secondary">Back to houses</a>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <img src="<?= htmlspecialchars($house['image_url'] ?? 'https://via.placeholder.com/600x400?text=House+Image', ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($house['title'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-lg-5">
                <h1 class="h3 fw-bold mb-3"><?= htmlspecialchars($house['title']) ?></h1>
                <p class="text-muted mb-3"><?= htmlspecialchars($house['location']) ?></p>
                <p class="display-6 text-primary mb-3">$<?= number_format((float)$house['price'], 2) ?></p>
                <p class="mb-3"><?= htmlspecialchars($house['description'] ?? 'No description available.') ?></p>
                <ul class="list-unstyled mb-4">
                    <li><strong>Status:</strong> <?= htmlspecialchars($house['status']) ?></li>
                    <li><strong>Bedrooms:</strong> <?= (int)$house['bedrooms'] ?></li>
                    <li><strong>Bathrooms:</strong> <?= (int)$house['bathrooms'] ?></li>
                </ul>
                <a href="houses.php" class="btn btn-outline-secondary">Back to houses</a>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Send an Inquiry</h2>
                        <?php if ($message !== ''): ?>
                            <div class="alert alert-info"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Your Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Inquiry</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>