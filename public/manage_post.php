<?php
require_once '../db/connect.php';
require_once '../classes/comment.php';
require_once '../classes/post.php';
require_once '../classes/user.php';

$post = new Post($pdo);
$user = new User($pdo);
$user->requireLogin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_GET['id'];
    $action = $_POST['action'];

    if ($action === 'edit') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (!empty($title) && !empty($content)) {
            if ($post->updatePost($postId, $title, $content)) {
                header("Location: view_posts.php");
            } else {
                $error = "Failed to update post.";
            }
        } else {
            $error = "Title and content cannot be empty.";
        }
    } else if ($action === 'delete') {
        if ($post->deletePost($postId)) {
            header("Location: view_posts.php");
            exit();
        } else {
            $error = "Failed to delete post.";
        }
    }
}

include 'components/header.php';

// Check if user owns post and get comments
$getPost = $post->getPostById($_GET["id"]);

if ($getPost): ?>
    <div class="form-container">
        <form method="POST">
            <div class="form-title">
                <h2>Manage Post</h2>
            </div>
            <hr>
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" value="<?= htmlspecialchars($getPost['title']) ?>" required>
            </div>
            <div class="form-group">
                <label>Content:</label>
                <textarea name="content" required><?= htmlspecialchars($getPost['content']) ?></textarea>
            </div>
            <div class="button-group">
                <button type="submit" name="action" value="edit">Save Changes</button>
                <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this post?')">Delete Post</button>
            </div>
        </form>
    </div>
<?php endif;

include 'components/footer.php';
