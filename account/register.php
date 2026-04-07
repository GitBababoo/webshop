<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
if (frontIsLoggedIn()) { header('Location: /webshop/'); exit; }

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username'  => trim($_POST['username'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'phone'     => trim($_POST['phone'] ?? ''),
        'full_name' => trim($_POST['full_name'] ?? ''),
        'password'  => $_POST['password'] ?? '',
    ];
    $confirm = $_POST['confirm_password'] ?? '';
    if (strlen($data['username']) < 3)          $error = 'Username ต้องมีอย่างน้อย 3 ตัวอักษร';
    elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $error = 'รูปแบบ Email ไม่ถูกต้อง';
    elseif (strlen($data['password']) < 6)      $error = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    elseif ($data['password'] !== $confirm)     $error = 'รหัสผ่านไม่ตรงกัน';
    elseif (!isset($_POST['terms']))             $error = 'กรุณายอมรับข้อกำหนดการใช้งาน';
    else {
        $result = frontRegister($data);
        if ($result['success']) {
            $success = 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ';
        } else {
            $error = $result['message'];
        }
    }
}
$pageTitle = 'สมัครสมาชิก';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background:linear-gradient(135deg,#f53d2d 0%,#f63 50%,#ffd200 100%)">
  <div class="container">
    <div class="row justify-content-center g-4 align-items-center">
      <div class="col-lg-5 d-none d-lg-block text-white">
        <h1 class="display-5 fw-bold mb-3">สมัครสมาชิก</h1>
        <p class="fs-5 opacity-90">เข้าร่วมกับผู้ใช้งานกว่า <strong>10 ล้านคน</strong><br>และเริ่มต้นช้อปปิ้งได้เลย!</p>
        <ul class="list-unstyled mt-4 opacity-90" style="font-size:16px">
          <li class="mb-2"><i class="bi bi-check-circle-fill me-2"></i>สมัครฟรี ไม่มีค่าธรรมเนียม</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill me-2"></i>รับเหรียญต้อนรับ 50 Coins</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill me-2"></i>ส่วนลดพิเศษสำหรับสมาชิกใหม่</li>
        </ul>
      </div>
      <div class="col-md-9 col-lg-5">
        <div class="auth-card">
          <h2 class="text-orange">สมัครสมาชิกฟรี</h2>

          <?php if ($error): ?>
          <div class="alert alert-danger py-2 mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?></div>
          <?php endif; ?>
          <?php if ($success): ?>
          <div class="alert alert-success py-2 mb-3">
            <i class="bi bi-check-circle me-2"></i><?= e($success) ?>
            <a href="/webshop/account/login.php" class="alert-link ms-2">เข้าสู่ระบบ →</a>
          </div>
          <?php endif; ?>

          <?php if (!$success): ?>
          <form method="POST">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">ชื่อ-นามสกุล</label>
                <input type="text" name="full_name" class="form-control" placeholder="ชื่อ นามสกุล" value="<?= e($_POST['full_name'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-at"></i></span>
                  <input type="text" name="username" class="form-control" placeholder="username" value="<?= e($_POST['username'] ?? '') ?>" required minlength="3">
                </div>
                <div class="form-text">ตัวอักษร ตัวเลข ขีดล่าง อย่างน้อย 3 ตัว</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com" value="<?= e($_POST['email'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input type="tel" name="phone" class="form-control" placeholder="0xxxxxxxxx" value="<?= e($_POST['phone'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="อย่างน้อย 6 ตัวอักษร" required minlength="6">
              </div>
              <div class="col-md-6">
                <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" placeholder="ใส่รหัสผ่านอีกครั้ง" required>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input type="checkbox" name="terms" id="terms" class="form-check-input" required>
                  <label for="terms" class="form-check-label" style="font-size:13px">
                    ฉันยอมรับ <a href="/webshop/page.php?slug=policies" class="text-orange">ข้อกำหนด</a> และ <a href="/webshop/page.php?slug=privacy" class="text-orange">นโยบายความเป็นส่วนตัว</a>
                  </label>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-orange w-100 py-2 fw-bold">สมัครสมาชิกฟรี</button>
              </div>
            </div>
          </form>
          <?php endif; ?>

          <p class="text-center mt-3 mb-0" style="font-size:13px">
            มีบัญชีแล้ว? <a href="/webshop/account/login.php" class="text-orange fw-semibold">เข้าสู่ระบบ</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
