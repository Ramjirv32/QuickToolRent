<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin') {
  header('Location: dashboard.php');
  exit;
}

$pdo = db();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email && $password) {
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['role'] === 'admin') {
      $stored = $user['password_hash'] ?? '';
      $ok = false;

      // Prefer bcrypt if hash looks like bcrypt, else fallback to SHA-256 hex compare
      if (is_string($stored) && preg_match('/^\$2[ayb]\$\d{2}\$/', $stored)) {
        $ok = password_verify($password, $stored);
      } else {
        $ok = hash_equals($stored, hash('sha256', $password));
      }

      if ($ok) {
        $_SESSION['user'] = [
          'id' => (int)$user['id'],
          'name' => $user['name'],
          'email' => $user['email'],
          'role' => $user['role']
        ];
        header('Location: dashboard.php');
        exit;
      }
    }
    $error = 'Invalid credentials or not an admin.';
  } else {
    $error = 'Please enter email and password.';
  }
}
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login • Tool Rental</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow p-6">
      <h1 class="text-2xl font-semibold text-center text-slate-900">Admin Login</h1>
      <p class="text-sm text-slate-500 mt-1 text-center">Use admin@example.com / admin123</p>
      <?php if ($error): ?>
        <div class="mt-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form class="mt-6 space-y-4" method="post" action="">
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input name="email" type="email" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" placeholder="admin@example.com">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <input name="password" type="password" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" placeholder="••••••••">
        </div>
        <button type="submit" class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white py-2.5 font-medium">Sign in</button>
      </form>
    </div>
  </div>
</body>
</html>
