<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once __DIR__ . '/db_guard.php';

function getSetting(string $key, string $default = ''): string {
    static $cache = [];
    if (!array_key_exists($key, $cache)) {
        try {
            $stmt = getDB()->prepare("SELECT setting_value FROM site_settings WHERE setting_key=?");
            $stmt->execute([$key]);
            $row = $stmt->fetch();
            $cache[$key] = $row ? (string)$row['setting_value'] : $default;
        } catch (Exception $e) { return $default; }
    }
    return $cache[$key];
}

/* ── Stability & Logging System ── */
function siteLog(string $message, string $level = 'INFO'): void {
    $logDir = dirname(__DIR__) . '/storage/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    $logFile = $logDir . '/error.log';
    $time    = date('Y-m-d H:i:s');
    $content = "[$time] [$level] $message" . PHP_EOL;
    @file_put_contents($logFile, $content, FILE_APPEND);
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) return false;
    $msg = "PHP Error: $errstr in $errfile on line $errline";
    siteLog($msg, 'ERROR');
    return false; // Let standard PHP error handler continue
});

set_exception_handler(function($e) {
    $msg = "Uncaught Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
    siteLog($msg, 'CRITICAL');
    echo "<div style='padding:20px;background:#fff5f5;border:1px solid #feb2b2;color:#c53030;border-radius:4px;margin:20px'>
            <h4 style='margin-top:0'>เกิดข้อผิดพลาดในการทำงาน</h4>
            <p style='margin-bottom:0'>ระบบได้บันทึกรายละเอียดไว้เพื่อตรวจสอบแล้ว กรุณาลองใหม่อีกครั้ง</p>
          </div>";
});

function getSettingsByGroup(string $group): array {
    $stmt = getDB()->prepare("SELECT * FROM site_settings WHERE setting_group=? ORDER BY sort_order");
    $stmt->execute([$group]);
    return $stmt->fetchAll();
}

function saveSetting(string $key, string $value): void {
    getDB()->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key=?")->execute([$value, $key]);
}

function paginateQuery(PDO $db, string $countSql, string $dataSql, array $params, int $page, int $perPage = 20): array {
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();
    $totalPages = max(1, (int)ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    $dataStmt = $db->prepare($dataSql . " LIMIT $perPage OFFSET $offset");
    $dataStmt->execute($params);
    return [
        'data'        => $dataStmt->fetchAll(),
        'total'       => $total,
        'per_page'    => $perPage,
        'current_page'=> $page,
        'total_pages' => $totalPages,
    ];
}

function paginator(array $pagination, string $url): string {
    if ($pagination['total_pages'] <= 1) return '';
    $html = '<nav><ul class="pagination pagination-sm mb-0">';
    $sep = str_contains($url, '?') ? '&' : '?';
    $p = $pagination['current_page'];
    $t = $pagination['total_pages'];
    $html .= '<li class="page-item ' . ($p<=1?'disabled':'') . '"><a class="page-link" href="'.$url.$sep.'page='.($p-1).'">«</a></li>';
    $start = max(1, $p-2); $end = min($t, $p+2);
    if ($start > 1) $html .= '<li class="page-item"><a class="page-link" href="'.$url.$sep.'page=1">1</a></li>' . ($start>2?'<li class="page-item disabled"><span class="page-link">…</span></li>':'');
    for ($i=$start; $i<=$end; $i++) $html .= '<li class="page-item '.($i==$p?'active':'').'"><a class="page-link" href="'.$url.$sep.'page='.$i.'">'.$i.'</a></li>';
    if ($end < $t) $html .= ($end<$t-1?'<li class="page-item disabled"><span class="page-link">…</span></li>':'') . '<li class="page-item"><a class="page-link" href="'.$url.$sep.'page='.$t.'">'.$t.'</a></li>';
    $html .= '<li class="page-item ' . ($p>=$t?'disabled':'') . '"><a class="page-link" href="'.$url.$sep.'page='.($p+1).'">»</a></li>';
    $html .= '</ul></nav>';
    return $html;
}

function uploadFile(array $file, string $subDir = ''): ?string {
    $allowed = ['jpg','jpeg','png','gif','webp','svg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed) || $file['error'] !== 0) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;
    $dir = UPLOAD_DIR . ltrim($subDir, '/');
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $path = $dir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $path)) return null;
    return UPLOAD_URL . ltrim($subDir, '/') . '/' . $filename;
}

function sanitize(string $str): string { return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8'); }
function e(string $str): string { return sanitize($str); }

function formatPrice(float $amount, string $symbol = '฿'): string {
    return $symbol . number_format($amount, 2, '.', ',');
}

function formatDate(string $datetime, string $format = 'd/m/Y H:i'): string {
    return $datetime ? date($format, strtotime($datetime)) : '-';
}

function timeAgo(string $datetime): string {
    if (!$datetime) return '-';
    $diff = time() - strtotime($datetime);
    if ($diff < 0)       return 'เมื่อกี้นี้';
    if ($diff < 60)      return $diff . ' วินาทีที่แล้ว';
    if ($diff < 3600)    return (int)($diff/60) . ' นาทีที่แล้ว';
    if ($diff < 86400)   return (int)($diff/3600) . ' ชั่วโมงที่แล้ว';
    if ($diff < 2592000) return (int)($diff/86400) . ' วันที่แล้ว';
    return formatDate($datetime, 'd/m/Y');
}

function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[\s\-]+/', '-', $text);
    $text = preg_replace('/[^\w\-ก-๙]/u', '', $text);
    return trim($text, '-');
}

function getAvatarUrl(?string $url, string $username = 'User'): string {
    if (empty($url)) {
        return 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=ee4d2d&color=fff&size=128';
    }
    if (str_starts_with($url, 'http')) return $url;
    // Prefix if not already prefixed
    if (!str_starts_with($url, '/webshop/')) {
        return '/webshop/' . ltrim($url, '/');
    }
    return $url;
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function renderFlash(): string {
    $f = getFlash();
    if (!$f) return '';
    $icons = ['success'=>'check-circle','danger'=>'x-circle','warning'=>'exclamation-triangle','info'=>'info-circle'];
    $icon = $icons[$f['type']] ?? 'info-circle';
    return '<div class="alert alert-'.$f['type'].' alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-'.$icon.'"></i><span>'.e($f['message']).'</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

function getStats(): array {
    $db = getDB();
    return [
        'total_users'    => $db->query("SELECT COUNT(*) FROM users WHERE role='buyer'")->fetchColumn(),
        'total_sellers'  => $db->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetchColumn(),
        'total_shops'    => $db->query("SELECT COUNT(*) FROM shops WHERE is_active=1")->fetchColumn(),
        'total_products' => $db->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn(),
        'total_orders'   => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        'pending_orders' => $db->query("SELECT COUNT(*) FROM orders WHERE order_status='pending'")->fetchColumn(),
        'today_orders'   => $db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()")->fetchColumn(),
        'today_revenue'  => $db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE DATE(created_at)=CURDATE() AND payment_status='paid'")->fetchColumn(),
        'month_revenue'  => $db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) AND payment_status='paid'")->fetchColumn(),
        'total_revenue'  => $db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE payment_status='paid'")->fetchColumn(),
        'pending_reviews'=> $db->query("SELECT COUNT(*) FROM reviews WHERE is_hidden=0")->fetchColumn(),
        'pending_returns'=> $db->query("SELECT COUNT(*) FROM return_requests WHERE status='pending'")->fetchColumn(),
    ];
}
