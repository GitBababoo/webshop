<?php
require_once dirname(__DIR__) . '/includes/auth.php';
if (isLoggedIn()) { header('Location: ' . ADMIN_URL . '/index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = login(trim($_POST['username'] ?? ''), $_POST['password'] ?? '');
    if ($result['success']) { header('Location: ' . ADMIN_URL . '/index.php'); exit; }
    $error = $result['message'];
}
$siteName = getSetting('site_name', 'Shopee TH');
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>เข้าสู่ระบบ – <?= e($siteName) ?> Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  body{background:linear-gradient(135deg,#EE4D2D 0%,#ff7337 50%,#ffb347 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .login-card{width:100%;max-width:420px;border-radius:1.5rem;border:none;box-shadow:0 20px 60px rgba(0,0,0,.2);}
  .login-header{background:linear-gradient(135deg,#EE4D2D,#ff7337);border-radius:1.5rem 1.5rem 0 0;padding:2rem;text-align:center;}
  .brand-icon{width:60px;height:60px;background:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.8rem;color:#EE4D2D;margin-bottom:.75rem;}
  .form-control:focus{border-color:#EE4D2D;box-shadow:0 0 0 .2rem rgba(238,77,45,.2);}
  .btn-login{background:#EE4D2D;border:none;padding:.75rem;font-weight:600;letter-spacing:.5px;}
  .btn-login:hover{background:#d43520;}
</style>
</head>
<body>
<div class="login-card card">
  <div class="login-header">
    <div class="brand-icon"><i class="bi bi-shop"></i></div>
    <h4 class="text-white mb-0 fw-bold"><?= e($siteName) ?></h4>
    <p class="text-white-50 mb-0 small">Admin Panel</p>
  </div>
  <div class="card-body p-4">
    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill"></i><?= e($error) ?>
      </div>
    <?php endif; ?>
    <form method="POST" autocomplete="off">
      <div class="mb-3">
        <label class="form-label fw-semibold">ชื่อผู้ใช้ / อีเมล</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" class="form-control" name="username" placeholder="superadmin" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">รหัสผ่าน</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" class="form-control" name="password" placeholder="••••••••" required>
          <button type="button" class="btn btn-outline-secondary" id="togglePwd"><i class="bi bi-eye"></i></button>
        </div>
      </div>
      <button type="submit" class="btn btn-login btn-danger w-100 text-white">
        <i class="bi bi-box-arrow-in-right me-2"></i>เข้าสู่ระบบ
      </button>
    </form>
    <p class="text-center text-muted small mt-3 mb-0">
      <i class="bi bi-shield-lock me-1"></i>ระบบสำหรับผู้ดูแลเท่านั้น
    </p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePwd').addEventListener('click',function(){
  const pw=document.querySelector('input[name=password]');
  const ic=this.querySelector('i');
  pw.type=pw.type==='password'?'text':'password';
  ic.className=pw.type==='password'?'bi bi-eye':'bi bi-eye-slash';
});
</script>
</body>
</html>
