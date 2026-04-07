<?php
$settingsPages = [
    'general.php'    => ['icon'=>'bi-sliders',       'label'=>'ทั่วไป'],
    'payment.php'    => ['icon'=>'bi-credit-card',   'label'=>'การชำระเงิน'],
    'shipping.php'   => ['icon'=>'bi-truck',         'label'=>'การจัดส่ง'],
    'seo.php'        => ['icon'=>'bi-search',        'label'=>'SEO & Tracking'],
    'appearance.php' => ['icon'=>'bi-palette',       'label'=>'ธีม & รูปลักษณ์'],
    'email.php'      => ['icon'=>'bi-envelope',      'label'=>'อีเมล'],
];
if (isSuperAdmin()) {
    $settingsPages['admins.php']      = ['icon'=>'bi-shield-person','label'=>'จัดการแอดมิน'];
    $settingsPages['permissions.php'] = ['icon'=>'bi-key',          'label'=>'สิทธิ์การเข้าถึง'];
    $settingsPages['activity-log.php']= ['icon'=>'bi-journal-text', 'label'=>'Activity Log'];
}
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="card mb-4">
  <div class="card-body py-2 px-3">
    <div class="d-flex gap-1 flex-wrap">
      <?php foreach ($settingsPages as $file => $info): ?>
      <a href="<?=ADMIN_URL?>/settings/<?=$file?>" class="btn btn-sm <?=$current===$file?'btn-primary':'btn-outline-secondary'?>">
        <i class="bi <?=$info['icon']?> me-1"></i><?=$info['label']?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
