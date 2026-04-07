<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
frontRequireLogin('/webshop/account/addresses.php');

$userId  = (int)$_SESSION['front_user_id'];
$db      = getDB();
$error   = ''; $success = '';
$editing = null;
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';

    if ($act === 'save') {
        $fields = [
            trim($_POST['label']??'Home'), trim($_POST['recipient_name']??''),
            trim($_POST['phone']??''), trim($_POST['address_line1']??''),
            trim($_POST['address_line2']??''), trim($_POST['district']??''),
            trim($_POST['province']??''), trim($_POST['postal_code']??''),
            !empty($_POST['is_default']) ? 1 : 0,
        ];
        if (!$fields[1]) { $error = 'กรุณากรอกชื่อผู้รับ'; }
        elseif (!$fields[2]) { $error = 'กรุณากรอกเบอร์โทรศัพท์'; }
        elseif (!$fields[3]) { $error = 'กรุณากรอกที่อยู่'; }
        elseif (!$fields[5]) { $error = 'กรุณาเลือกอำเภอ/เขต'; }
        elseif (!$fields[6]) { $error = 'กรุณาเลือกจังหวัด'; }
        else {
            $editId = (int)($_POST['address_id']??0);
            if ($fields[8]) { // Set default: unset others first
                $db->prepare("UPDATE user_addresses SET is_default=0 WHERE user_id=?")->execute([$userId]);
            }
            if ($editId) {
                $db->prepare("UPDATE user_addresses SET label=?,recipient_name=?,phone=?,address_line1=?,address_line2=?,district=?,province=?,postal_code=?,is_default=? WHERE address_id=? AND user_id=?")
                   ->execute([...$fields,$editId,$userId]);
                $success = 'อัพเดตที่อยู่สำเร็จ';
            } else {
                // First address = auto default
                $hasAny = (int)$db->prepare("SELECT COUNT(*) FROM user_addresses WHERE user_id=?")->execute([$userId]) ? 0 : 0;
                $cntS = $db->prepare("SELECT COUNT(*) FROM user_addresses WHERE user_id=?");
                $cntS->execute([$userId]);
                if (!$cntS->fetchColumn()) $fields[8] = 1;
                $db->prepare("INSERT INTO user_addresses (user_id,label,recipient_name,phone,address_line1,address_line2,district,province,postal_code,is_default) VALUES (?,?,?,?,?,?,?,?,?,?)")
                   ->execute([$userId,...$fields]);
                $success = 'เพิ่มที่อยู่ใหม่สำเร็จ';
            }
            if ($redirect && !$error) { header('Location: '.$redirect); exit; }
        }
    } elseif ($act === 'delete') {
        $addrId = (int)($_POST['address_id']??0);
        $db->prepare("DELETE FROM user_addresses WHERE address_id=? AND user_id=?")->execute([$addrId,$userId]);
        $success = 'ลบที่อยู่แล้ว';
    } elseif ($act === 'set_default') {
        $addrId = (int)($_POST['address_id']??0);
        $db->prepare("UPDATE user_addresses SET is_default=0 WHERE user_id=?")->execute([$userId]);
        $db->prepare("UPDATE user_addresses SET is_default=1 WHERE address_id=? AND user_id=?")->execute([$addrId,$userId]);
        $success = 'ตั้งเป็นที่อยู่หลักแล้ว';
    }
}

if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM user_addresses WHERE address_id=? AND user_id=?");
    $s->execute([(int)$_GET['edit'], $userId]);
    $editing = $s->fetch();
}

$showForm = isset($_GET['add']) || $editing;
$addrStmt = $db->prepare("SELECT * FROM user_addresses WHERE user_id=? ORDER BY is_default DESC, address_id");
$addrStmt->execute([$userId]);
$addresses = $addrStmt->fetchAll();

$thaiProvinces = ['กรุงเทพมหานคร','กระบี่','กาญจนบุรี','กาฬสินธุ์','กำแพงเพชร','ขอนแก่น','จันทบุรี','ฉะเชิงเทรา','ชลบุรี','ชัยนาท','ชัยภูมิ','ชุมพร','เชียงราย','เชียงใหม่','ตรัง','ตราด','ตาก','นครนายก','นครปฐม','นครพนม','นครราชสีมา','นครศรีธรรมราช','นครสวรรค์','นนทบุรี','นราธิวาส','น่าน','บึงกาฬ','บุรีรัมย์','ปทุมธานี','ประจวบคีรีขันธ์','ปราจีนบุรี','ปัตตานี','พระนครศรีอยุธยา','พะเยา','พังงา','พัทลุง','พิจิตร','พิษณุโลก','เพชรบุรี','เพชรบูรณ์','แพร่','ภูเก็ต','มหาสารคาม','มุกดาหาร','แม่ฮ่องสอน','ยโสธร','ยะลา','ร้อยเอ็ด','ระนอง','ระยอง','ราชบุรี','ลพบุรี','ลำปาง','ลำพูน','เลย','ศรีสะเกษ','สกลนคร','สงขลา','สตูล','สมุทรปราการ','สมุทรสงคราม','สมุทรสาคร','สระแก้ว','สระบุรี','สิงห์บุรี','สุโขทัย','สุพรรณบุรี','สุราษฎร์ธานี','สุรินทร์','หนองคาย','หนองบัวลำภู','อ่างทอง','อำนาจเจริญ','อุดรธานี','อุตรดิตถ์','อุทัยธานี','อุบลราชธานี'];

