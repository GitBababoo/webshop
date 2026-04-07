<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: /webshop/'); exit; }
$shop = getShopBySlug($slug);
if (!$shop) { http_response_code(404); $pageTitle='ไม่พบร้านค้า'; include __DIR__.'/includes/header.php'; echo '<div class="container py-5 text-center"><h3>ไม่พบร้านค้านี้</h3><a href="/webshop/" class="btn btn-orange mt-3">กลับหน้าแรก</a></div>'; include __DIR__.'/includes/footer.php'; exit; }

$page     = max(1,(int)($_GET['page'] ?? 1));
$sort     = $_GET['sort'] ?? 'popular';
$catId    = (int)($_GET['cat'] ?? 0);
$q        = trim($_GET['q'] ?? '');

$filters  = ['shop_id' => (int)$shop['shop_id'], 'sort' => $sort];
if ($q)    $filters['q']           = $q;
if ($catId) $filters['category_id'] = $catId;

$result      = getProducts($filters, $page, 20);
$wishlistIds = frontIsLoggedIn() ? getWishlistIds((int)$_SESSION['front_user_id']) : [];
$isFollowing = frontIsLoggedIn() ? isFollowingShop((int)$_SESSION['front_user_id'], (int)$shop['shop_id']) : false;

// Shop categories (categories of shop products)
$catStmt = getDB()->prepare("SELECT DISTINCT c.category_id,c.name,c.slug FROM products p JOIN categories c ON p.category_id=c.category_id WHERE p.shop_id=? AND p.status='active' ORDER BY c.name");
$catStmt->execute([$shop['shop_id']]);
$shopCats = $catStmt->fetchAll();

// Recent reviews for this shop
$rvStmt = getDB()->prepare("SELECT r.rating,r.comment,r.created_at,u.username,u.avatar_url,p.name AS product_name,p.slug AS product_slug FROM reviews r JOIN users u ON r.reviewer_id=u.user_id JOIN products p ON r.product_id=p.product_id WHERE r.shop_id=? AND r.is_hidden=0 ORDER BY r.created_at DESC LIMIT 6");
$rvStmt->execute([$shop['shop_id']]);
$shopReviews = $rvStmt->fetchAll();

$pageTitle = $shop['shop_name'];
include __DIR__ . '/includes/header.php';
?>

<!-- Shop Banner -->
<div class="position-relative mb-0" style="background:linear-gradient(135deg,#f53d2d,#f63);min-height:180px">
  <?php if ($shop['banner_url'] ?? null): ?>
  <img src="<?= e($shop['banner_url']) ?>" class="shop-banner" alt="" style="opacity:.7">
  <?php endif; ?>
  <div class="container-xl py-4" style="position:relative;z-index:1">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <img src="<?= $shop['avatar_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode($shop['shop_name']).'&background=fff&color=ee4d2d&size=80' ?>"
           class="shop-logo" alt="" style="border:3px solid #fff">
      <div class="text-white flex-fill">
        <h2 class="mb-1 fw-bold"><?= e($shop['shop_name']) ?></h2>
        <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:13px;opacity:.9">
          <?php if ($shop['is_verified']): ?>
          <span><i class="bi bi-patch-check-fill me-1"></i>ร้านยืนยัน</span>
          <?php endif; ?>
          <span><i class="bi bi-star-fill me-1" style="color:#fcd900"></i><?= number_format($shop['rating'],1) ?> คะแนน</span>
          <span><i class="bi bi-bag-check me-1"></i><?= number_format((int)$shop['total_sales']) ?> ขายแล้ว</span>
          <span><i class="bi bi-chat-dots me-1"></i><?= number_format((int)$shop['total_reviews']) ?> รีวิว</span>
          <span><i class="bi bi-person-heart me-1"></i><?= number_format((int)($shop['total_followers'] ?? 0)) ?> ผู้ติดตาม</span>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-sm btn-light" data-follow-shop="<?= $shop['shop_id'] ?>">
          <i class="bi <?= $isFollowing ? 'bi-person-check-fill' : 'bi-person-plus' ?> me-1"></i>
          <?= $isFollowing ? 'ติดตามแล้ว' : 'ติดตาม' ?>
        </button>
        <a href="/webshop/search.php?q=<?= urlencode($shop['shop_name']) ?>" class="btn btn-sm btn-outline-light">
          <i class="bi bi-search me-1"></i>ค้นหาในร้าน
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Description -->
<?php if ($shop['description']): ?>
<div class="bg-white border-bottom py-2">
  <div class="container-xl">
    <p class="mb-0 text-muted" style="font-size:13px"><?= e($shop['description']) ?></p>
  </div>
</div>
<?php endif; ?>

