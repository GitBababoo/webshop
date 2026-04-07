<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';
$pageTitle = 'ตะกร้าสินค้า';

$userId    = frontIsLoggedIn() ? (int)$_SESSION['front_user_id'] : null;
$cartItems = getCart($userId);

// Group by shop
$byShop = [];
foreach ($cartItems as $item) {
    $byShop[$item['shop_id']]['shop']   = ['id'=>$item['shop_id'],'name'=>$item['shop_name'],'slug'=>$item['shop_slug']];
    $byShop[$item['shop_id']]['items'][] = $item;
}

$subtotal = array_sum(array_column($cartItems, 'subtotal'));
$shipping = $subtotal >= 500 ? 0.0 : 40.0;
$total    = $subtotal + $shipping;

include __DIR__ . '/includes/header.php';
?>

<div class="site-breadcrumb">
  <div class="container-xl">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/webshop/">หน้าแรก</a></li>
      <li class="breadcrumb-item active">ตะกร้าสินค้า (<?= count($cartItems) ?>)</li>
    </ol>
  </div>
</div>

<div class="container-xl py-3">
  <?php if (empty($cartItems)): ?>
  <div class="empty-state surface">
    <div class="empty-icon"><i class="bi bi-cart-x"></i></div>
    <h5>ตะกร้าของคุณยังว่างอยู่</h5>
    <p class="text-muted">เริ่มช้อปปิ้งและเพิ่มสินค้าที่คุณชอบลงในตะกร้า</p>
    <a href="/webshop/" class="btn btn-orange px-5">เริ่มช้อปเลย</a>
  </div>
  <?php else: ?>
  <div class="row g-3">
    <div class="col-lg-8">
      <!-- Select All -->
      <div class="surface mb-2 d-flex align-items-center gap-3 py-2">
        <input type="checkbox" id="selectAll" class="form-check-input" style="width:20px;height:20px" checked>
        <label for="selectAll" class="fw-semibold mb-0">เลือกทั้งหมด (<?= count($cartItems) ?> รายการ)</label>
        <button class="btn btn-sm btn-outline-danger ms-auto" onclick="if(confirm('ลบสินค้าที่เลือก?'))location.reload()">
          <i class="bi bi-trash me-1"></i>ลบที่เลือก
        </button>
      </div>

      <!-- Items grouped by shop -->
      <?php foreach ($byShop as $shopGroup): ?>
      <div class="cart-item mb-2">
        <!-- Shop header -->
        <div class="d-flex align-items-center gap-2 pb-2 mb-3 border-bottom">
          <input type="checkbox" class="form-check-input shop-check" style="width:18px;height:18px">
          <i class="bi bi-shop text-orange"></i>
          <a href="/webshop/shop.php?slug=<?= e($shopGroup['shop']['slug']) ?>" class="fw-semibold text-dark"><?= e($shopGroup['shop']['name']) ?></a>
        </div>
        <!-- Items -->
        <?php foreach ($shopGroup['items'] as $item): ?>
        <div class="d-flex gap-3 align-items-start mb-3 pb-3 border-bottom" data-item-row="<?= $item['cart_item_id'] ?>" data-price="<?= $item['effective_price'] ?>">
          <input type="checkbox" class="cart-item-check form-check-input mt-2" style="width:18px;height:18px" checked>
          <a href="/webshop/product.php?slug=<?= e($item['slug']) ?>">
            <img src="<?= e($item['image_url'] ?: 'https://via.placeholder.com/90x90?text=No+Image') ?>" class="cart-img flex-shrink-0" alt="">
          </a>
          <div class="flex-fill">
            <a href="/webshop/product.php?slug=<?= e($item['slug']) ?>" class="d-block fw-semibold text-dark mb-1" style="font-size:14px"><?= e($item['name']) ?></a>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span class="text-orange fw-bold fs-6">฿<?= number_format($item['effective_price'], 0) ?></span>
              <?php if ($item['effective_price'] < $item['base_price']): ?>
              <span class="text-muted text-decoration-line-through" style="font-size:12px">฿<?= number_format((float)$item['base_price'], 0) ?></span>
              <?php endif; ?>
              <?php if ((int)$item['total_stock'] < 5): ?>
              <span class="badge bg-danger" style="font-size:11px">เหลือน้อย!</span>
              <?php endif; ?>
            </div>
            <div class="d-flex align-items-center gap-3 mt-2">
              <div class="quantity-ctrl">
                <button class="qty-btn" data-action="dec"><i class="bi bi-dash"></i></button>
                <input type="number" class="qty-input cart-qty-input" value="<?= $item['quantity'] ?>" min="1"
                       data-min="1" data-max="<?= $item['total_stock'] ?>" data-item-id="<?= $item['cart_item_id'] ?>">
                <button class="qty-btn" data-action="inc"><i class="bi bi-plus"></i></button>
              </div>
              <span class="text-orange fw-bold" data-item-sub="<?= $item['cart_item_id'] ?>">฿<?= number_format($item['subtotal'], 0) ?></span>
              <button class="btn btn-sm btn-link text-muted ms-auto p-0" data-remove-item="<?= $item['cart_item_id'] ?>">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- Shop Voucher -->
        <div class="d-flex align-items-center gap-2 pt-1" style="font-size:13px">
          <i class="bi bi-ticket-perforated text-orange"></i>
          <span class="text-muted">โค้ดส่วนลดร้านค้า:</span>
          <input type="text" class="form-control form-control-sm" style="max-width:160px" placeholder="กรอกโค้ด">
          <button class="btn btn-sm btn-outline-orange">ใช้</button>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Platform Voucher -->
      <div class="surface">
        <div class="d-flex align-items-center gap-3">
          <i class="bi bi-tags-fill fs-5 text-orange"></i>
          <div class="flex-fill">
            <div class="fw-semibold mb-1" style="font-size:14px">โค้ดส่วนลด Shopee</div>
            <div class="d-flex gap-2">
              <input type="text" id="voucherCode" class="form-control form-control-sm voucher-input flex-fill" placeholder="ใส่โค้ดส่วนลด">
              <button id="applyVoucher" class="btn btn-sm btn-outline-orange">ใช้โค้ด</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Summary -->
    <div class="col-lg-4">
      <div class="cart-summary-box">
        <h6 class="fw-bold border-bottom pb-3 mb-3">สรุปคำสั่งซื้อ</h6>

        <!-- Free shipping banner -->
        <?php if ($subtotal < 500): ?>
        <div class="alert alert-warning py-2" style="font-size:13px">
          <i class="bi bi-truck me-1"></i>ซื้อเพิ่ม <strong>฿<?= number_format(500-$subtotal, 0) ?></strong> รับฟรีค่าส่ง!
          <div class="progress mt-2" style="height:4px"><div class="progress-bar bg-warning" style="width:<?= min(100,($subtotal/500)*100) ?>%"></div></div>
        </div>
        <?php else: ?>
        <div class="alert alert-success py-2" style="font-size:13px">
          <i class="bi bi-truck me-1"></i>คุณได้รับ <strong>ฟรีค่าจัดส่ง!</strong>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between mb-2" style="font-size:14px">
          <span class="text-muted">ราคาสินค้า</span>
          <span>฿<?= number_format($subtotal, 0) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2" style="font-size:14px">
          <span class="text-muted">ค่าจัดส่ง</span>
          <span class="<?= $shipping==0?'text-success':'' ?>"><?= $shipping==0?'ฟรี':'฿'.number_format($shipping,0) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2" style="font-size:14px">
          <span class="text-muted">ส่วนลด</span>
          <span class="text-danger" id="voucherDiscount">-฿0</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-3 fw-bold fs-5">
          <span>ยอดรวม</span>
          <span class="text-orange" id="cartTotal">฿<?= number_format($total, 0) ?></span>
        </div>
        <div class="mb-2 text-center" style="font-size:12px;color:#999">
          <span id="selectedTotal" class="fw-bold text-orange">฿<?= number_format($total, 0) ?></span> (สินค้าที่เลือก)
        </div>

        <?php if (frontIsLoggedIn()): ?>
        <a href="/webshop/checkout.php" class="btn btn-orange w-100 py-3 fs-6 fw-bold">
          <i class="bi bi-bag-check me-2"></i>ดำเนินการสั่งซื้อ (<?= count($cartItems) ?> รายการ)
        </a>
        <?php else: ?>
        <a href="/webshop/account/login.php?redirect=/webshop/checkout.php" class="btn btn-orange w-100 py-3 fs-6 fw-bold">
          <i class="bi bi-lock me-2"></i>เข้าสู่ระบบเพื่อสั่งซื้อ
        </a>
        <?php endif; ?>

        <div class="text-center mt-3" style="font-size:12px;color:#999">
          <i class="bi bi-shield-check me-1 text-success"></i>การชำระเงินปลอดภัย 100%
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Recommended Products -->
  <?php $recs = getProducts(['sort'=>'popular'], 1, 5); if (!empty($recs['data'])): ?>
  <div class="mt-4">
    <div class="section-title">คุณอาจสนใจ</div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2">
      <?php foreach ($recs['data'] as $p): ?>
      <div class="col"><?= renderProductCard($p) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
