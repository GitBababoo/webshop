<?php
$pageTitle  = 'จัดการ Widget';
$breadcrumb = ['CMS' => false, 'Widget' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $act = $_POST['action'] ?? '';
    $id  = (int)($_POST['id'] ?? 0);
    if ($act === 'delete' && $id) { $db->prepare("DELETE FROM cms_widgets WHERE widget_id=?")->execute([$id]); flash('success','ลบ Widget เรียบร้อย'); header('Location: widgets.php'); exit; }
    if ($act === 'toggle' && $id) { $db->prepare("UPDATE cms_widgets SET is_active=NOT is_active WHERE widget_id=?")->execute([$id]); flash('success','อัปเดต'); header('Location: widgets.php'); exit; }
    if (in_array($act, ['create','update'])) {
        $name     = trim($_POST['name'] ?? '');
        $type     = $_POST['widget_type'] ?? 'html';
        $position = trim($_POST['position'] ?? '');
        $content  = $_POST['content'] ?? '';
        $sort     = (int)($_POST['sort_order'] ?? 0);
        $active   = (int)($_POST['is_active'] ?? 1);
        $startAt  = $_POST['start_at'] ?? null;
        $endAt    = $_POST['end_at'] ?? null;
        if ($name && $position) {
            if ($act === 'create') {
                $db->prepare("INSERT INTO cms_widgets (name,widget_type,position,content,sort_order,is_active,start_at,end_at) VALUES (?,?,?,?,?,?,?,?)")
                   ->execute([$name,$type,$position,$content,$sort,$active,$startAt?:null,$endAt?:null]);
                flash('success','สร้าง Widget เรียบร้อย');
            } else {
                $db->prepare("UPDATE cms_widgets SET name=?,widget_type=?,position=?,content=?,sort_order=?,is_active=?,start_at=?,end_at=?,updated_at=NOW() WHERE widget_id=?")
                   ->execute([$name,$type,$position,$content,$sort,$active,$startAt?:null,$endAt?:null,$id]);
                flash('success','บันทึก Widget เรียบร้อย');
            }
            header('Location: widgets.php'); exit;
        }
    }
    header('Location: widgets.php'); exit;
}

$editWidget = null;
if (isset($_GET['edit'])) { $s=$db->prepare("SELECT * FROM cms_widgets WHERE widget_id=?"); $s->execute([(int)$_GET['edit']]); $editWidget=$s->fetch(); }
$widgets = $db->query("SELECT * FROM cms_widgets ORDER BY position, sort_order, created_at DESC")->fetchAll();
include dirname(__DIR__) . '/includes/header.php';
$typeLabels = ['html'=>'HTML Custom','image_banner'=>'Image Banner','product_grid'=>'Product Grid','category_list'=>'Category List','text'=>'Text','video'=>'Video','countdown'=>'Countdown','announcement'=>'Announcement Bar'];
$positionOptions = ['homepage_top'=>'หน้าหลัก - บน','homepage_middle'=>'หน้าหลัก - กลาง','homepage_bottom'=>'หน้าหลัก - ล่าง','sidebar_left'=>'Sidebar ซ้าย','sidebar_right'=>'Sidebar ขวา','footer_top'=>'Footer บน','popup'=>'Popup','category_top'=>'หมวดหมู่ - บน'];
?>
<div class="row g-4">
  <!-- Widget Form -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-<?=$editWidget?'pencil':'plus-lg'?> me-2"></i><?=$editWidget?'แก้ไข Widget':'สร้าง Widget ใหม่'?></div>
      <div class="card-body">
        <form method="POST">
          <?=csrfField()?>
          <?php if ($editWidget): ?><input type="hidden" name="id" value="<?=$editWidget['widget_id']?>"><?php endif; ?>
          <div class="mb-3"><label class="form-label">ชื่อ Widget *</label>
            <input type="text" class="form-control" name="name" value="<?=e($editWidget['name']??'')?>" required></div>
          <div class="mb-3"><label class="form-label">ประเภท</label>
            <select class="form-select" name="widget_type">
              <?php foreach ($typeLabels as $tv=>$tl): ?>
              <option value="<?=$tv?>" <?=($editWidget['widget_type']??'html')===$tv?'selected':''?>><?=$tl?></option>
              <?php endforeach; ?>
            </select></div>
          <div class="mb-3"><label class="form-label">ตำแหน่ง *</label>
            <select class="form-select" name="position">
              <?php foreach ($positionOptions as $pv=>$pl): ?>
              <option value="<?=$pv?>" <?=($editWidget['position']??'')===$pv?'selected':''?>><?=$pl?></option>
              <?php endforeach; ?>
            </select></div>
          <div class="mb-3"><label class="form-label">เนื้อหา / HTML</label>
            <textarea class="form-control font-monospace" name="content" rows="6" style="font-size:.8rem"><?=htmlspecialchars($editWidget['content']??'')?></textarea></div>
          <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label small">เริ่มต้น</label><input type="datetime-local" class="form-control form-control-sm" name="start_at" value="<?=str_replace(' ','T',$editWidget['start_at']??'')?>"></div>
            <div class="col-6"><label class="form-label small">สิ้นสุด</label><input type="datetime-local" class="form-control form-control-sm" name="end_at" value="<?=str_replace(' ','T',$editWidget['end_at']??'')?>"></div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label small">ลำดับ</label><input type="number" class="form-control form-control-sm" name="sort_order" value="<?=(int)($editWidget['sort_order']??0)?>" min="0"></div>
            <div class="col-6 d-flex align-items-end"><div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" <?=($editWidget['is_active']??1)?'checked':''?> id="chkA"><label class="form-check-label small" for="chkA">เปิดใช้งาน</label></div></div>
          </div>
          <div class="d-flex gap-2">
            <?php if ($editWidget): ?><a href="widgets.php" class="btn btn-outline-secondary flex-fill">ยกเลิก</a><?php endif; ?>
            <button type="submit" name="action" value="<?=$editWidget?'update':'create'?>" class="btn btn-primary flex-fill"><i class="bi bi-check-lg me-1"></i>บันทึก</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Widget List -->
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-layout-wtf me-2"></i>Widget ทั้งหมด (<?=count($widgets)?> รายการ)</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light"><tr><th>ชื่อ</th><th>ประเภท</th><th>ตำแหน่ง</th><th>ลำดับ</th><th>สถานะ</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($widgets as $w): ?>
          <tr class="<?=$w['is_active']?'':'table-secondary'?>">
            <td class="fw-semibold small"><?=e($w['name'])?></td>
            <td><span class="badge bg-info-subtle text-info border border-info-subtle small"><?=$typeLabels[$w['widget_type']]??$w['widget_type']?></span></td>
            <td><span class="badge bg-light text-dark border small"><?=$positionOptions[$w['position']]??$w['position']?></span></td>
            <td class="small"><?=$w['sort_order']?></td>
            <td>
              <form method="POST" class="d-inline">
                <?=csrfField()?><input type="hidden" name="id" value="<?=$w['widget_id']?>">
                <button type="submit" name="action" value="toggle" class="btn btn-sm border-0 py-0 <?=$w['is_active']?'text-success':'text-muted'?>">
                  <i class="bi <?=$w['is_active']?'bi-toggle-on':'bi-toggle-off'?> fs-5"></i>
                </button>
              </form>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="widgets.php?edit=<?=$w['widget_id']?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" class="d-inline">
                  <?=csrfField()?><input type="hidden" name="id" value="<?=$w['widget_id']?>">
                  <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบ Widget นี้?')"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
