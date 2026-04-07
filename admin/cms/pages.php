<?php
$pageTitle  = 'จัดการหน้าเว็บ (CMS)';
$breadcrumb = ['CMS' => false, 'หน้าเว็บ' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $id  = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($id) {
        if ($act === 'delete') {
            $s = $db->prepare("SELECT is_system FROM cms_pages WHERE page_id=?"); $s->execute([$id]); $p=$s->fetch();
            if ($p && $p['is_system']) { flash('danger','ไม่สามารถลบหน้าระบบได้'); }
            else { $db->prepare("DELETE FROM cms_pages WHERE page_id=?")->execute([$id]); flash('success','ลบหน้าเรียบร้อย'); }
        }
        if ($act === 'toggle') { $db->prepare("UPDATE cms_pages SET status=IF(status='published','draft','published') WHERE page_id=?")->execute([$id]); flash('success','อัปเดตสถานะ'); }
    }
    header('Location: pages.php'); exit;
}

$pages = $db->query("SELECT p.*, u.username FROM cms_pages p LEFT JOIN users u ON p.created_by=u.user_id ORDER BY p.sort_order, p.title")->fetchAll();
include dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex justify-content-end mb-3">
  <a href="page-form.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>สร้างหน้าใหม่</a>
</div>
<div class="card">
  <div class="card-header fw-semibold"><i class="bi bi-file-earmark-text me-2"></i>หน้าเว็บทั้งหมด (<?=count($pages)?> หน้า)</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>ชื่อหน้า</th><th>Slug</th><th>Template</th><th>Meta Title</th><th>สถานะ</th><th>แก้ไขล่าสุด</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($pages as $pg):
        $stcfg = ['published'=>['label'=>'Published','class'=>'success'],'draft'=>['label'=>'Draft','class'=>'warning'],'private'=>['label'=>'Private','class'=>'secondary']];
        $sc = $stcfg[$pg['status']] ?? ['label'=>$pg['status'],'class'=>'secondary'];
      ?>
      <tr>
        <td>
          <div class="fw-semibold small"><?=e($pg['title'])?>
            <?php if ($pg['is_system']): ?><span class="badge bg-secondary ms-1">System</span><?php endif; ?>
          </div>
        </td>
        <td><code class="small">/<?=e($pg['slug'])?></code></td>
        <td><span class="badge bg-light text-dark border"><?=ucfirst($pg['template'])?></span></td>
        <td class="small text-muted text-truncate" style="max-width:160px"><?=e($pg['meta_title']??'—')?></td>
        <td>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="id" value="<?=$pg['page_id']?>">
            <button type="submit" name="action" value="toggle" class="btn btn-sm <?=$pg['status']==='published'?'btn-success':'btn-outline-secondary'?> border-0 py-0">
              <i class="bi <?=$pg['status']==='published'?'bi-toggle-on':'bi-toggle-off'?> fs-5"></i>
            </button>
          </form>
          <span class="badge bg-<?=$sc['class']?> ms-1"><?=$sc['label']?></span>
        </td>
        <td class="text-muted small"><?=formatDate($pg['updated_at'],'d/m/Y H:i')?></td>
        <td>
          <div class="d-flex gap-1">
            <a href="page-form.php?id=<?=$pg['page_id']?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <?php if (!$pg['is_system'] || isSuperAdmin()): ?>
            <form method="POST" class="d-inline">
              <?=csrfField()?><input type="hidden" name="id" value="<?=$pg['page_id']?>">
              <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบหน้านี้?')"><i class="bi bi-trash"></i></button>
            </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
