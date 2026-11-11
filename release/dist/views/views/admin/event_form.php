<?php
$isEdit = !empty($event);
$pageTitle = ($isEdit ? 'Edit Event' : 'Create New Event') . ' | Runit Events Hub Admin';
$activeAdminTab = 'event-form';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/nav.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold text-dark mb-0"><?= $isEdit ? 'Edit Event: ' . htmlspecialchars($event['title']) : 'Create New Webinar / Masterclass' ?></h3>
                    <span class="text-secondary small">Define event schedule, thumbnail artwork, hero slider promo, and auto-certificate parameters</span>
                </div>
                <a href="<?= app_url('admin/events') ?>" class="btn btn-outline-secondary btn-sm px-3">&larr; Back to Events</a>
            </div>

            <div class="light-card shadow-sm p-4 p-md-5">
                <form action="<?= $isEdit ? app_url('admin/event-form/' . $event['id']) : app_url('admin/events/create') ?>" method="POST" enctype="multipart/form-data">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label text-secondary small fw-bold">Event Title *</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title'] ?? '') ?>" placeholder="e.g. Bring Your Brand Summit" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Event Code</label>
                            <input type="text" name="event_code" class="form-control font-monospace" value="<?= htmlspecialchars($event['event_code'] ?? ('EVT-' . date('Y') . '-' . mt_rand(100, 999))) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Category</label>
                            <select name="category_id" class="form-select">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($event['category_id'] ?? 1) == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Event Type</label>
                            <select name="event_type" class="form-select">
                                <option value="Webinar" <?= ($event['event_type'] ?? '') === 'Webinar' ? 'selected' : '' ?>>Webinar</option>
                                <option value="Workshop" <?= ($event['event_type'] ?? '') === 'Workshop' ? 'selected' : '' ?>>Workshop</option>
                                <option value="Corporate Training" <?= ($event['event_type'] ?? '') === 'Corporate Training' ? 'selected' : '' ?>>Corporate Training</option>
                                <option value="Conference" <?= ($event['event_type'] ?? '') === 'Conference' ? 'selected' : '' ?>>Conference</option>
                                <option value="Certification Program" <?= ($event['event_type'] ?? '') === 'Certification Program' ? 'selected' : '' ?>>Certification Program</option>
                                <option value="Meetup" <?= ($event['event_type'] ?? '') === 'Meetup' ? 'selected' : '' ?>>Meetup</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="published" <?= ($event['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published (Upcoming)</option>
                                <option value="completed" <?= ($event['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed (Past / Archives)</option>
                                <option value="draft" <?= ($event['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Short Summary</label>
                        <textarea name="short_summary" class="form-control" rows="2" placeholder="Brief 1-2 sentence overview shown on catalog cards..."><?= htmlspecialchars($event['short_summary'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Full Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Detailed masterclass description, prerequisites, and learning outcomes..."><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Event Date *</label>
                            <input type="date" name="event_date" class="form-control" value="<?= $event['event_date'] ?? date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Start Time *</label>
                            <input type="time" name="start_time" class="form-control" value="<?= $event['start_time'] ?? '18:00' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Duration (Minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control" value="<?= $event['duration_minutes'] ?? 60 ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Capacity Slots (Seats)</label>
                            <input type="number" name="capacity" class="form-control" value="<?= $event['capacity'] ?? 250 ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-secondary small fw-bold">Live Meeting Link (Zoom / Teams / Meet)</label>
                            <input type="url" name="meeting_link" class="form-control" value="<?= htmlspecialchars($event['meeting_link'] ?? 'https://zoom.us/j/1234567890') ?>" required>
                        </div>
                    </div>

                    <!-- 1. MULTI-FORMAT MEDIA & VISUAL ASSETS (Square Card, Landscape Banner, Portrait Poster) -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="bi bi-images text-danger me-2"></i> Event Visual Assets (Square, Landscape &amp; Portrait)
                                </h5>
                                <span class="text-secondary small">Upload or configure multi-format images optimized for cards, wide hero banners, and mobile flyers</span>
                            </div>
                            <span class="badge bg-danger text-uppercase px-2 py-1">3 Formats Supported</span>
                        </div>

                        <!-- 1A. SQUARE CARD IMAGE (1:1 Ratio) -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label text-dark fw-bold mb-0">
                                    <i class="bi bi-square-fill text-primary me-1"></i> 1. Card &amp; Catalog Thumbnail (Square 1:1)
                                </label>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">1:1 Square</span>
                            </div>
                            <p class="text-secondary small mb-2">
                                Displayed on BookMyShow-style event catalog cards, search listings, attendee badges, and square grid previews.
                            </p>

                            <!-- Specifications Box -->
                            <div class="alert alert-light border p-2 rounded-3 small mb-3 d-flex flex-wrap align-items-center gap-2">
                                <span><i class="bi bi-arrows-fullscreen text-primary me-1"></i> <strong>Required Resolution:</strong> <code>800 &times; 800 px</code> (or <code>600 &times; 600 px</code>, min <code>400 &times; 400 px</code>)</span>
                                <span class="text-muted">&bull;</span>
                                <span><strong>Aspect Ratio:</strong> 1:1 Square</span>
                                <span class="text-muted">&bull;</span>
                                <span><strong>Formats:</strong> JPG, PNG, WebP (&le; 2 MB)</span>
                            </div>

                            <div class="row g-3 align-items-center mb-2">
                                <div class="col-lg-7">
                                    <label class="form-label text-secondary small fw-bold">Card Image Path or URL</label>
                                    <div class="input-group">
                                        <input type="text" name="card_image" id="cardImageInput" list="bannerPresets" class="form-control" value="<?= htmlspecialchars($event['card_image'] ?? ($event['banner_image'] ?? 'assets/banners/hero_ai.jpg')) ?>" placeholder="e.g. assets/banners/hero_ai.jpg or full URL">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="window.activeGalleryTargetInput='cardImageInput'">
                                            <i class="bi bi-images me-1"></i> Gallery
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <label class="form-label text-secondary small fw-bold">Or Upload from Device</label>
                                    <input type="file" name="card_file" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewUploadImage(this, 'cardPreview')">
                                </div>
                            </div>

                            <!-- Live Square Preview -->
                            <div class="d-flex align-items-center gap-3 p-2 bg-white rounded border" style="max-width: 420px;">
                                <img id="cardPreview" src="<?= htmlspecialchars($event['card_image'] ?? ($event['banner_image'] ?? 'assets/banners/hero_ai.jpg')) ?>" alt="Card Preview" style="height: 70px; width: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;" onerror="this.src='assets/banners/hero_ai.jpg'">
                                <div>
                                    <strong class="text-dark d-block small">1:1 Square Card Preview</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Crisp framing for desktop cards and mobile apps</span>
                                </div>
                            </div>
                        </div>

                        <!-- 1B. LANDSCAPE BANNER IMAGE (16:9 / Ultra-Wide Hero) -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label text-dark fw-bold mb-0">
                                    <i class="bi bi-aspect-ratio-fill text-danger me-1"></i> 2. Event Header &amp; Landscape Banner (16:9 / Wide) *
                                </label>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">16:9 Landscape &bull; Hero Banner Ready</span>
                            </div>
                            <p class="text-secondary small mb-2">
                                Displayed on the Event Detail hero section, webinar header, and can be used directly for the <strong>Homepage Hero Slider Carousel</strong>.
                            </p>

                            <!-- Specifications Box -->
                            <div class="alert alert-light border p-2 rounded-3 small mb-3 d-flex flex-wrap align-items-center gap-2">
                                <span><i class="bi bi-arrows-fullscreen text-danger me-1"></i> <strong>Required Resolution:</strong> <code>1920 &times; 1080 px</code> (or <code>1280 &times; 720 px</code>, or <code>1920 &times; 500 px</code>)</span>
                                <span class="text-muted">&bull;</span>
                                <span><strong>Aspect Ratio:</strong> 16:9 Landscape (or ~3.84:1 Wide)</span>
                                <span class="text-muted">&bull;</span>
                                <span><strong>Formats:</strong> JPG, PNG, WebP (&le; 2 MB)</span>
                            </div>

                            <div class="row g-3 align-items-center mb-2">
                                <div class="col-lg-7">
                                    <label class="form-label text-secondary small fw-bold">Landscape Image Path or URL *</label>
                                    <div class="input-group">
                                        <input type="text" name="banner_image" id="bannerImageInput" list="bannerPresets" class="form-control" value="<?= htmlspecialchars($event['banner_image'] ?? 'assets/banners/hero_ai.jpg') ?>" placeholder="e.g. assets/banners/hero_ai.jpg or full URL" required>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="window.activeGalleryTargetInput='bannerImageInput'">
                                            <i class="bi bi-images me-1"></i> Gallery
                                        </button>
                                    </div>
                                    <datalist id="bannerPresets">
                                        <option value="assets/banners/hero_ai.jpg">AI &amp; LLMs Masterclass</option>
                                        <option value="assets/banners/hero_k8s.jpg">Cloud-Native Kubernetes</option>
                                        <option value="assets/banners/hero_cert.jpg">Full-Stack Architecture Certification</option>
                                        <option value="assets/banners/banner_cloud.jpg">Cloud Systems</option>
                                        <option value="assets/banners/banner_fintech.jpg">FinTech Microservices</option>
                                        <option value="assets/banners/banner_web.jpg">Modern Web Performance</option>
                                    </datalist>
                                </div>
                                <div class="col-lg-5">
                                    <label class="form-label text-secondary small fw-bold">Or Upload from Device</label>
                                    <input type="file" name="banner_file" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewUploadImage(this, 'bannerPreview')">
                                </div>
                            </div>

                            <!-- Live Landscape Preview -->
                            <div class="d-flex align-items-center gap-3 p-2 bg-white rounded border" style="max-width: 480px;">
                                <img id="bannerPreview" src="<?= htmlspecialchars($event['banner_image'] ?? 'assets/banners/hero_ai.jpg') ?>" alt="Landscape Banner Preview" style="height: 60px; width: 140px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;" onerror="this.src='assets/banners/hero_ai.jpg'">
                                <div>
                                    <strong class="text-dark d-block small">Landscape Live Preview</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Wide backdrop for event detail hero &amp; homepage hero carousel</span>
                                </div>
                            </div>
                        </div>

                        <!-- 1C. PORTRAIT / VERTICAL POSTER (3:4 or 9:16 Ratio) -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label text-dark fw-bold mb-0">
                                    <i class="bi bi-phone-fill text-success me-1"></i> 3. Portrait Poster / Flyer (Portrait 3:4 / 9:16)
                                </label>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Portrait &bull; Mobile &amp; Flyer Ready</span>
                            </div>
                            <p class="text-secondary small mb-2">
                                For future vertical feeds, mobile story cards (WhatsApp/Instagram), downloadable printable flyers, and portrait popups.
                            </p>

                            <!-- Specifications Box -->
                            <div class="alert alert-light border p-2 rounded-3 small mb-3 d-flex flex-wrap align-items-center gap-2">
                                <span><i class="bi bi-arrows-fullscreen text-success me-1"></i> <strong>Required Resolution:</strong> <code>750 &times; 1000 px</code> (3:4 Poster) or <code>1080 &times; 1920 px</code> (9:16 Story/Mobile)</span>
                                <span class="text-muted">&bull;</span>
                                <span><strong>Aspect Ratio:</strong> 3:4 or 9:16 Vertical Portrait</span>
                                <span class="text-muted">&bull;</span>
                                <span><strong>Formats:</strong> JPG, PNG, WebP (&le; 2 MB)</span>
                            </div>

                            <div class="row g-3 align-items-center mb-2">
                                <div class="col-lg-7">
                                    <label class="form-label text-secondary small fw-bold">Portrait Poster Path or URL</label>
                                    <div class="input-group">
                                        <input type="text" name="poster_image" id="posterImageInput" list="bannerPresets" class="form-control" value="<?= htmlspecialchars($event['poster_image'] ?? '') ?>" placeholder="e.g. uploads/events/poster_flyer.jpg or full URL">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="window.activeGalleryTargetInput='posterImageInput'">
                                            <i class="bi bi-images me-1"></i> Gallery
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <label class="form-label text-secondary small fw-bold">Or Upload from Device</label>
                                    <input type="file" name="poster_file" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewUploadImage(this, 'posterPreview')">
                                </div>
                            </div>

                            <!-- Live Portrait Preview -->
                            <div class="d-flex align-items-center gap-3 p-2 bg-white rounded border" style="max-width: 420px;">
                                <img id="posterPreview" src="<?= htmlspecialchars(!empty($event['poster_image']) ? $event['poster_image'] : ($event['banner_image'] ?? 'assets/banners/hero_ai.jpg')) ?>" alt="Portrait Poster Preview" style="height: 85px; width: 64px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;" onerror="this.src='assets/banners/hero_ai.jpg'">
                                <div>
                                    <strong class="text-dark d-block small">Portrait Live Preview</strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Vertical flyer for stories, mobile portrait feeds &amp; print</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. INTEGRATED HOMEPAGE HERO SLIDER PROMO (Sync feature requested by user) -->
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-stars text-danger fs-4"></i>
                                <div>
                                    <strong class="text-dark d-block">Featured Homepage Hero Slider Promo</strong>
                                    <span class="text-secondary small">Automatically promote this event on the top edge-to-edge animated hero carousel</span>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="sync_hero_banner" id="syncHeroSwitch" value="1" <?= (!empty($linkedBanner) && $linkedBanner['is_active']) ? 'checked' : '' ?> onchange="toggleHeroBannerFields(this.checked)">
                                <label class="form-check-label text-dark small fw-bold" for="syncHeroSwitch">Feature in Hero Slider</label>
                            </div>
                        </div>

                        <div id="heroBannerFields" style="display: <?= (!empty($linkedBanner) && $linkedBanner['is_active']) ? 'block' : 'none' ?>;" class="pt-3 border-top mt-3">
                            <!-- Hero Slider Banner Specifications Alert -->
                            <div class="alert alert-warning border-warning bg-white p-3 rounded-3 small mb-3">
                                <div class="d-flex align-items-start gap-2 mb-1">
                                    <i class="bi bi-aspect-ratio-fill text-danger fs-5 mt-1"></i>
                                    <div>
                                        <strong class="text-dark d-block">Recommended Hero Banner Specifications (100% Uncropped Display)</strong>
                                        <div class="text-secondary small">
                                            <strong>Resolution:</strong> 1920 &times; 600 px (or 1920 &times; 500 px / 1200 &times; 400 px) &bull;
                                            <strong>Aspect Ratio:</strong> ~3.2:1 (16:5) or ~3.84:1 Widescreen &bull;
                                            <strong>Zero Crop:</strong> Automatically displays 100% of your artwork without clipping
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold d-block mb-2">Hero Slider Banner Image Choice:</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="hero_image_source" id="heroSourceSame" value="same" <?= (empty($linkedBanner['image_path']) || $linkedBanner['image_path'] === ($event['banner_image'] ?? '')) ? 'checked' : '' ?> onchange="toggleHeroUploadCustom(false)">
                                    <label class="form-check-label text-dark small" for="heroSourceSame">
                                        <strong>Use Event Landscape Banner</strong> (Auto-syncs directly with the Landscape Banner set above)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hero_image_source" id="heroSourceCustom" value="custom" <?= (!empty($linkedBanner['image_path']) && $linkedBanner['image_path'] !== ($event['banner_image'] ?? '')) ? 'checked' : '' ?> onchange="toggleHeroUploadCustom(true)">
                                    <label class="form-check-label text-dark small" for="heroSourceCustom">
                                        <strong>Upload dedicated Ultra-Wide Hero Banner</strong> (Custom 1920 &times; 500 px artwork)
                                    </label>
                                </div>
                            </div>

                            <!-- Dedicated Ultra-Wide Banner Upload Inputs -->
                            <div id="heroCustomFields" style="display: <?= (!empty($linkedBanner['image_path']) && $linkedBanner['image_path'] !== ($event['banner_image'] ?? '')) ? 'block' : 'none' ?>;" class="p-3 bg-white rounded border mb-3">
                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col-md-7">
                                        <label class="form-label text-secondary small fw-bold mb-1">Wide Hero Image Path</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="hero_image_path" id="heroImagePathInput" class="form-control" value="<?= htmlspecialchars($linkedBanner['image_path'] ?? '') ?>" placeholder="assets/banners/hero_ai.jpg">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#galleryModal" onclick="window.activeGalleryTargetInput='heroImagePathInput'">
                                                <i class="bi bi-images me-1"></i> Gallery
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label text-secondary small fw-bold mb-1">Or Upload File (1920&times;500)</label>
                                        <input type="file" name="hero_banner_file" class="form-control form-control-sm" accept="image/*" onchange="previewUploadImage(this, 'heroCustomPreview')">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border" style="max-width: 420px;">
                                    <img id="heroCustomPreview" src="<?= htmlspecialchars($linkedBanner['image_path'] ?? 'assets/banners/hero_ai.jpg') ?>" alt="Hero Preview" style="height: 50px; width: 130px; object-fit: cover; border-radius: 4px;" onerror="this.src='assets/banners/hero_ai.jpg'">
                                    <span class="text-muted" style="font-size: 0.72rem;">Dedicated Hero Slider Graphic (1920 &times; 500 px)</span>
                                </div>
                            </div>

                            <!-- Hero Banner Meta Controls -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small fw-bold mb-1">CTA Button Text</label>
                                    <input type="text" name="hero_cta_text" class="form-control form-control-sm" value="<?= htmlspecialchars($linkedBanner['cta_text'] ?? 'Reserve Seat') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small fw-bold mb-1">Badge Tag</label>
                                    <input type="text" name="hero_badge_text" class="form-control form-control-sm" value="<?= htmlspecialchars($linkedBanner['badge_text'] ?? 'FEATURED') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small fw-bold mb-1">Slider Display Order</label>
                                    <input type="number" name="hero_display_order" class="form-control form-control-sm" value="<?= (int)($linkedBanner['display_order'] ?? 1) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. POST-EVENT VIDEO EMBED (YouTube / Vimeo / Runit Showcase / <iframe>) -->
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-play-circle-fill text-danger fs-5"></i>
                                <strong class="text-dark small">Post-Event Video Embed (YouTube, Vimeo, Runit Showcase, or &lt;iframe&gt;)</strong>
                            </div>
                            <span id="recordingSourceBadge" class="badge bg-light text-secondary border">Auto-Detect</span>
                        </div>
                        <p class="text-secondary small mb-2">
                            Paste a video link (YouTube, Vimeo, Runit Showcase) <strong>OR</strong> paste the entire <code>&lt;iframe src="..."&gt;&lt;/iframe&gt;</code> embed code. The system will automatically extract and configure the embedded video player for attendees.
                        </p>
                        <textarea name="recording_url" id="recordingUrlInput" class="form-control font-monospace small mb-2" rows="2" placeholder="e.g. https://www.youtube.com/watch?v=... or <iframe src='https://runito.in/showcase/embed/...' ...></iframe>" oninput="detectVideoEmbedType(this.value)"><?= htmlspecialchars($event['recording_url'] ?? '') ?></textarea>
                        
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-check2-circle text-success me-1"></i> Supports: Runit Showcase (<code>https://runito.in/showcase/embed/...</code>), YouTube, Vimeo, or standard &lt;iframe&gt;.
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" onclick="testPreviewEmbed()">
                                <i class="bi bi-eye me-1"></i> Test Embed Preview
                            </button>
                        </div>

                        <!-- Live Inline Video Preview Container -->
                        <div id="liveVideoPreviewBox" class="mt-3" style="display: none;">
                            <span class="text-secondary small fw-bold d-block mb-1">Live Player Preview:</span>
                            <div class="ratio ratio-16x9 rounded border overflow-hidden bg-black" style="max-height: 280px;">
                                <iframe id="livePreviewIframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- 4. MAP SPEAKER / INSTRUCTOR (Keynote Presenter) -->
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-video3 text-danger fs-5"></i>
                                <strong class="text-dark small">Map Speaker / Instructor</strong>
                            </div>
                            <a href="<?= app_url('admin/speakers') ?>" target="_blank" class="small text-danger text-decoration-none fw-semibold">
                                <i class="bi bi-plus-circle me-1"></i> Add Speaker Account &rarr;
                            </a>
                        </div>
                        <p class="text-secondary small mb-3">
                            Select an industry speaker or corporate instructor to feature on the event page, passes, and certificate credential ledger.
                        </p>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold">Select Speaker *</label>
                                <select name="speaker_user_id" id="speakerSelect" class="form-select" onchange="onSpeakerSelected(this)">
                                    <option value="0" data-title="" data-company="" data-bio="">-- No Speaker / Self-Organized --</option>
                                    <?php foreach ($allSpeakers as $spk): ?>
                                        <option value="<?= $spk['id'] ?>" 
                                            data-name="<?= htmlspecialchars($spk['name']) ?>" 
                                            data-title="<?= htmlspecialchars($spk['designation'] ?? '') ?>" 
                                            data-company="<?= htmlspecialchars($spk['company'] ?? '') ?>" 
                                            data-avatar="<?= htmlspecialchars($spk['avatar'] ?? 'assets/speakers/sarah.jpg') ?>" 
                                            <?= (!empty($mappedSpeaker) && (int)$mappedSpeaker['user_id'] === (int)$spk['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($spk['name']) ?> (<?= htmlspecialchars($spk['company'] ?: 'Speaker') ?> - <?= htmlspecialchars($spk['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold">Speaker Title / Role Designation</label>
                                <input type="text" name="speaker_title" id="speakerTitleInput" class="form-control" value="<?= htmlspecialchars($mappedSpeaker['title'] ?? '') ?>" placeholder="e.g. Principal Architect / Keynote Speaker">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold">Speaker Company / Organization</label>
                                <input type="text" name="speaker_company" id="speakerCompanyInput" class="form-control" value="<?= htmlspecialchars($mappedSpeaker['company'] ?? '') ?>" placeholder="e.g. Google Cloud / Runit Infotech">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold">Speaker Short Bio</label>
                                <input type="text" name="speaker_bio" id="speakerBioInput" class="form-control" value="<?= htmlspecialchars($mappedSpeaker['bio'] ?? '') ?>" placeholder="Brief 1-sentence bio...">
                            </div>
                        </div>
                    </div>

                    <!-- Visibility & Certificate Options -->
                    <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_certificate_eligible" id="certSwitch" value="1" <?= (!isset($event) || !empty($event['is_certificate_eligible'])) ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark small fw-semibold" for="certSwitch">Award Digital Certificate</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="featSwitch" value="1" <?= !empty($event['is_featured']) ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark small fw-semibold" for="featSwitch">Feature on Homepage</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_trending" id="trendSwitch" value="1" <?= !empty($event['is_trending']) ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark small fw-semibold" for="trendSwitch">Mark as Trending</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= app_url('admin/events') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-bms-red px-4">
                            <i class="bi bi-save me-1"></i> Save Event &amp; Sync
                        </button>
                    </div>
                </form>
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

function toggleHeroBannerFields(checked) {
    var fields = document.getElementById('heroBannerFields');
    if (fields) {
        fields.style.display = checked ? 'block' : 'none';
    }
}

function toggleHeroUploadCustom(isCustom) {
    var customBox = document.getElementById('heroCustomFields');
    if (customBox) {
        customBox.style.display = isCustom ? 'block' : 'none';
    }
}

function onSpeakerSelected(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.value !== '0') {
        var titleInp = document.getElementById('speakerTitleInput');
        var compInp = document.getElementById('speakerCompanyInput');
        if (titleInp && !titleInp.value) {
            titleInp.value = opt.getAttribute('data-title') || '';
        }
        if (compInp && !compInp.value) {
            compInp.value = opt.getAttribute('data-company') || '';
        }
    }
}

function detectVideoEmbedType(val) {
    var badge = document.getElementById('recordingSourceBadge');
    if (!badge) return;
    val = (val || '').trim();
    if (!val) {
        badge.className = 'badge bg-light text-secondary border';
        badge.innerText = 'Empty';
        return;
    }
    if (val.indexOf('<iframe') !== -1) {
        badge.className = 'badge bg-success text-white';
        badge.innerText = 'iFrame Embed Code';
    } else if (val.indexOf('showcase') !== -1) {
        badge.className = 'badge bg-primary text-white';
        badge.innerText = 'Runit Showcase';
    } else if (val.indexOf('youtube') !== -1 || val.indexOf('youtu.be') !== -1) {
        badge.className = 'badge bg-danger text-white';
        badge.innerText = 'YouTube Video';
    } else if (val.indexOf('vimeo') !== -1) {
        badge.className = 'badge bg-info text-dark';
        badge.innerText = 'Vimeo Video';
    } else {
        badge.className = 'badge bg-secondary text-white';
        badge.innerText = 'Direct Embed URL';
    }
}

function testPreviewEmbed() {
    var raw = (document.getElementById('recordingUrlInput')?.value || '').trim();
    var box = document.getElementById('liveVideoPreviewBox');
    var iframe = document.getElementById('livePreviewIframe');
    if (!raw) {
        alert('Please paste a video URL or <iframe> embed code first.');
        return;
    }
    var cleanUrl = raw;
    var match = raw.match(/src=["']([^"']+)["']/i);
    if (match) {
        cleanUrl = match[1];
    } else if (cleanUrl.indexOf('youtube.com/watch') !== -1) {
        var v = cleanUrl.match(/v=([a-zA-Z0-9_-]+)/);
        if (v) cleanUrl = 'https://www.youtube-nocookie.com/embed/' + v[1] + '?rel=0&modestbranding=1';
    } else if (cleanUrl.indexOf('youtu.be/') !== -1) {
        var v = cleanUrl.match(/youtu\.be\/([a-zA-Z0-9_-]+)/);
        if (v) cleanUrl = 'https://www.youtube-nocookie.com/embed/' + v[1] + '?rel=0&modestbranding=1';
    } else if (cleanUrl.indexOf('/showcase/watch/') !== -1) {
        cleanUrl = cleanUrl.replace('/showcase/watch/', '/showcase/embed/');
    }
    iframe.src = cleanUrl;
    box.style.display = 'block';
}

if (document.getElementById('recordingUrlInput') && document.getElementById('recordingUrlInput').value) {
    detectVideoEmbedType(document.getElementById('recordingUrlInput').value);
}

document.getElementById('cardImageInput')?.addEventListener('input', function() {
    var preview = document.getElementById('cardPreview');
    if (preview && this.value) {
        preview.src = this.value;
    }
});

document.getElementById('bannerImageInput')?.addEventListener('input', function() {
    var preview = document.getElementById('bannerPreview');
    if (preview && this.value) {
        preview.src = this.value;
    }
});

document.getElementById('posterImageInput')?.addEventListener('input', function() {
    var preview = document.getElementById('posterPreview');
    if (preview && this.value) {
        preview.src = this.value;
    }
});

document.getElementById('heroImagePathInput')?.addEventListener('input', function() {
    var preview = document.getElementById('heroCustomPreview');
    if (preview && this.value) {
        preview.src = this.value;
    }
});
</script>

<?php require __DIR__ . '/gallery_modal.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
