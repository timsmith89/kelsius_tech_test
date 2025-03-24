<?php
require_once 'vendor/autoload.php';  // Load Composer dependencies (Faker)
require_once 'db/connect.php';        // Database connection

$faker = Faker\Factory::create();

$recordCount = isset($argv[1]) ? (int)$argv[1] : 10;  // Default to 10 records if no parameter is provided

// Insert users
$users = [];
for ($i = 0; $i < $recordCount; $i++) {
    $name = $faker->name;
    $email = $faker->unique()->safeEmail;
    $passwordHash = password_hash('password', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $passwordHash]);

    $users[] = $pdo->lastInsertId();
}

// Insert posts
$posts = [];
$userPostMap = [];  // Map to associate users with their posts
foreach ($users as $userId) {
    $userPostMap[$userId] = [];

    for ($j = 0; $j < rand(1, 5); $j++) {  // Each user gets 1-5 posts
        $title = $faker->sentence;
        $content = $faker->paragraph;

        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $title, $content]);

        $postId = $pdo->lastInsertId();
        $posts[] = $postId;
        $userPostMap[$userId][] = $postId;  // Map the post to the user
    }
}

// Ensure all posts have at least one comment
foreach ($posts as $postId) {
    $creatorId = null;

    // Find the creator of the post
    foreach ($userPostMap as $userId => $userPosts) {
        if (in_array($postId, $userPosts)) {
            $creatorId = $userId;
            break;
        }
    }

    $createdComment = false;

    // 50% chance for the creator to comment
    if ($creatorId && rand(0, 1) === 1) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$creatorId, $postId, $faker->sentence]);
        $createdComment = true;
    }

    // Add 1-4 random comments from other users
    $numComments = rand(1, 4);
    for ($k = 0; $k < $numComments; $k++) {
        $randomUserId = $users[array_rand($users)];

        // Avoid duplicate creator comments
        if ($randomUserId !== $creatorId) {
            $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$randomUserId, $postId, $faker->sentence]);
            $createdComment = true;
        }
    }

    // 💡 **Guarantee at least one comment**
    if (!$createdComment) {
        $fallbackUser = $users[array_rand($users)];  // Random fallback user
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$fallbackUser, $postId, $faker->sentence]);
    }
}

// Insert audit logs
foreach ($users as $userId) {
    for ($l = 0; $l < rand(1, 3); $l++) {  // 1-3 audit log entries per user
        $action = $faker->randomElement(["Updated profile", "Deleted record", "Created post", "Added comment"]);
        $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action) VALUES (?, ?)");
        $stmt->execute([$userId, $action]);
    }
}

echo "Database populated with $recordCount users, posts, comments, and audit logs.\n";
