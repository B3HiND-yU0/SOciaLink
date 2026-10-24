<div class="row justify-content-center">
  <div class="col-xl-6 col-lg-8 col-12">
    <section class="sl-card p-4 p-md-5 shadow-sm">
      <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= e(url('post/index')) ?>" class="btn sl-icon-btn">
          <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0 fw-bold">Edit Post</h1>
      </div>

      <form method="post" action="<?= e(url('post/update/' . (int) $post['id'])) ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
        <?= csrf_field() ?>
        
        <div class="mb-3">
          <label for="content" class="form-label fw-semibold">Post Content</label>
          <textarea class="form-control rounded-3" id="content" name="content" rows="4" maxlength="1000" required
                    data-counter="#contentCount"><?= e($post['content']) ?></textarea>
          <div class="d-flex justify-content-between mt-1">
            <div class="invalid-feedback">Write something before saving.</div>
            <small class="text-muted ms-auto" id="contentCount"></small>
          </div>
        </div>

        <?php if ($post['image'] && is_file(__DIR__ . '/../../../public/uploads/' . $post['image'])): ?>
          <div class="mb-3 p-3 rounded-4" style="background: var(--sl-surface-secondary);">
            <p class="form-label fw-semibold mb-2">Current Photo</p>
            <img class="sl-preview-img mb-2 d-block" src="<?= e(asset('uploads/' . rawurlencode($post['image']))) ?>" alt="Current post image">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
              <label class="form-check-label text-danger fw-semibold" for="remove_image">
                <i class="bi bi-trash me-1"></i> Remove this photo
              </label>
            </div>
          </div>
        <?php endif; ?>

        <div class="mb-4">
          <label for="image" class="form-label fw-semibold">
            <?= $post['image'] ? 'Replace Photo' : 'Attach Photo' ?> <span class="text-muted fw-normal">(optional)</span>
          </label>
          <input type="file" class="form-control rounded-pill" id="image" name="image" accept="image/*" data-image data-preview="#editPreview">
          <div class="invalid-feedback"></div>
          <div class="sl-image-preview-wrapper d-none mt-2">
            <img id="editPreview" class="sl-preview-img d-none" alt="Preview of chosen image">
          </div>
        </div>

        <div class="d-flex align-items-center gap-2 pt-2 border-top" style="border-color: var(--sl-border) !important;">
          <button class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" type="submit">
            <i class="bi bi-check-lg me-1"></i> Save Changes
          </button>
          <a class="btn btn-outline-secondary rounded-pill px-4" href="<?= e(url('post/index')) ?>">Cancel</a>
        </div>
      </form>
    </section>
  </div>
</div>
