<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';
frontRequireLogin('/webshop/checkout.php');

$userId = (int)$_SESSION['front_user_id'];
$isBuyNow = isset($_GET['buy_now']) || isset($_POST['buy_now_id']);
$cartItems = [];

if (isset($_GET['buy_now'])) {
    $pid = (int)$_GET['buy_now'];
    $qty = max(1, (int)($_GET['qty'] ?? 1));
    $p = getProductById($pid);
    if ($p) {
        $cartItems = [[
            'product_id' => $p['product_id'],
            'name'       => $p['name'],
            'slug'       => $p['slug'],
            'image_url'  => $p['main_image'],
            'effective_price' => (float)($p['discount_price'] ?: $p['base_price']),
            'quantity'   => $qty,
            'subtotal'   => (float)($p['discount_price'] ?: $p['base_price']) * $qty,
            'shop_id'    => $p['shop_id'],
            'shop_name'  => $p['shop_name'],
            'shop_slug'  => $p['shop_slug']
        ]];
    } else {
        header('Location: /webshop/'); exit;
    }
} elseif (isset($_POST['buy_now_id'])) {
    // Re-mock for POST handler
    $pid = (int)$_POST['buy_now_id'];
    $qty = max(1, (int)($_POST['buy_now_qty'] ?? 1));
    $p = getProductById($pid);
    if ($p) {
        $cartItems = [[
            'product_id' => $p['product_id'],
            'name'       => $p['name'],
            'slug'       => $p['slug'],
            'image_url'  => $p['main_image'],
            'effective_price' => (float)($p['discount_price'] ?: $p['base_price']),
            'quantity'   => $qty,
            'subtotal'   => (float)($p['discount_price'] ?: $p['base_price']) * $qty,
            'shop_id'    => $p['shop_id'],
            'shop_name'  => $p['shop_name'],
            'shop_slug'  => $p['shop_slug']
        ]];
    }
} else {
    $cartItems = getCart($userId);
    if (empty($cartItems)) { header('Location: /webshop/cart.php'); exit; }
}

// Addresses
$addrStmt = getDB()->prepare("SELECT * FROM user_addresses WHERE user_id=? ORDER BY is_default DESC, address_id");
$addrStmt->execute([$userId]);
$addresses = $addrStmt->fetchAll();

// Shipping providers
$provStmt = getDB()->query("SELECT * FROM shipping_providers WHERE is_active=1 ORDER BY name");
$providers = $provStmt->fetchAll();

// Grouped by shop
$byShop = [];
foreach ($cartItems as $item) {
    if (!isset($byShop[$item['shop_id']])) {
        $byShop[$item['shop_id']] = ['shop_name' => $item['shop_name'], 'items' => []];
    }
    $byShop[$item['shop_id']]['items'][] = $item;
}

$subtotal = array_sum(array_column($cartItems, 'subtotal'));
$shipping = $subtotal >= 500 ? 0.0 : 40.0;

