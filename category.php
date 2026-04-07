<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';

$slug     = trim($_GET['slug'] ?? '');
$category = $slug ? getCategoryBySlug($slug) : null;
$page     = max(1, (int)($_GET['page'] ?? 1));
$sort     = $_GET['sort'] ?? 'newest';
$priceMin = $_GET['price_min'] ?? '';
$priceMax = $_GET['price_max'] ?? '';
$brand    = $_GET['brand'] ?? '';
$cond     = $_GET['condition'] ?? '';

$filters = ['sort' => $sort];
if ($category)  $filters['category_id'] = (int)$category['category_id'];
if ($priceMin)  $filters['price_min']   = (float)$priceMin;
if ($priceMax)  $filters['price_max']   = (float)$priceMax;
if ($brand)     $filters['brand']       = $brand;
if ($cond)      $filters['condition']   = $cond;

$result      = getProducts($filters, $page, 24);
$wishlistIds = frontIsLoggedIn() ? getWishlistIds((int)$_SESSION['front_user_id']) : [];
$subCats     = $category ? getCategories((int)$category['category_id']) : [];

// Brands for this category
$brandStmt = getDB()->prepare("SELECT DISTINCT brand FROM products WHERE " . ($category ? "category_id=? AND " : "") . "brand IS NOT NULL AND status='active' ORDER BY brand");
$brandStmt->execute($category ? [(int)$category['category_id']] : []);
$brands = array_column($brandStmt->fetchAll(), 'brand');

$pageTitle = $category ? $category['name'] : 'สินค้าทั้งหมด';
include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<div class="site-breadcrumb">
  <div class="container-xl">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/webshop/">หน้าแรก</a></li>
        <?php if ($category): ?>
        <li class="breadcrumb-item active"><?= e($category['name']) ?></li>
        <?php else: ?>
        <li class="breadcrumb-item active">สินค้าทั้งหมด</li>
        <?php endif; ?>
      </ol>
    </nav>
  </div>
</div>

<div class="container-xl py-3">
  <!-- Sub-categories -->
  <?php if (!empty($subCats)): ?>
  <div class="surface mb-2">
    <div class="d-flex gap-2 flex-wrap">
      <a href="/webshop/category.php?slug=<?= e($slug) ?>" class="filter-chip <?= empty($_GET['sub']) ? 'active' : '' ?>">ทั้งหมด</a>
      <?php foreach ($subCats as $sc): ?>
      <a href="/webshop/category.php?slug=<?= e($sc['slug']) ?>" class="filter-chip"><?= e($sc['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-3">
    <!-- Filter Sidebar -->
    <div class="col-md-3 col-lg-2">
      <div class="filter-card">
        <h6><i class="bi bi-funnel me-1"></i>กรองสินค้า</h6>
        <!-- Price Range -->
        <div class="mb-3">
          <div class="fw-semibold mb-2" style="font-size:13px">ช่วงราคา (฿)</div>
          <div class="price-range-inputs">
            <input type="number" id="priceMin" class="form-control form-control-sm" placeholder="ต่ำสุด" value="<?= e($priceMin) ?>">
            <span class="text-muted">–</span>
            <input type="number" id="priceMax" class="form-control form-control-sm" placeholder="สูงสุด" value="<?= e($priceMax) ?>">
          </div>
          <button id="applyPriceFilter" class="btn btn-sm btn-outline-orange w-100 mt-2">ใช้ช่วงราคา</button>
        </div>
        <!-- Condition -->
        <div class="mb-3">
          <div class="fw-semibold mb-2" style="font-size:13px">สภาพ</div>
          <a href="<?= buildFilterUrl(['condition' => '']) ?>" class="filter-chip <?= !$cond ? 'active' : '' ?>">ทั้งหมด</a>
          <a href="<?= buildFilterUrl(['condition' => 'new']) ?>" class="filter-chip <?= $cond==='new' ? 'active' : '' ?>">ใหม่</a>
          <a href="<?= buildFilterUrl(['condition' => 'used']) ?>" class="filter-chip <?= $cond==='used' ? 'active' : '' ?>">มือสอง</a>
        </div>
        <!-- Brands -->
        <?php if (!empty($brands)): ?>
        <div>
          <div class="fw-semibold mb-2" style="font-size:13px">แบรนด์</div>
          <a href="<?= buildFilterUrl(['brand' => '']) ?>" class="filter-chip <?= !$brand ? 'active' : '' ?>">ทั้งหมด</a>
          <?php foreach (array_slice($brands, 0, 10) as $b): ?>
          <a href="<?= buildFilterUrl(['brand' => $b]) ?>" class="filter-chip <?= $brand===$b ? 'active' : '' ?>"><?= e($b) ?></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($priceMin || $priceMax || $brand || $cond): ?>
      <a href="/webshop/category.php?<?= $category ? 'slug='.e($slug) : '' ?>" class="btn btn-sm btn-outline-secondary w-100">
        <i class="bi bi-x-circle me-1"></i>ล้างตัวกรอง
      </a>
      <?php endif; ?>
    </div>

    <!-- Products Area -->
    <div class="col-md-9 col-lg-10">
      <!-- Sort Bar -->
      <div class="sort-bar">
        <span class="sort-label">เรียงตาม:</span>
        <button class="sort-btn <?= $sort==='newest'?'active':'' ?>" data-sort="newest">ล่าสุด</button>
        <button class="sort-btn <?= $sort==='popular'?'active':'' ?>" data-sort="popular">ยอดนิยม</button>
        <button class="sort-btn <?= $sort==='price_asc'?'active':'' ?>" data-sort="price_asc">ราคา ต่ำ→สูง</button>
        <button class="sort-btn <?= $sort==='price_desc'?'active':'' ?>" data-sort="price_desc">ราคา สูง→ต่ำ</button>
        <button class="sort-btn <?= $sort==='rating'?'active':'' ?>" data-sort="rating">คะแนนสูงสุด</button>
        <span class="ms-auto text-muted" style="font-size:13px"><?= number_format($result['total']) ?> รายการ</span>
      </div>

      <?php if (empty($result['data'])): ?>
      <div class="empty-state">
        <div class="empty-icon"><i class="bi bi-box-seam"></i></div>
        <h5>ไม่พบสินค้าในหมวดนี้</h5>
        <a href="/webshop/" class="btn btn-orange mt-3">กลับหน้าแรก</a>
      </div>
      <?php else: ?>
      <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 g-2">
        <?php foreach ($result['data'] as $p): ?>
        <div class="col"><?= renderProductCard($p, $wishlistIds) ?></div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if ($result['total_pages'] > 1): ?>
      <nav class="mt-4 d-flex justify-content-center">
        <ul class="pagination">
          <?php for ($i = max(1, $page-2); $i <= min($result['total_pages'], $page+4); $i++): ?>
          <li class="page-item <?= $i===$page?'active':'' ?>">
            <a class="page-link" href="?<?= buildQueryString(['page'=>$i]) ?>"><?= $i ?></a>
          </li>
          <?php endfor; ?>
        </ul>
      </nav>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
function buildFilterUrl(array $overrides): string {
    $params = array_merge($_GET, $overrides);
    $params['page'] = 1;
    foreach ($params as $k => $v) if ($v === '' || $v === null) unset($params[$k]);
    return '/webshop/category.php?' . http_build_query($params);
}
function buildQueryString(array $overrides): string {
    $params = array_merge($_GET, $overrides);
    return http_build_query($params);
}
include __DIR__ . '/includes/footer.php';
?>
