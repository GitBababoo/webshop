<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
frontRequireLogin();

$action   = $_POST['action'] ?? '';
$orderId  = (int)($_POST['order_id'] ?? 0);
$userId   = (int)$_SESSION['front_user_id'];
$isAjax   = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json');

if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
}

try {
    $db = getDB();
    $chk = $db->prepare("SELECT order_status FROM orders WHERE order_id=? AND buyer_user_id=?");
    $chk->execute([$orderId, $userId]);
    $order = $chk->fetch();

    if (!$order) {
        if ($isAjax) { echo json_encode(['success'=>false,'message'=>'ไม่พบคำสั่งซื้อ']); exit; }
        header('Location: /webshop/account/orders.php'); exit;
    }

    if ($action === 'cancel' && $order['order_status'] === 'pending') {
        $db->prepare("UPDATE orders SET order_status='cancelled', cancelled_at=NOW() WHERE order_id=?")->execute([$orderId]);
        $db->prepare("INSERT INTO order_status_history (order_id,status,note,created_by) VALUES (?,'cancelled','ลูกค้ายกเลิกคำสั่งซื้อ',?)")->execute([$orderId,$userId]);
        if ($isAjax) { echo json_encode(['success'=>true,'message'=>'ยกเลิกสำเร็จ']); exit; }
        header("Location: /webshop/account/orders.php?cancelled=1"); exit;

    } elseif ($action === 'confirm_received' && $order['order_status'] === 'shipped') {
        $db->prepare("UPDATE orders SET order_status='delivered', delivered_at=NOW() WHERE order_id=?")->execute([$orderId]);
        $db->prepare("INSERT INTO order_status_history (order_id,status,note,created_by) VALUES (?,'delivered','ลูกค้ายืนยันรับสินค้า',?)")->execute([$orderId,$userId]);
        if ($isAjax) { echo json_encode(['success'=>true,'message'=>'ยืนยันรับสินค้าสำเร็จ']); exit; }
        header("Location: /webshop/account/order-detail.php?id=$orderId&received=1"); exit;

    } else {
        if ($isAjax) { echo json_encode(['success'=>false,'message'=>'ไม่สามารถดำเนินการได้']); exit; }
        header("Location: /webshop/account/orders.php"); exit;
    }
} catch (Exception $e) {
    if ($isAjax) { echo json_encode(['success'=>false,'message'=>'เกิดข้อผิดพลาด']); exit; }
    header('Location: /webshop/account/orders.php'); exit;
}
