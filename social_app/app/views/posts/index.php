<div class="row g-4">

  <!-- ========================================================================
       LEFT SIDEBAR: Navigation & MeWe Privacy Shield (Desktop/Tablet)
       ======================================================================== -->
  <aside class="col-xl-3 col-lg-3 d-none d-lg-block">
    <div class="sl-sidebar-left">

      <!-- User Mini Profile Card -->
      <div class="sl-user-card mb-3">
        <a href="<?= e(url('profile/show/' . $meUser['username'])) ?>" class="d-flex align-items-center gap-3 text-decoration-none">
          <div class="avatar-ring">
            <img class="avatar avatar-md" src="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>" alt="">
          </div>
          <div class="min-w-0">
            <div class="fw-bold text-truncate text-body"><?= e($meUser['full_name']) ?></div>
            <div class="small text-muted">@<?= e($meUser['username']) ?></div>
          </div>
        </a>
      </div>

      <!-- Left Shortcuts Navigation Menu -->
      <ul class="sl-side-nav-list">
        <li class="sl-side-nav-item">
          <a class="sl-side-nav-link active" href="<?= e(url('post/index')) ?>">
            <span class="sl-nav-icon-badge" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
              <i class="bi bi-newspaper"></i>
            </span>
            <span>News Feed</span>
          </a>
        </li>
        <li class="sl-side-nav-item">
          <a class="sl-side-nav-link" href="<?= e(url('search/index')) ?>">
            <span class="sl-nav-icon-badge" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
              <i class="bi bi-people-fill"></i>
            </span>
            <span>Friends &amp; Network</span>
          </a>
        </li>
        <li class="sl-side-nav-item">
          <a class="sl-side-nav-link" href="#" data-chat-user="Community Chat" data-chat-avatar="<?= e(avatar_url(null, 'Community')) ?>">
            <span class="sl-nav-icon-badge" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
              <i class="bi bi-chat-quote-fill"></i>
            </span>
            <span>Messages &amp; Chat</span>
          </a>
        </li>
        <li class="sl-side-nav-item">
          <a class="sl-side-nav-link" href="<?= e(url('profile/show/' . $meUser['username'])) ?>">
            <span class="sl-nav-icon-badge" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
              <i class="bi bi-bookmark-star-fill"></i>
            </span>
            <span>Saved Bookmarks</span>
          </a>
        </li>
        <li class="sl-side-nav-item">
          <a class="sl-side-nav-link" href="<?= e(url('profile/edit')) ?>">
            <span class="sl-nav-icon-badge" style="background: rgba(6, 182, 212, 0.12); color: #06b6d4;">
              <i class="bi bi-gear-fill"></i>
            </span>
            <span>Profile Settings</span>
          </a>
        </li>
      </ul>

      <!-- MeWe Signature Element: Privacy Guarantee Card -->
      <div class="sl-privacy-shield mb-3">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="sl-privacy-shield-icon"><i class="bi bi-shield-check"></i></span>
          <span class="fw-bold" style="font-size: 0.88rem;">Privacy By Design</span>
        </div>
        <p class="mb-0 text-muted" style="font-size: 0.78rem; line-height: 1.4;">
          No tracking. No algorithmic feed manipulation. You control what you share with your circle.
        </p>
      </div>

      <!-- Quick footer info -->
      <div class="px-2 text-muted" style="font-size: 0.74rem;">
        <span>SOciaLink &copy; <?= date('Y') ?> &middot; Privacy &middot; Terms &middot; Cookies</span>
      </div>

    </div>
  </aside>

  <!-- ========================================================================
       CENTER STREAM: Stories, Post Composer, Feed Filters & Posts
       ======================================================================== -->
  <main class="col-xl-6 col-lg-9 col-12">
    <div class="sl-center-feed">

      <!-- Stories Tray (Facebook & MeWe Hybrid) -->
      <section class="sl-stories-wrapper" aria-label="Stories and Reels">
        <div class="sl-stories-tray">

          <!-- 1. Add Story Card (Opens Create Story Modal) -->
          <div class="sl-story-card sl-create-story-card" data-bs-toggle="modal" data-bs-target="#slCreateStoryModal" role="button" title="Create a new story">
            <div class="sl-create-story-top">
              <img src="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>" alt="">
              <div class="sl-create-story-btn">
                <i class="bi bi-plus-lg"></i>
              </div>
            </div>
            <div class="sl-create-story-bottom">
              Create Story
            </div>
          </div>

          <!-- 2. Dynamic Stories from Database -->
          <?php if (!empty($stories)): ?>
            <?php foreach ($stories as $st): 
              $hasImage = !empty($st['image']) && is_file(__DIR__ . '/../../../public/uploads/' . $st['image']);
              $imgUrl   = $hasImage ? asset('uploads/' . rawurlencode($st['image'])) : '';
              $gradClass = 'bg-grad-' . ($st['bg_gradient'] ?: 'ocean-blue');
            ?>
              <div class="sl-story-card <?= !$hasImage ? e($gradClass) : '' ?>"
                   data-story-author="<?= e($st['full_name']) ?>"
                   data-story-avatar="<?= e(avatar_url($st['profile_image'], $st['full_name'])) ?>"
                   data-story-caption="<?= e($st['caption'] ?? '') ?>"
                   data-story-img="<?= e($imgUrl) ?>"
                   data-story-gradient="<?= e($gradClass) ?>"
                   data-story-time="<?= e(time_ago($st['created_at'])) ?>">
                
                <?php if ($hasImage): ?>
                  <img class="sl-story-bg" src="<?= e($imgUrl) ?>" alt="" loading="lazy">
                  <div class="sl-story-gradient"></div>
                <?php else: ?>
                  <div class="sl-story-text-preview">
                    <?= e(mb_strimwidth($st['caption'] ?? '', 0, 75, '...')) ?>
                  </div>
                <?php endif; ?>

                <img class="sl-story-avatar" src="<?= e(avatar_url($st['profile_image'], $st['full_name'])) ?>" alt="">
                <span class="sl-story-author"><?= e($st['full_name']) ?></span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      </section>

      <!-- Create Post Composer (Facebook + MeWe Fusion) -->
      <section class="sl-card sl-composer-card" id="postComposer">
        <form method="post" action="<?= e(url('post/store')) ?>" enctype="multipart/form-data" class="needs-validation" novalidate id="newPostForm">
          <?= csrf_field() ?>
          
          <div class="sl-composer-top">
            <img class="avatar avatar-md" src="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>" alt="">
            <div class="flex-grow-1">
              <label for="content" class="visually-hidden">What's on your mind?</label>
              <textarea class="form-control sl-composer-textarea" id="content" name="content" rows="3" maxlength="1000" required
                        placeholder="What's on your mind, <?= e(explode(' ', $meUser['full_name'])[0]) ?>?"
                        data-counter="#contentCount"><?= old('content') ?></textarea>
              <div class="invalid-feedback">Write something before sharing.</div>
            </div>
          </div>

          <!-- Hidden Image File Picker -->
          <input type="file" id="postImageInput" class="d-none" name="image" accept="image/*" data-image data-preview="#newPreview">
          
          <!-- Image upload live preview -->
          <div class="sl-image-preview-wrapper d-none ms-md-5">
            <img id="newPreview" class="sl-preview-img d-none" alt="Preview of chosen image">
            <button type="button" class="sl-preview-close" data-clear-target="#postImageInput" aria-label="Remove image">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>

          <!-- Composer action bar with colorful pills -->
          <div class="sl-composer-actions flex-wrap gap-2">
            <div class="d-flex align-items-center gap-1">
              <button type="button" class="sl-composer-pill photo-pill" onclick="document.getElementById('postImageInput').click();">
                <i class="bi bi-image"></i>
                <span class="d-none d-sm-inline">Photo/Video</span>
              </button>
              <button type="button" class="sl-composer-pill feeling-pill" onclick="document.getElementById('content').value += ' 😊'; slToast('Added feeling 😊', 'bi-emoji-smile');">
                <i class="bi bi-emoji-smile"></i>
                <span class="d-none d-sm-inline">Feeling</span>
              </button>
              <button type="button" class="sl-composer-pill tag-pill" onclick="document.getElementById('content').value += ' @'; document.getElementById('content').focus();">
                <i class="bi bi-person-plus"></i>
                <span class="d-none d-sm-inline">Tag</span>
              </button>
            </div>

            <div class="d-flex align-items-center gap-2 ms-auto">
              <small class="text-muted" id="contentCount"></small>
              <button class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" type="submit">
                <i class="bi bi-send-fill me-1"></i> Post
              </button>
            </div>
          </div>
        </form>
      </section>

      <!-- MeWe Feed Filter Tabs -->
      <div class="sl-feed-filters">
        <button type="button" class="sl-filter-btn active" data-filter="all">
          <i class="bi bi-fire me-1"></i> All Posts
        </button>
        <button type="button" class="sl-filter-btn" data-filter="media">
          <i class="bi bi-camera-fill me-1"></i> Photos &amp; Media
        </button>
        <button type="button" class="sl-filter-btn" data-filter="text">
          <i class="bi bi-chat-left-text-fill me-1"></i> Discussions
        </button>
      </div>

      <!-- Feed Stream -->
      <?php if (!$posts): ?>
        <div class="sl-card p-5 text-center my-4">
          <div class="mb-3 text-primary display-4"><i class="bi bi-chat-heart"></i></div>
          <h2 class="h5 fw-bold">Your feed is waiting for memories</h2>
          <p class="text-muted mb-3">Be the first to share an update, thought, or photo with your friends!</p>
          <button class="btn btn-primary rounded-pill px-4" onclick="document.getElementById('content').focus();">
            Create First Post
          </button>
        </div>
      <?php endif; ?>

      <?php foreach ($posts as $post): 
        include __DIR__ . '/../partials/post_card.php'; 
      endforeach; ?>

      <!-- Pagination (Facebook style) -->
      <?php if ($pages > 1): ?>
        <nav aria-label="Feed pages" class="my-4">
          <ul class="pagination justify-content-center gap-2">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link rounded-pill px-3 border-0 shadow-sm" href="<?= e(url('post/index') . '&page=' . ($page - 1)) ?>">
                <i class="bi bi-chevron-left me-1"></i> Newer
              </a>
            </li>
            <li class="page-item disabled">
              <span class="page-link rounded-pill px-3 border-0 bg-transparent text-muted">
                Page <?= (int) $page ?> of <?= (int) $pages ?>
              </span>
            </li>
            <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
              <a class="page-link rounded-pill px-3 border-0 shadow-sm" href="<?= e(url('post/index') . '&page=' . ($page + 1)) ?>">
                Older <i class="bi bi-chevron-right ms-1"></i>
              </a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>

    </div>
  </main>

  <!-- ========================================================================
       RIGHT SIDEBAR: Active Contacts, Suggestions & Trends (Desktop)
       ======================================================================== -->
  <aside class="col-xl-3 d-none d-xl-block">
    <div class="sl-sidebar-right">

      <!-- Community Announcement Card -->
      <div class="sl-card sl-right-widget">
        <div class="d-flex align-items-center gap-2 mb-2 text-primary">
          <i class="bi bi-stars fs-5"></i>
          <span class="fw-bold small">Welcome to SOciaLink</span>
        </div>
        <p class="small text-muted mb-2">
          Experience privacy-first networking with fluid animations, real-time reactions, and interactive chat.
        </p>
      </div>

      <!-- Suggested Friends (People You May Know) -->
      <?php if (!empty($suggestedUsers)): ?>
      <div class="sl-card sl-right-widget">
        <div class="sl-widget-title">
          <span>Suggested for you</span>
          <a href="<?= e(url('search/index')) ?>" class="small text-primary text-decoration-none">See all</a>
        </div>
        <div>
          <?php foreach ($suggestedUsers as $sUser): ?>
            <div class="sl-suggestion-row">
              <a href="<?= e(url('profile/show/' . $sUser['username'])) ?>" class="text-decoration-none">
                <img src="<?= e(avatar_url($sUser['profile_image'], $sUser['full_name'])) ?>" class="avatar avatar-sm" alt="">
              </a>
              <div class="min-w-0 flex-grow-1">
                <a href="<?= e(url('profile/show/' . $sUser['username'])) ?>" class="fw-bold small text-body text-decoration-none d-block text-truncate">
                  <?= e($sUser['full_name']) ?>
                </a>
                <div class="text-muted small text-truncate" style="font-size: 0.76rem;">@<?= e($sUser['username']) ?></div>
              </div>
              <a href="<?= e(url('profile/show/' . $sUser['username'])) ?>" class="sl-btn-connect text-decoration-none">
                Connect
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Active Contacts (Online presence) -->
      <?php if (!empty($activeContacts)): ?>
      <div class="sl-card sl-right-widget">
        <div class="sl-widget-title">
          <span>Active Now</span>
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.72rem;">Online</span>
        </div>
        <div>
          <?php foreach ($activeContacts as $contact): ?>
            <div class="sl-contact-row" data-chat-user="<?= e($contact['full_name']) ?>"
                 data-chat-avatar="<?= e(avatar_url($contact['profile_image'], $contact['full_name'])) ?>">
              <div class="sl-online-badge-wrapper">
                <img src="<?= e(avatar_url($contact['profile_image'], $contact['full_name'])) ?>" class="avatar avatar-sm" alt="">
                <span class="sl-online-dot"></span>
              </div>
              <div class="min-w-0 flex-grow-1">
                <div class="fw-semibold small text-truncate"><?= e($contact['full_name']) ?></div>
              </div>
              <i class="bi bi-chat-fill text-muted small"></i>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Trending Hashtags -->
      <div class="sl-card sl-right-widget">
        <div class="sl-widget-title">
          <span>Trending on SOciaLink</span>
        </div>
        <div class="d-flex flex-column gap-1">
          <a href="<?= e(url('search/index') . '&q=SOciaLink') ?>" class="sl-trend-tag">
            <span class="fw-bold text-primary">#SOciaLink</span>
            <span class="text-muted small">1.2k posts</span>
          </a>
          <a href="<?= e(url('search/index') . '&q=Privacy') ?>" class="sl-trend-tag">
            <span class="fw-bold text-primary">#PrivacyFirst</span>
            <span class="text-muted small">850 posts</span>
          </a>
          <a href="<?= e(url('search/index') . '&q=WebDev') ?>" class="sl-trend-tag">
            <span class="fw-bold text-primary">#WebSystems</span>
            <span class="text-muted small">640 posts</span>
          </a>
          <a href="<?= e(url('search/index') . '&q=PHP') ?>" class="sl-trend-tag">
            <span class="fw-bold text-primary">#PHP8</span>
            <span class="text-muted small">490 posts</span>
          </a>
        </div>
      </div>

    </div>
  </aside>

</div>
