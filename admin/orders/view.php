<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$s = $db->prepare("SELECT o.*, u.username, u.full_name, u.email, u.phone AS buyer_phone,
    s.shop_name, s.shop_id,
    a.recipient_name, a.phone AS addr_phone, a.address_line1, a.address_line2,
    a.district, a.province, a.postal_code, a.country,
    sp.name AS provider_name
    FROM orders o
    JOIN users u ON o.buyer_user_id=u.user_id
    JOIN shops s ON o.shop_id=s.shop_id
    JOIN user_addresses a ON o.address_id=a.address_id
    LEFT JOIN shipping_providers sp ON o.provider_id=sp.provider_id
    WHERE o.order_id=?");
$s->execute([$id]);
$order = $s->fetch();
if (!$order) { flash('danger','ไม่พบออเดอร์'); header('Location: index.php'); exit; }

$items   = $db->prepare("SELECT * FROM order_items WHERE order_id=?"); $items->execute([$id]); $items=$items->fetchAll();
$history = $db->prepare("SELECT h.*, u.username FROM order_status_history h LEFT JOIN users u ON h.created_by=u.user_id WHERE h.order_id=? ORDER BY h.created_at DESC"); $history->execute([$id]); $history=$history->fetchAll();
$payment = $db->prepare("SELECT * FROM payments WHERE order_id=? ORDER BY created_at DESC LIMIT 1"); $payment->execute([$id]); $payment=$payment->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $newStatus = $_POST['order_status'] ?? '';
    $note      = trim($_POST['note'] ?? '');
    $tracking  = trim($_POST['tracking_number'] ?? '');
    if ($newStatus && array_key_exists($newStatus, ORDER_STATUSES)) {
        $db->prepare("UPDATE orders SET order_status=?, tracking_number=COALESCE(NULLIF(?,''),tracking_number), updated_at=NOW() WHERE order_id=?")->execute([$newStatus,$tracking,$id]);
        $db->prepare("INSERT INTO order_status_history (order_id,status,note,created_by) VALUES (?,?,?,?)")->execute([$id,$newStatus,$note,$_SESSION['admin_id']]);
        if ($newStatus==='shipped') $db->prepare("UPDATE orders SET shipped_at=NOW() WHERE order_id=?")->execute([$id]);
        if ($newStatus==='delivered') $db->prepare("UPDATE orders SET delivered_at=NOW() WHERE order_id=?")->execute([$id]);
        if ($newStatus==='completed') $db->prepare("UPDATE orders SET completed_at=NOW() WHERE order_id=?")->execute([$id]);
        if ($newStatus==='cancelled') $db->prepare("UPDATE orders SET cancelled_at=NOW(), cancel_reason=? WHERE order_id=?")->execute([$note,$id]);
        logActivity('update_status','orders','order',$id,"เปลี่ยนสถานะ: $newStatus");
        flash('success','อัปเดตสถานะเรียบร้อย');
        header("Location: view.php?id=$id"); exit;
    }
}

