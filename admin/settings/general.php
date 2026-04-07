<?php
$pageTitle  = 'ตั้งค่าทั่วไป';
$breadcrumb = ['ตั้งค่า' => false, 'ทั่วไป' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $keys = ['site_name','site_tagline','site_email','site_phone','site_address','maintenance_mode','currency','currency_symbol'];
    foreach ($keys as $key) {
        $val = trim($_POST[$key] ?? '');
        $db->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key=?")->execute([$val,$key]);
    }
    if (!empty($_FILES['site_logo']['name'])) {
        $up = uploadFile($_FILES['site_logo'],'settings');
        if ($up) $db->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key='site_logo'")->execute([$up]);
    }
    if (!empty($_FILES['site_favicon']['name'])) {
        $up = uploadFile($_FILES['site_favicon'],'settings');
        if ($up) $db->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key='site_favicon'")->execute([$up]);
    }
    logActivity('update','settings',null,null,'อัปเดตตั้งค่าทั่วไป');
    flash('success','บันทึกการตั้งค่าเรียบร้อย');
    header('Location: general.php'); exit;
}

$settings = [];
foreach ($db->query("SELECT setting_key,setting_value FROM site_settings WHERE setting_group='general'")->fetchAll() as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}
include dirname(__DIR__) . '/includes/header.php';
?>
<!-- Settings Nav -->
<?php include 'settings-nav.php'; ?>

<form method="POST" enctype="multipart/form-data">
  <?=csrfField()?>
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-globe me-2"></i>ข้อมูลเว็บไซต์</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">ชื่อเว็บไซต์ *</label>
              <input type="text" class="form-control" name="site_name" value="<?=e($settings['site_name']??'')?>" required></div>
            <div class="col-md-6"><label class="form-label">Tagline / คำขวัญ</label>
              <input type="text" class="form-control" name="site_tagline" value="<?=e($settings['site_tagline']??'')?>"></div>
            <div class="col-md-6"><label class="form-label">อีเมลติดต่อ</label>
              <input type="email" class="form-control" name="site_email" value="<?=e($settings['site_email']??'')?>"></div>
            <div class="col-md-6"><label class="form-label">เบอร์โทรศัพท์</label>
              <input type="text" class="form-control" name="site_phone" value="<?=e($settings['site_phone']??'')?>"></div>
            <div class="col-12"><label class="form-label">ที่อยู่</label>
              <textarea class="form-control" name="site_address" rows="2"><?=e($settings['site_address']??'')?></textarea></div>
          </div>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-currency-exchange me-2"></i>สกุลเงิน</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">สกุลเงิน</label>
              <input type="text" class="form-control" name="currency" value="<?=e($settings['currency']??'THB')?>" placeholder="THB"></div>
            <div class="col-md-6"><label class="form-label">สัญลักษณ์</label>
              <input type="text" class="form-control" name="currency_symbol" value="<?=e($settings['currency_symbol']??'฿')?>" placeholder="฿"></div>
          </div>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-tools me-2 text-warning"></i>โหมดบำรุงรักษา</div>
        <div class="card-body">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="maintenanceToggle" <?=($settings['maintenance_mode']??'0')==='1'?'checked':''?>>
            <label class="form-check-label fw-semibold" for="maintenanceToggle">เปิดโหมดปิดปรับปรุง</label>
          </div>
          <p class="text-muted small mt-1 mb-0">เมื่อเปิดใช้งาน ผู้เยี่ยมชมจะเห็นหน้า "กำลังปรับปรุง" แต่ Admin ยังสามารถเข้าใช้งานได้</p>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-image me-2"></i>โลโก้ & Favicon</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">โลโก้เว็บไซต์</label>
            <div class="img-preview-wrap mb-2" onclick="document.getElementById('logoFile').click()" style="height:80px">
              <?php if ($settings['site_logo']??''): ?>
                <img src="<?=e($settings['site_logo'])?>" id="logoPreview" style="max-height:60px" alt="">
              <?php else: ?>
                <span class="text-muted small"><i class="bi bi-cloud-upload fs-3 d-block mb-1"></i>อัปโหลดโลโก้</span>
              <?php endif; ?>
            </div>
            <input type="file" id="logoFile" class="d-none" name="site_logo" accept="image/*" data-preview="logoPreview">
          </div>
          <div>
            <label class="form-label">Favicon (32×32 px)</label>
            <div class="img-preview-wrap mb-2" onclick="document.getElementById('faviconFile').click()" style="height:60px">
              <?php if ($settings['site_favicon']??''): ?>
                <img src="<?=e($settings['site_favicon'])?>" id="faviconPreview" style="max-height:40px" alt="">
              <?php else: ?>
                <span class="text-muted small"><i class="bi bi-image fs-3 d-block"></i>Favicon</span>
              <?php endif; ?>
            </div>
            <input type="file" id="faviconFile" class="d-none" name="site_favicon" accept="image/*" data-preview="faviconPreview">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า</button>
  </div>
</form>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
