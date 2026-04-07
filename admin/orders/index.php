<?php
$pageTitle  = 'จัดการออเดอร์';
$breadcrumb = ['ออเดอร์' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db  = getDB();
$q   = trim($_GET['q'] ?? '');
$st  = $_GET['status'] ?? '';
$pay = $_GET['pay'] ?? '';
$page= max(1,(int)($_GET['page'] ?? 1));

$where = 'WHERE 1=1'; $params = [];
if ($q)  { $where .= ' AND (o.order_number LIKE ? OR u.username LIKE ? OR u.full_name LIKE ?)'; $params=array_merge($params,["%$q%","%$q%","%$q%"]); }
if ($st)  { $where .= ' AND o.order_status=?'; $params[]=$st; }
if ($pay) { $where .= ' AND o.payment_status=?'; $params[]=$pay; }

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM orders o JOIN users u ON o.buyer_user_id=u.user_id $where",
    "SELECT o.*, u.username, u.full_name, s.shop_name FROM orders o
     JOIN users u ON o.buyer_user_id=u.user_id
     JOIN shops s ON o.shop_id=s.shop_id
     $where ORDER BY o.created_at DESC",
    $params, $page, 25);

include dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex gap-2 flex-wrap mb-3">
  <?php foreach (array_merge([''=>'ทั้งหมด'],array_map(fn($v)=>$v['label'],ORDER_STATUSES)) as $v=>$l): ?>
    <a href="?status=<?=$v?>&q=<?=urlencode($q)?>&pay=<?=urlencode($pay)?>" class="btn btn-sm <?=$st===$v?'btn-primary':'btn-outline-secondary'?>"><?=$l?></a>
  <?php endforeach; ?>
</div>
<div class="card">
  <div class="card-header">
    <form class="d-flex flex-wrap gap-2" method="GET">
      <input type="hidden" name="status" value="<?=e($st)?>">
      <input type="text" class="form-control form-control-sm" name="q" placeholder="เลขออเดอร์ / ชื่อผู้ซื้อ..." value="<?=e($q)?>" style="max-width:240px">
      <select class="form-select form-select-sm" name="pay" style="width:160px">
        <option value="">-- การชำระเงิน --</option>
        <?php foreach (PAYMENT_STATUSES as $pv=>$pc): ?>
        <option value="<?=$pv?>" <?=$pay===$pv?'selected':''?>><?=$pc['label']?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
      <?php if ($q||$pay): ?><a href="?status=<?=e($st)?>" class="btn btn-sm btn-outline-secondary">ล้าง</a><?php endif; ?>
      <span class="ms-auto text-muted small align-self-center">ทั้งหมด <?=number_format($result['total'])?> ออเดอร์</span>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>เลขออเดอร์</th><th>ผู้ซื้อ</th><th>ร้านค้า</th><th>ยอดรวม</th><th>การชำระ</th><th>สถานะ</th><th>วันที่</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $o):
        $scfg = ORDER_STATUSES[$o['order_status']] ?? ['label'=>$o['order_status'],'class'=>'secondary'];
        $pcfg = PAYMENT_STATUSES[$o['payment_status']] ?? ['label'=>$o['payment_status'],'class'=>'secondary'];
      ?>
      <tr>
        <td><a href="view.php?id=<?=$o['order_id']?>" class="fw-semibold text-decoration-none"><?=e($o['order_number'])?></a></td>
        <td class="small"><?=e($o['full_name']?:$o['username'])?></td>
        <td class="small text-muted"><?=e($o['shop_name'])?></td>
        <td class="fw-semibold"><?=formatPrice((float)$o['total_amount'])?></td>
        <td><span class="badge bg-<?=$pcfg['class']?>"><?=$pcfg['label']?></span></td>
        <td><span class="badge bg-<?=$scfg['class']?>"><?=$scfg['label']?></span></td>
        <td class="text-muted small"><?=formatDate($o['created_at'],'d/m/Y H:i')?></td>
        <td><a href="view.php?id=<?=$o['order_id']?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'index.php?status='.urlencode($st).'&pay='.urlencode($pay).'&q='.urlencode($q))?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
