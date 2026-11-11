</main>

<?php
$footerSettings = \App\Models\Setting::all();
?>

<!-- Runit Events Hub Footer -->
<footer>
    <!-- 24/7 Support Banner -->
    <div class="bms-footer-banner">
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-headset fs-2 text-danger"></i>
                <div>
                    <strong class="d-block text-white"><?= htmlspecialchars($footerSettings['org_name'] ?? 'Runit Events Hub') ?> Support</strong>
                    <span class="small text-light opacity-75">Need assistance with webinar registration, access links, or certificates?</span>
                </div>
            </div>
            <a href="mailto:<?= htmlspecialchars($footerSettings['support_email'] ?? 'support@runito.in') ?>" class="btn btn-sm btn-bms-red px-3">
                <i class="bi bi-envelope me-1"></i> Contact Support
            </a>
        </div>
    </div>

    <!-- Main Footer Links -->
    <div class="bms-main-footer">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <a href="<?= app_url('') ?>" class="d-inline-block mb-3">
                        <img src="assets/logo/horizontal-lockup-light.png" alt="<?= htmlspecialchars($footerSettings['org_name'] ?? 'Runit Events') ?>" style="max-height: 40px;" onerror="this.onerror=null; this.src='assets/logo/primary-logo-light.png';">
                    </a>
                    <p class="small text-secondary mb-3">
                        <?= nl2br(htmlspecialchars($footerSettings['footer_about'] ?? 'Runit Events Hub is the official corporate event and technical webinar platform of Runit.')) ?>
                    </p>
                    <div class="d-flex gap-2">
                        <?php if (!empty($footerSettings['linkedin_url'])): ?>
                            <a href="<?= htmlspecialchars($footerSettings['linkedin_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-linkedin"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($footerSettings['twitter_url'])): ?>
                            <a href="<?= htmlspecialchars($footerSettings['twitter_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-twitter-x"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($footerSettings['youtube_url'])): ?>
                            <a href="<?= htmlspecialchars($footerSettings['youtube_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-youtube"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($footerSettings['github_url'])): ?>
                            <a href="<?= htmlspecialchars($footerSettings['github_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-github"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6>Event Categories</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="<?= app_url('explore', ['type' => 'Webinar']) ?>">Live Masterclasses</a></li>
                        <li><a href="<?= app_url('explore', ['type' => 'Workshop']) ?>">Hands-on Workshops</a></li>
                        <li><a href="<?= app_url('explore', ['type' => 'Corporate Training']) ?>">Corporate Training</a></li>
                        <?php if (has_certificate_programs()): ?>
                            <li><a href="<?= app_url('explore', ['type' => 'Certification Program']) ?>">Certification Tracks</a></li>
                        <?php endif; ?>
                        <li><a href="<?= app_url('explore', ['status' => 'completed']) ?>">Past Event Recordings</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6>Credentials & Verification</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <?php if (has_certificate_programs()): ?>
                            <li><a href="<?= app_url('verify-cert') ?>"><i class="bi bi-patch-check text-warning me-1"></i> Public Certificate Verifier</a></li>
                        <?php endif; ?>
                        <li><a href="<?= app_url('dashboard') ?>"><i class="bi bi-ticket-perforated text-danger me-1"></i> My Attendee Passes</a></li>
                        <?php if (has_certificate_programs()): ?>
                            <li><a href="<?= app_url('dashboard') ?>#certs"><i class="bi bi-award text-info me-1"></i> Download Certificates</a></li>
                        <?php endif; ?>
                        <li><a href="<?= app_url('admin') ?>"><i class="bi bi-shield-lock text-secondary me-1"></i> Organizer Admin Portal</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6>Contact Details</h6>
                    <?php if (!empty($footerSettings['office_address'])): ?>
                        <p class="small text-secondary mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i> <?= htmlspecialchars($footerSettings['office_address']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($footerSettings['support_email'])): ?>
                        <p class="small text-secondary mb-2"><i class="bi bi-envelope-fill text-secondary me-2"></i> <a href="mailto:<?= htmlspecialchars($footerSettings['support_email']) ?>" class="text-secondary"><?= htmlspecialchars($footerSettings['support_email']) ?></a></p>
                    <?php endif; ?>
                    <?php if (!empty($footerSettings['support_phone'])): ?>
                        <p class="small text-secondary"><i class="bi bi-telephone-fill text-secondary me-2"></i> <?= htmlspecialchars($footerSettings['support_phone']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <hr style="border-color: rgba(255, 255, 255, 0.1);">

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-secondary">
                <div><?= htmlspecialchars($footerSettings['copyright_text'] ?? '© ' . date('Y') . ' Runit. All rights reserved.') ?></div>
                <div class="d-flex gap-3">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Participation</a>
                    <?php if (has_certificate_programs()): ?>
                        <a href="<?= app_url('verify-cert') ?>">Certificate Verification</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="js/app.js"></script>
</body>
</html>
