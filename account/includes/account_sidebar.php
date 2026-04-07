<?php
$u    = frontCurrentUser();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$nav  = [
    '/webshop/account/profile.php'      => ['bi-person-circle',   'โปรไฟล์ของฉัน'],
    '/webshop/account/orders.php'       => ['bi-bag',             'คำสั่งซื้อของฉัน'],
    '/webshop/account/wishlist.php'     => ['bi-heart',           'สินค้าที่ถูกใจ'],
    '/webshop/account/addresses.php'    => ['bi-geo-alt',         'ที่อยู่ของฉัน'],
    '/webshop/account/wallet.php'       => ['bi-wallet2',         'กระเป๋าเงิน'],
];
?>
<div class="account-sidebar">
  <div class="p-3 border-bottom d-flex align-items-center gap-3">
    <img src="<?= $u && $u['avatar_url'] ? e($u['avatar_url']) : 'https://ui-avatars.com/api/?name='.urlencode($u['username'] ?? 'U').'&background=ee4d2d&color=fff&size=50' ?>"
         class="account-avatar" alt="">
    <div>
      <div class="fw-semibold" style="font-size:14px"><?= e($u['full_name'] ?: $u['username']) ?></div>
      <a href="/webshop/account/profile.php" class="text-orange" style="font-size:12px"><i class="bi bi-pencil me-1"></i>แก้ไขโปรไฟล์</a>
    </div>
  </div>
  <nav class="account-nav">
    <?php foreach ($nav as $href => [$icon, $label]): ?>
    <a href="<?= $href ?>" class="nav-link <?= $path === $href ? 'active' : '' ?>">
      <i class="bi <?= $icon ?>"></i><?= $label ?>
    </a>
    <?php endforeach; ?>
    <hr class="my-1 mx-3">
    <a href="/webshop/account/logout.php" class="nav-link text-danger">
      <i class="bi bi-box-arrow-right"></i>ออกจากระบบ
    </a>
  </nav>
</div>
