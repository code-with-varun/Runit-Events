<?php
$pageTitle = 'Platform Analytics & Reports | Runit Events Hub Admin';
$activeAdminTab = 'reports';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="fw-bold text-dark mb-0">Platform Performance & Analytics Reports</h3>
            <span class="text-secondary small">Comprehensive capacity utilization, attendance conversions, and certificate metrics</span>
        </div>
        <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary btn-sm px-3">&larr; Admin Console</a>
    </div>

    <div class="light-card p-0 overflow-hidden shadow-sm">
        <table class="table light-table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Webinar / Event Title</th>
                    <th>Date</th>
                    <th>Capacity</th>
                    <th>Bookings</th>
                    <th>Available Slots</th>
                    <th>Actual Attendees</th>
                    <th>Certificates Issued</th>
                    <th>Average Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventMetrics as $m): ?>
                    <tr>
                        <td><strong class="text-dark"><?= htmlspecialchars($m['title']) ?></strong></td>
                        <td class="small text-secondary"><?= date('M j, Y', strtotime($m['event_date'])) ?></td>
                        <td><?= $m['capacity'] ?></td>
                        <td><span class="badge bg-danger"><?= $m['booked_count'] ?></span></td>
                        <td>
                            <span class="<?= $m['seats_left'] < 20 ? 'text-danger fw-bold' : 'text-success fw-bold' ?>">
                                <?= $m['seats_left'] ?> seats
                            </span>
                        </td>
                        <td><span class="text-dark fw-bold"><?= $m['actual_attended'] ?></span></td>
                        <td><span class="badge bg-warning text-dark font-monospace"><?= $m['certs_issued'] ?> Issued</span></td>
                        <td>
                            <span class="text-warning small"><i class="bi bi-star-fill"></i> <?= $m['avg_rating'] ?: '5.0' ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
