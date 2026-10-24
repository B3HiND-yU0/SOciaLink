<?php
/**
 * CommentController - create / update / delete comments (own comments only).
 */
class CommentController extends Controller
{
    private CommentModel $comments;
    private PostModel $posts;

    public function __construct()
    {
        $this->comments = new CommentModel();
        $this->posts    = new PostModel();
    }

    public function store(string $postId = ''): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        if (!$this->posts->find((int) $postId)) {
            $this->notFound();
        }
        $content = $this->clean($_POST['content'] ?? '');
        if ($content === '' || mb_strlen($content) > 1000) {
            flash('danger', 'Comments must be 1-1,000 characters.');
        } else {
            $this->comments->create((int) $postId, $me, $content);
            flash('success', 'Comment added.');
        }
        redirect_back('post/index', '#post-' . (int) $postId);
    }

    public function update(string $id = ''): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $comment = $this->comments->find((int) $id);
        if (!$comment) {
            $this->notFound();
        }
        if ((int) $comment['user_id'] !== $me) {        // authorization check
            $this->forbidden();
        }
        $content = $this->clean($_POST['content'] ?? '');
        if ($content === '' || mb_strlen($content) > 1000) {
            flash('danger', 'Comments must be 1-1,000 characters.');
        } else {
            $this->comments->update((int) $id, $content);
            flash('success', 'Comment updated.');
        }
        redirect_back('post/index', '#post-' . (int) $comment['post_id']);
    }

    public function delete(string $id = ''): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $comment = $this->comments->find((int) $id);
        if (!$comment) {
            $this->notFound();
        }
        if ((int) $comment['user_id'] !== $me) {
            $this->forbidden();
        }
        $this->comments->delete((int) $id);
        flash('success', 'Comment deleted.');
        redirect_back('post/index', '#post-' . (int) $comment['post_id']);
    }

    /**
     * Like / unlike a comment. Works with AJAX (JSON) and plain form submissions.
     */
    public function like(string $id = ''): void
    {
        $me = $this->requireLogin();
        $this->requirePost();

        $comment = $this->comments->find((int) $id);
        if (!$comment) {
            $this->isAjax() ? $this->json(['ok' => false, 'error' => 'Comment not found'], 404) : $this->notFound();
        }

        $res = $this->comments->toggleLike((int) $id, $me);

        if ($this->isAjax()) {
            $this->json([
                'ok'        => true,
                'commentId' => (int) $id,
                'liked'     => $res['liked'],
                'count'     => $res['count'],
            ]);
        }

        redirect_back('post/index', '#comment-' . (int) $id);
    }
}

