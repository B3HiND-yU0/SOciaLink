# SOciaLink - Next-Gen Social Networking Platform

A modern social networking web application combining the intuitive, engaging layout of **Facebook** with the privacy-first, community-centric elegance of **MeWe**. Built with PHP 8 (MVC, PDO) + MySQL + Bootstrap 5 + Vanilla JavaScript with fluid animations and responsive design.

---

## ✨ Features & Architecture

### 🎨 Facebook + MeWe Combined UI & Layout
- **3-Column Desktop Layout**:
  - **Left Sidebar**: Quick navigation (News Feed, Friends & Network, Chat, Saved Bookmarks, Profile Settings) + MeWe Signature *Privacy by Design* badge.
  - **Center Stream**:
    - **Stories & Reels Carousel**: Horizontal touch tray with "Create Story" card and friend stories with interactive full-screen viewer modal.
    - **What's on your mind? Composer**: Rich post creation box with photo attachment preview, Feeling/Activity shortcuts, Tag friends, audience selector, and character limit indicator.
    - **MeWe Feed Filter Bar**: Quick filter pills (🔥 All Posts, 📷 Photos & Media, 💭 Discussions).
    - **Interactive Post Cards**: Author avatar with status ring, verified badge, 3-dots post options, hashtags auto-linking, full-resolution image lightbox, engagement counters (👍 ❤️ 🔥), and bubble comments.
    - **Floating Reaction Tray**: Hover or touch the Like button to reveal animated bouncy emojis (👍 Like, ❤️ Love, 😂 Haha, 😮 Wow, 😢 Sad, 🔥 Fire) with AJAX updates and heart-pop micro-animations.
  - **Right Sidebar**:
    - **Suggested For You**: People you may know with one-click connect.
    - **Active Now (Online Contacts)**: Real-time presence list with glowing animated green status dots. Clicking opens the bottom-right Messenger chat dock.
    - **Trending on SOciaLink**: Real-time trending hashtags and topics.
- **Top Navigation Bar**:
  - SOciaLink gradient link icon brand logo.
  - Expanding pill search bar with autocomplete style.
  - Facebook-style iconic 5 center tabs (Feed, Network, Communities, Saved, Explore).
  - Quick action buttons: Theme Switcher (Dark/Light mode with 360° spin), Messenger trigger with pulse badge, Notifications, and User avatar menu.
- **Mobile Bottom Navigation Bar (< 768px)**:
  - Fixed bottom bar for thumb-friendly navigation on mobile devices.
- **Interactive Modals & Docks**:
  - **Story Viewer Modal**: View stories with progress bar and direct reply.
  - **Floating Messenger Chat Dock**: Docked bottom-right Facebook Messenger chat.
  - **Photo Lightbox**: Click any post image for distraction-free preview.
  - **Toast Notifications**: Feedback for saved posts, copied links, and reactions.

---

## 🚀 Setup & Installation (XAMPP)

1. Make sure **Apache** and **MySQL** are running in the XAMPP Control Panel.
2. The project is placed at: `C:\xampp\htdocs\SOciaLink`.
3. Open your browser and visit:
   - **http://localhost/SOciaLink/** (or `http://localhost/SOciaLink/social_app/public/`)
4. Sample accounts (all use password: `password123`):
   - `maria`
   - `juan`
   - `ana`
   - Or click **Create new account** to register your own!

---

## 🔒 Security Practices
- **SQL Injection Guard**: 100% prepared statements via PDO (`ATTR_EMULATE_PREPARES = false`).
- **XSS Prevention**: Strict HTML escaping with `e()` (`htmlspecialchars`).
- **Authentication**: Secure password hashing (`password_hash` with BCRYPT) and session regeneration.
- **CSRF Protection**: Tokenized requests verified on all state-changing endpoints.
- **Upload Validation**: MIME verification via `finfo` and `getimagesize`, 2 MB limits, safe random filenames.
