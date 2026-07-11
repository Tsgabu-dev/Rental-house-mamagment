<?php
include 'includes/header.php';
include 'includes/db.php';

$search = trim($_GET['search'] ?? '');
$houses = [];

$sql = 'SELECT id, title, location, price, bedrooms, bathrooms, status, image_url FROM houses WHERE status != ?';
$params = ['Rented'];
$types = 's';

if ($search !== '') {
    $sql .= ' AND (title LIKE ? OR location LIKE ? OR description LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

$sql .= ' ORDER BY created_at DESC';
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $houses[] = $row;
    }
    $stmt->close();
}
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-column flex-md-row gap-3">
        <div>
            <h1 class="h3 mb-1">Available Houses</h1>
            <p class="text-muted mb-0">Find a rental that fits your needs.</p>
        </div>
        <form method="get" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search by title or location" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <?php if (empty($houses)): ?>
        <div class="alert alert-info">No matching houses were found.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($houses as $house): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($house['image_url'] ?? 'https://via.placeholder.com/600x400?text=House+Image', ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars($house['title'], ENT_QUOTES, 'UTF-8') ?>" style="height: 220px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($house['title']) ?></h5>
                            <p class="text-muted mb-2"><?= htmlspecialchars($house['location']) ?></p>
                            <p class="fw-bold text-primary mb-3">$<?= number_format((float)$house['price'], 2) ?></p>
                            <p class="small text-muted mb-3">
                                <?= (int)$house['bedrooms'] ?> bed • <?= (int)$house['bathrooms'] ?> bath
                            </p>
                            <a href="house-details.php?id=<?= (int)$house['id'] ?>" class="btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>