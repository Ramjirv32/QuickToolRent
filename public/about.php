<?php 
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php'; 
?>

<!-- Hero Section -->
<section class="mb-16 relative overflow-hidden rounded-2xl" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
  <div class="relative px-8 py-20 text-white text-center">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-5xl font-extrabold mb-6">
        <i class="fas fa-lightbulb text-yellow-300 mr-3"></i>How It Works
      </h1>
      <p class="text-2xl text-white/90">
        Simple, Fast, and Convenient Tool Rental in 3 Easy Steps
      </p>
    </div>
  </div>
</section>

<!-- How It Works Steps -->
<section class="mb-16">
  <div class="grid md:grid-cols-3 gap-8">
    <!-- Step 1 -->
    <div class="bg-white rounded-2xl shadow-xl p-8 text-center transform hover:scale-105 transition-all duration-300 border-2 border-orange-100">
      <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
        <span class="text-3xl font-bold text-white">1</span>
      </div>
      <div class="mb-6">
        <i class="fas fa-search text-6xl text-orange-500"></i>
      </div>
      <h3 class="text-2xl font-bold mb-4 text-gray-900">Browse & Search</h3>
      <p class="text-gray-600 leading-relaxed">
        Search through our extensive collection of tools. Find exactly what you need from power tools to ladders, electronics to furniture.
      </p>
    </div>

    <!-- Step 2 -->
    <div class="bg-white rounded-2xl shadow-xl p-8 text-center transform hover:scale-105 transition-all duration-300 border-2 border-blue-100">
      <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
        <span class="text-3xl font-bold text-white">2</span>
      </div>
      <div class="mb-6">
        <i class="fas fa-shopping-cart text-6xl text-blue-500"></i>
      </div>
      <h3 class="text-2xl font-bold mb-4 text-gray-900">Book & Pay</h3>
      <p class="text-gray-600 leading-relaxed">
        Select your rental duration, choose from multiple payment methods (Card, UPI, Wallet, COD), and confirm your booking instantly.
      </p>
    </div>

    <!-- Step 3 -->
    <div class="bg-white rounded-2xl shadow-xl p-8 text-center transform hover:scale-105 transition-all duration-300 border-2 border-green-100">
      <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
        <span class="text-3xl font-bold text-white">3</span>
      </div>
      <div class="mb-6">
        <i class="fas fa-truck text-6xl text-green-500"></i>
      </div>
      <h3 class="text-2xl font-bold mb-4 text-gray-900">Fast Delivery</h3>
      <p class="text-gray-600 leading-relaxed">
        Sit back and relax! Your tools will be delivered to your doorstep in just 30-60 minutes. Start your project right away!
      </p>
    </div>
  </div>
</section>

<!-- For Borrowers -->
<section class="mb-16 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-12">
  <div class="max-w-4xl mx-auto">
    <div class="text-center mb-10">
      <h2 class="text-4xl font-bold text-gray-900 mb-4">
        <i class="fas fa-user-check text-blue-600 mr-3"></i>For Borrowers
      </h2>
      <p class="text-lg text-gray-600">Everything you need to know about renting tools</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <i class="fas fa-tools text-blue-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-bold text-lg mb-2">Wide Selection</h4>
            <p class="text-gray-600 text-sm">Access to thousands of tools from power tools to specialized equipment.</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-bold text-lg mb-2">Fair Pricing</h4>
            <p class="text-gray-600 text-sm">Competitive hourly and daily rates that fit your budget.</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <i class="fas fa-shield-alt text-orange-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-bold text-lg mb-2">Secure Payment</h4>
            <p class="text-gray-600 text-sm">Multiple secure payment options for your convenience.</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <i class="fas fa-clock text-purple-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-bold text-lg mb-2">Quick Turnaround</h4>
            <p class="text-gray-600 text-sm">Get tools delivered within 30-60 minutes of booking.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- For Owners -->
<section class="mb-16 bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-12">
  <div class="max-w-4xl mx-auto">
    <div class="text-center mb-10">
      <h2 class="text-4xl font-bold text-gray-900 mb-4">
        <i class="fas fa-store text-orange-600 mr-3"></i>For Tool Owners
      </h2>
      <p class="text-lg text-gray-600">Turn your unused tools into passive income</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <i class="fas fa-dollar-sign text-orange-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-bold text-lg mb-2">Earn Money</h4>
            <p class="text-gray-600 text-sm">Make money from tools sitting idle in your garage or workshop.</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <i class="fas fa-chart-line text-blue-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-bold text-lg mb-2">Set Your Prices</h4>
            <p class="text-gray-600 text-sm">You control the pricing within fair market limits.</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-bold text-lg mb-2">Flexible Schedule</h4>
            <p class="text-gray-600 text-sm">Mark tools available or unavailable anytime you want.</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
            <i class="fas fa-users text-purple-600 text-xl"></i>
          </div>
          <div>
            <h4 class="font-bold text-lg mb-2">Community Impact</h4>
            <p class="text-gray-600 text-sm">Help your community by sharing resources sustainably.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-10">
      <a href="<?= BASE_URL ?: '/' ?>register.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transition-all transform hover:scale-105">
        <i class="fas fa-user-plus mr-3"></i>Become an Owner Today
      </a>
    </div>
  </div>
</section>

<!-- Why Choose Us -->
<section class="mb-16">
  <div class="text-center mb-12">
    <h2 class="text-4xl font-bold text-gray-900 mb-4">
      <i class="fas fa-star text-yellow-500 mr-3"></i>Why Choose <?php echo APP_NAME; ?>?
    </h2>
    <p class="text-lg text-gray-600">Join the community-driven sharing economy</p>
  </div>

  <div class="grid md:grid-cols-3 gap-8">
    <div class="text-center">
      <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
        <i class="fas fa-recycle text-4xl text-white"></i>
      </div>
      <h3 class="text-xl font-bold mb-3">Sustainable</h3>
      <p class="text-gray-600">Promote resource sharing and reduce waste through community collaboration.</p>
    </div>

    <div class="text-center">
      <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
        <i class="fas fa-hand-holding-usd text-4xl text-white"></i>
      </div>
      <h3 class="text-xl font-bold mb-3">Affordable</h3>
      <p class="text-gray-600">Save money by renting instead of buying expensive tools you'll rarely use.</p>
    </div>

    <div class="text-center">
      <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
        <i class="fas fa-heart text-4xl text-white"></i>
      </div>
      <h3 class="text-xl font-bold mb-3">Community</h3>
      <p class="text-gray-600">Build connections within your local community while helping each other.</p>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="mb-16 bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 rounded-2xl shadow-2xl p-12 text-center text-white">
  <h2 class="text-4xl font-extrabold mb-4">Ready to Get Started?</h2>
  <p class="text-xl mb-8 text-white/90">Join thousands of users renting and lending tools today!</p>
  <div class="flex flex-col sm:flex-row gap-4 justify-center">
    <a href="<?= BASE_URL ?: '/' ?>register.php" class="inline-flex items-center justify-center px-10 py-4 bg-white text-orange-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition-all shadow-2xl">
      <i class="fas fa-rocket mr-3"></i>Sign Up Now
    </a>
    <a href="<?= BASE_URL ?: '/' ?>index.php" class="inline-flex items-center justify-center px-10 py-4 bg-transparent text-white font-bold text-lg rounded-xl border-2 border-white hover:bg-white/10 transition-all">
      <i class="fas fa-search mr-3"></i>Browse Tools
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
