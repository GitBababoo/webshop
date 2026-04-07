<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
header('Content-Type: application/json; charset=utf-8');

$action    = $_POST['action'] ?? $_GET['action'] ?? '';
$userId    = frontIsLoggedIn() ? (int)$_SESSION['front_user_id'] : null;

try {
    switch ($action) {
        case 'add':
            $productId = (int)($_POST['product_id'] ?? 0);
            $qty       = max(1, (int)($_POST['quantity'] ?? 1));
            $skuId     = (int)($_POST['sku_id'] ?? 0) ?: null;
            if (!$productId) { echo json_encode(['success'=>false,'message'=>'ไม่พบสินค้า']); exit; }

            if ($userId) {
                $result = addToCartDB($userId, $productId, $qty, $skuId);
            } else {
                // Guest cart in session
                if (session_status() === PHP_SESSION_NONE) session_start();
                $stmt = getDB()->prepare("SELECT product_id,name,base_price,discount_price,total_stock FROM products WHERE product_id=? AND status='active'");
                $stmt->execute([$productId]);
                $prod = $stmt->fetch();
                if (!$prod) { echo json_encode(['success'=>false,'message'=>'สินค้าไม่พร้อมจำหน่าย']); exit; }
                $cart = $_SESSION['guest_cart'] ?? [];
                $found = false;
                foreach ($cart as &$item) {
                    if ($item['product_id'] == $productId) { $item['quantity'] = min($item['quantity']+$qty, $prod['total_stock']); $found=true; break; }
                }
                if (!$found) $cart[] = ['product_id'=>$productId,'quantity'=>$qty,'sku_id'=>$skuId,'name'=>$prod['name'],'price'=>$prod['discount_price']?:$prod['base_price']];
                $_SESSION['guest_cart'] = $cart;
                $result = ['success'=>true,'cart_count'=>array_sum(array_column($cart,'quantity'))];
            }
            echo json_encode($result); break;

        case 'update':
            $itemId = (int)($_POST['cart_item_id'] ?? 0);
            $qty    = max(1, (int)($_POST['quantity'] ?? 1));
            if (!$userId || !$itemId) { echo json_encode(['success'=>false]); exit; }
            $db     = getDB();
            // Verify ownership
            $check = $db->prepare("SELECT ci.cart_item_id, ci.product_id, p.base_price, p.discount_price, p.total_stock FROM cart_items ci JOIN carts c ON ci.cart_id=c.cart_id JOIN products p ON ci.product_id=p.product_id WHERE ci.cart_item_id=? AND c.user_id=?");
            $check->execute([$itemId, $userId]);
            $item = $check->fetch();
            if (!$item) { echo json_encode(['success'=>false,'message'=>'ไม่พบรายการ']); exit; }
            $qty = min($qty, (int)$item['total_stock']);
            $db->prepare("UPDATE cart_items SET quantity=? WHERE cart_item_id=?")->execute([$qty,$itemId]);
            $price = (float)($item['discount_price'] ?: $item['base_price']);
            $cartTotal = getCartTotal($userId);
            echo json_encode(['success'=>true,'item_subtotal'=>$price*$qty,'cart_total'=>$cartTotal,'cart_count'=>getCartCount($userId)]); break;

        case 'remove':
            $itemId = (int)($_POST['cart_item_id'] ?? 0);
            if (!$userId || !$itemId) { echo json_encode(['success'=>false]); exit; }
            $db = getDB();
            $db->prepare("DELETE ci FROM cart_items ci JOIN carts c ON ci.cart_id=c.cart_id WHERE ci.cart_item_id=? AND c.user_id=?")->execute([$itemId,$userId]);
            echo json_encode(['success'=>true,'cart_total'=>getCartTotal($userId),'cart_count'=>getCartCount($userId)]); break;

        case 'count':
            echo json_encode(['count'=>getCartCount($userId)]); break;

        default:
            echo json_encode(['success'=>false,'message'=>'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Server error: '.$e->getMessage()]);
}
