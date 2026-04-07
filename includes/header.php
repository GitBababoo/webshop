<?php
if (!defined('FRONT_INCLUDED')) {
    require_once dirname(__DIR__) . '/includes/functions_front.php';
}
$currentUser  = frontCurrentUser();
$cartCount    = getCartCount($currentUser ? (int)$currentUser['user_id'] : null);
$allCats      = getCategories();
$siteName     = getSetting('site_name', 'Shopee TH');
$currentPath  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle) . ' – ' : '' ?><?= e($siteName) ?></title>
<?php if (!empty($metaDesc)): ?><meta name="description" content="<?= e($metaDesc) ?>"><?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<link rel="stylesheet" href="/webshop/assets/css/style.css?v=<?= time() ?>">
</head>
<body>

<!-- ── Navbar ── -->
<nav class="site-navbar">
  <div class="container-xl d-flex align-items-center gap-3 h-100">
    <!-- Brand -->
    <a href="/webshop/" class="navbar-brand flex-shrink-0 d-flex align-items-center gap-2">
      <img src="/webshop/uploads/settings/site_logo.jpg" alt="Logo" style="height: 38px; width: auto; border-radius: 4px;">
      <div class="d-none d-sm-block lh-1 mt-1">
        <div style="font-size: 24px; font-weight: 700; color: #fff; letter-spacing: -0.5px;"><?= e($siteName) ?></div>
        <span style="font-size: 12px; font-weight: 400; color: rgba(255,255,255,0.9);">ช้อปทุกอย่าง ง่ายทุกที่</span>
      </div>
    </a>

    <!-- Search -->
    <form class="site-search flex-grow-1 d-none d-md-flex" action="/webshop/search.php" method="GET">
      <div class="input-group">
        <input type="text" name="q" id="siteSearch" class="form-control"
               placeholder="ค้นหาสินค้า ร้านค้า หรือหมวดหมู่..."
               value="<?= isset($_GET['q']) ? e($_GET['q']) : '' ?>"
               autocomplete="off">
        <button class="btn" type="submit"><i class="bi bi-search"></i></button>
      </div>
    </form>

    <!-- Actions -->
    <div class="d-flex align-items-center gap-1 ms-auto ms-md-0 flex-shrink-0">
      <!-- Cart -->
      <a href="/webshop/cart.php" class="nav-action-btn position-relative" title="ตะกร้า">
        <i class="bi bi-cart3"></i>
        <span class="nav-badge" data-cart><?= $cartCount > 0 ? $cartCount : '' ?></span>
      </a>

      <!-- Wishlist -->
      <?php if ($currentUser): ?>
      <a href="/webshop/account/wishlist.php" class="nav-action-btn d-none d-md-inline-flex" title="Wishlist">
        <i class="bi bi-heart"></i>
      </a>
      <?php endif; ?>

      <!-- Notifications -->
      <?php if ($currentUser): ?>
      <a href="/webshop/account/orders.php" class="nav-action-btn d-none d-md-inline-flex" title="คำสั่งซื้อ">
        <i class="bi bi-bag"></i>
      </a>
      <?php endif; ?>

      <!-- User Menu -->
      <?php if ($currentUser): ?>
      <div class="dropdown">
        <button class="nav-action-btn d-flex align-items-center gap-1" data-bs-toggle="dropdown">
          <img src="<?= getAvatarUrl($currentUser['avatar_url'], $currentUser['username']) ?>"
               class="rounded-circle" width="28" height="28" style="object-fit:cover" alt=""
               onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($currentUser['username']) ?>&background=ee4d2d&color=fff&size=32'; this.onerror=null;">
          <span class="d-none d-lg-inline small"><?= e($currentUser['full_name'] ?: $currentUser['username']) ?></span>
          <i class="bi bi-chevron-down small"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><h6 class="dropdown-header"><?= e($currentUser['username']) ?></h6></li>
          <li><a class="dropdown-item" href="/webshop/account/profile.php"><i class="bi bi-person me-2"></i>โปรไฟล์ของฉัน</a></li>
          <li><a class="dropdown-item" href="/webshop/account/orders.php"><i class="bi bi-bag me-2"></i>คำสั่งซื้อ</a></li>
          <li><a class="dropdown-item" href="/webshop/account/wishlist.php"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
          <li><a class="dropdown-item" href="/webshop/account/wallet.php"><i class="bi bi-wallet2 me-2"></i>กระเป๋าเงิน</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="/webshop/account/logout.php"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
        </ul>
      </div>
      <?php else: ?>
      <a href="/webshop/account/login.php" class="btn btn-sm btn-outline-light ms-1">เข้าสู่ระบบ</a>
      <a href="/webshop/account/register.php" class="btn btn-sm btn-light ms-1 d-none d-md-inline-flex">สมัครสมาชิก</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- ── Category Nav ── -->
<div class="category-nav d-none d-md-block">
  <div class="container-xl">
    <ul class="nav flex-nowrap overflow-auto" style="scrollbar-width:none;">
      <li class="nav-item">
        <a href="/webshop/" class="nav-link <?= $currentPath==='/webshop/' || $currentPath==='/webshop/index.php' ? 'active' : '' ?>">
          <i class="bi bi-house me-1"></i>หน้าแรก
        </a>
      </li>
      <?php foreach ($allCats as $cat): ?>
      <li class="nav-item">
        <a href="/webshop/category.php?slug=<?= e($cat['slug']) ?>" class="nav-link <?= (isset($_GET['slug']) && $_GET['slug']===$cat['slug']) ? 'active' : '' ?>">
          <?= e($cat['name']) ?>
        </a>
      </li>
      <?php endforeach; ?>
      <li class="nav-item ms-auto">
        <a href="/webshop/search.php?flash=1" class="nav-link text-orange fw-bold">
          <i class="bi bi-lightning-fill me-1"></i>Flash Sale
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- Mobile Search -->
<form class="d-md-none bg-white border-bottom px-3 py-2" action="/webshop/search.php" method="GET">
  <div class="input-group">
    <input type="text" name="q" class="form-control form-control-sm" placeholder="ค้นหาสินค้า...">
    <button class="btn btn-sm btn-orange"><i class="bi bi-search"></i></button>
  </div>
</form>
