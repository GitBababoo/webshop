<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';
$pageTitle = 'หน้าแรก';
$metaDesc  = 'ช้อปสินค้าราคาถูก ส่งไว ปลอดภัย';

$banners      = getBanners('homepage_main');
$flashSale    = getActiveFlashSale();
$featuredProds = getProducts(['is_featured' => true], 1, 10);
$newArrivals   = getProducts(['sort' => 'newest'], 1, 10);
$topSelling    = getProducts(['sort' => 'popular'], 1, 10);
$allCats       = getCategories();
$wishlistIds   = frontIsLoggedIn() ? getWishlistIds((int)$_SESSION['front_user_id']) : [];

include __DIR__ . '/includes/header.php';
?>

<!-- ── Hero: Banners + Sidebar ── -->
<div class="bg-white border-bottom py-3">
  <div class="container-xl">
    <div class="row g-2">
      <!-- Main Banner Slider -->
      <div class="col-md-8 col-lg-9">
        <?php if ($banners): ?>
        <div class="swiper hero-swiper" style="border-radius:4px;overflow:hidden">
          <div class="swiper-wrapper">
            <?php foreach ($banners as $b): ?>
            <div class="swiper-slide">
              <a href="<?= e($b['link_url'] ?? '#') ?>">
                <img src="<?= e($b['image_url']) ?>" alt="<?= e($b['title']) ?>" style="width:100%;height:300px;object-fit:cover">
              </a>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="swiper-pagination"></div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
        </div>
        <?php else: ?>
        <div class="bg-gradient rounded-2 d-flex align-items-center justify-content-center" style="height:300px;background:linear-gradient(135deg,#ee4d2d,#ff6a00)">
          <div class="text-white text-center">
            <div style="font-size:48px;font-weight:700">SHOPEE TH</div>
            <div style="font-size:18px;opacity:.8">ช้อปทุกอย่าง ง่ายทุกที่</div>
            <a href="/webshop/search.php" class="btn btn-light btn-sm mt-3 px-4">ช้อปเลย</a>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <!-- Side Panel -->
      <div class="col-md-4 col-lg-3 d-none d-md-block">
        <div class="h-100 d-flex flex-column gap-2">
          <?php if (frontIsLoggedIn()): $u = frontCurrentUser(); ?>
          <div class="surface mb-0 d-flex align-items-center gap-2 p-3" style="border-radius:4px;border:1px solid #eee">
            <img src="<?= $u['avatar_url'] ? e($u['avatar_url']) : 'https://ui-avatars.com/api/?name='.urlencode($u['username']).'&background=ee4d2d&color=fff&size=40' ?>"
                 class="rounded-circle" width="40" height="40" style="object-fit:cover" alt="">
            <div>
              <div style="font-size:13px;font-weight:600"><?= e($u['full_name'] ?: $u['username']) ?></div>
              <a href="/webshop/account/profile.php" class="text-orange" style="font-size:12px">ดูโปรไฟล์</a>
            </div>
          </div>
          <?php else: ?>
          <div class="surface mb-0 text-center p-3" style="border-radius:4px;border:1px solid #eee">
            <div style="font-size:13px;color:#666;margin-bottom:8px">เข้าสู่ระบบเพื่อประสบการณ์ที่ดีกว่า</div>
            <div class="d-flex gap-2 justify-content-center">
              <a href="/webshop/account/login.php" class="btn btn-sm btn-outline-orange flex-fill">เข้าสู่ระบบ</a>
              <a href="/webshop/account/register.php" class="btn btn-sm btn-orange flex-fill">สมัครสมาชิก</a>
            </div>
          </div>
          <?php endif; ?>
          <!-- Mini Category List -->
          <div class="flex-fill overflow-hidden rounded" style="border:1px solid #eee;background:#fff">
            <?php foreach (array_slice($allCats, 0, 8) as $cat): ?>
            <a href="/webshop/category.php?slug=<?= e($cat['slug']) ?>" class="d-flex align-items-center gap-2 px-3 py-2 border-bottom" style="font-size:13px;color:#333;transition:.2s" onmouseover="this.style.color='#ee4d2d'" onmouseout="this.style.color='#333'">
              <div style="width: 24px; height: 24px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                  <img src="<?= e($cat['image_url'] ?: 'https://via.placeholder.com/24') ?>" alt="" style="width:100%; height:100%; object-fit:cover;">
              </div>
              <?= e($cat['name']) ?>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── Categories Grid ── -->
<div class="bg-white border-bottom py-4">
  <div class="container-xl">
    <div class="section-title">หมวดหมู่ยอดนิยม</div>
    <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 row-cols-lg-8 g-3">
      <?php foreach ($allCats as $cat): ?>
      <div class="col">
        <a href="/webshop/category.php?slug=<?= e($cat['slug']) ?>" class="cat-item d-block">
          <div class="cat-icon d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; background: #f8f9fa; border: 1px solid #eee;">
            <img src="<?= e($cat['image_url'] ?: 'https://via.placeholder.com/60') ?>" alt="<?= e($cat['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
          <span class="d-block text-center" style="font-size:12px; font-weight: 500; color: #333;"><?= e($cat['name']) ?></span>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ── Flash Sale ── -->
