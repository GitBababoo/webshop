<?php
$pageTitle  = 'ตั้งค่าธีม & รูปลักษณ์';
$breadcrumb = ['ตั้งค่า' => false, 'ธีม & รูปลักษณ์' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $keys = ['primary_color','secondary_color','header_bg','footer_bg','btn_radius','font_family','custom_css'];
    foreach ($keys as $key) {
        $val = trim($_POST[$key] ?? '');
        $db->prepare("INSERT INTO site_settings (setting_group,setting_key,setting_value,setting_type,label) VALUES ('appearance',?,?,'text',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$key,$val,$key,$val]);
    }
    logActivity('update','settings',null,null,'อัปเดตธีม');
    flash('success','บันทึกการตั้งค่าเรียบร้อย');
    header('Location: appearance.php'); exit;
}

$settings = [];
foreach ($db->query("SELECT setting_key,setting_value FROM site_settings WHERE setting_group='appearance'")->fetchAll() as $r) {
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
        <div class="card-header fw-semibold"><i class="bi bi-palette me-2"></i>สีหลัก</div>
        <div class="card-body">
          <div class="row g-3">
            <?php $colorFields = [
              'primary_color'   => ['label'=>'สีหลัก (Primary)',      'default'=>'#EE4D2D'],
              'secondary_color' => ['label'=>'สีรอง (Secondary)',     'default'=>'#FF7337'],
              'header_bg'       => ['label'=>'สี Header Background',  'default'=>'#EE4D2D'],
              'footer_bg'       => ['label'=>'สี Footer Background',  'default'=>'#222222'],
            ]; ?>
            <?php foreach ($colorFields as $key=>$cf): $val = $settings[$key]??$cf['default']; ?>
            <div class="col-md-6">
              <label class="form-label"><?=$cf['label']?></label>
              <div class="input-group">
                <input type="color" class="form-control form-control-color" id="color_<?=$key?>" value="<?=e($val)?>" data-text-sync="text_<?=$key?>">
                <input type="text" class="form-control font-monospace" id="text_<?=$key?>" name="<?=$key?>" value="<?=e($val)?>" pattern="#[0-9A-Fa-f]{6}" maxlength="7">
                <span class="input-group-text"><div class="color-swatch" style="background:<?=e($val)?>"></div></span>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-type me-2"></i>ฟอนต์ & รูปแบบ</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">ฟอนต์หลัก</label>
              <select class="form-select" name="font_family">
                <?php $fonts = ['Sarabun, sans-serif'=>'Sarabun','Kanit, sans-serif'=>'Kanit','Prompt, sans-serif'=>'Prompt','Noto Sans Thai, sans-serif'=>'Noto Sans Thai','Segoe UI, sans-serif'=>'Segoe UI']; ?>
                <?php foreach ($fonts as $fv=>$fl): ?>
                <option value="<?=$fv?>" <?=($settings['font_family']??'')===$fv?'selected':''?>><?=$fl?></option>
                <?php endforeach; ?>
              </select></div>
            <div class="col-md-6"><label class="form-label">ความโค้งมนปุ่ม</label>
              <select class="form-select" name="btn_radius">
                <option value=".375rem" <?=($settings['btn_radius']??'')==='.375rem'?'selected':''?>>เหลี่ยม (Default)</option>
                <option value="2rem" <?=($settings['btn_radius']??'')==='2rem'?'selected':''?>>โค้งมน (Pill)</option>
                <option value="0" <?=($settings['btn_radius']??'')==='0'?'selected':''?>>มุมฉาก (Square)</option>
              </select></div>
          </div>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-code-slash me-2"></i>Custom CSS</div>
        <div class="card-body">
          <textarea class="form-control font-monospace small" name="custom_css" rows="8" placeholder="/* ใส่ CSS เพิ่มเติมที่นี่ */"><?=htmlspecialchars($settings['custom_css']??'')?></textarea>
        </div>
      </div>
    </div>
    <!-- Preview -->
    <div class="col-lg-5">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-eye me-2"></i>ตัวอย่าง</div>
        <div class="card-body p-2">
          <div id="previewBox" style="border-radius:.5rem;overflow:hidden;border:1px solid #dee2e6">
            <div id="previewHeader" style="background:<?=e($settings['header_bg']??'#EE4D2D');?>;padding:.75rem 1rem;color:#fff">
              <strong style="font-size:.9rem" id="previewSiteName"><?=getSetting('site_name','Shopee TH')?></strong>
            </div>
            <div style="padding:1rem;background:#f8f9fa">
              <button id="previewBtn" style="background:<?=e($settings['primary_color']??'#EE4D2D')?>;color:#fff;border:none;padding:.5rem 1.25rem;border-radius:<?=e($settings['btn_radius']??'.375rem')?>;cursor:pointer">ปุ่มหลัก</button>
              <button style="background:<?=e($settings['secondary_color']??'#FF7337')?>;color:#fff;border:none;padding:.5rem 1.25rem;border-radius:<?=e($settings['btn_radius']??'.375rem')?>;cursor:pointer;margin-left:.5rem">ปุ่มรอง</button>
              <div style="margin-top:1rem;background:#fff;border-radius:.5rem;padding:1rem;box-shadow:0 1px 4px rgba(0,0,0,.08)">
                <div style="height:12px;background:#eee;border-radius:4px;margin-bottom:.5rem;width:60%"></div>
                <div style="height:10px;background:#eee;border-radius:4px;width:80%"></div>
              </div>
            </div>
            <div id="previewFooter" style="background:<?=e($settings['footer_bg']??'#222222')?>;padding:.75rem 1rem;color:#999;font-size:.8rem">Footer</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า</button>
  </div>
</form>
<script>
// Live preview
const fields = {primary_color:'previewBtn',header_bg:'previewHeader',footer_bg:'previewFooter'};
Object.entries(fields).forEach(([name,elId])=>{
  const input = document.querySelector(`input[name="${name}"]`);
  const el    = document.getElementById(elId);
  if (input && el) {
    input.addEventListener('input',()=>{
      if (name==='primary_color'||name==='secondary_color') el.style.background=input.value;
      else el.style.background=input.value;
    });
  }
});
</script>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
