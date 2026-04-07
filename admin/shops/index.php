<?php
$pageTitle  = 'จัดการร้านค้า';
$breadcrumb = ['ร้านค้า' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$q    = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? '';
$page = max(1,(int)($_GET['page'] ?? 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $id  = (int)($_POST['shop_id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($id) {
        if ($act === 'verify')   $db->prepare("UPDATE shops SET is_verified=1 WHERE shop_id=?")->execute([$id]);
        if ($act === 'ban')      $db->prepare("UPDATE shops SET is_active=0 WHERE shop_id=?")->execute([$id]);
        if ($act === 'unban')    $db->prepare("UPDATE shops SET is_active=1 WHERE shop_id=?")->execute([$id]);
        logActivity($act,'shops','shop',$id);
        flash('success','ดำเนินการสำเร็จ');
        header('Location: index.php'); exit;
    }
}

$where = 'WHERE 1=1'; $params = [];
if ($q)    { $where .= ' AND (s.shop_name LIKE ? OR s.shop_slug LIKE ? OR u.username LIKE ?)'; $params = array_merge($params,["%$q%","%$q%","%$q%"]); }
if ($type) { $where .= ' AND s.shop_type=?'; $params[] = $type; }

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM shops s JOIN users u ON s.owner_user_id=u.user_id $where",
    "SELECT s.*, u.username, u.email, u.full_name FROM shops s JOIN users u ON s.owner_user_id=u.user_id $where ORDER BY s.created_at DESC",
    $params, $page, 25);

include dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <?php foreach ([''=>'ทั้งหมด','individual'=>'Individual','mall'=>'Mall','official'=>'Official'] as $t=>$l): ?>
      <a href="?type=<?=$t?>&q=<?=urlencode($q)?>" class="btn btn-sm <?=$type===$t?'btn-primary':'btn-outline-secondary'?>"><?=$l?></a>
    <?php endforeach; ?>
  </div>
</div>
<div class="card">
  <div class="card-header">
    <form class="d-flex gap-2" method="GET">
      <input type="hidden" name="type" value="<?=e($type)?>">
      <input type="text" class="form-control form-control-sm" name="q" placeholder="ค้นหาชื่อร้าน / เจ้าของ..." value="<?=e($q)?>" style="max-width:280px">
      <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
      <?php if ($q): ?><a href="?type=<?=e($type)?>" class="btn btn-sm btn-outline-secondary">ล้าง</a><?php endif; ?>
      <span class="ms-auto text-muted small align-self-center">ทั้งหมด <?=number_format($result['total'])?> ร้าน</span>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>ร้านค้า</th><th>เจ้าของ</th><th>ประเภท</th><th>สินค้า</th><th>ยอดขาย</th><th>คะแนน</th><th>สถานะ</th><th>วันที่สมัคร</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $s): ?>
      <tr>
        <td>
          <div class="d-flex align-items-center gap-2">
            <?php if ($s['logo_url']): ?><img src="<?=e($s['logo_url'])?>" class="avatar" alt=""><?php else: ?>
            <div class="avatar bg-warning text-dark"><?=mb_strtoupper(mb_substr($s['shop_name'],0,1))?></div><?php endif; ?>
            <div>
              <div class="fw-semibold small"><?=e($s['shop_name'])?></div>
              <div class="text-muted" style="font-size:.75rem">@<?=e($s['shop_slug'])?></div>
            </div>
          </div>
        </td>
        <td class="small"><?=e($s['full_name']?:$s['username'])?><br><span class="text-muted"><?=e($s['email'])?></span></td>
        <td><span class="badge bg-info-subtle text-info border border-info-subtle"><?=ucfirst($s['shop_type'])?></span>
          <?php if ($s['is_verified']): ?><span class="ms-1 text-primary"><i class="bi bi-patch-check-fill"></i></span><?php endif; ?></td>
        <td><?=number_format((int)$s['total_products'])?></td>
        <td><?=number_format((int)$s['total_sales'])?></td>
        <td><i class="bi bi-star-fill text-warning"></i> <?=number_format((float)$s['rating'],1)?> <small class="text-muted">(<?=$s['total_reviews']?>)</small></td>
        <td><?php if ($s['is_active']): ?><span class="badge bg-success-subtle text-success border border-success-subtle">เปิด</span>
            <?php else: ?><span class="badge bg-danger-subtle text-danger border border-danger-subtle">ระงับ</span><?php endif; ?></td>
        <td class="text-muted small"><?=formatDate($s['joined_at'],'d/m/Y')?></td>
        <td>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="shop_id" value="<?=$s['shop_id']?>">
            <div class="d-flex gap-1">
              <a href="view.php?id=<?=$s['shop_id']?>" class="btn btn-sm btn-outline-primary" title="ดู"><i class="bi bi-eye"></i></a>
              <?php if (!$s['is_verified']): ?>
              <button type="submit" name="action" value="verify" class="btn btn-sm btn-outline-success" title="ยืนยัน"><i class="bi bi-patch-check"></i></button>
              <?php endif; ?>
              <?php if ($s['is_active']): ?>
              <button type="submit" name="action" value="ban" class="btn btn-sm btn-outline-danger" title="ระงับ"><i class="bi bi-slash-circle"></i></button>
              <?php else: ?>
              <button type="submit" name="action" value="unban" class="btn btn-sm btn-outline-success" title="เปิด"><i class="bi bi-check-circle"></i></button>
              <?php endif; ?>
            </div>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'index.php?type='.urlencode($type).'&q='.urlencode($q))?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
