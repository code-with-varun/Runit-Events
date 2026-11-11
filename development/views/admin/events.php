<?php
$pageTitle = 'Manage Events | Runit Events Hub Admin';
$activeAdminTab = 'events';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="fw-bold text-dark mb-0">Event Catalog Management</h3>
            <span class="text-secondary small">Create, edit, and configure capacity and certificate eligibility</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary btn-sm px-3">
                &larr; Admin Console
            </a>
            <a href="<?= app_url('admin/events/create') ?>" class="btn btn-bms-red btn-sm px-3">
                <i class="bi bi-plus-lg me-1"></i> New Event
            </a>
        </div>
    </div>

    <?php if (isset($_GET['saved'])): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Event successfully saved and updated.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="light-card p-0 overflow-hidden shadow-sm">
        <?php if (empty($events)): ?>
            <div class="p-5 text-center text-muted">
                <i class="bi bi-calendar-x fs-1 text-secondary mb-3 d-block"></i>
                <h4 class="fw-bold text-dark">No Events Created Yet</h4>
                <p class="small text-secondary mb-4">The event catalog is empty. Start by creating your first webinar or workshop.</p>
                <a href="<?= app_url('admin/events/create') ?>" class="btn btn-bms-red px-4">
                    <i class="bi bi-plus-circle me-1"></i> Create First Event
                </a>
            </div>
        <?php else: ?>
        <table class="table light-table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date & Time</th>
                    <th>Capacity / Booked</th>
                    <th>Status</th>
                    <th>Certificate</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $evt): ?>
                    <tr>
                        <td class="font-monospace text-danger small fw-bold"><?= htmlspecialchars($evt['event_code']) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= htmlspecialchars(!empty($evt['card_image']) ? $evt['card_image'] : ($evt['banner_image'] ?: 'assets/banners/hero_ai.jpg')) ?>" style="width: 42px; height: 42px; object-fit: cover; border-radius: 6px; border: 1px solid #eef2f6;" alt="Thumbnail">
                                <div>
                                    <strong class="text-dark d-block"><?= htmlspecialchars($evt['title']) ?></strong>
                                    <?php if ($evt['is_featured']): ?>
                                        <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">FEATURED</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($evt['category_name']) ?></span></td>
                        <td class="small text-secondary"><?= date('M j, Y', strtotime($evt['event_date'])) ?> at <?= date('H:i', strtotime($evt['start_time'])) ?></td>
                        <td>
                            <span class="small text-dark fw-bold"><?= $evt['booked_count'] ?> / <?= $evt['capacity'] ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $evt['status'] === 'published' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= strtoupper($evt['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?= $evt['is_certificate_eligible'] ? '<span class="text-warning fw-bold"><i class="bi bi-award-fill"></i> Yes</span>' : '<span class="text-secondary">No</span>' ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= app_url('admin/event-form/' . $evt['id']) ?>" class="btn btn-outline-dark btn-sm px-2 py-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <a href="<?= app_url('event/' . $evt['id']) ?>" class="btn btn-outline-primary btn-sm px-2 py-1" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
