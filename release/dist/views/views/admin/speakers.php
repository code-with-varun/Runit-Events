<?php
$pageTitle = 'Manage Speakers | Runit Events Hub Admin';
$activeAdminTab = 'speakers';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-person-video3 text-danger me-2"></i> Speaker &amp; Instructor Accounts</h3>
            <span class="text-secondary small">Admin creates credentials and profile information for event presenters &amp; instructors</span>
        </div>
        <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary btn-sm px-3">&larr; Admin Console</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Create Speaker Form -->
        <div class="col-lg-5">
            <div class="light-card shadow-sm">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-plus-fill text-danger me-2"></i> Create Speaker Account</h5>
                <form action="<?= app_url('admin/speakers') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Full Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Sarah Jenkins" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Email Address * (Login ID)</label>
                        <input type="email" name="email" class="form-control" placeholder="speaker@runitevents.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Password *</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" value="speaker123" required>
                        <span class="text-muted" style="font-size: 0.75rem;">Default initial password: speaker123</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary small fw-bold">Company / Org</label>
                            <input type="text" name="company" class="form-control" placeholder="e.g. Runit Labs">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small fw-bold">Designation</label>
                            <input type="text" name="designation" class="form-control" placeholder="e.g. Chief Architect">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Mobile Number</label>
                        <input type="tel" name="mobile" class="form-control" placeholder="+91 98765 43210">
                    </div>

                    <!-- Speaker Photo with Upload & Gallery -->
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <label class="form-label text-secondary small fw-bold mb-1">Speaker Headshot Photo</label>
                        <div class="input-group mb-2">
                            <input type="text" name="photo" id="speakerPhotoInput" list="speakerPresets" class="form-control form-control-sm" value="assets/speakers/sarah.jpg" placeholder="assets/speakers/sarah.jpg">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="window.activeGalleryTargetInput='speakerPhotoInput'">
                                <i class="bi bi-images me-1"></i> Gallery
                            </button>
                        </div>
                        <datalist id="speakerPresets">
                            <option value="assets/speakers/sarah.jpg">Dr. Sarah Mitchell</option>
                            <option value="assets/speakers/vikram.jpg">Vikram Malhotra</option>
                            <option value="assets/speakers/ananya.jpg">Ananya Deshmukh</option>
                        </datalist>

                        <div class="mb-2">
                            <label class="form-label text-secondary small mb-1" style="font-size: 0.75rem;">Or Upload Photo File:</label>
                            <input type="file" name="photo_file" class="form-control form-control-sm" accept="image/*" onchange="previewUploadImage(this, 'speakerPhotoPreview')">
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-2">
                            <img id="speakerPhotoPreview" src="assets/speakers/sarah.jpg" alt="Preview" class="rounded-circle object-fit-cover border" style="width: 44px; height: 44px;" onerror="this.src='assets/speakers/sarah.jpg'">
                            <span class="text-secondary" style="font-size: 0.75rem;">Headshot preview</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-bms-red w-100 py-2">
                        <i class="bi bi-shield-check me-1"></i> Register Speaker Account
                    </button>
                </form>
            </div>
        </div>

        <!-- Speaker Accounts List -->
        <div class="col-lg-7">
            <div class="light-card p-0 overflow-hidden shadow-sm">
                <div class="p-3 border-bottom bg-light">
                    <h5 class="fw-bold text-dark mb-0">Active Speaker &amp; Instructor Accounts (<?= count($speakers) ?>)</h5>
                </div>

                <div class="table-responsive">
                    <table class="table light-table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Email / Login</th>
                                <th>Company &amp; Role</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($speakers)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-secondary">No speaker accounts registered yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($speakers as $spk): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?= htmlspecialchars($spk['avatar'] ?: 'assets/speakers/sarah.jpg') ?>" class="rounded-circle object-fit-cover" style="width: 38px; height: 38px;" alt="">
                                                <strong class="text-dark small"><?= htmlspecialchars($spk['name']) ?></strong>
                                            </div>
                                        </td>
                                        <td class="small text-secondary font-monospace"><?= htmlspecialchars($spk['email']) ?></td>
                                        <td class="small">
                                            <div class="text-dark fw-semibold"><?= htmlspecialchars($spk['designation'] ?? 'Speaker') ?></div>
                                            <div class="text-secondary"><?= htmlspecialchars($spk['company'] ?? '') ?></div>
                                        </td>
                                        <td class="small text-secondary"><?= date('M j, Y', strtotime($spk['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewUploadImage(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
document.getElementById('speakerPhotoInput')?.addEventListener('input', function() {
    var preview = document.getElementById('speakerPhotoPreview');
    if (preview && this.value) {
        preview.src = this.value;
    }
});
</script>

<?php require __DIR__ . '/gallery_modal.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
