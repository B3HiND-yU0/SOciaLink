<?php
/**
 * One post + its comments. Expects: $post (row), $comments (grouped by post id).
 * Uses $meUser (current user) from the base controller.
 */
$postId       = (int) $post['id'];
$isOwner      = (int) $post['user_id'] === (int) $meUser['id'];
$postComments = $comments[$postId] ?? [];
$postUrl      = url('post/index') . '#post-' . $postId;

// Hashtag formatting helper
$formattedContent = e($post['content']);
$formattedContent = preg_replace('/#(\w+)/u', '<a href="' . e(url('search/index')) . '&q=%23$1" class="sl-hashtag">#$1</a>', $formattedContent);
?>
<article class="sl-card sl-post-card" id="post-<?= $postId ?>">
  
  <!-- Post Author Header -->
  <header class="sl-post-header">
    <a href="<?= e(url('profile/show/' . $post['username'])) ?>" class="position-relative">
      <img class="avatar avatar-md" src="<?= e(avatar_url($post['profile_image'], $post['full_name'])) ?>" alt="">
      <span class="sl-online-dot"></span>
    </a>
    
    <div class="flex-grow-1 min-w-0">
      <div class="d-flex align-items-center gap-1">
        <a class="sl-author-name text-truncate text-decoration-none" href="<?= e(url('profile/show/' . $post['username'])) ?>">
          <?= e($post['full_name']) ?>
        </a>
        <i class="bi bi-patch-check-fill sl-verified-badge" title="Verified Member"></i>
      </div>
      <div class="sl-post-time">
        <span>@<?= e($post['username']) ?></span>
        <span>&middot;</span>
        <time datetime="<?= e($post['created_at']) ?>" title="<?= e($post['created_at']) ?>">
          <?= e(time_ago($post['created_at'])) ?>
        </time>
        <span>&middot;</span>
        <i class="bi bi-globe-americas" title="Public"></i>
      </div>
    </div>

    <!-- Post Options Dropdown -->
    <div class="dropdown">
      <button class="btn sl-icon-btn border-0" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Post actions">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2">
        <li>
          <button type="button" class="dropdown-item rounded-3 py-2" data-copy-link="<?= e($postUrl) ?>">
            <i class="bi bi-link-45deg me-2 text-primary"></i> Copy post link
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item rounded-3 py-2" data-save-post data-saved="false">
            <i class="bi bi-bookmark me-2 text-warning"></i> Save post
          </button>
        </li>
        <?php if ($isOwner): ?>
          <li><hr class="dropdown-divider my-2"></li>
          <li>
            <a class="dropdown-item rounded-3 py-2" href="<?= e(url('post/edit/' . $postId)) ?>">
              <i class="bi bi-pencil me-2 text-info"></i> Edit post
            </a>
          </li>
          <li>
            <form method="post" action="<?= e(url('post/delete/' . $postId)) ?>" data-confirm="Are you sure you want to delete this post?">
              <?= csrf_field() ?>
              <button class="dropdown-item rounded-3 py-2 text-danger" type="submit">
                <i class="bi bi-trash me-2"></i> Delete post
              </button>
            </form>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </header>

  <!-- Post Content Body -->
  <div class="sl-post-content"><?= $formattedContent ?></div>

  <!-- Post Image with Lightbox Trigger -->
  <?php if ($post['image'] && is_file(__DIR__ . '/../../../public/uploads/' . $post['image'])): ?>
    <div class="sl-post-media">
      <img src="<?= e(asset('uploads/' . rawurlencode($post['image']))) ?>" 
           alt="Post image by <?= e($post['full_name']) ?>" 
           loading="lazy">
    </div>
  <?php endif; ?>

  <?php
  $userReaction = $post['user_reaction'] ?? ($post['liked'] ? 'like' : null);
  $likeCount = (int) $post['like_count'];
  $reactionIcons = [
      'love' => '❤️',
      'haha' => '😂',
      'wow'  => '😮',
      'sad'  => '😢',
      'fire' => '🔥',
      'like' => '👍',
  ];
  ?>

  <!-- Engagement Stats Bar -->
  <div class="sl-stats-bar">
    <div class="sl-reaction-stack <?= $likeCount === 0 ? 'd-none' : '' ?>">
      <span class="sl-reaction-badges">
        <?php if ($userReaction && isset($reactionIcons[$userReaction])): ?>
          <span class="sl-emoji-badge" style="background: <?= $userReaction === 'love' || $userReaction === 'fire' ? '#ef4444' : ($userReaction === 'like' ? '#2563eb' : '#f59e0b') ?>; color: white;">
            <?= $reactionIcons[$userReaction] ?>
          </span>
        <?php else: ?>
          <span class="sl-emoji-badge" style="background: #2563eb; color: white;">👍</span>
          <span class="sl-emoji-badge" style="background: #ef4444; color: white;">❤️</span>
        <?php endif; ?>
      </span>
      <span class="ms-1 fw-semibold like-count"><?= $likeCount ?></span>
    </div>
    <div class="small text-muted fst-italic no-reactions-msg <?= $likeCount > 0 ? 'd-none' : '' ?>">
      Be the first to react
    </div>
    <div class="d-flex align-items-center gap-3 ms-auto">
      <span><?= count($postComments) ?> comment<?= count($postComments) === 1 ? '' : 's' ?></span>
      <span>1 share</span>
    </div>
  </div>

  <!-- Interactive Action Buttons Bar (Facebook / MeWe Reactions) -->
  <div class="sl-actions-bar">
    
    <!-- Like & Reaction Tray -->
    <div class="sl-reaction-tray-wrapper">
      <div class="sl-reaction-tray">
        <span class="sl-tray-emoji" role="button" data-type="like" title="Like">👍</span>
        <span class="sl-tray-emoji" role="button" data-type="love" title="Love">❤️</span>
        <span class="sl-tray-emoji" role="button" data-type="haha" title="Haha">😂</span>
        <span class="sl-tray-emoji" role="button" data-type="wow" title="Wow">😮</span>
        <span class="sl-tray-emoji" role="button" data-type="sad" title="Sad">😢</span>
        <span class="sl-tray-emoji" role="button" data-type="fire" title="Fire">🔥</span>
      </div>

      <form class="like-form w-100 m-0" method="post" action="<?= e(url('post/like/' . $postId)) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="reaction_type" value="<?= e($userReaction ?: 'like') ?>">
        <button type="submit" class="sl-action-btn like-btn w-100 <?= $userReaction ? 'reacted ' . e($userReaction) : '' ?>"
                aria-pressed="<?= $userReaction ? 'true' : 'false' ?>">
          <span class="btn-reaction-icon">
            <?php if ($userReaction && isset($reactionIcons[$userReaction])): ?>
              <?= $reactionIcons[$userReaction] ?>
            <?php else: ?>
              <i class="bi bi-hand-thumbs-up"></i>
            <?php endif; ?>
          </span>
          <span class="btn-reaction-label fw-semibold">
            <?= $userReaction ? ucfirst($userReaction) : 'Like' ?>
          </span>
        </button>
      </form>
    </div>

    <!-- Comment Button -->
    <button type="button" class="sl-action-btn" data-focus-comment="<?= $postId ?>">
      <i class="bi bi-chat-left"></i>
      <span>Comment</span>
    </button>

    <!-- Share Button -->
    <button type="button" class="sl-action-btn" data-copy-link="<?= e($postUrl) ?>">
      <i class="bi bi-share"></i>
      <span>Share</span>
    </button>
  </div>

  <!-- Comments Thread (Bubble Style) -->
  <div class="sl-comments-container">
    <?php foreach ($postComments as $c):
      $cid  = (int) $c['id'];
      $mine = (int) $c['user_id'] === (int) $meUser['id'];
    ?>
      <div class="sl-comment-item" id="comment-<?= $cid ?>">
        <a href="<?= e(url('profile/show/' . $c['username'])) ?>">
          <img class="avatar avatar-sm" src="<?= e(avatar_url($c['profile_image'], $c['full_name'])) ?>" alt="">
        </a>

        <div class="flex-grow-1 min-w-0" id="cbox-<?= $cid ?>">
          <div class="comment-view">
            <div class="sl-comment-bubble position-relative">
              <a class="sl-comment-author" href="<?= e(url('profile/show/' . $c['username'])) ?>">
                <?= e($c['full_name']) ?>
              </a>
              <div class="sl-comment-text"><?= nl2br(e($c['content'])) ?></div>

              <?php $cLikes = (int) ($c['like_count'] ?? 0); ?>
              <span class="sl-comment-like-pill <?= $cLikes > 0 ? '' : 'd-none' ?>" 
                    id="comment-pill-<?= $cid ?>" 
                    data-comment-like="<?= $cid ?>"
                    title="<?= $cLikes ?> like<?= $cLikes === 1 ? '' : 's' ?>"
                    role="button"
                    tabindex="0">
                <span class="sl-comment-pill-icon"><i class="bi bi-heart-fill"></i></span>
                <span class="sl-comment-pill-count count-num"><?= $cLikes ?></span>
              </span>
            </div>
            
            <div class="sl-comment-meta">
              <span><?= e(time_ago($c['created_at'])) ?></span>
              <?php $cLiked = !empty($c['user_liked']); ?>
              <form method="post" action="<?= e(url('comment/like/' . $cid)) ?>" class="d-inline m-0 comment-like-form">
                <?= csrf_field() ?>
                <button type="submit" 
                        class="sl-comment-action-link sl-comment-like-btn <?= $cLiked ? 'liked' : '' ?>" 
                        data-comment-like="<?= $cid ?>"
                        data-url="<?= e(url('comment/like/' . $cid)) ?>"
                        aria-pressed="<?= $cLiked ? 'true' : 'false' ?>">
                  <span class="like-label"><?= $cLiked ? 'Liked' : 'Like' ?></span>
                </button>
              </form>
              <?php if ($mine): ?>
                <button type="button" class="sl-comment-action-link" data-toggle-edit="cbox-<?= $cid ?>">Edit</button>
                <form method="post" action="<?= e(url('comment/delete/' . $cid)) ?>" data-confirm="Delete this comment?" class="d-inline m-0">
                  <?= csrf_field() ?>
                  <button type="submit" class="sl-comment-action-link text-danger">Delete</button>
                </form>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($mine): ?>
            <!-- Inline Edit Comment Form -->
            <form class="comment-edit d-none needs-validation mt-1" novalidate method="post" action="<?= e(url('comment/update/' . $cid)) ?>">
              <?= csrf_field() ?>
              <textarea class="form-control form-control-sm mb-1 rounded-3" name="content" rows="2" maxlength="1000" required><?= e($c['content']) ?></textarea>
              <div class="invalid-feedback">Comment cannot be empty.</div>
              <div class="d-flex gap-2">
                <button class="btn btn-primary btn-sm rounded-pill px-3" type="submit">Save</button>
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" type="button" data-toggle-edit="cbox-<?= $cid ?>">Cancel</button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <!-- Add New Comment Form -->
    <form class="sl-comment-input-row needs-validation" novalidate method="post" action="<?= e(url('comment/store/' . $postId)) ?>">
      <?= csrf_field() ?>
      <img class="avatar avatar-sm" src="<?= e(avatar_url($meUser['profile_image'], $meUser['full_name'])) ?>" alt="">
      <div class="sl-comment-input-wrapper">
        <input type="text" class="sl-comment-input" name="content" maxlength="1000" required
               placeholder="Write a comment..." aria-label="Write a comment">
        <button class="sl-comment-submit-btn" type="submit" aria-label="Send comment">
          <i class="bi bi-arrow-up"></i>
        </button>
      </div>
    </form>
  </div>

</article>
