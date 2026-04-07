<?php
$pageTitle  = 'จัดการหมวดหมู่';
$breadcrumb = ['หมวดหมู่' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $act = $_POST['action'] ?? '';
    $id  = (int)($_POST['id'] ?? 0);
    if ($act === 'delete' && $id) {
        $children = $db->prepare("SELECT COUNT(*) FROM categories WHERE parent_id=?")->execute([$id]) ? (int)$db->prepare("SELECT COUNT(*) FROM categories WHERE parent_id=?")->execute([$id]) : 0;
        $s = $db->prepare("SELECT COUNT(*) FROM categories WHERE parent_id=?"); $s->execute([$id]); $children = (int)$s->fetchColumn();
        $p = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id=?"); $p->execute([$id]); $prods = (int)$p->fetchColumn();
        if ($children > 0 || $prods > 0) { flash('danger','ไม่สามารถลบได้ มีหมวดหมู่ย่อยหรือสินค้าอยู่ภายใน'); }
        else { $db->prepare("DELETE FROM categories WHERE category_id=?")->execute([$id]); logActivity('delete','categories','category',$id); flash('success','ลบหมวดหมู่เรียบร้อย'); }
    }
    if ($act === 'toggle' && $id) {
        $db->prepare("UPDATE categories SET is_active = NOT is_active WHERE category_id=?")->execute([$id]);
        flash('success','อัปเดตสถานะเรียบร้อย');
    }
    header('Location: index.php'); exit;
}

$categories = $db->query("
    SELECT c.*, p.name AS parent_name,
           (SELECT COUNT(*) FROM products WHERE category_id=c.category_id) AS product_count,
           (SELECT COUNT(*) FROM categories WHERE parent_id=c.category_id) AS child_count
    FROM categories c LEFT JOIN categories p ON c.parent_id=p.category_id
    ORDER BY COALESCE(c.parent_id,c.category_id), c.parent_id IS NULL DESC, c.sort_order, c.name
")->fetchAll();

include dirname(__DIR__) . '/includes/header.php';
?>
<div class="d-flex justify-content-end mb-3">
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>เพิ่มหมวดหมู่</a>
</div>
<div class="card">
  <div class="card-header fw-semibold"><i class="bi bi-grid-3x3-gap me-2"></i>หมวดหมู่ทั้งหมด (<?=count($categories)?> รายการ)</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>ชื่อหมวดหมู่</th><th>หมวดหมู่แม่</th><th>สินค้า</th><th>หมวดย่อย</th><th>ลำดับ</th><th>สถานะ</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($categories as $cat): ?>
      <tr>
        <td>
          <div class="d-flex align-items-center gap-2">
            <?php if ($cat['icon_url']): ?><img src="<?=e($cat['icon_url'])?>" style="width:28px;height:28px;object-fit:contain" alt=""><?php endif; ?>
            <div>
              <span class="<?=$cat['parent_id']?'ms-3 text-muted':''?>"><?=$cat['parent_id']?'└ ':''?><?=e($cat['name'])?></span>
              <div class="text-muted" style="font-size:.75rem">slug: <?=e($cat['slug'])?></div>
            </div>
          </div>
        </td>
        <td class="text-muted small"><?=e($cat['parent_name'] ?? '—')?></td>
        <td><?=number_format((int)$cat['product_count'])?></td>
        <td><?=number_format((int)$cat['child_count'])?></td>
        <td><?=$cat['sort_order']?></td>
        <td>
          <form method="POST" class="d-inline">
            <?=csrfField()?><input type="hidden" name="id" value="<?=$cat['category_id']?>">
            <button type="submit" name="action" value="toggle" class="btn btn-sm <?=$cat['is_active']?'btn-success':'btn-outline-secondary'?> border-0 py-0">
              <?=$cat['is_active']?'<i class="bi bi-toggle-on fs-5"></i>':'<i class="bi bi-toggle-off fs-5 text-muted"></i>'?>
            </button>
          </form>
        </td>
        <td>
          <div class="d-flex gap-1">
            <a href="form.php?id=<?=$cat['category_id']?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <form method="POST" class="d-inline">
              <?=csrfField()?><input type="hidden" name="id" value="<?=$cat['category_id']?>">
              <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"
                onclick="return confirm('ลบหมวดหมู่นี้?')"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
