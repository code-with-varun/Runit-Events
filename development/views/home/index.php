<?php
$pageTitle = 'Runit Events Hub - Masterclasses, Webinars & Certifications';
$sliderAnim = \App\Models\Setting::get('slider_animation', 'fade');
$sliderInterval = (int)\App\Models\Setting::get('slider_interval', '5000');
require __DIR__ . '/../layouts/header.php';
?>

<!-- 1. Hero Banner Carousel (Clean Image-Only - 100% Uncropped Display) -->
<section class="bms-carousel-wrapper">
    <div class="container">
        <div id="heroCarousel" class="carousel slide <?= ($sliderAnim === 'fade' || $sliderAnim === 'zoom-fade') ? 'carousel-fade' : '' ?> hero-anim-<?= htmlspecialchars($sliderAnim) ?>" data-bs-ride="carousel" data-bs-interval="<?= $sliderInterval ?>">
            <div class="carousel-inner" style="border-radius: 16px;">
                <?php foreach ($banners as $idx => $banner): ?>
                    <?php 
                        $targetUrl = $banner['target_url'] ?: 'explore';
                        $fullTargetUrl = (strpos($targetUrl, 'http') === 0) ? $targetUrl : app_url($targetUrl);
                    ?>
                    <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                        <a href="<?= htmlspecialchars($fullTargetUrl) ?>" class="bms-hero-clean-slide text-decoration-none">
                            <img src="<?= htmlspecialchars($banner['image_path']) ?>" alt="<?= htmlspecialchars($banner['title']) ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon p-3 rounded-circle bg-dark bg-opacity-75"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon p-3 rounded-circle bg-dark bg-opacity-75"></span>
            </button>

            <!-- Indicators cleanly below image so they never obscure text or artwork -->
            <div class="carousel-indicators">
                <?php foreach ($banners as $idx => $banner): ?>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" style="width: 10px; height: 10px; border-radius: 50%; margin: 0 4px;"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 2. Entertainment / Event Categories Pill Bar -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0">Browse By Category</h5>
            <a href="<?= app_url('explore') ?>" class="text-danger small text-decoration-none fw-semibold">See All Categories &rarr;</a>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= app_url('explore') ?>" class="bms-cat-pill active">
                <i class="bi bi-grid-fill"></i> All Events
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= app_url('explore', ['category' => $cat['id']]) ?>" class="bms-cat-pill">
                    <i class="bi <?= htmlspecialchars($cat['icon']) ?>"></i> <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. Recommended Webinars Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Recommended Live Webinars</h3>
                <p class="text-secondary small mb-0">Book virtual capacity slots with instant calendar invites & verified certificates.</p>
            </div>
            <a href="<?= app_url('explore') ?>" class="text-danger fw-semibold text-decoration-none">
                See All (<?= count($upcomingEvents) ?>) &rarr;
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($upcomingEvents)): ?>
                <div class="col-12">
                    <div class="p-5 text-center text-muted bg-white rounded-3 border">
                        <i class="bi bi-calendar4-event fs-1 text-secondary mb-2 d-block"></i>
                        <h5 class="fw-bold text-dark">No Upcoming Webinars Scheduled Right Now</h5>
                        <p class="small text-secondary mb-3">New masterclasses and certification sessions will appear here as soon as scheduled.</p>
                        <a href="<?= app_url('explore', ['status' => 'completed']) ?>" class="btn btn-outline-danger btn-sm px-3">Browse Past Webinars</a>
                    </div>
                </div>
            <?php else: ?>
            <?php foreach ($upcomingEvents as $evt): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="bms-card">
                        <!-- Poster Image with Rating Overlay -->
                        <div class="bms-card-poster">
                            <img src="<?= htmlspecialchars(!empty($evt['card_image']) ? $evt['card_image'] : ($evt['banner_image'] ?: 'assets/banners/banner_ai.jpg')) ?>" alt="<?= htmlspecialchars($evt['title']) ?>">
                            
                            <!-- Bottom Bar on Poster -->
                            <div class="bms-poster-bottom-bar">
                                <span class="badge bg-danger text-uppercase px-2 py-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-award-fill me-1"></i> <?= htmlspecialchars($evt['event_type']) ?>
                                </span>
                                <span><i class="bi bi-clock me-1 text-warning"></i> <?= $evt['duration_minutes'] ?> Mins</span>
                            </div>

                            <?php if ($evt['is_trending']): ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-danger fw-bold" style="box-shadow: 0 2px 6px rgba(220, 38, 38, 0.4);">
                                    <i class="bi bi-fire"></i> TRENDING
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="bms-card-body">
                            <div class="bms-card-category text-uppercase"><?= htmlspecialchars($evt['category_name']) ?> &bull; <?= htmlspecialchars($evt['meeting_platform']) ?></div>
                            <h5 class="bms-card-title">
                                <a href="<?= app_url('event/' . $evt['id']) ?>">
                                    <?= htmlspecialchars($evt['title']) ?>
                                </a>
                            </h5>
                            <p class="bms-card-summary">
                                <?= htmlspecialchars($evt['short_summary']) ?>
                            </p>

                            <!-- Dynamic Seat Urgency Status (No raw count exposed) -->
                            <?php 
                            $seatStatus = format_seat_status($evt);
                            if (!$seatStatus['is_past']):
                            ?>
                                <div class="bms-seat-chip <?= $seatStatus['chip_class'] ?>">
                                    <span class="fw-bold"><i class="bi <?= $seatStatus['icon'] ?> me-1"></i> <?= $seatStatus['text'] ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Footer Actions -->
                            <div class="d-flex align-items-center justify-content-between pt-2 mt-auto border-top">
                                <div>
                                    <span class="bms-tag-free">FREE PASS</span>
                                    <?php if (!empty($evt['is_certificate_eligible'])): ?>
                                        <span class="bms-tag-cert ms-1"><i class="bi bi-award-fill"></i> CERTIFIED</span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= app_url('event/' . $evt['id']) ?>" class="btn btn-sm btn-bms-red px-3">
                                    Book Seat &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 4. Trending Events with Rank Numbers -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="badge bg-danger text-uppercase px-2 py-1 mb-1">TOP 4 THIS WEEK</span>
                <h3 class="fw-bold text-dark mb-0">Trending Technical Programs</h3>
            </div>
            <a href="index.php?page=events" class="text-danger fw-semibold text-decoration-none">Explore Trending &rarr;</a>
        </div>

        <div class="row g-4">
            <?php foreach ($trendingEvents as $index => $tEvt): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="bms-card position-relative">
                        <!-- Rank Number Badge -->
                        <span class="position-absolute top-0 start-0 m-2 badge bg-dark text-white rounded-circle p-2 fs-6 fw-bold" style="width: 32px; height: 32px; z-index: 5;">
                            #<?= $index + 1 ?>
                        </span>
                        
                        <div class="bms-card-poster" style="height: 160px;">
                            <img src="<?= htmlspecialchars(!empty($tEvt['card_image']) ? $tEvt['card_image'] : ($tEvt['banner_image'] ?: 'assets/banners/banner_cloud.jpg')) ?>" alt="<?= htmlspecialchars($tEvt['title']) ?>">
                            <div class="bms-poster-bottom-bar">
                                <span class="small font-monospace"><i class="bi bi-calendar3 me-1 text-warning"></i> <?= date('M j', strtotime($tEvt['event_date'])) ?></span>
                                <span class="text-success small fw-bold"><i class="bi bi-check-circle"></i> Filling Fast</span>
                            </div>
                        </div>

                        <div class="bms-card-body p-3">
                            <span class="small text-muted text-uppercase"><?= htmlspecialchars($tEvt['category_name']) ?></span>
                            <h6 class="bms-card-title mb-2">
                                <a href="index.php?page=event-detail&id=<?= $tEvt['id'] ?>"><?= htmlspecialchars($tEvt['title']) ?></a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-auto border-top">
                                <?php $tStatus = format_seat_status($tEvt); ?>
                                <span class="badge <?= $tStatus['badge_class'] ?>"><i class="bi <?= $tStatus['icon'] ?> me-1"></i> <?= $tStatus['text'] ?></span>
                                <a href="<?= app_url('event/' . $tEvt['id']) ?>" class="btn btn-sm btn-bms-red px-2 py-1" style="font-size: 0.8rem;">
                                    Book
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. Past Masterclasses & Knowledge Hub -->
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="text-danger small fw-bold text-uppercase"><i class="bi bi-collection-play-fill me-1"></i> Knowledge Repository</span>
                <h3 class="fw-bold text-dark mt-1">Past Sessions: Replays & Master Slide Decks</h3>
                <p class="text-secondary small mb-0">Missed a live webinar? Access recordings, master presentations (PPT/PDF), and verified attendee certificates.</p>
            </div>
            <a href="<?= app_url('explore', ['status' => 'completed']) ?>" class="btn btn-outline-danger btn-sm px-3">
                View Full Library &rarr;
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($pastEvents)): ?>
                <div class="col-12">
                    <div class="p-5 text-center text-muted bg-white rounded-3 border">
                        <i class="bi bi-collection-play fs-1 text-secondary mb-2 d-block"></i>
                        <h5 class="fw-bold text-dark">No Past Recordings Yet</h5>
                        <p class="small text-secondary mb-0">Completed webinars and masterclasses will be archived here with recordings and presentation slide decks.</p>
                    </div>
                </div>
            <?php else: ?>
            <?php foreach ($pastEvents as $past): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="bms-card">
                        <div class="bms-card-poster">
                            <img src="<?= htmlspecialchars($past['banner_image'] ?: 'assets/banners/banner_cloud.jpg') ?>" alt="<?= htmlspecialchars($past['title']) ?>">
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <a href="<?= app_url('past/' . $past['id']) ?>" class="btn btn-danger rounded-circle p-3 shadow-lg text-white" style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-play-fill fs-3"></i>
                                </a>
                            </div>
                            <span class="position-absolute top-0 start-0 m-2 badge bg-dark text-white">
                                REPLAY AVAILABLE
                            </span>
                        </div>

                        <div class="bms-card-body">
                            <span class="bms-card-category"><?= htmlspecialchars($past['category_name']) ?> &bull; Completed Session</span>
                            <h5 class="bms-card-title">
                                <a href="<?= app_url('past/' . $past['id']) ?>"><?= htmlspecialchars($past['title']) ?></a>
                            </h5>
                            <p class="bms-card-summary">
                                <?= htmlspecialchars($past['short_summary']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-auto border-top small text-secondary">
                                <span><i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> <?= $past['materials_count'] ?> Slide Decks</span>
                                <a href="<?= app_url('past/' . $past['id']) ?>" class="btn btn-sm btn-outline-dark px-3">
                                    Watch & Download &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 6. Platform Value Pillars -->
<section class="py-5 bg-white border-top">
    <div class="container text-center">
        <h6 class="text-uppercase text-secondary small fw-bold mb-4" style="letter-spacing: 1px;">Enterprise Standards &amp; Verification</h6>
        <div class="row g-4 justify-content-center">
            <?php if (has_certificate_programs()): ?>
            <div class="col-md-4 col-sm-6">
                <div class="p-3 bg-light rounded-3 border h-100 text-start">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-shield-check text-danger fs-4"></i>
                        <strong class="text-dark">Tamper-Proof Certificates</strong>
                    </div>
                    <p class="small text-secondary mb-0">Every completed participant receives an authentic credential with unique QR verification on the blockchain-ready ledger.</p>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-md-4 col-sm-6">
                <div class="p-3 bg-light rounded-3 border h-100 text-start">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-person-video3 text-primary fs-4"></i>
                        <strong class="text-dark">Corporate Masterclasses</strong>
                    </div>
                    <p class="small text-secondary mb-0">Curated technical workshops, architecture deep dives, and executive training led by verified industry practitioners.</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="p-3 bg-light rounded-3 border h-100 text-start">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-calendar2-check text-success fs-4"></i>
                        <strong class="text-dark">One-Click Calendar Sync</strong>
                    </div>
                    <p class="small text-secondary mb-0">Instant seat reservation with universal .ics calendar invites for Google Calendar, Outlook, and Apple Calendar.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
