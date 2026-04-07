<?php
$pageTitle  = 'โค้ดส่วนลด Platform';
$breadcrumb = ['โค้ดส่วนลด' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$page = max(1,(int)($_GET['page'] ?? 1));
$q    = trim($_GET['q'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $id  = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($id) {
        if ($act === 'toggle') $db->prepare("UPDATE platform_vouchers SET is_active=NOT is_active WHERE voucher_id=?")->execute([$id]);
        if ($act === 'delete' && isSuperAdmin()) $db->prepare("DELETE FROM platform_vouchers WHERE voucher_id=?")->execute([$id]);
        flash('success','ดำเนินการสำเร็จ');
        header('Location: index.php'); exit;
    }
}

$where = $q ? "WHERE code LIKE ? OR name LIKE ?" : '';
$params = $q ? ["%$q%","%$q%"] : [];
$result = paginateQuery($db,
    "SELECT COUNT(*) FROM platform_vouchers $where",
    "SELECT * FROM platform_vouchers $where ORDER BY created_at DESC",
    $params, $page, 20);

include dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex gap-2">
    <a href="?tab=platform" class="btn btn-sm btn-primary">Platform</a>
    <a href="shop-vouchers.php" class="btn btn-sm btn-outline-secondary">ของร้านค้า</a>
  </div>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>เพิ่มโค้ด</a>
</div>
<div class="card">
  <div class="card-header">
    <form class="d-flex gap-2" method="GET">
      <input type="text" class="form-control form-control-sm" name="q" placeholder="ค้นหาโค้ด..." value="<?=e($q)?>" style="max-width:240px">
      <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>โค้ด</th><th>ชื่อ</th><th>ประเภทส่วนลด</th><th>มูลค่า</th><th>ขั้นต่ำ</th><th>ใช้แล้ว/ทั้งหมด</th><th>วันหมดอายุ</th><th>สถานะ</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $v): ?>
      <tr>
        <td><code class="fw-bold text-danger"><?=e($v['code'])?></code></td>
        <td class="small"><?=e($v['name'])?></td>
        <td>
          <?php if ($v['discount_type']==='percentage'): ?><span class="badge bg-info">ลด %</span>
          <?php elseif ($v['discount_type']==='fixed'): ?><span class="badge bg-warning text-dark">ลด ฿</span>
          <?php else: ?><span class="badge bg-success">ฟรีค่าส่ง</span><?php endif; ?>
        </td>
        <td class="fw-semibold small"><?=$v['discount_type']==='percentage'?$v['discount_value'].'%':formatPrice((float)$v['discount_value'])?></td>
        <td class="small"><?=$v['min_order_amount']>0?formatPrice((float)$v['min_order_amount']):'ไม่มีขั้นต่ำ'?></td>
        <td class="small">
          <div class="d-flex align-items-center gap-1">
            <span><?=number_format((int)$v['used_qty'])?> / <?=$v['total_qty']?number_format((int)$v['total_qty']):'∞'?></span>
            <?php if ($v['total_qty'] && $v['used_qty']>=$v['total_qty']): ?><span class="badge bg-danger">หมดแล้ว</span><?php endif; ?>
          </div>
        </td>
        <td class="small <?=strtotime($v['expire_at'])<time()?'text-danger':''?>"><?=formatDate($v['expire_at'],'d/m/Y')?></td>
        <td>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="id" value="<?=$v['voucher_id']?>">
            <button type="submit" name="action" value="toggle" class="btn btn-sm <?=$v['is_active']?'btn-success':'btn-outline-secondary'?> border-0 py-0">
              <i class="bi <?=$v['is_active']?'bi-toggle-on':'bi-toggle-off'?> fs-5"></i>
            </button>
          </form>
        </td>
        <td>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="id" value="<?=$v['voucher_id']?>">
            <div class="d-flex gap-1">
              <a href="form.php?id=<?=$v['voucher_id']?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <?php if (isSuperAdmin()): ?>
              <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบโค้ดนี้?')"><i class="bi bi-trash"></i></button>
              <?php endif; ?>
            </div>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'index.php?q='.urlencode($q))?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
