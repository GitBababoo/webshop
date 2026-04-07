<?php
set_time_limit(300);
require_once dirname(__DIR__) . '/config/database.php';

$baseDir = dirname(__DIR__);

// Helper to ensure a local file exists. Returns the web path.
function ensureLocalFile($webPath, $sourceUrl) {
    global $baseDir;
    $localPath = $baseDir . $webPath;
    
    if (!is_dir(dirname($localPath))) {
        mkdir(dirname($localPath), 0777, true);
    }
    
    // Always redownload or copy to ensure it exists physically
    $ch = curl_init($sourceUrl);
    $fp = fopen($localPath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    
    return $webPath; // e.g. /uploads/products/product_1.jpg
}

try {
    $pdo = getDB();
    
    // 1. PRODUCTS (Map relevant source URLs to download)
    $keywordMap = [
        'แปรงสีฟันไฟฟ้า' => 'https://images.unsplash.com/photo-1605336183652-329b3ae3d2ce?auto=format&fit=crop&w=600&q=80',
        'ผ้าปูที่นอน' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80',
        'ชุดเครื่องนอน' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80',
        'กระเป๋าเป้' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80',
        'Samsung' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=600&q=80',
        'MacBook' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=600&q=80',
        'เสื้อยืด' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=600&q=80',
        'ครีมกันแดด' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&w=600&q=80',
        'หูฟังไร้สาย' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=600&q=80',
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
    $defaultProd = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=600&q=80';

    $stmt = $pdo->query("SELECT p.product_id, p.name, pi.image_id FROM products p JOIN product_images pi ON p.product_id = pi.product_id");
    $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($prods as $p) {
        $source = $defaultProd;
        foreach($keywordMap as $key => $url) {
            if (mb_stripos($p['name'], $key) !== false || stripos($p['name'], $key) !== false) {
                $source = $url;
                break;
            }
        }
        $webPath = "/uploads/products/product_" . $p['product_id'] . ".jpg";
        ensureLocalFile($webPath, $source);
        // FORCE the DB to exact local path
        $pdo->prepare("UPDATE product_images SET image_url = ? WHERE image_id = ?")->execute([$webPath, $p['image_id']]);
    }
    echo "Products synced directly to DB local paths.\n";

    // 2. CATEGORIES
    $catMap = [
        'Electronics' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&q=80',
        'Fashion' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&q=80',
        'Default' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=400&q=80'
    ];
    $stmt = $pdo->query("SELECT category_id, name FROM categories");
    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($cats as $c) {
        $source = isset($catMap[$c['name']]) ? $catMap[$c['name']] : $catMap['Default'];
        $webPath = "/uploads/categories/category_" . $c['category_id'] . ".jpg";
        ensureLocalFile($webPath, $source);
        $pdo->prepare("UPDATE categories SET image_url = ? WHERE category_id = ?")->execute([$webPath, $c['category_id']]);
    }
    echo "Categories synced directly to DB local paths.\n";

    // 3. BANNERS
    $bannerSources = [
        'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&q=80',
        'https://images.unsplash.com/photo-1607082349566-187342175e2f?w=1200&q=80',
        'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&q=80'
    ];
    $stmt = $pdo->query("SELECT banner_id FROM banners ORDER BY sort_order");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($banners as $i => $b) {
        $source = $bannerSources[$i % count($bannerSources)];
        $webPath = "/uploads/banners/banner_" . $b['banner_id'] . ".jpg";
        ensureLocalFile($webPath, $source);
        $pdo->prepare("UPDATE banners SET image_url = ? WHERE banner_id = ?")->execute([$webPath, $b['banner_id']]);
    }
    echo "Banners synced directly to DB local paths.\n";

    echo "ALL DONE. Database perfectly matches local uploads.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
