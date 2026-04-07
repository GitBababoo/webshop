<?php
$pageTitle  = 'จัดการแอดมิน';
$breadcrumb = ['ตั้งค่า' => false, 'จัดการแอดมิน' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireRole('superadmin');
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $act = $_POST['action'] ?? '';
    $id  = (int)($_POST['user_id'] ?? 0);
    if ($act === 'create_admin') {
        $uname = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $name  = trim($_POST['full_name'] ?? '');
        $role  = in_array($_POST['role'] ?? '', ['admin','superadmin']) ? $_POST['role'] : 'admin';
        $pass  = $_POST['password'] ?? '';
        if ($uname && $email && $pass) {
            try {
                $db->prepare("INSERT INTO users (username,email,full_name,role,is_active,is_verified,password_hash) VALUES (?,?,?,?,1,1,?)")
                   ->execute([$uname,$email,$name,$role,password_hash($pass,PASSWORD_BCRYPT)]);
                logActivity('create','admins','user',(int)$db->lastInsertId(),"สร้าง Admin: $uname");
                flash('success','สร้าง Admin เรียบร้อย');
            } catch (PDOException $e) { flash('danger','Username หรือ Email ซ้ำ'); }
        } else { flash('danger','กรุณากรอกข้อมูลให้ครบ'); }
    }
    if ($act === 'reset_password' && $id) {
        $newPwd = $_POST['new_password'] ?? '';
        if (strlen($newPwd) >= 6) {
            $db->prepare("UPDATE users SET password_hash=? WHERE user_id=? AND role IN ('admin','superadmin')")->execute([password_hash($newPwd,PASSWORD_BCRYPT),$id]);
            logActivity('reset_password','admins','user',$id);
            flash('success','รีเซ็ตรหัสผ่านเรียบร้อย');
        } else { flash('danger','รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'); }
    }
    if ($act === 'change_role' && $id && $id !== (int)$_SESSION['admin_id']) {
        $newRole = in_array($_POST['new_role']??'',['admin','superadmin']) ? $_POST['new_role'] : 'admin';
        $db->prepare("UPDATE users SET role=? WHERE user_id=?")->execute([$newRole,$id]);
        logActivity('change_role','admins','user',$id,"เปลี่ยน role เป็น $newRole");
        flash('success','เปลี่ยน Role เรียบร้อย');
    }
    if ($act === 'deactivate' && $id && $id !== (int)$_SESSION['admin_id']) {
        $db->prepare("UPDATE users SET is_active=NOT is_active WHERE user_id=? AND role IN ('admin','superadmin')")->execute([$id]);
        flash('success','อัปเดตสถานะเรียบร้อย');
    }
    header('Location: admins.php'); exit;
}

$admins = $db->query("SELECT * FROM users WHERE role IN ('admin','superadmin') ORDER BY role DESC, created_at ASC")->fetchAll();
include dirname(__DIR__) . '/includes/header.php';
?>
<?php include 'settings-nav.php'; ?>
<div class="row g-4">
  <!-- Admin List -->
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-shield-person me-2"></i>แอดมินทั้งหมด (<?=count($admins)?> คน)</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light"><tr><th>ผู้ใช้</th><th>อีเมล</th><th>Role</th><th>สถานะ</th><th>เข้าสู่ระบบล่าสุด</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($admins as $a): $isMe = $a['user_id'] === (int)$_SESSION['admin_id']; ?>
          <tr class="<?=$a['is_active']?'':'table-secondary'?>">
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar bg-<?=$a['role']==='superadmin'?'danger':'primary'?> text-white"><?=mb_strtoupper(mb_substr($a['full_name']?:$a['username'],0,1))?></div>
                <div>
                  <div class="fw-semibold small"><?=e($a['full_name']?:$a['username'])?>
                    <?php if ($isMe): ?><span class="badge bg-success ms-1">คุณ</span><?php endif; ?>
                  </div>
                  <div class="text-muted" style="font-size:.72rem">@<?=e($a['username'])?></div>
                </div>
              </div>
            </td>
            <td class="small text-muted"><?=e($a['email'])?></td>
            <td>
              <?php if (!$isMe): ?>
              <form method="POST" class="d-inline">
                <?=csrfField()?><input type="hidden" name="action" value="change_role"><input type="hidden" name="user_id" value="<?=$a['user_id']?>">
                <select class="form-select form-select-sm" name="new_role" onchange="this.form.submit()" style="width:130px">
                  <option value="admin" <?=$a['role']==='admin'?'selected':''?>>Administrator</option>
                  <option value="superadmin" <?=$a['role']==='superadmin'?'selected':''?>>Super Admin</option>
                </select>
              </form>
              <?php else: ?>
                <span class="badge bg-danger">Super Admin</span>
              <?php endif; ?>
            </td>
            <td><?=$a['is_active']?'<span class="badge bg-success">ใช้งาน</span>':'<span class="badge bg-secondary">ระงับ</span>'?></td>
            <td class="text-muted small"><?=$a['last_login_at']?timeAgo($a['last_login_at']):'ไม่เคย'?></td>
            <td>
              <?php if (!$isMe): ?>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#pwdModal_<?=$a['user_id']?>" title="รีเซ็ต Password"><i class="bi bi-key"></i></button>
                <form method="POST" class="d-inline">
                  <?=csrfField()?><input type="hidden" name="action" value="deactivate"><input type="hidden" name="user_id" value="<?=$a['user_id']?>">
                  <button type="submit" class="btn btn-sm btn-outline-<?=$a['is_active']?'danger':'success'?>" title="<?=$a['is_active']?'ระงับ':'เปิด'?>"><i class="bi bi-<?=$a['is_active']?'slash-circle':'check-circle'?>"></i></button>
                </form>
              </div>
              <!-- Password Modal -->
              <div class="modal fade" id="pwdModal_<?=$a['user_id']?>" tabindex="-1">
                <div class="modal-dialog modal-sm"><div class="modal-content">
                  <div class="modal-header"><h6 class="modal-title">รีเซ็ตรหัสผ่าน: <?=e($a['username'])?></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                  <form method="POST"><div class="modal-body">
                    <?=csrfField()?><input type="hidden" name="action" value="reset_password"><input type="hidden" name="user_id" value="<?=$a['user_id']?>">
                    <input type="password" class="form-control" name="new_password" placeholder="รหัสผ่านใหม่ (6+ ตัว)" required minlength="6">
                  </div><div class="modal-footer"><button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-sm btn-danger">รีเซ็ต</button></div>
                  </form>
                </div></div>
              </div>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Create Admin Form -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-person-plus me-2"></i>สร้าง Admin ใหม่</div>
      <div class="card-body">
        <form method="POST">
          <?=csrfField()?>
          <input type="hidden" name="action" value="create_admin">
          <div class="mb-3"><label class="form-label">ชื่อผู้ใช้ *</label><input type="text" class="form-control" name="username" required></div>
          <div class="mb-3"><label class="form-label">ชื่อ-นามสกุล</label><input type="text" class="form-control" name="full_name"></div>
          <div class="mb-3"><label class="form-label">อีเมล *</label><input type="email" class="form-control" name="email" required></div>
          <div class="mb-3"><label class="form-label">Role</label>
            <select class="form-select" name="role">
              <option value="admin">Administrator</option>
              <option value="superadmin">Super Administrator</option>
            </select></div>
          <div class="mb-4"><label class="form-label">รหัสผ่าน *</label><input type="password" class="form-control" name="password" required minlength="6"></div>
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>สร้าง Admin</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
