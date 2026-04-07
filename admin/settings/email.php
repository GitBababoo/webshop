<?php
$pageTitle  = 'ตั้งค่าอีเมล (SMTP)';
$breadcrumb = ['ตั้งค่า' => false, 'อีเมล' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $keys = ['smtp_host','smtp_port','smtp_user','smtp_pass','from_name','from_email','smtp_encryption',
             'email_order_confirm','email_order_shipped','email_order_delivered','email_welcome'];
    foreach ($keys as $key) {
        $val = trim($_POST[$key] ?? '');
        $db->prepare("INSERT INTO site_settings (setting_group,setting_key,setting_value,setting_type,label) VALUES ('email',?,?,'text',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$key,$val,$key,$val]);
    }
    logActivity('update','settings',null,null,'อัปเดตตั้งค่าอีเมล');
    flash('success','บันทึกการตั้งค่าอีเมลเรียบร้อย');
    header('Location: email.php'); exit;
}

$settings = [];
foreach ($db->query("SELECT setting_key,setting_value FROM site_settings WHERE setting_group='email'")->fetchAll() as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}
include dirname(__DIR__) . '/includes/header.php';
?>
<?php include 'settings-nav.php'; ?>
<form method="POST">
  <?=csrfField()?>
  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-envelope-at me-2"></i>SMTP Configuration</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label">SMTP Host</label>
              <input type="text" class="form-control" name="smtp_host" value="<?=e($settings['smtp_host']??'smtp.gmail.com')?>" placeholder="smtp.gmail.com"></div>
            <div class="col-md-4"><label class="form-label">Port</label>
              <input type="number" class="form-control" name="smtp_port" value="<?=e($settings['smtp_port']??'587')?>" placeholder="587"></div>
            <div class="col-md-6"><label class="form-label">SMTP Username</label>
              <input type="text" class="form-control" name="smtp_user" value="<?=e($settings['smtp_user']??'')?>"></div>
            <div class="col-md-6"><label class="form-label">SMTP Password</label>
              <input type="password" class="form-control" name="smtp_pass" value="<?=e($settings['smtp_pass']??'')?>" placeholder="App Password"></div>
            <div class="col-md-4"><label class="form-label">Encryption</label>
              <select class="form-select" name="smtp_encryption">
                <option value="tls" <?=($settings['smtp_encryption']??'tls')==='tls'?'selected':''?>>TLS</option>
                <option value="ssl" <?=($settings['smtp_encryption']??'')==='ssl'?'selected':''?>>SSL</option>
                <option value="none" <?=($settings['smtp_encryption']??'')==='none'?'selected':''?>>None</option>
              </select></div>
            <div class="col-md-4"><label class="form-label">ชื่อผู้ส่ง</label>
              <input type="text" class="form-control" name="from_name" value="<?=e($settings['from_name']??getSetting('site_name','Shopee TH'))?>"></div>
            <div class="col-md-4"><label class="form-label">อีเมลผู้ส่ง</label>
              <input type="email" class="form-control" name="from_email" value="<?=e($settings['from_email']??'noreply@shopee.th')?>"></div>
          </div>
          <div class="alert alert-info small mt-3 d-flex gap-2 mb-0">
            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
            <div>สำหรับ Gmail ให้ใช้ <strong>App Password</strong> แทนรหัสผ่านปกติ และเปิด 2-Step Verification ก่อน
            <a href="https://support.google.com/accounts/answer/185833" target="_blank" class="ms-1">คลิกที่นี่ <i class="bi bi-box-arrow-up-right"></i></a></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-toggle-on me-2"></i>เปิด/ปิดอีเมลอัตโนมัติ</div>
        <div class="card-body">
          <?php $emailToggles = [
            'email_order_confirm'   => 'อีเมลยืนยันออเดอร์',
            'email_order_shipped'   => 'อีเมลแจ้งจัดส่ง',
            'email_order_delivered' => 'อีเมลแจ้งรับสินค้าแล้ว',
            'email_welcome'         => 'อีเมลต้อนรับสมาชิกใหม่',
          ]; ?>
          <?php foreach ($emailToggles as $key=>$label): ?>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="<?=$key?>" value="1" id="chk_<?=$key?>" <?=($settings[$key]??'1')==='1'?'checked':''?>>
            <label class="form-check-label" for="chk_<?=$key?>"><?=$label?></label>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า</button>
  </div>
</form>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
