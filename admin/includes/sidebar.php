<?php
$currentFile = $_SERVER['PHP_SELF'];
function isActive(string $path): string {
    return str_contains($_SERVER['PHP_SELF'], $path) ? 'active' : '';
}
$db = getDB();
$pendingOrders  = $db->query("SELECT COUNT(*) FROM orders WHERE order_status='pending'")->fetchColumn();
$pendingReturns = $db->query("SELECT COUNT(*) FROM return_requests WHERE status='pending'")->fetchColumn();
?>
<div class="sidebar" id="sidebar">
  <div class="sidebar-inner">
    <ul class="sidebar-menu list-unstyled mb-0">

      <!-- Dashboard -->
      <li><a href="<?= ADMIN_URL ?>/index.php" class="<?= isActive('/admin/index.php') ?>">
        <i class="bi bi-speedometer2"></i><span>แดชบอร์ด</span>
      </a></li>

      <!-- ====== CONTENT MANAGEMENT ====== -->
      <li class="menu-section">จัดการเนื้อหา</li>

      <li><a href="<?= ADMIN_URL ?>/users/index.php" class="<?= isActive('/admin/users/') ?>">
        <i class="bi bi-people"></i><span>ผู้ใช้งาน</span>
      </a></li>

      <li><a href="<?= ADMIN_URL ?>/shops/index.php" class="<?= isActive('/admin/shops/') ?>">
        <i class="bi bi-shop"></i><span>ร้านค้า</span>
      </a></li>

      <li><a href="<?= ADMIN_URL ?>/categories/index.php" class="<?= isActive('/admin/categories/') ?>">
        <i class="bi bi-grid-3x3-gap"></i><span>หมวดหมู่</span>
      </a></li>

      <li><a href="<?= ADMIN_URL ?>/products/index.php" class="<?= isActive('/admin/products/') ?>">
        <i class="bi bi-box-seam"></i><span>สินค้า</span>
      </a></li>

      <li>
        <a href="<?= ADMIN_URL ?>/orders/index.php" class="<?= isActive('/admin/orders/') ?>">
          <i class="bi bi-cart3"></i><span>ออเดอร์</span>
          <?php if ($pendingOrders > 0): ?>
            <span class="badge bg-warning text-dark ms-auto"><?= $pendingOrders ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li>
        <a href="<?= ADMIN_URL ?>/returns/index.php" class="<?= isActive('/admin/returns/') ?>">
          <i class="bi bi-arrow-return-left"></i><span>คืนสินค้า</span>
          <?php if ($pendingReturns > 0): ?>
            <span class="badge bg-danger ms-auto"><?= $pendingReturns ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li><a href="<?= ADMIN_URL ?>/reviews/index.php" class="<?= isActive('/admin/reviews/') ?>">
        <i class="bi bi-star-half"></i><span>รีวิว</span>
      </a></li>

      <!-- ====== PROMOTIONS ====== -->
      <li class="menu-section">โปรโมชัน</li>

      <li><a href="<?= ADMIN_URL ?>/vouchers/index.php" class="<?= isActive('/admin/vouchers/') ?>">
        <i class="bi bi-ticket-perforated"></i><span>โค้ดส่วนลด</span>
      </a></li>

      <li><a href="<?= ADMIN_URL ?>/flash-sales/index.php" class="<?= isActive('/admin/flash-sales/') ?>">
        <i class="bi bi-lightning-charge"></i><span>Flash Sale</span>
      </a></li>

      <li><a href="<?= ADMIN_URL ?>/banners/index.php" class="<?= isActive('/admin/banners/') ?>">
        <i class="bi bi-image"></i><span>แบนเนอร์</span>
      </a></li>

      <!-- ====== REPORTS ====== -->
      <li class="menu-section">รายงาน</li>

      <li><a href="<?= ADMIN_URL ?>/reports/index.php" class="<?= isActive('/admin/reports/') ?>">
        <i class="bi bi-bar-chart-line"></i><span>รายงานภาพรวม</span>
      </a></li>

      <li><a href="<?= ADMIN_URL ?>/reports/sales.php" class="<?= isActive('/admin/reports/sales') ?>">
        <i class="bi bi-graph-up-arrow"></i><span>รายงานยอดขาย</span>
      </a></li>

      <!-- ====== CMS ====== -->
      <li class="menu-section">CMS</li>

      <li>
        <a href="#cmsMenu" data-bs-toggle="collapse" class="has-arrow <?= isActive('/admin/cms/') ? '' : 'collapsed' ?>">
          <i class="bi bi-layout-text-window-reverse"></i><span>จัดการเนื้อหา</span>
          <i class="bi bi-chevron-down arrow ms-auto"></i>
        </a>
        <ul class="collapse <?= isActive('/admin/cms/') ? 'show' : '' ?> sub-menu list-unstyled" id="cmsMenu">
          <li><a href="<?= ADMIN_URL ?>/cms/pages.php" class="<?= isActive('/cms/pages') ?>"><i class="bi bi-file-earmark-text"></i> หน้าเว็บ</a></li>
          <li><a href="<?= ADMIN_URL ?>/cms/menus.php" class="<?= isActive('/cms/menus') ?>"><i class="bi bi-list-ul"></i> เมนู</a></li>
          <li><a href="<?= ADMIN_URL ?>/cms/widgets.php" class="<?= isActive('/cms/widgets') ?>"><i class="bi bi-layout-wtf"></i> Widget</a></li>
        </ul>
      </li>

      <!-- ====== SETTINGS ====== -->
      <li class="menu-section">ตั้งค่า</li>

      <li>
        <a href="#settingsMenu" data-bs-toggle="collapse" class="has-arrow <?= isActive('/admin/settings/') ? '' : 'collapsed' ?>">
          <i class="bi bi-gear"></i><span>ตั้งค่าระบบ</span>
          <i class="bi bi-chevron-down arrow ms-auto"></i>
        </a>
        <ul class="collapse <?= isActive('/admin/settings/') ? 'show' : '' ?> sub-menu list-unstyled" id="settingsMenu">
          <li><a href="<?= ADMIN_URL ?>/settings/general.php" class="<?= isActive('/settings/general') ?>"><i class="bi bi-sliders"></i> ทั่วไป</a></li>
          <li><a href="<?= ADMIN_URL ?>/settings/payment.php" class="<?= isActive('/settings/payment') ?>"><i class="bi bi-credit-card"></i> การชำระเงิน</a></li>
          <li><a href="<?= ADMIN_URL ?>/settings/shipping.php" class="<?= isActive('/settings/shipping') ?>"><i class="bi bi-truck"></i> การจัดส่ง</a></li>
          <li><a href="<?= ADMIN_URL ?>/settings/seo.php" class="<?= isActive('/settings/seo') ?>"><i class="bi bi-search"></i> SEO</a></li>
          <li><a href="<?= ADMIN_URL ?>/settings/appearance.php" class="<?= isActive('/settings/appearance') ?>"><i class="bi bi-palette"></i> ธีม & รูปลักษณ์</a></li>
          <?php if (isSuperAdmin()): ?>
          <li><hr class="dropdown-divider my-1"></li>
          <li><a href="<?= ADMIN_URL ?>/settings/admins.php" class="<?= isActive('/settings/admins') ?>"><i class="bi bi-shield-person"></i> จัดการแอดมิน</a></li>
          <li><a href="<?= ADMIN_URL ?>/settings/permissions.php" class="<?= isActive('/settings/permissions') ?>"><i class="bi bi-key"></i> สิทธิ์การเข้าถึง</a></li>
          <li><a href="<?= ADMIN_URL ?>/settings/activity-log.php" class="<?= isActive('/settings/activity') ?>"><i class="bi bi-journal-text"></i> Activity Log</a></li>
          <?php endif; ?>
        </ul>
      </li>

    </ul>
  </div>
</div>
