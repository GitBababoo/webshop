<?php
$pageTitle  = 'จัดการ Flash Sale';
$breadcrumb = ['Flash Sale' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$page = max(1,(int)($_GET['page'] ?? 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $id  = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($id) {
        if ($act==='toggle') $db->prepare("UPDATE flash_sales SET is_active=NOT is_active WHERE flash_sale_id=?")->execute([$id]);
        if ($act==='delete' && isSuperAdmin()) $db->prepare("DELETE FROM flash_sales WHERE flash_sale_id=?")->execute([$id]);
        flash('success','ดำเนินการสำเร็จ'); header('Location: index.php'); exit;
    }
    if ($act==='create') {
        $title    = trim($_POST['title']??'');
        $startAt  = $_POST['start_at']??'';
        $endAt    = $_POST['end_at']??'';
        if ($title && $startAt && $endAt) {
            $db->prepare("INSERT INTO flash_sales (title,start_at,end_at,is_active) VALUES (?,?,?,1)")->execute([$title,$startAt,$endAt]);
            flash('success','สร้าง Flash Sale เรียบร้อย'); header('Location: index.php'); exit;
        }
    }
}

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM flash_sales",
    "SELECT fs.*, (SELECT COUNT(*) FROM flash_sale_items WHERE flash_sale_id=fs.flash_sale_id) AS item_count FROM flash_sales fs ORDER BY fs.start_at DESC",
    [], $page, 20);

include dirname(__DIR__) . '/includes/header.php';
?>
<!-- Create Flash Sale -->
<div class="card mb-3">
  <div class="card-header"><i class="bi bi-plus-lg me-2"></i>สร้าง Flash Sale ใหม่</div>
  <div class="card-body">
    <form method="POST" class="row g-3">
      <?=csrfField()?>
      <div class="col-md-4"><label class="form-label">ชื่อ Flash Sale</label>
        <input type="text" class="form-control" name="title" placeholder="Flash Sale 12.00 น." required></div>
      <div class="col-md-3"><label class="form-label">เริ่มต้น</label>
        <input type="datetime-local" class="form-control" name="start_at" required></div>
      <div class="col-md-3"><label class="form-label">สิ้นสุด</label>
        <input type="datetime-local" class="form-control" name="end_at" required></div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" name="action" value="create" class="btn btn-primary w-100"><i class="bi bi-lightning-charge me-1"></i>สร้าง</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header fw-semibold"><i class="bi bi-lightning-charge me-2 text-warning"></i>Flash Sales ทั้งหมด</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>ชื่อ</th><th>เริ่มต้น</th><th>สิ้นสุด</th><th>สินค้า</th><th>สถานะ</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $fs):
        $now   = time();
        $start = strtotime($fs['start_at']);
        $end   = strtotime($fs['end_at']);
        if ($fs['is_active'] && $now>=$start && $now<=$end) $badge='<span class="badge bg-success">กำลังดำเนิน</span>';
        elseif ($now < $start && $fs['is_active']) $badge='<span class="badge bg-info">รอเริ่ม</span>';
        elseif ($now > $end) $badge='<span class="badge bg-secondary">สิ้นสุด</span>';
        else $badge='<span class="badge bg-warning text-dark">ปิดใช้งาน</span>';
      ?>
      <tr>
        <td class="fw-semibold"><?=e($fs['title'])?></td>
        <td class="small"><?=formatDate($fs['start_at'],'d/m/Y H:i')?></td>
        <td class="small"><?=formatDate($fs['end_at'],'d/m/Y H:i')?></td>
        <td><?=number_format((int)$fs['item_count'])?> สินค้า</td>
        <td><?=$badge?></td>
        <td>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="id" value="<?=$fs['flash_sale_id']?>">
            <div class="d-flex gap-1">
              <a href="items.php?id=<?=$fs['flash_sale_id']?>" class="btn btn-sm btn-outline-primary" title="จัดการสินค้า"><i class="bi bi-list-ul"></i></a>
              <button type="submit" name="action" value="toggle" class="btn btn-sm <?=$fs['is_active']?'btn-success':'btn-outline-secondary'?> border-0 py-0">
                <i class="bi <?=$fs['is_active']?'bi-toggle-on':'bi-toggle-off'?> fs-5"></i>
              </button>
              <?php if (isSuperAdmin()): ?>
              <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบ?')"><i class="bi bi-trash"></i></button>
              <?php endif; ?>
            </div>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'index.php')?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
