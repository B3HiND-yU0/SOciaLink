<?php
/**
 * StoryController - handles creating and managing stories.
 */
class StoryController extends Controller
{
    private StoryModel $stories;

    public function __construct()
    {
        $this->stories = new StoryModel();
    }

    /**
     * Store a newly created story (photo, text, or gradient).
     */
    public function store(): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $caption = $this->clean($_POST['caption'] ?? '');
        $allowedGradients = [
            'ocean-blue', 'sunset-gradient', 'fire-sunset',
            'neon-purple', 'emerald-forest', 'midnight-dark', 'candy-pink'
        ];
        $gradient = $_POST['bg_gradient'] ?? 'ocean-blue';
        if (!in_array($gradient, $allowedGradients, true)) {
            $gradient = 'ocean-blue';
        }

        // Handle optional uploaded photo
        [$image, $imgError] = save_uploaded_image($_FILES['image'] ?? [], 'story');

        if ($imgError) {
            flash('danger', $imgError);
            redirect_back('post/index');
        }

        // Must have either caption or image
        if ($caption === '' && $image === null) {
            flash('warning', 'Please enter a caption or choose a photo for your story.');
            redirect_back('post/index');
        }

        if (mb_strlen($caption) > 255) {
            delete_uploaded_image($image);
            flash('danger', 'Story captions can be up to 255 characters.');
            redirect_back('post/index');
        }

        $this->stories->create($me, $caption, $image, $gradient);
        flash('success', 'Your story has been shared! 🌟');
        redirect('post/index');
    }

    /**
     * Delete a story owned by the user.
     */
    public function delete(string $id = ''): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $story = $this->stories->find((int) $id);
        if (!$story) {
            $this->notFound();
        }
        if ((int) $story['user_id'] !== $me) {
            $this->forbidden();
        }

        if ($story['image']) {
            delete_uploaded_image($story['image']);
        }
        $this->stories->delete((int) $id, $me);

        flash('success', 'Story deleted.');
        redirect_back('post/index');
    }
}
