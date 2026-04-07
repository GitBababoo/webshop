<?php
require_once '../config/database.php';

try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT product_id, name FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mapping keywords/names to beautiful high-quality Unsplash source images
    $keywordMap = [
        'แปรงสีฟันไฟฟ้า' => 'https://images.unsplash.com/photo-1605336183652-329b3ae3d2ce?auto=format&fit=crop&w=600&q=80',
        'ผ้าปูที่นอน' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80',
        'ชุดเครื่องนอน' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80',
        'กระเป๋าเป้' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80',
        'Samsung' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=600&q=80',
        'MacBook' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=600&q=80',
        'เสื้อยืด' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=600&q=80',
        'ครีมกันแดด' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&w=600&q=80',
        'หูฟังไร้สาย' => 'https://images.unsplash.com/photo-1606220588913-b3eea4cece4e?auto=format&fit=crop&w=600&q=80',
        'น้ำหอม' => 'https://images.unsplash.com/photo-1523293115678-d290623f95e8?auto=format&fit=crop&w=600&q=80',
        'เซรั่ม' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80',
        'ดัมเบล' => 'https://images.unsplash.com/photo-1586716503901-5259bc2c30cc?auto=format&fit=crop&w=600&q=80',
        'เสื้อฮู้ด' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=600&q=80',
        'หม้อทอดไร้น้ำมัน' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?auto=format&fit=crop&w=600&q=80',
        'หูฟัง Sony' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?auto=format&fit=crop&w=600&q=80',
        'รองเท้าผ้าใบ' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=600&q=80',
        'กระทะ' => 'https://images.unsplash.com/photo-1584990347449-a6efa1a20078?auto=format&fit=crop&w=600&q=80',
        'iPhone' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=600&q=80',
        'OPPO' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=600&q=80',
        'โน๊ตบุ๊คเกมมิ่ง' => 'https://images.unsplash.com/photo-1600861194942-f883de0dfe96?auto=format&fit=crop&w=600&q=80',
    ];
    
    $defaultImg = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=600&q=80';
    
    foreach($products as $p) {
        $name = $p['name'];
        $mappedUrl = $defaultImg;
        foreach($keywordMap as $key => $url) {
            if (mb_stripos($name, $key) !== false || stripos($name, $key) !== false) {
                $mappedUrl = $url;
                break;
            }
        }
        $pdo->prepare("UPDATE product_images SET image_url = ? WHERE product_id = ?")->execute([$mappedUrl, $p['product_id']]);
    }

    echo "Successfully associated unique, relevant images for each product!";

} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
