<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db   = getDB();
$id   = (int)($_GET['id'] ?? 0);
$row  = null; $errors = [];

if ($id) {
    $s = $db->prepare("SELECT * FROM products WHERE product_id=?");
    $s->execute([$id]);
    $row = $s->fetch();
    if (!$row) { flash('danger','ไม่พบสินค้า'); header('Location: index.php'); exit; }
}

$pageTitle  = $id ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่';
$breadcrumb = ['สินค้า' => 'index.php', ($id ? 'แก้ไข' : 'เพิ่ม') => false];

$categories = $db->query("SELECT category_id,name,parent_id FROM categories WHERE is_active=1 ORDER BY parent_id IS NULL DESC,sort_order,name")->fetchAll();
$shops      = $db->query("SELECT shop_id,shop_name FROM shops WHERE is_active=1 ORDER BY shop_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) { flash('danger','CSRF'); header('Location: index.php'); exit; }
    $d = [
        'shop_id'       => (int)($_POST['shop_id'] ?? 0),
        'category_id'   => (int)($_POST['category_id'] ?? 0),
        'name'          => trim($_POST['name'] ?? ''),
        'slug'          => trim($_POST['slug'] ?? '') ?: slugify(trim($_POST['name'] ?? '')),
        'description'   => $_POST['description'] ?? '',
        'base_price'    => (float)($_POST['base_price'] ?? 0),
        'discount_price'=> ($_POST['discount_price'] !== '' ? (float)$_POST['discount_price'] : null),
        'brand'         => trim($_POST['brand'] ?? ''),
        'sku'           => trim($_POST['sku'] ?? ''),
        'weight_grams'  => ($_POST['weight_grams'] !== '' ? (int)$_POST['weight_grams'] : null),
        'condition_type'=> $_POST['condition_type'] ?? 'new',
        'status'        => $_POST['status'] ?? 'draft',
        'is_featured'   => (int)($_POST['is_featured'] ?? 0),
    ];
    if (!$d['name'])        $errors[] = 'กรุณากรอกชื่อสินค้า';
    if (!$d['shop_id'])     $errors[] = 'กรุณาเลือกร้านค้า';
    if (!$d['category_id']) $errors[] = 'กรุณาเลือกหมวดหมู่';
    if ($d['base_price'] <= 0) $errors[] = 'ราคาต้องมากกว่า 0';
    if (!$errors) {
        if ($id) {
            $db->prepare("UPDATE products SET shop_id=?,category_id=?,name=?,slug=?,description=?,base_price=?,discount_price=?,brand=?,sku=?,weight_grams=?,condition_type=?,status=?,is_featured=?,updated_at=NOW() WHERE product_id=?")
               ->execute([$d['shop_id'],$d['category_id'],$d['name'],$d['slug'],$d['description'],$d['base_price'],$d['discount_price'],$d['brand']?:null,$d['sku']?:null,$d['weight_grams'],$d['condition_type'],$d['status'],$d['is_featured'],$id]);
        } else {
            $db->prepare("INSERT INTO products (shop_id,category_id,name,slug,description,base_price,discount_price,brand,sku,weight_grams,condition_type,status,is_featured) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")
               ->execute([$d['shop_id'],$d['category_id'],$d['name'],$d['slug'],$d['description'],$d['base_price'],$d['discount_price'],$d['brand']?:null,$d['sku']?:null,$d['weight_grams'],$d['condition_type'],$d['status'],$d['is_featured']]);
            $id = (int)$db->lastInsertId();
        }
        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($_FILES['images']['error'][$i] === 0) {
                    $singleFile = ['name'=>$_FILES['images']['name'][$i],'tmp_name'=>$tmp,'error'=>0,'size'=>$_FILES['images']['size'][$i]];
                    $url = uploadFile($singleFile, 'products');
                    if ($url) {
                        $isPrimary = $i === 0 ? 1 : 0;
                        $db->prepare("INSERT INTO product_images (product_id,image_url,sort_order,is_primary) VALUES (?,?,?,?)")->execute([$id,$url,$i,$isPrimary]);
                    }
                }
            }
        }
        logActivity($id ? 'edit' : 'create', 'products', 'product', $id, $d['name']);
        flash('success', 'บันทึกสินค้าเรียบร้อย');
        header("Location: index.php"); exit;
    }
    $row = $d;
}

$existingImages = [];
if ($id) {
    $imgStmt = $db->prepare("SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order");
    $imgStmt->execute([$id]);
    $existingImages = $imgStmt->fetchAll();
}

