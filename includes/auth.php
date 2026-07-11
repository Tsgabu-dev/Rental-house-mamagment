<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function currentUser(): ?string
{
    return $_SESSION['user_name'] ?? null;
}

function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function isAdmin(): bool
{
    return ($_SESSION['user_role'] ?? '') === 'admin';
}

function root(string $path = ''): string
{
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    if (basename($scriptDir) === 'admin') {
        $scriptDir = dirname($scriptDir);
    }

    $base = rtrim($scriptDir, '/');
    if ($base === '') {
        return '/' . ltrim($path, '/');
    }

    return $base . '/' . ltrim($path, '/');
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . root('login.php'));
        exit;
    }
}

function loginUser(string $identifier, string $password): bool
{
    global $conn;

    $identifier = trim($identifier);
    if ($identifier === '') {
        return false;
    }

    $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE email = ? OR username = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_role'] = $user['role'] ?? 'landlord';
        return true;
    }
    return false;
}

function registerUser(string $username, string $email, string $password, string $phone = '', string $role = 'landlord'): bool
{
    global $conn;

    $username = trim($username);
    $email = trim($email);
    $phone = trim($phone);
    if ($username === '' || $email === '' || $password === '') {
        return false;
    }

    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return false;
    }
    $stmt->close();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (username, email, password, phone_number, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('sssss', $username, $email, $passwordHash, $phone, $role);
    $success = $stmt->execute();
    if (!$success) {
        $stmt->close();
        return false;
    }
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['user_name'] = $username;
    $_SESSION['user_role'] = $role;
    $stmt->close();
    return true;
}
