<?php
require_once '../db/connect.php';
require_once 'audit.php';

class Comment
{
    private PDO $pdo;
    private Audit $audit;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->audit = new Audit($pdo);
    }

    /**
     * Create a comment.
     * 
     * @param int $postId
     * @param string $content
     * @return bool
     */
    public function createComment(int $postId, string $content): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Check if the post belongs to the logged-in user
        $stmt = $this->pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post || $post['user_id'] != $_SESSION['user_id']) {
            return false;  // User can only comment on their own posts
        }

        $stmt = $this->pdo->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");

        if ($stmt->execute([$_SESSION['user_id'], $postId, $content])) {
            $this->audit->logAuditTrail($_SESSION['user_id'], "Created a comment at " . date('h:iA'));
            return true;
        }

        return false;
    }

    /**
     * Get comments by post id.
     * 
     * @param int $postId
     * @return array
     */
    public function getCommentsByPostId(int $postId): array
    {
        if (!isset($_SESSION['user_id'])) {
            return [];
        }

        $stmt = $this->pdo->prepare("
            SELECT comments.id, comments.user_id, comments.content, comments.created_at, users.name AS author
            FROM comments 
            JOIN users ON comments.user_id = users.id
            WHERE comments.post_id = ?
            AND comments.user_id = ?
            ORDER BY comments.created_at ASC
        ");

        $stmt->execute([$postId, $_SESSION['user_id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a comment.
     * 
     * @param int $commentId
     * @param string $content
     * @return bool
     */
    public function updateComment(int $commentId, string $content): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Ensure the comment belongs to the logged-in user
        $stmt = $this->pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($comment && $comment['user_id'] === $_SESSION['user_id']) {
            $stmt = $this->pdo->prepare("UPDATE comments SET content = ? WHERE id = ?");
            if ($stmt->execute([$content, $commentId])) {
                $this->audit->logAuditTrail($_SESSION['user_id'], "Updated comment at " . date('h:iA'));
                return true;
            }
        }

        return false;
    }

    /**
     * Delete a comment.
     * 
     * @param int $commentId
     * @return bool
     */
    public function deleteComment(int $commentId): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Ensure the comment belongs to the logged-in user
        $stmt = $this->pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($comment && $comment['user_id'] === $_SESSION['user_id']) {
            $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
            if ($stmt->execute([$commentId])) {
                $this->audit->logAuditTrail($_SESSION['user_id'], "Deleted a comment at " . date('h:iA'));
                return true;
            }
        }

        return false;
    }
}
