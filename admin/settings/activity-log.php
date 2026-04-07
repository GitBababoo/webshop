<?php
$pageTitle  = 'Activity Log';
$breadcrumb = ['ตั้งค่า' => false, 'Activity Log' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireRole('superadmin');
$db = getDB();

$q      = trim($_GET['q'] ?? '');
$module = $_GET['module'] ?? '';
$page   = max(1,(int)($_GET['page'] ?? 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    if ($_POST['action'] === 'clear' && isSuperAdmin()) {
        $db->query("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
        flash('success','ลบ Log เก่ากว่า 90 วันเรียบร้อย');
        header('Location: activity-log.php'); exit;
    }
}

$where = 'WHERE 1=1'; $params = [];
if ($q)      { $where .= ' AND (al.action LIKE ? OR al.description LIKE ? OR u.username LIKE ?)'; $params = array_merge($params,["%$q%","%$q%","%$q%"]); }
if ($module) { $where .= ' AND al.module=?'; $params[] = $module; }

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM activity_logs al LEFT JOIN users u ON al.user_id=u.user_id $where",
    "SELECT al.*, u.username, u.full_name FROM activity_logs al LEFT JOIN users u ON al.user_id=u.user_id $where ORDER BY al.created_at DESC",
    $params, $page, 30);

$modules = $db->query("SELECT DISTINCT module FROM activity_logs WHERE module IS NOT NULL ORDER BY module")->fetchAll(PDO::FETCH_COLUMN);
include dirname(__DIR__) . '/includes/header.php';
?>
<?php include 'settings-nav.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <a href="activity-log.php" class="btn btn-sm <?=$module===''?'btn-primary':'btn-outline-secondary'?>">ทั้งหมด</a>
    <?php foreach ($modules as $m): ?>
    <a href="?module=<?=urlencode($m)?>" class="btn btn-sm <?=$module===$m?'btn-primary':'btn-outline-secondary'?>"><?=e(ucfirst($m))?></a>
    <?php endforeach; ?>
  </div>
  <form method="POST" class="d-inline">
    <?=csrfField()?>
    <button type="submit" name="action" value="clear" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบ Log เก่ากว่า 90 วัน?')">
      <i class="bi bi-trash me-1"></i>ลบ Log เก่า
    </button>
  </form>
</div>
<div class="card">
  <div class="card-header">
    <form class="d-flex gap-2" method="GET">
      <input type="hidden" name="module" value="<?=e($module)?>">
      <input type="text" class="form-control form-control-sm" name="q" placeholder="ค้นหา action / ผู้ใช้..." value="<?=e($q)?>" style="max-width:280px">
      <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
      <span class="ms-auto text-muted small align-self-center">ทั้งหมด <?=number_format($result['total'])?> รายการ</span>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-sm mb-0">
      <thead class="table-light"><tr><th>วันที่/เวลา</th><th>ผู้ใช้</th><th>Action</th><th>Module</th><th>คำอธิบาย</th><th>IP</th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $log):
        $actionColors = ['login'=>'success','logout'=>'secondary','create'=>'primary','edit'=>'info','update'=>'info','delete'=>'danger','ban'=>'danger','activate'=>'success','deactivate'=>'warning'];
        $color = $actionColors[$log['action']] ?? 'secondary';
      ?>
      <tr>
        <td class="text-muted small text-nowrap"><?=formatDate($log['created_at'],'d/m/Y H:i:s')?></td>
        <td class="small">
          <?php if ($log['username']): ?>
          <span class="fw-semibold"><?=e($log['full_name']?:$log['username'])?></span><br>
          <span class="text-muted" style="font-size:.72rem">@<?=e($log['username'])?></span>
          <?php else: ?><span class="text-muted">System</span><?php endif; ?>
        </td>
        <td><span class="badge bg-<?=$color?>"><?=e($log['action'])?></span></td>
        <td><span class="badge bg-light text-dark border small"><?=e($log['module']??'—')?></span>
          <?php if ($log['target_type'] && $log['target_id']): ?>
            <span class="text-muted small ms-1"><?=e($log['target_type']).'#'.$log['target_id']?></span>
          <?php endif; ?>
        </td>
        <td class="small text-muted" style="max-width:300px"><div class="text-truncate"><?=e($log['description']??'—')?></div></td>
        <td class="small text-muted"><?=e($log['ip_address']??'—')?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'activity-log.php?module='.urlencode($module).'&q='.urlencode($q))?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
