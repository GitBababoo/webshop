<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';
$pageTitle = '403 – ไม่มีสิทธิ์เข้าถึง';
?><!DOCTYPE html><html lang="th"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>403 – ไม่มีสิทธิ์</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
<div class="text-center p-5">
  <div class="display-1 text-danger mb-3"><i class="bi bi-shield-x"></i></div>
  <h2 class="fw-bold mb-2">403 – ไม่มีสิทธิ์เข้าถึง</h2>
  <p class="text-muted mb-4">คุณไม่มีสิทธิ์เข้าถึงหน้านี้<br>กรุณาติดต่อผู้ดูแลระบบ</p>
  <a href="<?= ADMIN_URL ?>" class="btn btn-danger"><i class="bi bi-house me-2"></i>กลับหน้าหลัก</a>
</div>
</body></html>