$pageTitle = 'ที่อยู่ของฉัน';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container-xl py-3">
  <div class="row g-3">
    <div class="col-md-3">
      <?php include __DIR__ . '/includes/account_sidebar.php'; ?>
    </div>
    <div class="col-md-9">
      <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

      <?php if ($showForm): ?>
      <!-- Add/Edit Form -->
      <div class="surface mb-3">
        <h5 class="fw-bold mb-4"><?= $editing ? 'แก้ไขที่อยู่' : 'เพิ่มที่อยู่ใหม่' ?></h5>
        <form method="POST">
          <input type="hidden" name="action" value="save">
          <?php if ($editing): ?><input type="hidden" name="address_id" value="<?= $editing['address_id'] ?>"><?php endif; ?>
          <?php if ($redirect): ?><input type="hidden" name="redirect" value="<?= e($redirect) ?>"><?php endif; ?>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">ชื่อที่อยู่</label>
              <select name="label" class="form-select">
                <?php foreach (['Home'=>'บ้าน','Office'=>'ที่ทำงาน','Other'=>'อื่นๆ'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($editing['label']??'Home')===$v?'selected':'' ?>><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">ชื่อผู้รับ <span class="text-danger">*</span></label>
              <input type="text" name="recipient_name" class="form-control" value="<?= e($editing['recipient_name']??'') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
              <input type="tel" name="phone" class="form-control" value="<?= e($editing['phone']??'') ?>" placeholder="0xxxxxxxxx" required>
            </div>
            <div class="col-12">
              <label class="form-label">ที่อยู่ (บ้านเลขที่ ถนน ซอย) <span class="text-danger">*</span></label>
              <input type="text" name="address_line1" class="form-control" value="<?= e($editing['address_line1']??'') ?>" required>
            </div>
            <div class="col-12">
              <label class="form-label">ที่อยู่เพิ่มเติม (ชั้น อาคาร)</label>
              <input type="text" name="address_line2" class="form-control" value="<?= e($editing['address_line2']??'') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">อำเภอ/เขต <span class="text-danger">*</span></label>
              <input type="text" name="district" class="form-control" value="<?= e($editing['district']??'') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">จังหวัด <span class="text-danger">*</span></label>
              <select name="province" class="form-select" required>
                <option value="">เลือกจังหวัด</option>
                <?php foreach ($thaiProvinces as $p): ?>
                <option value="<?= $p ?>" <?= ($editing['province']??'')===$p?'selected':'' ?>><?= $p ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">รหัสไปรษณีย์</label>
              <input type="text" name="postal_code" class="form-control" value="<?= e($editing['postal_code']??'') ?>" maxlength="5" placeholder="10xxx">
            </div>
            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" name="is_default" id="isDefault" class="form-check-input" <?= ($editing['is_default']??0)?'checked':'' ?>>
                <label for="isDefault" class="form-check-label">ตั้งเป็นที่อยู่หลัก</label>
              </div>
            </div>
            <div class="col-12 d-flex gap-2">
              <button type="submit" class="btn btn-orange px-5">บันทึก</button>
              <a href="/webshop/account/addresses.php<?= $redirect?'?redirect='.urlencode($redirect):'' ?>" class="btn btn-outline-secondary px-4">ยกเลิก</a>
            </div>
          </div>
        </form>
      </div>
      <?php endif; ?>

      <!-- Address List -->
      <div class="surface">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2 text-orange"></i>ที่อยู่ทั้งหมด (<?= count($addresses) ?>/20)</h5>
          <?php if (!$showForm && count($addresses) < 20): ?>
          <a href="?add=1<?= $redirect?'&redirect='.urlencode($redirect):'' ?>" class="btn btn-orange btn-sm">
            <i class="bi bi-plus-circle me-1"></i>เพิ่มที่อยู่
          </a>
          <?php endif; ?>
        </div>

        <?php if (empty($addresses)): ?>
        <div class="empty-state py-4">
          <div class="empty-icon"><i class="bi bi-geo-alt"></i></div>
          <h5>ยังไม่มีที่อยู่</h5>
          <a href="?add=1" class="btn btn-orange">เพิ่มที่อยู่แรก</a>
        </div>
        <?php else: ?>
        <div class="row g-3">
          <?php foreach ($addresses as $addr): ?>
          <div class="col-12">
            <div class="border rounded p-3 <?= $addr['is_default']?'border-orange':'border' ?>" style="<?= $addr['is_default']?'border-color:var(--shopee-orange)!important;background:var(--shopee-orange-light)':'' ?>">
              <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-secondary"><?= e($addr['label']) ?></span>
                    <?php if ($addr['is_default']): ?>
                    <span class="badge" style="background:var(--shopee-orange)">ที่อยู่หลัก</span>
                    <?php endif; ?>
                    <span class="fw-semibold"><?= e($addr['recipient_name']) ?></span>
                    <span class="text-muted"><?= e($addr['phone']) ?></span>
                  </div>
                  <div class="text-muted" style="font-size:13px">
                    <?= e($addr['address_line1']) ?>
                    <?php if ($addr['address_line2']): ?>, <?= e($addr['address_line2']) ?><?php endif; ?>,
                    <?= e($addr['district']) ?>, <?= e($addr['province']) ?> <?= e($addr['postal_code']) ?>
                  </div>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                  <?php if (!$addr['is_default']): ?>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="set_default">
                    <input type="hidden" name="address_id" value="<?= $addr['address_id'] ?>">
                    <button class="btn btn-sm btn-outline-orange">ตั้งเป็นหลัก</button>
                  </form>
                  <?php endif; ?>
                  <a href="?edit=<?= $addr['address_id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="address_id" value="<?= $addr['address_id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบที่อยู่นี้?')"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
