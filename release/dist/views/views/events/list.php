<?php
$isCompletedView = ($filters['status'] ?? '') === 'completed';
$pageTitle = ($isCompletedView ? 'Past Highlights & Replays' : 'Explore Technical Webinars & Workshops') . ' | Runit Events Hub';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= app_url('') ?>" class="text-secondary text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= $isCompletedView ? 'Past Highlights & Replays' : 'Webinars & Masterclasses' ?></li>
        </ol>
    </nav>

    <!-- Top Headline & Search -->
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold text-dark mb-1">
                <?= $isCompletedView ? 'Past Highlights & Session Replays' : 'Explore Technical Webinars & Masterclasses' ?>
            </h2>
            <p class="text-secondary small mb-0">
                <?= $isCompletedView ? 'Access past session recordings, master presentation slide decks, and verifiable attendee credentials.' : 'Browse through scheduled live webinars, corporate training programs, and certification tracks.' ?>
            </p>
        </div>
        <div class="col-lg-6">
            <form action="<?= app_url('explore') ?>" method="GET" class="d-flex gap-2">
                <?php if ($isCompletedView): ?>
                    <input type="hidden" name="status" value="completed">
                <?php endif; ?>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control bg-white border-start-0" placeholder="Search by topic, speaker name, or technology..." value="<?= htmlspecialchars($filters['query'] ?? '') ?>">
                    <button class="btn btn-bms-red px-4" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Filter Sidebar -->
        <div class="col-lg-3">
            <div class="light-card sticky-top" style="top: 80px;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-funnel-fill text-danger me-1"></i> Filters</h6>
                    <a href="<?= app_url('explore') ?>" class="small text-danger text-decoration-none fw-semibold">Clear All</a>
                </div>

                <form action="<?= app_url('explore') ?>" method="GET">
                    <?php if (!empty($filters['query'])): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($filters['query']) ?>">
                    <?php endif; ?>

                    <!-- Category Filter -->
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold text-uppercase">Category</label>
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Event Type Filter -->
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold text-uppercase">Event Format</label>
                        <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Formats</option>
                            <option value="Webinar" <?= ($filters['event_type'] ?? '') === 'Webinar' ? 'selected' : '' ?>>Live Webinar</option>
                            <option value="Workshop" <?= ($filters['event_type'] ?? '') === 'Workshop' ? 'selected' : '' ?>>Hands-on Workshop</option>
                            <option value="Corporate Training" <?= ($filters['event_type'] ?? '') === 'Corporate Training' ? 'selected' : '' ?>>Corporate Training</option>
                            <option value="Certification Program" <?= ($filters['event_type'] ?? '') === 'Certification Program' ? 'selected' : '' ?>>Certification Program</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-bold text-uppercase">Status</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Upcoming & Past</option>
                            <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Upcoming Live Only</option>
                            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed Replays</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-outline-dark btn-sm w-100">
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>

        <!-- Event Grid -->
        <div class="col-lg-9">
            <?php if (empty($events)): ?>
                <div class="light-card text-center py-5">
                    <i class="bi bi-calendar-x fs-1 text-secondary mb-3 d-block"></i>
                    <h5 class="text-dark fw-bold">No events found matching your criteria</h5>
                    <p class="text-secondary small mb-3">Try adjusting your filters or search keywords.</p>
                    <a href="<?= app_url('explore') ?>" class="btn btn-bms-red btn-sm px-4">View All Events</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($events as $evt): ?>
                        <?php 
                        $seatStatus = format_seat_status($evt);
                        $isPast = $seatStatus['is_past'] || ($evt['status'] === 'completed') || (!empty($evt['event_date']) && $evt['event_date'] < date('Y-m-d'));
                        $cardImg = !empty($evt['card_image']) ? $evt['card_image'] : (!empty($evt['banner_image']) ? $evt['banner_image'] : 'assets/banners/banner_ai.jpg');
                        $detailUrl = app_url(($isPast ? 'past/' : 'event/') . $evt['id']);
                        ?>
                        <div class="col-md-6">
                            <div class="bms-card">
                                <div class="bms-card-poster">
                                    <img src="<?= htmlspecialchars($cardImg) ?>" alt="<?= htmlspecialchars($evt['title']) ?>">
                                    <div class="bms-poster-bottom-bar">
                                        <span class="badge bg-danger text-uppercase px-2 py-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-award-fill me-1"></i> <?= htmlspecialchars($evt['event_type']) ?>
                                        </span>
                                        <span><i class="bi bi-calendar-event me-1 text-warning"></i> <?= date('M j, Y', strtotime($evt['event_date'])) ?></span>
                                    </div>
                                    <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white">
                                        <?= htmlspecialchars($evt['category_name']) ?>
                                    </span>
                                    <?php if ($isPast): ?>
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-secondary text-white">
                                            <i class="bi bi-collection-play-fill me-1"></i> REPLAY
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="bms-card-body">
                                    <div class="bms-card-category text-uppercase"><?= htmlspecialchars($evt['event_type']) ?> &bull; <?= htmlspecialchars($evt['meeting_platform']) ?></div>
                                    <h5 class="bms-card-title">
                                        <a href="<?= $detailUrl ?>"><?= htmlspecialchars($evt['title']) ?></a>
                                    </h5>
                                    <p class="bms-card-summary">
                                        <?= htmlspecialchars($evt['short_summary']) ?>
                                    </p>

                                    <!-- Dynamic Seat Urgency Status (No raw count exposed) -->
                                    <?php if (!$isPast): ?>
                                        <div class="bms-seat-chip <?= $seatStatus['chip_class'] ?>">
                                            <span class="fw-bold"><i class="bi <?= $seatStatus['icon'] ?> me-1"></i> <?= $seatStatus['text'] ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex align-items-center justify-content-between pt-2 mt-auto border-top">
                                        <?php if ($isPast): ?>
                                            <span class="badge bg-secondary-subtle text-secondary border">ARCHIVED SESSION</span>
                                            <a href="<?= $detailUrl ?>" class="btn btn-outline-danger btn-sm px-3">
                                                <i class="bi bi-play-circle me-1"></i> Highlights & Replay
                                            </a>
                                        <?php else: ?>
                                            <div>
                                                <span class="bms-tag-free">FREE</span>
                                                <?php if (!empty($evt['is_certificate_eligible'])): ?>
                                                    <span class="bms-tag-cert ms-1"><i class="bi bi-award-fill"></i> CERTIFIED</span>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?= $detailUrl ?>" class="btn btn-sm btn-bms-red px-3">
                                                Book Seat &rarr;
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
