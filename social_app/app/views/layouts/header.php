<?php
/** Layout header: <head>, navbar, flash messages. Variables: $title, $meUser, $suggestedUsers, $activeContacts */
$currentUrl = $_GET['url'] ?? '';
$isFeed = ($currentUrl === '' || str_starts_with($currentUrl, 'post/index'));
$isSearch = str_starts_with($currentUrl, 'search');
$isProfile = str_starts_with($currentUrl, 'profile');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <title><?= e(($title ?? 'Feed') . ' | SOciaLink') ?></title>

  <script>
    window.BASE_URL = <?= json_encode(BASE_URL) ?>;
    window.CSRF_TOKEN = <?= json_encode(csrf_token()) ?>;
    // Apply saved theme before paint to avoid flash of light mode
    try {
      const savedTheme = localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-bs-theme', savedTheme);
    } catch (e) {}
  </script>

  <!-- Google Fonts: Plus Jakarta Sans & Bricolage Grotesque -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5.3.3 & Bootstrap Icons 1.11.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- SOciaLink Custom Theme -->
  <link href="<?= e(asset('assets/css/style.css')) ?>?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

<!-- Top Navigation Bar (Facebook & MeWe Hybrid) -->
<header class="navbar sl-navbar sticky-top">
  <div class="container-fluid sl-nav-container px-3 px-md-4">
    
    <!-- Left Section: Brand Logo & Search -->
    <div class="d-flex align-items-center gap-3">
      <a class="sl-brand" href="<?= e(url($meUser ? 'post/index' : 'auth/login')) ?>" title="SOciaLink Home">
        <span class="sl-brand-icon"><i class="bi bi-link-45deg"></i></span>
        <span class="brand-font">SOcia<span class="sl-brand-highlight">Link</span></span>
      </a>

      <?php if ($meUser): ?>
      <form class="sl-search-box d-none d-md-block" role="search" method="get" action="<?= e(url()) ?>">
        <input type="hidden" name="url" value="search/index">
        <i class="bi bi-search sl-search-icon"></i>
        <input class="form-control sl-search-input" type="search" name="q" maxlength="50"
               placeholder="Search people, posts..." aria-label="Search" value="<?= e($_GET['q'] ?? '') ?>">
      </form>
      <?php endif; ?>
    </div>

    <!-- Center Section: Facebook-Style Iconic 5 Tabs (Desktop) -->
    <?php if ($meUser): ?>
    <div class="sl-nav-tabs-center d-none d-xl-flex">
      <a class="sl-tab-link <?= $isFeed ? 'active' : '' ?>" href="<?= e(url('post/index')) ?>" title="Newsfeed" aria-label="Newsfeed">
        <i class="bi bi-house-door-fill"></i>
      </a>
      <a class="sl-tab-link <?= ($isSearch && empty($_GET['q'])) ? 'active' : '' ?>" href="<?= e(url('search/index')) ?>" title="Friends & Network" aria-label="Friends & Network">
        <i class="bi bi-people-fill"></i>
      </a>
      <a class="sl-tab-link" href="<?= e(url('post/index')) ?>" title="Communities & Groups" aria-label="Communities & Groups">
        <i class="bi bi-collection-play-fill"></i>
      </a>
      <a class="sl-tab-link" href="<?= e(url('profile/show/' . $meUser['username'])) ?>" title="My Saved Posts" aria-label="Saved Posts">
        <i class="bi bi-bookmark-fill"></i>
      </a>
      <a class="sl-tab-link" href="<?= e(url('search/index')) ?>" title="Explore Trends" aria-label="Explore Trends">
        <i class="bi bi-compass-fill"></i>
      </a>
    </div>
    <?php endif; ?>

    <!-- Right Section: Actions & User Menu -->
    <div class="sl-nav-right">
      <!-- Dark/Light Theme Toggle -->
      <button type="button" class="sl-icon-btn" id="themeToggle" aria-label="Toggle theme">
        <i class="bi bi-moon-stars-fill"></i>
      </button>

      <?php if ($meUser): ?>
        <!-- Messenger Chat Trigger -->
        <button type="button" class="sl-icon-btn" data-chat-user="<?= e($meUser['full_name']) ?>"
                data-chat-avatar="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>"
                title="Messenger" aria-label="Messages">
          <i class="bi bi-chat-dots-fill"></i>
          <span class="sl-badge-dot"></span>
        </button>

        <!-- Notifications Bell -->
        <button type="button" class="sl-icon-btn" onclick="slToast('No new notifications', 'bi-bell-fill')"
                title="Notifications" aria-label="Notifications">
          <i class="bi bi-bell-fill"></i>
        </button>

        <!-- User Profile Dropdown Menu -->
        <div class="dropdown">
          <button class="btn p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu">
            <div class="sl-user-pill">
              <img src="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>" class="avatar avatar-sm" alt="Avatar">
              <span class="d-none d-lg-inline fw-semibold font-monospace small ps-1 pe-1"><?= e($meUser['username']) ?></span>
              <i class="bi bi-chevron-down small text-muted d-none d-lg-inline"></i>
            </div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2">
            <li>
              <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?= e(url('profile/show/' . $meUser['username'])) ?>">
                <img src="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>" class="avatar avatar-sm" alt="">
                <div>
                  <div class="fw-bold text-truncate" style="max-width: 170px;"><?= e($meUser['full_name']) ?></div>
                  <div class="small text-muted">@<?= e($meUser['username']) ?></div>
                </div>
              </a>
            </li>
            <li><hr class="dropdown-divider my-2"></li>
            <li>
              <a class="dropdown-item rounded-3 py-2" href="<?= e(url('profile/edit')) ?>">
                <i class="bi bi-pencil-square me-2 text-primary"></i> Edit Profile
              </a>
            </li>
            <li>
              <a class="dropdown-item rounded-3 py-2" href="<?= e(url('search/index')) ?>">
                <i class="bi bi-search me-2 text-success"></i> Find People
              </a>
            </li>
            <li><hr class="dropdown-divider my-2"></li>
            <li>
              <form method="post" action="<?= e(url('auth/logout')) ?>" class="m-0">
                <?= csrf_field() ?>
                <button class="dropdown-item rounded-3 py-2 text-danger" type="submit">
                  <i class="bi bi-box-arrow-right me-2"></i> Log out
                </button>
              </form>
            </li>
          </ul>
        </div>
      <?php else: ?>
        <a href="<?= e(url('auth/login')) ?>" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold">Log in</a>
        <a href="<?= e(url('auth/register')) ?>" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold">Sign up</a>
      <?php endif; ?>
    </div>

  </div>
</header>

<!-- Main Container -->
<main class="sl-layout-container">
  <?php foreach (['success', 'danger', 'warning'] as $type): ?>
    <?php if ($msg = flash($type)): ?>
      <div class="alert alert-<?= e($type) ?> alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-<?= $type === 'success' ? 'check-circle-fill text-success' : ($type === 'danger' ? 'exclamation-triangle-fill text-danger' : 'info-circle-fill text-warning') ?> fs-5"></i>
          <div><?= nl2br(e($msg)) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
