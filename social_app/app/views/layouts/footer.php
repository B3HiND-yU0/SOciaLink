</main>

<!-- Mobile Bottom Navigation Bar (< 768px) -->
<?php if ($meUser): 
  $currentUrl = $_GET['url'] ?? '';
  $isFeed = ($currentUrl === '' || str_starts_with($currentUrl, 'post/index'));
  $isSearch = str_starts_with($currentUrl, 'search');
  $isProfile = str_starts_with($currentUrl, 'profile');
?>
<nav class="sl-mobile-bottom-nav">
  <a class="sl-mobile-nav-link <?= $isFeed ? 'active' : '' ?>" href="<?= e(url('post/index')) ?>" title="Feed">
    <i class="bi bi-house-door-fill"></i>
  </a>
  <a class="sl-mobile-nav-link <?= $isSearch ? 'active' : '' ?>" href="<?= e(url('search/index')) ?>" title="Search">
    <i class="bi bi-search"></i>
  </a>
  <a class="sl-mobile-nav-link create-btn-mob" href="<?= e(url('post/index')) ?>#postComposer" title="Create Post">
    <i class="bi bi-plus-circle-fill"></i>
  </a>
  <a class="sl-mobile-nav-link" href="<?= e(url('search/index')) ?>" title="Friends">
    <i class="bi bi-people-fill"></i>
  </a>
  <a class="sl-mobile-nav-link <?= $isProfile ? 'active' : '' ?>" href="<?= e(url('profile/show/' . $meUser['username'])) ?>" title="Profile">
    <img src="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>" class="avatar avatar-xs" alt="">
  </a>
</nav>
<?php endif; ?>

<!-- Floating Messenger Chat Dock (Facebook Style) -->
<div class="sl-chat-dock" id="slChatDock">
  <div class="sl-chat-header">
    <div class="d-flex align-items-center gap-2">
      <div class="sl-online-badge-wrapper">
        <img id="slChatTargetAvatar" src="" class="avatar avatar-xs" alt="">
        <span class="sl-online-dot"></span>
      </div>
      <div>
        <div class="fw-bold small text-truncate" id="slChatTargetName" style="max-width: 180px;">Messenger</div>
        <div class="text-success small" style="font-size: 0.7rem;">Active now</div>
      </div>
    </div>
    <div class="d-flex gap-1">
      <button type="button" class="btn btn-sm btn-link text-muted p-0 me-1" id="slChatClose" aria-label="Close chat">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </div>
  <div class="sl-chat-body" id="slChatBody">
    <div class="sl-chat-msg incoming">
      Hi! Connect and chat anytime on SOciaLink!
    </div>
  </div>
  <form class="sl-chat-footer m-0" id="slChatForm">
    <input type="text" class="form-control form-control-sm rounded-pill" id="slChatInput" placeholder="Aa" autocomplete="off">
    <button type="submit" class="btn btn-primary btn-sm rounded-circle p-1" style="width: 30px; height: 30px;">
      <i class="bi bi-send-fill" style="font-size: 0.8rem;"></i>
    </button>
  </form>
</div>

<!-- Create Story Modal (Facebook & MeWe Style) -->
<?php if ($meUser): ?>
<div class="modal fade sl-modal-backdrop" id="slCreateStoryModal" tabindex="-1" aria-labelledby="createStoryTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
    <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden" style="background: var(--sl-surface);">
      
      <div class="modal-header border-bottom p-3" style="border-color: var(--sl-border) !important;">
        <h5 class="modal-title fw-bold h6 mb-0" id="createStoryTitle">
          <i class="bi bi-plus-circle-fill text-primary me-2"></i> Create Your Story
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="post" action="<?= e(url('story/store')) ?>" enctype="multipart/form-data" class="needs-validation" novalidate id="createStoryForm">
        <?= csrf_field() ?>
        <input type="hidden" name="bg_gradient" id="storyBgInput" value="ocean-blue">

        <div class="modal-body p-4">
          <!-- Live Story Preview Card -->
          <div class="sl-story-creator-preview mb-4 bg-grad-ocean-blue" id="storyLivePreview">
            <div class="sl-story-text-preview" id="storyLiveText">
              Type something below...
            </div>
            <img id="storyLiveImage" class="w-100 h-100 object-fit-cover d-none position-absolute inset-0" alt="Story preview">
            <div class="position-absolute top-0 start-0 p-3 d-flex align-items-center gap-2 z-2">
              <img src="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>" class="avatar avatar-xs border border-2 border-white" alt="">
              <span class="text-white fw-bold small text-shadow"><?= e($meUser['full_name']) ?></span>
            </div>
          </div>

          <!-- Story Text / Caption -->
          <div class="mb-3">
            <label for="storyCaption" class="form-label fw-semibold small">Story Text / Caption</label>
            <textarea class="form-control rounded-3" id="storyCaption" name="caption" rows="2" maxlength="255"
                      placeholder="Share a quick thought, update or mood..."></textarea>
          </div>

          <!-- Color Gradient Picker -->
          <div class="mb-3">
            <label class="form-label fw-semibold small d-block mb-2">Background Gradient</label>
            <div class="d-flex align-items-center gap-2 overflow-x-auto pb-1">
              <div class="sl-gradient-circle bg-grad-ocean-blue active" data-gradient="ocean-blue" title="Ocean Blue"></div>
              <div class="sl-gradient-circle bg-grad-sunset-gradient" data-gradient="sunset-gradient" title="Sunset"></div>
              <div class="sl-gradient-circle bg-grad-fire-sunset" data-gradient="fire-sunset" title="Fire"></div>
              <div class="sl-gradient-circle bg-grad-neon-purple" data-gradient="neon-purple" title="Neon Purple"></div>
              <div class="sl-gradient-circle bg-grad-emerald-forest" data-gradient="emerald-forest" title="Emerald"></div>
              <div class="sl-gradient-circle bg-grad-midnight-dark" data-gradient="midnight-dark" title="Midnight"></div>
              <div class="sl-gradient-circle bg-grad-candy-pink" data-gradient="candy-pink" title="Candy Pink"></div>
            </div>
          </div>

          <!-- Optional Photo Upload -->
          <div class="mb-2">
            <label for="storyImageInput" class="form-label fw-semibold small">
              <i class="bi bi-camera-fill text-success me-1"></i> Or Attach a Photo <span class="text-muted fw-normal">(optional)</span>
            </label>
            <input type="file" class="form-control rounded-pill" id="storyImageInput" name="image" accept="image/*">
            <div class="form-text small">Upload an image or keep the vibrant gradient!</div>
          </div>
        </div>

        <div class="modal-footer border-top p-3" style="border-color: var(--sl-border) !important;">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm btn-sm">
            <i class="bi bi-send-fill me-1"></i> Share to Story
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
<?php endif; ?>

