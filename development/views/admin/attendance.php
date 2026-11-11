<?php
$pageTitle = 'Attendance & Check-in | Runit Events Hub Admin';
$activeAdminTab = 'attendance';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="fw-bold text-dark mb-0">Attendance Tracker & QR Check-In</h3>
            <span class="text-secondary small">Record attendance duration; automated certificates trigger at &ge; 75%</span>
        </div>
        <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary btn-sm px-3">&larr; Admin Console</a>
    </div>

    <!-- Event Selector -->
    <div class="light-card shadow-sm mb-4">
        <form action="index.php" method="GET" class="row g-3 align-items-center">
            <input type="hidden" name="page" value="admin-attendance">
            <div class="col-md-8">
                <label class="form-label text-secondary small fw-bold mb-1">SELECT EVENT / WEBINAR</label>
                <select name="event_id" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($events as $ev): ?>
                        <option value="<?= $ev['id'] ?>" <?= $eventId == $ev['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ev['title']) ?> (<?= date('M j, Y', strtotime($ev['event_date'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 text-md-end pt-md-3">
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <?= count($attendees) ?> Total Registrations
                </span>
            </div>
        </form>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Attendance successfully recorded. Certificate auto-issued if criteria fulfilled.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Attendees Table -->
    <div class="light-card p-0 overflow-hidden shadow-sm">
        <table class="table light-table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Pass Code</th>
                    <th>Attendee</th>
                    <th>Company / Org</th>
                    <th>Recorded Attendance %</th>
                    <th>Certificate Status</th>
                    <th class="text-end">Mark Attendance</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attendees)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-secondary">
                            No registered attendees for this session yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($attendees as $att): ?>
                        <tr>
                            <td class="font-monospace text-danger small fw-bold"><?= htmlspecialchars($att['booking_code']) ?></td>
                            <td>
                                <strong class="text-dark"><?= htmlspecialchars($att['user_name']) ?></strong>
                                <div class="text-secondary small"><?= htmlspecialchars($att['user_email']) ?></div>
                            </td>
                            <td class="small text-secondary"><?= htmlspecialchars($att['user_company'] ?? 'Independent') ?></td>
                            <td>
                                <?php $pct = (float)($att['percentage_attended'] ?? 0); ?>
                                <span class="badge <?= $pct >= 75 ? 'bg-success' : ($pct > 0 ? 'bg-warning text-dark' : 'bg-secondary') ?> fs-6">
                                    <?= $pct ?>%
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($att['cert_number'])): ?>
                                    <span class="badge bg-warning text-dark font-monospace">
                                        <i class="bi bi-award-fill"></i> <?= htmlspecialchars($att['cert_number']) ?>
                                    </span>
                                <?php elseif ($pct >= 75): ?>
                                    <span class="badge bg-success">Eligible</span>
                                <?php else: ?>
                                    <span class="text-secondary small">&lt; 75% (Ineligible)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <!-- Fast Attendance Form -->
                                <form action="index.php?page=admin-attendance&event_id=<?= $eventId ?>" method="POST" class="d-inline-flex gap-2 justify-content-end">
                                    <input type="hidden" name="action" value="mark">
                                    <input type="hidden" name="user_id" value="<?= $att['user_id'] ?>">
                                    <select name="percentage_attended" class="form-select form-select-sm" style="width: 110px;">
                                        <option value="100" <?= $pct == 100 ? 'selected' : '' ?>>100% Full</option>
                                        <option value="85" <?= $pct == 85 ? 'selected' : '' ?>>85%</option>
                                        <option value="75" <?= $pct == 75 ? 'selected' : '' ?>>75% Min</option>
                                        <option value="50" <?= $pct == 50 ? 'selected' : '' ?>>50% (No Cert)</option>
                                        <option value="0" <?= $pct == 0 ? 'selected' : '' ?>>No Show</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-check-lg"></i> Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
