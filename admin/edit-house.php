<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: manage-houses.php');
    exit;
}

$stmt = $conn->prepare('SELECT * FROM houses WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$house = $result->fetch_assoc();
$stmt->close();

if (!$house) {
    header('Location: manage-houses.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $bedrooms = trim($_POST['bedrooms'] ?? '1');
    $bathrooms = trim($_POST['bathrooms'] ?? '1');
    $status = trim($_POST['status'] ?? 'Available');
    $description = trim($_POST['description'] ?? '');
    $imageUrl = $house['image_url'] ?? null;

    if ($title === '' || $location === '' || $price === '') {
        $error = 'Title, location, and price are required.';
    } else {
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $fileName = basename($_FILES['image_file']['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($fileExt, $allowed, true)) {
                $error = 'Only JPG, JPEG, PNG, and WEBP files are allowed.';
            } else {
                $targetDir = __DIR__ . '/../uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $newFileName = 'house_' . $id . '_' . time() . '.' . $fileExt;
                $targetPath = $targetDir . $newFileName;
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath)) {
                    $imageUrl = 'uploads/' . $newFileName;
                } else {
                    $error = 'Unable to upload the image.';
                }
            }
        }

        if ($error === '') {
            $stmt = $conn->prepare('UPDATE houses SET title = ?, location = ?, price = ?, bedrooms = ?, bathrooms = ?, status = ?, image_url = ?, description = ? WHERE id = ?');
            $stmt->bind_param('ssddisssi', $title, $location, $price, $bedrooms, $bathrooms, $status, $imageUrl, $description, $id);
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: manage-houses.php');
                exit;
            }
            $error = 'Unable to update house. Please try again.';
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
                    <h1 class="h4 mb-4">Edit House</h1>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="<?= root('admin/edit-house.php') ?>?id=<?= $id ?>" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title'] ?? $house['title']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" required value="<?= htmlspecialchars($_POST['location'] ?? $house['location']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price</label>
                                <input type="number" name="price" class="form-control" min="0" step="0.01" required value="<?= htmlspecialchars($_POST['price'] ?? $house['price']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bedrooms</label>
                                <input type="number" name="bedrooms" class="form-control" min="0" value="<?= htmlspecialchars($_POST['bedrooms'] ?? $house['bedrooms']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bathrooms</label>
                                <input type="number" name="bathrooms" class="form-control" min="0" value="<?= htmlspecialchars($_POST['bathrooms'] ?? $house['bathrooms']) ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Available" <?= (($_POST['status'] ?? $house['status']) === 'Available') ? 'selected' : '' ?>>Available</option>
                                    <option value="Unavailable" <?= (($_POST['status'] ?? $house['status']) === 'Unavailable') ? 'selected' : '' ?>>Unavailable</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Upload New Image</label>
                                <input type="file" name="image_file" class="form-control" accept="image/*">
                                <?php if (!empty($house['image_url'])): ?>
                                    <div class="form-text">Current image: <?= htmlspecialchars($house['image_url']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($_POST['description'] ?? $house['description']) ?></textarea>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="<?= root('admin/manage-houses.php') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
