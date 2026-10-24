/* ==========================================================================
   SOciaLink - Client-side interactive engine
   Reactions, Chat dock, Story viewer, Lightbox, Toasts, Validation & Dark Mode
   ========================================================================== */
(function () {
  'use strict';

  /* ---- Toast Notification Helper ---- */
  function showToast(message, icon = 'bi-check-circle-fill') {
    let container = document.getElementById('slToastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'slToastContainer';
      container.className = 'sl-toast-container';
      document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = 'sl-toast';
    toast.innerHTML = `<i class="bi ${icon} text-primary fs-5"></i><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
      toast.style.transition = 'all 0.3s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  }
  window.slToast = showToast;

  /* ---- Dark Mode Toggle ---- */
  const toggle = document.getElementById('themeToggle');
  function syncIcon() {
    if (!toggle) return;
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    toggle.innerHTML = isDark
      ? '<i class="bi bi-sun-fill text-warning"></i>'
      : '<i class="bi bi-moon-stars-fill text-primary"></i>';
    toggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
  }

  if (toggle) {
    toggle.addEventListener('click', function () {
      const current = document.documentElement.getAttribute('data-bs-theme');
      const next = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', next);
      try { localStorage.setItem('theme', next); } catch (e) {}
      syncIcon();
      showToast(next === 'dark' ? 'Dark mode enabled' : 'Light mode enabled', next === 'dark' ? 'bi-moon-fill' : 'bi-sun-fill');
    });
    syncIcon();
  }

  /* ---- Post Image Preview & Discard ---- */
  const MAX_BYTES = 2 * 1024 * 1024;
  const OK_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

  document.querySelectorAll('input[type=file][data-image]').forEach(function (input) {
    input.addEventListener('change', function () {
      const file = input.files[0];
      let msg = '';
      if (file && !OK_TYPES.includes(file.type)) {
        msg = 'Only JPG, PNG, GIF, or WEBP images are allowed.';
      } else if (file && file.size > MAX_BYTES) {
        msg = 'Image is too large. Maximum size is 2 MB.';
      }
      input.setCustomValidity(msg);
      const fb = input.parentElement.querySelector('.invalid-feedback');
      if (fb && msg) fb.textContent = msg;

      const previewId = input.getAttribute('data-preview');
      const preview = previewId ? document.querySelector(previewId) : null;
      const previewWrapper = preview ? preview.closest('.sl-image-preview-wrapper') : null;

      if (preview) {
        if (file && !msg) {
          preview.src = URL.createObjectURL(file);
          if (previewWrapper) previewWrapper.classList.remove('d-none');
          preview.classList.remove('d-none');
        } else {
          if (previewWrapper) previewWrapper.classList.add('d-none');
          preview.classList.add('d-none');
        }
      }
    });
  });

  // Discard preview button
  document.querySelectorAll('.sl-preview-close').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const targetInput = document.querySelector(btn.getAttribute('data-clear-target') || 'input[name="image"]');
      if (targetInput) targetInput.value = '';
      const wrapper = btn.closest('.sl-image-preview-wrapper');
      if (wrapper) wrapper.classList.add('d-none');
    });
  });

  /* ---- Client-side Form Validation ---- */
  document.querySelectorAll('form.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
      const pw = form.querySelector('[name=password]');
      const cf = form.querySelector('[name=confirm]');
      if (pw && cf) {
        cf.setCustomValidity(pw.value !== cf.value ? 'Passwords do not match.' : '');
      }
      if (!form.checkValidity()) {
        ev.preventDefault();
        ev.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });

  /* ---- Live Character Counters ---- */
  document.querySelectorAll('[data-counter]').forEach(function (el) {
    const out = document.querySelector(el.getAttribute('data-counter'));
    const max = el.getAttribute('maxlength');
    function update() {
      if (out) out.textContent = el.value.length + ' / ' + max;
    }
    el.addEventListener('input', update);
    update();
  });

  /* ---- Confirm Dialogs on Destructive Actions ---- */
  document.addEventListener('submit', function (ev) {
    const msg = ev.target.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) {
      ev.preventDefault();
    }
  });

  /* ---- Inline Comment Edit Toggle ---- */
  document.addEventListener('click', function (ev) {
    const btn = ev.target.closest('[data-toggle-edit]');
    if (!btn) return;
    const box = document.getElementById(btn.getAttribute('data-toggle-edit'));
    if (!box) return;
    const view = box.querySelector('.comment-view');
    const form = box.querySelector('.comment-edit');
    if (view && form) {
      view.classList.toggle('d-none');
      form.classList.toggle('d-none');
      if (!form.classList.contains('d-none')) {
        const ta = form.querySelector('textarea');
        if (ta) ta.focus();
      }
    }
  });

  /* ---- AJAX Comment Likes (Facebook & MeWe Hybrid) ---- */
  document.addEventListener('click', function (ev) {
    const btn = ev.target.closest('.sl-comment-like-btn, .sl-comment-like-pill');
    if (!btn) return;

    const commentId = btn.getAttribute('data-comment-like') || btn.id.replace('comment-pill-', '');
    if (!commentId) return;

    ev.preventDefault();

    const commentItem = document.getElementById('comment-' + commentId);
    const likeBtn = commentItem ? commentItem.querySelector('.sl-comment-like-btn') : (btn.classList.contains('sl-comment-like-btn') ? btn : null);
    const pill = commentItem ? commentItem.querySelector('.sl-comment-like-pill') : document.getElementById('comment-pill-' + commentId);
    const pillCount = pill ? pill.querySelector('.count-num') : null;
    const labelSpan = likeBtn ? likeBtn.querySelector('.like-label') : null;

    const csrfToken = (typeof window !== 'undefined' && window.CSRF_TOKEN) 
      || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      || document.querySelector('input[name="csrf"]')?.value 
      || '';

    const actionUrl = likeBtn?.getAttribute('data-url') 
      || ((typeof window !== 'undefined' && window.BASE_URL) ? window.BASE_URL + '/comment/like/' + commentId : 'comment/like/' + commentId);

    // Optimistic toggle
    const isCurrentlyLiked = likeBtn ? likeBtn.classList.contains('liked') : false;
    const nextLiked = !isCurrentlyLiked;
    const curCount = parseInt(pillCount?.textContent || '0', 10);
    const nextCount = nextLiked ? curCount + 1 : Math.max(0, curCount - 1);

    if (likeBtn) {
      likeBtn.classList.toggle('liked', nextLiked);
      likeBtn.setAttribute('aria-pressed', nextLiked ? 'true' : 'false');
      if (labelSpan) labelSpan.textContent = nextLiked ? 'Liked' : 'Like';
    }

    if (pill && pillCount) {
      pillCount.textContent = nextCount;
      if (nextCount > 0) {
        pill.classList.remove('d-none');
        pill.classList.add('sl-pop-in');
        setTimeout(() => pill.classList.remove('sl-pop-in'), 300);
      } else {
        pill.classList.add('d-none');
      }
    }

    const formData = new FormData();
    formData.append('csrf', csrfToken);

    fetch(actionUrl, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken
      },
      body: formData,
      credentials: 'same-origin'
    })
      .then(function (r) {
        if (!r.ok) throw new Error('Comment like failed: ' + r.status);
        return r.json();
      })
      .then(function (data) {
        if (!data.ok) return;

        // Apply server verified state
        if (likeBtn) {
          likeBtn.classList.toggle('liked', !!data.liked);
          likeBtn.setAttribute('aria-pressed', data.liked ? 'true' : 'false');
          if (labelSpan) labelSpan.textContent = data.liked ? 'Liked' : 'Like';
        }
        if (pill && pillCount) {
          pillCount.textContent = data.count;
          if (data.count > 0) {
            pill.classList.remove('d-none');
          } else {
            pill.classList.add('d-none');
          }
        }
        if (data.liked) {
          showToast('Comment liked! ❤️', 'bi-heart-fill');
        }
      })
      .catch(function (err) {
        console.error('Comment like error:', err);
        // Rollback optimistic state
        if (likeBtn) {
          likeBtn.classList.toggle('liked', isCurrentlyLiked);
          likeBtn.setAttribute('aria-pressed', isCurrentlyLiked ? 'true' : 'false');
          if (labelSpan) labelSpan.textContent = isCurrentlyLiked ? 'Liked' : 'Like';
        }
        if (pill && pillCount) {
          pillCount.textContent = curCount;
          if (curCount > 0) pill.classList.remove('d-none');
          else pill.classList.add('d-none');
        }
        showToast('Could not update like. Please try again.', 'bi-exclamation-triangle');
      });
  });

  document.addEventListener('submit', function (ev) {
    const form = ev.target.closest('form.comment-like-form');
    if (!form) return;
    ev.preventDefault();
    const btn = form.querySelector('.sl-comment-like-btn');
    if (btn) btn.click();
  });

  /* ---- Focus Comment Box Trigger ---- */
  document.addEventListener('click', function (ev) {
    const btn = ev.target.closest('[data-focus-comment]');
    if (!btn) return;
    const postId = btn.getAttribute('data-focus-comment');
    const postEl = document.getElementById('post-' + postId);
    if (!postEl) return;
    const input = postEl.querySelector('input[name="content"]');
    if (input) {
      input.focus();
      input.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

  /* ---- Copy Post Link Helper ---- */
  document.addEventListener('click', function (ev) {
    const btn = ev.target.closest('[data-copy-link]');
    if (!btn) return;
    const url = btn.getAttribute('data-copy-link');
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(() => {
        showToast('Link copied to clipboard!', 'bi-link-45deg');
      }).catch(() => {
        showToast('Link ready: ' + url, 'bi-link-45deg');
      });
    } else {
      showToast('Link: ' + url, 'bi-link-45deg');
    }
  });

  /* ---- Bookmark / Save Post Helper ---- */
  document.addEventListener('click', function (ev) {
    const btn = ev.target.closest('[data-save-post]');
    if (!btn) return;
    const isSaved = btn.getAttribute('data-saved') === 'true';
    const nextSaved = !isSaved;
    btn.setAttribute('data-saved', nextSaved ? 'true' : 'false');
    const icon = btn.querySelector('i');
    if (icon) {
      icon.className = nextSaved ? 'bi bi-bookmark-fill text-primary' : 'bi bi-bookmark';
    }
    showToast(nextSaved ? 'Post saved to your bookmarks!' : 'Post removed from bookmarks', 'bi-bookmark-check-fill');
  });

  /* ---- AJAX Likes + Emoji Reaction Tray Interaction ---- */
  const emojiMap = {
    like: '👍',
    love: '❤️',
    haha: '😂',
    wow:  '😮',
    sad:  '😢',
    fire: '🔥'
  };

  const reactionColorMap = {
    like: '#2563eb',
    love: '#ef4444',
    haha: '#f59e0b',
    wow:  '#f59e0b',
    sad:  '#f59e0b',
    fire: '#ef4444'
  };

  document.addEventListener('submit', function (ev) {
    const form = ev.target.closest('form.like-form');
    if (!form) return;
    ev.preventDefault();

    const card = form.closest('.sl-post-card') || form.closest('article');
    const btn = form.querySelector('.like-btn');
    if (!btn) return;
    btn.disabled = true;

    fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: new FormData(form),
      credentials: 'same-origin'
    })
      .then(function (r) {
        if (!r.ok) throw new Error('Reaction failed');
        return r.json();
      })
      .then(function (data) {
        // 1. Update Like Button Icon & Label
        const iconSpan = btn.querySelector('.btn-reaction-icon');
        const labelSpan = btn.querySelector('.btn-reaction-label');
        
        // Reset classes
        btn.className = 'sl-action-btn like-btn w-100';

        if (data.liked && data.reaction) {
          btn.classList.add('reacted', data.reaction);
          btn.setAttribute('aria-pressed', 'true');
          
          if (iconSpan) {
            iconSpan.innerHTML = emojiMap[data.reaction] || '👍';
          }
          if (labelSpan) {
            labelSpan.textContent = data.reaction.charAt(0).toUpperCase() + data.reaction.slice(1);
          }

          // Visual pop animation
          btn.style.transform = 'scale(1.2)';
          setTimeout(() => btn.style.transform = '', 200);
        } else {
          btn.setAttribute('aria-pressed', 'false');
          if (iconSpan) {
            iconSpan.innerHTML = '<i class="bi bi-hand-thumbs-up"></i>';
          }
          if (labelSpan) {
            labelSpan.textContent = 'Like';
          }
        }

        // 2. Update Like Count & Badges in Stats Bar
        if (card) {
          const countSpan = card.querySelector('.like-count');
          const stack = card.querySelector('.sl-reaction-stack');
          const noMsg = card.querySelector('.no-reactions-msg');
          const badgesSpan = card.querySelector('.sl-reaction-badges');

          if (countSpan) {
            countSpan.textContent = data.count;
          }

          if (stack) {
            if (data.count > 0) {
              stack.classList.remove('d-none');
              if (noMsg) noMsg.classList.add('d-none');
            } else {
              stack.classList.add('d-none');
              if (noMsg) noMsg.classList.remove('d-none');
            }
          }

          // Update badges stack
          if (badgesSpan && data.topReactions && data.topReactions.length > 0) {
            badgesSpan.innerHTML = data.topReactions.map(function (r) {
              const bg = reactionColorMap[r] || '#2563eb';
              const emo = emojiMap[r] || '👍';
              return `<span class="sl-emoji-badge" style="background:${bg};color:white;">${emo}</span>`;
            }).join('');
          }
        }
      })
      .catch(function () {
        form.classList.remove('like-form');
        form.submit();
      })
      .finally(function () {
        btn.disabled = false;
        // Reset form hidden reaction_type to default 'like'
        const input = form.querySelector('input[name="reaction_type"]');
        if (input) input.value = 'like';
      });
  });

  // Reaction Tray Emoji Click
  document.addEventListener('click', function (ev) {
    const emojiEl = ev.target.closest('.sl-tray-emoji');
    if (!emojiEl) return;
    const tray = emojiEl.closest('.sl-reaction-tray');
    const card = emojiEl.closest('.sl-post-card') || emojiEl.closest('article');
    if (!card) return;

    const rType = emojiEl.getAttribute('data-type') || 'like';
    const form = card.querySelector('form.like-form');
    if (!form) return;

    // Set hidden reaction_type in form
    let input = form.querySelector('input[name="reaction_type"]');
    if (!input) {
      input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'reaction_type';
      form.appendChild(input);
    }
    input.value = rType;

    // Immediately hide tray so it doesn't stay stuck on screen
    if (tray) {
      tray.classList.add('d-none');
    }

    form.requestSubmit();
    showToast(`Reacted with ${emojiEl.textContent.trim()}!`, 'bi-emoji-smile-fill');
  });

  // Ensure reaction tray can reopen when mouse moves away and back
  document.addEventListener('mouseleave', function (ev) {
    const wrapper = ev.target.closest && ev.target.closest('.sl-reaction-tray-wrapper');
    if (wrapper) {
      const tray = wrapper.querySelector('.sl-reaction-tray');
      if (tray) tray.classList.remove('d-none');
    }
  }, true);

  /* ---- Feed Filter Buttons (MeWe style) ---- */
  document.querySelectorAll('.sl-filter-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.sl-filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const filter = btn.getAttribute('data-filter');
      const posts = document.querySelectorAll('.sl-post-card');

      posts.forEach(post => {
        if (filter === 'all') {
          post.style.display = '';
        } else if (filter === 'media') {
          const hasMedia = post.querySelector('.sl-post-media') !== null;
          post.style.display = hasMedia ? '' : 'none';
        } else if (filter === 'text') {
          const hasMedia = post.querySelector('.sl-post-media') !== null;
          post.style.display = !hasMedia ? '' : 'none';
        }
      });
    });
  });

  /* ---- Interactive Messenger Chat Dock (Facebook style) ---- */
  const chatDock = document.getElementById('slChatDock');
  const chatName = document.getElementById('slChatTargetName');
  const chatAvatar = document.getElementById('slChatTargetAvatar');
  const chatBody = document.getElementById('slChatBody');
  const chatInput = document.getElementById('slChatInput');
  const chatForm = document.getElementById('slChatForm');
  const chatClose = document.getElementById('slChatClose');

  if (chatClose && chatDock) {
    chatClose.addEventListener('click', function () {
      chatDock.classList.remove('active');
    });
  }

  // Open chat dock from contacts
  document.querySelectorAll('[data-chat-user]').forEach(function (el) {
    el.addEventListener('click', function (ev) {
      ev.preventDefault();
      const name = el.getAttribute('data-chat-user');
      const avatar = el.getAttribute('data-chat-avatar');

      if (chatDock && chatName && chatAvatar) {
        chatName.textContent = name;
        if (avatar) chatAvatar.src = avatar;
        chatDock.classList.add('active');
        if (chatInput) chatInput.focus();
      }
    });
  });

  // Sending a chat message in the dock
  if (chatForm && chatInput && chatBody) {
    chatForm.addEventListener('submit', function (ev) {
      ev.preventDefault();
      const text = chatInput.value.trim();
      if (!text) return;

      const outMsg = document.createElement('div');
      outMsg.className = 'sl-chat-msg outgoing';
      outMsg.textContent = text;
      chatBody.appendChild(outMsg);
      chatInput.value = '';
      chatBody.scrollTop = chatBody.scrollHeight;

      // Simulated automated friendly response
      setTimeout(() => {
        const reply = document.createElement('div');
        reply.className = 'sl-chat-msg incoming';
        reply.textContent = 'Hey! Thanks for connecting on SOciaLink! 😊';
        chatBody.appendChild(reply);
        chatBody.scrollTop = chatBody.scrollHeight;
      }, 1000);
    });
  }

  /* ---- Create Story Live Preview & Gradient Picker ---- */
  const storyCaption = document.getElementById('storyCaption');
  const storyLiveText = document.getElementById('storyLiveText');
  const storyLivePreview = document.getElementById('storyLivePreview');
  const storyBgInput = document.getElementById('storyBgInput');
  const storyImageInput = document.getElementById('storyImageInput');
  const storyLiveImage = document.getElementById('storyLiveImage');

  if (storyCaption && storyLiveText) {
    storyCaption.addEventListener('input', function () {
      const val = storyCaption.value.trim();
      storyLiveText.textContent = val !== '' ? val : 'Type something below...';
    });
  }

  // Gradient circle clicks
  document.querySelectorAll('.sl-gradient-circle').forEach(function (circle) {
    circle.addEventListener('click', function () {
      document.querySelectorAll('.sl-gradient-circle').forEach(c => c.classList.remove('active'));
      circle.classList.add('active');

      const grad = circle.getAttribute('data-gradient');
      if (storyBgInput) storyBgInput.value = grad;

      if (storyLivePreview) {
        // Remove existing gradient classes
        storyLivePreview.className = storyLivePreview.className.replace(/bg-grad-\S+/g, '');
        storyLivePreview.classList.add('bg-grad-' + grad);
      }
    });
  });

  // Story photo file selection
  if (storyImageInput && storyLiveImage) {
    storyImageInput.addEventListener('change', function () {
      const f = storyImageInput.files[0];
      if (f && OK_TYPES.includes(f.type) && f.size <= MAX_BYTES) {
        storyLiveImage.src = URL.createObjectURL(f);
        storyLiveImage.classList.remove('d-none');
      } else {
        storyLiveImage.src = '';
        storyLiveImage.classList.add('d-none');
      }
    });
  }

  /* ---- Story Viewer Modal ---- */
  const storyCards = document.querySelectorAll('.sl-story-card[data-story-author]');
  const storyModal = document.getElementById('slStoryModal');
  const storyAuthorName = document.getElementById('slStoryAuthorName');
  const storyAuthorAvatar = document.getElementById('slStoryAuthorAvatar');
  const storyTime = document.getElementById('slStoryTime');
  const storyImage = document.getElementById('slStoryImage');
  const storyGradientBg = document.getElementById('slStoryGradientBg');
  const storyCaptionText = document.getElementById('slStoryCaption');

  storyCards.forEach(function (card) {
    card.addEventListener('click', function () {
      const author = card.getAttribute('data-story-author');
      const avatar = card.getAttribute('data-story-avatar');
      const time = card.getAttribute('data-story-time');
      const caption = card.getAttribute('data-story-caption');
      const imgUrl = card.getAttribute('data-story-img');
      const gradClass = card.getAttribute('data-story-gradient') || 'bg-grad-ocean-blue';

      if (storyAuthorName) storyAuthorName.textContent = author;
      if (storyAuthorAvatar && avatar) storyAuthorAvatar.src = avatar;
      if (storyTime && time) storyTime.textContent = '· ' + time;

      if (imgUrl) {
        // Photo story
        if (storyImage) {
          storyImage.src = imgUrl;
          storyImage.classList.remove('d-none');
        }
        if (storyGradientBg) {
          storyGradientBg.className = 'position-absolute bottom-0 start-0 end-0 p-3 text-center';
          storyGradientBg.style.background = 'linear-gradient(0deg, rgba(0,0,0,0.85) 0%, transparent 100%)';
        }
        if (storyCaptionText) storyCaptionText.textContent = caption || '';
      } else {
        // Gradient text story
        if (storyImage) {
          storyImage.classList.add('d-none');
        }
        if (storyGradientBg) {
          storyGradientBg.className = 'w-100 h-100 d-flex align-items-center justify-content-center p-4 text-center position-absolute inset-0 ' + gradClass;
          storyGradientBg.style.background = '';
        }
        if (storyCaptionText) storyCaptionText.textContent = caption || '';
      }

      if (storyModal && window.bootstrap) {
        const modal = new bootstrap.Modal(storyModal);
        modal.show();
      }
    });
  });

  /* ---- Image Lightbox Modal ---- */
  const lightboxModal = document.getElementById('slLightboxModal');
  const lightboxImage = document.getElementById('slLightboxImage');

  document.querySelectorAll('.sl-post-media img').forEach(function (img) {
    img.addEventListener('click', function () {
      if (lightboxImage && lightboxModal && window.bootstrap) {
        lightboxImage.src = img.src;
        const modal = new bootstrap.Modal(lightboxModal);
        modal.show();
      }
    });
  });

})();
