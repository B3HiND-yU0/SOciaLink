<?php
/**
 * PostController - newsfeed + CRUD for posts + likes.
 */
class PostController extends Controller
{
    private const PER_PAGE = 10;

    private PostModel $posts;
    private CommentModel $comments;
    private LikeModel $likes;

    public function __construct()
    {
        $this->posts    = new PostModel();
        $this->comments = new CommentModel();
        $this->likes    = new LikeModel();
    }

    /** Newsfeed (latest first, paginated). */
    public function index(): void
    {
        $me    = $this->requireLogin();
        $page  = max(1, (int) ($_GET['page'] ?? 1));
        $total = $this->posts->countAll();
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page  = min($page, $pages);

        $posts   = $this->posts->feed($me, self::PER_PAGE, ($page - 1) * self::PER_PAGE);
        $stories = (new StoryModel())->allRecent(15);

        $this->view('posts/index', [
            'title'    => 'Newsfeed',
            'posts'    => $posts,
            'stories'  => $stories,
            'comments' => $this->comments->forPosts(array_column($posts, 'id')),
            'page'     => $page,
            'pages'    => $pages,
        ]);
    }

    public function store(): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $content = $this->clean($_POST['content'] ?? '');
        $error   = $this->validateContent($content);
        [$image, $imgError] = save_uploaded_image($_FILES['image'] ?? [], 'post');
        $error = $error ?? $imgError;

        if ($error) {
            delete_uploaded_image($image);
            $_SESSION['old'] = ['content' => $content];
            flash('danger', $error);
            redirect_back('post/index');
        }

        $this->posts->create($me, $content, $image);
        flash('success', 'Posted!');
        redirect('post/index');
    }

    public function edit(string $id = ''): void
    {
        $me   = $this->requireLogin();
        $post = $this->posts->find((int) $id, $me);
        if (!$post) {
            $this->notFound();
        }
        if ((int) $post['user_id'] !== $me) {
            $this->forbidden();
        }
        $this->view('posts/edit', ['title' => 'Edit post', 'post' => $post]);
    }

    public function update(string $id = ''): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $post = $this->posts->find((int) $id, $me);
        if (!$post) {
            $this->notFound();
        }
        if ((int) $post['user_id'] !== $me) {          // authorization check
            $this->forbidden();
        }

        $content = $this->clean($_POST['content'] ?? '');
        $error   = $this->validateContent($content);
        [$image, $imgError] = save_uploaded_image($_FILES['image'] ?? [], 'post');
        $error = $error ?? $imgError;

        if ($error) {
            delete_uploaded_image($image);
            flash('danger', $error);
            redirect('post/edit/' . (int) $id);
        }

        if ($image !== null) {                          // replaced image
            delete_uploaded_image($post['image']);
            $this->posts->update((int) $id, $content, $image);
        } else {
            $this->posts->update((int) $id, $content, null);
            if (!empty($_POST['remove_image'])) {
                delete_uploaded_image($post['image']);
                $this->posts->removeImage((int) $id);
            }
        }
        flash('success', 'Post updated.');
        redirect('post/index');
    }

    public function delete(string $id = ''): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $post = $this->posts->find((int) $id, $me);
        if (!$post) {
            $this->notFound();
        }
        if ((int) $post['user_id'] !== $me) {
            $this->forbidden();
        }
        delete_uploaded_image($post['image']);
        $this->posts->delete((int) $id);
        flash('success', 'Post deleted.');
        redirect_back('post/index');
    }

    /** Like / unlike / react. Works with AJAX (JSON) and plain form posts. */
    public function like(string $id = ''): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $post = $this->posts->find((int) $id, $me);
        if (!$post) {
            $this->isAjax() ? $this->json(['ok' => false], 404) : $this->notFound();
        }

        $type = $this->clean($_POST['reaction_type'] ?? 'like');
        $res  = $this->likes->react((int) $id, $me, $type ?: 'like');

        if ($this->isAjax()) {
            $this->json([
                'ok'           => true,
                'liked'        => $res['liked'],
                'reaction'     => $res['reaction'],
                'count'        => $res['count'],
                'topReactions' => $res['topReactions'],
            ]);
        }
        redirect_back('post/index', '#post-' . (int) $id);
    }

    private function validateContent(string $content): ?string
    {
        if ($content === '') {
            return 'Write something before posting.';
        }
        if (mb_strlen($content) > 1000) {
            return 'Posts can be up to 1,000 characters.';
        }
        return null;
    }
}
