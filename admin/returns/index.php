<?php
$pageTitle  = 'คำขอคืนสินค้า';
$breadcrumb = ['คืนสินค้า' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$st   = $_GET['status'] ?? '';
$page = max(1,(int)($_GET['page'] ?? 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $id     = (int)($_POST['return_id'] ?? 0);
    $act    = $_POST['action'] ?? '';
    $refund = (float)($_POST['refund_amount'] ?? 0);
    if ($id) {
        if ($act === 'approve') {
            $db->prepare("UPDATE return_requests SET status='approved', refund_amount=?, resolved_at=NOW() WHERE return_id=?")->execute([$refund, $id]);
            flash('success', 'อนุมัติคำขอคืนสินค้าเรียบร้อย');
        } elseif ($act === 'reject') {
            $db->prepare("UPDATE return_requests SET status='rejected', resolved_at=NOW() WHERE return_id=?")->execute([$id]);
            flash('success', 'ปฏิเสธคำขอเรียบร้อย');
        } elseif ($act === 'complete') {
            $db->prepare("UPDATE return_requests SET status='completed', resolved_at=NOW() WHERE return_id=?")->execute([$id]);
            flash('success', 'ดำเนินการคืนสินค้าสำเร็จ');
        }
        logActivity($act,'returns','return',$id);
        header('Location: index.php'); exit;
    }
}

$where = 'WHERE 1=1'; $params = [];
if ($st) { $where .= ' AND r.status=?'; $params[] = $st; }

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM return_requests r $where",
    "SELECT r.*, o.order_number, u.username, u.full_name FROM return_requests r
     JOIN orders o ON r.order_id=o.order_id
     JOIN users u ON r.buyer_user_id=u.user_id
     $where ORDER BY r.created_at DESC",
    $params, $page, 20);

include dirname(__DIR__) . '/includes/header.php';
$statusCfg = ['pending'=>['label'=>'รอดำเนินการ','class'=>'warning'],'approved'=>['label'=>'อนุมัติ','class'=>'success'],'rejected'=>['label'=>'ปฏิเสธ','class'=>'danger'],'completed'=>['label'=>'สำเร็จ','class'=>'info']];
?>
<div class="d-flex gap-2 mb-3 flex-wrap">
  <?php foreach (array_merge([''=>'ทั้งหมด'],array_map(fn($v)=>$v['label'],$statusCfg)) as $v=>$l): ?>
    <a href="?status=<?=$v?>" class="btn btn-sm <?=$st===$v?'btn-primary':'btn-outline-secondary'?>"><?=$l?></a>
  <?php endforeach; ?>
</div>
<div class="card">
  <div class="card-header fw-semibold"><i class="bi bi-arrow-return-left me-2"></i>คำขอคืนสินค้า (<?=number_format($result['total'])?> รายการ)</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>เลขออเดอร์</th><th>ผู้ซื้อ</th><th>เหตุผล</th><th>ประเภท</th><th>สถานะ</th><th>วันที่</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $r):
        $sc = $statusCfg[$r['status']] ?? ['label'=>$r['status'],'class'=>'secondary'];
      ?>
      <tr>
        <td><a href="<?=ADMIN_URL?>/orders/view.php?id=<?=$r['order_id']?>" class="fw-semibold text-decoration-none"><?=e($r['order_number'])?></a></td>
        <td class="small"><?=e($r['full_name']?:$r['username'])?></td>
        <td class="small"><?=e(mb_strimwidth($r['reason'],0,40,'...'))?></td>
        <td><span class="badge bg-info-subtle text-info border border-info-subtle"><?=$r['return_type']==='refund_only'?'คืนเงินอย่างเดียว':'คืนสินค้า+เงิน'?></span></td>
        <td><span class="badge bg-<?=$sc['class']?>"><?=$sc['label']?></span></td>
        <td class="text-muted small"><?=formatDate($r['created_at'],'d/m/Y')?></td>
        <td>
          <?php if ($r['status'] === 'pending'): ?>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="return_id" value="<?=$r['return_id']?>">
            <div class="d-flex gap-1 align-items-center">
              <input type="number" name="refund_amount" class="form-control form-control-sm" style="width:90px" placeholder="฿ คืน">
              <button type="submit" name="action" value="approve" class="btn btn-sm btn-success" title="อนุมัติ"><i class="bi bi-check"></i></button>
              <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" title="ปฏิเสธ" onclick="return confirm('ปฏิเสธคำขอ?')"><i class="bi bi-x"></i></button>
            </div>
          </form>
          <?php elseif ($r['status'] === 'approved'): ?>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="return_id" value="<?=$r['return_id']?>">
            <button type="submit" name="action" value="complete" class="btn btn-sm btn-outline-success">สำเร็จ</button>
          </form>
          <?php else: ?>
          <?php if ($r['refund_amount']): ?><span class="text-success small fw-bold">คืน <?=formatPrice((float)$r['refund_amount'])?></span><?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'index.php?status='.urlencode($st))?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
