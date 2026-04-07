<?php
$pageTitle  = 'โปรไฟล์ของฉัน';
$breadcrumb = ['โปรไฟล์' => false];
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireLogin();
$db   = getDB();
$id   = (int)$_SESSION['admin_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE user_id=?");
$stmt->execute([$id]);
$me   = $stmt->fetch();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $act = $_POST['action'] ?? 'profile';
    if ($act === 'profile') {
        $full_name  = trim($_POST['full_name'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $gender     = $_POST['gender'] ?? null;
        $birth_date = $_POST['birth_date'] ?? null;
        $avatarUrl  = $me['avatar_url'] ?? '';
        if (!empty($_FILES['avatar']['name'])) {
            $up = uploadFile($_FILES['avatar'], 'avatars');
            if ($up) $avatarUrl = $up;
        }
        $db->prepare("UPDATE users SET full_name=?,phone=?,gender=?,birth_date=?,avatar_url=? WHERE user_id=?")
           ->execute([$full_name,$phone?:null,$gender?:null,$birth_date?:null,$avatarUrl,$id]);
        $_SESSION['admin_name']   = $full_name ?: $me['username'];
        $_SESSION['admin_avatar'] = $avatarUrl;
        logActivity('update','profile','user',$id,'อัปเดตโปรไฟล์');
        flash('success','บันทึกโปรไฟล์เรียบร้อย');
        header('Location: profile.php'); exit;
    }
    if ($act === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!password_verify($current, $me['password_hash'])) $errors[]='รหัสผ่านปัจจุบันไม่ถูกต้อง';
        if (strlen($new) < 6) $errors[]='รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร';
        if ($new !== $confirm) $errors[]='รหัสผ่านไม่ตรงกัน';
        if (!$errors) {
            $db->prepare("UPDATE users SET password_hash=? WHERE user_id=?")->execute([password_hash($new,PASSWORD_BCRYPT),$id]);
            logActivity('change_password','profile','user',$id);
            flash('success','เปลี่ยนรหัสผ่านเรียบร้อย');
            header('Location: profile.php'); exit;
        }
    }
}

$recentActivity = $db->prepare("SELECT * FROM activity_logs WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$recentActivity->execute([$id]);
$activity = $recentActivity->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<div class="row g-4 justify-content-center">
  <div class="col-lg-4">
    <!-- Profile Card -->
    <div class="card mb-3 text-center">
      <div class="card-body py-4">
        <div class="position-relative d-inline-block mb-3">
          <img src="<?= getAvatarUrl($me['avatar_url'], $me['username']) ?>" 
               class="rounded-circle shadow-sm" style="width:100px;height:100px;object-fit:cover" alt=""
               onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($me['username']) ?>&background=ee4d2d&color=fff&size=100'; this.onerror=null;">
        </div>
        <h5 class="fw-bold mb-1"><?=e($me['full_name']?:$me['username'])?></h5>
        <p class="text-muted small mb-2">@<?=e($me['username'])?></p>
        <span class="badge bg-<?=$me['role']==='superadmin'?'danger':'primary'?> fs-6"><?=ROLES[$me['role']]??$me['role']?></span>
        <hr>
        <div class="text-start small">
          <div class="d-flex gap-2 mb-1"><i class="bi bi-envelope text-muted"></i><span><?=e($me['email'])?></span></div>
          <?php if ($me['phone']): ?><div class="d-flex gap-2 mb-1"><i class="bi bi-telephone text-muted"></i><span><?=e($me['phone'])?></span></div><?php endif; ?>
          <div class="d-flex gap-2 mb-1"><i class="bi bi-calendar text-muted"></i><span>สมัครเมื่อ <?=formatDate($me['created_at'],'d/m/Y')?></span></div>
          <?php if ($me['last_login_at']): ?>
          <div class="d-flex gap-2"><i class="bi bi-clock text-muted"></i><span>เข้าสู่ระบบล่าสุด <?=timeAgo($me['last_login_at'])?></span></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <!-- Recent Activity -->
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-clock-history me-2"></i>กิจกรรมล่าสุด</div>
      <ul class="list-group list-group-flush">
        <?php foreach ($activity as $log): ?>
        <li class="list-group-item py-2">
          <div class="fw-semibold small"><?=e($log['action'])?> <span class="text-muted fw-normal"><?=e($log['module']??'')?></span></div>
          <div class="text-muted" style="font-size:.72rem"><?=formatDate($log['created_at'],'d/m/Y H:i')?> · <?=e($log['ip_address']??'')?></div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <div class="col-lg-7">
    <!-- Edit Profile -->
    <div class="card mb-3">
      <div class="card-header fw-semibold"><i class="bi bi-person-gear me-2"></i>แก้ไขข้อมูลส่วนตัว</div>
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <?=csrfField()?>
          <input type="hidden" name="action" value="profile">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">ชื่อ-นามสกุล</label>
              <input type="text" class="form-control" name="full_name" value="<?=e($me['full_name']??'')?>"></div>
            <div class="col-md-6"><label class="form-label">เบอร์โทรศัพท์</label>
              <input type="text" class="form-control" name="phone" value="<?=e($me['phone']??'')?>"></div>
            <div class="col-md-6"><label class="form-label">เพศ</label>
              <select class="form-select" name="gender">
                <option value="">-- ไม่ระบุ --</option>
                <option value="male" <?=($me['gender']??'')==='male'?'selected':''?>>ชาย</option>
                <option value="female" <?=($me['gender']??'')==='female'?'selected':''?>>หญิง</option>
                <option value="other" <?=($me['gender']??'')==='other'?'selected':''?>>อื่นๆ</option>
              </select></div>
            <div class="col-md-6"><label class="form-label">วันเกิด</label>
              <input type="date" class="form-control" name="birth_date" value="<?=e($me['birth_date']??'')?>"></div>
            <div class="col-12"><label class="form-label">รูปโปรไฟล์</label>
              <input type="file" class="form-control" name="avatar" accept="image/*" data-preview="avatarPreview">
              <img id="avatarPreview" src="<?= getAvatarUrl($me['avatar_url'], $me['username']) ?>" class="mt-2 rounded-circle shadow-sm" style="width:60px;height:60px;object-fit:cover" alt="">
            </div>
          </div>
          <div class="text-end mt-3"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึก</button></div>
        </form>
      </div>
    </div>

    <!-- Change Password -->
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-shield-lock me-2"></i>เปลี่ยนรหัสผ่าน</div>
      <div class="card-body">
        <?php if ($errors): ?><div class="alert alert-danger small"><ul class="mb-0"><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul></div><?php endif; ?>
        <form method="POST">
          <?=csrfField()?>
          <input type="hidden" name="action" value="password">
          <div class="row g-3">
            <div class="col-12"><label class="form-label">รหัสผ่านปัจจุบัน</label>
              <input type="password" class="form-control" name="current_password" required></div>
            <div class="col-md-6"><label class="form-label">รหัสผ่านใหม่</label>
              <input type="password" class="form-control" name="new_password" required minlength="6"></div>
            <div class="col-md-6"><label class="form-label">ยืนยันรหัสผ่านใหม่</label>
              <input type="password" class="form-control" name="confirm_password" required minlength="6"></div>
          </div>
          <div class="text-end mt-3"><button type="submit" class="btn btn-warning"><i class="bi bi-key me-1"></i>เปลี่ยนรหัสผ่าน</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