include dirname(__DIR__) . '/includes/header.php';
$d = $row ?? [];
?>
<form method="POST" enctype="multipart/form-data">
  <?=csrfField()?>
  <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul></div><?php endif; ?>
  <div class="row g-3">
    <!-- Main Form -->
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-box-seam me-2"></i>ข้อมูลสินค้า</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12"><label class="form-label">ชื่อสินค้า *</label>
              <input type="text" class="form-control" name="name" value="<?=e($d['name']??'')?>" required data-slug-source="slugField"></div>
            <div class="col-12"><label class="form-label">Slug URL</label>
              <input type="text" class="form-control" name="slug" id="slugField" value="<?=e($d['slug']??'')?>" data-slug-target="1"></div>
            <div class="col-md-4"><label class="form-label">ร้านค้า *</label>
              <select class="form-select" name="shop_id" required>
                <option value="">-- เลือกร้านค้า --</option>
                <?php foreach ($shops as $sh): ?>
                <option value="<?=$sh['shop_id']?>" <?=($d['shop_id']??0)==$sh['shop_id']?'selected':''?>><?=e($sh['shop_name'])?></option>
                <?php endforeach; ?>
              </select></div>
            <div class="col-md-4"><label class="form-label">หมวดหมู่ *</label>
              <select class="form-select" name="category_id" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?=$c['category_id']?>" <?=($d['category_id']??0)==$c['category_id']?'selected':''?>>
                  <?=$c['parent_id']?'└ ':''?><?=e($c['name'])?>
                </option>
                <?php endforeach; ?>
              </select></div>
            <div class="col-md-4"><label class="form-label">ยี่ห้อ / Brand</label>
              <input type="text" class="form-control" name="brand" value="<?=e($d['brand']??'')?>"></div>
            <div class="col-12"><label class="form-label">คำอธิบายสินค้า</label>
              <textarea class="form-control" name="description" rows="6"><?=htmlspecialchars($d['description']??'')?></textarea></div>
          </div>
        </div>
      </div>

      <!-- Pricing -->
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-tag me-2"></i>ราคา & สต็อก</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4"><label class="form-label">ราคาปกติ (฿) *</label>
              <input type="number" class="form-control" name="base_price" value="<?=e($d['base_price']??'')?>" step="0.01" min="0" required></div>
            <div class="col-md-4"><label class="form-label">ราคาลด (฿)</label>
              <input type="number" class="form-control" name="discount_price" value="<?=e($d['discount_price']??'')?>" step="0.01" min="0" placeholder="ว่าง = ไม่มีส่วนลด"></div>
            <div class="col-md-4"><label class="form-label">SKU</label>
              <input type="text" class="form-control" name="sku" value="<?=e($d['sku']??'')?>"></div>
            <div class="col-md-4"><label class="form-label">น้ำหนัก (กรัม)</label>
              <input type="number" class="form-control" name="weight_grams" value="<?=e($d['weight_grams']??'')?>" min="0"></div>
            <div class="col-md-4"><label class="form-label">สภาพสินค้า</label>
              <select class="form-select" name="condition_type">
                <option value="new" <?=($d['condition_type']??'new')==='new'?'selected':''?>>ใหม่</option>
                <option value="used" <?=($d['condition_type']??'')==='used'?'selected':''?>>มือสอง</option>
                <option value="refurbished" <?=($d['condition_type']??'')==='refurbished'?'selected':''?>>ซ่อมแซมแล้ว</option>
              </select></div>
          </div>
        </div>
      </div>

      <!-- Images -->
      <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-images me-2"></i>รูปภาพสินค้า</div>
        <div class="card-body">
          <?php if ($existingImages): ?>
          <div class="d-flex gap-2 flex-wrap mb-3">
            <?php foreach ($existingImages as $img): ?>
            <div class="position-relative">
              <img src="<?=e($img['image_url'])?>" style="width:80px;height:80px;object-fit:cover;border-radius:.5rem;border:2px solid <?=$img['is_primary']?'#EE4D2D':'#dee2e6'?>" alt="">
              <?php if ($img['is_primary']): ?><span class="position-absolute bottom-0 start-50 translate-middle-x badge bg-danger small">หลัก</span><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <label class="form-label">อัปโหลดรูปภาพ (สูงสุด 8 รูป)</label>
          <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
          <div class="form-text">รูปแรกที่อัปโหลดจะเป็นรูปหลัก รองรับ JPG, PNG, WebP ขนาดไม่เกิน 5MB/รูป</div>
        </div>
      </div>
    </div>

    <!-- Sidebar Options -->
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header fw-semibold">ตัวเลือก</div>
        <div class="card-body">
          <div class="mb-3"><label class="form-label">สถานะสินค้า</label>
            <select class="form-select" name="status">
              <option value="draft" <?=($d['status']??'draft')==='draft'?'selected':''?>>Draft (ร่าง)</option>
              <option value="active" <?=($d['status']??'')==='active'?'selected':''?>>Active (เปิดขาย)</option>
              <option value="inactive" <?=($d['status']??'')==='inactive'?'selected':''?>>Inactive (ปิดขาย)</option>
            </select></div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="chkFeat" <?=($d['is_featured']??0)?'checked':''?>>
            <label class="form-check-label" for="chkFeat">สินค้าแนะนำ (Featured)</label>
          </div>
        </div>
      </div>
      <div class="d-flex flex-column gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึกสินค้า</button>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>ยกเลิก</a>
      </div>
    </div>
  </div>
</form>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
