<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db  = getDB();
$id  = (int)($_GET['id'] ?? 0);
$row = null; $errors = [];
if ($id) { $s=$db->prepare("SELECT * FROM cms_pages WHERE page_id=?"); $s->execute([$id]); $row=$s->fetch(); if (!$row){flash('danger','ไม่พบหน้า');header('Location: pages.php');exit;} }
$pageTitle  = $id ? 'แก้ไขหน้า: '.e($row['title']) : 'สร้างหน้าใหม่';
$breadcrumb = ['CMS'=>false,'หน้าเว็บ'=>'pages.php',($id?'แก้ไข':'สร้าง')=>false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST[CSRF_TOKEN_NAME]??'')) { flash('danger','CSRF');header('Location: pages.php');exit; }
    $d = [
        'title'     => trim($_POST['title']??''),
        'slug'      => trim($_POST['slug']??'') ?: slugify(trim($_POST['title']??'')),
        'content'   => $_POST['content']??'',
        'meta_title'=> trim($_POST['meta_title']??''),
        'meta_desc' => trim($_POST['meta_desc']??''),
        'meta_keywords'=> trim($_POST['meta_keywords']??''),
        'template'  => $_POST['template']??'default',
        'status'    => $_POST['status']??'draft',
        'sort_order'=> (int)($_POST['sort_order']??0),
    ];
    if (!$d['title']) $errors[]='กรุณากรอกชื่อหน้า';
    if (!$errors) {
        $uid = $_SESSION['admin_id'];
        if ($id) {
            $db->prepare("UPDATE cms_pages SET title=?,slug=?,content=?,meta_title=?,meta_desc=?,meta_keywords=?,template=?,status=?,sort_order=?,updated_by=?,updated_at=NOW() WHERE page_id=?")
               ->execute([$d['title'],$d['slug'],$d['content'],$d['meta_title'],$d['meta_desc'],$d['meta_keywords'],$d['template'],$d['status'],$d['sort_order'],$uid,$id]);
        } else {
            $db->prepare("INSERT INTO cms_pages (title,slug,content,meta_title,meta_desc,meta_keywords,template,status,sort_order,created_by,updated_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
               ->execute([$d['title'],$d['slug'],$d['content'],$d['meta_title'],$d['meta_desc'],$d['meta_keywords'],$d['template'],$d['status'],$d['sort_order'],$uid,$uid]);
        }
        logActivity($id?'edit':'create','cms','page',$id,$d['title']);
        flash('success','บันทึกเรียบร้อย'); header('Location: pages.php'); exit;
    }
    $row = $d;
}
$d = $row ?? [];
include dirname(__DIR__) . '/includes/header.php';
$extraJs = '<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>tinymce.init({selector:"#pageContent",height:420,plugins:"advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount",toolbar:"undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help",content_style:"body { font-family:Segoe UI,sans-serif; font-size:14px }"});</script>';
?>
<div class="row">
<div class="col-lg-9">
<form method="POST" id="pageForm">
  <?=csrfField()?>
  <div class="card mb-3">
    <div class="card-header"><i class="bi bi-file-earmark-text me-2"></i>เนื้อหาหน้า</div>
    <div class="card-body">
      <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul></div><?php endif; ?>
      <div class="mb-3"><label class="form-label">ชื่อหน้า *</label>
        <input type="text" class="form-control" name="title" value="<?=e($d['title']??'')?>" required data-slug-source="slugField"></div>
      <div class="mb-3"><label class="form-label">Slug URL</label>
        <div class="input-group"><span class="input-group-text text-muted small">/</span>
          <input type="text" class="form-control" name="slug" id="slugField" value="<?=e($d['slug']??'')?>" data-slug-target="1">
        </div></div>
      <div class="mb-3"><label class="form-label">เนื้อหา</label>
        <textarea id="pageContent" name="content" class="form-control" rows="15"><?=htmlspecialchars($d['content']??'')?></textarea></div>
    </div>
  </div>
  <!-- SEO -->
  <div class="card mb-3">
    <div class="card-header"><i class="bi bi-search me-2"></i>SEO Settings</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Meta Title</label>
          <input type="text" class="form-control" name="meta_title" value="<?=e($d['meta_title']??'')?>" placeholder="ชื่อสำหรับ Search Engine"></div>
        <div class="col-12"><label class="form-label">Meta Description</label>
          <textarea class="form-control" name="meta_desc" rows="2" maxlength="160" placeholder="คำอธิบายสั้น (ไม่เกิน 160 ตัวอักษร)"><?=e($d['meta_desc']??'')?></textarea></div>
        <div class="col-12"><label class="form-label">Keywords</label>
          <input type="text" class="form-control" name="meta_keywords" value="<?=e($d['meta_keywords']??'')?>" placeholder="keyword1, keyword2, ..."></div>
      </div>
    </div>
  </div>
  <div class="d-flex gap-2 justify-content-end mb-4">
    <a href="pages.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>ยกเลิก</a>
    <button type="submit" name="status_submit" value="draft" class="btn btn-outline-secondary"><i class="bi bi-save me-1"></i>บันทึก Draft</button>
    <button type="submit" name="status_submit" value="published" class="btn btn-primary"><i class="bi bi-globe me-1"></i>เผยแพร่</button>
  </div>
</form>
</div>
<!-- Sidebar Options -->
<div class="col-lg-3">
  <div class="card mb-3">
    <div class="card-header">ตัวเลือก</div>
    <div class="card-body">
      <label class="form-label">สถานะ</label>
      <select class="form-select mb-3" name="status" form="pageForm">
        <option value="draft" <?=($d['status']??'draft')==='draft'?'selected':''?>>Draft</option>
        <option value="published" <?=($d['status']??'')==='published'?'selected':''?>>Published</option>
        <option value="private" <?=($d['status']??'')==='private'?'selected':''?>>Private</option>
      </select>
      <label class="form-label">Template</label>
      <select class="form-select mb-3" name="template" form="pageForm">
        <option value="default" <?=($d['template']??'default')==='default'?'selected':''?>>Default</option>
        <option value="fullwidth" <?=($d['template']??'')==='fullwidth'?'selected':''?>>Full Width</option>
        <option value="sidebar" <?=($d['template']??'')==='sidebar'?'selected':''?>>Sidebar</option>
        <option value="landing" <?=($d['template']??'')==='landing'?'selected':''?>>Landing Page</option>
      </select>
      <label class="form-label">ลำดับแสดง</label>
      <input type="number" class="form-control" name="sort_order" form="pageForm" value="<?=(int)($d['sort_order']??0)?>" min="0">
    </div>
  </div>
</div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
