<?php
require_once '../db/connect.php';
require_once '../classes/comment.php';
require_once '../classes/post.php';
require_once '../classes/user.php';

$comment = new Comment($pdo);
$post = new Post($pdo);
$user = new User($pdo);

$user->requireLogin();

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $postId = $_POST['delete_post'];

    if ($post->deletePost($postId)) {
        header("Location: posts.php");
        exit;
    }
}

include 'components/header.php';

// Fetch all posts with author names
$posts = $post->getAllPosts(); ?>

<div class="form-container">
    <form>
        <div class="form-title">
            <h2>View Posts</h2>
        </div>
        <hr>
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post):
                $commentsCount = count($comment->getCommentsByPostId($post["id"]));
            ?>
                <div class="post">
                    <b>Title:</b> <?= nl2br(htmlspecialchars($post["title"])); ?><br>
                    <b>Content:</b> <?= nl2br(htmlspecialchars($post["content"])); ?><br>
                    <b>Number of Comments:</b> <?= $commentsCount; ?><br>
                    <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                        <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                            <div class="button-group">
                                <button type="submit"><a href="manage_post.php?id=<?= $post['id']; ?>" class="button">Manage Post</a></button>
                                <button type="submit"><a href="manage_comments.php?id=<?= $post['id']; ?>" class="button">Manage Comments</a></button>
                            </div>
                        <?php endif; ?>
                        <hr>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts yet.</p>
        <?php endif; ?>
    </form>
</div>

<?php include 'components/footer.php';
