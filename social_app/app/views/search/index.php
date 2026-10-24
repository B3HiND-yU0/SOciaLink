<div class="row justify-content-center">
  <div class="col-xl-7 col-lg-9 col-12">

    <!-- Search Header & Bar -->
    <div class="sl-card p-4 mb-4 shadow-sm">
      <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-search text-primary fs-4"></i>
        <h1 class="h4 mb-0 fw-bold">Search SOciaLink</h1>
      </div>

      <form method="get" action="<?= e(url()) ?>" class="d-flex gap-2">
        <input type="hidden" name="url" value="search/index">
        <div class="input-group">
          <span class="input-group-text bg-body-secondary border-end-0 rounded-start-pill ps-3">
            <i class="bi bi-search text-muted"></i>
          </span>
          <input type="search" class="form-control bg-body-secondary border-start-0 rounded-end-pill py-2"
                 name="q" maxlength="50" placeholder="Search people by name, username, or post keywords..."
                 value="<?= e($q) ?>" autofocus>
        </div>
        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Search</button>
      </form>
    </div>

    <?php if ($q === ''): ?>
      <!-- Search Discovery Suggestions -->
      <div class="sl-card p-5 text-center text-muted mb-4 shadow-sm">
        <div class="display-5 text-primary mb-3"><i class="bi bi-compass"></i></div>
        <h2 class="h5 fw-bold text-body">Explore the network</h2>
        <p class="mb-4">Search for friends, colleagues, or topics like #SOciaLink, #WebDev, or #Technology.</p>
        
        <div class="d-flex flex-wrap justify-content-center gap-2">
          <a href="<?= e(url('search/index') . '&q=maria') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">@maria</a>
          <a href="<?= e(url('search/index') . '&q=juan') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">@juan</a>
          <a href="<?= e(url('search/index') . '&q=ana') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">@ana</a>
          <a href="<?= e(url('search/index') . '&q=SOciaLink') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">#SOciaLink</a>
          <a href="<?= e(url('search/index') . '&q=dark mode') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">dark mode</a>
        </div>
      </div>

    <?php else: ?>

      <!-- 1. People Results -->
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h2 class="h6 text-muted text-uppercase fw-bold letter-spacing-1 mb-0">
          People matching &ldquo;<?= e($q) ?>&rdquo; (<?= count($users) ?>)
        </h2>
      </div>

      <?php if (!$users): ?>
        <div class="sl-card p-4 mb-4 text-muted text-center">
          <i class="bi bi-person-x fs-3 d-block mb-1"></i>
          No people found matching &ldquo;<?= e($q) ?>&rdquo;. Try another name.
        </div>
      <?php else: ?>
        <div class="sl-card mb-4 overflow-hidden shadow-sm">
          <?php foreach ($users as $u): ?>
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: var(--sl-border) !important;">
              <a class="d-flex align-items-center gap-3 text-decoration-none text-body min-w-0" href="<?= e(url('profile/show/' . $u['username'])) ?>">
                <img class="avatar avatar-md" src="<?= e(avatar_url($u['profile_image'], $u['full_name'])) ?>" alt="">
                <div class="min-w-0">
                  <div class="fw-bold text-truncate"><?= e($u['full_name']) ?></div>
                  <div class="small text-muted text-truncate">
                    @<?= e($u['username']) ?><?= $u['bio'] ? ' &middot; ' . e(mb_strimwidth($u['bio'], 0, 60, '...')) : '' ?>
                  </div>
                </div>
              </a>
              <div class="d-flex gap-2 ms-2">
                <a href="<?= e(url('profile/show/' . $u['username'])) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                  View
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- 2. Posts Results -->
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h2 class="h6 text-muted text-uppercase fw-bold letter-spacing-1 mb-0">
          Posts mentioning &ldquo;<?= e($q) ?>&rdquo; (<?= count($posts) ?>)
        </h2>
      </div>

      <?php if (!$posts): ?>
        <div class="sl-card p-4 mb-4 text-muted text-center">
          <i class="bi bi-chat-left-dots fs-3 d-block mb-1"></i>
          No posts found with that keyword.
        </div>
      <?php else: ?>
        <?php foreach ($posts as $post): 
          include __DIR__ . '/../partials/post_card.php'; 
        endforeach; ?>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</div>
