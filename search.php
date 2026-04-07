<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';

$q        = trim($_GET['q'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$sort     = $_GET['sort'] ?? 'popular';
$priceMin = $_GET['price_min'] ?? '';
$priceMax = $_GET['price_max'] ?? '';
$brand    = $_GET['brand'] ?? '';
$cond     = $_GET['condition'] ?? '';
$catId    = (int)($_GET['cat'] ?? 0);
$flash    = !empty($_GET['flash']);

$filters = ['sort' => $sort, 'q' => $q];
if ($catId)    $filters['category_id'] = $catId;
if ($priceMin) $filters['price_min']   = (float)$priceMin;
if ($priceMax) $filters['price_max']   = (float)$priceMax;
if ($brand)    $filters['brand']       = $brand;
if ($cond)     $filters['condition']   = $cond;
if ($flash)    $filters['is_featured'] = true;

$result      = getProducts($filters, $page, 24);
$wishlistIds = frontIsLoggedIn() ? getWishlistIds((int)$_SESSION['front_user_id']) : [];
$allCats     = getCategories();

// Save search history
if ($q && frontIsLoggedIn()) {
    try { getDB()->prepare("INSERT INTO search_history (user_id,keyword) VALUES (?,?)")->execute([$_SESSION['front_user_id'], $q]); } catch(Exception $e) {}
}

$pageTitle = $flash ? 'Flash Sale' : ($q ? "ค้นหา: $q" : 'สินค้าทั้งหมด');
include __DIR__ . '/includes/header.php';
?>

<div class="site-breadcrumb">
  <div class="container-xl">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/webshop/">หน้าแรก</a></li>
      <li class="breadcrumb-item active"><?= $flash ? 'Flash Sale' : ($q ? 'ผลการค้นหา "'.e($q).'"' : 'สินค้าทั้งหมด') ?></li>
    </ol>
  </div>
</div>

<div class="container-xl py-3">
  <?php if ($q): ?>
  <div class="mb-3 text-muted" style="font-size:14px">
    ผลการค้นหา "<strong class="text-dark"><?= e($q) ?></strong>" — พบ <?= number_format($result['total']) ?> รายการ
  </div>
  <?php endif; ?>

  <div class="row g-3">
    <!-- Sidebar Filters -->
    <div class="col-md-3 col-lg-2">
      <div class="filter-card">
        <h6><i class="bi bi-funnel me-1"></i>กรองสินค้า</h6>
        <!-- Category -->
        <div class="mb-3">
          <div class="fw-semibold mb-2" style="font-size:13px">หมวดหมู่</div>
          <a href="?<?= http_build_query(array_merge($_GET,['cat'=>'','page'=>1])) ?>" class="filter-chip <?= !$catId?'active':'' ?>">ทั้งหมด</a>
          <?php foreach ($allCats as $cat): ?>
          <a href="?<?= http_build_query(array_merge($_GET,['cat'=>$cat['category_id'],'page'=>1])) ?>" class="filter-chip <?= $catId==(int)$cat['category_id']?'active':'' ?>"><?= e($cat['name']) ?></a>
          <?php endforeach; ?>
        </div>
        <!-- Price -->
        <div class="mb-3">
          <div class="fw-semibold mb-2" style="font-size:13px">ช่วงราคา (฿)</div>
          <div class="price-range-inputs">
            <input type="number" id="priceMin" class="form-control form-control-sm" placeholder="ต่ำสุด" value="<?= e($priceMin) ?>">
            <span>–</span>
            <input type="number" id="priceMax" class="form-control form-control-sm" placeholder="สูงสุด" value="<?= e($priceMax) ?>">
          </div>
          <button id="applyPriceFilter" class="btn btn-sm btn-outline-orange w-100 mt-2">ใช้</button>
        </div>
        <!-- Condition -->
        <div class="mb-2">
          <div class="fw-semibold mb-2" style="font-size:13px">สภาพ</div>
          <a href="?<?= http_build_query(array_merge($_GET,['condition'=>'','page'=>1])) ?>" class="filter-chip <?= !$cond?'active':'' ?>">ทั้งหมด</a>
          <a href="?<?= http_build_query(array_merge($_GET,['condition'=>'new','page'=>1])) ?>" class="filter-chip <?= $cond==='new'?'active':'' ?>">ใหม่</a>
          <a href="?<?= http_build_query(array_merge($_GET,['condition'=>'used','page'=>1])) ?>" class="filter-chip <?= $cond==='used'?'active':'' ?>">มือสอง</a>
        </div>
      </div>
      <?php if ($priceMin || $priceMax || $catId || $cond): ?>
      <a href="/webshop/search.php<?= $q?'?q='.urlencode($q):'' ?>" class="btn btn-sm btn-outline-secondary w-100 mt-1">
        <i class="bi bi-x-circle me-1"></i>ล้างตัวกรอง
      </a>
      <?php endif; ?>
    </div>

    <!-- Results -->
    <div class="col-md-9 col-lg-10">
      <div class="sort-bar">
        <span class="sort-label">เรียงตาม:</span>
        <?php foreach (['popular'=>'ยอดนิยม','newest'=>'ล่าสุด','price_asc'=>'ราคา ต่ำ→สูง','price_desc'=>'ราคา สูง→ต่ำ','rating'=>'คะแนนสูงสุด'] as $k=>$lbl): ?>
        <button class="sort-btn <?= $sort===$k?'active':'' ?>" data-sort="<?= $k ?>"><?= $lbl ?></button>
        <?php endforeach; ?>
        <span class="ms-auto text-muted" style="font-size:12px"><?= number_format($result['total']) ?> รายการ</span>
      </div>

      <?php if (empty($result['data'])): ?>
      <div class="empty-state surface">
        <div class="empty-icon"><i class="bi bi-search"></i></div>
        <h5>ไม่พบสินค้าที่ต้องการ</h5>
        <?php if ($q): ?><p class="text-muted">ลองค้นหาด้วยคำอื่น หรือเช็คการสะกดคำ</p><?php endif; ?>
        <a href="/webshop/" class="btn btn-orange">กลับหน้าแรก</a>
      </div>
      <?php else: ?>
      <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 g-2">
        <?php foreach ($result['data'] as $p): ?>
        <div class="col"><?= renderProductCard($p, $wishlistIds) ?></div>
        <?php endforeach; ?>
      </div>
      <?php if ($result['total_pages'] > 1): ?>
      <nav class="mt-4 d-flex justify-content-center">
        <ul class="pagination">
          <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])) ?>">‹</a></li><?php endif; ?>
          <?php for ($i=max(1,$page-2); $i<=min($result['total_pages'],$page+4); $i++): ?>
          <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>"><?= $i ?></a></li>
          <?php endfor; ?>
          <?php if ($page < $result['total_pages']): ?><li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])) ?>">›</a></li><?php endif; ?>
        </ul>
      </nav>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
