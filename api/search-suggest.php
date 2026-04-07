<?php
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';
header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 1) { echo json_encode([]); exit; }

try {
    $db = getDB();
    // Products match
    $stmt = $db->prepare("SELECT name AS text, 'product' AS type, slug FROM products WHERE name LIKE ? AND status='active' LIMIT 5");
    $stmt->execute(["%$q%"]);
    $products = $stmt->fetchAll();
    // Categories match
    $stmt2 = $db->prepare("SELECT name AS text, 'category' AS type, slug FROM categories WHERE name LIKE ? AND is_active=1 LIMIT 3");
    $stmt2->execute(["%$q%"]);
    $cats = $stmt2->fetchAll();
    // Shops match
    $stmt3 = $db->prepare("SELECT shop_name AS text, 'shop' AS type, shop_slug AS slug FROM shops WHERE shop_name LIKE ? AND is_active=1 LIMIT 2");
    $stmt3->execute(["%$q%"]);
    $shops = $stmt3->fetchAll();
    $result = [
        'products'=> $products,
        'categories'=> $cats,
        'shops'=> $shops,
    ];
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error'=>'search error']);
}
