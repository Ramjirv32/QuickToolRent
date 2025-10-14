<?php
require_once __DIR__ . '/db.php';

function current_user(): ?array {
  return $_SESSION['user'] ?? null;
}
function require_login(): void {
  if (!current_user()) {
    header('Location: ' . (BASE_URL ?: '/') . 'login.php');
    exit;
  }
}
function is_owner(): bool {
  return (current_user()['role'] ?? '') === 'owner';
}
function is_admin(): bool {
  return (current_user()['role'] ?? '') === 'admin';
}
function require_role(string $role): void {
  if ((current_user()['role'] ?? '') !== $role) {
    header('Location: ' . (BASE_URL ?: '/') . 'login.php');
    exit;
  }
}

function login_user(string $email, string $password): bool {
  $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
  $stmt->execute([$email]);
  $user = $stmt->fetch();
  if ($user) {
    // Special-case admin password for demo as requested
    if (($user['role'] ?? '') === 'admin' && $email === 'admin@example.com' && $password === 'admin123') {
      $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
      ];
      return true;
    }
    if (password_verify($password, $user['password_hash'])) {
      $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
      ];
      return true;
    }
  }
  return false;
}
function logout_user(): void {
  unset($_SESSION['user']);
}
