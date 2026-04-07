<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
frontRequireLogin('/webshop/account/wallet.php');

$userId = (int)$_SESSION['front_user_id'];
$db     = getDB();

$walletStmt = $db->prepare("SELECT * FROM wallets WHERE user_id=?");
$walletStmt->execute([$userId]);
$wallet = $walletStmt->fetch();
if (!$wallet) {
    $db->prepare("INSERT IGNORE INTO wallets (user_id,balance,coins) VALUES (?,0,0)")->execute([$userId]);
    $walletStmt->execute([$userId]);
    $wallet = $walletStmt->fetch();
}

$loyaltyStmt = $db->prepare("SELECT * FROM loyalty_points WHERE user_id=?");
$loyaltyStmt->execute([$userId]);
$loyalty = $loyaltyStmt->fetch() ?: ['total_points'=>0,'used_points'=>0,'tier'=>'bronze'];

$txnStmt = $db->prepare("SELECT * FROM wallet_transactions WHERE wallet_id=? ORDER BY created_at DESC LIMIT 20");
$txnStmt->execute([$wallet['wallet_id']]);
$transactions = $txnStmt->fetchAll();

$lyTxnStmt = $db->prepare("SELECT * FROM loyalty_transactions WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$lyTxnStmt->execute([$userId]);
$loyaltyTxns = $lyTxnStmt->fetchAll();

$tierColors = ['bronze'=>'#cd7f32','silver'=>'#aaa','gold'=>'#ffc107','platinum'=>'#6fd4c4','diamond'=>'#74b9ff'];
$tierNext   = ['bronze'=>'Silver','silver'=>'Gold','gold'=>'Platinum','platinum'=>'Diamond','diamond'=>'MAX'];
$tierPts    = ['bronze'=>100,'silver'=>500,'gold'=>2000,'platinum'=>5000,'diamond'=>10000];
$currentPts = (int)$loyalty['total_points'] - (int)$loyalty['used_points'];
$nextPts    = $tierPts[$loyalty['tier']] ?? 100;
$pct        = min(100, round($currentPts / $nextPts * 100));

$pageTitle = 'กระเป๋าเงิน';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container-xl py-3">
  <div class="row g-3">
    <div class="col-md-3">
      <?php include __DIR__ . '/includes/account_sidebar.php'; ?>
    </div>
    <div class="col-md-9">
      <!-- Wallet Cards -->
      <div class="row g-3 mb-3">
        <div class="col-sm-6">
          <div class="surface h-100" style="background:linear-gradient(135deg,#ee4d2d,#ff6a00);color:#fff;border:none">
            <div class="d-flex align-items-center gap-2 mb-1 opacity-80"><i class="bi bi-wallet2"></i><span style="font-size:13px">Shopee Pay</span></div>
            <div style="font-size:32px;font-weight:700">฿<?= number_format((float)$wallet['balance'],2) ?></div>
            <div class="mt-3 d-flex gap-2">
              <button class="btn btn-sm btn-light" onclick="showToast('ฟีเจอร์นี้กำลังพัฒนา','info')"><i class="bi bi-plus-circle me-1"></i>เติมเงิน</button>
              <button class="btn btn-sm btn-outline-light" onclick="showToast('ฟีเจอร์นี้กำลังพัฒนา','info')"><i class="bi bi-arrow-up-right me-1"></i>ถอนเงิน</button>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="surface h-100" style="background:linear-gradient(135deg,#ffc107,#ff9800);color:#333;border:none">
            <div class="d-flex align-items-center gap-2 mb-1 opacity-80"><i class="bi bi-coin"></i><span style="font-size:13px">Shopee Coins</span></div>
            <div style="font-size:32px;font-weight:700"><?= number_format((float)$wallet['coins'],0) ?></div>
            <div style="font-size:12px;opacity:.8;margin-top:4px">≈ ฿<?= number_format((float)$wallet['coins'] * 0.01, 2) ?> มูลค่า</div>
            <div class="mt-3">
              <button class="btn btn-sm btn-dark" onclick="showToast('ฟีเจอร์นี้กำลังพัฒนา','info')"><i class="bi bi-arrow-right-circle me-1"></i>แลกเป็นส่วนลด</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loyalty / Member Tier -->
      <div class="surface mb-3">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div style="width:56px;height:56px;border-radius:50%;background:<?= $tierColors[$loyalty['tier']] ?? '#ccc' ?>;display:flex;align-items:center;justify-content:center;font-size:24px">
            <?= $loyalty['tier']==='diamond'?'💎':($loyalty['tier']==='platinum'?'🏆':($loyalty['tier']==='gold'?'🥇':($loyalty['tier']==='silver'?'🥈':'🥉'))) ?>
          </div>
          <div class="flex-fill">
            <div class="fw-bold" style="font-size:16px"><?= ucfirst($loyalty['tier']) ?> Member</div>
            <div class="text-muted" style="font-size:13px">Points: <strong class="text-dark"><?= number_format($currentPts) ?></strong> คะแนน</div>
          </div>
          <div class="text-end">
            <div class="text-muted" style="font-size:12px">เป้าหมายระดับถัดไป</div>
            <div class="fw-bold text-orange"><?= $tierNext[$loyalty['tier']] ?></div>
          </div>
        </div>
        <!-- Progress bar -->
        <div class="d-flex justify-content-between mb-1" style="font-size:12px;color:#999">
          <span><?= ucfirst($loyalty['tier']) ?></span>
          <span><?= number_format($currentPts) ?> / <?= number_format($nextPts) ?> คะแนน</span>
          <span><?= $tierNext[$loyalty['tier']] ?></span>
        </div>
        <div class="progress" style="height:10px;border-radius:5px">
          <div class="progress-bar" style="width:<?= $pct ?>%;background:<?= $tierColors[$loyalty['tier']] ?>"></div>
        </div>
        <div class="row g-3 mt-2 text-center" style="font-size:12px;color:#666">
          <div class="col-4"><div class="fw-bold text-dark"><?= number_format((int)$loyalty['total_points']) ?></div>คะแนนสะสม</div>
          <div class="col-4"><div class="fw-bold text-dark"><?= number_format((int)$loyalty['used_points']) ?></div>ใช้ไปแล้ว</div>
          <div class="col-4"><div class="fw-bold text-orange"><?= number_format($currentPts) ?></div>คะแนนคงเหลือ</div>
        </div>
      </div>

      <!-- Tabs: Transactions -->
      <div class="surface">
        <ul class="nav nav-tabs mb-3">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#walletTab">ประวัติ Shopee Pay</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#loyaltyTab">ประวัติ Coins</button></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="walletTab">
            <?php if (empty($transactions)): ?>
            <div class="text-center py-4 text-muted"><i class="bi bi-receipt d-block fs-1 mb-2 opacity-25"></i>ยังไม่มีรายการ</div>
            <?php else: ?>
            <?php foreach ($transactions as $txn):
              $isIn = in_array($txn['type'],['topup','refund','cashback','bonus']);
            ?>
            <div class="d-flex align-items-center gap-3 py-3 border-bottom">
              <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px;background:<?= $isIn?'#e8f5e9':'#ffebee' ?>">
                <i class="bi <?= $isIn?'bi-arrow-down-circle-fill text-success':'bi-arrow-up-circle-fill text-danger' ?> fs-5"></i>
              </div>
              <div class="flex-fill">
                <div class="fw-semibold" style="font-size:14px"><?= e($txn['description'] ?? ucfirst($txn['type'])) ?></div>
                <div class="text-muted" style="font-size:12px"><?= formatDate($txn['created_at'],'d M Y H:i') ?></div>
              </div>
              <div class="fw-bold <?= $isIn?'text-success':'text-danger' ?>">
                <?= $isIn?'+':'-' ?>฿<?= number_format(abs((float)$txn['amount']),2) ?>
              </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <div class="tab-pane fade" id="loyaltyTab">
            <?php if (empty($loyaltyTxns)): ?>
            <div class="text-center py-4 text-muted"><i class="bi bi-coin d-block fs-1 mb-2 opacity-25"></i>ยังไม่มีรายการ</div>
            <?php else: ?>
            <?php foreach ($loyaltyTxns as $lt):
              $isIn = $lt['points'] > 0;
            ?>
            <div class="d-flex align-items-center gap-3 py-3 border-bottom">
              <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px;background:<?= $isIn?'#fff8e1':'#ffebee' ?>">
                <i class="bi bi-coin" style="color:<?= $isIn?'#ffc107':'#f44336' ?>;font-size:20px"></i>
              </div>
              <div class="flex-fill">
                <div class="fw-semibold" style="font-size:14px"><?= e($lt['description'] ?? ucfirst($lt['type'])) ?></div>
                <div class="text-muted" style="font-size:12px"><?= formatDate($lt['created_at'],'d M Y H:i') ?></div>
              </div>
              <div class="fw-bold" style="color:<?= $isIn?'#ffc107':'#f44336' ?>">
                <?= $isIn?'+':'' ?><?= number_format((int)$lt['points']) ?> coins
              </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
