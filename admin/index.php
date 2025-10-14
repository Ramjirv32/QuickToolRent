<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

$pdo = db();

// KPIs from rentals (sum only paid/delivered/completed)
$totalUsers = (int)$pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'];
$totalProducts = (int)$pdo->query("SELECT COUNT(*) AS c FROM products")->fetch()['c'];
$totalRentals = (int)$pdo->query("SELECT COUNT(*) AS c FROM rentals")->fetch()['c'];
$totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) AS s FROM rentals WHERE status IN ('paid','delivered','completed')")->fetch()['s'];

// Rentals last 7 days
$rentalsDaily = $pdo->query("
  SELECT DATE(created_at) d, COUNT(*) c
  FROM rentals
  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
  GROUP BY DATE(created_at)
  ORDER BY d ASC
")->fetchAll();

// Revenue last 7 days
$revenueDaily = $pdo->query("
  SELECT DATE(created_at) d, COALESCE(SUM(total_amount),0) s
  FROM rentals
  WHERE status IN ('paid','delivered','completed') AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
  GROUP BY DATE(created_at)
  ORDER BY d ASC
")->fetchAll();

// Rentals by product category
$byCategory = $pdo->query("
  SELECT COALESCE(p.category, 'General') AS category, COUNT(*) cnt
  FROM rentals r
  JOIN products p ON p.id = r.product_id
  GROUP BY category
  ORDER BY cnt DESC
")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold">Admin Dashboard</h1>
    <p class="text-sm text-gray-600">Overview of platform performance</p>
  </div>
  <a href="../browse.php" class="px-3 py-1 rounded-md border border-blue-600 text-blue-600 hover:bg-blue-50">View Site</a>
</div>

<div class="rounded-xl overflow-hidden ring-1 ring-gray-200 bg-white mb-8">
  <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-HDtZK6ziH2eCkqBHz1afFLvRtI6uY7.png" alt="Dashboard reference design" class="w-full h-auto">
</div>

<div class="grid md:grid-cols-4 gap-4">
  <div class="rounded-lg bg-white ring-1 ring-gray-200 p-4">
    <div class="text-sm text-gray-600">Total Revenue</div>
    <div class="text-2xl font-semibold text-blue-600"><?= money($totalRevenue) ?></div>
  </div>
  <div class="rounded-lg bg-white ring-1 ring-gray-200 p-4">
    <div class="text-sm text-gray-600">Rentals</div>
    <div class="text-2xl font-semibold"><?= $totalRentals ?></div>
  </div>
  <div class="rounded-lg bg-white ring-1 ring-gray-200 p-4">
    <div class="text-sm text-gray-600">Products</div>
    <div class="text-2xl font-semibold"><?= $totalProducts ?></div>
  </div>
  <div class="rounded-lg bg-white ring-1 ring-gray-200 p-4">
    <div class="text-sm text-gray-600">Users</div>
    <div class="text-2xl font-semibold"><?= $totalUsers ?></div>
  </div>
</div>

<div class="mt-8 grid lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 rounded-xl bg-white ring-1 ring-gray-200 p-4">
    <div class="flex items-center justify-between mb-2">
      <div class="font-semibold">Revenue Last 7 Days</div>
    </div>
    <canvas id="revenueChart" height="120"></canvas>
  </div>
  <div class="rounded-xl bg-white ring-1 ring-gray-200 p-4">
    <div class="font-semibold mb-2">Rentals by Category</div>
    <canvas id="categoryChart" height="120"></canvas>
  </div>
  <div class="rounded-xl bg-white ring-1 ring-gray-200 p-4 lg:col-span-3">
    <div class="font-semibold mb-2">Rentals Count Last 7 Days</div>
    <canvas id="rentalsChart" height="100"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const revenueDaily = <?= json_encode($revenueDaily) ?>;
  const rentalsDaily = <?= json_encode($rentalsDaily) ?>;
  const byCategory = <?= json_encode($byCategory) ?>;

  const rLabels = revenueDaily.map(i => i.d);
  const rData = revenueDaily.map(i => Number(i.s));
  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: { labels: rLabels, datasets: [{ label: 'Revenue', data: rData, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.15)', tension: 0.35, fill: true }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  const cLabels = byCategory.map(i => i.category || 'General');
  const cData = byCategory.map(i => Number(i.cnt));
  new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: { labels: cLabels, datasets: [{ data: cData, backgroundColor: ['#2563eb','#10b981','#f59e0b','#6b7280','#ef4444'] }] },
    options: { plugins: { legend: { position: 'bottom' } } }
  });

  const rcLabels = rentalsDaily.map(i => i.d);
  const rcData = rentalsDaily.map(i => Number(i.c));
  new Chart(document.getElementById('rentalsChart'), {
    type: 'bar',
    data: { labels: rcLabels, datasets: [{ label: 'Rentals', data: rcData, backgroundColor: 'rgba(16,185,129,0.35)', borderColor: '#10b981' }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
