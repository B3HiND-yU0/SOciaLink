<?php
/**
 * CommentModel - database logic for the `comments` table.
 */
class CommentModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /** Returns comments grouped by post id: [postId => [comment, ...]] (oldest first). */
    public function forPosts(array $postIds, int $viewerId = 0): array
    {
        if (!$postIds) {
            return [];
        }
        $viewerId = $viewerId ?: (int) (function_exists('current_user_id') ? (current_user_id() ?? 0) : 0);
        $postIds  = array_map('intval', array_values($postIds));
        $marks    = implode(',', array_fill(0, count($postIds), '?'));
        
        $sql = "SELECT c.*, u.username, u.full_name, u.profile_image,
                       (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id) AS like_count,
                       (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND user_id = ?) AS user_liked
                FROM comments c 
                JOIN users u ON u.id = c.user_id
                WHERE c.post_id IN ($marks)
                ORDER BY c.created_at ASC, c.id ASC";
        
        $st = $this->db->prepare($sql);
        $st->execute(array_merge([$viewerId], $postIds));
        $grouped = [];
        foreach ($st->fetchAll() as $row) {
            $grouped[$row['post_id']][] = $row;
        }
        return $grouped;
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM comments WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function create(int $postId, int $userId, string $content): int
    {
        $st = $this->db->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (:p, :u, :c)');
        $st->execute([':p' => $postId, ':u' => $userId, ':c' => $content]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $content): void
    {
        $st = $this->db->prepare('UPDATE comments SET content = :c WHERE id = :id');
        $st->execute([':c' => $content, ':id' => $id]);
    }

    public function delete(int $id): void
    {
        $delLikes = $this->db->prepare('DELETE FROM comment_likes WHERE comment_id = :id');
        $delLikes->execute([':id' => $id]);

        $st = $this->db->prepare('DELETE FROM comments WHERE id = :id');
        $st->execute([':id' => $id]);
    }

    /**
     * Toggle like for a comment by a specific user.
     * Returns ['liked' => bool, 'count' => int]
     */
    public function toggleLike(int $commentId, int $userId): array
    {
        $comment = $this->find($commentId);
        if (!$comment) {
            return ['liked' => false, 'count' => 0];
        }

        $check = $this->db->prepare('SELECT id FROM comment_likes WHERE comment_id = :cid AND user_id = :uid LIMIT 1');
        $check->execute([':cid' => $commentId, ':uid' => $userId]);
        $existing = $check->fetch();

        if ($existing) {
            $del = $this->db->prepare('DELETE FROM comment_likes WHERE comment_id = :cid AND user_id = :uid');
            $del->execute([':cid' => $commentId, ':uid' => $userId]);
            $liked = false;
        } else {
            $ins = $this->db->prepare('INSERT INTO comment_likes (comment_id, user_id) VALUES (:cid, :uid)');
            $ins->execute([':cid' => $commentId, ':uid' => $userId]);
            $liked = true;
        }

        $cntSt = $this->db->prepare('SELECT COUNT(*) FROM comment_likes WHERE comment_id = :cid');
        $cntSt->execute([':cid' => $commentId]);
        $count = (int) $cntSt->fetchColumn();

        return [
            'liked' => $liked,
            'count' => $count,
        ];
    }
}

