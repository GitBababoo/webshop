<?php
$pageTitle  = 'รายงานภาพรวม';
$breadcrumb = ['รายงาน' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$period = $_GET['period'] ?? '30';

$days = in_array($period, ['7','30','90','365']) ? (int)$period : 30;

$revenueByDay = $db->prepare("SELECT DATE(created_at) AS d, COUNT(*) AS orders, SUM(total_amount) AS revenue
    FROM orders WHERE payment_status='paid' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    GROUP BY DATE(created_at) ORDER BY d");
$revenueByDay->execute([$days]);
$revenueRows = $revenueByDay->fetchAll();

$topCats = $db->query("SELECT c.name, SUM(oi.quantity) AS sold, SUM(oi.subtotal) AS revenue
    FROM order_items oi JOIN products p ON oi.product_id=p.product_id
    JOIN categories c ON p.category_id=c.category_id
    JOIN orders o ON oi.order_id=o.order_id WHERE o.payment_status='paid'
    GROUP BY c.category_id ORDER BY revenue DESC LIMIT 8")->fetchAll();

$topProducts = $db->query("SELECT p.name, p.total_sold, p.rating, SUM(oi.subtotal) AS revenue,
    pi.image_url FROM order_items oi JOIN products p ON oi.product_id=p.product_id
    LEFT JOIN product_images pi ON p.product_id=pi.product_id AND pi.is_primary=1
    JOIN orders o ON oi.order_id=o.order_id WHERE o.payment_status='paid'
    GROUP BY p.product_id ORDER BY revenue DESC LIMIT 10")->fetchAll();

$topShops = $db->query("SELECT s.shop_name, COUNT(DISTINCT o.order_id) AS orders, SUM(o.total_amount) AS revenue
    FROM orders o JOIN shops s ON o.shop_id=s.shop_id WHERE o.payment_status='paid'
    GROUP BY s.shop_id ORDER BY revenue DESC LIMIT 8")->fetchAll();

$payMethods = $db->query("SELECT payment_method, COUNT(*) AS cnt, SUM(total_amount) AS total
    FROM orders WHERE payment_status='paid' GROUP BY payment_method ORDER BY total DESC")->fetchAll();

$summary = [
    'total_orders'       => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'completed_orders'   => $db->query("SELECT COUNT(*) FROM orders WHERE order_status='completed'")->fetchColumn(),
    'cancelled_orders'   => $db->query("SELECT COUNT(*) FROM orders WHERE order_status='cancelled'")->fetchColumn(),
    'total_revenue'      => $db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='paid'")->fetchColumn(),
    'avg_order_value'    => $db->query("SELECT COALESCE(AVG(total_amount),0) FROM orders WHERE payment_status='paid'")->fetchColumn(),
    'total_users'        => $db->query("SELECT COUNT(*) FROM users WHERE role='buyer'")->fetchColumn(),
    'new_users_30d'      => $db->query("SELECT COUNT(*) FROM users WHERE role='buyer' AND created_at>=DATE_SUB(NOW(),INTERVAL 30 DAY)")->fetchColumn(),
    'total_products'     => $db->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn(),
    'total_reviews'      => $db->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
    'avg_rating'         => $db->query("SELECT COALESCE(AVG(rating),0) FROM reviews")->fetchColumn(),
];

include dirname(__DIR__) . '/includes/header.php';
?>
<!-- Period Filter -->
<div class="d-flex gap-2 mb-4 align-items-center">
  <span class="text-muted small fw-semibold">ช่วงเวลา:</span>
  <?php foreach (['7'=>'7 วัน','30'=>'30 วัน','90'=>'90 วัน','365'=>'1 ปี'] as $v=>$l): ?>
  <a href="?period=<?=$v?>" class="btn btn-sm <?=$period==$v?'btn-primary':'btn-outline-secondary'?>"><?=$l?></a>
  <?php endforeach; ?>
  <a href="sales.php" class="btn btn-sm btn-outline-success ms-auto"><i class="bi bi-graph-up me-1"></i>รายงานยอดขายละเอียด</a>
</div>

<!-- Summary Stats -->
<div class="row g-3 mb-4">
  <?php $cards = [
    ['label'=>'ออเดอร์ทั้งหมด','value'=>number_format((int)$summary['total_orders']),'icon'=>'bi-cart3','color'=>'#EE4D2D'],
    ['label'=>'ออเดอร์สำเร็จ','value'=>number_format((int)$summary['completed_orders']),'icon'=>'bi-cart-check','color'=>'#198754'],
    ['label'=>'ออเดอร์ยกเลิก','value'=>number_format((int)$summary['cancelled_orders']),'icon'=>'bi-cart-x','color'=>'#dc3545'],
    ['label'=>'รายได้รวม','value'=>formatPrice((float)$summary['total_revenue']),'icon'=>'bi-cash-coin','color'=>'#0d6efd'],
    ['label'=>'เฉลี่ยต่อออเดอร์','value'=>formatPrice((float)$summary['avg_order_value']),'icon'=>'bi-calculator','color'=>'#6f42c1'],
    ['label'=>'ผู้ใช้ทั้งหมด','value'=>number_format((int)$summary['total_users']),'icon'=>'bi-people','color'=>'#20c997'],
    ['label'=>'ผู้ใช้ใหม่ (30 วัน)','value'=>number_format((int)$summary['new_users_30d']),'icon'=>'bi-person-plus','color'=>'#fd7e14'],
    ['label'=>'สินค้าทั้งหมด','value'=>number_format((int)$summary['total_products']),'icon'=>'bi-box-seam','color'=>'#0dcaf0'],
    ['label'=>'คะแนนเฉลี่ย','value'=>number_format((float)$summary['avg_rating'],2).' ★','icon'=>'bi-star','color'=>'#ffc107'],
  ];
  foreach ($cards as $c): ?>
  <div class="col-sm-6 col-md-4 col-xl-3">
    <div class="card stat-card shadow-sm h-100">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="stat-icon text-white flex-shrink-0" style="background:<?=$c['color']?>">
          <i class="bi <?=$c['icon']?>"></i>
        </div>
        <div><div class="text-muted small"><?=$c['label']?></div><div class="fw-bold fs-6"><?=$c['value']?></div></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
  <!-- Revenue Line Chart -->
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header fw-semibold"><i class="bi bi-graph-up me-2"></i>รายได้ <?=$days?> วันล่าสุด</div>
      <div class="card-body"><div class="chart-container"><canvas id="revenueChart"></canvas></div></div>
    </div>
  </div>
  <!-- Payment Methods Pie -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header fw-semibold"><i class="bi bi-credit-card me-2"></i>วิธีชำระเงิน</div>
      <div class="card-body">
        <div style="height:180px"><canvas id="payChart"></canvas></div>
        <div class="mt-2">
          <?php foreach ($payMethods as $pm): ?>
          <div class="d-flex justify-content-between small mb-1">
            <span><?=e(strtoupper($pm['payment_method']))?></span>
            <span class="fw-semibold"><?=formatPrice((float)$pm['total'])?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <!-- Top Categories -->
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header fw-semibold"><i class="bi bi-grid me-2"></i>หมวดหมู่ขายดี</div>
      <div class="card-body">
        <div style="height:220px"><canvas id="catChart"></canvas></div>
      </div>
    </div>
  </div>
  <!-- Top Shops -->
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header fw-semibold"><i class="bi bi-shop me-2"></i>ร้านค้ายอดขายสูงสุด</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead class="table-light"><tr><th>#</th><th>ร้านค้า</th><th>ออเดอร์</th><th>รายได้</th></tr></thead>
          <tbody>
            <?php foreach ($topShops as $i=>$ts): ?>
            <tr><td><?=$i+1?></td><td class="small"><?=e($ts['shop_name'])?></td><td><?=number_format((int)$ts['orders'])?></td><td class="fw-semibold small"><?=formatPrice((float)$ts['revenue'])?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Top Products Table -->
<div class="card">
  <div class="card-header fw-semibold"><i class="bi bi-trophy me-2 text-warning"></i>สินค้าขายดี Top 10</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>#</th><th>สินค้า</th><th>ขาย</th><th>คะแนน</th><th>รายได้</th></tr></thead>
      <tbody>
        <?php foreach ($topProducts as $i=>$tp): ?>
        <tr>
          <td class="fw-bold text-muted"><?=$i+1?></td>
          <td class="d-flex align-items-center gap-2">
            <?php if ($tp['image_url']): ?><img src="<?=e($tp['image_url'])?>" class="product-thumb" alt=""><?php endif; ?>
            <span class="small"><?=e($tp['name'])?></span>
          </td>
          <td><?=number_format((int)$tp['total_sold'])?></td>
          <td><i class="bi bi-star-fill text-warning"></i> <?=number_format((float)$tp['rating'],1)?></td>
          <td class="fw-semibold"><?=formatPrice((float)$tp['revenue'])?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$labels = []; $revData = []; $ordData = [];
for ($i=$days-1;$i>=0;$i--) {
    $day = date('Y-m-d',strtotime("-$i days"));
    $labels[] = date('d/m',strtotime($day));
    $found = array_filter($revenueRows, fn($r)=>$r['d']===$day);
    $r = $found ? array_values($found)[0] : null;
    $revData[] = $r ? (float)$r['revenue'] : 0;
    $ordData[] = $r ? (int)$r['orders'] : 0;
}
$extraJs = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script><script>
new Chart(document.getElementById("revenueChart"),{type:"line",data:{labels:'.json_encode($labels).',datasets:[{label:"รายได้",data:'.json_encode($revData).',borderColor:"#EE4D2D",backgroundColor:"rgba(238,77,45,.1)",fill:true,tension:.4},{label:"ออเดอร์",data:'.json_encode($ordData).',borderColor:"#0d6efd",backgroundColor:"rgba(13,110,253,.1)",fill:true,tension:.4,yAxisID:"y2"}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{ticks:{callback:v=>"฿"+v.toLocaleString()}},y2:{position:"right",grid:{drawOnChartArea:false}}}}});
new Chart(document.getElementById("payChart"),{type:"pie",data:{labels:'.json_encode(array_map(fn($p)=>strtoupper($p['payment_method']),$payMethods)).',datasets:[{data:'.json_encode(array_map(fn($p)=>(float)$p['total'],$payMethods)).',backgroundColor:["#EE4D2D","#0d6efd","#198754","#fd7e14","#6f42c1","#0dcaf0","#ffc107"]}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:"bottom",labels:{font:{size:10}}}}}});
new Chart(document.getElementById("catChart"),{type:"bar",data:{labels:'.json_encode(array_map(fn($c)=>$c['name'],$topCats)).',datasets:[{label:"รายได้",data:'.json_encode(array_map(fn($c)=>(float)$c['revenue'],$topCats)).',backgroundColor:"rgba(238,77,45,.7)",borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:"y",plugins:{legend:{display:false}},scales:{x:{ticks:{callback:v=>"฿"+v.toLocaleString()}}}}});
</script>';
include dirname(__DIR__) . '/includes/footer.php';
?>
