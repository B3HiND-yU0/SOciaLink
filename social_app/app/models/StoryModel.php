<?php
/**
 * StoryModel - database logic for the `stories` table.
 */
class StoryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Get recent stories (within last 24 hours or latest active).
     */
    public function allRecent(int $limit = 20): array
    {
        $st = $this->db->prepare(
            'SELECT s.*, u.username, u.full_name, u.profile_image 
             FROM stories s
             JOIN users u ON u.id = s.user_id
             ORDER BY s.created_at DESC
             LIMIT :lim'
        );
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function create(int $userId, ?string $caption, ?string $image, string $bgGradient = 'ocean-blue'): int
    {
        $st = $this->db->prepare(
            'INSERT INTO stories (user_id, caption, image, bg_gradient)
             VALUES (:uid, :cap, :img, :grad)'
        );
        $st->execute([
            ':uid'  => $userId,
            ':cap'  => $caption !== '' ? $caption : null,
            ':img'  => $image,
            ':grad' => $bgGradient ?: 'ocean-blue',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare(
            'SELECT s.*, u.username, u.full_name, u.profile_image 
             FROM stories s
             JOIN users u ON u.id = s.user_id
             WHERE s.id = :id'
        );
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function delete(int $id, int $userId): bool
    {
        $st = $this->db->prepare('DELETE FROM stories WHERE id = :id AND user_id = :uid');
        return $st->execute([':id' => $id, ':uid' => $userId]);
    }
}
