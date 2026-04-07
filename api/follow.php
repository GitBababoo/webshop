<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
header('Content-Type: application/json; charset=utf-8');

if (!frontIsLoggedIn()) {
    echo json_encode(['success'=>false,'redirect'=>'/webshop/account/login.php']);
    exit;
}

$userId = (int)$_SESSION['front_user_id'];
$action = $_POST['action'] ?? '';
$shopId = (int)($_POST['shop_id'] ?? 0);

if (!$shopId) { echo json_encode(['success'=>false,'message'=>'ไม่พบร้านค้า']); exit; }

try {
    if ($action === 'toggle' || $action === 'follow') {
        $db = getDB();
        $check = $db->prepare("SELECT COUNT(*) FROM shop_followers WHERE user_id=? AND shop_id=?");
        $check->execute([$userId,$shopId]);
        $following = (bool)$check->fetchColumn();
        if ($following) {
            $db->prepare("DELETE FROM shop_followers WHERE user_id=? AND shop_id=?")->execute([$userId,$shopId]);
            echo json_encode(['success'=>true,'following'=>false,'message'=>'เลิกติดตามแล้ว']);
        } else {
            $db->prepare("INSERT IGNORE INTO shop_followers (user_id,shop_id) VALUES (?,?)")->execute([$userId,$shopId]);
            echo json_encode(['success'=>true,'following'=>true,'message'=>'ติดตามสำเร็จ']);
        }
    } elseif ($action === 'unfollow') {
        getDB()->prepare("DELETE FROM shop_followers WHERE user_id=? AND shop_id=?")->execute([$userId,$shopId]);
        echo json_encode(['success'=>true,'following'=>false]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
