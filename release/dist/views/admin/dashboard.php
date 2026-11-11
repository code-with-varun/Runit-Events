<?php
$pageTitle = 'Admin Management Console | Runit Events Hub';
$activeAdminTab = 'dashboard';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-4">
    <!-- Clean Executive Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger text-uppercase px-2 py-1"><i class="bi bi-speedometer2 me-1"></i> Operations Overview</span>
                <span class="text-secondary small">Real-time attendee activity, catalog metrics, and capacity tracker</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Platform Administration Dashboard</h3>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <a href="<?= app_url('admin/events/create') ?>" class="btn btn-bms-red btn-sm px-3 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> + Create Event
            </a>
            <a href="<?= app_url('admin/settings') ?>" class="btn btn-outline-secondary btn-sm px-3">
                <i class="bi bi-gear-fill me-1"></i> Footer &amp; Settings
            </a>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show py-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <!-- Modern KPI Metric Cards (Refined, no harsh wireframe borders) -->
    <div class="row g-3 g-lg-4 mb-4">
        <!-- 1. Registered Learners -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white" style="border: 1px solid #eef2f6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Registered Learners</span>
                    <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark mb-1"><?= number_format($stats['total_users']) ?></div>
                <a href="<?= app_url('admin/users') ?>" class="small text-primary text-decoration-none fw-semibold d-inline-flex align-items-center gap-1">
                    View Entire Users &rarr;
                </a>
            </div>
        </div>

        <!-- 2. Speakers & Instructors -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white" style="border: 1px solid #eef2f6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Speakers &amp; Instructors</span>
                    <div class="p-2 rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-person-video3 fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-danger mb-1"><?= number_format($stats['total_speakers']) ?></div>
                <a href="<?= app_url('admin/speakers') ?>" class="small text-danger text-decoration-none fw-semibold d-inline-flex align-items-center gap-1">
                    Manage Speakers &rarr;
                </a>
            </div>
        </div>

        <!-- 3. Hosted Events -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white" style="border: 1px solid #eef2f6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Hosted Events</span>
                    <div class="p-2 rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-calendar2-check-fill fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark mb-1"><?= number_format($stats['total_events']) ?></div>
                <a href="<?= app_url('admin/events') ?>" class="small text-info text-decoration-none fw-semibold d-inline-flex align-items-center gap-1">
                    Manage Catalog &rarr;
                </a>
            </div>
        </div>

        <!-- 4. Certificates Issued -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white" style="border: 1px solid #eef2f6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Certificates Issued</span>
                    <div class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-patch-check-fill fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark mb-1"><?= number_format($stats['total_certs']) ?></div>
                <div class="small text-secondary d-flex align-items-center gap-1">
                    <i class="bi bi-qr-code"></i> <span>Verifiable Credentials</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Recent Bookings Table -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border: 1px solid #eef2f6 !important;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white rounded-top-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-ticket-perforated-fill text-danger me-2"></i> Recent Registrations
                    </h5>
                    <span class="badge bg-light text-secondary border">Live Stream</span>
                </div>

                <div class="table-responsive">
                    <table class="table light-table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Booking Code</th>
                                <th>Attendee</th>
                                <th>Event</th>
                                <th class="pe-3">Booked At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentBookings)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-secondary opacity-50 mb-2">
                                            <i class="bi bi-ticket-perforated" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">No Registrations Yet</h6>
                                        <p class="small text-muted mb-0">Attendee bookings and pass generation will appear here in real time.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentBookings as $b): ?>
                                    <tr>
                                        <td class="ps-3 font-monospace text-danger small fw-bold">
                                            <?= htmlspecialchars($b['booking_code']) ?>
                                        </td>
                                        <td>
                                            <div class="text-dark small fw-bold"><?= htmlspecialchars($b['user_name']) ?></div>
                                            <div class="text-secondary" style="font-size: 0.75rem;"><?= htmlspecialchars($b['user_email']) ?></div>
                                        </td>
                                        <td class="text-dark small"><?= htmlspecialchars($b['event_title']) ?></td>
                                        <td class="pe-3 text-secondary small"><?= date('M j, H:i', strtotime($b['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Events Capacity Overview -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border: 1px solid #eef2f6 !important;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white rounded-top-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-people-fill text-danger me-2"></i> Event Capacity Status
                    </h5>
                    <a href="<?= app_url('admin/events') ?>" class="small text-danger text-decoration-none fw-semibold">Manage Events &rarr;</a>
                </div>

                <div class="p-3 d-flex flex-column gap-3">
                    <?php if (empty($eventsOverview)): ?>
                        <div class="text-center py-5">
                            <div class="text-secondary opacity-50 mb-2">
                                <i class="bi bi-calendar-x" style="font-size: 2.5rem;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Active Events</h6>
                            <p class="small text-muted mb-3">Create your first webinar or corporate session to track seat occupancy.</p>
                            <a href="<?= app_url('admin/events/create') ?>" class="btn btn-sm btn-bms-red px-3">
                                <i class="bi bi-plus-circle me-1"></i> Create First Event
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($eventsOverview as $eo): ?>
                            <?php 
                                $seatStatus = format_seat_status($eo);
                                $pct = min(100, round(($eo['booked_count'] / max(1, $eo['capacity'])) * 100));
                            ?>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <a href="<?= app_url('event/' . $eo['id']) ?>" target="_blank" class="text-dark small fw-bold text-truncate text-decoration-none" style="max-width: 220px;" title="<?= htmlspecialchars($eo['title']) ?>">
                                        <?= htmlspecialchars($eo['title']) ?> <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.7rem;"></i>
                                    </a>
                                    <span class="badge <?= $seatStatus['badge_class'] ?> small">
                                        <i class="bi <?= $seatStatus['icon'] ?> me-1"></i> <?= $seatStatus['text'] ?>
                                    </span>
                                </div>
                                
                                <?php if (!$seatStatus['is_past']): ?>
                                    <!-- Progress bar for visual clarity -->
                                    <div class="progress mb-2 mt-2" style="height: 5px;">
                                        <div class="progress-bar <?= $pct >= 85 ? 'bg-danger' : ($pct >= 40 ? 'bg-warning' : 'bg-success') ?>" role="progressbar" style="width: <?= $pct ?>%;"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-secondary">
                                        <span><i class="bi bi-calendar-event me-1"></i> <?= date('M j, Y', strtotime($eo['event_date'])) ?></span>
                                        <span class="fw-semibold text-dark"><?= $pct ?>% Occupancy</span>
                                    </div>
                                <?php else: ?>
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-clock-history me-1"></i> Completed on <?= date('M j, Y', strtotime($eo['event_date'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
