<?php
$pageTitle  = 'จัดการผู้ใช้งาน';
$breadcrumb = ['ผู้ใช้งาน' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$role = $_GET['role'] ?? '';
$q    = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $ids = array_map('intval', $_POST['ids'] ?? []);
    $act = $_POST['action'] ?? '';
    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        if ($act === 'activate')   $db->prepare("UPDATE users SET is_active=1 WHERE user_id IN ($placeholders)")->execute($ids);
        if ($act === 'deactivate') $db->prepare("UPDATE users SET is_active=0 WHERE user_id IN ($placeholders)")->execute($ids);
        if ($act === 'delete' && isSuperAdmin()) $db->prepare("DELETE FROM users WHERE user_id IN ($placeholders) AND role NOT IN ('admin','superadmin')")->execute($ids);
        logActivity($act, 'users', 'bulk', null, "Bulk $act: " . implode(',', $ids));
        flash('success', 'ดำเนินการสำเร็จ');
        header('Location: index.php'); exit;
    }
}

$where = 'WHERE 1=1';
$params = [];
if ($role) { $where .= ' AND role=?'; $params[] = $role; }
if ($q)    { $where .= ' AND (username LIKE ? OR email LIKE ? OR full_name LIKE ? OR phone LIKE ?)'; $params = array_merge($params, ["%$q%","%$q%","%$q%","%$q%"]); }

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM users $where",
    "SELECT * FROM users $where ORDER BY created_at DESC",
    $params, $page, 25
);

include dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <?php foreach ([''=>'ทั้งหมด','buyer'=>'Buyer','seller'=>'Seller','admin'=>'Admin','superadmin'=>'SuperAdmin'] as $r=>$label): ?>
      <a href="?role=<?= $r ?>&q=<?= urlencode($q) ?>" class="btn btn-sm <?= $role===$r?'btn-primary':'btn-outline-secondary' ?>"><?= $label ?></a>
    <?php endforeach; ?>
  </div>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>เพิ่มผู้ใช้</a>
</div>

<div class="card">
  <div class="card-header">
    <form class="d-flex gap-2" method="GET">
      <input type="hidden" name="role" value="<?= e($role) ?>">
      <input type="text" class="form-control form-control-sm" name="q" placeholder="ค้นหา ชื่อ / อีเมล / โทร..." value="<?= e($q) ?>" style="max-width:280px">
      <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
      <?php if ($q): ?><a href="?role=<?= e($role) ?>" class="btn btn-sm btn-outline-secondary">ล้าง</a><?php endif; ?>
      <span class="ms-auto text-muted small align-self-center">ทั้งหมด <?= number_format($result['total']) ?> รายการ</span>
    </form>
  </div>
  <form method="POST">
    <?= csrfField() ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light"><tr>
          <th><input type="checkbox" id="selectAll"></th>
          <th>ผู้ใช้</th><th>อีเมล / โทร</th><th>บทบาท</th><th>สถานะ</th><th>เข้าสู่ระบบล่าสุด</th><th>วันที่สมัคร</th><th></th>
        </tr></thead>
        <tbody>
          <?php foreach ($result['data'] as $u): ?>
          <tr>
            <td><input type="checkbox" name="ids[]" value="<?= $u['user_id'] ?>"></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <img src="<?= getAvatarUrl($u['avatar_url'], $u['username']) ?>" class="avatar" alt=""
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($u['username']) ?>&background=0d6efd&color=fff&size=32'; this.onerror=null;">
                <div>
                  <div class="fw-semibold small"><?= e($u['full_name'] ?: $u['username']) ?></div>
                  <div class="text-muted" style="font-size:.75rem">@<?= e($u['username']) ?></div>
                </div>
              </div>
            </td>
            <td class="small"><?= e($u['email']) ?><br><span class="text-muted"><?= e($u['phone'] ?? '-') ?></span></td>
            <td>
              <?php $rclr = ['superadmin'=>'danger','admin'=>'warning','seller'=>'info','buyer'=>'secondary']; ?>
              <span class="badge bg-<?= $rclr[$u['role']] ?? 'secondary' ?>"><?= ROLES[$u['role']] ?? $u['role'] ?></span>
            </td>
            <td>
              <?php if ($u['is_active']): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle">ใช้งาน</span>
              <?php else: ?>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">ระงับ</span>
              <?php endif; ?>
              <?php if ($u['is_verified']): ?><span class="ms-1 text-primary"><i class="bi bi-patch-check-fill" title="Verified"></i></span><?php endif; ?>
            </td>
            <td class="text-muted small"><?= $u['last_login_at'] ? timeAgo($u['last_login_at']) : 'ไม่เคย' ?></td>
            <td class="text-muted small"><?= formatDate($u['created_at'],'d/m/Y') ?></td>
            <td>
              <div class="d-flex gap-1">
                <a href="form.php?id=<?= $u['user_id'] ?>" class="btn btn-sm btn-outline-primary" title="แก้ไข"><i class="bi bi-pencil"></i></a>
                <?php if ($u['is_active']): ?>
                  <a href="action.php?act=deactivate&id=<?= $u['user_id'] ?>" class="btn btn-sm btn-outline-warning" data-confirm="ระงับผู้ใช้นี้?" title="ระงับ"><i class="bi bi-slash-circle"></i></a>
                <?php else: ?>
                  <a href="action.php?act=activate&id=<?= $u['user_id'] ?>" class="btn btn-sm btn-outline-success" title="เปิดใช้งาน"><i class="bi bi-check-circle"></i></a>
                <?php endif; ?>
                <?php if (isSuperAdmin() && !in_array($u['role'], ['admin','superadmin'])): ?>
                  <a href="action.php?act=delete&id=<?= $u['user_id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="ลบผู้ใช้นี้ถาวร?" title="ลบ"><i class="bi bi-trash"></i></a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
      <div class="d-flex gap-2 align-items-center">
        <select class="form-select form-select-sm" name="action" style="width:auto">
          <option value="">-- Bulk Action --</option>
          <option value="activate">เปิดใช้งาน</option>
          <option value="deactivate">ระงับ</option>
          <?php if (isSuperAdmin()): ?><option value="delete">ลบ</option><?php endif; ?>
        </select>
        <button class="btn btn-sm btn-outline-secondary">ดำเนินการ</button>
      </div>
      <?= paginator($result, 'index.php?role='.urlencode($role).'&q='.urlencode($q)) ?>
    </div>
  </form>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
