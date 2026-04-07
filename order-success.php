<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';
frontRequireLogin();

$orderId = (int)($_GET['order'] ?? 0);
$stmt    = getDB()->prepare("SELECT o.*,a.recipient_name,a.phone,a.address_line1,a.district,a.province,a.postal_code,sp.name AS provider_name FROM orders o LEFT JOIN user_addresses a ON o.address_id=a.address_id LEFT JOIN shipping_providers sp ON o.provider_id=sp.provider_id WHERE o.order_id=? AND o.buyer_user_id=?");
$stmt->execute([$orderId, $_SESSION['front_user_id']]);
$order = $stmt->fetch();
if (!$order) { header('Location: /webshop/account/orders.php'); exit; }

$itemStmt = getDB()->prepare("SELECT * FROM order_items WHERE order_id=?");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

$pageTitle = 'สั่งซื้อสำเร็จ';
include __DIR__ . '/includes/header.php';
?>

<div class="container-xl py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">

      <!-- Success Header -->
      <div class="surface text-center mb-3 py-5">
        <div class="mb-3">
          <div style="width:80px;height:80px;background:linear-gradient(135deg,#00c853,#64dd17);border-radius:50%;display:inline-flex;align-items:center;justify-content:center">
            <i class="bi bi-check-lg text-white" style="font-size:40px"></i>
          </div>
        </div>
        <h3 class="fw-bold text-success mb-2">สั่งซื้อสำเร็จแล้ว!</h3>
        <p class="text-muted mb-1">หมายเลขคำสั่งซื้อ: <strong class="text-dark"><?= e($order['order_number']) ?></strong>
          <button class="btn btn-sm btn-link p-0 ms-1" data-copy="<?= e($order['order_number']) ?>"><i class="bi bi-copy"></i></button>
        </p>
        <p class="text-muted" style="font-size:13px">เราได้รับคำสั่งซื้อของคุณแล้ว และกำลังดำเนินการ</p>
      </div>

      <!-- Payment Notice -->
      <?php if ($order['payment_method'] === 'bank_transfer'): ?>
      <div class="alert alert-warning">
        <h6 class="fw-bold"><i class="bi bi-bank me-2"></i>กรุณาโอนเงินภายใน 24 ชั่วโมง</h6>
        <p class="mb-1 small">ธนาคาร: ไทยพาณิชย์ (SCB) | บัญชี: บริษัท Shopee TH</p>
        <p class="mb-1 small">เลขบัญชี: <strong>123-456789-0</strong></p>
        <p class="mb-0 small">ยอดที่ต้องโอน: <strong class="text-danger">฿<?= number_format((float)$order['total_amount'],0) ?></strong></p>
      </div>
      <?php endif; ?>

      <!-- Order Summary Card -->
      <div class="surface">
        <div class="row g-3 mb-3">
          <div class="col-sm-6">
            <div style="font-size:13px;color:#666;margin-bottom:4px">วันที่สั่งซื้อ</div>
            <div class="fw-semibold"><?= formatDate($order['created_at'],'d M Y H:i') ?></div>
          </div>
          <div class="col-sm-6">
            <div style="font-size:13px;color:#666;margin-bottom:4px">สถานะ</div>
            <span class="status-badge status-<?= $order['order_status'] ?>"><?= e(is_array($os=ORDER_STATUSES[$order['order_status']]??$order['order_status']) ? ($os['label']??$order['order_status']) : $os) ?></span>
          </div>
          <div class="col-sm-6">
            <div style="font-size:13px;color:#666;margin-bottom:4px">วิธีชำระเงิน</div>
            <div class="fw-semibold"><?= e(PAYMENT_METHODS[$order['payment_method']] ?? $order['payment_method']) ?></div>
          </div>
          <div class="col-sm-6">
            <div style="font-size:13px;color:#666;margin-bottom:4px">บริษัทขนส่ง</div>
            <div class="fw-semibold"><?= e($order['provider_name'] ?? '–') ?></div>
          </div>
        </div>

        <!-- Address -->
        <div class="p-3 rounded mb-3" style="background:#f9f9f9;font-size:13px">
          <div class="fw-semibold mb-1"><i class="bi bi-geo-alt me-1 text-orange"></i>ที่อยู่จัดส่ง</div>
          <div><?= e($order['recipient_name']) ?> | <?= e($order['phone']) ?></div>
          <div class="text-muted"><?= e($order['address_line1']) ?>, <?= e($order['district']) ?>, <?= e($order['province']) ?> <?= e($order['postal_code']) ?></div>
        </div>

        <!-- Items -->
        <h6 class="fw-bold mb-3"><i class="bi bi-bag me-1 text-orange"></i>รายการสินค้า (<?= count($items) ?> รายการ)</h6>
        <?php foreach ($items as $item): ?>
        <div class="d-flex gap-3 align-items-center py-2 border-bottom">
          <img src="<?= e($item['image_url'] ?: 'https://via.placeholder.com/56') ?>" class="rounded" width="56" height="56" style="object-fit:cover" alt="">
          <div class="flex-fill">
            <div style="font-size:14px;font-weight:500"><?= e($item['product_name']) ?></div>
            <div class="text-muted" style="font-size:12px">฿<?= number_format((float)$item['unit_price'],0) ?> × <?= $item['quantity'] ?></div>
          </div>
          <div class="text-orange fw-bold">฿<?= number_format((float)$item['subtotal'],0) ?></div>
        </div>
        <?php endforeach; ?>

        <!-- Totals -->
        <div class="mt-3 pt-2">
          <div class="d-flex justify-content-between mb-1" style="font-size:13px"><span class="text-muted">ราคาสินค้า</span><span>฿<?= number_format((float)$order['subtotal'],0) ?></span></div>
          <div class="d-flex justify-content-between mb-1" style="font-size:13px"><span class="text-muted">ค่าจัดส่ง</span><span><?= (float)$order['shipping_fee']==0?'ฟรี':'฿'.number_format((float)$order['shipping_fee'],0) ?></span></div>
          <?php if ((float)$order['voucher_discount'] > 0): ?>
          <div class="d-flex justify-content-between mb-1" style="font-size:13px"><span class="text-muted">ส่วนลด</span><span class="text-danger">-฿<?= number_format((float)$order['voucher_discount'],0) ?></span></div>
          <?php endif; ?>
          <div class="d-flex justify-content-between mt-2 fw-bold fs-5 border-top pt-2">
            <span>ยอดรวม</span>
            <span class="text-orange">฿<?= number_format((float)$order['total_amount'],0) ?></span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="d-flex gap-3 mt-3 flex-wrap">
        <a href="/webshop/account/orders.php" class="btn btn-orange flex-fill">
          <i class="bi bi-bag me-1"></i>ดูคำสั่งซื้อทั้งหมด
        </a>
        <a href="/webshop/" class="btn btn-outline-orange flex-fill">
          <i class="bi bi-house me-1"></i>ช้อปต่อ
        </a>
      </div>

      <!-- Recommendation -->
      <div class="mt-4 text-center">
        <p class="text-muted small mb-3">คุณอาจสนใจสินค้าเหล่านี้ด้วย</p>
        <?php $recs = getProducts(['sort'=>'popular'],1,4); ?>
        <div class="row row-cols-2 row-cols-sm-4 g-2">
          <?php foreach ($recs['data'] as $p): ?>
          <div class="col"><?= renderProductCard($p) ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