<div class="container-xl py-3">
  <div class="row g-3">
    <!-- Sidebar -->
    <div class="col-md-2 d-none d-md-block">
      <div class="filter-card">
        <h6><i class="bi bi-grid me-1"></i>หมวดหมู่</h6>
        <a href="/webshop/shop.php?slug=<?= e($slug) ?>" class="filter-chip d-block mb-1 <?= !$catId?'active':'' ?>">ทั้งหมด</a>
        <?php foreach ($shopCats as $sc): ?>
        <a href="/webshop/shop.php?slug=<?= e($slug) ?>&cat=<?= $sc['category_id'] ?>" class="filter-chip d-block mb-1 <?= $catId==(int)$sc['category_id']?'active':'' ?>"><?= e($sc['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <!-- Shop Stats -->
      <div class="filter-card text-center">
        <div class="row g-2">
          <div class="col-6">
            <div class="text-orange fw-bold fs-5"><?= number_format((int)$shop['total_products']) ?></div>
            <div class="text-muted" style="font-size:11px">สินค้า</div>
          </div>
          <div class="col-6">
            <div class="text-orange fw-bold fs-5"><?= number_format($shop['rating'],1) ?></div>
            <div class="text-muted" style="font-size:11px">คะแนน</div>
          </div>
          <div class="col-12">
            <div class="text-orange fw-bold"><?= number_format((int)$shop['total_sales']) ?></div>
            <div class="text-muted" style="font-size:11px">ขายแล้ว</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Products -->
    <div class="col-md-10">
      <!-- Search in shop -->
      <form class="d-flex gap-2 mb-3" method="GET">
        <input type="hidden" name="slug" value="<?= e($slug) ?>">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="ค้นหาในร้านนี้..." value="<?= e($q) ?>">
        <button class="btn btn-sm btn-orange"><i class="bi bi-search"></i></button>
        <?php if ($q): ?><a href="/webshop/shop.php?slug=<?= e($slug) ?>" class="btn btn-sm btn-outline-secondary">ล้าง</a><?php endif; ?>
      </form>

      <div class="sort-bar mb-2">
        <span class="sort-label">เรียงตาม:</span>
        <?php foreach (['popular'=>'ขายดี','newest'=>'ใหม่','price_asc'=>'ราคาต่ำ→สูง','price_desc'=>'ราคาสูง→ต่ำ','rating'=>'คะแนนสูงสุด'] as $k=>$lbl): ?>
        <button class="sort-btn <?= $sort===$k?'active':'' ?>" data-sort="<?= $k ?>"><?= $lbl ?></button>
        <?php endforeach; ?>
        <span class="ms-auto text-muted small"><?= number_format($result['total']) ?> รายการ</span>
      </div>

      <?php if (empty($result['data'])): ?>
      <div class="empty-state surface"><div class="empty-icon"><i class="bi bi-box-seam"></i></div><h5>ไม่พบสินค้า</h5></div>
      <?php else: ?>
      <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 g-2">
        <?php foreach ($result['data'] as $p): ?>
        <div class="col"><?= renderProductCard($p, $wishlistIds) ?></div>
        <?php endforeach; ?>
      </div>
      <?php if ($result['total_pages'] > 1): ?>
      <nav class="mt-4 d-flex justify-content-center">
        <ul class="pagination">
          <?php for ($i=max(1,$page-2); $i<=min($result['total_pages'],$page+4); $i++): ?>
          <li class="page-item <?= $i===$page?'active':'' ?>">
            <a class="page-link" href="?slug=<?= e($slug) ?>&page=<?= $i ?>&sort=<?= $sort ?>"><?= $i ?></a>
          </li>
          <?php endfor; ?>
        </ul>
      </nav>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Shop Reviews -->
  <?php if (!empty($shopReviews)): ?>
  <div class="surface mt-3">
    <h5 class="fw-bold mb-3"><i class="bi bi-star-fill me-2 text-orange"></i>รีวิวร้านค้า</h5>
    <div class="row g-3">
      <?php foreach ($shopReviews as $rv): ?>
      <div class="col-md-6 col-lg-4">
        <div class="border rounded p-3 h-100" style="font-size:13px">
          <div class="d-flex align-items-center gap-2 mb-2">
            <img src="<?= getAvatarUrl($rv['avatar_url'], $rv['username']) ?>"
                 class="rounded-circle" width="32" height="32" style="object-fit:cover" alt=""
                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($rv['username']) ?>&size=32'; this.onerror=null;">
            <div>
              <div class="fw-semibold"><?= e($rv['username']) ?></div>
              <div class="stars-display" style="font-size:11px">
                <?php for($i=1;$i<=5;$i++): ?><i class="bi bi-star-fill" style="color:<?= $i<=$rv['rating']?'#ffa500':'#ddd' ?>"></i><?php endfor; ?>
              </div>
            </div>
            <span class="ms-auto text-muted small"><?= formatDate($rv['created_at'],'d/m/Y') ?></span>
          </div>
          <p class="mb-1 text-muted" style="font-size:11px">สินค้า: <a href="/webshop/product.php?slug=<?= e($rv['product_slug']) ?>" class="text-orange"><?= e($rv['product_name']) ?></a></p>
          <p class="mb-0"><?= e(mb_substr($rv['comment'], 0, 100)) ?><?= mb_strlen($rv['comment'])>100?'...':'' ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
