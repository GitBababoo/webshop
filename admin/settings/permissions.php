<?php
$pageTitle  = 'จัดการสิทธิ์การเข้าถึง';
$breadcrumb = ['ตั้งค่า' => false, 'สิทธิ์การเข้าถึง' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireRole('superadmin');
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $targetUser = (int)($_POST['target_user_id'] ?? 0);
    if ($targetUser) {
        $allPerms = $db->query("SELECT perm_id FROM permissions")->fetchAll(PDO::FETCH_COLUMN);
        $granted  = array_map('intval', $_POST['perms'] ?? []);
        $db->prepare("DELETE FROM admin_permissions WHERE user_id=?")->execute([$targetUser]);
        foreach ($allPerms as $pid) {
            $isGranted = in_array((int)$pid, $granted) ? 1 : 0;
            $db->prepare("INSERT INTO admin_permissions (user_id,perm_id,granted) VALUES (?,?,?)")->execute([$targetUser,$pid,$isGranted]);
        }
        logActivity('update_permissions','admins','user',$targetUser,'อัปเดตสิทธิ์');
        flash('success','บันทึกสิทธิ์เรียบร้อย');
        header("Location: permissions.php?uid=$targetUser"); exit;
    }
}

$selectedUid = (int)($_GET['uid'] ?? 0);
$admins = $db->query("SELECT user_id,username,full_name,role FROM users WHERE role='admin' ORDER BY username")->fetchAll();
$allPerms = $db->query("SELECT * FROM permissions ORDER BY perm_group, perm_id")->fetchAll();
$userPerms = [];
if ($selectedUid) {
    $s = $db->prepare("SELECT perm_id,granted FROM admin_permissions WHERE user_id=?");
    $s->execute([$selectedUid]);
    foreach ($s->fetchAll() as $r) $userPerms[$r['perm_id']] = (bool)$r['granted'];
}
$permsByGroup = [];
foreach ($allPerms as $p) $permsByGroup[$p['perm_group']][] = $p;

include dirname(__DIR__) . '/includes/header.php';
?>
<?php include 'settings-nav.php'; ?>
<div class="row g-4">
  <!-- Select Admin -->
  <div class="col-lg-3">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-person-badge me-2"></i>เลือก Admin</div>
      <div class="list-group list-group-flush">
        <?php foreach ($admins as $a): ?>
        <a href="permissions.php?uid=<?=$a['user_id']?>" class="list-group-item list-group-item-action d-flex align-items-center gap-2 <?=$selectedUid===$a['user_id']?'active':''?>">
          <div class="avatar bg-primary text-white" style="width:32px;height:32px;font-size:.8rem"><?=mb_strtoupper(mb_substr($a['full_name']?:$a['username'],0,1))?></div>
          <div>
            <div class="small fw-semibold"><?=e($a['full_name']?:$a['username'])?></div>
            <div style="font-size:.7rem;opacity:.7">@<?=e($a['username'])?></div>
          </div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($admins)): ?>
        <div class="list-group-item text-muted small">ไม่มี Admin (ยกเว้น SuperAdmin)</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Permissions Grid -->
  <div class="col-lg-9">
    <?php if ($selectedUid):
      $selAdmin = $db->prepare("SELECT * FROM users WHERE user_id=?"); $selAdmin->execute([$selectedUid]); $selAdmin=$selAdmin->fetch();
    ?>
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-key me-2"></i>สิทธิ์ของ <?=e($selAdmin['full_name']?:$selAdmin['username'])?></span>
        <div>
          <button class="btn btn-sm btn-outline-primary" id="checkAllBtn" onclick="document.querySelectorAll('.perm-cb').forEach(c=>c.checked=true);return false">เลือกทั้งหมด</button>
          <button class="btn btn-sm btn-outline-secondary" onclick="document.querySelectorAll('.perm-cb').forEach(c=>c.checked=false);return false">ยกเลิกทั้งหมด</button>
        </div>
      </div>
      <form method="POST">
        <?=csrfField()?>
        <input type="hidden" name="target_user_id" value="<?=$selectedUid?>">
        <div class="card-body">
          <?php foreach ($permsByGroup as $group => $perms): ?>
          <h6 class="text-uppercase text-muted fw-bold mb-2 mt-3" style="font-size:.75rem;letter-spacing:.1em"><?=e(ucfirst($group))?></h6>
          <div class="row g-2 mb-3">
            <?php foreach ($perms as $perm): $checked = $userPerms[$perm['perm_id']] ?? false; ?>
            <div class="col-md-4 col-sm-6">
              <div class="form-check border rounded p-2 ps-4 <?=$checked?'border-primary bg-primary bg-opacity-10':''?>">
                <input class="form-check-input perm-cb" type="checkbox" name="perms[]" value="<?=$perm['perm_id']?>" <?=$checked?'checked':''?> id="perm_<?=$perm['perm_id']?>">
                <label class="form-check-label small" for="perm_<?=$perm['perm_id']?>">
                  <strong class="d-block"><?=e($perm['label'])?></strong>
                  <span class="text-muted" style="font-size:.7rem"><?=e($perm['perm_key'])?></span>
                </label>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="card-footer text-end">
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>บันทึกสิทธิ์</button>
        </div>
      </form>
    </div>
    <?php else: ?>
    <div class="card"><div class="card-body text-center text-muted py-5">
      <i class="bi bi-arrow-left-circle fs-1 d-block mb-2"></i>เลือก Admin ทางซ้ายเพื่อจัดการสิทธิ์
    </div></div>
    <?php endif; ?>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
