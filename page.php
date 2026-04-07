<?php
define('FRONT_INCLUDED', true);
require_once __DIR__ . '/includes/functions_front.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: /webshop/'); exit; }

$stmt = getDB()->prepare("SELECT * FROM cms_pages WHERE slug=? AND is_active=1 AND (published_at IS NULL OR published_at <= NOW())");
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page) { http_response_code(404); $pageTitle='ไม่พบหน้า'; include __DIR__.'/includes/header.php'; echo '<div class="container py-5 text-center"><h3>ไม่พบหน้านี้</h3></div>'; include __DIR__.'/includes/footer.php'; exit; }

$pageTitle = $page['title'];
include __DIR__ . '/includes/header.php';
?>

<div class="site-breadcrumb">
  <div class="container-xl">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/webshop/">หน้าแรก</a></li>
      <li class="breadcrumb-item active"><?= e($page['title']) ?></li>
    </ol>
  </div>
</div>

<div class="container-xl py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="surface">
        <h2 class="fw-bold mb-4"><?= e($page['title']) ?></h2>
        <div class="cms-content">
          <?= $page['content'] ?>
        </div>
        <div class="text-muted mt-4 pt-3 border-top" style="font-size:12px">
          <i class="bi bi-clock me-1"></i>อัปเดตล่าสุด: <?= formatDate($page['updated_at'],'d M Y H:i') ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
