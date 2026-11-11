<?php
$pageTitle = htmlspecialchars($event['title']) . ' | Past Event Highlights';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= app_url('') ?>" class="text-secondary text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= app_url('explore', ['status' => 'completed']) ?>" class="text-secondary text-decoration-none">Past Highlights & Replays</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= htmlspecialchars($event['title']) ?></li>
        </ol>
    </nav>

    <!-- Header Banner Card (Light Theme) -->
    <div class="light-card mb-4" style="border-left: 6px solid var(--bms-red);">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-danger text-uppercase px-3 py-1 mb-2 fw-bold">
                    <i class="bi bi-collection-play-fill me-1"></i> PAST MASTERCLASS HIGHLIGHTS & ARCHIVE
                </span>
                <h1 class="display-6 fw-bold text-dark mb-2">
                    <?= htmlspecialchars($event['title']) ?>
                </h1>
                <p class="text-secondary mb-3">
                    <?= htmlspecialchars($event['short_summary']) ?>
                </p>
                <div class="d-flex flex-wrap gap-4 text-secondary small">
                    <span><i class="bi bi-calendar-check text-danger me-1"></i> Hosted on: <strong><?= date('F j, Y', strtotime($event['event_date'])) ?></strong></span>
                    <span><i class="bi bi-people text-success me-1"></i> <strong><?= $event['booked_count'] ?></strong> Attendees Completed</span>
                    <span><i class="bi bi-award text-warning me-1"></i> Verified Digital Credentials Issued</span>
                </div>
            </div>

            <div class="col-lg-4 text-lg-end">
                <?php if ($userCert): ?>
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <i class="bi bi-award-fill text-warning fs-1 d-block mb-1"></i>
                        <h6 class="text-dark fw-bold mb-1">Your Certificate is Ready!</h6>
                        <span class="small text-secondary font-monospace d-block mb-3"><?= htmlspecialchars($userCert['cert_number']) ?></span>
                        <a href="index.php?page=download-cert&id=<?= $userCert['id'] ?>" class="btn btn-sm btn-bms-red w-100 py-2">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Certificate
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Column: Video Replay & Slides -->
        <div class="col-lg-8">
            <!-- Video Recording Player -->
            <?php if (!empty($event['recording_url']) || !empty($videos)): ?>
                <div class="light-card mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-play-circle-fill text-danger me-2"></i> Full Session Video Recording</h5>
                    <?php if (!empty($event['recording_url'])): ?>
                        <?php $mainEmbed = format_embed_url($event['recording_url']); ?>
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden border mb-3 shadow-sm bg-black">
                            <iframe src="<?= htmlspecialchars($mainEmbed) ?>" title="<?= htmlspecialchars($event['title']) ?> - Session Replay" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($videos)): ?>
                        <?php foreach ($videos as $vid): ?>
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden border mb-3 shadow-sm bg-black">
                                <iframe src="<?= htmlspecialchars(format_embed_url($vid['video_url'])) ?>" title="<?= htmlspecialchars($vid['title']) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                            <div class="d-flex justify-content-between text-secondary small mb-3">
                                <span class="text-dark fw-bold"><?= htmlspecialchars($vid['title']) ?></span>
                                <span>Duration: <?= htmlspecialchars($vid['duration'] ?? 'Full Session') ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Presentation Slides Deck -->
            <div class="light-card mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-slides-fill text-warning me-2"></i> Master Slides & Technical Presentations</h5>
                <div class="d-flex flex-column gap-3">
                    <?php if (empty($materials)): ?>
                        <p class="text-secondary small mb-0">Presentation slides for this session are being prepared for public download.</p>
                    <?php else: ?>
                        <?php foreach ($materials as $mat): ?>
                            <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                                    <div>
                                        <h6 class="text-dark mb-0 fw-bold"><?= htmlspecialchars($mat['title']) ?></h6>
                                        <span class="text-secondary small">Format: <?= htmlspecialchars($mat['file_type']) ?> &bull; Size: <?= $mat['file_size_kb'] ?> KB</span>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-dark" onclick="alert('Downloading session slide deck...');">
                                    <i class="bi bi-download me-1"></i> Download Deck
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- External Code Links & GitHub Repos -->
            <?php if (!empty($links)): ?>
                <div class="light-card mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-github text-dark me-2"></i> Starter Code & Repositories</h5>
                    <div class="row g-3">
                        <?php foreach ($links as $link): ?>
                            <div class="col-md-6">
                                <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between text-decoration-none hover-shadow">
                                    <div>
                                        <div class="text-dark fw-semibold small"><?= htmlspecialchars($link['title']) ?></div>
                                        <span class="text-secondary small" style="font-size: 0.75rem;"><?= htmlspecialchars($link['url']) ?></span>
                                    </div>
                                    <i class="bi bi-box-arrow-up-right text-danger"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reviews & Ratings -->
            <div class="light-card">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-chat-square-quote-fill text-info me-2"></i> Attendee Feedback & Reviews</h5>
                <?php if (empty($reviews)): ?>
                    <p class="text-secondary small mb-0">No reviews recorded yet for this session.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong class="text-dark"><?= htmlspecialchars($rev['user_name']) ?></strong>
                                    <span class="text-secondary small ms-2"><?= htmlspecialchars($rev['user_company'] ?? '') ?></span>
                                </div>
                                <div class="text-warning">
                                    <?php for ($i = 0; $i < $rev['rating']; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                                </div>
                            </div>
                            <p class="text-dark small mb-1"><?= htmlspecialchars($rev['feedback_text'] ?? '') ?></p>
                            <?php if (!empty($rev['suggestions'])): ?>
                                <div class="small text-secondary fst-italic">"Suggestion: <?= htmlspecialchars($rev['suggestions']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Instructors -->
        <div class="col-lg-4">
            <div class="light-card sticky-top" style="top: 80px;">
                <h5 class="fw-bold text-dark mb-4">Masterclass Instructors</h5>
                <?php foreach ($speakers as $spk): ?>
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                        <img src="<?= htmlspecialchars($spk['photo'] ?: 'assets/speakers/sarah.jpg') ?>" class="rounded-circle object-fit-cover shadow-sm" style="width: 60px; height: 60px;" alt="<?= htmlspecialchars($spk['name']) ?>">
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($spk['name']) ?></h6>
                            <div class="text-danger small"><?= htmlspecialchars($spk['title']) ?></div>
                            <div class="text-secondary small"><?= htmlspecialchars($spk['company'] ?? '') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="p-3 bg-light rounded-3 border text-center mt-3">
                    <span class="small text-secondary d-block mb-2">Need to verify a certificate from this session?</span>
                    <a href="index.php?page=verify-cert" class="btn btn-outline-dark btn-sm w-100">
                        <i class="bi bi-patch-check me-1"></i> Public Certificate Verifier
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
