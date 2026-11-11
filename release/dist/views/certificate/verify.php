<?php
$pageTitle = 'Public Certificate Verification Portal | Runit Events Hub';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="text-center mb-4">
                <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-2">
                    <i class="bi bi-shield-check me-1"></i> PUBLIC CREDENTIAL VERIFIER
                </span>
                <h2 class="fw-bold text-dark mb-1">Verify Certificate Authenticity</h2>
                <p class="text-secondary small">
                    Every certificate issued by Runit Events Hub contains a tamper-proof verification ID for employers, recruiters, and corporate institutions.
                </p>
            </div>

            <!-- Search Form Card -->
            <div class="light-card shadow-sm mb-4">
                <form action="<?= app_url('verify-cert') ?>" method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-warning border-end-0"><i class="bi bi-award-fill"></i></span>
                        <input type="text" name="cert" class="form-control bg-light border-start-0 font-monospace" placeholder="e.g. CERT-2026-0001245" value="<?= htmlspecialchars($certNumber ?? '') ?>" required>
                        <button type="submit" class="btn btn-bms-red px-4">Verify Now</button>
                    </div>
                </form>
            </div>

            <!-- Verification Results Display -->
            <?php if (!empty($certNumber)): ?>
                <?php if ($cert): ?>
                    <div class="light-card text-center shadow-md p-4 p-md-5" style="border: 2px solid #10b981;">
                        <div class="mb-3">
                            <span class="p-3 bg-success bg-opacity-10 rounded-circle text-success fs-1 d-inline-block shadow-sm">
                                <i class="bi bi-patch-check-fill"></i>
                            </span>
                        </div>

                        <span class="badge bg-success px-3 py-2 text-uppercase fw-bold mb-3">
                            <i class="bi bi-shield-fill-check me-1"></i> OFFICIALLY VERIFIED CREDENTIAL
                        </span>

                        <h3 class="fw-bold text-dark mb-1"><?= htmlspecialchars($cert['user_name']) ?></h3>
                        <p class="text-secondary small mb-4"><?= htmlspecialchars($cert['user_company'] ?? 'Verified Attendee') ?></p>

                        <div class="p-4 bg-light rounded-3 border text-start mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <span class="text-secondary small d-block">Webinar / Program Title:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($cert['event_title']) ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-secondary small d-block">Certificate Reference ID:</span>
                                    <strong class="font-monospace text-danger"><?= htmlspecialchars($cert['cert_number']) ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-secondary small d-block">Date of Completion:</span>
                                    <strong class="text-dark"><?= date('F j, Y', strtotime($cert['issue_date'])) ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-secondary small d-block">Attendance Criteria:</span>
                                    <strong class="text-success">&ge; 75% Live Duration Fulfilled</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="index.php?page=download-cert&cert=<?= urlencode($cert['cert_number']) ?>" class="btn btn-bms-red btn-sm px-4">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Verified PDF
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="light-card text-center shadow-sm p-4" style="border: 1px solid #ef4444;">
                        <i class="bi bi-x-circle-fill text-danger fs-1 mb-2 d-block"></i>
                        <h5 class="text-dark fw-bold mb-1">Certificate Not Found</h5>
                        <p class="text-secondary small mb-0">The certificate ID "<strong><?= htmlspecialchars($certNumber) ?></strong>" does not match any official credential in our records. Please verify the code.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
