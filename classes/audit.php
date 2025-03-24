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
     * Get audit trail.
     * 
     * @return array
     */
    public function getAuditTrail(): array
    {
        $stmt = $this->pdo->prepare("SELECT action FROM audit_log WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Log audit trail.
     *
     * @param string $action
     * @return void
     */
    public function logAuditTrail(string $action): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO audit_log (user_id, action) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $action]);
    }
}