// Handle POST
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $addrId  = (int)($_POST['address_id'] ?? 0);
    $provId  = (int)($_POST['provider_id'] ?? 1);
    $payment = $_POST['payment_method'] ?? 'cod';
    $note    = trim($_POST['note'] ?? '');

    if (!$addrId) { $error = 'กรุณาเลือกที่อยู่จัดส่ง'; }
    else {
        $db = getDB();
        $db->beginTransaction();
        try {
            foreach ($byShop as $shopId => $group) {
                $orderSubtotal = array_sum(array_column($group['items'], 'subtotal'));
                $orderShipping = $orderSubtotal >= 500 ? 0.0 : 40.0;
                $orderTotal    = $orderSubtotal + $orderShipping;
                $orderNum = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT);
                
                $db->prepare("INSERT INTO orders (order_number,buyer_user_id,shop_id,address_id,provider_id,subtotal,shipping_fee,total_amount,payment_method,payment_status,order_status,note) VALUES (?,?,?,?,?,?,?,?,?,'pending','pending',?)")
                   ->execute([$orderNum, $userId, $shopId, $addrId, $provId, $orderSubtotal, $orderShipping, $orderTotal, $payment, $note]);
                $orderId = (int)$db->lastInsertId();
                
                foreach ($group['items'] as $item) {
                    $db->prepare("INSERT INTO order_items (order_id,product_id,product_name,image_url,unit_price,quantity,subtotal) VALUES (?,?,?,?,?,?,?)")
                       ->execute([$orderId, $item['product_id'], $item['name'], $item['image_url'], $item['effective_price'], $item['quantity'], $item['subtotal']]);
                    // Logic to reduce stock could go here
                }
                $db->prepare("INSERT INTO payments (order_id,payment_method,amount,currency,status) VALUES (?,?,?,'THB','pending')")
                   ->execute([$orderId, $payment, $orderTotal]);
                $db->prepare("INSERT INTO order_status_history (order_id,status,note,created_by) VALUES (?,'pending','Order placed',?)")->execute([$orderId, $userId]);
            }
            
            if (!$isBuyNow) {
                $cartId = getCartId($userId);
                $db->prepare("DELETE FROM cart_items WHERE cart_id=?")->execute([$cartId]);
            }
            $db->commit();
            header("Location: /webshop/order-success.php?order=" . ($orderId ?? 0)); exit;
        } catch(Exception $e) {
            $db->rollBack();
            $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'ยืนยันการสั่งซื้อ';
include __DIR__ . '/includes/header.php';
?>

<div class="container-xl py-4">
  <h4 class="fw-bold mb-4">ทำการสั่งซื้อ</h4>
  
  <?php if ($error): ?>
  <div class="alert alert-danger mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <?php if (isset($_GET['buy_now'])): ?>
    <input type="hidden" name="buy_now_id" value="<?= (int)$_GET['buy_now'] ?>">
    <input type="hidden" name="buy_now_qty" value="<?= (int)$_GET['qty'] ?>">
    <?php elseif (isset($_POST['buy_now_id'])): ?>
    <input type="hidden" name="buy_now_id" value="<?= (int)$_POST['buy_now_id'] ?>">
    <input type="hidden" name="buy_now_qty" value="<?= (int)$_POST['buy_now_qty'] ?>">
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Step 1: Address -->
        <div class="checkout-step">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><span class="step-num">1</span>ที่อยู่ในการจัดส่ง</h5>
          </div>
          <?php if (empty($addresses)): ?>
          <div class="alert alert-warning">คุณยังไม่มีที่อยู่สำหรับจัดส่ง <a href="/webshop/account/addresses.php?add=1&redirect=/webshop/checkout.php">เพิ่มที่อยู่ใหม่</a></div>
          <?php else: ?>
          <div class="row g-3">
            <?php foreach ($addresses as $addr): ?>
            <div class="col-12">
              <label class="payment-option d-block" style="cursor:pointer">
                <input type="radio" name="address_id" value="<?= $addr['address_id'] ?>" <?= $addr['is_default'] ? 'checked' : '' ?> class="d-none">
                <div class="d-flex justify-content-between">
                  <div class="fw-bold"><?= e($addr['recipient_name']) ?> (<?= e($addr['phone']) ?>)
                    <?php if ($addr['is_default']): ?><span class="badge bg-orange ms-1" style="font-size:11px">ค่าเริ่มต้น</span><?php endif; ?>
                  </div>
                </div>
                <div class="text-muted mt-1" style="font-size:13px">
                  <?= e($addr['address_line1']) ?>, <?= e($addr['district']) ?>, <?= e($addr['province']) ?> <?= e($addr['postal_code']) ?>
                </div>
              </label>
            </div>
            <?php endforeach; ?>
            <div class="col-12">
              <a href="/webshop/account/addresses.php?add=1&redirect=/webshop/checkout.php" class="btn btn-sm btn-outline-orange">
                <i class="bi bi-plus-circle me-1"></i>เพิ่มที่อยู่ใหม่
              </a>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Step 2: Shipping -->
        <div class="checkout-step">
          <h5><span class="step-num">2</span>วิธีจัดส่ง</h5>
          <div id="providerBox">
            <?php foreach ($providers as $p): ?>
            <label class="payment-option mb-2" style="cursor:pointer">
              <input type="radio" name="provider_id" value="<?= $p['provider_id'] ?>" <?= $p['provider_id']==1?'checked':'' ?> class="d-none">
              <i class="bi bi-truck fs-5 text-orange"></i>
              <div class="flex-fill">
                <div class="fw-semibold" style="font-size:14px"><?= e($p['name']) ?></div>
                <div class="text-muted" style="font-size:12px">ส่งภายใน <?= $p['estimated_days_min']??1 ?>-<?= $p['estimated_days_max']??3 ?> วัน</div>
              </div>
              <span class="text-orange fw-bold"><?= $subtotal >= 500 ? 'ฟรี' : '฿'.number_format($p['base_rate'] ?? 40, 0) ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Step 3: Items -->
        <div class="checkout-step">
          <h5><span class="step-num">3</span>รายการสินค้า</h5>
          <?php foreach ($cartItems as $item): ?>
          <div class="d-flex gap-3 align-items-center py-2 border-bottom">
            <img src="<?= e($item['image_url'] ?: 'https://via.placeholder.com/60') ?>" class="rounded" width="60" height="60" style="object-fit:cover" alt="">
            <div class="flex-fill">
              <div style="font-size:14px;font-weight:500"><?= e($item['name']) ?></div>
              <div class="text-muted" style="font-size:12px">จาก: <?= e($item['shop_name']) ?></div>
            </div>
            <div class="text-end">
              <div class="text-orange fw-bold">฿<?= number_format($item['effective_price'],0) ?></div>
              <div class="text-muted" style="font-size:12px">x<?= $item['quantity'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
          <div class="mt-3">
            <label class="form-label fw-semibold">หมายเหตุถึงผู้ขาย</label>
            <textarea name="note" class="form-control" rows="2" placeholder="ข้อความถึงผู้ขาย (ไม่จำเป็น)"></textarea>
          </div>
        </div>

        <!-- Step 4: Payment -->
        <div class="checkout-step">
          <h5><span class="step-num">4</span>วิธีชำระเงิน</h5>
          <div class="row g-2">
            <?php foreach ([['cod','เก็บเงินปลายทาง (COD)','bi-cash-coin'],['bank_transfer','โอนเงินผ่านธนาคาร','bi-bank'],['credit_card','บัตรเครดิต/เดบิต','bi-credit-card'],['shopee_pay','Shopee Pay','bi-wallet2']] as [$val,$lbl,$ico]): ?>
            <div class="col-md-6">
              <label class="payment-option <?= $val==='cod'?'selected':'' ?>" style="cursor:pointer" data-method="<?= $val ?>">
                <input type="radio" name="payment_method" value="<?= $val ?>" <?= $val==='cod'?'checked':'' ?> class="d-none">
                <i class="bi <?= $ico ?> fs-5 payment-icon"></i>
                <span><?= $lbl ?></span>
              </label>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="surface shadow-sm sticky-top" style="top:90px">
          <h6 class="fw-bold mb-3">สรุปคำสั่งซื้อ</h6>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">รวมค่าสินค้า</span>
            <span>฿<?= number_format($subtotal, 0) ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">ค่าจัดส่ง</span>
            <span><?= $shipping > 0 ? '฿'.number_format($shipping,0) : 'ฟรี' ?></span>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-4">
            <span class="fw-bold">ยอดสุทธิ</span>
            <span class="text-orange fw-bold fs-4">฿<?= number_format($subtotal + $shipping, 0) ?></span>
          </div>
          <button type="submit" class="btn btn-orange w-100 py-3 fw-bold">สั่งซื้อสินค้า</button>
          <p class="text-muted x-small text-center mt-3 mb-0">การกดสั่งซื้อแสดงว่าคุณยอมรับเงื่อนไขการใช้บริการ</p>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const opts = document.querySelectorAll('.payment-option');
    opts.forEach(opt => {
        opt.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                opts.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
            }
        });
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
