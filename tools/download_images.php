<?php
/**
 * Download & Upload Images to Local Server
 * Professional file organization with proper naming
 * Run: C:\xampp\php\php.exe tools/download_images.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== DOWNLOAD & ORGANIZE IMAGES ===\n\n";

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
        echo "Created: $dir\n";
    }
}
echo "\n";

// AI prompts for products
$aiPrompts = [
    1 => 'Samsung Galaxy S24 Ultra smartphone titanium gray professional product photo white background studio',
    2 => 'MacBook Air M3 laptop silver thin aluminum professional product photo white background',
    3 => 'Sony WH-1000XM5 premium wireless headphones black professional product photo white background',
    4 => 'Oversized pastel hoodie sweatshirt fashion product photo on hanger white background',
    5 => 'White chunky platform sneakers shoes fashion product photo white background',
    6 => 'Ceramic non-stick frying pan 28cm kitchen cookware professional product photo white background',
    7 => 'Premium cotton bed sheet set bedroom white soft fabric product photo',
    8 => 'iPhone 15 Pro Max titanium natural professional product photo white background studio',
    9 => 'OPPO Find X7 Ultra smartphone professional product photo white background studio lighting',
    10 => 'ASUS ROG Zephyrus gaming laptop RGB keyboard professional product photo',
    11 => 'iPhone 15 Pro Max 256GB titanium professional product photo',
    12 => 'OPPO Find X7 Ultra camera smartphone professional product photo',
    13 => 'ASUS ROG gaming laptop RTX4060 professional product photo',
    14 => 'Oversized pastel hoodie streetwear fashion product photo',
    15 => 'White chunky platform sneakers trendy shoes product photo',
    16 => 'Ceramic non-stick frying pan 28cm professional kitchen product photo',
    17 => 'Premium cotton bed sheet set cozy bedroom product photo',
    18 => 'iPhone 15 Pro Max smartphone professional product photo',
    19 => 'OPPO Find X7 smartphone professional product photo',
    20 => 'ASUS ROG gaming laptop professional product photo',
    21 => 'Vitamin C serum skincare bottle glass dropper cosmetic product photo white background',
    22 => 'Adjustable dumbbell 2-24kg fitness equipment chrome professional product photo',
    23 => 'Air fryer 5 liter digital black modern kitchen appliance product photo white background',
    24 => 'Crew neck t-shirt pack 3 basic cotton white product photo clothing',
    25 => 'Sunscreen cream SPF50 tube cosmetic product photo white background professional',
    26 => 'Wireless Bluetooth earbuds white modern tech product photo white background',
    27 => 'Perfume bottle 100ml luxury fragrance cosmetic product photo white background',
    28 => 'Electric toothbrush white modern dental care product photo white background',
    29 => 'Bed sheet set 6 feet cotton bedroom product photo',
    30 => 'Waterproof backpack laptop bag black modern travel product photo white background',
    31 => 'Samsung Galaxy S24 Ultra phantom black professional product photo studio',
    32 => 'MacBook Air M3 midnight black thin laptop professional product photo',
];

function downloadImage(string $url, string $savePath): bool {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $imageData !== false) {
        return file_put_contents($savePath, $imageData) !== false;
    }
    return false;
}

function generateAIImageUrl(string $prompt, int $seed, int $width = 400, int $height = 400): string {
    $encodedPrompt = urlencode($prompt);
    return "https://image.pollinations.ai/prompt/{$encodedPrompt}?width={$width}&height={$height}&nologo=true&seed={$seed}";
}

// Download product images
echo "[1] Downloading PRODUCT images...\n";
$products = $db->query("SELECT product_id, name, slug FROM products ORDER BY product_id")->fetchAll();
$updated = 0;
$failed = 0;

foreach ($products as $prod) {
    $productId = $prod['product_id'];
    $slug = $prod['slug'] ?: 'product-' . $productId;
    
    // Clean filename
    $cleanName = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $slug);
    $cleanName = trim($cleanName, '-');
    
    // Get prompt
    if (isset($aiPrompts[$productId])) {
        $prompt = $aiPrompts[$productId];
    } else {
        $cleanNameTh = str_replace(['กระเป๋า', 'เสื้อ', 'รองเท้า', 'หม้อ', 'กระทะ', 'ดัมเบล', 'ผ้า'], 
                                   ['backpack', 'shirt', 'shoes', 'pot', 'pan', 'dumbbell', 'fabric'], 
                                   $prod['name']);
        $prompt = $cleanNameTh . ' professional product photo white background studio lighting';
    }
    
    // Download 3 images (main + 2 additional)
    $imagesSaved = [];
    for ($imgNum = 1; $imgNum <= 3; $imgNum++) {
        $seed = ($productId * 100) + $imgNum;
        $variation = $imgNum === 1 ? '' : ($imgNum === 2 ? ' side view' : ' detail closeup');
        $imageUrl = generateAIImageUrl($prompt . $variation, $seed, 400, 400);
        
        $filename = sprintf("product_%03d_%d.jpg", $productId, $imgNum);
        $filepath = __DIR__ . '/../uploads/products/' . $filename;
        $webpath = '/uploads/products/' . $filename;
        
        echo "  Downloading {$filename}... ";
        
        if (downloadImage($imageUrl, $filepath)) {
            echo "✓\n";
            $imagesSaved[] = ['path' => $filepath, 'web' => $webpath, 'num' => $imgNum];
        } else {
            echo "✗ (using placeholder)\n";
            $failed++;
        }
        
        usleep(500000); // 500ms delay between downloads
    }
    
    // Update database
    try {
        // Clear old images
        $db->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$productId]);
        
        // Insert new images
        foreach ($imagesSaved as $img) {
            $isPrimary = $img['num'] === 1 ? 1 : 0;
            $sortOrder = $img['num'] - 1;
            $stmt = $db->prepare("INSERT INTO product_images (product_id, image_url, is_primary, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$productId, $img['web'], $isPrimary, $sortOrder]);
        }
        
        echo "  ✓ Product {$productId}: {$prod['name']} (" . count($imagesSaved) . " images)\n";
        $updated++;
        
    } catch (Exception $e) {
        echo "  ✗ DB Error: {$e->getMessage()}\n";
    }
}

echo "\n✓ Products: {$updated} updated, {$failed} failed\n";

// Download category images
echo "\n[2] Downloading CATEGORY images...\n";
$categoryPrompts = [
    1 => 'smartphone mobile technology devices category banner header',
    2 => 'laptop computer technology workspace category banner header',
    3 => 'mens fashion clothing style category banner header',
    4 => 'womens fashion dress elegant category banner header',
    5 => 'beauty cosmetics skincare makeup category banner header',
    6 => 'home furniture interior living category banner header',
    7 => 'food grocery fresh healthy category banner header',
    8 => 'sports fitness gym equipment category banner header',
    9 => 'headphones audio music tech category banner header',
    10 => 'shoes footwear fashion category banner header',
    11 => 'kitchen cookware cooking category banner header',
    12 => 'bedding bedroom sleep cozy category banner header',
];

$categories = $db->query("SELECT category_id, name, slug FROM categories ORDER BY category_id")->fetchAll();
$catUpdated = 0;

foreach ($categories as $cat) {
    $catId = $cat['category_id'];
    $prompt = $categoryPrompts[$catId] ?? 'category shopping ecommerce banner header';
    $seed = $catId * 1000;
    
    $filename = sprintf("category_%03d.jpg", $catId);
    $filepath = __DIR__ . '/../uploads/categories/' . $filename;
    $webpath = '/uploads/categories/' . $filename;
    
    $imageUrl = generateAIImageUrl($prompt, $seed, 800, 400);
    
    echo "  Downloading {$filename}... ";
    if (downloadImage($imageUrl, $filepath)) {
        echo "✓\n";
        $catUpdated++;
    } else {
        echo "✗\n";
    }
    
    usleep(300000);
}
echo "\n✓ Categories: {$catUpdated} images downloaded\n";

// Download shop logos
echo "\n[3] Downloading SHOP logos...\n";
$shopPrompts = [
    1 => 'skincare beauty cosmetic shop logo minimalist modern professional circular',
    2 => 'fitness gym sport energy shop logo dynamic modern professional circular',
    3 => 'home living cozy furniture shop logo warm modern professional circular',
    4 => 'fashion clothing boutique shop logo stylish modern professional circular',
    5 => 'technology electronics shop logo tech modern professional blue circular',
];

$shops = $db->query("SELECT shop_id, shop_slug FROM shops ORDER BY shop_id")->fetchAll();
$shopUpdated = 0;

foreach ($shops as $shop) {
    $shopId = $shop['shop_id'];
    $prompt = $shopPrompts[$shopId] ?? 'modern shop logo minimalist professional circular';
    $seed = $shopId * 5000;
    
    $filename = sprintf("shop_%03d_logo.jpg", $shopId);
    $filepath = __DIR__ . '/../uploads/shops/logos/' . $filename;
    $webpath = '/uploads/shops/logos/' . $filename;
    
    $imageUrl = generateAIImageUrl($prompt, $seed, 200, 200);
    
    echo "  Downloading {$filename}... ";
    if (downloadImage($imageUrl, $filepath)) {
        echo "✓\n";
        
        // Update shop logo in DB
        $stmt = $db->prepare("UPDATE shops SET logo_url = ? WHERE shop_id = ?");
        $stmt->execute([$webpath, $shopId]);
        
        $shopUpdated++;
    } else {
        echo "✗\n";
    }
    
    usleep(300000);
}
echo "\n✓ Shops: {$shopUpdated} logos downloaded\n";

// Download banner images
echo "\n[4] Downloading BANNER images...\n";
$bannerTitles = [
    1 => 'Flash Sale shopping discount banner promotion orange red',
    2 => 'Free shipping delivery shopping banner promotion blue',
    3 => 'New member discount shopping banner promotion purple',
];

$banners = $db->query("SELECT banner_id FROM banners ORDER BY banner_id")->fetchAll();
$bannerUpdated = 0;

foreach ($banners as $i => $banner) {
    $bannerId = $banner['banner_id'];
    $prompt = $bannerTitles[$bannerId] ?? 'shopping ecommerce promotion banner header';
    $seed = $bannerId * 10000;
    
    $filename = sprintf("banner_%03d.jpg", $bannerId);
    $filepath = __DIR__ . '/../uploads/banners/' . $filename;
    $webpath = '/uploads/banners/' . $filename;
    
    $imageUrl = generateAIImageUrl($prompt, $seed, 1200, 400);
    
    echo "  Downloading {$filename}... ";
    if (downloadImage($imageUrl, $filepath)) {
        echo "✓\n";
        
        // Update banner in DB
        $stmt = $db->prepare("UPDATE banners SET image_url = ? WHERE banner_id = ?");
        $stmt->execute([$webpath, $bannerId]);
        
        $bannerUpdated++;
    } else {
        echo "✗\n";
    }
    
    usleep(300000);
}
echo "\n✓ Banners: {$bannerUpdated} images downloaded\n";

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "DOWNLOAD COMPLETE\n";
echo str_repeat("=", 50) . "\n";
echo "Products:  {$updated} products (" . ($updated * 3) . " images)\n";
echo "Categories: {$catUpdated} images\n";
echo "Shops:     {$shopUpdated} logos\n";
echo "Banners:   {$bannerUpdated} images\n";
echo "\nTotal: " . (($updated * 3) + $catUpdated + $shopUpdated + $bannerUpdated) . " images\n";
echo "\nLocation: /uploads/\n";
echo "  - products/     : product_XXX_1.jpg, product_XXX_2.jpg, product_XXX_3.jpg\n";
echo "  - categories/   : category_XXX.jpg\n";
echo "  - shops/logos/  : shop_XXX_logo.jpg\n";
echo "  - banners/      : banner_XXX.jpg\n";
echo "\nDatabase updated with local file paths!\n";
