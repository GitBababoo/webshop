<?php
$pageTitle  = 'จัดการเมนู';
$breadcrumb = ['CMS' => false, 'เมนู' => false];
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $act = $_POST['action'] ?? '';
    if ($act === 'add_item') {
        $menuId = (int)($_POST['menu_id'] ?? 0);
        $label  = trim($_POST['label'] ?? '');
        $url    = trim($_POST['url'] ?? '');
        $target = $_POST['target'] ?? '_self';
        $icon   = trim($_POST['icon'] ?? '');
        if ($menuId && $label && $url) {
            $maxSort = $db->prepare("SELECT COALESCE(MAX(sort_order),0)+1 FROM cms_menu_items WHERE menu_id=?")->execute([$menuId]) ? 0 : 0;
            $s=$db->prepare("SELECT COALESCE(MAX(sort_order),0)+1 AS ms FROM cms_menu_items WHERE menu_id=?"); $s->execute([$menuId]); $maxSort=(int)$s->fetchColumn();
            $db->prepare("INSERT INTO cms_menu_items (menu_id,label,url,target,icon,sort_order) VALUES (?,?,?,?,?,?)")->execute([$menuId,$label,$url,$target,$icon,$maxSort]);
            flash('success','เพิ่มเมนูเรียบร้อย');
        }
    }
    if ($act === 'delete_item') {
        $itemId = (int)($_POST['item_id'] ?? 0);
        if ($itemId) { $db->prepare("DELETE FROM cms_menu_items WHERE item_id=?")->execute([$itemId]); flash('success','ลบรายการเมนูเรียบร้อย'); }
    }
    if ($act === 'toggle_item') {
        $itemId = (int)($_POST['item_id'] ?? 0);
        if ($itemId) { $db->prepare("UPDATE cms_menu_items SET is_active=NOT is_active WHERE item_id=?")->execute([$itemId]); }
    }
    if ($act === 'edit_item') {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $label  = trim($_POST['label'] ?? '');
        $url    = trim($_POST['url'] ?? '');
        if ($itemId && $label) {
            $db->prepare("UPDATE cms_menu_items SET label=?,url=?,target=?,icon=? WHERE item_id=?")->execute([$label,$url,$_POST['target']??'_self',$_POST['icon']??'',$itemId]);
            flash('success','แก้ไขเมนูเรียบร้อย');
        }
    }
    header('Location: menus.php'); exit;
}

$menus    = $db->query("SELECT * FROM cms_menus ORDER BY menu_id")->fetchAll();
$allItems = $db->query("SELECT * FROM cms_menu_items ORDER BY menu_id, sort_order")->fetchAll();
$pages    = $db->query("SELECT slug,title FROM cms_pages WHERE status='published' ORDER BY title")->fetchAll();

include dirname(__DIR__) . '/includes/header.php';
?>
<div class="row g-4">
<?php foreach ($menus as $menu):
  $items = array_filter($allItems, fn($i)=>$i['menu_id']===$menu['menu_id'] && !$i['parent_id']);
?>
<div class="col-lg-4">
  <div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold"><i class="bi bi-list-ul me-2"></i><?=e($menu['name'])?></span>
      <code class="small text-muted"><?=e($menu['location'])?></code>
    </div>
    <!-- Items List -->
    <ul class="list-group list-group-flush" id="menuList_<?=$menu['menu_id']?>">
      <?php foreach ($items as $item): ?>
      <li class="list-group-item py-2">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-grip-vertical drag-handle text-muted"></i>
          <?php if ($item['icon']): ?><i class="bi <?=e($item['icon'])?> text-muted"></i><?php endif; ?>
          <div class="flex-grow-1">
            <div class="small fw-semibold <?=$item['is_active']?'':'text-muted text-decoration-line-through'?>"><?=e($item['label'])?></div>
            <div class="text-muted" style="font-size:.72rem"><?=e($item['url'])?> <?=$item['target']==='_blank'?'<i class="bi bi-box-arrow-up-right"></i>':''?></div>
          </div>
          <form method="POST" class="d-flex gap-1">
            <?=csrfField()?><input type="hidden" name="item_id" value="<?=$item['item_id']?>">
            <button type="submit" name="action" value="toggle_item" class="btn btn-sm border-0 py-0 <?=$item['is_active']?'text-success':'text-muted'?>"><i class="bi bi-eye<?=$item['is_active']?'':'-slash'?>"></i></button>
            <button type="submit" name="action" value="delete_item" class="btn btn-sm border-0 py-0 text-danger" onclick="return confirm('ลบ?')"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
    <!-- Add Item Form -->
    <div class="card-footer bg-white">
      <form method="POST">
        <?=csrfField()?>
        <input type="hidden" name="action" value="add_item">
        <input type="hidden" name="menu_id" value="<?=$menu['menu_id']?>">
        <div class="row g-2">
          <div class="col-6"><input type="text" class="form-control form-control-sm" name="label" placeholder="ชื่อเมนู *" required></div>
          <div class="col-6">
            <select class="form-select form-select-sm" name="url">
              <option value="/">หน้าหลัก</option>
              <?php foreach ($pages as $pg): ?><option value="/<?=e($pg['slug'])?>"><?=e($pg['title'])?></option><?php endforeach; ?>
              <option value="custom">กรอก URL เอง...</option>
            </select>
          </div>
          <div class="col-8"><input type="text" class="form-control form-control-sm" name="icon" placeholder="Bootstrap icon เช่น bi-house"></div>
          <div class="col-4"><button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-plus-lg"></i> เพิ่ม</button></div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
