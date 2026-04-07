<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
frontRequireLogin();

$orderId = (int)($_GET['id'] ?? 0);
$userId  = (int)$_SESSION['front_user_id'];

$stmt = getDB()->prepare("
    SELECT o.*, s.shop_name,s.shop_slug,
           a.recipient_name,a.phone,a.address_line1,a.district,a.province,a.postal_code,
           sp.name AS provider_name, sp.tracking_url
    FROM orders o
    JOIN shops s ON o.shop_id=s.shop_id
    LEFT JOIN user_addresses a ON o.address_id=a.address_id
    LEFT JOIN shipping_providers sp ON o.provider_id=sp.provider_id
    WHERE o.order_id=? AND o.buyer_user_id=?
");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch();
if (!$order) { header('Location: /webshop/account/orders.php'); exit; }

$itemStmt = getDB()->prepare("SELECT * FROM order_items WHERE order_id=?");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

$histStmt = getDB()->prepare("SELECT * FROM order_status_history WHERE order_id=? ORDER BY created_at ASC");
$histStmt->execute([$orderId]);
$history = $histStmt->fetchAll();

$pageTitle = 'คำสั่งซื้อ #' . $order['order_number'];
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container-xl py-3">
  <div class="row g-3">
    <div class="col-md-3">
      <?php include __DIR__ . '/includes/account_sidebar.php'; ?>
    </div>
    <div class="col-md-9">
      <!-- Header -->
      <div class="surface mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <a href="/webshop/account/orders.php" class="text-muted text-decoration-none" style="font-size:13px"><i class="bi bi-arrow-left me-1"></i>กลับรายการคำสั่งซื้อ</a>
          <h5 class="fw-bold mb-0 mt-1"><?= e($order['order_number']) ?></h5>
          <div class="text-muted" style="font-size:13px">สั่งซื้อเมื่อ: <?= formatDate($order['created_at'],'d M Y H:i') ?></div>
        </div>
        <span class="status-badge status-<?= $order['order_status'] ?> fs-6 px-3 py-2"><?= e(is_array($os=ORDER_STATUSES[$order['order_status']]??$order['order_status']) ? ($os['label']??$order['order_status']) : $os) ?></span>
      </div>

      <div class="row g-3">
        <div class="col-lg-8">
          <!-- Order Items -->
          <div class="surface mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-shop me-1 text-orange"></i><a href="/webshop/shop.php?slug=<?= e($order['shop_slug']) ?>" class="text-dark"><?= e($order['shop_name']) ?></a></h6>
            <?php foreach ($items as $item): ?>
            <div class="d-flex gap-3 align-items-center py-3 border-bottom">
              <img src="<?= e($item['image_url'] ?: 'https://via.placeholder.com/72') ?>" class="rounded" width="72" height="72" style="object-fit:cover;flex-shrink:0" alt="">
              <div class="flex-fill">
                <div class="fw-semibold" style="font-size:14px"><?= e($item['product_name']) ?></div>
                <?php if ($item['variant_info']??null): ?><div class="text-muted" style="font-size:12px"><?= e($item['variant_info']) ?></div><?php endif; ?>
                <div class="text-muted mt-1" style="font-size:12px">฿<?= number_format((float)$item['unit_price'],0) ?> × <?= $item['quantity'] ?></div>
              </div>
              <div class="text-end flex-shrink-0">
                <div class="text-orange fw-bold">฿<?= number_format((float)$item['subtotal'],0) ?></div>
                <?php if (in_array($order['order_status'],['delivered','completed'])): ?>
                <a href="/webshop/product.php?slug=<?= e($item['product_slug'] ?? '') ?>" class="btn btn-sm btn-outline-orange mt-1" style="font-size:11px">รีวิว</a>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>

            <!-- Note -->
            <?php if ($order['note']): ?>
            <div class="mt-2 p-2 bg-light rounded" style="font-size:13px">
              <i class="bi bi-chat-left-text me-1 text-muted"></i>หมายเหตุ: <?= e($order['note']) ?>
            </div>
            <?php endif; ?>
          </div>

          <!-- Tracking -->
          <?php if ($order['tracking_number']): ?>
          <div class="surface mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-truck me-1 text-orange"></i>ติดตามพัสดุ</h6>
            <div class="d-flex align-items-center gap-3 flex-wrap">
              <div>
                <div class="text-muted" style="font-size:12px">บริษัทขนส่ง</div>
                <div class="fw-semibold"><?= e($order['provider_name'] ?? '–') ?></div>
              </div>
              <div>
                <div class="text-muted" style="font-size:12px">หมายเลขพัสดุ</div>
                <div class="fw-semibold d-flex align-items-center gap-1">
                  <?= e($order['tracking_number']) ?>
                  <button class="btn btn-sm btn-link p-0" data-copy="<?= e($order['tracking_number']) ?>"><i class="bi bi-copy"></i></button>
                </div>
              </div>
              <?php if ($order['tracking_url']): ?>
              <a href="<?= e($order['tracking_url']) ?>?tracking=<?= urlencode($order['tracking_number']) ?>" target="_blank" class="btn btn-sm btn-outline-orange ms-auto">
                <i class="bi bi-box-arrow-up-right me-1"></i>ติดตามพัสดุ
              </a>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>

          <!-- Status Timeline -->
          <div class="surface">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1 text-orange"></i>ประวัติสถานะ</h6>
            <div class="position-relative ps-4" style="border-left:2px solid #eee">
              <?php foreach (array_reverse($history) as $h): ?>
              <div class="mb-3 position-relative">
                <div class="position-absolute" style="left:-25px;top:2px;width:12px;height:12px;background:var(--shopee-orange);border-radius:50%"></div>
                <div class="fw-semibold" style="font-size:14px"><?= e(is_array($os=ORDER_STATUSES[$h['status']]??$h['status']) ? ($os['label']??$h['status']) : $os) ?></div>
                <div class="text-muted" style="font-size:12px"><?= formatDate($h['created_at'],'d M Y H:i') ?></div>
                <?php if ($h['note']): ?><div class="text-muted mt-1" style="font-size:12px"><?= e($h['note']) ?></div><?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <!-- Summary -->
          <div class="surface mb-3">
            <h6 class="fw-bold mb-3">สรุปยอดชำระ</h6>
            <div class="d-flex justify-content-between mb-2" style="font-size:13px"><span class="text-muted">ราคาสินค้า</span><span>฿<?= number_format((float)$order['subtotal'],0) ?></span></div>
            <div class="d-flex justify-content-between mb-2" style="font-size:13px"><span class="text-muted">ค่าจัดส่ง</span><span><?= (float)$order['shipping_fee']==0?'ฟรี':'฿'.number_format((float)$order['shipping_fee'],0) ?></span></div>
            <?php if ((float)($order['voucher_discount']??0)>0): ?>
            <div class="d-flex justify-content-between mb-2" style="font-size:13px"><span class="text-muted">ส่วนลด</span><span class="text-danger">-฿<?= number_format((float)$order['voucher_discount'],0) ?></span></div>
            <?php endif; ?>
            <hr>
            <div class="d-flex justify-content-between fw-bold"><span>ยอดรวม</span><span class="text-orange fs-6">฿<?= number_format((float)$order['total_amount'],0) ?></span></div>
            <div class="mt-2 text-muted" style="font-size:12px">วิธีชำระ: <?= PAYMENT_METHODS[$order['payment_method']] ?? $order['payment_method'] ?></div>
          </div>

          <!-- Address -->
          <div class="surface mb-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt me-1 text-orange"></i>ที่อยู่จัดส่ง</h6>
            <div style="font-size:13px">
              <div class="fw-semibold"><?= e($order['recipient_name']) ?></div>
              <div class="text-muted"><?= e($order['phone']) ?></div>
              <div class="mt-1"><?= e($order['address_line1']) ?>, <?= e($order['district']) ?>, <?= e($order['province']) ?> <?= e($order['postal_code']) ?></div>
            </div>
          </div>

          <!-- Actions -->
          <div class="surface">
            <h6 class="fw-bold mb-3">การดำเนินการ</h6>
            <?php if ($order['order_status']==='pending'): ?>
            <form method="POST" action="/webshop/api/order-action.php" class="mb-2">
              <input type="hidden" name="order_id" value="<?= $orderId ?>">
              <input type="hidden" name="action" value="cancel">
              <button class="btn btn-outline-danger w-100" onclick="return confirm('ยืนยันยกเลิก?')"><i class="bi bi-x-circle me-1"></i>ยกเลิกคำสั่งซื้อ</button>
            </form>
            <?php endif; ?>
            <?php if ($order['order_status']==='shipped'): ?>
            <form method="POST" action="/webshop/api/order-action.php" class="mb-2">
              <input type="hidden" name="order_id" value="<?= $orderId ?>">
              <input type="hidden" name="action" value="confirm_received">
              <button class="btn btn-orange w-100" onclick="return confirm('ยืนยันว่าได้รับสินค้าแล้ว?')"><i class="bi bi-check-circle me-1"></i>ยืนยันรับสินค้าแล้ว</button>
            </form>
            <?php endif; ?>
            <?php if (in_array($order['order_status'],['delivered','completed'])): ?>
            <a href="/webshop/search.php" class="btn btn-outline-orange w-100 mb-2">
              <i class="bi bi-arrow-repeat me-1"></i>ซื้อสินค้าอีกครั้ง
            </a>
            <?php endif; ?>
            <?php if (in_array($order['order_status'],['delivered','completed']) && !($order['return_requested']??false)): ?>
            <a href="/webshop/account/orders.php?return=<?= $orderId ?>" class="btn btn-outline-secondary w-100 mb-2" style="font-size:13px">
              <i class="bi bi-arrow-return-left me-1"></i>คืน/คืนเงิน
            </a>
            <?php endif; ?>
            <a href="/webshop/account/support.php" class="btn btn-link w-100 text-muted" style="font-size:12px">
              <i class="bi bi-headset me-1"></i>ติดต่อฝ่ายบริการลูกค้า
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
