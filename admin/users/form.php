<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$id   = (int)($_GET['id'] ?? 0);
$user_row = null;
$errors   = [];

if ($id) {
    $user_row = $db->prepare("SELECT * FROM users WHERE user_id=?")->execute([$id]) ? $db->prepare("SELECT * FROM users WHERE user_id=?")->execute([$id]) : null;
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->execute([$id]);
    $user_row = $stmt->fetch();
    if (!$user_row) { flash('danger','ไม่พบผู้ใช้'); header('Location: index.php'); exit; }
}

$pageTitle  = $id ? 'แก้ไขผู้ใช้: ' . e($user_row['username']) : 'เพิ่มผู้ใช้ใหม่';
$breadcrumb = ['ผู้ใช้งาน' => 'index.php', ($id ? 'แก้ไข' : 'เพิ่ม') => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) { flash('danger','CSRF error'); header('Location: index.php'); exit; }
    $data = [
        'username'  => trim($_POST['username'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'phone'     => trim($_POST['phone'] ?? ''),
        'full_name' => trim($_POST['full_name'] ?? ''),
        'gender'    => $_POST['gender'] ?? null,
        'birth_date'=> $_POST['birth_date'] ?? null,
        'role'      => $_POST['role'] ?? 'buyer',
        'is_active' => (int)($_POST['is_active'] ?? 1),
        'is_verified'=> (int)($_POST['is_verified'] ?? 0),
        'password'  => $_POST['password'] ?? '',
    ];
    if (empty($data['username'])) $errors[] = 'กรุณากรอกชื่อผู้ใช้';
    if (empty($data['email']))    $errors[] = 'กรุณากรอกอีเมล';
    if (!$id && empty($data['password'])) $errors[] = 'กรุณากรอกรหัสผ่าน';
    if (!isSuperAdmin() && in_array($data['role'], ['admin','superadmin'])) $errors[] = 'ไม่มีสิทธิ์กำหนด role นี้';
    if (empty($errors)) {
        if ($id) {
            $sql = "UPDATE users SET username=?,email=?,phone=?,full_name=?,gender=?,birth_date=?,role=?,is_active=?,is_verified=? WHERE user_id=?";
            $params = [$data['username'],$data['email'],$data['phone']?:null,$data['full_name'],$data['gender']?:null,$data['birth_date']?:null,$data['role'],$data['is_active'],$data['is_verified'],$id];
            if ($data['password']) { $sql = "UPDATE users SET username=?,email=?,phone=?,full_name=?,gender=?,birth_date=?,role=?,is_active=?,is_verified=?,password_hash=? WHERE user_id=?"; $params = [$data['username'],$data['email'],$data['phone']?:null,$data['full_name'],$data['gender']?:null,$data['birth_date']?:null,$data['role'],$data['is_active'],$data['is_verified'],password_hash($data['password'],PASSWORD_BCRYPT),$id]; }
            $db->prepare($sql)->execute($params);
            logActivity('edit','users','user',$id,'แก้ไขผู้ใช้ '.$data['username']);
            flash('success','บันทึกข้อมูลเรียบร้อย');
        } else {
            $db->prepare("INSERT INTO users (username,email,phone,full_name,gender,birth_date,role,is_active,is_verified,password_hash) VALUES (?,?,?,?,?,?,?,?,?,?)")
               ->execute([$data['username'],$data['email'],$data['phone']?:null,$data['full_name'],$data['gender']?:null,$data['birth_date']?:null,$data['role'],$data['is_active'],$data['is_verified'],password_hash($data['password'],PASSWORD_BCRYPT)]);
            logActivity('create','users','user',(int)$db->lastInsertId(),'สร้างผู้ใช้ '.$data['username']);
            flash('success','เพิ่มผู้ใช้เรียบร้อย');
        }
        header('Location: index.php'); exit;
    }
    $user_row = array_merge($user_row ?? [], $data);
}

include dirname(__DIR__) . '/includes/header.php';
$d = $user_row ?? [];
?>
<div class="row justify-content-center">
<div class="col-lg-8">
<form method="POST" enctype="multipart/form-data">
  <?= csrfField() ?>
  <div class="card mb-3">
    <div class="card-header"><i class="bi bi-person me-2"></i>ข้อมูลทั่วไป</div>
    <div class="card-body">
      <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul></div>
      <?php endif; ?>
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">ชื่อผู้ใช้ *</label>
          <input type="text" class="form-control" name="username" value="<?= e($d['username'] ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">ชื่อ-นามสกุล</label>
          <input type="text" class="form-control" name="full_name" value="<?= e($d['full_name'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">อีเมล *</label>
          <input type="email" class="form-control" name="email" value="<?= e($d['email'] ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">เบอร์โทรศัพท์</label>
          <input type="text" class="form-control" name="phone" value="<?= e($d['phone'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">เพศ</label>
          <select class="form-select" name="gender">
            <option value="">-- ไม่ระบุ --</option>
            <option value="male" <?= ($d['gender']??'')==='male'?'selected':'' ?>>ชาย</option>
            <option value="female" <?= ($d['gender']??'')==='female'?'selected':'' ?>>หญิง</option>
            <option value="other" <?= ($d['gender']??'')==='other'?'selected':'' ?>>อื่นๆ</option>
          </select></div>
        <div class="col-md-4"><label class="form-label">วันเกิด</label>
          <input type="date" class="form-control" name="birth_date" value="<?= e($d['birth_date'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">บทบาท</label>
          <select class="form-select" name="role">
            <?php foreach (ROLES as $rk => $rl): if (!isSuperAdmin() && in_array($rk,['admin','superadmin'])) continue; ?>
            <option value="<?= $rk ?>" <?= ($d['role']??'buyer')===$rk?'selected':'' ?>><?= $rl ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="col-md-6"><label class="form-label"><?= $id ? 'รหัสผ่านใหม่ (เว้นว่างไว้ถ้าไม่เปลี่ยน)' : 'รหัสผ่าน *' ?></label>
          <input type="password" class="form-control" name="password" <?= !$id ? 'required' : '' ?> placeholder="••••••••"></div>
        <div class="col-md-3 d-flex align-items-end"><div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= ($d['is_active']??1)?'checked':'' ?> id="chkActive">
          <label class="form-check-label" for="chkActive">เปิดใช้งาน</label>
        </div></div>
        <div class="col-md-3 d-flex align-items-end"><div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_verified" value="1" <?= ($d['is_verified']??0)?'checked':'' ?> id="chkVerified">
          <label class="form-check-label" for="chkVerified">ยืนยันแล้ว</label>
        </div></div>
      </div>
    </div>
  </div>
  <div class="d-flex gap-2 justify-content-end">
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>ยกเลิก</a>
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
  </div>
</form>
</div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
