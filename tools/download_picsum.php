<?php
/**
 * Download Images - Using Lorem Picsum (Stable Service)
 * Run: C:\xampp\php\php.exe tools/download_picsum.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== DOWNLOAD IMAGES (Lorem Picsum) ===\n\n";

$db = getDB();

// Create upload directories
$uploadDirs = [
    __DIR__ . '/../uploads/products',
    __DIR__ . '/../uploads/products/thumbs',
    __DIR__ . '/../uploads/categories',
    __DIR__ . '/../uploads/shops',
    __DIR__ . '/../uploads/shops/logos',
    __DIR__ . '/../uploads/banners',
    __DIR__ . '/../uploads/flashsales',
    __DIR__ . '/../uploads/users',
];

foreach ($uploadDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created: " . basename($dir) . "\n";
    }
}
echo "\n";

function downloadImage(string $url, string $savePath): bool {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode === 200 && $imageData !== false && strlen($imageData) > 1000) {
        return file_put_contents($savePath, $imageData) !== false;
    }
    return false;
}

// Product images with specific seeds for consistent images
echo "[1] Downloading PRODUCT images (3 per product)...\n";
$products = $db->query("SELECT product_id, name FROM products ORDER BY product_id")->fetchAll();
$updated = 0;
$successCount = 0;

foreach ($products as $prod) {
    $productId = $prod['product_id'];
    $imagesSaved = [];
    
    for ($imgNum = 1; $imgNum <= 3; $imgNum++) {
        // Use seeded images from Lorem Picsum for consistency
        $seed = ($productId * 100) + $imgNum;
        $imageUrl = "https://picsum.photos/seed/{$seed}/400/400.jpg";
        
        $filename = sprintf("product_%03d_%d.jpg", $productId, $imgNum);
        $filepath = __DIR__ . '/../uploads/products/' . $filename;
        $webpath = '/uploads/products/' . $filename;
        
        echo "  Product {$productId} image {$imgNum}... ";
        
        if (downloadImage($imageUrl, $filepath)) {
            echo "✓\n";
            $imagesSaved[] = ['path' => $filepath, 'web' => $webpath, 'num' => $imgNum];
            $successCount++;
        } else {
            echo "✗\n";
        }
        
        usleep(200000); // 200ms delay
    }
    
    // Update database
    if (!empty($imagesSaved)) {
        try {
            $db->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$productId]);
            
            foreach ($imagesSaved as $img) {
                $isPrimary = $img['num'] === 1 ? 1 : 0;
                $sortOrder = $img['num'] - 1;
                $stmt = $db->prepare("INSERT INTO product_images (product_id, image_url, is_primary, sort_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$productId, $img['web'], $isPrimary, $sortOrder]);
            }
            
            echo "  ✓ Saved " . count($imagesSaved) . " images to DB\n";
            $updated++;
        } catch (Exception $e) {
            echo "  ✗ DB Error: {$e->getMessage()}\n";
        }
    }
}

echo "\n✓ Products: {$updated} updated, {$successCount} images downloaded\n";

// Category images
echo "\n[2] Downloading CATEGORY images...\n";
$categories = $db->query("SELECT category_id FROM categories ORDER BY category_id")->fetchAll();
$catUpdated = 0;

foreach ($categories as $cat) {
    $catId = $cat['category_id'];
    $seed = $catId * 1000;
    $imageUrl = "https://picsum.photos/seed/{$seed}/800/400.jpg";
    
    $filename = sprintf("category_%03d.jpg", $catId);
    $filepath = __DIR__ . '/../uploads/categories/' . $filename;
    
    echo "  Category {$catId}... ";
    if (downloadImage($imageUrl, $filepath)) {
        echo "✓\n";
        $catUpdated++;
    } else {
        echo "✗\n";
    }
    
    usleep(200000);
}
echo "\n✓ Categories: {$catUpdated} images downloaded\n";

// Shop logos
echo "\n[3] Downloading SHOP logos...\n";
$shops = $db->query("SELECT shop_id FROM shops ORDER BY shop_id")->fetchAll();
$shopUpdated = 0;

foreach ($shops as $shop) {
    $shopId = $shop['shop_id'];
    $seed = $shopId * 5000;
    $imageUrl = "https://picsum.photos/seed/{$seed}/200/200.jpg";
    
    $filename = sprintf("shop_%03d_logo.jpg", $shopId);
    $filepath = __DIR__ . '/../uploads/shops/logos/' . $filename;
    $webpath = '/uploads/shops/logos/' . $filename;
    
    echo "  Shop {$shopId}... ";
    if (downloadImage($imageUrl, $filepath)) {
        echo "✓\n";
        
        $stmt = $db->prepare("UPDATE shops SET logo_url = ? WHERE shop_id = ?");
        $stmt->execute([$webpath, $shopId]);
        
        $shopUpdated++;
    } else {
        echo "✗\n";
    }
    
    usleep(200000);
}
echo "\n✓ Shops: {$shopUpdated} logos downloaded\n";

// Banner images
echo "\n[4] Downloading BANNER images...\n";
$banners = $db->query("SELECT banner_id FROM banners ORDER BY banner_id")->fetchAll();
$bannerUpdated = 0;

foreach ($banners as $banner) {
    $bannerId = $banner['banner_id'];
    $seed = $bannerId * 10000;
    $imageUrl = "https://picsum.photos/seed/{$seed}/1200/400.jpg";
    
    $filename = sprintf("banner_%03d.jpg", $bannerId);
    $filepath = __DIR__ . '/../uploads/banners/' . $filename;
    $webpath = '/uploads/banners/' . $filename;
    
    echo "  Banner {$bannerId}... ";
    if (downloadImage($imageUrl, $filepath)) {
        echo "✓\n";
        
        $stmt = $db->prepare("UPDATE banners SET image_url = ? WHERE banner_id = ?");
        $stmt->execute([$webpath, $bannerId]);
        
        $bannerUpdated++;
    } else {
        echo "✗\n";
    }
    
    usleep(200000);
}
echo "\n✓ Banners: {$bannerUpdated} images downloaded\n";

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "DOWNLOAD COMPLETE\n";
echo str_repeat("=", 50) . "\n";
echo "Total images downloaded: " . ($successCount + $catUpdated + $shopUpdated + $bannerUpdated) . "\n";
echo "\nFile structure:\n";
echo "  uploads/products/     : product_XXX_1.jpg to product_XXX_3.jpg\n";
echo "  uploads/categories/   : category_XXX.jpg\n";
echo "  uploads/shops/logos/  : shop_XXX_logo.jpg\n";
echo "  uploads/banners/      : banner_XXX.jpg\n";
echo "\nAll images are from Lorem Picsum (placeholder service)\n";
