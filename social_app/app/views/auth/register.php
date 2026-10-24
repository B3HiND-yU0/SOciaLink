<div class="sl-auth-wrapper">
  <div class="sl-auth-card">
    
    <!-- Brand Header -->
    <div class="text-center mb-4">
      <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-4 mb-2 shadow-sm" style="background: var(--sl-gradient-brand); color: white;">
        <i class="bi bi-link-45deg display-6"></i>
      </div>
      <h1 class="h3 fw-bold mt-2 mb-1">
        Join <span class="brand-font">SOcia<span class="text-primary">Link</span></span>
      </h1>
      <p class="text-muted small">The privacy-first social network built for authentic connection.</p>
    </div>

    <!-- Registration Form -->
    <form method="post" action="<?= e(url('auth/register')) ?>" enctype="multipart/form-data" class="needs-validation" novalidate id="registerForm">
      <?= csrf_field() ?>
      
      <div class="mb-3">
        <label for="full_name" class="form-label fw-semibold small">Full Name</label>
        <div class="input-group">
          <span class="input-group-text bg-body-secondary border-end-0 rounded-start-3"><i class="bi bi-person text-muted"></i></span>
          <input type="text" class="form-control border-start-0 rounded-end-3 py-2" id="full_name" name="full_name" required maxlength="100"
                 placeholder="e.g. Maria Santos" autocomplete="name" value="<?= old('full_name') ?>">
        </div>
        <div class="invalid-feedback">Enter your full name.</div>
      </div>

      <div class="mb-3">
        <label for="username" class="form-label fw-semibold small">Username</label>
        <div class="input-group has-validation">
          <span class="input-group-text bg-body-secondary border-end-0 rounded-start-3">@</span>
          <input type="text" class="form-control border-start-0 rounded-end-3 py-2" id="username" name="username" required
                 pattern="[A-Za-z0-9_]{3,30}" maxlength="30" placeholder="e.g. maria_s" autocomplete="username" value="<?= old('username') ?>">
          <div class="invalid-feedback">3-30 characters: letters, numbers, or underscore.</div>
        </div>
      </div>

      <div class="row g-2 mb-3">
        <div class="col-md-6">
          <label for="password" class="form-label fw-semibold small">Password</label>
          <input type="password" class="form-control rounded-3 py-2" id="password" name="password" required minlength="8"
                 placeholder="Min. 8 chars" autocomplete="new-password">
          <div class="invalid-feedback">At least 8 characters.</div>
        </div>
        <div class="col-md-6">
          <label for="confirm" class="form-label fw-semibold small">Confirm</label>
          <input type="password" class="form-control rounded-3 py-2" id="confirm" name="confirm" required
                 placeholder="Repeat password" autocomplete="new-password">
          <div class="invalid-feedback">Passwords must match.</div>
        </div>
      </div>

      <div class="mb-4">
        <label for="profile_image" class="form-label fw-semibold small">
          Profile Photo <span class="text-muted fw-normal">(optional)</span>
        </label>
        <input type="file" class="form-control rounded-pill" id="profile_image" name="profile_image" accept="image/*" data-image>
        <div class="invalid-feedback">Choose a JPG, PNG, GIF, or WEBP under 2 MB.</div>
      </div>

      <button class="btn btn-primary w-100 py-2 rounded-pill fw-semibold shadow-sm" type="submit">
        <i class="bi bi-person-check-fill me-1"></i> Create My Account
      </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--sl-border) !important;">
      <p class="mb-2 text-muted small">Already a member?</p>
      <a href="<?= e(url('auth/login')) ?>" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">
        Log in here
      </a>
    </div>

  </div>
</div>
