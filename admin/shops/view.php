<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$s = $db->prepare("SELECT s.*, u.username, u.email, u.full_name, u.phone FROM shops s JOIN users u ON s.owner_user_id=u.user_id WHERE s.shop_id=?");
$s->execute([$id]);
$shop = $s->fetch();
if (!$shop) { flash('danger','ไม่พบร้านค้า'); header('Location: index.php'); exit; }

$pageTitle  = 'ร้านค้า: ' . e($shop['shop_name']);
$breadcrumb = ['ร้านค้า' => 'index.php', $shop['shop_name'] => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $act = $_POST['action'] ?? '';
    if ($act === 'verify')  { $db->prepare("UPDATE shops SET is_verified=1 WHERE shop_id=?")->execute([$id]); flash('success','ยืนยันร้านค้าเรียบร้อย'); }
    if ($act === 'ban')     { $db->prepare("UPDATE shops SET is_active=0 WHERE shop_id=?")->execute([$id]); flash('warning','ระงับร้านค้าเรียบร้อย'); }
    if ($act === 'unban')   { $db->prepare("UPDATE shops SET is_active=1 WHERE shop_id=?")->execute([$id]); flash('success','เปิดร้านค้าเรียบร้อย'); }
    logActivity($act,'shops','shop',$id);
    header("Location: view.php?id=$id"); exit;
}

$products = $db->prepare("SELECT p.*, pi.image_url AS thumb FROM products p LEFT JOIN product_images pi ON p.product_id=pi.product_id AND pi.is_primary=1 WHERE p.shop_id=? ORDER BY p.created_at DESC LIMIT 12");
$products->execute([$id]);
$products = $products->fetchAll();

$orders = $db->prepare("SELECT COUNT(*) FROM orders WHERE shop_id=?"); $orders->execute([$id]); $orderCount = (int)$orders->fetchColumn();
$revenue = $db->prepare("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE shop_id=? AND payment_status='paid'"); $revenue->execute([$id]); $totalRevenue = (float)$revenue->fetchColumn();

$reviews = $db->prepare("SELECT r.rating, r.comment, u.username, r.created_at FROM reviews r JOIN users u ON r.reviewer_id=u.user_id WHERE r.shop_id=? ORDER BY r.created_at DESC LIMIT 5");
$reviews->execute([$id]);
$reviews = $reviews->fetchAll();

