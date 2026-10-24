<?php
/**
 * Global helper functions used by controllers and views.
 */

/** Escape output (XSS guard). Use on EVERY value printed in a view. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build an app URL: url('post/edit/5') */
function url(string $path = ''): string
{
    return BASE_URL . '/index.php' . ($path !== '' ? '?url=' . ltrim($path, '/') : '');
}

/** Build a URL to a file inside public/ */
function asset(string $path): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/** Redirect back to the page the user came from (same host only). */
function redirect_back(string $fallback = 'post/index', string $anchor = ''): void
{
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $p   = parse_url($ref);
    if ($ref !== '' && isset($p['host']) && $p['host'] === ($_SERVER['HTTP_HOST'] ?? '')
        && !preg_match('/[\r\n]/', $ref)) {
        $target = strtok($ref, '#');
        header('Location: ' . $target . $anchor);
        exit;
    }
    redirect($fallback);
}

/** Flash messages: flash('success', 'Saved!') then flash('success') to read once. */
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

/** Old form input (re-fill after validation error). */
function old(string $key, string $default = ''): string
{
    return e($_SESSION['old'][$key] ?? $default);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

/** "5 minutes ago" style timestamp. */
function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . ' min ago';
    if ($diff < 86400)  return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' day' . (floor($diff / 86400) > 1 ? 's' : '') . ' ago';
    return date('M j, Y', strtotime($datetime));
}

/** Avatar URL, falling back to a generated initial avatar (inline SVG data URI). */
function avatar_url(?string $file, string $name = '?'): string
{
    if ($file && is_file(__DIR__ . '/../../public/uploads/' . $file)) {
        return asset('uploads/' . rawurlencode($file));
    }
    $initial = strtoupper(mb_substr(trim($name) ?: '?', 0, 1));
    $hash = md5($name);
    // Dynamic modern gradients based on name
    $colors = [
        ['#2563EB', '#06B6D4'],
        ['#4F46E5', '#7C3AED'],
        ['#059669', '#10B981'],
        ['#D97706', '#F59E0B'],
        ['#DC2626', '#F43F5E'],
        ['#0891B2', '#0284C7']
    ];
    $idx = hexdec(substr($hash, 0, 2)) % count($colors);
    $c1 = $colors[$idx][0];
    $c2 = $colors[$idx][1];

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="' . $c1 . '"/><stop offset="100%" stop-color="' . $c2 . '"/></linearGradient></defs><rect width="120" height="120" rx="60" fill="url(#g)"/><text x="50%" y="54%" font-family="system-ui,-apple-system,sans-serif" font-weight="700" font-size="52" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">'
         . htmlspecialchars($initial, ENT_XML1) . '</text></svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/**
 * Validate and save an uploaded image.
 * Returns [filename|null, error|null]. No file chosen => [null, null].
 */
function save_uploaded_image(array $file, string $prefix = 'img'): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'The image could not be uploaded. Try again.'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [null, 'Image is too large. Maximum size is 2 MB.'];
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $mime    = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        return [null, 'Only JPG, PNG, GIF, or WEBP images are allowed.'];
    }
    $name = $prefix . '_' . bin2hex(random_bytes(10)) . '.' . $allowed[$mime];
    $dest = __DIR__ . '/../../public/uploads/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return [null, 'Could not save the image. Check the uploads folder permissions.'];
    }
    return [$name, null];
}

function delete_uploaded_image(?string $name): void
{
    if ($name && preg_match('/^[A-Za-z0-9_.-]+$/', $name)) {
        $path = __DIR__ . '/../../public/uploads/' . $name;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
