<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$result = $conn->query('SELECT COUNT(*) AS total, SUM(status = "Available") AS available, SUM(status != "Available") AS unavailable FROM houses');
$stats = ['houses' => 0, 'available' => 0, 'unavailable' => 0];
if ($result) {
    $row = $result->fetch_assoc();
    $stats['houses'] = (int)$row['total'];
    $stats['available'] = (int)$row['available'];
    $stats['unavailable'] = (int)$row['unavailable'];
}

$inquiries = [];
$inqResult = $conn->query('SELECT i.id, i.name, i.email, i.status, h.title FROM inquiries i JOIN houses h ON h.id = i.house_id ORDER BY i.created_at DESC LIMIT 10');
if ($inqResult) {
    while ($row = $inqResult->fetch_assoc()) {
        $inquiries[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-column flex-md-row gap-3">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Manage rental listings and recent inquiries.</p>
        </div>
        <a href="<?= root('admin/manage-houses.php') ?>" class="btn btn-primary">Manage Houses</a>
    </div>
    <div class="row g-4">
        <div class="col-md-4"><div class="card shadow-sm h-100"><div class="card-body"><h5 class="card-title">Total Houses</h5><p class="display-6 fw-bold"><?= $stats['houses'] ?></p></div></div></div>
        <div class="col-md-4"><div class="card shadow-sm h-100"><div class="card-body"><h5 class="card-title">Available</h5><p class="display-6 fw-bold text-success"><?= $stats['available'] ?></p></div></div></div>
        <div class="col-md-4"><div class="card shadow-sm h-100"><div class="card-body"><h5 class="card-title">Unavailable</h5><p class="display-6 fw-bold text-secondary"><?= $stats['unavailable'] ?></p></div></div></div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Recent Inquiries</h2>
            <?php if (empty($inquiries)): ?>
                <div class="alert alert-info mb-0">No inquiries yet.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Name</th><th>Email</th><th>House</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($inquiries as $inquiry): ?>
                                <tr><td><?= htmlspecialchars($inquiry['name']) ?></td><td><?= htmlspecialchars($inquiry['email']) ?></td><td><?= htmlspecialchars($inquiry['title']) ?></td><td><?= htmlspecialchars($inquiry['status']) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
