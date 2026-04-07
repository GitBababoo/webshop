<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
header('Content-Type: application/json; charset=utf-8');

if (!frontIsLoggedIn()) {
    echo json_encode(['success'=>false,'redirect'=>'/webshop/account/login.php']);
    exit;
}

$userId    = (int)$_SESSION['front_user_id'];
$action    = $_POST['action'] ?? '';
$productId = (int)($_POST['product_id'] ?? 0);

if (!$productId) { echo json_encode(['success'=>false,'message'=>'ไม่พบสินค้า']); exit; }

try {
    if ($action === 'toggle') {
        $result = toggleWishlistDB($userId, $productId);
        echo json_encode($result);
    } elseif ($action === 'add') {
        getDB()->prepare("INSERT IGNORE INTO wishlists (user_id,product_id) VALUES (?,?)")->execute([$userId,$productId]);
        echo json_encode(['success'=>true,'in_wishlist'=>true]);
    } elseif ($action === 'remove') {
        getDB()->prepare("DELETE FROM wishlists WHERE user_id=? AND product_id=?")->execute([$userId,$productId]);
        echo json_encode(['success'=>true,'in_wishlist'=>false]);
    } elseif ($action === 'check') {
        $stmt = getDB()->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id=? AND product_id=?");
        $stmt->execute([$userId,$productId]);
        echo json_encode(['success'=>true,'in_wishlist'=>(bool)$stmt->fetchColumn()]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
