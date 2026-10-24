<?php
/**
 * SearchController - search users (name/username) AND posts (keywords).
 */
class SearchController extends Controller
{
    public function index(): void
    {
        $me = $this->requireLogin();
        $q  = $this->clean($_GET['q'] ?? '');
        if (mb_strlen($q) > 50) {
            $q = mb_substr($q, 0, 50);
        }

        $users = $posts = $comments = [];
        if ($q !== '') {
            $users    = (new UserModel())->search($q);
            $posts    = (new PostModel())->search($q, $me);
            $comments = (new CommentModel())->forPosts(array_column($posts, 'id'));
        }
        $this->view('search/index', [
            'title'    => 'Search',
            'q'        => $q,
            'users'    => $users,
            'posts'    => $posts,
            'comments' => $comments,
        ]);
    }
}
