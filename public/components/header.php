<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelsius Tech Test</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <div class="container">
        <header class="header">
            <h1>Kelsius Tech Test</h1>
            <?php
            echo isset($pageTitle) ? "<h2>" . htmlspecialchars($pageTitle) . "</h2>" : "";
            if (isset($_SESSION['user_id'])) : ?>
                <nav>
                    <ul>
                        <li><a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">Profile</a></li>
                        <li><a href="audit_log.php" class="<?= basename($_SERVER['PHP_SELF']) === 'audit_log.php' ? 'active' : '' ?>">Audit Log</a></li>
                        <li><a href="add_post.php" class="<?= basename($_SERVER['PHP_SELF']) === 'add_post.php' ? 'active' : '' ?>">Add New Post</a></li>
                        <li><a href="view_posts.php" class="<?= basename($_SERVER['PHP_SELF']) === 'view_posts.php' ? 'active' : '' ?>">View Posts</a></li>
                        <li><a href="logout.php" class="<?= basename($_SERVER['PHP_SELF']) === 'logout.php' ? 'active' : '' ?>">Logout</a></li>
                    </ul>
                </nav>
            <?php endif; ?>
        </header>
        <main>