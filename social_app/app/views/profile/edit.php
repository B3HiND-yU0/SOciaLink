<div class="row justify-content-center">
  <div class="col-xl-6 col-lg-8 col-12">
    <section class="sl-card p-4 p-md-5 shadow-sm">
      <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= e(url('profile/show/' . $profile['username'])) ?>" class="btn sl-icon-btn">
          <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0 fw-bold">Edit Profile</h1>
      </div>

      <form method="post" action="<?= e(url('profile/update')) ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
        <?= csrf_field() ?>

        <div class="d-flex align-items-center gap-4 mb-4 p-3 rounded-4" style="background: var(--sl-surface-secondary);">
          <img id="avatarPreview" class="avatar avatar-xl" src="<?= e(avatar_url($profile['profile_image'], $profile['full_name'])) ?>" alt="Current profile photo">
          <div class="flex-grow-1">
            <label for="profile_image" class="form-label fw-semibold">Profile Photo</label>
            <input type="file" class="form-control rounded-pill" id="profile_image" name="profile_image" accept="image/*"
                   data-image data-preview="#avatarPreview">
            <div class="invalid-feedback"></div>
            <div class="form-text small">Recommended: JPG, PNG or WEBP up to 2 MB.</div>
          </div>
        </div>

        <div class="mb-3">
          <label for="full_name" class="form-label fw-semibold">Full Name</label>
          <input type="text" class="form-control rounded-3" id="full_name" name="full_name" required maxlength="100"
                 value="<?= e($profile['full_name']) ?>">
          <div class="invalid-feedback">Enter your full name.</div>
        </div>

        <div class="mb-3">
          <label for="username" class="form-label fw-semibold">Username</label>
          <div class="input-group">
            <span class="input-group-text rounded-start-3 bg-secondary-subtle">@</span>
            <input type="text" class="form-control rounded-end-3 bg-secondary-subtle" id="username" value="<?= e($profile['username']) ?>" disabled>
          </div>
          <div class="form-text small">Usernames are unique and cannot be modified.</div>
        </div>

        <div class="mb-4">
          <label for="bio" class="form-label fw-semibold">Bio</label>
          <textarea class="form-control rounded-3" id="bio" name="bio" rows="3" maxlength="300"
                    placeholder="Tell your friends what you're passionate about..."
                    data-counter="#bioCount"><?= e($profile['bio']) ?></textarea>
          <div class="text-end mt-1"><small class="text-muted" id="bioCount"></small></div>
        </div>

        <div class="d-flex align-items-center gap-2 pt-2 border-top" style="border-color: var(--sl-border) !important;">
          <button class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" type="submit">
            <i class="bi bi-check-lg me-1"></i> Save Changes
          </button>
          <a class="btn btn-outline-secondary rounded-pill px-4" href="<?= e(url('profile/show/' . $profile['username'])) ?>">
            Cancel
          </a>
        </div>
      </form>
    </section>
  </div>
</div>
