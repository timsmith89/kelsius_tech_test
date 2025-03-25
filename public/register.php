<?php
require_once '../db/connect.php';
require_once '../classes/user.php';

$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else if ($user->register($name, $email, $password, $role)) {
        $user->login($email, $password);
        header("Location: profile.php");  // Redirect on successful registration
        exit;
    } else {
        $error = "Email already in use.";
    }
}

include 'components/header.php';
?>

<div class="form-container">
    <form action="/register.php" method="POST">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name">
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email">
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password">
        </div>
        <div class="form-group">
            <label>Role:</label>
            <select name="role" class="styled-dropdown" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit">Register</button>
        <?php if (!empty($error) || !empty($success)): ?>
            <p style="color: <?= !empty($error) ? 'red' : 'green'; ?>;">
                <?= !empty($error) ? $error : $success; ?>
            </p>
        <?php endif; ?>
        <br><br>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</div>

<?php include 'components/footer.php';
