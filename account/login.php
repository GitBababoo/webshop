<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
if (frontIsLoggedIn()) { header('Location: /webshop/account/profile.php'); exit; }

$redirect = trim($_GET['redirect'] ?? '/webshop/');
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = frontLogin(trim($_POST['username'] ?? ''), $_POST['password'] ?? '');
    if ($result['success']) {
        header('Location: ' . $redirect); exit;
    }
    $error = $result['message'];
}
$pageTitle = 'เข้าสู่ระบบ';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background:linear-gradient(135deg,#f53d2d 0%,#f63 50%,#ffd200 100%)">
  <div class="container">
    <div class="row justify-content-center g-4 align-items-center">
      <!-- Brand side -->
      <div class="col-lg-5 d-none d-lg-block text-white">
        <h1 class="display-5 fw-bold mb-3">Shopee TH</h1>
        <p class="fs-5 opacity-90 mb-0">ช้อปทุกอย่าง ง่ายทุกที่<br>ราคาถูก ส่งไว ปลอดภัย</p>
      </div>
      <!-- Form -->
      <div class="col-md-8 col-lg-4">
        <div class="auth-card">
          <h2 class="text-orange">เข้าสู่ระบบ</h2>

          <?php if ($error): ?>
          <div class="alert alert-danger py-2 mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?></div>
          <?php endif; ?>

          <form method="POST">
            <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
            <div class="mb-3">
              <label class="form-label">ชื่อผู้ใช้ / Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Username หรือ Email"
                       value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">รหัสผ่าน</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="รหัสผ่าน" required>
                <button type="button" class="input-group-text bg-white border-start-0" onclick="const i=document.getElementById('passwordInput');i.type=i.type==='password'?'text':'password'">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
            <div class="d-flex justify-content-between mb-3">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember" style="font-size:13px">จำฉันไว้</label>
              </div>
              <a href="#" class="text-orange" style="font-size:13px">ลืมรหัสผ่าน?</a>
            </div>
            <button type="submit" class="btn btn-orange w-100 py-2 fw-bold mb-3">เข้าสู่ระบบ</button>
          </form>

          <div class="auth-divider">หรือ</div>

          <div class="d-flex gap-2 mb-3">
            <button class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center gap-2" style="font-size:13px">
              <i class="bi bi-google text-danger"></i>Google
            </button>
            <button class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center gap-2" style="font-size:13px">
              <i class="bi bi-facebook text-primary"></i>Facebook
            </button>
          </div>

          <p class="text-center mb-0" style="font-size:13px">
            ยังไม่มีบัญชี?
            <a href="/webshop/account/register.php" class="text-orange fw-semibold">สมัครสมาชิกฟรี</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
