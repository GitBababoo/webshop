<?php
require_once dirname(__DIR__) . '/includes/functions.php';
// Ideally, check for admin login here
// dashboardRequireAdmin();

$logFile = dirname(__DIR__) . '/storage/logs/error.log';
$logs    = [];
if (file_exists($logFile)) {
    $lines = file($logFile);
    $logs  = array_slice(array_reverse($lines), 0, 100);
}

$pageTitle = 'รายงานข้อผิดพลาด (Error Logs)';
include dirname(__DIR__) . '/admin/includes/header.php';
?>

<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">ระบบเฝ้าระวังข้อผิดพลาด (Real-time Monitoring)</h4>
    <form method="POST" action="">
      <input type="hidden" name="clear_logs" value="1">
      <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยืนยันการล้างประวัติ?')">
        <i class="bi bi-trash me-1"></i>ล้างประวัติ
      </button>
    </form>
  </div>

  <div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>ระบบจะจดบันทึกทุกครั้งที่มี PHP Error, Warning หรือ Exception เกิดขึ้นในเว็บไซต์โดยอัตโนมัติ
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px">
          <thead class="table-light">
            <tr>
              <th style="width:180px">เวลา</th>
              <th style="width:100px">ระดับ</th>
              <th>ข้อความ / รายละเอียด</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($logs)): ?>
            <tr><td colspan="3" class="text-center py-5 text-muted">ยังไม่มีบันทึกข้อผิดพลาด (ระบบเสถียรดีมาก!)</td></tr>
            <?php else: ?>
              <?php foreach ($logs as $line): ?>
                <?php
                preg_match('/^\[(.*?)\] \[(.*?)\] (.*)$/', $line, $m);
                $time = $m[1] ?? 'N/A';
                $lv   = $m[2] ?? 'INFO';
                $msg  = $m[3] ?? $line;
                $color = $lv === 'CRITICAL' ? 'text-danger' : ($lv === 'ERROR' ? 'text-warning' : 'text-primary');
                ?>
                <tr>
                  <td class="text-muted"><?= e($time) ?></td>
                  <td><span class="badge <?= $lv === 'CRITICAL' ? 'bg-danger' : ($lv === 'ERROR' ? 'bg-warning text-dark' : 'bg-info') ?>"><?= e($lv) ?></span></td>
                  <td class="<?= $color ?> fw-medium"><?= e($msg) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php 
if (isset($_POST['clear_logs']) && file_exists($logFile)) {
    @file_put_contents($logFile, '');
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
include dirname(__DIR__) . '/admin/includes/footer.php'; ?>
