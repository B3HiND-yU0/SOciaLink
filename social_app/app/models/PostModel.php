<?php
/**
 * PostModel - database logic for the `posts` table.
 * Every list query also returns like_count, comment_count and liked (for the viewer).
 */
class PostModel
{
    private PDO $db;

    private const SELECT = 'SELECT p.*, u.username, u.full_name, u.profile_image,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count,
        (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count,
        IF(mylike.id IS NOT NULL, 1, 0) AS liked,
        mylike.reaction_type AS user_reaction
        FROM posts p
        JOIN users u ON u.id = p.user_id
        LEFT JOIN likes mylike ON mylike.post_id = p.id AND mylike.user_id = :me';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /** Newsfeed: all posts, latest first. */
    public function feed(int $viewerId, int $limit, int $offset): array
    {
        $st = $this->db->prepare(self::SELECT . ' ORDER BY p.created_at DESC, p.id DESC LIMIT :lim OFFSET :off');
        $st->bindValue(':me', $viewerId, PDO::PARAM_INT);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM posts')->fetchColumn();
    }

    public function byUser(int $userId, int $viewerId): array
    {
        $st = $this->db->prepare(self::SELECT . ' WHERE p.user_id = :uid ORDER BY p.created_at DESC, p.id DESC');
        $st->bindValue(':me', $viewerId, PDO::PARAM_INT);
        $st->bindValue(':uid', $userId, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function find(int $id, int $viewerId = 0): ?array
    {
        $st = $this->db->prepare(self::SELECT . ' WHERE p.id = :pid');
        $st->bindValue(':me', $viewerId, PDO::PARAM_INT);
        $st->bindValue(':pid', $id, PDO::PARAM_INT);
        $st->execute();
        return $st->fetch() ?: null;
    }

    /** Search posts by keyword. */
    public function search(string $term, int $viewerId, int $limit = 30): array
    {
        $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term) . '%';
        $st = $this->db->prepare(
            self::SELECT . ' WHERE p.content LIKE :q ORDER BY p.created_at DESC LIMIT :lim'
        );
        $st->bindValue(':me', $viewerId, PDO::PARAM_INT);
        $st->bindValue(':q', $like);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function create(int $userId, string $content, ?string $image): int
    {
        $st = $this->db->prepare('INSERT INTO posts (user_id, content, image) VALUES (:u, :c, :i)');
        $st->execute([':u' => $userId, ':c' => $content, ':i' => $image]);
        return (int) $this->db->lastInsertId();
    }

    /** $image = null keeps the current image. */
    public function update(int $id, string $content, ?string $image): void
    {
        if ($image !== null) {
            $st = $this->db->prepare('UPDATE posts SET content = :c, image = :i WHERE id = :id');
            $st->execute([':c' => $content, ':i' => $image, ':id' => $id]);
        } else {
            $st = $this->db->prepare('UPDATE posts SET content = :c WHERE id = :id');
            $st->execute([':c' => $content, ':id' => $id]);
        }
    }

    public function removeImage(int $id): void
    {
        $st = $this->db->prepare('UPDATE posts SET image = NULL WHERE id = :id');
        $st->execute([':id' => $id]);
    }

    public function delete(int $id): void
    {
        $st = $this->db->prepare('DELETE FROM posts WHERE id = :id');
        $st->execute([':id' => $id]);
    }
}
