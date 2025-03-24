<?php

require_once '../db/connect.php';
require_once '../classes/user.php';
include 'header.php';

$user = new User($pdo);

if ($user->isLoggedIn()) {
    header("Location: profile.php");
}
?>

<div class="text-center">
    <p>This is a simple PHP application demonstrating user authentication and CRUD functionality.</p>
    <p><a href="login.php">Login</a> or <a href="register.php">Register</a> to get started.</p>
</div>

<?php include 'footer.php'; ?>