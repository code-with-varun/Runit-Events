<?php
$pageTitle = 'Sign In | Runit Events Hub';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="light-card shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="assets/logo/primary-logo.png" alt="Runit Events Hub Logo" class="img-fluid mb-3" style="max-height: 52px;" onerror="this.onerror=null; this.src='assets/logo/app-icon-red.png';">
                    <h3 class="fw-bold text-dark mb-1">Welcome Back</h3>
                    <p class="text-secondary small">Sign in to your Runit Events Hub account</p>
                </div>

                <?php if (!empty($notice)): ?>
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> <?= htmlspecialchars($notice) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="<?= app_url('login') ?>" method="POST" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Username or Email</label>
                        <input type="text" name="email" class="form-control" placeholder="admin or you@company.com" required autofocus>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-secondary small fw-bold mb-0">Password</label>
                            <a href="<?= app_url('forgot-password') ?>" class="text-secondary small text-decoration-none">Forgot Password?</a>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-bms-red w-100 py-2 fs-6 mb-3">
                        Sign In &rarr;
                    </button>
                </form>

                <div class="mt-4 text-center text-secondary small pt-3 border-top">
                    New to Runit Events Hub? <a href="<?= app_url('register') ?>" class="text-danger text-decoration-none fw-bold">Register free account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
