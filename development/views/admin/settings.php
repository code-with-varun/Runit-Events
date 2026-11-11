<?php
$pageTitle = 'Footer & Platform Settings | Runit Events Hub Admin';
$activeAdminTab = 'settings';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-gear-fill text-danger me-2"></i> Footer &amp; Platform Settings</h3>
            <span class="text-secondary small">Manage organization contact info, address, social channels, and public footer text</span>
        </div>
        <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary btn-sm px-3">&larr; Dashboard</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show py-3 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Settings Form -->
        <div class="col-lg-8">
            <div class="light-card shadow-sm p-4">
                <form action="<?= app_url('admin/settings') ?>" method="POST">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-building text-danger me-2"></i> Organization &amp; Branding
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Organization Name *</label>
                            <input type="text" name="org_name" class="form-control" value="<?= htmlspecialchars($settings['org_name'] ?? 'Runit Events Hub') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Tagline</label>
                            <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($settings['tagline'] ?? '') ?>" placeholder="Enterprise Webinars & Masterclasses">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-secondary small fw-bold">Footer About / Mission Statement</label>
                            <textarea name="footer_about" class="form-control" rows="3" placeholder="Brief summary of Runit Events Hub shown in the footer..."><?= htmlspecialchars($settings['footer_about'] ?? '') ?></textarea>
                            <span class="text-muted" style="font-size: 0.75rem;">This text appears on the public footer across all pages.</span>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-geo-alt text-danger me-2"></i> Contact &amp; Support Details
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Support Email Address *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="support_email" class="form-control" value="<?= htmlspecialchars($settings['support_email'] ?? 'support@runito.in') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Support Phone / Helpdesk</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="support_phone" class="form-control" value="<?= htmlspecialchars($settings['support_phone'] ?? '+91 (80) 4567-8900') ?>">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-secondary small fw-bold">Office / Campus Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="bi bi-geo-alt-fill"></i></span>
                                <input type="text" name="office_address" class="form-control" value="<?= htmlspecialchars($settings['office_address'] ?? 'Runit Innovation Hub, Bengaluru, India') ?>">
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-share text-danger me-2"></i> Social Media &amp; Channels
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">LinkedIn URL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-linkedin"></i></span>
                                <input type="url" name="linkedin_url" class="form-control" value="<?= htmlspecialchars($settings['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/company/runit">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">Twitter / X URL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark"><i class="bi bi-twitter-x"></i></span>
                                <input type="url" name="twitter_url" class="form-control" value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>" placeholder="https://x.com/runit">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">YouTube URL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-danger"><i class="bi bi-youtube"></i></span>
                                <input type="url" name="youtube_url" class="form-control" value="<?= htmlspecialchars($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/@runit">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small fw-bold">GitHub / Developer URL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark"><i class="bi bi-github"></i></span>
                                <input type="url" name="github_url" class="form-control" value="<?= htmlspecialchars($settings['github_url'] ?? '') ?>" placeholder="https://github.com/code-with-varun">
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-c-circle text-danger me-2"></i> Copyright
                    </h5>

                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-bold">Footer Copyright Notice</label>
                        <input type="text" name="copyright_text" class="form-control" value="<?= htmlspecialchars($settings['copyright_text'] ?? '© 2026 Runit. All rights reserved.') ?>">
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-bms-red px-4">
                            <i class="bi bi-save me-1"></i> Save Footer Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Live Preview Card -->
        <div class="col-lg-4">
            <div class="light-card shadow-sm sticky-top" style="top: 80px;">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-eye text-primary me-2"></i> Live Footer Preview</h6>
                <div class="p-3 bg-dark text-white rounded-3 small">
                    <strong class="d-block text-white mb-1"><?= htmlspecialchars($settings['org_name'] ?? 'Runit Events Hub') ?></strong>
                    <p class="text-white-50 mb-3" style="font-size: 0.75rem;">
                        <?= htmlspecialchars($settings['footer_about'] ?? 'Runit Events Hub is the official corporate event and technical webinar platform of Runit.') ?>
                    </p>
                    <div class="mb-2 text-white-50" style="font-size: 0.75rem;">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($settings['office_address'] ?? 'Bengaluru, India') ?>
                    </div>
                    <div class="mb-2 text-white-50" style="font-size: 0.75rem;">
                        <i class="bi bi-envelope-fill text-secondary me-1"></i> <?= htmlspecialchars($settings['support_email'] ?? 'support@runito.in') ?>
                    </div>
                    <div class="mb-3 text-white-50" style="font-size: 0.75rem;">
                        <i class="bi bi-telephone-fill text-secondary me-1"></i> <?= htmlspecialchars($settings['support_phone'] ?? '+91 (80) 4567-8900') ?>
                    </div>
                    <div class="pt-2 border-top border-secondary text-white-50" style="font-size: 0.7rem;">
                        <?= htmlspecialchars($settings['copyright_text'] ?? '© 2026 Runit. All rights reserved.') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

