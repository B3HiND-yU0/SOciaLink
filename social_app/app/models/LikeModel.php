<?php
/**
 * LikeModel - database logic for the `likes` table with reaction types.
 */
class LikeModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getReaction(int $postId, int $userId): ?string
    {
        $st = $this->db->prepare('SELECT reaction_type FROM likes WHERE post_id = :p AND user_id = :u');
        $st->execute([':p' => $postId, ':u' => $userId]);
        $val = $st->fetchColumn();
        return $val !== false ? (string) $val : null;
    }

    public function hasLiked(int $postId, int $userId): bool
    {
        return $this->getReaction($postId, $userId) !== null;
    }

    public function count(int $postId): int
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM likes WHERE post_id = :p');
        $st->execute([':p' => $postId]);
        return (int) $st->fetchColumn();
    }

    /**
     * Get list of unique reaction types used on this post (up to 3 most popular).
     */
    public function topReactions(int $postId): array
    {
        $st = $this->db->prepare(
            'SELECT reaction_type 
             FROM likes 
             WHERE post_id = :p 
             GROUP BY reaction_type 
             ORDER BY COUNT(*) DESC 
             LIMIT 3'
        );
        $st->execute([':p' => $postId]);
        $types = $st->fetchAll(PDO::FETCH_COLUMN);
        return !empty($types) ? $types : ['like'];
    }

    /**
     * React with a specific type ('like', 'love', 'haha', 'wow', 'sad', 'fire').
     * If same reaction is clicked again, removes it (un-react).
     * If different reaction is clicked, updates it.
     */
    public function react(int $postId, int $userId, string $type = 'like'): array
    {
        $allowed = ['like', 'love', 'haha', 'wow', 'sad', 'fire'];
        if (!in_array($type, $allowed, true)) {
            $type = 'like';
        }

        $existing = $this->getReaction($postId, $userId);

        if ($existing !== null) {
            if ($existing === $type) {
                // Same reaction clicked again -> remove
                $st = $this->db->prepare('DELETE FROM likes WHERE post_id = :p AND user_id = :u');
                $st->execute([':p' => $postId, ':u' => $userId]);
                return [
                    'liked'        => false,
                    'reaction'     => null,
                    'count'        => $this->count($postId),
                    'topReactions' => $this->topReactions($postId),
                ];
            } else {
                // Different reaction -> update
                $st = $this->db->prepare('UPDATE likes SET reaction_type = :r WHERE post_id = :p AND user_id = :u');
                $st->execute([':r' => $type, ':p' => $postId, ':u' => $userId]);
                return [
                    'liked'        => true,
                    'reaction'     => $type,
                    'count'        => $this->count($postId),
                    'topReactions' => $this->topReactions($postId),
                ];
            }
        }

        // New reaction
        $st = $this->db->prepare('INSERT INTO likes (post_id, user_id, reaction_type) VALUES (:p, :u, :r)');
        $st->execute([':p' => $postId, ':u' => $userId, ':r' => $type]);
        return [
            'liked'        => true,
            'reaction'     => $type,
            'count'        => $this->count($postId),
            'topReactions' => $this->topReactions($postId),
        ];
    }

    /**
     * Legacy toggle for backward compatibility.
     */
    public function toggle(int $postId, int $userId): bool
    {
        $res = $this->react($postId, $userId, 'like');
        return $res['liked'];
    }
}
