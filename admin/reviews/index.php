<?php
$pageTitle  = 'จัดการรีวิว';
$breadcrumb = ['รีวิว' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$q    = trim($_GET['q'] ?? '');
$rating = (int)($_GET['rating'] ?? 0);
$page = max(1,(int)($_GET['page'] ?? 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $id  = (int)($_POST['review_id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($id) {
        if ($act === 'hide')   $db->prepare("UPDATE reviews SET is_hidden=1 WHERE review_id=?")->execute([$id]);
        if ($act === 'show')   $db->prepare("UPDATE reviews SET is_hidden=0 WHERE review_id=?")->execute([$id]);
        if ($act === 'delete' && isSuperAdmin()) $db->prepare("DELETE FROM reviews WHERE review_id=?")->execute([$id]);
        logActivity($act,'reviews','review',$id);
        flash('success','ดำเนินการสำเร็จ');
        header('Location: index.php'); exit;
    }
}

$where = 'WHERE 1=1'; $params = [];
if ($q) { $where .= ' AND (p.name LIKE ? OR u.username LIKE ? OR r.comment LIKE ?)'; $params = array_merge($params,["%$q%","%$q%","%$q%"]); }
if ($rating) { $where .= ' AND r.rating=?'; $params[] = $rating; }

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM reviews r JOIN products p ON r.product_id=p.product_id JOIN users u ON r.reviewer_id=u.user_id $where",
    "SELECT r.*, p.name AS product_name, u.username, u.full_name, s.shop_name,
        (SELECT COUNT(*) FROM review_images ri WHERE ri.review_id=r.review_id) AS img_count
     FROM reviews r JOIN products p ON r.product_id=p.product_id
     JOIN users u ON r.reviewer_id=u.user_id JOIN shops s ON r.shop_id=s.shop_id
     $where ORDER BY r.created_at DESC",
    $params, $page, 25);

include dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
  <span class="text-muted small">กรอง Rating:</span>
  <?php for ($i=5;$i>=1;$i--): ?>
  <a href="?rating=<?=$i?>&q=<?=urlencode($q)?>" class="btn btn-sm <?=$rating===$i?'btn-warning':'btn-outline-secondary'?>">
    <?=str_repeat('★',$i)?><?=str_repeat('☆',5-$i)?>
  </a>
  <?php endfor; ?>
  <a href="?q=<?=urlencode($q)?>" class="btn btn-sm <?=$rating===0?'btn-primary':'btn-outline-secondary'?>">ทั้งหมด</a>
</div>
<div class="card">
  <div class="card-header">
    <form class="d-flex gap-2" method="GET">
      <input type="hidden" name="rating" value="<?=$rating?>">
      <input type="text" class="form-control form-control-sm" name="q" placeholder="ค้นหาสินค้า / ผู้รีวิว..." value="<?=e($q)?>" style="max-width:280px">
      <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>สินค้า</th><th>ผู้รีวิว</th><th>คะแนน</th><th>ความเห็น</th><th>รูป</th><th>ร้านตอบ</th><th>สถานะ</th><th>วันที่</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $r): ?>
      <tr class="<?=$r['is_hidden']?'table-secondary':''?>">
        <td class="small fw-semibold" style="max-width:150px"><div class="text-truncate"><?=e($r['product_name'])?></div>
          <div class="text-muted" style="font-size:.72rem"><?=e($r['shop_name'])?></div></td>
        <td class="small"><?=e($r['full_name']?:$r['username'])?><?=$r['is_anonymous']?'<span class="badge bg-secondary ms-1">ไม่ระบุตัว</span>':''?></td>
        <td><span class="text-warning"><?=str_repeat('★',(int)$r['rating'])?></span></td>
        <td class="small" style="max-width:200px"><div class="text-truncate"><?=e($r['comment']??'—')?></div></td>
        <td class="text-center"><?=$r['img_count']>0?"<span class='badge bg-info'>{$r['img_count']}</span>":'—'?></td>
        <td class="small text-muted"><?=$r['seller_reply']?'<i class="bi bi-check-circle text-success"></i>':'—'?></td>
        <td><?=$r['is_hidden']?'<span class="badge bg-secondary">ซ่อน</span>':'<span class="badge bg-success">แสดง</span>'?></td>
        <td class="text-muted small"><?=formatDate($r['created_at'],'d/m/Y')?></td>
        <td>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="review_id" value="<?=$r['review_id']?>">
            <div class="d-flex gap-1">
              <?php if ($r['is_hidden']): ?>
              <button type="submit" name="action" value="show" class="btn btn-sm btn-outline-success" title="แสดง"><i class="bi bi-eye"></i></button>
              <?php else: ?>
              <button type="submit" name="action" value="hide" class="btn btn-sm btn-outline-warning" title="ซ่อน"><i class="bi bi-eye-slash"></i></button>
              <?php endif; ?>
              <?php if (isSuperAdmin()): ?>
              <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบรีวิวนี้?')" title="ลบ"><i class="bi bi-trash"></i></button>
              <?php endif; ?>
            </div>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'index.php?rating='.$rating.'&q='.urlencode($q))?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
