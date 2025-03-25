<?php
require_once '../db/connect.php';
require_once '../classes/comment.php';
require_once '../classes/post.php';
require_once '../classes/user.php';

$audit = new Audit($pdo);
$user = new User($pdo);
$user->requireLogin();
$userId = $_SESSION['user_id'];
$auditLog = $audit->getAuditTrail($userId);

include 'components/header.php'; ?>

<div class="form-container">
    <form>
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

<?php include 'components/footer.php';