$pageTitle  = 'ออเดอร์ #' . e($order['order_number']);
$breadcrumb = ['ออเดอร์' => 'index.php', $order['order_number'] => false];
include dirname(__DIR__) . '/includes/header.php';
$scfg = ORDER_STATUSES[$order['order_status']] ?? ['label'=>$order['order_status'],'class'=>'secondary'];
$pcfg = PAYMENT_STATUSES[$order['payment_status']] ?? ['label'=>$order['payment_status'],'class'=>'secondary'];
?>
<div class="row g-3">
  <!-- Left Column -->
  <div class="col-lg-8">
    <!-- Order Summary -->
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i>รายละเอียดออเดอร์</span>
        <div class="d-flex gap-2">
          <span class="badge bg-<?=$pcfg['class']?> fs-6"><?=$pcfg['label']?></span>
          <span class="badge bg-<?=$scfg['class']?> fs-6"><?=$scfg['label']?></span>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-2 mb-3">
          <div class="col-md-3"><small class="text-muted">เลขออเดอร์</small><div class="fw-bold"><?=e($order['order_number'])?></div></div>
          <div class="col-md-3"><small class="text-muted">วันที่สั่ง</small><div><?=formatDate($order['created_at'])?></div></div>
          <div class="col-md-3"><small class="text-muted">วิธีชำระ</small><div><?=e(strtoupper($order['payment_method']))?></div></div>
          <div class="col-md-3"><small class="text-muted">เลข Tracking</small><div><?=e($order['tracking_number']??'-')?></div></div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>สินค้า</th><th>Variant</th><th class="text-end">ราคา</th><th class="text-center">จำนวน</th><th class="text-end">รวม</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <?php if ($item['image_url']): ?><img src="<?=e($item['image_url'])?>" class="product-thumb" alt=""><?php endif; ?>
                  <span class="small fw-semibold"><?=e($item['product_name'])?></span>
                </div>
              </td>
              <td class="small text-muted"><?=e($item['sku_snapshot']??'-')?></td>
              <td class="text-end small"><?=formatPrice((float)$item['unit_price'])?></td>
              <td class="text-center small"><?=$item['quantity']?></td>
              <td class="text-end fw-semibold small"><?=formatPrice((float)$item['subtotal'])?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr><td colspan="4" class="text-end text-muted small">ราคาสินค้า</td><td class="text-end small"><?=formatPrice((float)$order['subtotal'])?></td></tr>
              <tr><td colspan="4" class="text-end text-muted small">ค่าจัดส่ง</td><td class="text-end small"><?=formatPrice((float)$order['shipping_fee'])?></td></tr>
              <?php if ((float)$order['voucher_discount']>0): ?>
              <tr><td colspan="4" class="text-end text-muted small">ส่วนลดโค้ด</td><td class="text-end small text-danger">-<?=formatPrice((float)$order['voucher_discount'])?></td></tr>
              <?php endif; ?>
              <?php if ((float)$order['shop_discount']>0): ?>
              <tr><td colspan="4" class="text-end text-muted small">ส่วนลดร้านค้า</td><td class="text-end small text-danger">-<?=formatPrice((float)$order['shop_discount'])?></td></tr>
              <?php endif; ?>
              <tr class="table-light"><td colspan="4" class="text-end fw-bold">ยอดรวมทั้งหมด</td><td class="text-end fw-bold text-danger"><?=formatPrice((float)$order['total_amount'])?></td></tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
    <!-- Update Status Form -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-arrow-repeat me-2"></i>อัปเดตสถานะออเดอร์</div>
      <div class="card-body">
        <form method="POST" class="row g-3">
          <?=csrfField()?>
          <div class="col-md-4">
            <label class="form-label">สถานะใหม่</label>
            <select class="form-select" name="order_status" required>
              <?php foreach (ORDER_STATUSES as $sv=>$sc): ?>
              <option value="<?=$sv?>" <?=$order['order_status']===$sv?'selected':''?>><?=$sc['label']?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">เลข Tracking (ถ้ามี)</label>
            <input type="text" class="form-control" name="tracking_number" value="<?=e($order['tracking_number']??'')?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">หมายเหตุ</label>
            <input type="text" class="form-control" name="note" placeholder="หมายเหตุ...">
          </div>
          <div class="col-12"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึกสถานะ</button></div>
        </form>
      </div>
    </div>
    <!-- Timeline -->
    <div class="card">
      <div class="card-header"><i class="bi bi-clock-history me-2"></i>ประวัติสถานะ</div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <?php foreach ($history as $h): $hc=ORDER_STATUSES[$h['status']]['class']??'secondary'; ?>
          <li class="list-group-item d-flex gap-3">
            <span class="mt-1"><span class="badge bg-<?=$hc?> rounded-pill p-1">&nbsp;</span></span>
            <div>
              <div class="fw-semibold small"><?=ORDER_STATUSES[$h['status']]['label']??$h['status']?></div>
              <?php if ($h['note']): ?><div class="text-muted small"><?=e($h['note'])?></div><?php endif; ?>
              <div class="text-muted" style="font-size:.72rem"><?=formatDate($h['created_at'])?> <?=$h['username']?'· by '.e($h['username']):''?></div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
  <!-- Right Column -->
  <div class="col-lg-4">
    <!-- Buyer Info -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-person me-2"></i>ข้อมูลผู้ซื้อ</div>
      <div class="card-body small">
        <p class="mb-1 fw-semibold"><?=e($order['full_name']?:$order['username'])?></p>
        <p class="mb-1 text-muted"><i class="bi bi-envelope me-1"></i><?=e($order['email'])?></p>
        <p class="mb-0 text-muted"><i class="bi bi-telephone me-1"></i><?=e($order['buyer_phone']??'-')?></p>
      </div>
    </div>
    <!-- Shipping Address -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-geo-alt me-2"></i>ที่อยู่จัดส่ง</div>
      <div class="card-body small">
        <p class="mb-1 fw-semibold"><?=e($order['recipient_name'])?></p>
        <p class="mb-1 text-muted"><i class="bi bi-telephone me-1"></i><?=e($order['addr_phone'])?></p>
        <p class="mb-0 text-muted"><?=e($order['address_line1'])?> <?=e($order['address_line2']??'')?><br><?=e($order['district'])?>, <?=e($order['province'])?> <?=e($order['postal_code'])?>  <?=e($order['country'])?></p>
        <?php if ($order['provider_name']): ?><hr class="my-2"><p class="mb-0"><i class="bi bi-truck me-1"></i><?=e($order['provider_name'])?></p><?php endif; ?>
      </div>
    </div>
    <!-- Shop Info -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-shop me-2"></i>ร้านค้า</div>
      <div class="card-body small">
        <p class="mb-1 fw-semibold"><?=e($order['shop_name'])?></p>
        <a href="<?=ADMIN_URL?>/shops/view.php?id=<?=$order['shop_id']?>" class="btn btn-sm btn-outline-primary mt-1">ดูร้านค้า</a>
      </div>
    </div>
    <!-- Payment -->
    <?php if ($payment): ?>
    <div class="card">
      <div class="card-header"><i class="bi bi-credit-card me-2"></i>การชำระเงิน</div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5 text-muted">วิธีชำระ</dt><dd class="col-7"><?=e(strtoupper($payment['payment_method']))?></dd>
          <dt class="col-5 text-muted">จำนวน</dt><dd class="col-7 fw-bold text-danger"><?=formatPrice((float)$payment['amount'])?></dd>
          <dt class="col-5 text-muted">สถานะ</dt><dd class="col-7"><span class="badge bg-<?=PAYMENT_STATUSES[$payment['status']]['class']??'secondary'?>"><?=PAYMENT_STATUSES[$payment['status']]['label']??$payment['status']?></span></dd>
          <?php if ($payment['transaction_ref']): ?><dt class="col-5 text-muted">Ref</dt><dd class="col-7 text-break"><?=e($payment['transaction_ref'])?></dd><?php endif; ?>
          <?php if ($payment['paid_at']): ?><dt class="col-5 text-muted">ชำระเมื่อ</dt><dd class="col-7"><?=formatDate($payment['paid_at'])?></dd><?php endif; ?>
        </dl>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
