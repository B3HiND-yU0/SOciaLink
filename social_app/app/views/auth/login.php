<div class="sl-auth-wrapper">
  <div class="sl-auth-card">
    
    <!-- Brand Header -->
    <div class="text-center mb-4">
      <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-4 mb-2 shadow-sm" style="background: var(--sl-gradient-brand); color: white;">
        <i class="bi bi-link-45deg display-6"></i>
      </div>
      <h1 class="h3 fw-bold mt-2 mb-1">
        Welcome to <span class="brand-font">SOcia<span class="text-primary">Link</span></span>
      </h1>
      <p class="text-muted small">Connect, share, and express yourself without boundaries.</p>
    </div>

    <!-- Login Form -->
    <form method="post" action="<?= e(url('auth/login')) ?>" class="needs-validation" novalidate id="loginForm">
      <?= csrf_field() ?>
      
      <div class="mb-3">
        <label for="username" class="form-label fw-semibold small">Username</label>
        <div class="input-group">
          <span class="input-group-text bg-body-secondary border-end-0 rounded-start-3"><i class="bi bi-person text-muted"></i></span>
          <input type="text" class="form-control border-start-0 rounded-end-3 py-2" id="username" name="username" required maxlength="30"
                 placeholder="e.g. maria" autocomplete="username" value="<?= old('username') ?>" autofocus>
        </div>
        <div class="invalid-feedback">Enter your username.</div>
      </div>

      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
          <label for="password" class="form-label fw-semibold small mb-1">Password</label>
          <a href="#" onclick="slToast('Demo password is password123', 'bi-key-fill'); return false;" class="small text-primary text-decoration-none">Forgot?</a>
        </div>
        <div class="input-group">
          <span class="input-group-text bg-body-secondary border-end-0 rounded-start-3"><i class="bi bi-lock text-muted"></i></span>
          <input type="password" class="form-control border-start-0 rounded-end-3 py-2" id="password" name="password" required
                 placeholder="Enter password" autocomplete="current-password">
        </div>
        <div class="invalid-feedback">Enter your password.</div>
      </div>

      <button class="btn btn-primary w-100 py-2 rounded-pill fw-semibold shadow-sm mt-2" type="submit">
        <i class="bi bi-box-arrow-in-right me-1"></i> Log in to SOciaLink
      </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--sl-border) !important;">
      <p class="mb-2 text-muted small">Don't have an account yet?</p>
      <a href="<?= e(url('auth/register')) ?>" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">
        Create new account
      </a>
    </div>

    <!-- Quick Demo Logins -->
    <div class="mt-4 p-3 rounded-4 bg-body-secondary text-center small">
      <div class="fw-semibold text-muted mb-2">⚡ Quick Demo Accounts (pass: <code>password123</code>):</div>
      <div class="d-flex justify-content-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2"
                onclick="document.getElementById('username').value='maria'; document.getElementById('password').value='password123';">
          maria
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2"
                onclick="document.getElementById('username').value='juan'; document.getElementById('password').value='password123';">
          juan
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2"
                onclick="document.getElementById('username').value='ana'; document.getElementById('password').value='password123';">
          ana
        </button>
      </div>
    </div>

  </div>
</div>
