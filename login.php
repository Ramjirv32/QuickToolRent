<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$err = '';
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $email_val = $email;
  
  if ($email && $pass) {
    if (login_user($email, $pass)) {
      header('Location: ' . (BASE_URL ?: '/') . 'index.php'); 
      exit;
    } else {
      $err = 'Invalid email or password. Please try again.';
    }
  } else {
    $err = 'Please enter both email and password.';
  }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-8">
    <!-- Header -->
    <div class="text-center">
      <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center text-3xl mb-4 shadow-lg">
        🛠️
      </div>
      <h2 class="text-3xl font-extrabold text-gray-900">Welcome Back!</h2>
      <p class="mt-2 text-sm text-gray-600">Sign in to your account to continue</p>
    </div>

    <!-- Error Message -->
    <?php if ($err): ?>
      <div class="rounded-lg bg-red-50 border border-red-200 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-red-800"><?= e($err) ?></p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-gray-100">
      <form method="POST" class="space-y-6" action="<?= BASE_URL ?: '/' ?>login.php">
        <?= csrf_input(); ?>
        
        <div>
          <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
          <input 
            id="email" 
            name="email" 
            type="email" 
            required 
            value="<?= e($email_val) ?>"
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
            placeholder="your@email.com"
          >
        </div>

        <div>
          <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
          <input 
            id="password" 
            name="password" 
            type="password" 
            required 
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
            placeholder="Enter your password"
          >
        </div>

        <div>
          <button 
            type="submit" 
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
              </svg>
            </span>
            Sign In
          </button>
        </div>
      </form>

      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
          Don't have an account? 
          <a href="<?= BASE_URL ?: '/' ?>register.php" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">
            Sign up for free
          </a>
        </p>
      </div>

      <!-- Demo Credentials -->
      <div class="mt-6 pt-6 border-t border-gray-200">
        <p class="text-xs font-semibold text-gray-700 mb-2">🔑 Demo Accounts:</p>
        <div class="space-y-1 text-xs text-gray-600">
          <p>👤 Borrower: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">charlie.borrower@example.com</span> / <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">password</span></p>
          <p>🏪 Owner: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">alice.owner@example.com</span> / <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">password</span></p>
          <p>👨‍💼 Admin: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">admin@example.com</span> / <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">admin123</span></p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
