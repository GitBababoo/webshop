<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
frontRequireLogin('/webshop/account/wishlist.php');

$userId = (int)$_SESSION['front_user_id'];
$page   = max(1,(int)($_GET['page']??1));
$perPage = 20;
$offset  = ($page-1)*$perPage;

$cntStmt = getDB()->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id=?");
$cntStmt->execute([$userId]);
$total = (int)$cntStmt->fetchColumn();

$stmt = getDB()->prepare("
    SELECT p.product_id,p.name,p.slug,p.base_price,p.discount_price,p.rating,p.total_reviews,p.total_sold,p.total_stock,p.status,
           pi.image_url, s.shop_name,s.shop_slug,w.added_at AS wishlisted_at
    FROM wishlists w
    JOIN products p ON w.product_id=p.product_id
    JOIN shops s ON p.shop_id=s.shop_id
    LEFT JOIN product_images pi ON pi.product_id=p.product_id AND pi.is_primary=1
    WHERE w.user_id=?
    ORDER BY w.added_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll();
$wishlistIds = array_column($items, 'product_id');

$pageTitle = 'สินค้าที่ถูกใจ';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container-xl py-3">
  <div class="row g-3">
    <div class="col-md-3">
      <?php include __DIR__ . '/includes/account_sidebar.php'; ?>
    </div>
    <div class="col-md-9">
      <div class="surface mb-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-heart-fill me-2 text-orange"></i>สินค้าที่ถูกใจ (<?= number_format($total) ?>)</h5>
        <?php if (!empty($items)): ?>
        <button class="btn btn-sm btn-outline-orange" onclick="if(confirm('เพิ่มทุกรายการลงตะกร้า?')){/* TODO */}">
          <i class="bi bi-cart-plus me-1"></i>เพิ่มทั้งหมดลงตะกร้า
        </button>
        <?php endif; ?>
      </div>

      <?php if (empty($items)): ?>
      <div class="empty-state surface">
        <div class="empty-icon"><i class="bi bi-heart"></i></div>
        <h5>ยังไม่มีสินค้าที่ถูกใจ</h5>
        <p class="text-muted">กดหัวใจที่สินค้าเพื่อเพิ่มใน Wishlist</p>
        <a href="/webshop/" class="btn btn-orange">เริ่มเลือกสินค้า</a>
      </div>
      <?php else: ?>
      <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 g-2">
        <?php foreach ($items as $p): ?>
        <div class="col">
          <div class="product-card h-100 position-relative">
            <a href="/webshop/product.php?slug=<?= e($p['slug']) ?>" class="d-block">
              <div class="img-wrap">
                <img src="<?= e($p['image_url'] ?: 'https://via.placeholder.com/300') ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                <?php if ($p['status'] !== 'active' || $p['total_stock'] == 0): ?>
                <div class="position-absolute inset-0 d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,.7);top:0;left:0;right:0;bottom:0">
                  <span class="badge bg-secondary">สินค้าหมด</span>
                </div>
                <?php endif; ?>
              </div>
            </a>
            <button class="wishlist-btn active" onclick="toggleWishlist(<?= $p['product_id'] ?>,this)" title="ลบออกจาก Wishlist">
              <i class="bi bi-heart-fill"></i>
            </button>
            <div class="card-body">
              <a href="/webshop/product.php?slug=<?= e($p['slug']) ?>" class="product-name d-block text-dark"><?= e($p['name']) ?></a>
              <div class="text-muted mb-1" style="font-size:11px">
                <a href="/webshop/shop.php?slug=<?= e($p['shop_slug']) ?>" class="text-muted"><?= e($p['shop_name']) ?></a>
              </div>
              <div class="price-wrap">
                <span class="price">฿<?= number_format((float)($p['discount_price']?:$p['base_price']),0) ?></span>
                <?php if ($p['discount_price']): ?><span class="price-original">฿<?= number_format((float)$p['base_price'],0) ?></span><?php endif; ?>
              </div>
              <div class="rating-row">
                <span class="stars"><?= str_repeat('★',round($p['rating'])).str_repeat('☆',5-round($p['rating'])) ?></span>
                <span>(<?= $p['total_reviews'] ?>)</span>
                <span class="text-muted ms-auto" style="font-size:11px"><?= formatDate($p['wishlisted_at'],'d/m/Y') ?></span>
              </div>
              <?php if ($p['status']==='active' && $p['total_stock']>0): ?>
              <button class="add-cart-btn" onclick="addToCart(<?= $p['product_id'] ?>)">
                <i class="bi bi-cart-plus me-1"></i>ใส่ตะกร้า
              </button>
              <?php else: ?>
              <button class="add-cart-btn" disabled style="opacity:.5;cursor:not-allowed">สินค้าหมด</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <?php if (ceil($total/$perPage) > 1): ?>
      <nav class="mt-4 d-flex justify-content-center">
        <ul class="pagination">
          <?php for ($i=max(1,$page-2);$i<=min(ceil($total/$perPage),$page+4);$i++): ?>
          <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
          <?php endfor; ?>
        </ul>
      </nav>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
