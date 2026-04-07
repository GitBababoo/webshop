<?php
$pageTitle  = 'โค้ดส่วนลดของร้านค้า';
$breadcrumb = ['โค้ดส่วนลด' => 'index.php', 'ร้านค้า' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$page = max(1,(int)($_GET['page'] ?? 1));
$q    = trim($_GET['q'] ?? '');
$shop_id = (int)($_GET['shop'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $id  = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($id) {
        if ($act === 'toggle') $db->prepare("UPDATE shop_vouchers SET is_active=NOT is_active WHERE voucher_id=?")->execute([$id]);
        if ($act === 'delete' && isSuperAdmin()) $db->prepare("DELETE FROM shop_vouchers WHERE voucher_id=?")->execute([$id]);
        flash('success','ดำเนินการสำเร็จ');
        header('Location: shop-vouchers.php'); exit;
    }
}

$where = 'WHERE 1=1'; $params = [];
if ($q)       { $where .= ' AND (sv.code LIKE ? OR sv.name LIKE ? OR s.shop_name LIKE ?)'; $params = array_merge($params,["%$q%","%$q%","%$q%"]); }
if ($shop_id) { $where .= ' AND sv.shop_id=?'; $params[] = $shop_id; }

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM shop_vouchers sv JOIN shops s ON sv.shop_id=s.shop_id $where",
    "SELECT sv.*, s.shop_name FROM shop_vouchers sv JOIN shops s ON sv.shop_id=s.shop_id $where ORDER BY sv.created_at DESC",
    $params, $page, 25);

include dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex gap-2">
    <a href="index.php" class="btn btn-sm btn-outline-secondary">Platform Vouchers</a>
    <a href="shop-vouchers.php" class="btn btn-sm btn-primary">ของร้านค้า</a>
  </div>
</div>
<div class="card">
  <div class="card-header">
    <form class="d-flex gap-2" method="GET">
      <input type="text" class="form-control form-control-sm" name="q" placeholder="ค้นหาโค้ด / ร้านค้า..." value="<?=e($q)?>" style="max-width:280px">
      <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
      <?php if ($q): ?><a href="shop-vouchers.php" class="btn btn-sm btn-outline-secondary">ล้าง</a><?php endif; ?>
      <span class="ms-auto text-muted small align-self-center">ทั้งหมด <?=number_format($result['total'])?> โค้ด</span>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>โค้ด</th><th>ร้านค้า</th><th>ชื่อ</th><th>ประเภท</th><th>มูลค่า</th><th>ใช้แล้ว/ทั้งหมด</th><th>หมดอายุ</th><th>สถานะ</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $v): ?>
      <tr>
        <td><code class="fw-bold text-primary"><?=e($v['code'])?></code></td>
        <td class="small text-muted"><?=e($v['shop_name'])?></td>
        <td class="small"><?=e($v['name'])?></td>
        <td>
          <?php if ($v['discount_type']==='percentage'): ?><span class="badge bg-info">ลด %</span>
          <?php elseif ($v['discount_type']==='fixed'): ?><span class="badge bg-warning text-dark">ลด ฿</span>
          <?php else: ?><span class="badge bg-success">ฟรีค่าส่ง</span><?php endif; ?>
        </td>
        <td class="fw-semibold small"><?=$v['discount_type']==='percentage'?$v['discount_value'].'%':formatPrice((float)$v['discount_value'])?></td>
        <td class="small"><?=number_format((int)$v['used_qty'])?> / <?=$v['total_qty']?number_format((int)$v['total_qty']):'∞'?></td>
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
          <?php if (isSuperAdmin()): ?>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="id" value="<?=$v['voucher_id']?>">
            <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบโค้ดนี้?')"><i class="bi bi-trash"></i></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'shop-vouchers.php?q='.urlencode($q))?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
