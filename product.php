<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';

$slug    = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: /webshop/'); exit; }
$product = getProductBySlug($slug);
if (!$product) { http_response_code(404); $pageTitle='ไม่พบสินค้า'; include __DIR__.'/includes/header.php'; echo '<div class="container py-5 text-center"><h3>ไม่พบสินค้านี้</h3><a href="/webshop/" class="btn btn-orange mt-3">กลับหน้าแรก</a></div>'; include __DIR__.'/includes/footer.php'; exit; }

$reviews      = getProductReviews((int)$product['product_id'], 1, 6);
$related      = getRelatedProducts((int)$product['product_id'], (int)$product['category_id'], (int)$product['shop_id'], 10);
$wishlistIds  = frontIsLoggedIn() ? getWishlistIds((int)$_SESSION['front_user_id']) : [];
$inWishlist   = in_array($product['product_id'], $wishlistIds);
$isFollowing  = frontIsLoggedIn() ? isFollowingShop((int)$_SESSION['front_user_id'], (int)$product['shop_id']) : false;

$price    = (float)($product['discount_price'] ?: $product['base_price']);
$origPrice = $product['discount_price'] ? (float)$product['base_price'] : null;
$discount  = $origPrice ? round((1 - $price/$origPrice)*100) : 0;
$pageTitle = $product['name'];
$metaDesc  = substr(strip_tags($product['description'] ?? ''), 0, 160);

// Questions
$qStmt = getDB()->prepare("SELECT pq.*,u.username,u.avatar_url,pa.answer,pa.is_verified FROM product_questions pq JOIN users u ON pq.user_id=u.user_id LEFT JOIN product_answers pa ON pa.question_id=pq.question_id WHERE pq.product_id=? AND pq.status='answered' ORDER BY pq.created_at DESC LIMIT 5");
$qStmt->execute([$product['product_id']]);
$questions = $qStmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<div class="site-breadcrumb">
  <div class="container-xl">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/webshop/">หน้าแรก</a></li>
      <li class="breadcrumb-item"><a href="/webshop/category.php?slug=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a></li>
      <li class="breadcrumb-item active" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($product['name']) ?></li>
    </ol>
  </div>
</div>

