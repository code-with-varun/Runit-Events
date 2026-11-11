<?php
$pageTitle = 'My Dashboard & Attendee Passes | Runit Events Hub';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    <!-- User Profile Header Card (Light Theme) -->
    <div class="light-card mb-4 shadow-sm" style="border-left: 6px solid var(--bms-red);">
        <div class="row align-items-center g-3">
            <div class="col-md-8 d-flex align-items-center gap-3">
                <div class="p-3 bg-danger bg-opacity-10 rounded-circle text-danger fs-2">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($user['name']) ?></h3>
                    <div class="text-secondary small">
                        <?= htmlspecialchars($user['email']) ?> &bull; <?= htmlspecialchars($user['company'] ?? 'Tech Professional') ?> (<?= htmlspecialchars($user['designation'] ?? 'Attendee') ?>)
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold">
                    <i class="bi bi-award-fill me-1"></i> <?= count($certificates) ?> Earned Certificates
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs (BookMyShow Pill Style) -->
    <ul class="nav nav-pills mb-4 gap-2" id="dashboardTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active px-4 fw-semibold" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming" type="button">
                <i class="bi bi-ticket-perforated me-2"></i> Upcoming Passes (<?= count($upcomingBookings) ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-4 fw-semibold" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button">
                <i class="bi bi-clock-history me-2"></i> Attended History (<?= count($pastEvents) ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-4 fw-semibold" id="certs-tab" data-bs-toggle="pill" data-bs-target="#certs" type="button">
                <i class="bi bi-award-fill text-warning me-2"></i> Certificates (<?= count($certificates) ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-4 fw-semibold" id="materials-tab" data-bs-toggle="pill" data-bs-target="#materials" type="button">
                <i class="bi bi-folder2-open me-2"></i> Event Materials (<?= count($materials) ?>)
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="dashboardTabContent">
        <!-- Tab 1: Upcoming Bookings -->
        <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
            <?php if (empty($upcomingBookings)): ?>
                <div class="light-card text-center py-5">
                    <i class="bi bi-ticket-perforated fs-1 text-secondary mb-3 d-block"></i>
                    <h5 class="text-dark fw-bold">No active webinar bookings</h5>
                    <p class="text-secondary small mb-3">Explore our technical masterclasses and reserve a free capacity slot today.</p>
                    <a href="<?= app_url('explore') ?>" class="btn btn-bms-red btn-sm px-4">Browse Live Webinars</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($upcomingBookings as $b): ?>
                        <div class="col-lg-6">
                            <div class="light-card h-100 d-flex flex-column justify-content-between shadow-sm">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-danger text-uppercase"><?= htmlspecialchars($b['category_name']) ?></span>
                                        <span class="badge bg-light text-dark border font-monospace">
                                            ID: <?= htmlspecialchars($b['booking_code']) ?>
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($b['event_title']) ?></h5>
                                    <div class="small text-secondary mb-3">
                                        <i class="bi bi-calendar3 text-danger me-1"></i> <?= date('l, M j, Y', strtotime($b['event_date'])) ?> &bull; <?= date('h:i A', strtotime($b['start_time'])) ?> IST
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                                    <a href="<?= app_url('download-pass/' . $b['id']) ?>" class="btn btn-sm btn-bms-red px-3">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Download Pass
                                    </a>
                                    <a href="<?= app_url('download-ical/' . $b['id']) ?>" class="btn btn-sm btn-outline-dark px-3">
                                        <i class="bi bi-calendar-plus me-1 text-danger"></i> Add to Calendar
                                    </a>
                                    <?php if (!empty($b['meeting_link'])): ?>
                                        <a href="<?= htmlspecialchars($b['meeting_link']) ?>" target="_blank" class="btn btn-sm btn-outline-success px-3">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Join URL
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab 2: Attended History -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <?php if (empty($pastEvents)): ?>
                <div class="light-card text-center py-5">
                    <p class="text-secondary mb-0">No past attended webinars recorded yet.</p>
                </div>
            <?php else: ?>
                <div class="light-card p-0 overflow-hidden shadow-sm">
                    <table class="table light-table mb-0">
                        <thead>
                            <tr>
                                <th>Event Title</th>
                                <th>Date</th>
                                <th>Attendance %</th>
                                <th>Status</th>
                                <th class="text-end">Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pastEvents as $past): ?>
                                <tr>
                                    <td>
                                        <strong class="text-dark"><?= htmlspecialchars($past['event_title']) ?></strong>
                                    </td>
                                    <td class="small text-secondary"><?= date('M j, Y', strtotime($past['event_date'])) ?></td>
                                    <td>
                                        <span class="badge <?= ($past['percentage_attended'] ?? 0) >= 75 ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= $past['percentage_attended'] ?? 0 ?>% Attended
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">Completed</span>
                                    </td>
                                    <td class="text-end">
                                        <?php if (!empty($past['cert_number'])): ?>
                                            <a href="index.php?page=download-cert&cert=<?= urlencode($past['cert_number']) ?>" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                                            </a>
                                        <?php else: ?>
                                            <span class="text-secondary small">&lt; 75% Duration</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab 3: My Certificates -->
        <div class="tab-pane fade" id="certs" role="tabpanel">
            <?php if (empty($certificates)): ?>
                <div class="light-card text-center py-5">
                    <i class="bi bi-award fs-1 text-warning mb-3 d-block"></i>
                    <h5 class="text-dark fw-bold">No Certificates Issued Yet</h5>
                    <p class="text-secondary small mb-0">Complete any certificate-eligible webinar with 75%+ attendance to unlock your verified credential.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($certificates as $c): ?>
                        <div class="col-md-6">
                            <div class="light-card shadow-sm text-center" style="border: 2px solid #f59e0b;">
                                <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold">OFFICIAL CREDENTIAL</span>
                                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($c['event_title']) ?></h5>
                                <div class="font-monospace text-danger small mb-3">ID: <?= htmlspecialchars($c['cert_number']) ?></div>
                                <div class="small text-secondary mb-4">
                                    Issued on: <strong><?= date('F j, Y', strtotime($c['issue_date'])) ?></strong> &bull; Downloads: <?= $c['download_count'] ?>
                                </div>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= app_url('download-cert/' . $c['id']) ?>" class="btn btn-sm btn-bms-red px-4">
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF
                                    </a>
                                    <a href="<?= app_url('verify-cert', ['cert' => $c['cert_number']]) ?>" class="btn btn-sm btn-outline-dark px-3">
                                        <i class="bi bi-patch-check me-1"></i> Verify Credential
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab 4: Event Materials -->
        <div class="tab-pane fade" id="materials" role="tabpanel">
            <?php if (empty($materials)): ?>
                <div class="light-card text-center py-5">
                    <p class="text-secondary mb-0">No course materials available from your current registrations.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($materials as $m): ?>
                        <div class="col-md-6">
                            <div class="light-card d-flex justify-content-between align-items-center shadow-sm">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                                    <div>
                                        <strong class="text-dark small d-block"><?= htmlspecialchars($m['title']) ?></strong>
                                        <span class="text-secondary small"><?= htmlspecialchars($m['event_title']) ?></span>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-dark" onclick="alert('Downloading slide deck artifact...');">
                                    <i class="bi bi-download"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
