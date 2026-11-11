<?php
$pageTitle = 'Seat Reservation Checkout | BookMyShow Style';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <!-- 10-Minute Hold Reservation Alert (BookMyShow Style Timeout) -->
            <div class="alert alert-warning border-warning bg-warning bg-opacity-10 p-3 rounded-4 mb-4 d-flex align-items-center justify-content-between shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 bg-warning rounded-circle text-dark fs-3">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                    <div>
                        <strong class="text-dark d-block">Seat Reserved For You</strong>
                        <span class="small text-secondary">Your capacity slot is temporarily locked for 10 minutes.</span>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-warning text-dark fs-5 fw-bold font-monospace px-3 py-2 shadow-sm" id="seat-countdown-timer">
                        10:00
                    </span>
                </div>
            </div>

            <!-- Booking Card (Clean White Surface) -->
            <div class="light-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Confirm Webinar Booking</h4>
                        <span class="text-secondary small">Review event details and attendee confirmation</span>
                    </div>
                    <span class="bms-tag-free px-3 py-2">
                        100% FREE PASS
                    </span>
                </div>

                <!-- Event Snapshot -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <span class="small text-danger fw-bold text-uppercase"><?= htmlspecialchars($event['category_name']) ?></span>
                    <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($event['title']) ?></h5>
                    <div class="d-flex flex-wrap gap-4 text-secondary small">
                        <span><i class="bi bi-calendar3 text-danger me-1"></i> <?= date('D, M j, Y', strtotime($event['event_date'])) ?></span>
                        <span><i class="bi bi-clock text-warning me-1"></i> <?= date('h:i A', strtotime($event['start_time'])) ?> IST</span>
                        <span><i class="bi bi-camera-video text-primary me-1"></i> <?= htmlspecialchars($event['meeting_platform']) ?></span>
                    </div>
                </div>

                <!-- Attendee Confirmation Form -->
                <form action="<?= app_url('book-process') ?>" method="POST">
                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

                    <h6 class="fw-bold text-dark mb-3">Attendee Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Full Name</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['name']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Email Address</label>
                            <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Company / Organization</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['company'] ?? 'Independent Attendee') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Designation</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['designation'] ?? 'Software Professional') ?>" readonly>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-4 small text-secondary">
                        <i class="bi bi-info-circle-fill text-danger me-1"></i>
                        Upon clicking confirm, your <strong>digital Executive Attendee Pass</strong> with verification QR code and calendar sync invite will be generated instantly.
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?= app_url('event/' . $event['id']) ?>" class="btn btn-outline-secondary w-50 py-2">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-bms-red w-50 py-2 fs-6">
                            <i class="bi bi-check2-circle me-1"></i> Confirm &amp; Reserve Seat (Free)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
