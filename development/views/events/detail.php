<?php
$pageTitle = htmlspecialchars($event['title']) . ' | Runit Events Hub';
require __DIR__ . '/../layouts/header.php';
?>

<!-- 1. Corporate Webinar & Event Details Hero Section -->
<section class="bms-details-hero">
    <div class="bms-details-hero-bg" style="background-image: url('<?= htmlspecialchars($event['banner_image'] ?: 'assets/banners/banner_ai.jpg') ?>');"></div>
    <div class="container bms-details-content">
        <!-- Breadcrumb on dark hero -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small text-white-50">
                <li class="breadcrumb-item"><a href="<?= app_url('') ?>" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= app_url('explore') ?>" class="text-white-50 text-decoration-none">Events</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page"><?= htmlspecialchars($event['title']) ?></li>
            </ol>
        </nav>

        <div class="row g-4 align-items-center">
            <!-- Left Poster Frame -->
            <div class="col-md-4 col-lg-3 text-center text-md-start">
                <div class="bms-poster-frame d-inline-block">
                    <img src="<?= htmlspecialchars(!empty($event['poster_image']) ? $event['poster_image'] : (!empty($event['card_image']) ? $event['card_image'] : ($event['banner_image'] ?: 'assets/banners/banner_ai.jpg'))) ?>" class="w-100 h-100 object-fit-cover" alt="Event Poster">
                </div>
            </div>

            <!-- Right Details & CTA -->
            <div class="col-md-8 col-lg-9">
                <h1 class="display-6 fw-bold text-white mb-2" style="line-height: 1.25;">
                    <?= htmlspecialchars($event['title']) ?>
                </h1>

                <!-- Corporate Program Verification Badge -->
                <div class="d-inline-flex flex-wrap align-items-center gap-3 px-3 py-2 rounded-3 mb-3" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.15);">
                    <div class="text-white small">
                        <span class="badge bg-danger text-uppercase me-2"><i class="bi bi-shield-check me-1"></i> Official Program</span>
                        <span class="text-light opacity-75">Code: <code><?= htmlspecialchars($event['event_code']) ?></code></span>
                    </div>
                    <?php if ($event['is_certificate_eligible']): ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-award-fill me-1"></i> Verified Credential</span>
                    <?php endif; ?>
                </div>

                <!-- Format & Metadata Chips -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="bms-format-chip"><i class="bi bi-camera-video me-1 text-danger"></i> <?= htmlspecialchars($event['meeting_platform']) ?> Online</span>
                    <span class="bms-format-chip"><i class="bi bi-clock me-1 text-warning"></i> <?= $event['duration_minutes'] ?> Minutes</span>
                    <span class="bms-format-chip"><i class="bi bi-translate me-1 text-info"></i> English</span>
                    <span class="bms-format-chip"><i class="bi bi-tag me-1 text-success"></i> <?= htmlspecialchars($event['event_type']) ?></span>
                    <?php if ($event['is_certificate_eligible']): ?>
                        <span class="bms-format-chip" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4);">
                            <i class="bi bi-award-fill me-1"></i> Verified Certificate
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Event Schedule Bar -->
                <div class="d-flex flex-wrap gap-4 text-white small mb-4">
                    <div><i class="bi bi-calendar3 text-danger me-1"></i> <strong><?= date('l, F j, Y', strtotime($event['event_date'])) ?></strong></div>
                    <div><i class="bi bi-clock-history text-warning me-1"></i> <strong><?= date('h:i A', strtotime($event['start_time'])) ?> IST</strong></div>
                    <div><i class="bi bi-people text-info me-1"></i> <strong><?= $availableSeats ?> Seats Available</strong> (Total: <?= $event['capacity'] ?>)</div>
                </div>

                <!-- Big Book Button -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <?php if ($isBooked): ?>
                        <div class="p-3 bg-success bg-opacity-25 rounded-3 border border-success d-flex align-items-center gap-3 text-white">
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            <div>
                                <strong class="d-block">You're Registered!</strong>
                                <span class="small text-white-50">Booking Code: <?= htmlspecialchars($booking['booking_code']) ?></span>
                            </div>
                            <a href="<?= app_url('booking-success/' . $booking['id']) ?>" class="btn btn-sm btn-light ms-2">
                                <i class="bi bi-ticket-perforated me-1"></i> View Attendee Pass
                            </a>
                        </div>
                    <?php elseif ($availableSeats <= 0): ?>
                        <a href="<?= app_url('checkout/' . $event['id']) ?>" class="btn btn-warning py-3 px-5 fs-5 fw-bold">
                            <i class="bi bi-clock-history me-2"></i> Join Waitlist (100% Full)
                        </a>
                    <?php else: ?>
                        <a href="<?= app_url('checkout/' . $event['id']) ?>" class="btn btn-bms-red py-3 px-5 fs-5">
                            <i class="bi bi-ticket-perforated-fill me-2"></i> Reserve Seat (Free)
                        </a>
                        <span class="small text-white-50">
                            <i class="bi bi-stopwatch text-warning me-1"></i> 10-Minute seat hold lock active on click
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Main Content & Sections -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if (!empty($event['recording_url'])): ?>
                    <?php 
                        $embedUrl = format_embed_url($event['recording_url']);
                        $isYt = (strpos($embedUrl, 'youtube') !== false || strpos($embedUrl, 'youtu.be') !== false);
                    ?>
                    <!-- Session Recording Video Embed Player -->
                    <div class="light-card mb-4 border border-danger border-opacity-50">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi <?= $isYt ? 'bi-youtube' : 'bi-play-btn-fill' ?> text-danger me-2"></i> Session Video Recording
                            </h5>
                            <span class="badge bg-danger text-uppercase px-2 py-1">Official Video</span>
                        </div>
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden border shadow-sm bg-black mb-2">
                            <iframe src="<?= htmlspecialchars($embedUrl) ?>" title="<?= htmlspecialchars($event['title']) ?> - Session Recording" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                        <span class="text-secondary small"><i class="bi bi-info-circle me-1"></i> Recorded session stream for this event.</span>
                    </div>
                <?php endif; ?>

                <!-- About the Event Card -->
                <div class="light-card mb-4">
                    <h4 class="fw-bold text-dark mb-3">About the Masterclass</h4>
                    <p class="text-secondary leading-relaxed mb-4" style="font-size: 1rem;">
                        <?= nl2br(htmlspecialchars($event['description'])) ?>
                    </p>

                    <?php if (!empty($event['is_certificate_eligible'])): ?>
                    <!-- Certificate USP Banner -->
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                        <i class="bi bi-award-fill text-warning fs-1"></i>
                        <div>
                            <strong class="text-dark d-block">Certification & Verifiable Credential</strong>
                            <span class="small text-secondary">
                                All registered attendees who complete at least <strong>75% of the total session duration</strong> will automatically receive a digitally stamped Certificate of Achievement with a public QR verification URL.
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Instructors & Speakers -->
                <div class="light-card mb-4">
                    <h4 class="fw-bold text-dark mb-4">Masterclass Instructors & Speakers</h4>
                    <div class="row g-4">
                        <?php foreach ($speakers as $spk): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                                    <img src="<?= htmlspecialchars($spk['photo'] ?: 'assets/speakers/sarah.jpg') ?>" class="rounded-circle object-fit-cover shadow-sm" style="width: 70px; height: 70px; border: 2px solid #ffffff;" alt="<?= htmlspecialchars($spk['name']) ?>">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($spk['name']) ?></h6>
                                        <div class="text-danger small fw-semibold"><?= htmlspecialchars($spk['title']) ?></div>
                                        <div class="text-secondary small mb-1"><?= htmlspecialchars($spk['company'] ?? '') ?></div>
                                        <?php if (!empty($spk['linkedin_url'])): ?>
                                            <a href="<?= htmlspecialchars($spk['linkedin_url']) ?>" target="_blank" class="small text-primary text-decoration-none">
                                                <i class="bi bi-linkedin me-1"></i> LinkedIn Profile
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Agenda Timeline -->
                <?php if (!empty($agendas)): ?>
                    <div class="light-card mb-4">
                        <h4 class="fw-bold text-dark mb-3">Session Agenda Timeline</h4>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($agendas as $agenda): ?>
                                <div class="p-3 bg-light rounded-3 border d-flex gap-3">
                                    <div class="text-danger fw-bold small text-nowrap" style="min-width: 140px;">
                                        <i class="bi bi-clock-fill me-1"></i> <?= date('h:i A', strtotime($agenda['time_start'])) ?> - <?= date('h:i A', strtotime($agenda['time_end'])) ?>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($agenda['topic']) ?></h6>
                                        <p class="small text-secondary mb-1"><?= htmlspecialchars($agenda['description'] ?? '') ?></p>
                                        <?php if (!empty($agenda['speaker_name'])): ?>
                                            <span class="badge bg-secondary small"><?= htmlspecialchars($agenda['speaker_name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Study Materials -->
                <?php if (!empty($materials)): ?>
                    <div class="light-card mb-4">
                        <h4 class="fw-bold text-dark mb-3">Pre-Read Materials & Artifacts</h4>
                        <div class="row g-3">
                            <?php foreach ($materials as $mat): ?>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
                                            <div>
                                                <span class="small text-dark fw-bold d-block"><?= htmlspecialchars($mat['title']) ?></span>
                                                <span class="text-secondary" style="font-size: 0.75rem;"><?= $mat['file_size_kb'] ?> KB</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- FAQs -->
                <div class="light-card">
                    <h4 class="fw-bold text-dark mb-3">Frequently Asked Questions</h4>
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I receive my pass and meeting credentials?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary small">
                                    Immediately upon booking, you will receive a digital Attendee Pass with an embedded verification QR code, direct meeting access credentials, and a downloadable calendar invitation (.ics).
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($event['is_certificate_eligible'])): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How is attendance calculated for certification?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary small">
                                    Our platform automatically tracks your join and leave duration. Attending 75% or more of the live masterclass automatically unlocks your verified certificate of completion on your dashboard.
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Sticky Booking Summary Card -->
            <div class="col-lg-4">
                <div class="light-card sticky-top shadow-sm" style="top: 80px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Seat Capacity Status</h6>
                        <span class="bms-tag-free">100% FREE</span>
                    </div>

                    <!-- Dynamic Seat Urgency Status (No raw count exposed, hides for past events) -->
                    <?php 
                    $seatStatus = format_seat_status($event);
                    ?>
                    <?php if ($seatStatus['is_past']): ?>
                        <div class="p-3 bg-light rounded-3 border mb-3 text-center">
                            <span class="badge bg-secondary mb-1">Session Completed</span>
                            <p class="text-secondary small mb-0">This webinar has concluded.</p>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary small">Seat Status:</span>
                                <span class="badge <?= $seatStatus['badge_class'] ?> py-1 px-2">
                                    <i class="bi <?= $seatStatus['icon'] ?> me-1"></i> <?= $seatStatus['text'] ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($isBooked): ?>
                        <a href="<?= app_url('booking-success/' . $booking['id']) ?>" class="btn btn-outline-dark w-100 py-2 mb-3">
                            <i class="bi bi-ticket-perforated me-1"></i> View Attendee Pass
                        </a>
                    <?php elseif ($availableSeats <= 0): ?>
                        <a href="<?= app_url('checkout/' . $event['id']) ?>" class="btn btn-warning w-100 py-3 fw-bold mb-3">
                            Join Waitlist
                        </a>
                    <?php else: ?>
                        <a href="<?= app_url('checkout/' . $event['id']) ?>" class="btn btn-bms-red w-100 py-3 fs-5 mb-3">
                            <i class="bi bi-ticket-perforated-fill me-2"></i> Reserve Seat (Free)
                        </a>
                    <?php endif; ?>

                    <!-- Social Sharing -->
                    <div class="mb-3 text-center">
                        <span class="text-secondary small d-block mb-2">Share this masterclass:</span>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($event['title'] . ' ' . app_url('event/' . $event['id'])) ?>" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1 small">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(app_url('event/' . $event['id'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1 small">
                                <i class="bi bi-linkedin"></i> LinkedIn
                            </a>
                        </div>
                    </div>

                    <hr>

                    <div class="small text-secondary">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span>Guaranteed instant confirmation pass</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-calendar-event text-danger fs-5"></i>
                            <span>1-Click Google / Outlook calendar sync</span>
                        </div>
                        <?php if (!empty($event['is_certificate_eligible'])): ?>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-patch-check text-warning fs-5"></i>
                            <span>Verifiable Certificate upon completion</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
