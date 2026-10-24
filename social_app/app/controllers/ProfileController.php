<?php
/**
 * ProfileController - view and edit profiles.
 */
class ProfileController extends Controller
{
    private UserModel $users;
    private PostModel $posts;
    private CommentModel $comments;

    public function __construct()
    {
        $this->users    = new UserModel();
        $this->posts    = new PostModel();
        $this->comments = new CommentModel();
    }

    public function show(string $username = ''): void
    {
        $me = $this->requireLogin();
        $profile = $this->users->findByUsername(strtolower($username));
        if (!$profile) {
            $this->notFound();
        }
        $posts = $this->posts->byUser((int) $profile['id'], $me);
        $this->view('profile/show', [
            'title'     => $profile['full_name'],
            'profile'   => $profile,
            'posts'     => $posts,
            'comments'  => $this->comments->forPosts(array_column($posts, 'id')),
            'postCount' => count($posts),
        ]);
    }

    public function edit(): void
    {
        $me = $this->requireLogin();
        $this->view('profile/edit', ['title' => 'Edit profile', 'profile' => $this->users->findById($me)]);
    }

    public function update(): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $user     = $this->users->findById($me);
        $fullName = $this->clean($_POST['full_name'] ?? '');
        $bio      = $this->clean($_POST['bio'] ?? '');

        $errors = [];
        if ($fullName === '' || mb_strlen($fullName) > 100) {
            $errors[] = 'Enter your full name (up to 100 characters).';
        }
        if (mb_strlen($bio) > 300) {
            $errors[] = 'Bio can be up to 300 characters.';
        }
        [$image, $imgError] = save_uploaded_image($_FILES['profile_image'] ?? [], 'avatar');
        if ($imgError) {
            $errors[] = $imgError;
        }
        if ($errors) {
            delete_uploaded_image($image);
            flash('danger', implode("\n", $errors));
            redirect('profile/edit');
        }

        $this->users->updateProfile($me, $fullName, $bio, $image);
        if ($image !== null) {
            delete_uploaded_image($user['profile_image']);
        }
        flash('success', 'Profile updated.');
        redirect('profile/show/' . $user['username']);
    }
}
