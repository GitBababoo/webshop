<?php
$pageTitle  = 'ตั้งค่าการชำระเงิน';
$breadcrumb = ['ตั้งค่า' => false, 'การชำระเงิน' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $keys = ['cod_enabled','bank_transfer_enabled','credit_card_enabled','bank_name','bank_account','bank_account_name','promptpay_number','omise_public_key','omise_secret_key','payment_note'];
    foreach ($keys as $key) {
        $val = trim($_POST[$key] ?? '');
        $db->prepare("INSERT INTO site_settings (setting_group,setting_key,setting_value,setting_type,label) VALUES ('payment',?,?,'text',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$key,$val,$key,$val]);
    }
    logActivity('update','settings',null,null,'อัปเดตตั้งค่าการชำระเงิน');
    flash('success','บันทึกการตั้งค่าเรียบร้อย');
    header('Location: payment.php'); exit;
}

$settings = [];
foreach ($db->query("SELECT setting_key,setting_value FROM site_settings WHERE setting_group='payment'")->fetchAll() as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}
include dirname(__DIR__) . '/includes/header.php';
?>
<?php include 'settings-nav.php'; ?>
<form method="POST">
  <?=csrfField()?>
  <div class="row g-3">
    <div class="col-lg-6">
      <!-- Payment Methods Toggle -->
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-toggles me-2"></i>เปิด/ปิดวิธีชำระเงิน</div>
        <div class="card-body">
          <?php $methods = ['cod_enabled'=>['icon'=>'bi-cash','label'=>'เก็บเงินปลายทาง (COD)'],'bank_transfer_enabled'=>['icon'=>'bi-bank','label'=>'โอนเงินผ่านธนาคาร'],'credit_card_enabled'=>['icon'=>'bi-credit-card','label'=>'บัตรเครดิต / เดบิต']]; ?>
          <?php foreach ($methods as $key=>$m): ?>
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
              <i class="bi <?=$m['icon']?> fs-5 text-muted"></i>
              <span class="fw-semibold small"><?=$m['label']?></span>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="<?=$key?>" value="1" <?=($settings[$key]??'0')==='1'?'checked':''?> id="chk_<?=$key?>">
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <!-- Bank Transfer Info -->
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-bank me-2"></i>ข้อมูลธนาคารสำหรับโอน</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12"><label class="form-label">ชื่อธนาคาร</label>
              <input type="text" class="form-control" name="bank_name" value="<?=e($settings['bank_name']??'')?>" placeholder="ธนาคารกสิกรไทย"></div>
            <div class="col-md-6"><label class="form-label">เลขบัญชี</label>
              <input type="text" class="form-control" name="bank_account" value="<?=e($settings['bank_account']??'')?>" placeholder="000-0-00000-0"></div>
            <div class="col-md-6"><label class="form-label">ชื่อบัญชี</label>
              <input type="text" class="form-control" name="bank_account_name" value="<?=e($settings['bank_account_name']??'')?>"></div>
            <div class="col-12"><label class="form-label">หมายเลข PromptPay</label>
              <input type="text" class="form-control" name="promptpay_number" value="<?=e($settings['promptpay_number']??'')?>" placeholder="0XX-XXX-XXXX หรือ เลข Tax ID"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <!-- Credit Card (Omise) -->
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-credit-card me-2"></i>บัตรเครดิต (Omise)</div>
        <div class="card-body">
          <div class="alert alert-info small d-flex gap-2"><i class="bi bi-info-circle-fill"></i><span>ต้องสมัครใช้งาน <a href="https://omise.co" target="_blank">Omise</a> ก่อนจึงจะใช้งานได้</span></div>
          <div class="mb-3"><label class="form-label">Public Key</label>
            <input type="text" class="form-control font-monospace small" name="omise_public_key" value="<?=e($settings['omise_public_key']??'')?>" placeholder="pkey_..."></div>
          <div><label class="form-label">Secret Key <span class="text-danger">*เก็บเป็นความลับ*</span></label>
            <input type="password" class="form-control font-monospace small" name="omise_secret_key" value="<?=e($settings['omise_secret_key']??'')?>" placeholder="skey_..."></div>
        </div>
      </div>
      <!-- Payment Note -->
      <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-chat-text me-2"></i>หมายเหตุการชำระเงิน</div>
        <div class="card-body">
          <label class="form-label">ข้อความแสดงหลังสั่งซื้อ</label>
          <textarea class="form-control" name="payment_note" rows="3" placeholder="ขอบคุณที่สั่งซื้อ กรุณาชำระเงินภายใน 24 ชั่วโมง"><?=e($settings['payment_note']??'')?></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-end mt-3">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า</button>
  </div>
</form>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
