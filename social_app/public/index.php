<?php
/**
 * Front controller - every request enters here.
 * URL format:  index.php?url=controller/action/param
 * Example:     index.php?url=post/edit/5   ->  PostController::edit('5')
 */
declare(strict_types=1);

date_default_timezone_set('Asia/Manila');

// Secure session cookie settings
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();

// Base URL (works in any folder, e.g. http://localhost/social_app/public)
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

$app = __DIR__ . '/../app/';
require $app . 'core/helpers.php';
require $app . 'core/Database.php';
require $app . 'core/Controller.php';
foreach (['UserModel', 'PostModel', 'CommentModel', 'LikeModel', 'StoryModel'] as $m) {
    require $app . "models/$m.php";
}

// --- Routing -------------------------------------------------------------
$route = trim((string) ($_GET['url'] ?? ''), '/');
if ($route === '') {
    redirect(current_user_id() ? 'post/index' : 'auth/login');
}

$parts      = explode('/', $route);
$controller = strtolower($parts[0]);
$action     = strtolower($parts[1] ?? 'index');
$params     = array_slice($parts, 2);

// Only letters allowed -> no path traversal / arbitrary class loading
$class = ucfirst($controller) . 'Controller';
$file  = $app . 'controllers/' . $class . '.php';

if (!preg_match('/^[a-z]+$/', $controller) || !preg_match('/^[a-z_]+$/', $action) || !is_file($file)) {
    http_response_code(404);
    exit('404 - Page not found.');
}

require $file;
$instance = new $class();

// is_callable from here only accepts PUBLIC methods (helpers are protected/private)
if (!is_callable([$instance, $action]) || str_starts_with($action, '__')) {
    http_response_code(404);
    exit('404 - Page not found.');
}

call_user_func_array([$instance, $action], $params);
