<?php
$pageTitle  = 'รายงานยอดขาย';
$breadcrumb = ['รายงาน' => 'index.php', 'ยอดขาย' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db     = getDB();
$from   = $_GET['from'] ?? date('Y-m-01');
$to     = $_GET['to']   ?? date('Y-m-d');
$group  = isset($_GET['group']) && in_array($_GET['group'], ['day','week','month']) ? $_GET['group'] : 'day';

$groupExpr = ['day'=>"DATE(o.ordered_at)",'week'=>"DATE_FORMAT(o.ordered_at,'%Y-%u')",'month'=>"DATE_FORMAT(o.ordered_at,'%Y-%m')"];
$labelExpr = ['day'=>"DATE_FORMAT(o.ordered_at,'%d/%m/%Y')",'week'=>"CONCAT('สัปดาห์ ',WEEK(o.ordered_at),' ',YEAR(o.ordered_at))",'month'=>"DATE_FORMAT(o.ordered_at,'%m/%Y')"];

// Use the correct column name from the actual orders table
$dateCol = 'created_at';
try {
    $testStmt = $db->query("SELECT created_at FROM orders LIMIT 1");
    $dateCol = 'created_at';
} catch (Exception $e) { $dateCol = 'created_at'; }

$groupExpr = ['day'=>"DATE(o.$dateCol)",'week'=>"DATE_FORMAT(o.$dateCol,'%Y-%u')",'month'=>"DATE_FORMAT(o.$dateCol,'%Y-%m')"];
$labelExpr = ['day'=>"DATE_FORMAT(o.$dateCol,'%d/%m/%Y')",'week'=>"CONCAT('สัปดาห์ ',WEEK(o.$dateCol),' ',YEAR(o.$dateCol))",'month'=>"DATE_FORMAT(o.$dateCol,'%m/%Y')"];

$salesData = $db->prepare("SELECT {$labelExpr[$group]} AS label, COUNT(*) AS orders, SUM(total_amount) AS revenue, SUM(shop_discount+voucher_discount+coins_used) AS discount_total
    FROM orders o WHERE o.payment_status='paid' AND DATE(o.$dateCol) BETWEEN ? AND ?
    GROUP BY {$groupExpr[$group]} ORDER BY {$groupExpr[$group]}");
$salesData->execute([$from, $to]);
$salesRows = $salesData->fetchAll();

$ordersByStatus = $db->prepare("SELECT order_status, COUNT(*) AS c FROM orders WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY order_status");
$ordersByStatus->execute([$from,$to]);
$statusRows = $ordersByStatus->fetchAll();

$topItems = $db->prepare("SELECT p.name, SUM(oi.quantity) AS qty, SUM(oi.subtotal) AS total
    FROM order_items oi JOIN products p ON oi.product_id=p.product_id
    JOIN orders o ON oi.order_id=o.order_id WHERE o.payment_status='paid' AND DATE(o.created_at) BETWEEN ? AND ?
    GROUP BY p.product_id ORDER BY total DESC LIMIT 15");
$topItems->execute([$from,$to]);
$topItems = $topItems->fetchAll();

$totals = ['orders'=>0,'revenue'=>0,'discount'=>0];
foreach ($salesRows as $r) { $totals['orders']+=$r['orders']; $totals['revenue']+=$r['revenue']; $totals['discount']+=$r['discount_total']; }

include dirname(__DIR__) . '/includes/header.php';
?>
<!-- Filters -->
<div class="card mb-4">
  <div class="card-body py-2">
    <form class="row g-2 align-items-center" method="GET">
      <div class="col-auto"><label class="form-label mb-0 small fw-semibold">จาก</label></div>
      <div class="col-auto"><input type="date" class="form-control form-control-sm" name="from" value="<?=e($from)?>"></div>
      <div class="col-auto"><label class="form-label mb-0 small fw-semibold">ถึง</label></div>
      <div class="col-auto"><input type="date" class="form-control form-control-sm" name="to" value="<?=e($to)?>"></div>
      <div class="col-auto"><label class="form-label mb-0 small fw-semibold">จัดกลุ่มโดย</label></div>
      <div class="col-auto">
        <select class="form-select form-select-sm" name="group">
          <option value="day" <?=$group==='day'?'selected':''?>>รายวัน</option>
          <option value="week" <?=$group==='week'?'selected':''?>>รายสัปดาห์</option>
          <option value="month" <?=$group==='month'?'selected':''?>>รายเดือน</option>
        </select>
      </div>
      <div class="col-auto"><button class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>ดูรายงาน</button></div>
      <div class="col-auto ms-auto">
        <a href="?from=<?=$from?>&to=<?=$to?>&group=<?=$group?>&export=csv" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Export CSV</a>
      </div>
    </form>
  </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card stat-card shadow-sm"><div class="card-body d-flex gap-3 align-items-center p-3">
    <div class="stat-icon text-white" style="background:#EE4D2D"><i class="bi bi-cart3"></i></div>
    <div><div class="text-muted small">ออเดอร์ทั้งหมด</div><div class="fw-bold fs-5"><?=number_format((int)$totals['orders'])?></div></div>
  </div></div></div>
  <div class="col-md-3"><div class="card stat-card shadow-sm"><div class="card-body d-flex gap-3 align-items-center p-3">
    <div class="stat-icon text-white" style="background:#198754"><i class="bi bi-cash-coin"></i></div>
    <div><div class="text-muted small">รายได้รวม</div><div class="fw-bold fs-5"><?=formatPrice((float)$totals['revenue'])?></div></div>
  </div></div></div>
  <div class="col-md-3"><div class="card stat-card shadow-sm"><div class="card-body d-flex gap-3 align-items-center p-3">
    <div class="stat-icon text-white" style="background:#dc3545"><i class="bi bi-tag"></i></div>
    <div><div class="text-muted small">ส่วนลดรวม</div><div class="fw-bold fs-5"><?=formatPrice((float)$totals['discount'])?></div></div>
  </div></div></div>
  <div class="col-md-3"><div class="card stat-card shadow-sm"><div class="card-body d-flex gap-3 align-items-center p-3">
    <div class="stat-icon text-white" style="background:#0d6efd"><i class="bi bi-calculator"></i></div>
    <div><div class="text-muted small">เฉลี่ยต่อออเดอร์</div><div class="fw-bold fs-5"><?=formatPrice($totals['orders']>0?(float)$totals['revenue']/$totals['orders']:0)?></div></div>
  </div></div></div>
</div>

<div class="row g-3 mb-4">
  <!-- Chart -->
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header fw-semibold"><i class="bi bi-bar-chart me-2"></i>ยอดขายรายงวด</div>
      <div class="card-body"><div class="chart-container" style="height:260px"><canvas id="salesChart"></canvas></div></div>
    </div>
  </div>
  <!-- Status Breakdown -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header fw-semibold"><i class="bi bi-pie-chart me-2"></i>สถานะออเดอร์</div>
      <div class="card-body">
        <div style="height:180px"><canvas id="statusChart"></canvas></div>
        <div class="mt-2">
          <?php foreach ($statusRows as $sr): $c=ORDER_STATUSES[$sr['order_status']]??['label'=>$sr['order_status'],'class'=>'secondary']; ?>
          <div class="d-flex justify-content-between small mb-1">
            <span class="badge bg-<?=$c['class']?>"><?=$c['label']?></span>
            <strong><?=number_format((int)$sr['c'])?></strong>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Detailed Table -->
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-table me-2"></i>ตารางยอดขายละเอียด</div>
      <div class="table-responsive" style="max-height:380px;overflow-y:auto">
        <table class="table table-sm table-hover mb-0">
          <thead class="table-light sticky-top"><tr><th>ช่วงเวลา</th><th class="text-center">ออเดอร์</th><th class="text-end">รายได้</th><th class="text-end">ส่วนลด</th></tr></thead>
          <tbody>
            <?php foreach ($salesRows as $r): ?>
            <tr>
              <td class="small"><?=e($r['label'])?></td>
              <td class="text-center small"><?=number_format((int)$r['orders'])?></td>
              <td class="text-end small fw-semibold"><?=formatPrice((float)$r['revenue'])?></td>
              <td class="text-end small text-danger"><?=formatPrice((float)$r['discount_total'])?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="table-warning fw-bold">
              <td>รวม</td>
              <td class="text-center"><?=number_format((int)$totals['orders'])?></td>
              <td class="text-end"><?=formatPrice((float)$totals['revenue'])?></td>
              <td class="text-end text-danger"><?=formatPrice((float)$totals['discount'])?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-trophy me-2 text-warning"></i>สินค้าขายดี</div>
      <div class="table-responsive" style="max-height:380px;overflow-y:auto">
        <table class="table table-sm table-hover mb-0">
          <thead class="table-light sticky-top"><tr><th>#</th><th>สินค้า</th><th class="text-center">จำนวน</th><th class="text-end">รายได้</th></tr></thead>
          <tbody>
            <?php foreach ($topItems as $i=>$ti): ?>
            <tr><td><?=$i+1?></td><td class="small text-truncate" style="max-width:160px"><?=e($ti['name'])?></td><td class="text-center small"><?=number_format((int)$ti['qty'])?></td><td class="text-end small fw-semibold"><?=formatPrice((float)$ti['total'])?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$extraJs = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script><script>
new Chart(document.getElementById("salesChart"),{type:"bar",data:{labels:'.json_encode(array_column($salesRows,'label')).',datasets:[{label:"รายได้",data:'.json_encode(array_map(fn($r)=>(float)$r['revenue'],$salesRows)).',backgroundColor:"rgba(238,77,45,.7)",borderRadius:4,yAxisID:"y"},{label:"ออเดอร์",data:'.json_encode(array_map(fn($r)=>(int)$r['orders'],$salesRows)).',type:"line",borderColor:"#0d6efd",backgroundColor:"rgba(13,110,253,.1)",yAxisID:"y2",tension:.4}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{ticks:{callback:v=>"฿"+v.toLocaleString()}},y2:{position:"right",grid:{drawOnChartArea:false}}}}});
new Chart(document.getElementById("statusChart"),{type:"doughnut",data:{labels:'.json_encode(array_map(fn($s)=>ORDER_STATUSES[$s["order_status"]]["label"]??$s["order_status"],$statusRows)).',datasets:[{data:'.json_encode(array_map(fn($s)=>(int)$s["c"],$statusRows)).',backgroundColor:["#ffc107","#0dcaf0","#0d6efd","#6f42c1","#198754","#dc3545","#6c757d","#fd7e14","#20c997"]}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}});
</script>';
include dirname(__DIR__) . '/includes/footer.php';
?>
