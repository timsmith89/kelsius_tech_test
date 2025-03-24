<?php
require_once '../db/connect.php';

class Audit
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Log audit trail.
     * 
     * @param int $userId
     * @param string $action
     * @return void
     */
    public function logAuditTrail(int $userId, string $action): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO audit_log (user_id, action) VALUES (?, ?)");
        $stmt->execute([$userId, $action]);
    }
}
