<?php 
$isMe = (int) $profile['id'] === (int) $meUser['id']; 
?>
<div class="row justify-content-center">
  <div class="col-xl-8 col-lg-10 col-12">

    <!-- Profile Header Card (Facebook Style Cover + Avatar) -->
    <section class="sl-card mb-4 overflow-hidden shadow-sm">
      <div class="sl-profile-cover">
        <!-- Floating cover action badge -->
        <div class="position-absolute bottom-0 end-0 p-3">
          <span class="badge bg-dark bg-opacity-50 text-white rounded-pill px-3 py-2 small">
            <i class="bi bi-shield-check me-1 text-info"></i> Verified Profile
          </span>
        </div>
      </div>

      <div class="px-3 px-md-4 pb-4">
        <div class="sl-profile-head d-flex flex-wrap align-items-end justify-content-between gap-3">
          <div class="d-flex align-items-end gap-3 flex-wrap">
            <div class="sl-avatar-wrapper">
              <img class="avatar avatar-xl" src="<?= e(avatar_url($profile['profile_image'], $profile['full_name'])) ?>"
                   alt="Profile photo of <?= e($profile['full_name']) ?>">
              <span class="sl-online-dot" style="width: 18px; height: 18px; bottom: 8px; right: 8px; border-width: 3px;"></span>
            </div>
            <div class="pb-1">
              <h1 class="h3 mb-1 fw-bold d-flex align-items-center gap-2">
                <span><?= e($profile['full_name']) ?></span>
                <i class="bi bi-patch-check-fill text-primary fs-5" title="Verified Member"></i>
              </h1>
              <div class="text-muted">@<?= e($profile['username']) ?></div>
            </div>
          </div>

          <!-- Profile Actions -->
          <div class="d-flex align-items-center gap-2 pb-1">
            <?php if ($isMe): ?>
              <a class="btn btn-outline-primary rounded-pill px-4 fw-semibold" href="<?= e(url('profile/edit')) ?>">
                <i class="bi bi-pencil-square me-1"></i> Edit profile
              </a>
            <?php else: ?>
              <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm"
                      onclick="slToast('Connected with <?= e(addslashes($profile['full_name'])) ?>! 🎉', 'bi-person-check-fill')">
                <i class="bi bi-person-plus-fill me-1"></i> Connect
              </button>
              <button type="button" class="btn btn-outline-secondary rounded-pill px-3"
                      data-chat-user="<?= e($profile['full_name']) ?>"
                      data-chat-avatar="<?= e(avatar_url($profile['profile_image'], $profile['full_name'])) ?>">
                <i class="bi bi-chat-dots-fill"></i>
              </button>
            <?php endif; ?>
          </div>
        </div>

        <!-- Bio & Details -->
        <div class="mt-4 pt-2 border-top" style="border-color: var(--sl-border) !important;">
          <p class="sl-post-content mb-3" style="max-width: 680px;">
            <?= $profile['bio'] ? nl2br(e($profile['bio'])) : '<span class="text-muted fst-italic">No bio provided yet.</span>' ?>
          </p>

          <div class="d-flex flex-wrap align-items-center gap-4 text-muted small">
            <div><i class="bi bi-calendar3 me-1 text-primary"></i> Joined <?= e(date('F Y', strtotime($profile['created_at']))) ?></div>
            <div><i class="bi bi-grid-fill me-1 text-success"></i> <strong><?= (int) $postCount ?></strong> post<?= $postCount === 1 ? '' : 's' ?></div>
            <div><i class="bi bi-people-fill me-1 text-info"></i> <strong><?= rand(42, 128) ?></strong> connections</div>
            <div><i class="bi bi-shield-check me-1 text-warning"></i> Privacy Protected</div>
          </div>
        </div>
      </div>
    </section>

    <!-- User Posts Stream -->
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="h5 fw-bold mb-0">Timeline Posts</h2>
      <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3"><?= (int) $postCount ?> Posts</span>
    </div>

    <?php if (!$posts): ?>
      <div class="sl-card p-5 text-center text-muted mb-4">
        <div class="display-6 mb-2 text-primary"><i class="bi bi-chat-square-dots"></i></div>
        <p class="mb-0">
          <?= $isMe ? "You haven't posted yet. Share something exciting with the network!" : e($profile['full_name']) . " hasn't posted anything yet." ?>
        </p>
      </div>
    <?php endif; ?>

    <?php foreach ($posts as $post): 
      include __DIR__ . '/../partials/post_card.php'; 
    endforeach; ?>

  </div>
</div>
