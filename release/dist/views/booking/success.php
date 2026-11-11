<?php
$pageTitle = 'Booking Confirmed - Attendee Pass | Runit Events Hub';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-4">
        <span class="p-3 bg-success bg-opacity-10 rounded-circle text-success fs-1 d-inline-block shadow-sm mb-2">
            <i class="bi bi-check-circle-fill"></i>
        </span>
        <h3 class="fw-bold text-dark mb-1">Registration Confirmed!</h3>
        <p class="text-secondary small">Your digital Executive Attendee Pass is ready below. A copy has been saved to your dashboard.</p>
    </div>

    <!-- BookMyShow Signature Attendee Pass (Perforated Card) -->
    <div class="bms-ticket-container">
        <!-- Ticket Header (Dark Slate) -->
        <div class="bms-ticket-header">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-danger text-uppercase px-2 py-1 fw-bold">OFFICIAL ATTENDEE PASS</span>
                <span class="badge bg-success"><i class="bi bi-check-lg"></i> CONFIRMED</span>
            </div>
            <h4 class="fw-bold text-white mb-2"><?= htmlspecialchars($booking['event_title']) ?></h4>
            <div class="small text-light opacity-75">
                Booking ID: <strong class="font-monospace text-warning"><?= htmlspecialchars($booking['booking_code']) ?></strong>
            </div>
        </div>

        <!-- Perforated Notch Divider -->
        <div class="bms-ticket-divider"></div>

        <!-- Ticket Body (Clean White Surface) -->
        <div class="bms-ticket-body">
            <div class="row align-items-center g-4">
                <div class="col-md-7">
                    <div class="mb-3">
                        <span class="text-secondary small d-block">Attendee Name</span>
                        <strong class="text-dark fs-6"><?= htmlspecialchars($booking['user_name']) ?></strong>
                        <div class="text-secondary small"><?= htmlspecialchars($booking['user_email']) ?></div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <span class="text-secondary small d-block">Date</span>
                            <strong class="text-dark small"><i class="bi bi-calendar3 text-danger me-1"></i> <?= date('M j, Y', strtotime($booking['event_date'])) ?></strong>
                        </div>
                        <div class="col-6">
                            <span class="text-secondary small d-block">Time</span>
                            <strong class="text-dark small"><i class="bi bi-clock text-warning me-1"></i> <?= date('h:i A', strtotime($booking['start_time'])) ?> IST</strong>
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="text-secondary small d-block">Platform / Venue</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-camera-video me-1 text-primary"></i> <?= htmlspecialchars($booking['meeting_platform']) ?> Live Stream</span>
                    </div>
                </div>

                <!-- Right QR Code Section -->
                <div class="col-md-5 text-center border-start ps-md-4">
                    <img src="<?= $qrDataUri ?>" alt="Pass Verification QR Code" class="img-fluid p-2 bg-light rounded border shadow-sm mb-2" style="max-width: 130px;">
                    <div class="small text-secondary font-monospace" style="font-size: 0.75rem;">Scan for Session Check-in</div>
                </div>
            </div>

            <!-- Join Link Alert Box -->
            <?php if (!empty($booking['meeting_link'])): ?>
                <div class="mt-4 p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-secondary small d-block">Direct Meeting Join Link:</span>
                        <span class="small font-monospace text-primary text-truncate d-inline-block" style="max-width: 320px;"><?= htmlspecialchars($booking['meeting_link']) ?></span>
                    </div>
                    <a href="<?= htmlspecialchars($booking['meeting_link']) ?>" target="_blank" class="btn btn-sm btn-success">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Join
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Buttons Below Attendee Pass -->
    <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
        <a href="<?= app_url('download-pass/' . $booking['id']) ?>" class="btn btn-bms-red px-4 py-2">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i> Download Pass PDF
        </a>
        <a href="<?= app_url('download-ical/' . $booking['id']) ?>" class="btn btn-outline-dark px-4 py-2">
            <i class="bi bi-calendar-plus me-2 text-danger"></i> Add to Calendar (.ics)
        </a>
        <a href="<?= app_url('dashboard') ?>" class="btn btn-outline-secondary px-4 py-2">
            Go to My Dashboard &rarr;
        </a>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
