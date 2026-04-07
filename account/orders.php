<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
frontRequireLogin('/webshop/account/orders.php');

$userId = (int)$_SESSION['front_user_id'];
$tab    = $_GET['tab'] ?? 'all';
$page   = max(1,(int)($_GET['page'] ?? 1));
$perPage = 10;

$statusMap = [
    'all'              => '',
    'pending'          => 'pending',
    'confirmed'        => 'confirmed',
    'shipped'          => 'shipped',
    'delivered'        => 'delivered',
    'completed'        => 'completed',
    'cancelled'        => 'cancelled',
    'return_requested' => 'return_requested',
];
$statusFilter = $statusMap[$tab] ?? '';
$where  = "o.buyer_user_id=?";
$params = [$userId];
if ($statusFilter) { $where .= " AND o.order_status=?"; $params[] = $statusFilter; }

$cntStmt = getDB()->prepare("SELECT COUNT(*) FROM orders o WHERE $where");
$cntStmt->execute($params);
$total = (int)$cntStmt->fetchColumn();
$offset = ($page-1)*$perPage;

$stmt = getDB()->prepare("
    SELECT o.*, s.shop_name, s.shop_slug,
           COUNT(oi.item_id) AS item_count,
           GROUP_CONCAT(oi.product_name SEPARATOR '|||') AS product_names,
           GROUP_CONCAT(oi.image_url SEPARATOR '|||') AS product_images
    FROM orders o
    JOIN shops s ON o.shop_id=s.shop_id
    LEFT JOIN order_items oi ON o.order_id=oi.order_id
    WHERE $where
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$orders = $stmt->fetchAll();

$tabs = [
    'all'=>'ทั้งหมด','pending'=>'รอชำระ','confirmed'=>'กำลังเตรียม',
    'shipped'=>'กำลังจัดส่ง','delivered'=>'จัดส่งแล้ว','completed'=>'สำเร็จ','cancelled'=>'ยกเลิก'
];
$pageTitle = 'คำสั่งซื้อของฉัน';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container-xl py-3">
  <div class="row g-3">
    <div class="col-md-3">
      <?php include __DIR__ . '/includes/account_sidebar.php'; ?>
    </div>
    <div class="col-md-9">
      <div class="surface">
        <h5 class="fw-bold mb-0">คำสั่งซื้อของฉัน</h5>
      </div>

      <!-- Status Tabs -->
      <div class="bg-white border rounded mb-3 overflow-hidden">
        <div class="d-flex overflow-auto" style="scrollbar-width:none">
          <?php foreach ($tabs as $key=>$lbl): ?>
          <a href="?tab=<?= $key ?>" class="flex-shrink-0 text-center py-3 px-4 border-end text-decoration-none <?= $tab===$key?'text-orange fw-bold border-bottom border-2 border-orange':' text-muted' ?>" style="font-size:13px;border-bottom-color:var(--shopee-orange)!important;min-width:80px">
            <?= $lbl ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if (empty($orders)): ?>
      <div class="empty-state surface">
        <div class="empty-icon"><i class="bi bi-bag-x"></i></div>
        <h5>ไม่มีคำสั่งซื้อ<?= $tab!=='all'?' ('.($tabs[$tab]??'').')':'' ?></h5>
        <a href="/webshop/" class="btn btn-orange mt-3">เริ่มช้อปเลย</a>
      </div>
      <?php else: ?>
      <?php foreach ($orders as $o):
        $names  = explode('|||', $o['product_names'] ?? '');
        $images = explode('|||', $o['product_images'] ?? '');
      ?>
      <div class="surface mb-2">
        <!-- Order header -->
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shop text-orange"></i>
            <a href="/webshop/shop.php?slug=<?= e($o['shop_slug']) ?>" class="fw-semibold text-dark"><?= e($o['shop_name']) ?></a>
          </div>
          <span class="status-badge status-<?= $o['order_status'] ?>"><?= e(is_array($os=ORDER_STATUSES[$o['order_status']]??$o['order_status']) ? ($os['label']??$o['order_status']) : $os) ?></span>
        </div>
        <!-- Items preview -->
        <div class="d-flex gap-3 align-items-start mb-3">
          <div class="d-flex gap-1 flex-shrink-0">
            <?php foreach (array_slice($images, 0, 3) as $img): ?>
            <img src="<?= e($img ?: 'https://via.placeholder.com/64') ?>" class="rounded" width="64" height="64" style="object-fit:cover" alt="">
            <?php endforeach; ?>
            <?php if (count($images) > 3): ?>
            <div class="rounded d-flex align-items-center justify-content-center bg-light" style="width:64px;height:64px;font-size:12px;color:#666">+<?= count($images)-3 ?></div>
            <?php endif; ?>
          </div>
          <div class="flex-fill">
            <div style="font-size:13px;color:#444"><?= e($names[0] ?? '') ?><?= count($names)>1?' และอีก '.(count($names)-1).' รายการ':'' ?></div>
            <div class="text-muted mt-1" style="font-size:12px">
              <?= $o['item_count'] ?> รายการ |
              วันที่สั่ง: <?= formatDate($o['created_at'],'d/m/Y H:i') ?>
            </div>
          </div>
          <div class="text-end flex-shrink-0">
            <div class="text-orange fw-bold fs-6">฿<?= number_format((float)$o['total_amount'],0) ?></div>
            <?php if ($o['tracking_number']): ?>
            <div class="text-muted mt-1" style="font-size:11px">
              <i class="bi bi-truck me-1"></i><?= e($o['tracking_number']) ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center pt-2 border-top flex-wrap gap-2">
          <span class="text-muted" style="font-size:12px">
            <?= e(PAYMENT_METHODS[$o['payment_method']] ?? $o['payment_method']) ?>
          </span>
          <div class="d-flex gap-2">
            <?php if ($o['order_status']==='delivered'): ?>
            <a href="/webshop/account/order-detail.php?id=<?= $o['order_id'] ?>&review=1" class="btn btn-sm btn-outline-orange">รีวิวสินค้า</a>
            <?php endif; ?>
            <?php if (in_array($o['order_status'],['pending'])): ?>
            <form method="POST" action="/webshop/api/order-action.php" class="d-inline">
              <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
              <input type="hidden" name="action" value="cancel">
              <button class="btn btn-sm btn-outline-danger" onclick="return confirm('ยืนยันการยกเลิก?')">ยกเลิก</button>
            </form>
            <?php endif; ?>
            <?php if ($o['order_status']==='shipped'): ?>
            <form method="POST" action="/webshop/api/order-action.php" class="d-inline">
              <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
              <input type="hidden" name="action" value="confirm_received">
              <button class="btn btn-sm btn-orange" onclick="return confirm('ยืนยันว่าได้รับสินค้าแล้ว?')"><i class="bi bi-check-circle me-1"></i>ยืนยันรับสินค้า</button>
            </form>
            <?php endif; ?>
            <a href="/webshop/account/order-detail.php?id=<?= $o['order_id'] ?>" class="btn btn-sm btn-outline-secondary">ดูรายละเอียด</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (ceil($total/$perPage) > 1): ?>
      <nav class="mt-3 d-flex justify-content-center">
        <ul class="pagination">
          <?php for ($i=max(1,$page-2);$i<=min(ceil($total/$perPage),$page+4);$i++): ?>
          <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?tab=<?= $tab ?>&page=<?= $i ?>"><?= $i ?></a></li>
          <?php endfor; ?>
        </ul>
      </nav>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
