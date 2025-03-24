<?php
require_once '../db/connect.php';
require_once 'audit.php';

class Post
{
    private PDO $pdo;
    private Audit $audit;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->audit = new Audit($pdo);
    }

    /**
     * Create a new post.
     * 
     * @param string $title
     * @param string $content
     * @return bool
     */
    public function createPost(string $title, string $content): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $stmt = $this->pdo->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");

        if ($stmt->execute([$_SESSION['user_id'], $title, $content])) {
            $this->audit->logAuditTrail($_SESSION['user_id'], "Created a post at " . date('h:iA'));
            return true;
        }

        return false;
    }

    /**
     * Get all posts by user ID.
     * 
     * @param int $userId
     * @return array
     */
    public function getAllPostsByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT posts.id, posts.user_id, posts.title, posts.content, posts.created_at, users.name AS author
            FROM posts 
            JOIN users ON posts.user_id = users.id
            WHERE users.id = ?
            ORDER BY posts.created_at DESC
        ");

        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single post by ID and user ID.
     * 
     * @param int $postId
     * @param int $userId
     * @return array|null
     */
    public function getPostById(int $postId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT posts.id, posts.user_id, posts.title, posts.content, posts.created_at, users.name AS author
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.id = ? AND posts.user_id = ?
        ");

        $stmt->execute([$postId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Update a post.
     * 
     * @param int $postId
     * @param string $title
     * @param string $content
     * @return bool
     */
    public function updatePost(int $postId, string $title, string $content): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Ensure the post belongs to the logged-in user
        $stmt = $this->pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post && $post['user_id'] === $_SESSION['user_id']) {
            $stmt = $this->pdo->prepare("UPDATE posts SET title = ?, content = ?, created_at = NOW() WHERE id = ?");

            if ($stmt->execute([$title, $content, $postId])) {
                $this->audit->logAuditTrail($_SESSION['user_id'], "Updated a post at " . date('h:iA'));
                return true;
            }
        }

        return false;
    }

    /**
     * Delete a post.
     * 
     * @param int $postId
     * @param int $userId
     * @return bool
     */
    public function deletePost(int $postId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");

        if ($stmt->execute([$postId, $userId])) {
            $this->audit->logAuditTrail($_SESSION['user_id'], "Deleted a post at " . date('h:iA'));
            return true;
        }

        return false;
    }
}
