<?php
$pageTitle = 'แดชบอร์ด';
require_once __DIR__ . '/includes/header.php';
$stats = getStats();
$db    = getDB();

// Revenue chart (last 7 days)
$revenueRows = $db->query("
    SELECT DATE(created_at) AS d, COALESCE(SUM(total_amount),0) AS rev
    FROM orders WHERE payment_status='paid' AND created_at >= DATE_SUB(NOW(),INTERVAL 7 DAY)
    GROUP BY DATE(created_at) ORDER BY d")->fetchAll();
$chartLabels = []; $chartData = [];
for ($i=6; $i>=0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('d/m', strtotime($day));
    $found = array_filter($revenueRows, fn($r) => $r['d'] === $day);
    $chartData[] = $found ? (float)array_values($found)[0]['rev'] : 0;
}

// Top products
$topProducts = $db->query("
    SELECT p.name, p.total_sold, p.rating, pi.image_url
    FROM products p LEFT JOIN product_images pi ON p.product_id=pi.product_id AND pi.is_primary=1
    WHERE p.status='active' ORDER BY p.total_sold DESC LIMIT 5")->fetchAll();

// Recent orders
$recentOrders = $db->query("
    SELECT o.*, u.username, u.full_name, s.shop_name
    FROM orders o JOIN users u ON o.buyer_user_id=u.user_id JOIN shops s ON o.shop_id=s.shop_id
    ORDER BY o.created_at DESC LIMIT 8")->fetchAll();

// Top shops
$topShops = $db->query("
    SELECT s.shop_name, s.rating, s.total_sales, s.total_products, s.logo_url
    FROM shops s WHERE s.is_active=1 ORDER BY s.total_sales DESC LIMIT 5")->fetchAll();
?>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['label'=>'ยอดขายวันนี้',     'value'=>formatPrice((float)$stats['today_revenue']),  'icon'=>'bi-cash-coin',      'bg'=>'linear-gradient(135deg,#EE4D2D,#ff7337)', 'sub'=>$stats['today_orders'].' ออเดอร์'],
    ['label'=>'รายได้เดือนนี้',    'value'=>formatPrice((float)$stats['month_revenue']), 'icon'=>'bi-graph-up-arrow',  'bg'=>'linear-gradient(135deg,#0d6efd,#6610f2)', 'sub'=>'รายได้รวมเดือน'],
    ['label'=>'ออเดอร์รอดำเนินการ','value'=>number_format((int)$stats['pending_orders']),'icon'=>'bi-cart-check',      'bg'=>'linear-gradient(135deg,#fd7e14,#ffc107)', 'sub'=>'รอยืนยัน'],
    ['label'=>'ผู้ใช้งานทั้งหมด', 'value'=>number_format((int)$stats['total_users']),   'icon'=>'bi-people-fill',     'bg'=>'linear-gradient(135deg,#198754,#20c997)', 'sub'=>$stats['total_sellers'].' ร้านค้า'],
    ['label'=>'สินค้าทั้งหมด',    'value'=>number_format((int)$stats['total_products']),'icon'=>'bi-box-seam-fill',   'bg'=>'linear-gradient(135deg,#0dcaf0,#0d6efd)', 'sub'=>$stats['total_shops'].' ร้านค้า'],
    ['label'=>'รายได้รวม',        'value'=>formatPrice((float)$stats['total_revenue']), 'icon'=>'bi-bank',            'bg'=>'linear-gradient(135deg,#6f42c1,#d63384)', 'sub'=>'ทั้งหมด'],
  ];
  foreach ($cards as $c): ?>
  <div class="col-sm-6 col-xl-4">
    <div class="card stat-card shadow-sm h-100">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="stat-icon text-white flex-shrink-0" style="background:<?= $c['bg'] ?>">
          <i class="bi <?= $c['icon'] ?>"></i>
        </div>
        <div class="flex-grow-1 min-width-0">
          <div class="text-muted small"><?= $c['label'] ?></div>
          <div class="fw-bold fs-5 text-truncate"><?= $c['value'] ?></div>
          <div class="text-muted" style="font-size:.75rem"><?= $c['sub'] ?></div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-bar-chart me-2 text-primary"></i>รายได้ 7 วันล่าสุด</span>
        <a href="<?= ADMIN_URL ?>/reports/sales.php" class="btn btn-sm btn-outline-primary">ดูรายงาน</a>
      </div>
      <div class="card-body"><div class="chart-container"><canvas id="revenueChart"></canvas></div></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-pie-chart me-2 text-success"></i>สถานะออเดอร์</div>
      <div class="card-body d-flex flex-column align-items-center">
        <div style="height:200px;width:200px"><canvas id="orderStatusChart"></canvas></div>
        <?php
        $statusCounts = $db->query("SELECT order_status, COUNT(*) as c FROM orders GROUP BY order_status")->fetchAll();
        ?>
        <div class="mt-2 w-100">
          <?php foreach ($statusCounts as $sc):
            $cfg = ORDER_STATUSES[$sc['order_status']] ?? ['label'=>$sc['order_status'],'class'=>'secondary']; ?>
            <div class="d-flex justify-content-between small mb-1">
              <span class="badge bg-<?= $cfg['class'] ?>"><?= $cfg['label'] ?></span>
              <strong><?= $sc['c'] ?></strong>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Orders + Top Shops -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cart3 me-2 text-primary"></i>ออเดอร์ล่าสุด</span>
        <a href="<?= ADMIN_URL ?>/orders/index.php" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light"><tr>
            <th>เลขออเดอร์</th><th>ผู้ซื้อ</th><th>ร้านค้า</th><th>ยอด</th><th>สถานะ</th><th>วันที่</th>
          </tr></thead>
          <tbody>
          <?php foreach ($recentOrders as $o):
            $scfg = ORDER_STATUSES[$o['order_status']] ?? ['label'=>$o['order_status'],'class'=>'secondary']; ?>
          <tr>
            <td><a href="<?= ADMIN_URL ?>/orders/view.php?id=<?= $o['order_id'] ?>" class="text-decoration-none fw-semibold"><?= e($o['order_number']) ?></a></td>
            <td><?= e($o['full_name'] ?: $o['username']) ?></td>
            <td><?= e($o['shop_name']) ?></td>
            <td class="fw-semibold"><?= formatPrice((float)$o['total_amount']) ?></td>
            <td><span class="badge bg-<?= $scfg['class'] ?>"><?= $scfg['label'] ?></span></td>
            <td class="text-muted small"><?= formatDate($o['created_at'], 'd/m/Y') ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shop me-2 text-success"></i>ร้านค้ายอดนิยม</span>
        <a href="<?= ADMIN_URL ?>/shops/index.php" class="btn btn-sm btn-outline-success">ดูทั้งหมด</a>
      </div>
      <ul class="list-group list-group-flush">
        <?php foreach ($topShops as $i => $shop): ?>
        <li class="list-group-item d-flex align-items-center gap-3 px-3">
          <span class="fw-bold text-muted" style="width:20px"><?= $i+1 ?></span>
          <?php if ($shop['logo_url']): ?>
            <img src="<?= e($shop['logo_url']) ?>" class="avatar" alt="">
          <?php else: ?>
            <div class="avatar bg-primary text-white"><?= mb_substr($shop['shop_name'],0,1) ?></div>
          <?php endif; ?>
          <div class="flex-grow-1 min-width-0">
            <div class="text-truncate fw-semibold small"><?= e($shop['shop_name']) ?></div>
            <div class="text-muted" style="font-size:.75rem">
              <i class="bi bi-star-fill text-warning"></i> <?= number_format((float)$shop['rating'],1) ?>
              · <?= number_format((int)$shop['total_sales']) ?> ชิ้น
            </div>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>

<!-- Top Products -->
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-box-seam me-2 text-warning"></i>สินค้าขายดี</span>
    <a href="<?= ADMIN_URL ?>/products/index.php" class="btn btn-sm btn-outline-warning">ดูทั้งหมด</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>#</th><th>สินค้า</th><th>ขายแล้ว</th><th>คะแนน</th></tr></thead>
      <tbody>
        <?php foreach ($topProducts as $i => $p): ?>
        <tr>
          <td class="fw-bold text-muted"><?= $i+1 ?></td>
          <td class="d-flex align-items-center gap-2">
            <?php if ($p['image_url']): ?>
              <img src="<?= e($p['image_url']) ?>" class="product-thumb" alt="">
            <?php else: ?>
              <div class="product-thumb bg-light d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
            <?php endif; ?>
            <span><?= e($p['name']) ?></span>
          </td>
          <td><?= number_format((int)$p['total_sold']) ?> ชิ้น</td>
          <td>
            <i class="bi bi-star-fill text-warning"></i> <?= number_format((float)$p['rating'],1) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$chartLabelsJson = json_encode($chartLabels);
$chartDataJson   = json_encode($chartData);
$statusLabels = array_map(fn($s) => ORDER_STATUSES[$s['order_status']]['label'] ?? $s['order_status'], $statusCounts);
$statusData   = array_map(fn($s) => (int)$s['c'], $statusCounts);
$statusLabelsJson = json_encode($statusLabels);
$statusDataJson   = json_encode($statusData);
$extraJs = <<<JS
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('revenueChart'), {
  type:'bar',
  data:{labels:$chartLabelsJson,datasets:[{label:'รายได้ (฿)',data:$chartDataJson,
    backgroundColor:'rgba(238,77,45,.7)',borderColor:'#EE4D2D',borderWidth:1,borderRadius:6}]},
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
    scales:{y:{ticks:{callback:v=>'฿'+v.toLocaleString()}}}}
});
new Chart(document.getElementById('orderStatusChart'), {
  type:'doughnut',
  data:{labels:$statusLabelsJson,datasets:[{data:$statusDataJson,
    backgroundColor:['#ffc107','#0dcaf0','#0d6efd','#6f42c1','#198754','#20c997','#dc3545','#fd7e14','#6c757d']}]},
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}
});
</script>
JS;
include __DIR__ . '/includes/footer.php';
