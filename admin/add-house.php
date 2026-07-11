<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$error = '';
$success = '';
$categories = [];

$result = $conn->query('SELECT id, name FROM categories ORDER BY name');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $bedrooms = trim($_POST['bedrooms'] ?? '1');
    $bathrooms = trim($_POST['bathrooms'] ?? '1');
    $status = trim($_POST['status'] ?? 'Available');
    $description = trim($_POST['description'] ?? '');
    $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $imageUrl = trim($_POST['image_url'] ?? '');
    $ownerId = currentUserId();

    if ($title === '' || $location === '' || $price === '') {
        $error = 'Title, location, and price are required.';
    } else {
        $stmt = $conn->prepare('INSERT INTO houses (owner_id, category_id, title, description, location, price, bedrooms, bathrooms, status, image_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->bind_param('iisssddiss', $ownerId, $categoryId, $title, $description, $location, $price, $bedrooms, $bathrooms, $status, $imageUrl);
        if ($stmt->execute()) {
            $stmt->close();
            $success = 'House listing added successfully.';
        } else {
            $error = 'Unable to save house. Please try again.';
            $stmt->close();
        }
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h4 mb-4">Add New House</h1>
                    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
                    <form method="post" action="<?= root('admin/add-house.php') ?>" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" required value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price</label>
                                <input type="number" name="price" class="form-control" min="0" step="0.01" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bedrooms</label>
                                <input type="number" name="bedrooms" class="form-control" min="0" value="<?= htmlspecialchars($_POST['bedrooms'] ?? '1') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bathrooms</label>
                                <input type="number" name="bathrooms" class="form-control" min="0" value="<?= htmlspecialchars($_POST['bathrooms'] ?? '1') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Select category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= (int)$category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Available" <?= (($_POST['status'] ?? 'Available') === 'Available') ? 'selected' : '' ?>>Available</option>
                                    <option value="Unavailable" <?= (($_POST['status'] ?? '') === 'Unavailable') ? 'selected' : '' ?>>Unavailable</option>
                                    <option value="Rented" <?= (($_POST['status'] ?? '') === 'Rented') ? 'selected' : '' ?>>Rented</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Image URL</label>
                                <input type="url" name="image_url" class="form-control" value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>" placeholder="https://example.com/image.jpg">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="<?= root('admin/manage-houses.php') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Add House</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
