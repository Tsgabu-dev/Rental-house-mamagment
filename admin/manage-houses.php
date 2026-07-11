<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare('DELETE FROM houses WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage-houses.php');
    exit;
}

$houses = [];
$userId = currentUserId();
$query = 'SELECT * FROM houses WHERE owner_id = ? OR ? = 1 ORDER BY created_at DESC';
$stmt = $conn->prepare($query);
$adminFlag = isAdmin() ? 1 : 0;
$stmt->bind_param('ii', $userId, $adminFlag);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $houses[] = $row;
}
$stmt->close();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-column flex-md-row gap-3">
        <div>
            <h1 class="h3 mb-1">Manage Houses</h1>
            <p class="text-muted mb-0">Add, edit, or remove property listings.</p>
        </div>
        <a href="<?= root('admin/add-house.php') ?>" class="btn btn-success">Add New House</a>
    </div>
    <?php if (count($houses) === 0): ?>
        <div class="alert alert-info">No house listings exist yet. Add one to get started.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($houses as $house): ?>
                        <tr>
                            <td><?= (int)$house['id'] ?></td>
                            <td><?= htmlspecialchars($house['title']) ?></td>
                            <td><?= htmlspecialchars($house['location']) ?></td>
                            <td>$<?= number_format((float)$house['price'], 2) ?></td>
                            <td><?= htmlspecialchars($house['status']) ?></td>
                            <td>
                                <a href="<?= root('admin/edit-house.php') ?>?id=<?= (int)$house['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="manage-houses.php?action=delete&id=<?= (int)$house['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
