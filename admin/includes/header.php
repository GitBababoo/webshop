<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$user = currentUser();
$siteName = getSetting('site_name', 'Shopee TH');
$flash = renderFlash();
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> – <?= e($siteName) ?> Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="<?= ADMIN_URL ?>/assets/css/admin.css">
</head>
<body>
<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand navbar-dark admin-navbar fixed-top px-3">
  <button class="btn btn-sm btn-link text-white me-2" id="sidebarToggle">
    <i class="bi bi-list fs-4"></i>
  </button>
  <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= ADMIN_URL ?>">
    <div class="brand-dot"><i class="bi bi-shop-window"></i></div>
    <span class="d-none d-md-inline"><?= e($siteName) ?></span>
    <span class="badge bg-warning text-dark small fw-bold"><?= isSuperAdmin() ? 'SuperAdmin' : 'Admin' ?></span>
  </a>
  <div class="ms-auto d-flex align-items-center gap-3">
    <!-- Notifications -->
    <div class="dropdown">
      <button class="btn btn-sm btn-link text-white position-relative" data-bs-toggle="dropdown">
        <i class="bi bi-bell fs-5"></i>
        <?php
        $pendingOrders = getDB()->query("SELECT COUNT(*) FROM orders WHERE order_status='pending'")->fetchColumn();
        if ($pendingOrders > 0):
        ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size:.6rem"><?= $pendingOrders ?></span><?php endif; ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:280px">
        <li><h6 class="dropdown-header">การแจ้งเตือน</h6></li>
        <?php if ($pendingOrders > 0): ?>
        <li><a class="dropdown-item d-flex gap-2" href="<?= ADMIN_URL ?>/orders/index.php?status=pending">
          <span class="text-warning fs-5"><i class="bi bi-cart-check"></i></span>
          <div><strong><?= $pendingOrders ?> ออเดอร์ใหม่</strong><br><small class="text-muted">รอการยืนยัน</small></div>
        </a></li>
        <?php else: ?>
        <li><span class="dropdown-item text-muted small">ไม่มีการแจ้งเตือน</span></li>
        <?php endif; ?>
      </ul>
    </div>
    <!-- User menu -->
    <div class="dropdown">
      <button class="btn btn-sm btn-link text-white d-flex align-items-center gap-2" data-bs-toggle="dropdown">
        <div class="avatar-sm">
          <img src="<?= getAvatarUrl($user['avatar'] ?? '', $user['username'] ?? 'Admin') ?>" 
               alt="" style="width:32px;height:32px;object-fit:cover;border-radius:50%"
               onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['username'] ?? 'A') ?>&background=ffc107&color=000&size=32'; this.onerror=null;">
        </div>
        <span class="d-none d-md-inline"><?= e($user['name']) ?></span>
        <i class="bi bi-chevron-down small"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li><h6 class="dropdown-header"><?= e($user['name']) ?></h6></li>
        <li><span class="dropdown-item-text small text-muted"><?= e($user['username']) ?> · <?= ROLES[$user['role']] ?? $user['role'] ?></span></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="<?= ADMIN_URL ?>/profile.php"><i class="bi bi-person me-2"></i>โปรไฟล์</a></li>
        <li><a class="dropdown-item" href="<?= ADMIN_URL ?>/settings/general.php"><i class="bi bi-gear me-2"></i>ตั้งค่า</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?= ADMIN_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="wrapper d-flex">
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="main-content flex-grow-1">
<div class="content-area p-4">
<?php if ($flash): echo $flash; endif; ?>
<div class="page-header mb-4">
  <h4 class="mb-0 fw-bold"><?= e($pageTitle) ?></h4>
  <?php if (isset($breadcrumb)): ?>
  <nav aria-label="breadcrumb" class="mt-1">
    <ol class="breadcrumb mb-0 small">
      <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>">หน้าหลัก</a></li>
      <?php foreach ($breadcrumb as $label => $url): ?>
        <?php if ($url): ?>
          <li class="breadcrumb-item"><a href="<?= e($url) ?>"><?= e($label) ?></a></li>
        <?php else: ?>
          <li class="breadcrumb-item active"><?= e($label) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ol>
  </nav>
  <?php endif; ?>
</div>