include dirname(__DIR__) . '/includes/header.php';
$typeColors = ['individual'=>'info','mall'=>'warning','official'=>'danger'];
?>
<div class="row g-4">
  <!-- Shop Info Card -->
  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-body text-center py-4">
        <?php if ($shop['logo_url']): ?>
          <img src="<?=e($shop['logo_url'])?>" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover" alt="">
        <?php else: ?>
          <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px;font-size:2.5rem">
            <?=mb_strtoupper(mb_substr($shop['shop_name'],0,1))?>
          </div>
        <?php endif; ?>
        <h5 class="fw-bold mb-1"><?=e($shop['shop_name'])?>
          <?php if ($shop['is_verified']): ?><i class="bi bi-patch-check-fill text-primary ms-1"></i><?php endif; ?>
        </h5>
        <p class="text-muted small mb-2">@<?=e($shop['shop_slug'])?></p>
        <div class="d-flex justify-content-center gap-2 mb-3">
          <span class="badge bg-<?=$typeColors[$shop['shop_type']]??'secondary'?>"><?=ucfirst($shop['shop_type'])?></span>
          <?php if ($shop['is_active']): ?>
            <span class="badge bg-success">เปิดอยู่</span>
          <?php else: ?>
            <span class="badge bg-danger">ระงับ</span>
          <?php endif; ?>
        </div>
        <div class="row text-center g-0 border-top pt-3">
          <div class="col"><div class="fw-bold"><?=number_format((int)$shop['total_products'])?></div><div class="text-muted small">สินค้า</div></div>
          <div class="col border-start"><div class="fw-bold"><?=number_format((int)$shop['total_sales'])?></div><div class="text-muted small">ขายแล้ว</div></div>
          <div class="col border-start"><div class="fw-bold text-warning"><?=number_format((float)$shop['rating'],1)?></div><div class="text-muted small">คะแนน</div></div>
        </div>
      </div>
    </div>

    <!-- Owner Info -->
    <div class="card mb-3">
      <div class="card-header fw-semibold"><i class="bi bi-person me-2"></i>เจ้าของร้าน</div>
      <div class="card-body small">
        <p class="mb-1 fw-semibold"><?=e($shop['full_name']?:$shop['username'])?></p>
        <p class="mb-1 text-muted"><i class="bi bi-envelope me-1"></i><?=e($shop['email'])?></p>
        <p class="mb-0 text-muted"><i class="bi bi-telephone me-1"></i><?=e($shop['phone']??'-')?></p>
      </div>
    </div>

    <!-- Actions -->
    <div class="card mb-3">
      <div class="card-header fw-semibold"><i class="bi bi-gear me-2"></i>การดำเนินการ</div>
      <div class="card-body d-flex flex-column gap-2">
        <form method="POST">
          <?=csrfField()?>
          <?php if (!$shop['is_verified']): ?>
          <button type="submit" name="action" value="verify" class="btn btn-success w-100"><i class="bi bi-patch-check me-1"></i>ยืนยันร้านค้า</button>
          <?php endif; ?>
          <?php if ($shop['is_active']): ?>
          <button type="submit" name="action" value="ban" class="btn btn-outline-danger w-100"><i class="bi bi-slash-circle me-1"></i>ระงับร้านค้า</button>
          <?php else: ?>
          <button type="submit" name="action" value="unban" class="btn btn-outline-success w-100"><i class="bi bi-check-circle me-1"></i>เปิดร้านค้า</button>
          <?php endif; ?>
        </form>
        <a href="<?=ADMIN_URL?>/users/form.php?id=<?=$shop['owner_user_id']?>" class="btn btn-outline-primary w-100"><i class="bi bi-person-gear me-1"></i>แก้ไขเจ้าของร้าน</a>
      </div>
    </div>

    <!-- Stats -->
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-graph-up me-2"></i>สถิติ</div>
      <div class="list-group list-group-flush">
        <div class="list-group-item d-flex justify-content-between"><span class="small text-muted">ออเดอร์ทั้งหมด</span><strong><?=number_format($orderCount)?></strong></div>
        <div class="list-group-item d-flex justify-content-between"><span class="small text-muted">รายได้รวม</span><strong class="text-success"><?=formatPrice($totalRevenue)?></strong></div>
        <div class="list-group-item d-flex justify-content-between"><span class="small text-muted">รีวิวทั้งหมด</span><strong><?=number_format((int)$shop['total_reviews'])?></strong></div>
        <div class="list-group-item d-flex justify-content-between"><span class="small text-muted">วันที่เปิดร้าน</span><span class="small"><?=formatDate($shop['joined_at'],'d/m/Y')?></span></div>
      </div>
    </div>
  </div>

  <!-- Right Column -->
  <div class="col-lg-8">
    <!-- Description -->
    <?php if ($shop['description']): ?>
    <div class="card mb-3">
      <div class="card-header fw-semibold">เกี่ยวกับร้าน</div>
      <div class="card-body small text-muted"><?=nl2br(e($shop['description']))?></div>
    </div>
    <?php endif; ?>

    <!-- Products -->
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-box-seam me-2"></i>สินค้าในร้าน (<?=count($products)?>)</span>
        <a href="<?=ADMIN_URL?>/products/index.php?shop=<?=$id?>" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
      </div>
      <div class="card-body">
        <div class="row g-2">
          <?php foreach ($products as $p): ?>
          <div class="col-4 col-md-3">
            <div class="card h-100 border-0 shadow-sm">
              <?php if ($p['thumb']): ?><img src="<?=e($p['thumb'])?>" class="card-img-top" style="height:80px;object-fit:cover" alt=""></div><?php else: ?><div style="height:80px;background:#f0f0f0;display:flex;align-items:center;justify-content:center"><i class="bi bi-image text-muted"></i></div><?php endif; ?>
              <div class="card-body p-2">
                <div class="text-truncate small fw-semibold" style="font-size:.75rem"><?=e($p['name'])?></div>
                <div class="text-danger" style="font-size:.72rem"><?=formatPrice((float)($p['discount_price']?:$p['base_price']))?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Reviews -->
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-star me-2"></i>รีวิวล่าสุด</div>
      <ul class="list-group list-group-flush">
        <?php foreach ($reviews as $rev): ?>
        <li class="list-group-item">
          <div class="d-flex justify-content-between align-items-start">
            <span class="fw-semibold small"><?=e($rev['username'])?></span>
            <span class="text-warning"><?=str_repeat('★',(int)$rev['rating'])?><?=str_repeat('☆',5-(int)$rev['rating'])?></span>
          </div>
          <?php if ($rev['comment']): ?><p class="text-muted small mb-0"><?=e($rev['comment'])?></p><?php endif; ?>
          <div class="text-muted" style="font-size:.72rem"><?=formatDate($rev['created_at'],'d/m/Y')?></div>
        </li>
        <?php endforeach; ?>
        <?php if (empty($reviews)): ?><li class="list-group-item text-muted small text-center py-3">ยังไม่มีรีวิว</li><?php endif; ?>
      </ul>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
