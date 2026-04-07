<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db  = getDB();
$id  = (int)($_GET['id'] ?? 0);
$row = null; $errors = [];

if ($id) { $s=$db->prepare("SELECT * FROM categories WHERE category_id=?"); $s->execute([$id]); $row=$s->fetch(); if (!$row){flash('danger','ไม่พบหมวดหมู่');header('Location: index.php');exit;} }

$pageTitle  = $id ? 'แก้ไขหมวดหมู่' : 'เพิ่มหมวดหมู่';
$breadcrumb = ['หมวดหมู่' => 'index.php', ($id?'แก้ไข':'เพิ่ม') => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) { flash('danger','CSRF');header('Location: index.php');exit; }
    $name   = trim($_POST['name'] ?? '');
    $slug   = trim($_POST['slug'] ?? '') ?: slugify($name);
    $parent = (int)($_POST['parent_id'] ?? 0) ?: null;
    $sort   = (int)($_POST['sort_order'] ?? 0);
    $active = (int)($_POST['is_active'] ?? 1);
    if (!$name) $errors[] = 'กรุณากรอกชื่อหมวดหมู่';
    $imgUrl = $row['icon_url'] ?? '';
    if (!empty($_FILES['icon']['name'])) { $up = uploadFile($_FILES['icon'],'categories'); if ($up) $imgUrl = $up; }
    if (!$errors) {
        if ($id) {
            $db->prepare("UPDATE categories SET name=?,slug=?,parent_id=?,sort_order=?,is_active=?,icon_url=? WHERE category_id=?")->execute([$name,$slug,$parent,$sort,$active,$imgUrl,$id]);
            flash('success','บันทึกเรียบร้อย');
        } else {
            $db->prepare("INSERT INTO categories (name,slug,parent_id,sort_order,is_active,icon_url) VALUES (?,?,?,?,?,?)")->execute([$name,$slug,$parent,$sort,$active,$imgUrl]);
            flash('success','เพิ่มหมวดหมู่เรียบร้อย');
        }
        header('Location: index.php'); exit;
    }
}
$parents = $db->query("SELECT category_id,name FROM categories WHERE parent_id IS NULL ORDER BY sort_order,name")->fetchAll();
include dirname(__DIR__) . '/includes/header.php'; $d=$row??[];
?>
<div class="row justify-content-center"><div class="col-lg-7">
<form method="POST" enctype="multipart/form-data">
  <?=csrfField()?>
  <div class="card mb-3">
    <div class="card-header"><?=$pageTitle?></div>
    <div class="card-body">
      <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e) echo "<li>$e</li>"?></ul></div><?php endif; ?>
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">ชื่อหมวดหมู่ *</label>
          <input type="text" class="form-control" name="name" value="<?=e($d['name']??'')?>" required data-slug-source="slug_field">
        </div>
        <div class="col-md-4"><label class="form-label">ลำดับแสดง</label>
          <input type="number" class="form-control" name="sort_order" value="<?=(int)($d['sort_order']??0)?>" min="0">
        </div>
        <div class="col-12"><label class="form-label">Slug URL</label>
          <input type="text" class="form-control" name="slug" id="slug_field" value="<?=e($d['slug']??'')?>" data-slug-target="1">
        </div>
        <div class="col-md-6"><label class="form-label">หมวดหมู่แม่</label>
          <select class="form-select" name="parent_id">
            <option value="">— หมวดหมู่หลัก —</option>
            <?php foreach ($parents as $p): if ($p['category_id']==$id) continue; ?>
            <option value="<?=$p['category_id']?>" <?=($d['parent_id']??'')==$p['category_id']?'selected':''?>><?=e($p['name'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label">ไอคอน / รูปภาพ</label>
          <input type="file" class="form-control" name="icon" accept="image/*" data-preview="icon_preview">
          <?php if ($d['icon_url']??''): ?><img src="<?=e($d['icon_url'])?>" id="icon_preview" style="height:48px;margin-top:.5rem" alt=""><?php else: ?><img id="icon_preview" style="height:48px;margin-top:.5rem;display:none" alt=""><?php endif; ?>
        </div>
        <div class="col-12"><div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" <?=($d['is_active']??1)?'checked':''?> id="chkA">
          <label class="form-check-label" for="chkA">เปิดใช้งาน</label>
        </div></div>
      </div>
    </div>
  </div>
  <div class="d-flex gap-2 justify-content-end">
    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>ยกเลิก</a>
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
  </div>
</form>
</div></div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
