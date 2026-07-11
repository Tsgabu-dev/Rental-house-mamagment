<?php
include 'includes/header.php';
include 'includes/db.php';

$houses = [];
$result = $conn->query("SELECT id, title, location, price, bedrooms, bathrooms, image_url FROM houses ORDER BY id DESC LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $houses[] = $row;
    }
}
?>

<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold mb-3">Find the perfect place to call home</h1>
                <p class="lead text-muted mb-4">Explore modern homes, cozy apartments, and family-friendly spaces in the best neighborhoods.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="houses.php" class="btn btn-primary btn-lg">Browse Houses</a>
                    <a href="register.php" class="btn btn-outline-secondary btn-lg">Create Account</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-card p-4 rounded shadow-sm">
                    <h3 class="fw-bold mb-3">Why choose us?</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">✔ Verified listings with clear details</li>
                        <li class="mb-2">✔ Easy search by location and price</li>
                        <li class="mb-2">✔ Fast and secure account access</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 fw-bold mb-0">Featured Houses</h2>
            <a href="houses.php" class="text-decoration-none">View all</a>
        </div>

        <div class="row g-4">
            <?php if (!empty($houses)): ?>
                <?php foreach ($houses as $house): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?= htmlspecialchars($house['image_url'] ?? 'https://via.placeholder.com/600x400?text=House+Image', ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars($house['title'], ENT_QUOTES, 'UTF-8') ?>" style="height: 220px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($house['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                                <p class="text-muted mb-2"><?= htmlspecialchars($house['location'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="fw-bold text-primary mb-3">$<?= number_format((float)$house['price'], 2) ?></p>
                                <p class="small text-muted mb-3">
                                    <?= (int)$house['bedrooms'] ?> bed • <?= (int)$house['bathrooms'] ?> bath
                                </p>
                                <a href="house-details.php?id=<?= (int)$house['id'] ?>" class="btn btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info mb-0">No houses available right now. Please check back soon.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 rounded bg-white shadow-sm h-100">
                    <h4 class="fw-bold">Easy Booking</h4>
                    <p class="text-muted mb-0">Browse homes, compare options, and find your next rental without hassle.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded bg-white shadow-sm h-100">
                    <h4 class="fw-bold">Flexible Search</h4>
                    <p class="text-muted mb-0">Filter listings by price, bedrooms, bathrooms, and neighborhood.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded bg-white shadow-sm h-100">
                    <h4 class="fw-bold">Trusted Platform</h4>
                    <p class="text-muted mb-0">A simple and reliable experience for renters and property owners.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>