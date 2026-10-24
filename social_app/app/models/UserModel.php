<?php
/**
 * UserModel - database logic for the `users` table only.
 */
class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findById(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $st = $this->db->prepare('SELECT * FROM users WHERE username = :u');
        $st->execute([':u' => $username]);
        return $st->fetch() ?: null;
    }

    public function usernameExists(string $username, ?int $exceptId = null): bool
    {
        $st = $this->db->prepare('SELECT id FROM users WHERE username = :u AND id <> :id');
        $st->execute([':u' => $username, ':id' => $exceptId ?? 0]);
        return (bool) $st->fetch();
    }

    public function create(string $username, string $hash, string $fullName, ?string $image): int
    {
        $st = $this->db->prepare(
            'INSERT INTO users (username, password, full_name, profile_image)
             VALUES (:u, :p, :f, :i)'
        );
        $st->execute([':u' => $username, ':p' => $hash, ':f' => $fullName, ':i' => $image]);
        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $fullName, string $bio, ?string $image): void
    {
        if ($image !== null) {
            $st = $this->db->prepare('UPDATE users SET full_name = :f, bio = :b, profile_image = :i WHERE id = :id');
            $st->execute([':f' => $fullName, ':b' => $bio, ':i' => $image, ':id' => $id]);
        } else {
            $st = $this->db->prepare('UPDATE users SET full_name = :f, bio = :b WHERE id = :id');
            $st->execute([':f' => $fullName, ':b' => $bio, ':id' => $id]);
        }
    }

    public function postCount(int $id): int
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM posts WHERE user_id = :id');
        $st->execute([':id' => $id]);
        return (int) $st->fetchColumn();
    }

    /** Search users by name or username. */
    public function search(string $term, int $limit = 20): array
    {
        $like = '%' . $this->escapeLike($term) . '%';
        $st = $this->db->prepare(
            'SELECT id, username, full_name, bio, profile_image FROM users
             WHERE username LIKE :q1 OR full_name LIKE :q2
             ORDER BY full_name ASC LIMIT :lim'
        );
        $st->bindValue(':q1', $like);
        $st->bindValue(':q2', $like);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    /** Get suggested users (excluding current user) */
    public function getSuggestedUsers(int $excludeUserId, int $limit = 5): array
    {
        $st = $this->db->prepare(
            'SELECT id, username, full_name, bio, profile_image 
             FROM users 
             WHERE id <> :id 
             ORDER BY id DESC 
             LIMIT :lim'
        );
        $st->bindValue(':id', $excludeUserId, PDO::PARAM_INT);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    /** Get active / online contact list */
    public function getContacts(int $excludeUserId, int $limit = 8): array
    {
        $st = $this->db->prepare(
            'SELECT id, username, full_name, profile_image 
             FROM users 
             WHERE id <> :id 
             ORDER BY username ASC 
             LIMIT :lim'
        );
        $st->bindValue(':id', $excludeUserId, PDO::PARAM_INT);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    private function escapeLike(string $s): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $s);
    }
}

