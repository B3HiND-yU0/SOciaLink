<?php
/**
 * AuthController - register, login, logout.
 */
class AuthController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function login(): void
    {
        $this->requireGuest();

        if ($this->isPost()) {
            $this->requirePost();
            $username = strtolower($this->clean($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            $user = $this->users->findByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);           // prevent session fixation
                $_SESSION['user_id'] = (int) $user['id'];
                flash('success', 'Welcome back, ' . $user['full_name'] . '!');
                redirect('post/index');
            }
            $_SESSION['old'] = ['username' => $username];
            flash('danger', 'Wrong username or password.');
            redirect('auth/login');
        }

        $this->view('auth/login', ['title' => 'Log in']);
    }

    public function register(): void
    {
        $this->requireGuest();

        if ($this->isPost()) {
            $this->requirePost();
            $fullName = $this->clean($_POST['full_name'] ?? '');
            $username = strtolower($this->clean($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm  = (string) ($_POST['confirm'] ?? '');

            $errors = [];
            if ($fullName === '' || mb_strlen($fullName) > 100) {
                $errors[] = 'Enter your full name (up to 100 characters).';
            }
            if (!preg_match('/^[a-z0-9_]{3,30}$/', $username)) {
                $errors[] = 'Username must be 3-30 characters: letters, numbers, or underscore.';
            } elseif ($this->users->usernameExists($username)) {
                $errors[] = 'That username is taken. Try another one.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }
            if ($password !== $confirm) {
                $errors[] = 'Passwords do not match.';
            }

            [$image, $imgError] = save_uploaded_image($_FILES['profile_image'] ?? [], 'avatar');
            if ($imgError) {
                $errors[] = $imgError;
            }

            if ($errors) {
                delete_uploaded_image($image);
                $_SESSION['old'] = ['full_name' => $fullName, 'username' => $username];
                flash('danger', implode("\n", $errors));
                redirect('auth/register');
            }

            $id = $this->users->create($username, password_hash($password, PASSWORD_DEFAULT), $fullName, $image);
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            flash('success', 'Account created. Welcome to Barkada!');
            redirect('post/index');
        }

        $this->view('auth/register', ['title' => 'Create account']);
    }

    public function logout(): void
    {
        $this->requirePost();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        flash('success', 'You have been logged out.');
        redirect('auth/login');
    }
}
