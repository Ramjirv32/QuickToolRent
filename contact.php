<?php 
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php'; 

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  $message = trim($_POST['message'] ?? '');
  
  if ($name && $email && $subject && $message) {
   
    $success = 'Thank you for contacting us! We will get back to you within 24 hours.';
  } else {
    $error = 'Please fill in all fields.';
  }
}
?>

<!-- Hero Section -->
<section class="mb-16 relative overflow-hidden rounded-2xl" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
  <div class="relative px-8 py-20 text-white text-center">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-5xl font-extrabold mb-6">
        <i class="fas fa-envelope text-yellow-300 mr-3"></i>Get In Touch
      </h1>
      <p class="text-2xl text-white/90">
        We're here to help! Reach out to us anytime
      </p>
    </div>
  </div>
</section>

<div class="grid lg:grid-cols-2 gap-12 mb-16">
  <!-- Contact Form -->
  <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-gray-100">
    <h2 class="text-3xl font-bold mb-6 text-gray-900">
      <i class="fas fa-paper-plane text-orange-500 mr-3"></i>Send Us a Message
    </h2>

    <?php if ($success): ?>
      <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-4">
        <div class="flex items-center">
          <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
          <p class="text-green-800 font-medium"><?= htmlspecialchars($success) ?></p>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
          <p class="text-red-800 font-medium"><?= htmlspecialchars($error) ?></p>
        </div>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
      <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">
          <i class="fas fa-user text-gray-400 mr-2"></i>Your Name *
        </label>
        <input 
          type="text" 
          name="name" 
          required
          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
          placeholder="John Doe"
        >
      </div>

      <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">
          <i class="fas fa-envelope text-gray-400 mr-2"></i>Email Address *
        </label>
        <input 
          type="email" 
          name="email" 
          required
          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
          placeholder="your@email.com"
        >
      </div>

      <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">
          <i class="fas fa-tag text-gray-400 mr-2"></i>Subject *
        </label>
        <input 
          type="text" 
          name="subject" 
          required
          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
          placeholder="What's this about?"
        >
      </div>

      <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">
          <i class="fas fa-comment-alt text-gray-400 mr-2"></i>Message *
        </label>
        <textarea 
          name="message" 
          rows="5" 
          required
          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none"
          placeholder="Tell us more..."
        ></textarea>
      </div>

      <button 
        type="submit"
        class="w-full py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transition-all transform hover:scale-105"
      >
        <i class="fas fa-paper-plane mr-2"></i>Send Message
      </button>
    </form>
  </div>

  <!-- Contact Information -->
  <div class="space-y-6">
    <!-- Contact Cards -->
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-start">
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-6 flex-shrink-0">
          <i class="fas fa-map-marker-alt text-3xl"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-2">Visit Us</h3>
          <p class="text-white/90">123 Tool Street, Suite 456<br/>City, State 12345<br/>United States</p>
        </div>
      </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-start">
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-6 flex-shrink-0">
          <i class="fas fa-phone-alt text-3xl"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-2">Call Us</h3>
          <p class="text-white/90">Phone: <a href="tel:+1234567890" class="hover:underline">+1 (234) 567-890</a></p>
          <p class="text-white/90 mt-1">Toll Free: <a href="tel:+18001234567" class="hover:underline">1-800-123-4567</a></p>
        </div>
      </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-start">
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-6 flex-shrink-0">
          <i class="fas fa-envelope text-3xl"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-2">Email Us</h3>
          <p class="text-white/90">Support: <a href="mailto:support@example.com" class="hover:underline">support@example.com</a></p>
          <p class="text-white/90 mt-1">Sales: <a href="mailto:sales@example.com" class="hover:underline">sales@example.com</a></p>
        </div>
      </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-xl p-8 text-white">
      <div class="flex items-start">
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-6 flex-shrink-0">
          <i class="fas fa-clock text-3xl"></i>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-2">Working Hours</h3>
          <p class="text-white/90">Monday - Friday: 9:00 AM - 6:00 PM</p>
          <p class="text-white/90 mt-1">Saturday - Sunday: 10:00 AM - 4:00 PM</p>
          <p class="text-sm text-white/70 mt-2"><i class="fas fa-info-circle mr-1"></i>Support available 24/7</p>
        </div>
      </div>
    </div>

    <!-- Social Media -->
    <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-gray-100">
      <h3 class="text-xl font-bold mb-6 text-gray-900">
        <i class="fas fa-share-alt text-orange-500 mr-2"></i>Connect With Us
      </h3>
      <div class="flex space-x-4">
        <a href="#" class="w-14 h-14 bg-blue-600 hover:bg-blue-700 rounded-xl flex items-center justify-center text-white text-xl transition-all transform hover:scale-110 shadow-lg">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="#" class="w-14 h-14 bg-sky-500 hover:bg-sky-600 rounded-xl flex items-center justify-center text-white text-xl transition-all transform hover:scale-110 shadow-lg">
          <i class="fab fa-twitter"></i>
        </a>
        <a href="#" class="w-14 h-14 bg-pink-600 hover:bg-pink-700 rounded-xl flex items-center justify-center text-white text-xl transition-all transform hover:scale-110 shadow-lg">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="#" class="w-14 h-14 bg-blue-700 hover:bg-blue-800 rounded-xl flex items-center justify-center text-white text-xl transition-all transform hover:scale-110 shadow-lg">
          <i class="fab fa-linkedin-in"></i>
        </a>
        <a href="#" class="w-14 h-14 bg-green-600 hover:bg-green-700 rounded-xl flex items-center justify-center text-white text-xl transition-all transform hover:scale-110 shadow-lg">
          <i class="fab fa-whatsapp"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- FAQ Section -->
