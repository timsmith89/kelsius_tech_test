<?php
require_once '../db/connect.php';
require_once 'audit.php';

class User
{
    private PDO $pdo;
    private Audit $audit;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->audit = new Audit($pdo);
    }

    /**
     * Check if an email already exists (for registration validation).
     * 
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ? true : false;
    }

    /**
     * Get user details by ID.
     * 
     * @param int $userId
     * @return array|null
     */
    public function getUserById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Check if the user is logged in.
     * 
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Log the user in.
     * 
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login(string $email, string $password): bool
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $this->getUserRole($user['id']);
            $this->audit->logAuditTrail("Logged in at " . date('h:iA'));
            header("Location: profile.php");
            exit;
        }

        return false;
    }

    /**
     * Log the user out.
     * 
     * @return void
     */
    public function logout(): void
    {
        session_destroy();
        header("Location: /login.php");
        exit();
    }

    /**
     * Register a new account.
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function register(string $name, string $email, string $password, string $role): bool
    {
        if ($this->emailExists($email)) {
            return false;  // Email already exists
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");

        if ($stmt->execute([$name, $email, $passwordHash])) {
            $userId = $this->pdo->lastInsertId();

            // Assign the user role to the new user
            $roleStmt = $this->pdo->prepare("
                INSERT INTO user_roles (user_id, role_id)
                VALUES (?, (SELECT id FROM roles WHERE name = ? LIMIT 1))
            ");

            return $roleStmt->execute([$userId, $role]);
        }

        return false;
    }

    /**
     * Protect pages that require authentication.
     * 
     * @return void
     */
    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            header("Location: /login.php");
            exit();
        }
    }

    /**
     * Update user profile.
     * 
     * @param string $name
     * @param string $email
     * @return bool
     */
    public function updateUserProfile(string $name, string $email): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userId = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);

        if ($stmt->fetch()) {
            return false;  // Email already taken by another user
        }

        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");

        if ($stmt->execute([$name, $email, $userId])) {
            $_SESSION["user_name"] = $name;
            $_SESSION["user_email"] = $email;
            $this->audit->logAuditTrail("Profile updated at " . date('h:iA'));
            return true;
        }

        return false;
    }

    /**
     * Get user role.
     * 
     * @param int $userId
     * @return string
     */
    public function getUserRole(int $userId): string
    {
        $stmt = $this->pdo->prepare("
            SELECT r.name 
            FROM roles r
            JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
}
