<?php
// views/admin/gallery_modal.php - Reusable Media Gallery Modal
$galleryImages = get_media_gallery();
?>
<!-- Media Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="galleryModalLabel">
                    <i class="bi bi-images text-danger me-2"></i> Select from Media Gallery
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <p class="text-secondary small mb-3">
                    Click any image below to select it as the banner/photo. It will automatically populate the image path and update the live preview.
                </p>

                <?php if (empty($galleryImages)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-image fs-1 d-block mb-2"></i>
                        No images found in gallery. Upload an image above to populate.
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($galleryImages as $img): ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100 shadow-sm border gallery-item" style="cursor: pointer; transition: all 0.2s;" onclick="selectGalleryImage('<?= htmlspecialchars($img['path']) ?>')">
                                    <div style="height: 110px; overflow: hidden; background: #0b1329;">
                                        <img src="<?= htmlspecialchars($img['path']) ?>" class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($img['name']) ?>">
                                    </div>
                                    <div class="p-2 small text-truncate">
                                        <strong class="text-dark d-block text-truncate" style="font-size: 0.75rem;" title="<?= htmlspecialchars($img['name']) ?>"><?= htmlspecialchars($img['name']) ?></strong>
                                        <span class="badge bg-secondary" style="font-size: 0.65rem;"><?= htmlspecialchars($img['type']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function selectGalleryImage(imagePath) {
    // 1. Fill active input (data-target-input or #bannerImageInput)
    var targetInputId = window.activeGalleryTargetInput || 'bannerImageInput';
    var inputElem = document.getElementById(targetInputId);
    if (inputElem) {
        inputElem.value = imagePath;
        inputElem.dispatchEvent(new Event('input'));
        inputElem.dispatchEvent(new Event('change'));
    }

    // 2. Update preview if present
    var previewElem = document.getElementById(targetInputId + '_preview') || 
                      document.getElementById(targetInputId.replace('Input', 'Preview')) || 
                      document.getElementById('bannerPreview');
    if (previewElem) {
        previewElem.src = imagePath;
        previewElem.style.display = 'block';
    }

    // 3. Close modal
    var modalElem = document.getElementById('galleryModal');
    var modalInstance = bootstrap.Modal.getInstance(modalElem);
    if (modalInstance) {
        modalInstance.hide();
    }
}
</script>

