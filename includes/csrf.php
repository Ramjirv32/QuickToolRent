<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

function csrf_token(): string {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function csrf_field(): string {
  $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
  return '<input type="hidden" name="csrf_token" value="' . $t . '">';
}

function csrf_input(): string {
  return csrf_field();
}

function verify_csrf(): void {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sent = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $sent)) {
      http_response_code(400);
      die('Invalid CSRF token');
    }
  }
}

function csrf_validate(): bool {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return true;
  $sent = $_POST['csrf_token'] ?? '';
  return hash_equals($_SESSION['csrf_token'] ?? '', $sent);
}
