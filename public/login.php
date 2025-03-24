<?php
require_once '../db/connect.php';
require_once '../classes/user.php';

$user = new User($pdo);

if ($user->isLoggedIn()) {
    header("Location: profile.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        if (!$user->login($email, $password)) {
            $error = "Invalid User Name or Password";
        }
    }
}

include 'components/header.php';
?>

<script src="/assets/user_forms.js"></script>

<div class="form-container">
    <form action="/login.php" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
        <button type="button" id="togglePassword">Show Password</button>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</div>

<?php include 'components/footer.php';
