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

include 'components/header.php';

// Check if the user is an admin
$isAdmin = $user->getUserRole($userId) === "admin";

// Validate post ID
$postId = $_GET['id'] ?? null;
if (!$postId || !$post->getPostById($postId)) {
    echo "<p>Invalid post ID or no permission to manage comments.</p>";
    include '../components/footer.php';
    exit;
}

// Handle comment actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = $_POST['comment_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($action === 'add') {
        $newComment = trim($_POST['new_comment']);

        if (!empty($newComment)) {
            $comments->createComment($postId, $newComment);
        }
    } else if ($action === 'edit') {
        $content = trim($_POST['content']);
        if (!empty($content)) {
            $comments->updateComment($commentId, $content);
        }
    } else if ($action === 'delete') {
        $comments->deleteComment($commentId);
    }
}

$getComments = $comments->getCommentsByPostId($postId);
?>

<div class="form-container">
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php elseif (!empty($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" class="comment-form">
        <h2>Manage Comments</h2>
        <hr>
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label for="new_comment">Add a New Comment:</label>
            <textarea name="new_comment" id="new_comment" rows="4"></textarea>
        </div>
        <button type="submit">Add Comment</button>
        <hr>
        <?php if (!empty($getComments)): ?>
            <form method="POST" class="comment-form">
                <?php foreach ($getComments as $c): ?>
                    <input type="hidden" name="comment_id" value="<?= htmlspecialchars($c['id']) ?>">

                    <div class="form-group">
                        <label>Author:</label><?= htmlspecialchars($c['author']) ?>
                    </div>

                    <div class="form-group">
                        <label>Comment:</label>
                        <textarea name="content" class="form-control <?= $isAdmin ? '' : 'greyed-out' ?>"
                            <?= $isAdmin ? '' : 'readonly' ?>
                            required><?= htmlspecialchars($c['content']) ?></textarea>
                    </div>

                    <!-- Only show these buttons to admins -->
                    <?php if ($isAdmin): ?>
                        <div class="button-group">
                            <button type="submit" name="action" value="edit">Save Changes</button>
                            <button type="submit" name="action" value="delete"
                                onclick="return confirm('Are you sure you want to delete this comment?')">Delete Comment</button>
                        </div>
                    <?php endif; ?>
                    <hr>
                <?php endforeach; ?>
            </form>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
    </form>
</div>

<?php include 'components/footer.php'; ?>