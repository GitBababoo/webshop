<?php
$pageTitle  = 'จัดการสินค้า';
$breadcrumb = ['สินค้า' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db  = getDB();
$q   = trim($_GET['q'] ?? '');
$cat = (int)($_GET['cat'] ?? 0);
$st  = $_GET['status'] ?? '';
$page= max(1,(int)($_GET['page'] ?? 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $id  = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($id && $act === 'ban')     $db->prepare("UPDATE products SET status='banned' WHERE product_id=?")->execute([$id]);
    if ($id && $act === 'activate')$db->prepare("UPDATE products SET status='active' WHERE product_id=?")->execute([$id]);
    if ($id && $act === 'delete' && isSuperAdmin()) $db->prepare("DELETE FROM products WHERE product_id=?")->execute([$id]);
    if ($id && $act === 'feature') $db->prepare("UPDATE products SET is_featured=NOT is_featured WHERE product_id=?")->execute([$id]);
    logActivity($act,'products','product',$id);
    flash('success','ดำเนินการสำเร็จ');
    header('Location: index.php'); exit;
}

$where = 'WHERE 1=1'; $params = [];
if ($q)  { $where .= ' AND (p.name LIKE ? OR p.sku LIKE ? OR p.brand LIKE ?)'; $params=array_merge($params,["%$q%","%$q%","%$q%"]); }
if ($cat) { $where .= ' AND p.category_id=?'; $params[]=$cat; }
if ($st)  { $where .= ' AND p.status=?'; $params[]=$st; }

$result = paginateQuery($db,
    "SELECT COUNT(*) FROM products p $where",
    "SELECT p.*, c.name AS cat_name, s.shop_name, pi.image_url AS thumb FROM products p
     LEFT JOIN categories c ON p.category_id=c.category_id
     LEFT JOIN shops s ON p.shop_id=s.shop_id
     LEFT JOIN product_images pi ON p.product_id=pi.product_id AND pi.is_primary=1
     $where ORDER BY p.created_at DESC",
    $params, $page, 25);

$catList = $db->query("SELECT category_id,name FROM categories WHERE is_active=1 ORDER BY name")->fetchAll();
include dirname(__DIR__) . '/includes/header.php';
$statusOptions = ['active'=>'เปิดขาย','draft'=>'ร่าง','inactive'=>'ปิดขาย','banned'=>'ระงับ'];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <?php foreach (array_merge([''=>'ทั้งหมด'],$statusOptions) as $v=>$l): ?>
      <a href="?status=<?=$v?>&q=<?=urlencode($q)?>&cat=<?=$cat?>" class="btn btn-sm <?=$st===$v?'btn-primary':'btn-outline-secondary'?>"><?=$l?></a>
    <?php endforeach; ?>
  </div>
</div>
<div class="card">
  <div class="card-header">
    <form class="row g-2" method="GET">
      <div class="col-auto"><input type="text" class="form-control form-control-sm" name="q" placeholder="ค้นหาสินค้า..." value="<?=e($q)?>" style="width:220px"></div>
      <div class="col-auto">
        <select class="form-select form-select-sm" name="cat" style="width:180px">
          <option value="">-- ทุกหมวดหมู่ --</option>
          <?php foreach ($catList as $c): ?>
          <option value="<?=$c['category_id']?>" <?=$cat==$c['category_id']?'selected':''?>><?=e($c['name'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <input type="hidden" name="status" value="<?=e($st)?>">
      <div class="col-auto"><button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button></div>
      <?php if ($q||$cat): ?><div class="col-auto"><a href="?status=<?=e($st)?>" class="btn btn-sm btn-outline-secondary">ล้าง</a></div><?php endif; ?>
      <div class="col-auto ms-auto align-self-center"><span class="text-muted small">ทั้งหมด <?=number_format($result['total'])?> สินค้า</span></div>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>สินค้า</th><th>ร้านค้า</th><th>หมวดหมู่</th><th>ราคา</th><th>สต็อก</th><th>ขาย</th><th>คะแนน</th><th>สถานะ</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($result['data'] as $p):
        $stclr = ['active'=>'success','draft'=>'secondary','inactive'=>'warning','banned'=>'danger'];
        $stlbl = $statusOptions[$p['status']] ?? $p['status'];
      ?>
      <tr>
        <td>
          <div class="d-flex align-items-center gap-2">
            <?php if ($p['thumb']): ?><img src="<?=e($p['thumb'])?>" class="product-thumb" alt=""><?php else: ?>
            <div class="product-thumb bg-light d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div><?php endif; ?>
            <div style="max-width:200px">
              <div class="fw-semibold small text-truncate"><?=e($p['name'])?></div>
              <div class="text-muted" style="font-size:.72rem"><?=$p['sku']?'SKU: '.e($p['sku']):''?> <?=$p['brand']?'· '.e($p['brand']):''?></div>
              <?php if ($p['is_featured']): ?><span class="badge bg-warning text-dark" style="font-size:.65rem">Featured</span><?php endif; ?>
            </div>
          </div>
        </td>
        <td class="small text-muted"><?=e($p['shop_name'])?></td>
        <td class="small text-muted"><?=e($p['cat_name']??'-')?></td>
        <td class="small fw-semibold">
          <?php if ($p['discount_price']): ?>
            <span class="text-danger"><?=formatPrice((float)$p['discount_price'])?></span><br>
            <span class="text-muted text-decoration-line-through" style="font-size:.72rem"><?=formatPrice((float)$p['base_price'])?></span>
          <?php else: ?>
            <?=formatPrice((float)$p['base_price'])?>
          <?php endif; ?>
        </td>
        <td class="small <?=$p['total_stock']<5?'text-danger fw-bold':''?>"><?=number_format((int)$p['total_stock'])?></td>
        <td class="small"><?=number_format((int)$p['total_sold'])?></td>
        <td class="small"><i class="bi bi-star-fill text-warning"></i> <?=number_format((float)$p['rating'],1)?></td>
        <td><span class="badge bg-<?=$stclr[$p['status']]??'secondary'?>"><?=$stlbl?></span></td>
        <td>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="id" value="<?=$p['product_id']?>">
            <div class="d-flex gap-1">
              <?php if ($p['status']==='active'): ?>
                <button type="submit" name="action" value="ban" class="btn btn-sm btn-outline-danger" title="ระงับ"><i class="bi bi-slash-circle"></i></button>
              <?php else: ?>
                <button type="submit" name="action" value="activate" class="btn btn-sm btn-outline-success" title="เปิดขาย"><i class="bi bi-check-circle"></i></button>
              <?php endif; ?>
              <button type="submit" name="action" value="feature" class="btn btn-sm btn-outline-warning" title="<?=$p['is_featured']?'ยกเลิก Featured':'ตั้ง Featured'?>"><i class="bi bi-star<?=$p['is_featured']?'-fill':''?>"></i></button>
              <?php if (isSuperAdmin()): ?>
              <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบสินค้านี้?')" title="ลบ"><i class="bi bi-trash"></i></button>
              <?php endif; ?>
            </div>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?=paginator($result,'index.php?status='.urlencode($st).'&q='.urlencode($q).'&cat='.$cat)?></div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
