<?php
use App\Models\Setting;
$pageTitle = 'Hero Banner CMS | Runit Events Hub Admin';
$activeAdminTab = 'banners';
$currentAnimation = Setting::get('slider_animation', 'fade');
$currentInterval = Setting::get('slider_interval', '5000');
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-images text-danger me-2"></i> Homepage Hero Slider CMS</h3>
            <span class="text-secondary small">Manage promotional edge-to-edge banners displayed on the main homepage</span>
        </div>
        <a href="<?= app_url('admin') ?>" class="btn btn-outline-secondary btn-sm px-3">&larr; Admin Console</a>
    </div>

    <!-- Banner Dimensions & Specifications Alert Box -->
    <div class="alert alert-info border-info bg-light p-4 rounded-4 shadow-sm mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-aspect-ratio-fill text-danger fs-2 mt-1"></i>
            <div>
                <h5 class="fw-bold text-dark mb-1">Recommended Hero Banner Specifications (100% Uncropped Display)</h5>
                <p class="text-secondary small mb-2">
                    The homepage hero slider displays your banner graphics cleanly without any automatic text overlays or cropping. All text, speaker highlights, dates, and logos you design inside the image will render 100% intact from edge to edge.
                </p>
                <div class="d-flex flex-wrap gap-2 small">
                    <span class="badge bg-white text-dark border p-2">
                        <i class="bi bi-arrows-fullscreen text-danger me-1"></i> <strong>Best Resolution:</strong> 1920 &times; 600 px (or 1920 &times; 500 px / 1200 &times; 400 px)
                    </span>
                    <span class="badge bg-white text-dark border p-2">
                        <i class="bi bi-bounding-box text-primary me-1"></i> <strong>Aspect Ratio:</strong> 16:5 (~3.2 : 1) or ~3.84 : 1 Widescreen
                    </span>
                    <span class="badge bg-white text-dark border p-2">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Zero Crop:</strong> Full image visible, indicators placed below
                    </span>
                    <span class="badge bg-white text-dark border p-2">
                        <i class="bi bi-file-earmark-image text-info me-1"></i> <strong>Formats:</strong> JPG, PNG, WebP (&le; 2 MB)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['saved'])): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Banner settings saved successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Hero Slider Transition & Animation Settings Card -->
    <div class="light-card shadow-sm mb-4 border border-secondary border-opacity-25">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-play-circle-fill text-danger me-2"></i> Hero Slider Animation &amp; Transition
                </h5>
                <span class="text-secondary small">Select how promotional banners transition when cycling on the homepage</span>
            </div>
            <span class="badge bg-danger text-uppercase px-2 py-1 mt-2 mt-md-0">
                <i class="bi bi-lightning-charge me-1"></i> Active: <?= strtoupper($currentAnimation) ?>
            </span>
        </div>

        <?php if (isset($_GET['anim_saved'])): ?>
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Slider animation and timing updated successfully! View homepage to see the new effect.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= app_url('admin/banners') ?>" method="POST">
            <input type="hidden" name="action" value="update_animation">

            <div class="row g-3 mb-3">
                <!-- 1. Smooth Fade -->
                <div class="col-md-4 col-sm-6">
                    <label class="card h-100 p-3 border rounded-3 text-start position-relative <?= $currentAnimation === 'fade' ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>" style="cursor: pointer;">
                        <input type="radio" name="slider_animation" value="fade" class="form-check-input position-absolute top-0 end-0 m-3" <?= $currentAnimation === 'fade' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-circle-half text-danger me-1"></i> Smooth Crossfade</div>
                        <span class="text-secondary small">Classic BookMyShow soft dissolve between banner slides.</span>
                    </label>
                </div>

                <!-- 2. Horizontal Slide -->
                <div class="col-md-4 col-sm-6">
                    <label class="card h-100 p-3 border rounded-3 text-start position-relative <?= $currentAnimation === 'slide' ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>" style="cursor: pointer;">
                        <input type="radio" name="slider_animation" value="slide" class="form-check-input position-absolute top-0 end-0 m-3" <?= $currentAnimation === 'slide' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-arrow-left-right text-primary me-1"></i> Horizontal Slide</div>
                        <span class="text-secondary small">Standard linear sliding carousel motion from right to left.</span>
                    </label>
                </div>

                <!-- 3. Ken Burns Zoom & Fade -->
                <div class="col-md-4 col-sm-6">
                    <label class="card h-100 p-3 border rounded-3 text-start position-relative <?= $currentAnimation === 'zoom-fade' ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>" style="cursor: pointer;">
                        <input type="radio" name="slider_animation" value="zoom-fade" class="form-check-input position-absolute top-0 end-0 m-3" <?= $currentAnimation === 'zoom-fade' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-zoom-in text-success me-1"></i> Ken Burns Zoom &amp; Fade</div>
                        <span class="text-secondary small">Cinematic slow zoom-in while crossfading into view.</span>
                    </label>
                </div>

                <!-- 4. 3D Perspective Flip -->
                <div class="col-md-4 col-sm-6">
                    <label class="card h-100 p-3 border rounded-3 text-start position-relative <?= $currentAnimation === 'flip-3d' ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>" style="cursor: pointer;">
                        <input type="radio" name="slider_animation" value="flip-3d" class="form-check-input position-absolute top-0 end-0 m-3" <?= $currentAnimation === 'flip-3d' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-box text-warning me-1"></i> 3D Perspective Flip</div>
                        <span class="text-secondary small">Interactive 3D cube perspective rotation effect.</span>
                    </label>
                </div>

                <!-- 5. Vertical Slide Up -->
                <div class="col-md-4 col-sm-6">
                    <label class="card h-100 p-3 border rounded-3 text-start position-relative <?= $currentAnimation === 'slide-up' ? 'border-danger bg-danger bg-opacity-10' : 'bg-light' ?>" style="cursor: pointer;">
                        <input type="radio" name="slider_animation" value="slide-up" class="form-check-input position-absolute top-0 end-0 m-3" <?= $currentAnimation === 'slide-up' ? 'checked' : '' ?> onchange="this.form.submit()">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-arrow-up text-info me-1"></i> Vertical Slide Up</div>
                        <span class="text-secondary small">Modern upward card transition between banners.</span>
                    </label>
                </div>

                <!-- Timing -->
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 p-3 border rounded-3 bg-light">
                        <label class="form-label text-secondary small fw-bold mb-1">Slide Cycle Timing</label>
                        <select name="slider_interval" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="3000" <?= $currentInterval === '3000' ? 'selected' : '' ?>>Fast (3 Seconds)</option>
                            <option value="5000" <?= ($currentInterval === '5000' || empty($currentInterval)) ? 'selected' : '' ?>>Standard (5 Seconds - Recommended)</option>
                            <option value="7000" <?= $currentInterval === '7000' ? 'selected' : '' ?>>Relaxed (7 Seconds)</option>
                            <option value="10000" <?= $currentInterval === '10000' ? 'selected' : '' ?>>Slow (10 Seconds)</option>
                        </select>
                        <span class="text-muted mt-2" style="font-size: 0.72rem;"><i class="bi bi-info-circle me-1"></i> Selecting updates immediately.</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= app_url('') ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-eye me-1"></i> Preview Homepage Slider
                </a>
                <button type="submit" class="btn btn-sm btn-bms-red px-4">
                    <i class="bi bi-check2-circle me-1"></i> Apply Animation
                </button>
            </div>
        </form>
    </div>

    <!-- Add New Banner Box -->
    <div class="light-card shadow-sm mb-4 border border-secondary border-opacity-25">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle text-danger me-2"></i> Add New Hero Banner</h5>
        <form action="<?= app_url('admin/banners') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-secondary small fw-bold mb-1">Banner Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. AI & LLM Masterclass" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-secondary small fw-bold mb-1">Click Destination URL</label>
                    <input type="text" name="target_url" class="form-control font-monospace" placeholder="explore or event/1" value="explore">
                </div>

                <div class="col-md-7">
                    <label class="form-label text-secondary small fw-bold mb-1">Banner Image Path or URL *</label>
                    <div class="input-group">
                        <input type="text" name="image_path" id="newBannerPath" class="form-control" placeholder="assets/banners/hero_ai.jpg" value="assets/banners/hero_ai.jpg" required>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="window.activeGalleryTargetInput='newBannerPath'">
                            <i class="bi bi-images me-1"></i> Gallery
                        </button>
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="form-label text-secondary small fw-bold mb-1">Or Upload from Device</label>
                    <input type="file" name="banner_file" class="form-control" accept="image/*" onchange="previewUploadImage(this, 'newBannerPreview')">
                </div>

                <div class="col-12">
                    <div class="d-flex align-items-center gap-3 p-2 bg-light rounded border" style="max-width: 480px;">
                        <img id="newBannerPreview" src="assets/banners/hero_ai.jpg" alt="Preview" style="height: 55px; width: 120px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;" onerror="this.src='assets/banners/hero_ai.jpg'">
                        <div>
                            <strong class="text-dark d-block small">New Banner Preview</strong>
                            <span class="text-muted" style="font-size: 0.72rem;">1920 &times; 500 ratio</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary small fw-bold mb-1">CTA Button Text</label>
                    <input type="text" name="cta_text" class="form-control form-control-sm" value="Register Now">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Badge Text</label>
                    <input type="text" name="badge_text" class="form-control form-control-sm" value="FEATURED">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-secondary small fw-bold mb-1">Display Order</label>
                    <input type="number" name="display_order" class="form-control form-control-sm" value="<?= count($banners) + 1 ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end justify-content-between">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" name="is_active" id="newActive" value="1" checked>
                        <label class="form-check-label text-dark small fw-semibold" for="newActive">Active in Slider</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-bms-red px-3">
                        <i class="bi bi-plus-lg me-1"></i> Add Banner
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Banners List -->
    <div class="row g-4">
        <?php foreach ($banners as $b): ?>
            <div class="col-lg-6">
                <div class="light-card shadow-sm h-100">
                    <div class="position-relative mb-3 rounded-3 overflow-hidden border" style="height: 180px; background: #0b1329;">
                        <img id="banner_img_<?= $b['id'] ?>" src="<?= htmlspecialchars($b['image_path']) ?>" class="w-100 h-100 object-fit-cover" alt="Banner Preview">
                        <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark font-monospace shadow-sm">
                            Order #<?= $b['display_order'] ?>
                        </span>
                        <span class="position-absolute top-0 end-0 m-2 badge <?= $b['is_active'] ? 'bg-success' : 'bg-danger' ?> shadow-sm">
                            <?= $b['is_active'] ? 'ACTIVE IN SLIDER' : 'INACTIVE' ?>
                        </span>
                    </div>

                    <form action="<?= app_url('admin/banners') ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">

                        <div class="mb-2">
                            <label class="form-label text-secondary small fw-bold mb-1">Banner Title</label>
                            <input type="text" name="title" class="form-control form-control-sm" value="<?= htmlspecialchars($b['title']) ?>" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-secondary small fw-bold mb-1">Click-through Destination (Target URL)</label>
                            <input type="text" name="target_url" class="form-control form-control-sm font-monospace" value="<?= htmlspecialchars($b['target_url'] ?: 'explore') ?>">
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-7">
                                <label class="form-label text-secondary small fw-bold mb-1">Image Path</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="image_path" id="banner_input_<?= $b['id'] ?>" class="form-control" value="<?= htmlspecialchars($b['image_path']) ?>" required>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="window.activeGalleryTargetInput='banner_input_<?= $b['id'] ?>'">
                                        <i class="bi bi-images"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-5">
                                <label class="form-label text-secondary small fw-bold mb-1">Replace Image</label>
                                <input type="file" name="banner_file_<?= $b['id'] ?>" class="form-control form-control-sm" accept="image/*" onchange="previewUploadImage(this, 'banner_img_<?= $b['id'] ?>')">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-secondary small fw-bold mb-1">Display Order</label>
                                <input type="number" name="display_order" class="form-control form-control-sm" value="<?= $b['display_order'] ?>">
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitch_<?= $b['id'] ?>" value="1" <?= $b['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label text-dark small fw-semibold" for="activeSwitch_<?= $b['id'] ?>">Active</label>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields to preserve schema compatibility -->
                        <input type="hidden" name="subtitle" value="<?= htmlspecialchars($b['subtitle'] ?? '') ?>">
                        <input type="hidden" name="cta_text" value="<?= htmlspecialchars($b['cta_text'] ?? 'Register Now') ?>">
                        <input type="hidden" name="badge_text" value="<?= htmlspecialchars($b['badge_text'] ?? 'FEATURED') ?>">

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-secondary" style="font-size: 0.75rem;">1920 &times; 500 px</span>
                            <button type="submit" class="btn btn-sm btn-bms-red">
                                <i class="bi bi-save me-1"></i> Update Banner
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
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
</script>

<?php require __DIR__ . '/gallery_modal.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
