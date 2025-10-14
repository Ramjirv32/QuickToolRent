<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/helpers.php';

$err = ''; 
$ok = '';
$form_data = ['name' => '', 'email' => '', 'phone' => '', 'role' => 'borrower'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $pass = $_POST['password'] ?? '';
  $role = ($_POST['role'] ?? 'borrower') === 'owner' ? 'owner' : 'borrower';
  
  $form_data = ['name' => $name, 'email' => $email, 'phone' => $phone, 'role' => $role];
  
  if ($name && $email && $pass) {
    try {
      $stmt = db()->prepare("INSERT INTO users (name,email,phone,password_hash,role) VALUES (?,?,?,?,?)");
      $stmt->execute([$name, $email, $phone, password_hash($pass, PASSWORD_BCRYPT), $role]);
      $ok = 'Account created successfully! Redirecting to login...';
      echo '<script>setTimeout(function(){ window.location.href="' . (BASE_URL ?: '/') . 'login.php"; }, 2000);</script>';
    } catch (Exception $e) {
      $err = 'Unable to register. Email may already be in use.';
    }
  } else {
    $err = 'Please fill all required fields.';
  }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-xl w-full space-y-8">
    <!-- Header -->
    <div class="text-center">
      <div class="mx-auto h-16 w-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-3xl mb-4 shadow-lg">
        🚀
      </div>
      <h2 class="text-3xl font-extrabold text-gray-900">Create Your Account</h2>
      <p class="mt-2 text-sm text-gray-600">Join our community and start renting tools today!</p>
    </div>

    <!-- Success Message -->
    <?php if ($ok): ?>
      <div class="rounded-lg bg-green-50 border border-green-200 p-4 animate-pulse">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800"><?= e($ok) ?></p>
          </div>
        </div>
      </div>
    <?php endif; ?>

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

    <!-- Registration Form -->
    <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-gray-100">
      <form method="POST" class="space-y-5" action="<?= BASE_URL ?: '/' ?>register.php">
        <?= csrf_input(); ?>
        
        <div>
          <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
          <input 
            id="name" 
            name="name" 
            type="text" 
            required 
            value="<?= e($form_data['name']) ?>"
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
            placeholder="John Doe"
          >
        </div>

        <div>
          <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
          <input 
            id="email" 
            name="email" 
            type="email" 
            required 
            value="<?= e($form_data['email']) ?>"
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
            placeholder="your@email.com"
          >
        </div>

        <div>
          <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
          <input 
            id="phone" 
            name="phone" 
            type="tel" 
            value="<?= e($form_data['phone']) ?>"
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
            placeholder="+1 (555) 123-4567"
          >
        </div>

        <div>
          <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
          <input 
            id="password" 
            name="password" 
            type="password" 
            required 
            minlength="6"
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
            placeholder="Min. 6 characters"
          >
        </div>

        <div>
          <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">I want to *</label>
          <select 
            id="role" 
            name="role" 
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
          >
            <option value="borrower" <?= $form_data['role'] === 'borrower' ? 'selected' : '' ?>>🔧 Borrow Tools (Renter)</option>
            <option value="owner" <?= $form_data['role'] === 'owner' ? 'selected' : '' ?>>🏪 Lend My Tools (Owner)</option>
          </select>
          <p class="mt-1 text-xs text-gray-500">Owners can list their tools for rent. Borrowers can rent tools from others.</p>
        </div>

        <div>
          <button 
            type="submit" 
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
              </svg>
            </span>
            Create Account
          </button>
        </div>
      </form>

      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
          Already have an account? 
          <a href="<?= BASE_URL ?: '/' ?>login.php" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">
            Sign in here
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