<section class="mb-16 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-12">
  <div class="text-center mb-12">
    <h2 class="text-4xl font-bold text-gray-900 mb-4">
      <i class="fas fa-question-circle text-blue-600 mr-3"></i>Frequently Asked Questions
    </h2>
    <p class="text-lg text-gray-600">Quick answers to common questions</p>
  </div>

  <div class="max-w-4xl mx-auto space-y-4">
    <details class="bg-white rounded-xl shadow-lg p-6 cursor-pointer group">
      <summary class="font-bold text-lg text-gray-900 flex items-center justify-between">
        <span><i class="fas fa-clock text-orange-500 mr-2"></i>How fast is the delivery?</span>
        <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
      </summary>
      <p class="mt-4 text-gray-600 pl-7">We deliver tools within 30-60 minutes of booking in most areas. Delivery time may vary based on your location and tool availability.</p>
    </details>

    <details class="bg-white rounded-xl shadow-lg p-6 cursor-pointer group">
      <summary class="font-bold text-lg text-gray-900 flex items-center justify-between">
        <span><i class="fas fa-credit-card text-orange-500 mr-2"></i>What payment methods do you accept?</span>
        <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
      </summary>
      <p class="mt-4 text-gray-600 pl-7">We accept Card, UPI, Wallet, and Cash on Delivery (COD) for your convenience.</p>
    </details>

    <details class="bg-white rounded-xl shadow-lg p-6 cursor-pointer group">
      <summary class="font-bold text-lg text-gray-900 flex items-center justify-between">
        <span><i class="fas fa-undo text-orange-500 mr-2"></i>What's your return policy?</span>
        <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
      </summary>
      <p class="mt-4 text-gray-600 pl-7">Tools must be returned in the same condition as received. Late returns may incur additional charges. Contact support for any issues.</p>
    </details>

    <details class="bg-white rounded-xl shadow-lg p-6 cursor-pointer group">
      <summary class="font-bold text-lg text-gray-900 flex items-center justify-between">
        <span><i class="fas fa-tools text-orange-500 mr-2"></i>Can I rent tools for multiple days?</span>
        <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
      </summary>
      <p class="mt-4 text-gray-600 pl-7">Yes! You can rent tools for hours or days. Daily rates offer better value for longer rentals.</p>
    </details>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
