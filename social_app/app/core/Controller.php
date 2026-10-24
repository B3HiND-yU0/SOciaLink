<?php
/**
 * Base Controller - shared helpers for all controllers.
 */
abstract class Controller
{
    /** Render a view inside the header/footer layout. */
    protected function view(string $view, array $data = []): void
    {
        // Logged-in user for navbar and sidebars
        $meUser = null;
        $suggestedUsers = [];
        $activeContacts = [];
        if (current_user_id()) {
            $userModel = new UserModel();
            $meUser = $userModel->findById(current_user_id());
            if (!$meUser) {                       // account was removed
                $_SESSION = [];
            } else {
                $suggestedUsers = $userModel->getSuggestedUsers(current_user_id(), 5);
                $activeContacts = $userModel->getContacts(current_user_id(), 8);
            }
        }
        $data['suggestedUsers'] = $data['suggestedUsers'] ?? $suggestedUsers;
        $data['activeContacts'] = $data['activeContacts'] ?? $activeContacts;
        extract($data, EXTR_SKIP);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/layouts/header.php';
        require $viewFile;
        require __DIR__ . '/../views/layouts/footer.php';
        unset($_SESSION['old']);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }

    protected function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    /** Only allow POST + a valid CSRF token. */
    protected function requirePost(): void
    {
        if (!$this->isPost()) {
            http_response_code(405);
            exit('Method not allowed.');
        }
        $token = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
            http_response_code(419);
            exit('Your session expired. Go back, refresh the page, and try again.');
        }
    }

    protected function requireLogin(): int
    {
        if (!current_user_id()) {
            flash('warning', 'Please log in to continue.');
            redirect('auth/login');
        }
        return current_user_id();
    }

    protected function requireGuest(): void
    {
        if (current_user_id()) {
            redirect('post/index');
        }
    }

    /** Trim + strip control characters. Output is still escaped with e() in views. */
    protected function clean(?string $value): string
    {
        $value = trim((string) $value);
        return preg_replace('/[^\P{C}\n\r\t]+/u', '', $value) ?? '';
    }

    protected function notFound(): void
    {
        http_response_code(404);
        $this->view('partials/404');
        exit;
    }

    protected function forbidden(): void
    {
        http_response_code(403);
        flash('danger', 'You can only change your own content.');
        redirect('post/index');
    }
}