<!-- Story Viewer Modal -->
<div class="modal fade sl-modal-backdrop" id="slStoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
    <div class="modal-content border-0 rounded-4 overflow-hidden bg-black text-white position-relative shadow-lg">
      <!-- Progress Bar -->
      <div class="progress" style="height: 3px; background: rgba(255,255,255,0.25);">
        <div class="progress-bar bg-white progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
      </div>
      
      <!-- Top header -->
      <div class="d-flex align-items-center justify-content-between p-3 position-absolute top-0 start-0 end-0 z-3" style="background: linear-gradient(180deg, rgba(0,0,0,0.7) 0%, transparent 100%);">
        <div class="d-flex align-items-center gap-2">
          <img id="slStoryAuthorAvatar" src="" class="avatar avatar-sm border border-2 border-primary" alt="">
          <span class="fw-bold small text-white" id="slStoryAuthorName">Author</span>
          <span class="text-white-50 small" id="slStoryTime">&middot; Just now</span>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Story media / gradient container -->
      <div id="slStoryMediaContainer" class="d-flex align-items-center justify-content-center position-relative" style="min-height: 480px; max-height: 580px; background: #111;">
        <img id="slStoryImage" src="" class="w-100 h-100 object-fit-cover d-none" alt="Story view">
        <div id="slStoryGradientBg" class="w-100 h-100 d-flex align-items-center justify-content-center p-4 text-center position-absolute inset-0">
          <p id="slStoryCaption" class="h4 fw-bold text-white mb-0 text-shadow"></p>
        </div>
      </div>

      <!-- Quick reply -->
      <div class="p-3 position-absolute bottom-0 start-0 end-0 z-3" style="background: linear-gradient(0deg, rgba(0,0,0,0.8) 0%, transparent 100%);">
        <div class="input-group">
          <input type="text" class="form-control form-control-sm rounded-pill bg-dark border-secondary text-white" placeholder="Reply to story...">
          <button class="btn btn-outline-light btn-sm rounded-pill ms-2" type="button" onclick="slToast('Reply sent! ❤️', 'bi-heart-fill')"><i class="bi bi-heart-fill text-danger"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Photo Lightbox Modal -->
<div class="modal fade sl-modal-backdrop" id="slLightboxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 bg-transparent text-center position-relative">
      <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body p-0 d-flex justify-content-center align-items-center">
        <img id="slLightboxImage" class="sl-lightbox-img" src="" alt="Enlarged photo preview">
      </div>
    </div>
  </div>
</div>

<footer class="text-center py-4 text-muted small mt-auto border-top" style="border-color: var(--sl-border) !important;">
  <div class="container">
    <p class="mb-1 fw-semibold">
      <span class="brand-font">SOcia<span class="text-primary">Link</span></span> &middot; Privacy-First Next-Gen Social Network
    </p>
    <p class="mb-0 text-secondary" style="font-size: 0.8rem;">
      Inspired by Facebook &amp; MeWe &middot; Fast &middot; Responsive &middot; Animated &middot; PHP &amp; MySQL
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  window.SOCIALINK = { csrf: <?= json_encode(csrf_token()) ?> };
  window.BARKADA = window.SOCIALINK; // Backwards-compatible
</script>
<script src="<?= e(asset('assets/js/app.js')) ?>?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/app.js') ?>"></script>
</body>
</html>
