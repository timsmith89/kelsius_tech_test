<?php
require_once '../db/connect.php';
require_once '../classes/comment.php';
require_once '../classes/post.php';
require_once '../classes/user.php';

$comments = new Comment($pdo);
$post = new Post($pdo);
$user = new User($pdo);

$user->requireLogin();
$userId = $_SESSION['user_id'];

// Check if the user is an admin
$isAdmin = $user->getUserRole($userId) === "admin";

// Validate post ID
$postId = $_GET['id'] ?? null;

if (!$postId || !$post->getPostById($postId)) {
    header("Location: view_posts.php");
    exit();
}

// Success message variable
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'add') {
        $newComment = trim($_POST['new_comment'] ?? '');

        if (!empty($newComment)) {
            $comments->createComment($postId, $newComment);
            $successMessage = "Comment added successfully!";
        }
    } elseif ($action === 'edit' && $isAdmin) {
        if (
            !empty($_POST['comment_id']) && is_array($_POST['comment_id']) &&
            !empty($_POST['content']) && is_array($_POST['content'])
        ) {
            foreach ($_POST['comment_id'] as $index => $commentId) {
                $commentId = intval($commentId);
                $content = $_POST['content'][$index] ?? '';

                if (!empty($content) && is_string($content)) {
                    $content = trim($content);
                    $comments->updateComment($commentId, $content);
                }
            }
            $successMessage = "Comment successfully updated!";
        }
    } elseif ($action === 'delete' && $isAdmin) {
        $commentId = intval($_POST['comment_id'][0] ?? 0);

        if ($commentId) {
            $comments->deleteComment($commentId);
            $successMessage = "Comment successfully deleted!";
        }
    }
}

include 'components/header.php';
$getComments = $comments->getCommentsByPostId($postId);
?>

<div class="form-container">
    <form id="manage-comment-form" action="manage_comments.php?id=<?= $postId ?>" method="POST">
        <h2>Manage Comments</h2>
        <hr>

        <!-- Success message container for individual comments -->
        <div class="success-message" style="display: block; color: green; margin-top: 10px; margin-bottom: 10px;">
            <?= htmlspecialchars($successMessage) ?>
        </div>

        <!-- Add New Comment -->
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label for="new_comment">Add a New Comment:</label>
            <textarea name="new_comment" id="new_comment" rows="4"></textarea>
        </div>
        <button type="submit">Add Comment</button>
        <hr>

        <?php if (!empty($getComments)): ?>
            <?php foreach ($getComments as $c): ?>
                <input type="hidden" name="comment_id[]" value="<?= htmlspecialchars($c['id']) ?>">

                <div class="form-group">
                    <label>Author:</label><?= htmlspecialchars($c['author']) ?>
                </div>

                <div class="form-group">
                    <label>Comment:</label>
                    <textarea name="content[]" class="form-control <?= $isAdmin ? '' : 'greyed-out' ?>"
                        <?= $isAdmin ? '' : 'readonly' ?>
                        required><?= htmlspecialchars($c['content']) ?></textarea>
                </div>

                <?php if ($isAdmin): ?>
                    <div class="button-group">
                        <button type="submit" name="action" value="edit"
                            data-comment-id="<?= htmlspecialchars($c['id']) ?>" class="edit-btn">Save Changes</button>
                        <button type="submit" name="action" value="delete"
                            data-comment-id="<?= htmlspecialchars($c['id']) ?>" class="delete-btn"
                            onclick="return confirm('Are you sure you want to delete this comment?')">Delete Comment</button>
                    </div>
                <?php endif; ?>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
    </form>
</div>

<script src="/assets/comment.js"></script>

<?php include 'components/footer.php'; ?>