<?php if ($flashSale && !empty($flashSale['items'])): ?>
<div class="py-3">
  <div class="container-xl">
    <div class="flash-sale-bar">
      <i class="bi bi-lightning-fill fs-4"></i>
      <span class="sale-title">Flash Sale</span>
      <span style="font-size:13px;opacity:.9">สิ้นสุดใน</span>
      <div id="flashCountdown" data-end="<?= strtotime($flashSale['end_at']) ?>" class="d-flex align-items-center gap-1"></div>
      <a href="/webshop/search.php?flash=1" class="btn btn-sm btn-light ms-auto">ดูทั้งหมด →</a>
    </div>
    <div class="flash-sale-grid p-3">
      <div class="swiper product-swiper" style="padding-bottom:8px">
        <div class="swiper-wrapper">
          <?php foreach ($flashSale['items'] as $item):
            $origPrice   = (float)$item['base_price'];
            $salePrice   = (float)$item['sale_price'];
            $disc        = $origPrice > 0 ? round((1-$salePrice/$origPrice)*100) : 0;
            $soldPct     = $item['available_stock'] > 0 ? min(100, round($item['sold_count']/($item['available_stock']+$item['sold_count'])*100)) : 100;
          ?>
          <div class="swiper-slide">
            <a href="/webshop/product.php?slug=<?= e($item['slug']) ?>" class="d-block product-card p-0">
              <div class="img-wrap">
                <img src="<?= e($item['image_url'] ?: 'https://via.placeholder.com/300x300?text=Sale') ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                <?php if ($disc): ?><span class="badge-discount">-<?= $disc ?>%</span><?php endif; ?>
              </div>
              <div class="p-2">
                <div class="text-orange fw-bold" style="font-size:15px">฿<?= number_format($salePrice, 0) ?></div>
                <div class="text-muted text-decoration-line-through" style="font-size:12px">฿<?= number_format($origPrice, 0) ?></div>
                <div class="progress mt-1" style="height:6px;border-radius:3px">
                  <div class="progress-bar bg-danger" style="width:<?= $soldPct ?>%" role="progressbar"></div>
                </div>
                <div style="font-size:11px;color:#999;margin-top:2px"><?= $soldPct >= 80 ? 'ใกล้หมดแล้ว!' : 'ขาย '.$item['sold_count'].' ชิ้น' ?></div>
              </div>
            </a>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="swiper-button-next" style="color:#ee4d2d"></div>
        <div class="swiper-button-prev" style="color:#ee4d2d"></div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ── Featured Products ── -->
<?php if (!empty($featuredProds['data'])): ?>
<div class="py-3">
  <div class="container-xl">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="section-title mb-0">สินค้าแนะนำ</div>
      <a href="/webshop/search.php?featured=1" class="text-orange small">ดูทั้งหมด →</a>
    </div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2">
      <?php foreach ($featuredProds['data'] as $p): ?>
      <div class="col"><?= renderProductCard($p, $wishlistIds) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ── New Arrivals ── -->
<div class="py-3">
  <div class="container-xl">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="section-title mb-0">สินค้ามาใหม่</div>
      <a href="/webshop/search.php?sort=newest" class="text-orange small">ดูทั้งหมด →</a>
    </div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2">
      <?php foreach ($newArrivals['data'] as $p): ?>
      <div class="col"><?= renderProductCard($p, $wishlistIds) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ── Top Selling ── -->
<div class="py-3 bg-white border-top border-bottom">
  <div class="container-xl">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="section-title mb-0">ขายดีที่สุด</div>
      <a href="/webshop/search.php?sort=popular" class="text-orange small">ดูทั้งหมด →</a>
    </div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2">
      <?php foreach ($topSelling['data'] as $p): ?>
      <div class="col"><?= renderProductCard($p, $wishlistIds) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ── Why Shop Here ── -->
<div class="bg-white py-5">
  <div class="container-xl">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <i class="bi bi-truck fs-1 text-orange"></i>
        <h6 class="mt-2">จัดส่งรวดเร็ว</h6>
        <p class="text-muted small mb-0">ส่งถึงบ้านภายใน 1-3 วัน</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-shield-check fs-1 text-orange"></i>
        <h6 class="mt-2">การันตีของแท้</h6>
        <p class="text-muted small mb-0">สินค้าทุกชิ้นผ่านการตรวจสอบ</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-arrow-counterclockwise fs-1 text-orange"></i>
        <h6 class="mt-2">คืนได้ใน 15 วัน</h6>
        <p class="text-muted small mb-0">ไม่พอใจยินดีคืนเงิน</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-headset fs-1 text-orange"></i>
        <h6 class="mt-2">บริการ 24/7</h6>
        <p class="text-muted small mb-0">ทีม Support พร้อมช่วยทุกเวลา</p>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
