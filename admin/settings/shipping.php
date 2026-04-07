<?php
$pageTitle  = 'ตั้งค่าการจัดส่ง';
$breadcrumb = ['ตั้งค่า' => false, 'การจัดส่ง' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $act = $_POST['action'] ?? 'settings';
    if ($act === 'settings') {
        $keys = ['free_shipping_min','default_shipping_fee','express_shipping_fee','same_day_enabled','express_enabled'];
        foreach ($keys as $key) {
            $val = trim($_POST[$key] ?? '');
            $db->prepare("INSERT INTO site_settings (setting_group,setting_key,setting_value,setting_type,label) VALUES ('shipping',?,?,'text',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$key,$val,$key,$val]);
        }
        flash('success','บันทึกการตั้งค่าเรียบร้อย');
    }
    if ($act === 'add_provider') {
        $name = trim($_POST['prov_name'] ?? '');
        $code = strtoupper(trim($_POST['prov_code'] ?? ''));
        $tracking = trim($_POST['tracking_url'] ?? '');
        if ($name && $code) {
            $db->prepare("INSERT INTO shipping_providers (name,code,tracking_url,is_active) VALUES (?,?,?,1) ON DUPLICATE KEY UPDATE name=?,tracking_url=?")->execute([$name,$code,$tracking,$name,$tracking]);
            flash('success','เพิ่มบริษัทขนส่งเรียบร้อย');
        }
    }
    if ($act === 'toggle_provider') {
        $pid = (int)($_POST['provider_id'] ?? 0);
        if ($pid) $db->prepare("UPDATE shipping_providers SET is_active=NOT is_active WHERE provider_id=?")->execute([$pid]);
    }
    logActivity('update','settings',null,null,'อัปเดตตั้งค่าการจัดส่ง');
    header('Location: shipping.php'); exit;
}

$settings = [];
foreach ($db->query("SELECT setting_key,setting_value FROM site_settings WHERE setting_group='shipping'")->fetchAll() as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}
$providers = $db->query("SELECT * FROM shipping_providers ORDER BY is_active DESC, name")->fetchAll();
include dirname(__DIR__) . '/includes/header.php';
?>
<?php include 'settings-nav.php'; ?>
<div class="row g-3">
  <div class="col-lg-6">
    <form method="POST">
      <?=csrfField()?>
      <input type="hidden" name="action" value="settings">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-truck me-2"></i>นโยบายค่าจัดส่ง</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">ฟรีค่าส่งเมื่อซื้อครบ (฿)</label>
              <input type="number" class="form-control" name="free_shipping_min" value="<?=e($settings['free_shipping_min']??'500')?>" step="1" min="0" placeholder="0 = ฟรีค่าส่งทุกออเดอร์"></div>
            <div class="col-md-6"><label class="form-label">ค่าส่งมาตรฐาน (฿)</label>
              <input type="number" class="form-control" name="default_shipping_fee" value="<?=e($settings['default_shipping_fee']??'40')?>" step="1" min="0"></div>
            <div class="col-md-6"><label class="form-label">ค่าส่ง Express (฿)</label>
              <input type="number" class="form-control" name="express_shipping_fee" value="<?=e($settings['express_shipping_fee']??'80')?>" step="1" min="0"></div>
            <div class="col-12">
              <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="express_enabled" value="1" <?=($settings['express_enabled']??'0')==='1'?'checked':''?> id="chkExpress">
                <label class="form-check-label" for="chkExpress">เปิดบริการ Express Delivery</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="same_day_enabled" value="1" <?=($settings['same_day_enabled']??'0')==='1'?'checked':''?> id="chkSameDay">
                <label class="form-check-label" for="chkSameDay">เปิดบริการ Same Day Delivery</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
    </form>
  </div>
  <div class="col-lg-6">
    <div class="card mb-3">
      <div class="card-header fw-semibold"><i class="bi bi-box me-2"></i>บริษัทขนส่ง</div>
      <div class="list-group list-group-flush">
        <?php foreach ($providers as $prov): ?>
        <div class="list-group-item d-flex align-items-center justify-content-between py-2">
          <div>
            <div class="fw-semibold small"><?=e($prov['name'])?> <code class="ms-1"><?=e($prov['code'])?></code></div>
            <?php if ($prov['tracking_url']): ?><div class="text-muted" style="font-size:.72rem"><?=e(substr($prov['tracking_url'],0,40))?>...</div><?php endif; ?>
          </div>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="action" value="toggle_provider"><input type="hidden" name="provider_id" value="<?=$prov['provider_id']?>">
            <button type="submit" class="btn btn-sm border-0 py-0 <?=$prov['is_active']?'text-success':'text-muted'?>">
              <i class="bi <?=$prov['is_active']?'bi-toggle-on':'bi-toggle-off'?> fs-5"></i>
            </button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="card-footer">
        <form method="POST" class="row g-2">
          <?=csrfField()?>
          <input type="hidden" name="action" value="add_provider">
          <div class="col-5"><input type="text" class="form-control form-control-sm" name="prov_name" placeholder="ชื่อขนส่ง *" required></div>
          <div class="col-3"><input type="text" class="form-control form-control-sm text-uppercase" name="prov_code" placeholder="CODE" required></div>
          <div class="col-4"><button type="submit" class="btn btn-sm btn-primary w-100">เพิ่ม</button></div>
          <div class="col-12"><input type="text" class="form-control form-control-sm" name="tracking_url" placeholder="Tracking URL (ใส่ {tracking_no} แทนเลข)"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
