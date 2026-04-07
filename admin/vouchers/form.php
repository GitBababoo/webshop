<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();
$db  = getDB();
$id  = (int)($_GET['id'] ?? 0);
$row = null; $errors = [];
if ($id) { $s=$db->prepare("SELECT * FROM platform_vouchers WHERE voucher_id=?"); $s->execute([$id]); $row=$s->fetch(); }
$pageTitle  = $id ? 'แก้ไขโค้ดส่วนลด' : 'เพิ่มโค้ดส่วนลด';
$breadcrumb = ['โค้ดส่วนลด'=>'index.php', ($id?'แก้ไข':'เพิ่ม')=>false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST[CSRF_TOKEN_NAME]??'')) { flash('danger','CSRF'); header('Location: index.php'); exit; }
    $d = [
        'code'            => strtoupper(trim($_POST['code']??'')),
        'name'            => trim($_POST['name']??''),
        'description'     => trim($_POST['description']??''),
        'discount_type'   => $_POST['discount_type']??'fixed',
        'discount_value'  => (float)($_POST['discount_value']??0),
        'min_order_amount'=> (float)($_POST['min_order_amount']??0),
        'max_discount_cap'=> ($_POST['max_discount_cap']!==''?(float)$_POST['max_discount_cap']:null),
        'total_qty'       => ($_POST['total_qty']!==''?(int)$_POST['total_qty']:null),
        'per_user_limit'  => (int)($_POST['per_user_limit']??1),
        'start_at'        => $_POST['start_at']??'',
        'expire_at'       => $_POST['expire_at']??'',
        'is_active'       => (int)($_POST['is_active']??1),
    ];
    if (!$d['code']) $errors[]='กรุณากรอกรหัสโค้ด';
    if (!$d['name']) $errors[]='กรุณากรอกชื่อโค้ด';
    if ($d['discount_value']<=0) $errors[]='มูลค่าส่วนลดต้องมากกว่า 0';
    if (!$errors) {
        if ($id) {
            $db->prepare("UPDATE platform_vouchers SET code=?,name=?,description=?,discount_type=?,discount_value=?,min_order_amount=?,max_discount_cap=?,total_qty=?,per_user_limit=?,start_at=?,expire_at=?,is_active=? WHERE voucher_id=?")
               ->execute([$d['code'],$d['name'],$d['description'],$d['discount_type'],$d['discount_value'],$d['min_order_amount'],$d['max_discount_cap'],$d['total_qty'],$d['per_user_limit'],$d['start_at'],$d['expire_at'],$d['is_active'],$id]);
        } else {
            $db->prepare("INSERT INTO platform_vouchers (code,name,description,discount_type,discount_value,min_order_amount,max_discount_cap,total_qty,per_user_limit,start_at,expire_at,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
               ->execute([$d['code'],$d['name'],$d['description'],$d['discount_type'],$d['discount_value'],$d['min_order_amount'],$d['max_discount_cap'],$d['total_qty'],$d['per_user_limit'],$d['start_at'],$d['expire_at'],$d['is_active']]);
        }
        logActivity($id?'edit':'create','vouchers');
        flash('success','บันทึกเรียบร้อย'); header('Location: index.php'); exit;
    }
    $row = $d;
}
include dirname(__DIR__) . '/includes/header.php'; $d=$row??[];
?>
<div class="row justify-content-center"><div class="col-lg-8">
<form method="POST">
  <?=csrfField()?>
  <div class="card mb-3">
    <div class="card-header"><?=$pageTitle?></div>
    <div class="card-body">
      <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul></div><?php endif; ?>
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">รหัสโค้ด *</label>
          <input type="text" class="form-control text-uppercase fw-bold" name="code" value="<?=e($d['code']??'')?>" required placeholder="SHOP50"></div>
        <div class="col-md-8"><label class="form-label">ชื่อโค้ด *</label>
          <input type="text" class="form-control" name="name" value="<?=e($d['name']??'')?>" required></div>
        <div class="col-12"><label class="form-label">คำอธิบาย</label>
          <textarea class="form-control" name="description" rows="2"><?=e($d['description']??'')?></textarea></div>
        <div class="col-md-4"><label class="form-label">ประเภทส่วนลด</label>
          <select class="form-select" name="discount_type" id="discType">
            <option value="fixed" <?=($d['discount_type']??'')==='fixed'?'selected':''?>>ลด ฿ (Fixed)</option>
            <option value="percentage" <?=($d['discount_type']??'')==='percentage'?'selected':''?>>ลด % (Percent)</option>
            <option value="free_shipping" <?=($d['discount_type']??'')==='free_shipping'?'selected':''?>>ฟรีค่าส่ง</option>
          </select></div>
        <div class="col-md-4"><label class="form-label">มูลค่าส่วนลด *</label>
          <div class="input-group"><input type="number" class="form-control" name="discount_value" value="<?=$d['discount_value']??0?>" step="0.01" min="0" required>
          <span class="input-group-text" id="discUnit">฿</span></div></div>
        <div class="col-md-4"><label class="form-label">ลดสูงสุด (% เท่านั้น)</label>
          <input type="number" class="form-control" name="max_discount_cap" value="<?=$d['max_discount_cap']??''?>" step="0.01" min="0" placeholder="ไม่จำกัด"></div>
        <div class="col-md-4"><label class="form-label">ยอดสั่งซื้อขั้นต่ำ</label>
          <input type="number" class="form-control" name="min_order_amount" value="<?=$d['min_order_amount']??0?>" step="0.01" min="0"></div>
        <div class="col-md-4"><label class="form-label">จำนวนโค้ดทั้งหมด</label>
          <input type="number" class="form-control" name="total_qty" value="<?=$d['total_qty']??''?>" min="1" placeholder="ไม่จำกัด"></div>
        <div class="col-md-4"><label class="form-label">ใช้ได้ต่อ User</label>
          <input type="number" class="form-control" name="per_user_limit" value="<?=$d['per_user_limit']??1?>" min="1"></div>
        <div class="col-md-6"><label class="form-label">เริ่มต้น</label>
          <input type="datetime-local" class="form-control" name="start_at" value="<?=str_replace(' ','T',$d['start_at']??date('Y-m-d H:i:s'))?>"></div>
        <div class="col-md-6"><label class="form-label">หมดอายุ</label>
          <input type="datetime-local" class="form-control" name="expire_at" value="<?=str_replace(' ','T',$d['expire_at']??'')?>"></div>
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
<script>
document.getElementById('discType').addEventListener('change',function(){
  document.getElementById('discUnit').textContent=this.value==='percentage'?'%':'฿';
});
</script>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
