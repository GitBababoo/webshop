<?php
$pageTitle  = 'ตั้งค่า SEO & Tracking';
$breadcrumb = ['ตั้งค่า' => false, 'SEO' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $keys = ['meta_title','meta_description','google_analytics','facebook_pixel','google_tag_manager','head_scripts','body_scripts'];
    foreach ($keys as $key) {
        $val = trim($_POST[$key] ?? '');
        $db->prepare("INSERT INTO site_settings (setting_group,setting_key,setting_value,setting_type,label) VALUES ('seo',?,?,'text',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$key,$val,$key,$val]);
    }
    $skeys = ['facebook_url','instagram_url','line_url','youtube_url','tiktok_url','twitter_url'];
    foreach ($skeys as $key) {
        $val = trim($_POST[$key] ?? '');
        $db->prepare("INSERT INTO site_settings (setting_group,setting_key,setting_value,setting_type,label) VALUES ('social',?,?,'text',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$key,$val,$key,$val]);
    }
    logActivity('update','settings',null,null,'อัปเดต SEO');
    flash('success','บันทึกการตั้งค่าเรียบร้อย');
    header('Location: seo.php'); exit;
}

$settings = [];
foreach ($db->query("SELECT setting_key,setting_value FROM site_settings WHERE setting_group IN ('seo','social')")->fetchAll() as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}
include dirname(__DIR__) . '/includes/header.php';
?>
<?php include 'settings-nav.php'; ?>
<form method="POST">
  <?=csrfField()?>
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-search me-2"></i>SEO หลัก</div>
        <div class="card-body">
          <div class="mb-3"><label class="form-label">Meta Title หลัก</label>
            <input type="text" class="form-control" name="meta_title" value="<?=e($settings['meta_title']??'')?>" placeholder="ชื่อเว็บ - คำอธิบายสั้น">
            <div class="form-text">จำนวนตัวอักษรแนะนำ: 50–60 ตัว</div></div>
          <div class="mb-3"><label class="form-label">Meta Description หลัก</label>
            <textarea class="form-control" name="meta_description" rows="3" maxlength="160" placeholder="คำอธิบายเว็บไซต์..."><?=e($settings['meta_description']??'')?></textarea>
            <div class="form-text">จำนวนตัวอักษรแนะนำ: 150–160 ตัว</div></div>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-graph-up me-2"></i>Analytics & Tracking</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><i class="bi bi-google me-1"></i>Google Analytics ID</label>
              <input type="text" class="form-control" name="google_analytics" value="<?=e($settings['google_analytics']??'')?>" placeholder="G-XXXXXXXXXX"></div>
            <div class="col-md-6"><label class="form-label"><i class="bi bi-facebook me-1"></i>Facebook Pixel ID</label>
              <input type="text" class="form-control" name="facebook_pixel" value="<?=e($settings['facebook_pixel']??'')?>" placeholder="XXXXXXXXXXXXXXXX"></div>
            <div class="col-12"><label class="form-label">Google Tag Manager ID</label>
              <input type="text" class="form-control" name="google_tag_manager" value="<?=e($settings['google_tag_manager']??'')?>" placeholder="GTM-XXXXXXX"></div>
          </div>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-code-slash me-2"></i>Custom Scripts</div>
        <div class="card-body">
          <div class="mb-3"><label class="form-label">Scripts ใน &lt;head&gt;</label>
            <textarea class="form-control font-monospace small" name="head_scripts" rows="4" placeholder="&lt;!-- Custom head scripts --&gt;"><?=htmlspecialchars($settings['head_scripts']??'')?></textarea></div>
          <div><label class="form-label">Scripts ก่อนปิด &lt;/body&gt;</label>
            <textarea class="form-control font-monospace small" name="body_scripts" rows="4" placeholder="&lt;!-- Custom body scripts --&gt;"><?=htmlspecialchars($settings['body_scripts']??'')?></textarea></div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-share me-2"></i>Social Media Links</div>
        <div class="card-body">
          <?php $socials = ['facebook_url'=>['icon'=>'bi-facebook','label'=>'Facebook'],'instagram_url'=>['icon'=>'bi-instagram','label'=>'Instagram'],'line_url'=>['icon'=>'bi-chat-dots','label'=>'LINE'],'youtube_url'=>['icon'=>'bi-youtube','label'=>'YouTube'],'tiktok_url'=>['icon'=>'bi-tiktok','label'=>'TikTok'],'twitter_url'=>['icon'=>'bi-twitter-x','label'=>'Twitter/X']]; ?>
          <?php foreach ($socials as $key=>$s): ?>
          <div class="mb-2">
            <label class="form-label small"><i class="bi <?=$s['icon']?> me-1"></i><?=$s['label']?></label>
            <input type="url" class="form-control form-control-sm" name="<?=$key?>" value="<?=e($settings[$key]??'')?>" placeholder="https://...">
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
