<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
frontRequireLogin('/webshop/account/profile.php');

$userId = (int)$_SESSION['front_user_id'];
$user   = frontCurrentUser();
$error  = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    $db  = getDB();

    if ($act === 'profile') {
        $full = trim($_POST['full_name'] ?? '');
        $phone= trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $birth  = $_POST['birth_date'] ?? null;
        $bio    = trim($_POST['bio'] ?? '');
        $db->prepare("UPDATE users SET full_name=?,phone=?,gender=?,birth_date=?,bio=? WHERE user_id=?")
           ->execute([$full,$phone,$gender,$birth?:null,$bio,$userId]);
        $success = 'อัพเดตโปรไฟล์สำเร็จ';

    } elseif ($act === 'password') {
        $old = $_POST['old_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $cfm = $_POST['confirm_new'] ?? '';
        if (!password_verify($old, $user['password_hash'] ?? '')) { $error = 'รหัสผ่านเดิมไม่ถูกต้อง'; }
        elseif (strlen($new) < 6) { $error = 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร'; }
        elseif ($new !== $cfm) { $error = 'ยืนยันรหัสผ่านไม่ตรงกัน'; }
        else {
            $db->prepare("UPDATE users SET password_hash=? WHERE user_id=?")->execute([password_hash($new, PASSWORD_BCRYPT), $userId]);
            $success = 'เปลี่ยนรหัสผ่านสำเร็จ';
        }

    } elseif ($act === 'avatar' && isset($_FILES['avatar'])) {
        $file = $_FILES['avatar'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) { $error = 'รองรับเฉพาะ JPG, PNG, WEBP'; }
        elseif ($file['size'] > 2*1024*1024) { $error = 'ขนาดไฟล์ต้องไม่เกิน 2MB'; }
        else {
            $dir     = dirname(__DIR__) . '/uploads/avatars/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $newName = 'user_'.$userId.'_'.time().'.'.$ext;
            if (move_uploaded_file($file['tmp_name'], $dir.$newName)) {
                $url = '/webshop/uploads/avatars/'.$newName;
                $db->prepare("UPDATE users SET avatar_url=? WHERE user_id=?")->execute([$url,$userId]);
                $_SESSION['front_avatar'] = $url;
                $success = 'อัพเดตรูปโปรไฟล์สำเร็จ';
            } else { $error = 'อัพโหลดไม่สำเร็จ กรุณาลองใหม่'; }
        }
    }
    // Refresh user data
    $stmt = getDB()->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}

// Get roles for this user
$roleStmt = getDB()->prepare("SELECT r.role_name,r.color,r.icon FROM user_roles ur JOIN roles r ON ur.role_id=r.role_id WHERE ur.user_id=? AND ur.is_active=1 ORDER BY r.sort_order");
$roleStmt->execute([$userId]);
$userRoles = $roleStmt->fetchAll();

// Loyalty
$loyaltyStmt = getDB()->prepare("SELECT * FROM loyalty_points WHERE user_id=?");
$loyaltyStmt->execute([$userId]);
$loyalty = $loyaltyStmt->fetch();

$pageTitle = 'โปรไฟล์ของฉัน';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container-xl py-3">
  <div class="row g-3">
    <div class="col-md-3">
      <?php include __DIR__ . '/includes/account_sidebar.php'; ?>
    </div>
    <div class="col-md-9">
      <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div><?php endif; ?>

      <!-- Profile Header Card -->
      <div class="surface mb-3">
        <div class="d-flex align-items-center gap-4 flex-wrap">
          <div class="position-relative">
            <img src="<?= getAvatarUrl($user['avatar_url'], $user['username']) ?>"
                 class="rounded-circle" width="96" height="96" style="object-fit:cover;border:3px solid var(--shopee-orange)" alt="" id="avatarPreview"
                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['username']) ?>&background=ee4d2d&color=fff&size=96'; this.onerror=null;">
            <form method="POST" enctype="multipart/form-data" class="d-inline">
              <input type="hidden" name="action" value="avatar">
              <label class="position-absolute bottom-0 end-0 bg-orange rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;cursor:pointer" title="เปลี่ยนรูป">
                <i class="bi bi-camera-fill text-white" style="font-size:12px"></i>
                <input type="file" name="avatar" accept="image/*" class="d-none" data-preview="avatarPreview" onchange="this.closest('form').submit()">
              </label>
            </form>
          </div>
          <div>
            <h4 class="fw-bold mb-1"><?= e($user['full_name'] ?: $user['username']) ?></h4>
            <div class="text-muted mb-2" style="font-size:13px"><?= e($user['email']) ?></div>
            <div class="d-flex gap-2 flex-wrap">
              <?php foreach ($userRoles as $r): ?>
              <span class="badge" style="background:<?= e($r['color']) ?>;font-size:12px">
                <i class="bi <?= e($r['icon'] ?? 'bi-person') ?> me-1"></i><?= e($r['role_name']) ?>
              </span>
              <?php endforeach; ?>
              <?php if ($user['is_verified']): ?>
              <span class="badge bg-success" style="font-size:12px"><i class="bi bi-patch-check-fill me-1"></i>ยืนยันแล้ว</span>
              <?php endif; ?>
            </div>
          </div>
          <?php if ($loyalty): ?>
          <div class="ms-auto text-center d-none d-md-block">
            <div class="text-orange fw-bold fs-4"><?= number_format($loyalty['total_points'] - $loyalty['used_points']) ?></div>
            <div class="text-muted" style="font-size:12px">Shopee Coins</div>
            <span class="badge mt-1 <?= ['bronze'=>'bg-secondary','silver'=>'bg-secondary text-dark','gold'=>'bg-warning text-dark','platinum'=>'bg-info','diamond'=>'bg-primary'][$loyalty['tier']]??'bg-secondary' ?>">
              <?= ucfirst($loyalty['tier']) ?>
            </span>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Profile Form -->
      <div class="surface">
        <ul class="nav nav-tabs mb-4">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profileTab">ข้อมูลส่วนตัว</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#passwordTab">เปลี่ยนรหัสผ่าน</button></li>
        </ul>
        <div class="tab-content">
          <!-- Profile Tab -->
          <div class="tab-pane fade show active" id="profileTab">
            <form method="POST">
              <input type="hidden" name="action" value="profile">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">ชื่อ-นามสกุล</label>
                  <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Username</label>
                  <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled>
                  <div class="form-text text-muted">ไม่สามารถเปลี่ยน Username ได้</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">เบอร์โทรศัพท์</label>
                  <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="0xxxxxxxxx">
                </div>
                <div class="col-md-6">
                  <label class="form-label">เพศ</label>
                  <select name="gender" class="form-select">
                    <option value="">ไม่ระบุ</option>
                    <option value="male" <?= ($user['gender']??'')==='male'?'selected':'' ?>>ชาย</option>
                    <option value="female" <?= ($user['gender']??'')==='female'?'selected':'' ?>>หญิง</option>
                    <option value="other" <?= ($user['gender']??'')==='other'?'selected':'' ?>>อื่นๆ</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">วันเกิด</label>
                  <input type="date" name="birth_date" class="form-control" value="<?= e($user['birth_date'] ?? '') ?>">
                </div>
                <div class="col-12">
                  <label class="form-label">เกี่ยวกับฉัน</label>
                  <textarea name="bio" class="form-control" rows="3" placeholder="บอกเล่าเกี่ยวกับตัวคุณ..."><?= e($user['bio'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-orange px-5">บันทึกการเปลี่ยนแปลง</button>
                </div>
              </div>
            </form>
          </div>
          <!-- Password Tab -->
          <div class="tab-pane fade" id="passwordTab">
            <form method="POST" style="max-width:440px">
              <input type="hidden" name="action" value="password">
              <div class="mb-3">
                <label class="form-label">รหัสผ่านเดิม</label>
                <input type="password" name="old_password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">รหัสผ่านใหม่</label>
                <input type="password" name="new_password" class="form-control" required minlength="6">
                <div class="form-text">อย่างน้อย 6 ตัวอักษร</div>
              </div>
              <div class="mb-4">
                <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="confirm_new" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-orange px-5">เปลี่ยนรหัสผ่าน</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
