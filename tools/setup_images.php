<?php
/**
 * Setup Product Images - Using Placeholder Services
 * Generates appropriate placeholder images for all products
 * Run: C:\xampp\php\php.exe tools/setup_images.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== SETUP PRODUCT IMAGES ===\n\n";

$db = getDB();

// Image mapping by product category/type
$imageMap = [
    // Beauty/Skincare
    'เซรั่ม' => ['keyword' => 'skincare,serum', 'color' => 'FFB6C1', 'bg' => 'FFF0F5'],
    'ครีม' => ['keyword' => 'cream,beauty', 'color' => 'FFC0CB', 'bg' => 'FFF5EE'],
    'น้ำหอม' => ['keyword' => 'perfume,fragrance', 'color' => 'DDA0DD', 'bg' => 'F8F4FF'],
    'แปรง' => ['keyword' => 'toothbrush', 'color' => '87CEEB', 'bg' => 'F0F8FF'],
    
    // Fitness/Sports
    'ดัมเบล' => ['keyword' => 'dumbbell,fitness', 'color' => '2F4F4F', 'bg' => 'F5F5F5'],
    'ออกกำลังกาย' => ['keyword' => 'fitness,gym', 'color' => '228B22', 'bg' => 'F0FFF0'],
    'กระเป๋า' => ['keyword' => 'backpack,bag', 'color' => '8B4513', 'bg' => 'FAF0E6'],
    
    // Kitchen/Home
    'หม้อ' => ['keyword' => 'cooking,pot', 'color' => 'FF6347', 'bg' => 'FFF8DC'],
    'กระทะ' => ['keyword' => 'frying,pan', 'color' => 'CD853F', 'bg' => 'FDF5E6'],
    'เครื่องครัว' => ['keyword' => 'kitchen,cookware', 'color' => 'D2691E', 'bg' => 'FFFAF0'],
    
    // Fashion/Clothing
    'เสื้อ' => ['keyword' => 'tshirt,clothing', 'color' => '4169E1', 'bg' => 'F0F8FF'],
    'เสื้อยืด' => ['keyword' => 'tshirt,casual', 'color' => '1E90FF', 'bg' => 'F0F8FF'],
    'เสื้อฮู้ด' => ['keyword' => 'hoodie,sweater', 'color' => '9370DB', 'bg' => 'E6E6FA'],
    'รองเท้า' => ['keyword' => 'shoes,sneakers', 'color' => '696969', 'bg' => 'F5F5F5'],
    
    // Electronics
    'โทรศัพท์' => ['keyword' => 'smartphone,mobile', 'color' => '000080', 'bg' => 'E6E6FA'],
    'iPhone' => ['keyword' => 'iphone,apple', 'color' => '191970', 'bg' => 'F0F8FF'],
    'Samsung' => ['keyword' => 'samsung,android', 'color' => '00008B', 'bg' => 'E6E6FA'],
    'โน๊ตบุ๊ค' => ['keyword' => 'laptop,computer', 'color' => '2F4F4F', 'bg' => 'F5F5F5'],
    'MacBook' => ['keyword' => 'macbook,laptop', 'color' => 'A9A9A9', 'bg' => 'F5F5F5'],
    'หูฟัง' => ['keyword' => 'headphones,audio', 'color' => '4B0082', 'bg' => 'F8F8FF'],
    
    // Home/Bedding
    'ผ้าปู' => ['keyword' => 'bedding,sheets', 'color' => '20B2AA', 'bg' => 'F0FFFF'],
    'หมอน' => ['keyword' => 'pillow,bed', 'color' => '48D1CC', 'bg' => 'F0FFFF'],
    'เครื่องนอน' => ['keyword' => 'bedding,sleep', 'color' => '008B8B', 'bg' => 'F0FFFF'],
    
    // Default
    'default' => ['keyword' => 'product,shopping', 'color' => 'EE4D2D', 'bg' => 'FFF5F5'],
];

// Lorem Picsum keywords mapping for better images
$loremPicsumKeywords = [
    1 => 'technology',      // Samsung
    2 => 'computer',        // MacBook
    3 => 'headphones',      // Sony
    4 => 'fashion',         // Hoodie
    5 => 'shoes',           // Sneakers
    6 => 'food',            // Frying pan
    7 => 'home',            // Bed sheets
    8 => 'phone',           // iPhone
    9 => 'camera',          // OPPO
    10 => 'gaming',         // ASUS
];

function getImageConfig(string $productName): array {
    global $imageMap;
    
    foreach ($imageMap as $keyword => $config) {
        if (mb_stripos($productName, $keyword) !== false) {
            return $config;
        }
    }
    
    return $imageMap['default'];
}

echo "Setting up product images...\n";
$products = $db->query("SELECT product_id, name FROM products")->fetchAll();
$updated = 0;

foreach ($products as $i => $prod) {
    $productId = $prod['product_id'];
    $name = $prod['name'];
    
    // Use Lorem Picsum for variety (seeded by product_id for consistency)
    // Primary image
    $seed = $productId * 100;
    $primaryImage = "https://picsum.photos/seed/{$seed}/400/400";
    
    // Alternative: Use Pollinations AI for product-like images
    $config = getImageConfig($name);
    $keyword = urlencode($config['keyword']);
    $aiImage = "https://image.pollinations.ai/prompt/{$keyword}%20product%20photo%20white%20background%20professional?width=400&height=400&nologo=true";
    
    // Use placeholder.com as fallback with custom text
    $placeholderText = urlencode(mb_substr($name, 0, 20));
    $placeholderImage = "https://via.placeholder.com/400x400/{$config['bg']}/{$config['color']}?text={$placeholderText}";
    
    try {
        // Update primary image
        $stmt = $db->prepare("UPDATE product_images SET image_url = ? WHERE product_id = ? AND is_primary = 1");
        $stmt->execute([$primaryImage, $productId]);
        
        // If no rows affected, insert new
        if ($stmt->rowCount() == 0) {
            $stmt = $db->prepare("INSERT INTO product_images (product_id, image_url, is_primary, sort_order) VALUES (?, ?, 1, 0)");
            $stmt->execute([$productId, $primaryImage]);
        }
        
        // Add additional images (2-3 images per product)
        $additionalSeeds = [$seed + 1, $seed + 2];
        $sortOrder = 1;
        foreach ($additionalSeeds as $addSeed) {
            $addImage = "https://picsum.photos/seed/{$addSeed}/400/400";
            
            // Check if exists
            $check = $db->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ? AND sort_order = ?");
            $check->execute([$productId, $sortOrder]);
            
            if ($check->fetchColumn() == 0) {
                $stmt = $db->prepare("INSERT INTO product_images (product_id, image_url, is_primary, sort_order) VALUES (?, ?, 0, ?)");
                $stmt->execute([$productId, $addImage, $sortOrder]);
            }
            $sortOrder++;
        }
        
        echo "  ✓ Product {$productId}: {$name}\n";
        $updated++;
        
        // Rate limiting - don't overwhelm the services
        if ($i % 10 == 0) {
            usleep(100000); // 100ms delay every 10 products
        }
        
    } catch (Exception $e) {
        echo "  ✗ Error product {$productId}: {$e->getMessage()}\n";
    }
}

echo "\n✓ Updated {$updated} products with images\n";

// Setup category images
echo "\nSetting up category images...\n";
$categories = $db->query("SELECT category_id, name FROM categories")->fetchAll();

$categoryKeywords = [
    'โทรศัพท์' => 'smartphone,mobile',
    'คอมพิวเตอร์' => 'computer,laptop',
    'เสื้อผ้า' => 'fashion,clothing',
    'ความงาม' => 'beauty,cosmetics',
    'ของใช้ในบ้าน' => 'home,furniture',
    'อาหาร' => 'food,grocery',
    'กีฬา' => 'sports,fitness',
    'หูฟัง' => 'headphones,music',
    'รองเท้า' => 'shoes,footwear',
    'เครื่องครัว' => 'kitchen,cooking',
    'เครื่องนอน' => 'bedding,bedroom',
];

$catUpdated = 0;
foreach ($categories as $cat) {
    $keyword = 'category';
    foreach ($categoryKeywords as $key => $val) {
        if (mb_stripos($cat['name'], $key) !== false) {
            $keyword = $val;
            break;
        }
    }
    
    $seed = $cat['category_id'] * 1000;
    $imageUrl = "https://picsum.photos/seed/{$seed}/300/300";
    
    try {
        // Add icon if column exists
        $stmt = $db->prepare("UPDATE categories SET icon = ? WHERE category_id = ?");
        $stmt->execute(['bi-grid', $cat['category_id']]); // Bootstrap icon
        
        echo "  ✓ Category {$cat['category_id']}: {$cat['name']}\n";
        $catUpdated++;
    } catch (Exception $e) {
        // Icon column might not exist, ignore
    }
}
echo "✓ Updated {$catUpdated} categories\n";

// Setup shop logos
echo "\nSetting up shop logos...\n";
$shops = $db->query("SELECT shop_id, shop_name FROM shops")->fetchAll();
$shopUpdated = 0;

foreach ($shops as $shop) {
    $seed = $shop['shop_id'] * 5000;
    $logoUrl = "https://picsum.photos/seed/{$seed}/200/200";
    
    try {
        $stmt = $db->prepare("UPDATE shops SET logo_url = ? WHERE shop_id = ?");
        $stmt->execute([$logoUrl, $shop['shop_id']]);
        
        echo "  ✓ Shop {$shop['shop_id']}: {$shop['shop_name']}\n";
        $shopUpdated++;
    } catch (Exception $e) {
        echo "  ✗ Error shop {$shop['shop_id']}: {$e->getMessage()}\n";
    }
}
echo "✓ Updated {$shopUpdated} shops\n";

// Setup banner images
echo "\nSetting up banner images...\n";
$banners = $db->query("SELECT banner_id, title FROM banners")->fetchAll();
$bannerUpdated = 0;

foreach ($banners as $banner) {
    $seed = $banner['banner_id'] * 10000;
    $imageUrl = "https://picsum.photos/seed/{$seed}/1200/400";
    
    try {
        $stmt = $db->prepare("UPDATE banners SET image_url = ? WHERE banner_id = ?");
        $stmt->execute([$imageUrl, $banner['banner_id']]);
        
        echo "  ✓ Banner {$banner['banner_id']}: " . mb_substr($banner['title'], 0, 30) . "...\n";
        $bannerUpdated++;
    } catch (Exception $e) {
        echo "  ✗ Error banner {$banner['banner_id']}: {$e->getMessage()}\n";
    }
}
echo "✓ Updated {$bannerUpdated} banners\n";

// Setup flash sale images
echo "\nSetting up flash sale banner images...\n";
$flashSales = $db->query("SELECT flash_sale_id, title FROM flash_sales")->fetchAll();
$flashUpdated = 0;

foreach ($flashSales as $fs) {
    $seed = $fs['flash_sale_id'] * 20000;
    $bannerUrl = "https://picsum.photos/seed/{$seed}/800/200";
    
    try {
        $stmt = $db->prepare("UPDATE flash_sales SET banner_url = ? WHERE flash_sale_id = ?");
        $stmt->execute([$bannerUrl, $fs['flash_sale_id']]);
        
        echo "  ✓ Flash Sale {$fs['flash_sale_id']}: {$fs['title']}\n";
        $flashUpdated++;
    } catch (Exception $e) {
        echo "  ✗ Error flash sale {$fs['flash_sale_id']}: {$e->getMessage()}\n";
    }
}
echo "✓ Updated {$flashUpdated} flash sales\n";

echo "\n=== สรุป ===\n";
echo "✓ สินค้า: {$updated} รายการ\n";
echo "✓ หมวดหมู่: {$catUpdated} รายการ\n";
echo "✓ ร้านค้า: {$shopUpdated} รายการ\n";
echo "✓ แบนเนอร์: {$bannerUpdated} รายการ\n";
echo "✓ Flash Sale: {$flashUpdated} รายการ\n";
echo "\nรูปภาพทั้งหมดเสร็จสิ้น! 🖼️\n";
echo "หมายเหตุ: ใช้ Lorem Picsum (https://picsum.photos) สำหรับรูปตัวอย่าง\n";
echo "รูปจะเปลี่ยนแปลงตาม seed ที่กำหนด ทำให้ได้รูปเดิมทุกครั้ง\n";