<div class="container-xl py-3">
  <div class="row g-3">

    <!-- Gallery -->
    <div class="col-md-5 col-lg-4">
      <div class="product-gallery surface">
        <div class="gallery-main mb-2">
          <img id="mainImage"
               src="<?= e($product['images'][0]['image_url'] ?? $product['main_image'] ?? 'https://via.placeholder.com/500') ?>"
               alt="<?= e($product['name']) ?>">
        </div>
        <div class="gallery-thumbs">
          <?php foreach ($product['images'] as $img): ?>
          <div class="thumb <?= $img['is_primary'] ? 'active' : '' ?>" data-src="<?= e($img['image_url']) ?>">
            <img src="<?= e($img['image_url']) ?>" alt="">
          </div>
          <?php endforeach; ?>
        </div>
        <!-- Wishlist + Share -->
        <div class="d-flex gap-2 mt-3 pt-2 border-top">
          <button class="btn btn-sm btn-outline-secondary flex-fill <?= $inWishlist ? 'text-orange border-orange' : '' ?>"
                  onclick="toggleWishlist(<?= $product['product_id'] ?>, this)">
            <i class="bi <?= $inWishlist ? 'bi-heart-fill' : 'bi-heart' ?> me-1"></i>
            <?= $inWishlist ? 'อยู่ใน Wishlist' : 'เพิ่ม Wishlist' ?>
          </button>
          <button class="btn btn-sm btn-outline-secondary" data-copy="<?= e('https://'.$_SERVER['HTTP_HOST'].'/webshop/product.php?slug='.$product['slug']) ?>" title="คัดลอกลิงก์">
            <i class="bi bi-share"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Product Info -->
    <div class="col-md-7 col-lg-5">
      <div class="product-info surface">
        <?php if ($product['is_featured']): ?>
        <span class="badge bg-warning text-dark mb-2"><i class="bi bi-star-fill me-1"></i>สินค้าแนะนำ</span>
        <?php endif; ?>
        <h1 class="product-title"><?= e($product['name']) ?></h1>

        <div class="product-rating-row">
          <div class="stars-display">
            <?php for ($i=1;$i<=5;$i++): ?>
            <i class="bi <?= $i <= round($product['rating']) ? 'bi-star-fill' : ($i-0.5 <= $product['rating'] ? 'bi-star-half' : 'bi-star') ?>" style="color:#ffa500"></i>
            <?php endfor; ?>
          </div>
          <span class="fw-bold"><?= number_format($product['rating'],1) ?></span>
          <span class="text-muted">(<?= number_format($product['total_reviews']) ?> รีวิว)</span>
          <span class="text-muted">|</span>
          <span class="text-muted">ขายแล้ว <?= number_format((int)$product['total_sold']) ?> ชิ้น</span>
        </div>

        <!-- Price -->
        <div class="py-3 mb-2" style="background:var(--shopee-orange-light);border-radius:4px;padding:12px 16px!important">
          <?php if ($origPrice): ?>
          <span class="price-orig">฿<?= number_format($origPrice, 0) ?></span>
          <span class="discount-badge ms-1">-<?= $discount ?>%</span>
          <?php endif; ?>
          <div class="price-main" id="productPrice" data-unit-price="<?= $price ?>">฿<?= number_format($price, 0) ?></div>
        </div>

        <!-- Brand / Condition -->
        <table class="table table-sm table-borderless mb-2" style="font-size:13px">
          <?php if ($product['brand']): ?>
          <tr><td class="text-muted" style="width:100px">แบรนด์</td><td class="fw-semibold"><?= e($product['brand']) ?></td></tr>
          <?php endif; ?>
          <tr><td class="text-muted">สภาพ</td><td><?= $product['condition_type']==='new'?'<span class="badge bg-success">ใหม่</span>':'<span class="badge bg-secondary">มือสอง</span>' ?></td></tr>
          <tr><td class="text-muted">คงเหลือ</td><td class="text-orange fw-bold"><?= number_format((int)$product['total_stock']) ?> ชิ้น</td></tr>
          <tr><td class="text-muted">SKU</td><td class="text-muted small"><?= e($product['sku'] ?? '–') ?></td></tr>
        </table>

        <!-- Quantity + Buttons -->
        <div class="mb-3">
          <div class="d-flex align-items-center gap-3 mb-3">
            <span class="text-muted" style="font-size:13px">จำนวน:</span>
            <div class="quantity-ctrl">
              <button type="button" class="qty-btn" data-action="dec" aria-label="Decrease quantity"><i class="bi bi-dash"></i></button>
              <input type="number" class="qty-input" id="qty" value="1" min="1" data-min="1" data-max="<?= (int)$product['total_stock'] ?>" readonly>
              <button type="button" class="qty-btn" data-action="inc" aria-label="Increase quantity"><i class="bi bi-plus"></i></button>
            </div>
            <span class="text-muted small">สต็อก <?= number_format((int)$product['total_stock']) ?> ชิ้น</span>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn-add-cart w-100" onclick="addToCart(<?= $product['product_id'] ?>, parseInt(document.getElementById('qty').value))">
              <i class="bi bi-cart-plus"></i> ใส่ตะกร้า
            </button>
            <a href="/webshop/checkout.php?buy_now=<?= $product['product_id'] ?>&qty=1" class="btn-buy-now w-100" id="buyNowBtn">
              <i class="bi bi-lightning-fill"></i> ซื้อเลย
            </a>
          </div>
          <script>
          document.getElementById('qty').addEventListener('change', function() {
            const qty = parseInt(this.value) || 1;
            
            // Update Price
            const priceEl = document.getElementById('productPrice');
            if (priceEl) {
              const unitPrice = parseFloat(priceEl.dataset.unitPrice);
              const total = unitPrice * qty;
              priceEl.textContent = '฿' + total.toLocaleString('th-TH', {minimumFractionDigits: 0, maximumFractionDigits: 0});
            }

            // Update Buy Now Button
            const buyNow = document.getElementById('buyNowBtn');
            if (buyNow) {
              const url = new URL(buyNow.href, window.location.origin);
              url.searchParams.set('qty', qty);
              buyNow.href = url.pathname + url.search;
            }
          });
          </script>
        </div>

        <!-- Guarantee -->
        <div class="d-flex gap-3 flex-wrap pt-2 border-top" style="font-size:12px;color:#666">
          <span><i class="bi bi-shield-check text-success me-1"></i>สินค้าของแท้</span>
          <span><i class="bi bi-truck text-orange me-1"></i>ส่งทั่วไทย</span>
          <span><i class="bi bi-arrow-clockwise text-orange me-1"></i>คืนได้ใน 15 วัน</span>
        </div>
      </div>
    </div>

    <!-- Shop Info -->
    <div class="col-lg-3">
      <div class="surface">
        <div class="d-flex align-items-center gap-3 mb-3">
          <a href="/webshop/shop.php?slug=<?= e($product['shop_slug']) ?>">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($product['shop_name']) ?>&background=ee4d2d&color=fff&size=64"
                 class="rounded-circle" width="56" height="56" alt="">
          </a>
          <div>
            <a href="/webshop/shop.php?slug=<?= e($product['shop_slug']) ?>" class="fw-bold d-block"><?= e($product['shop_name']) ?></a>
            <?php if ($product['shop_verified']): ?>
            <span class="badge bg-success" style="font-size:11px"><i class="bi bi-patch-check-fill me-1"></i>ร้านยืนยัน</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="row g-2 text-center mb-3" style="font-size:12px">
          <div class="col-6"><div class="text-orange fw-bold"><?= number_format($product['shop_rating'],1) ?>★</div><div class="text-muted">คะแนน</div></div>
          <div class="col-6"><div class="text-orange fw-bold"><?= number_format((int)$product['shop_sales']) ?></div><div class="text-muted">ขายแล้ว</div></div>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-sm <?= $isFollowing ? 'btn-orange' : 'btn-outline-orange' ?> flex-fill"
                  data-follow-shop="<?= $product['shop_id'] ?>">
            <i class="bi <?= $isFollowing ? 'bi-person-check-fill' : 'bi-person-plus' ?> me-1"></i>
            <?= $isFollowing ? 'ติดตามแล้ว' : 'ติดตาม' ?>
          </button>
          <a href="/webshop/shop.php?slug=<?= e($product['shop_slug']) ?>" class="btn btn-sm btn-outline-secondary flex-fill">
            <i class="bi bi-shop me-1"></i>ร้านค้า
          </a>
        </div>
      </div>

      <!-- Specifications -->
      <?php if (!empty($product['specs'])): ?>
      <div class="surface">
        <h6 class="fw-bold mb-3"><i class="bi bi-list-ul me-1 text-orange"></i>สเปคสินค้า</h6>
        <table class="table table-sm table-borderless mb-0" style="font-size:13px">
          <?php foreach ($product['specs'] as $spec): ?>
          <tr>
            <td class="text-muted" style="width:45%"><?= e($spec['spec_key']) ?></td>
            <td class="fw-semibold"><?= e($spec['spec_value']) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Description -->
  <div class="surface mt-3">
    <h5 class="fw-bold mb-3"><i class="bi bi-file-text me-2 text-orange"></i>รายละเอียดสินค้า</h5>
    <div style="font-size:14px;line-height:1.8;color:#444">
      <?= nl2br(e($product['description'] ?? 'ไม่มีรายละเอียดสินค้า')) ?>
    </div>
  </div>

  <!-- Reviews -->
  <div class="surface mt-3">
    <h5 class="fw-bold mb-4"><i class="bi bi-star-fill me-2 text-orange"></i>รีวิวสินค้า</h5>
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="review-summary">
          <div class="review-score"><?= number_format($product['rating'],1) ?></div>
          <div class="stars-display my-2">
            <?php for ($i=1;$i<=5;$i++): ?>
            <i class="bi bi-star-fill" style="color:<?= $i<=round($product['rating'])?'#ffa500':'#ddd' ?>"></i>
            <?php endfor; ?>
          </div>
          <div class="text-muted" style="font-size:13px"><?= number_format($product['total_reviews']) ?> รีวิว</div>
        </div>
      </div>
      <div class="col-md-9">
        <!-- Star Filter -->
        <div class="d-flex gap-2 flex-wrap mb-3">
          <a href="?" class="filter-chip active">ทั้งหมด</a>
          <?php for ($r=5;$r>=1;$r--): ?>
          <a href="?slug=<?= e($slug) ?>&rating=<?= $r ?>" class="filter-chip"><?= $r ?>★</a>
          <?php endfor; ?>
          <a href="?slug=<?= e($slug) ?>&img=1" class="filter-chip">มีรูปภาพ</a>
        </div>
        <!-- Review List -->
        <?php if (empty($reviews['data'])): ?>
        <div class="text-muted text-center py-4"><i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>ยังไม่มีรีวิว</div>
        <?php else: ?>
        <?php foreach ($reviews['data'] as $rv): ?>
        <div class="review-card">
          <div class="d-flex gap-3">
            <img src="<?= getAvatarUrl($rv['avatar_url'], $rv['username']) ?>"
                 class="review-avatar" alt=""
                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($rv['username']) ?>&size=40'; this.onerror=null;">
            <div class="flex-fill">
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="fw-semibold" style="font-size:14px"><?= e($rv['username']) ?></span>
                <span class="stars-display">
                  <?php for($i=1;$i<=5;$i++): ?>
                  <i class="bi bi-star-fill" style="font-size:12px;color:<?= $i<=$rv['rating']?'#ffa500':'#ddd' ?>"></i>
                  <?php endfor; ?>
                </span>
                <span class="text-muted ms-auto" style="font-size:12px"><?= formatDate($rv['created_at'],'d M Y') ?></span>
              </div>
              <p style="font-size:14px;margin-bottom:8px;color:#444"><?= nl2br(e($rv['comment'])) ?></p>
              <?php if ($rv['seller_reply']): ?>
              <div class="bg-light p-2 rounded" style="font-size:13px;border-left:3px solid var(--shopee-orange)">
                <span class="fw-semibold text-orange"><i class="bi bi-shop me-1"></i>ผู้ขาย:</span> <?= nl2br(e($rv['seller_reply'])) ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if ($reviews['total'] > 6): ?>
        <div class="text-center mt-3">
          <a href="/webshop/product.php?slug=<?= e($slug) ?>&all_reviews=1" class="btn btn-outline-orange btn-sm">ดูรีวิวทั้งหมด (<?= $reviews['total'] ?>)</a>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Q&A -->
  <?php if (!empty($questions)): ?>
  <div class="surface mt-3">
    <h5 class="fw-bold mb-3"><i class="bi bi-question-circle me-2 text-orange"></i>คำถาม & คำตอบ</h5>
    <?php foreach ($questions as $q): ?>
    <div class="border rounded p-3 mb-2" style="background:#fafafa">
      <div class="d-flex gap-2 mb-2">
        <i class="bi bi-question-circle-fill text-orange mt-1"></i>
        <div>
          <span class="fw-semibold" style="font-size:14px"><?= e($q['question']) ?></span>
          <div class="text-muted" style="font-size:12px">โดย <?= e($q['username']) ?> · <?= formatDate($q['created_at'],'d/m/Y') ?></div>
        </div>
      </div>
      <?php if ($q['answer']): ?>
      <div class="d-flex gap-2 ms-4">
        <i class="bi bi-reply-fill text-success mt-1"></i>
        <div style="font-size:13px;color:#444">
          <?= e($q['answer']) ?>
          <?php if ($q['is_verified']): ?><span class="badge bg-success ms-1" style="font-size:10px">ยืนยันโดยผู้ขาย</span><?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Related Products -->
  <?php if (!empty($related)): ?>
  <div class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="section-title mb-0">สินค้าที่เกี่ยวข้อง</div>
    </div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2">
      <?php foreach ($related as $p): ?>
      <div class="col"><?= renderProductCard($p, $wishlistIds) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
