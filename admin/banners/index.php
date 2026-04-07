<?php
$pageTitle  = 'จัดการแบนเนอร์';
$breadcrumb = ['แบนเนอร์' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db   = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $act = $_POST['action'] ?? '';
    $id  = (int)($_POST['id'] ?? 0);
    if ($act === 'delete' && $id) { $db->prepare("DELETE FROM banners WHERE banner_id=?")->execute([$id]); flash('success','ลบแบนเนอร์เรียบร้อย'); header('Location: index.php'); exit; }
    if ($act === 'toggle' && $id) { $db->prepare("UPDATE banners SET is_active=NOT is_active WHERE banner_id=?")->execute([$id]); flash('success','อัปเดตเรียบร้อย'); header('Location: index.php'); exit; }
    if ($act === 'save') {
        $title   = trim($_POST['title']??'');
        $link    = trim($_POST['link_url']??'');
        $pos     = $_POST['position']??'homepage_main';
        $sort    = (int)($_POST['sort_order']??0);
        $active  = (int)($_POST['is_active']??1);
        $startAt = $_POST['start_at']??null;
        $endAt   = $_POST['end_at']??null;
        if (!$title) $errors[]='กรุณากรอกชื่อแบนเนอร์';
        $imgUrl = '';
        if (!empty($_FILES['image']['name'])) { $up=uploadFile($_FILES['image'],'banners'); if ($up) $imgUrl=$up; else $errors[]='อัปโหลดรูปล้มเหลว'; }
        if (!$errors) {
            if ($id) {
                $sql = "UPDATE banners SET title=?,link_url=?,position=?,sort_order=?,is_active=?,start_at=?,end_at=? WHERE banner_id=?";
                $p   = [$title,$link?:null,$pos,$sort,$active,$startAt?:null,$endAt?:null,$id];
                if ($imgUrl) { $sql = "UPDATE banners SET title=?,link_url=?,position=?,sort_order=?,is_active=?,start_at=?,end_at=?,image_url=? WHERE banner_id=?"; $p[]=$imgUrl; array_splice($p,-1,0,[$imgUrl]); $p=[$title,$link?:null,$pos,$sort,$active,$startAt?:null,$endAt?:null,$imgUrl,$id]; }
                $db->prepare($sql)->execute($p);
            } else {
                if (!$imgUrl) $errors[]='กรุณาอัปโหลดรูปแบนเนอร์';
                else { $db->prepare("INSERT INTO banners (title,image_url,link_url,position,sort_order,is_active,start_at,end_at) VALUES (?,?,?,?,?,?,?,?)")->execute([$title,$imgUrl,$link?:null,$pos,$sort,$active,$startAt?:null,$endAt?:null]); flash('success','เพิ่มแบนเนอร์เรียบร้อย'); header('Location: index.php'); exit; }
            }
            if (!$errors) { flash('success','บันทึกเรียบร้อย'); header('Location: index.php'); exit; }
        }
    }
}

$banners = $db->query("SELECT * FROM banners ORDER BY position, sort_order, created_at DESC")->fetchAll();
include dirname(__DIR__) . '/includes/header.php';
$posLabels = ['homepage_main'=>'หน้าหลัก (Main)','homepage_sub'=>'หน้าหลัก (Sub)','category'=>'หมวดหมู่','flash_sale'=>'Flash Sale','popup'=>'Popup'];
?>
<div class="row g-3">
  <!-- Form -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-plus-lg me-2"></i>เพิ่มแบนเนอร์ใหม่</div>
      <div class="card-body">
        <?php if ($errors): ?><div class="alert alert-danger small"><ul class="mb-0"><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
          <?=csrfField()?>
          <input type="hidden" name="action" value="save">
          <div class="mb-3"><label class="form-label">ชื่อ *</label><input type="text" class="form-control" name="title" required></div>
          <div class="mb-3"><label class="form-label">รูปภาพ *</label>
            <div class="img-preview-wrap mb-1" onclick="document.getElementById('bannerFile').click()">
              <img id="bannerPreview" src="" alt="" style="display:none;max-width:100%;max-height:130px">
              <span id="bannerPlaceholder" class="text-muted small"><i class="bi bi-cloud-upload fs-3 d-block mb-1"></i>คลิกเพื่ออัปโหลด</span>
            </div>
            <input type="file" id="bannerFile" class="d-none" name="image" accept="image/*">
          </div>
          <div class="mb-3"><label class="form-label">ลิงก์ (URL)</label><input type="text" class="form-control" name="link_url" placeholder="/category/electronics"></div>
          <div class="mb-3"><label class="form-label">ตำแหน่ง</label>
            <select class="form-select" name="position">
              <?php foreach ($posLabels as $v=>$l): ?><option value="<?=$v?>"><?=$l?></option><?php endforeach; ?>
            </select></div>
          <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label small">เริ่มต้น</label><input type="datetime-local" class="form-control form-control-sm" name="start_at"></div>
            <div class="col-6"><label class="form-label small">สิ้นสุด</label><input type="datetime-local" class="form-control form-control-sm" name="end_at"></div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label small">ลำดับ</label><input type="number" class="form-control form-control-sm" name="sort_order" value="0" min="0"></div>
            <div class="col-6 d-flex align-items-end"><div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" checked id="chkA"><label class="form-check-label" for="chkA">เปิดใช้งาน</label></div></div>
          </div>
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Banner List -->
  <div class="col-lg-8">
    <?php foreach ($posLabels as $posKey=>$posLabel):
      $filtered = array_filter($banners, fn($b)=>$b['position']===$posKey);
      if (!$filtered) continue;
    ?>
    <div class="card mb-3">
      <div class="card-header fw-semibold"><i class="bi bi-image me-2"></i><?=$posLabel?></div>
      <div class="list-group list-group-flush">
        <?php foreach ($filtered as $b): ?>
        <div class="list-group-item">
          <div class="d-flex align-items-center gap-3">
            <img src="<?=e($b['image_url'])?>" style="width:100px;height:50px;object-fit:cover;border-radius:.5rem" alt="">
            <div class="flex-grow-1">
              <div class="fw-semibold small"><?=e($b['title'])?></div>
              <?php if ($b['link_url']): ?><div class="text-muted" style="font-size:.72rem"><?=e($b['link_url'])?></div><?php endif; ?>
              <div class="text-muted" style="font-size:.72rem">ลำดับ: <?=$b['sort_order']?> <?=$b['start_at']?'| '.formatDate($b['start_at'],'d/m/Y').'–'.formatDate($b['end_at'],'d/m/Y'):''?></div>
            </div>
            <form method="POST" class="d-flex gap-1">
              <?=csrfField()?><input type="hidden" name="id" value="<?=$b['banner_id']?>">
              <button type="submit" name="action" value="toggle" class="btn btn-sm <?=$b['is_active']?'btn-success':'btn-outline-secondary'?> border-0 py-0"><i class="bi <?=$b['is_active']?'bi-toggle-on':'bi-toggle-off'?> fs-5"></i></button>
              <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบแบนเนอร์นี้?')"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<script>
document.getElementById('bannerFile').addEventListener('change',function(){
  if(this.files[0]){const r=new FileReader();r.onload=e=>{document.getElementById('bannerPreview').src=e.target.result;document.getElementById('bannerPreview').style.display='block';document.getElementById('bannerPlaceholder').style.display='none';};r.readAsDataURL(this.files[0]);}
});
</script>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
