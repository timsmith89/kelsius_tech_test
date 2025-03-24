<?php
require_once '../db/connect.php';
require_once '../classes/user.php';

$audit = new Audit($pdo);
$user = new User($pdo);
$user->requireLogin();
$userId = $_SESSION['user_id'];
$auditLog = $audit->getAuditTrail($userId);

// Initialize message variables
if (!isset($_SESSION['success'])) {
    $_SESSION['success'] = '';
}
if (!isset($_SESSION['error'])) {
    $_SESSION['error'] = '';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid input. Please enter a valid name and email.";
    } else if ($user->updateUserProfile($name, $email)) {
        $_SESSION['success'] = "Profile updated successfully.";
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
    } else {
        $_SESSION['error'] = "Failed to update profile.";
    }

    // Handle AJAX with JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['redirect' => 'profile.php']);
        exit();
    }

    // Fallback for standard form submission
    header("Location: profile.php");
    exit();
}

include 'components/header.php';
?>

<script src="/assets/user_forms.js"></script>

<div class="form-container">
    <form id="update-profile" action="profile.php" method="POST">
        <div class="form-group">
            <h2>Manage Profile</h2>
        </div>
        <hr>

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" required>
        </div>
        <button type="submit">Update Profile</button>

        <br><br>

        <!-- Audit Log as a List -->
        <h2>Audit Log</h2>
        <hr>
        <div class="audit-log">
            <?php if (!empty($auditLog)): ?>
                <ul style="list-style: none; padding: 0;">
                    <?php foreach ($auditLog as $log): ?>
                        <li style="margin-bottom: 15px; padding: 10px; background: #f4f4f4; border-radius: 5px;">
                            <?= htmlspecialchars($log['action']) ?><br>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No audit log entries found.</p>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php include 'components/footer.php'; ?>