<?php
require_once '../db/connect.php';
require_once '../classes/comment.php';
require_once '../classes/post.php';
require_once '../classes/user.php';

$user = new User($pdo);
$post = new Post($pdo);

$user->requireLogin();

$userId = $_SESSION['user_id'];

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    $title = trim($_POST['title']);

    if (!empty($content)) {
        if ($post->createPost($title, $content)) {
            header("Location: view_posts.php"); // Refresh page
            exit;
        }
    } else {
        $error = "Post content cannot be empty.";
    }
}

include 'components/header.php';
?>

<div class="form-container">
    <form method="POST">
        <div class="form-title">
            <h2>Add a New Post</h2>
        </div>
        <hr>
        <div class="form-group">
            <label>Title: <input type="text" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required></label>
        </div>
        <div class="form-group">
            <label>Content: </label>
            <textarea name="content" rows="5" required><?= htmlspecialchars($content ?? '') ?></textarea>
        </div>
        <button type="submit">Submit</button>
    </form>
</div>

<?php include 'components/footer.php';
