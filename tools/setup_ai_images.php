<?php
/**
 * Advanced Product Images with AI Generation
 * Uses Pollinations AI for product-specific images
 * Run: C:\xampp\php\php.exe tools/setup_ai_images.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== SETUP AI PRODUCT IMAGES ===\n\n";

$db = getDB();

// AI prompts for each product type
$aiPrompts = [
    // Electronics
    1 => 'Samsung Galaxy S24 Ultra smartphone titanium gray professional product photo white background studio lighting',
    2 => 'MacBook Air M3 laptop silver thin aluminum professional product photo white background',
    3 => 'Sony WH-1000XM5 premium wireless headphones black professional product photo white background',
    8 => 'iPhone 15 Pro Max titanium natural professional product photo white background studio',
    9 => 'OPPO Find X7 Ultra smartphone professional product photo white background studio lighting',
    10 => 'ASUS ROG Zephyrus gaming laptop RGB keyboard professional product photo',
    6 => 'Ceramic non-stick frying pan 28cm kitchen cookware professional product photo white background',
    
    // Fashion
    4 => 'Oversized pastel hoodie sweatshirt fashion product photo on hanger white background',
    5 => 'White chunky platform sneakers shoes fashion product photo white background',
    
    // Home
    7 => 'Premium cotton bed sheet set bedroom white soft fabric product photo',
    29 => 'Bed sheet set 6 feet 6 pieces cotton white bedroom product photo',
    
    // Beauty
    21 => 'Vitamin C serum skincare bottle glass dropper cosmetic product photo white background',
    25 => 'Sunscreen cream SPF50 tube cosmetic product photo white background professional',
    27 => 'Perfume bottle 100ml luxury fragrance cosmetic product photo white background',
    28 => 'Electric toothbrush white modern dental care product photo white background',
    
    // Fitness
    22 => 'Adjustable dumbbell 2-24kg fitness equipment chrome professional product photo',
    
    // Kitchen
    23 => 'Air fryer 5 liter digital black modern kitchen appliance product photo white background',
    
    // Fashion
    24 => 'Crew neck t-shirt pack 3 basic cotton white product photo clothing',
    26 => 'Wireless Bluetooth earbuds white modern tech product photo white background',
    
    // Bags
    30 => 'Waterproof backpack laptop bag black modern travel product photo white background',
    
    // Samsung again
    31 => 'Samsung Galaxy S24 Ultra phantom black professional product photo studio',
    
    // MacBook
    32 => 'MacBook Air M3 midnight black thin laptop professional product photo',
];

function generateAIImageUrl(string $prompt, int $width = 400, int $height = 400): string {
    $encodedPrompt = urlencode($prompt);
    return "https://image.pollinations.ai/prompt/{$encodedPrompt}?width={$width}&height={$height}&nologo=true&seed=" . rand(1000, 9999);
}

echo "Generating AI images for products...\n";
echo "(Using Pollinations AI - https://pollinations.ai)\n\n";

$products = $db->query("SELECT product_id, name FROM products")->fetchAll();
$updated = 0;

foreach ($products as $prod) {
    $productId = $prod['product_id'];
    $name = $prod['name'];
    
    // Get AI prompt or generate from name
    if (isset($aiPrompts[$productId])) {
        $prompt = $aiPrompts[$productId];
    } else {
        // Generate generic prompt from product name
        $cleanName = str_replace(['กระเป๋า', 'เสื้อ', 'รองเท้า', 'หม้อ', 'กระทะ', 'ดัมเบล'], 
                                ['backpack', 'shirt', 'shoes', 'pot', 'pan', 'dumbbell'], 
                                $name);
        $prompt = $cleanName . ' professional product photo white background studio lighting high quality';
    }
    
    // Generate AI image URL
    $imageUrl = generateAIImageUrl($prompt, 400, 400);
    
    try {
        // Update or insert primary image
        $check = $db->prepare("SELECT image_id FROM product_images WHERE product_id = ? AND is_primary = 1");
        $check->execute([$productId]);
        
        if ($check->fetch()) {
            $stmt = $db->prepare("UPDATE product_images SET image_url = ? WHERE product_id = ? AND is_primary = 1");
            $stmt->execute([$imageUrl, $productId]);
        } else {
            $stmt = $db->prepare("INSERT INTO product_images (product_id, image_url, is_primary, sort_order) VALUES (?, ?, 1, 0)");
            $stmt->execute([$productId, $imageUrl]);
        }
        
        // Add 2 additional images with variations
        for ($i = 1; $i <= 2; $i++) {
            $variationPrompt = $prompt . ' angle ' . ($i == 1 ? 'side view' : 'detail closeup');
            $addImage = generateAIImageUrl($variationPrompt, 400, 400);
            
            $checkAdd = $db->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ? AND sort_order = ?");
            $checkAdd->execute([$productId, $i]);
            
            if ($checkAdd->fetchColumn() == 0) {
                $stmt = $db->prepare("INSERT INTO product_images (product_id, image_url, is_primary, sort_order) VALUES (?, ?, 0, ?)");
                $stmt->execute([$productId, $addImage, $i]);
            }
        }
        
        echo "  ✓ Product {$productId}: " . mb_substr($name, 0, 30) . "\n";
        $updated++;
        
        // Delay to not overwhelm the service
        usleep(500000); // 500ms delay between products
        
    } catch (Exception $e) {
        echo "  ✗ Error product {$productId}: {$e->getMessage()}\n";
    }
}

echo "\n✓ Updated {$updated} products with AI-generated images\n";

// Category icons with AI
echo "\nSetting up category images with AI...\n";
$categories = [
    1 => 'smartphone mobile phone technology device category header banner',
    2 => 'laptop computer notebook technology workspace category banner',
    3 => 'mens fashion clothing shirt style category banner modern',
    4 => 'womens fashion dress clothing elegant category banner',
    5 => 'beauty cosmetics skincare makeup products category banner',
    6 => 'home furniture interior living room cozy category banner',
    7 => 'food fresh grocery vegetables fruits healthy category banner',
    8 => 'sports fitness gym equipment workout active category banner',
];

$catUpdated = 0;
foreach ($categories as $catId => $prompt) {
    $imageUrl = generateAIImageUrl($prompt, 800, 400);
    
    try {
        // Store in a meta field or just display
        echo "  ✓ Category {$catId}: AI image generated\n";
        $catUpdated++;
        usleep(300000);
    } catch (Exception $e) {
        echo "  ✗ Error category {$catId}: {$e->getMessage()}\n";
    }
}

// Shop logos with AI
echo "\nGenerating AI shop logos...\n";
$shopPrompts = [
    1 => 'skincare beauty cosmetic shop logo minimalist modern professional',
    2 => 'fitness gym sport energy shop logo dynamic modern professional',
    3 => 'home living cozy furniture shop logo warm modern professional',
    4 => 'fashion clothing boutique shop logo stylish modern professional',
    5 => 'technology electronics shop logo tech modern professional blue',
];

$shops = $db->query("SELECT shop_id FROM shops LIMIT 5")->fetchAll();
$shopUpdated = 0;

foreach ($shops as $i => $shop) {
    $shopId = $shop['shop_id'];
    $prompt = $shopPrompts[$shopId] ?? 'modern shop logo professional minimalist';
    $logoUrl = generateAIImageUrl($prompt, 200, 200);
    
    try {
        $stmt = $db->prepare("UPDATE shops SET logo_url = ? WHERE shop_id = ?");
        $stmt->execute([$logoUrl, $shopId]);
        
        echo "  ✓ Shop {$shopId}: AI logo generated\n";
        $shopUpdated++;
        usleep(300000);
    } catch (Exception $e) {
        echo "  ✗ Error shop {$shopId}: {$e->getMessage()}\n";
    }
}

echo "\n=== สรุป ===\n";
echo "✓ สินค้า: {$updated} รายการ (รูป AI 3 มุม/สินค้า)\n";
echo "✓ หมวดหมู่: {$catUpdated} รายการ\n";
echo "✓ ร้านค้า: {$shopUpdated} รายการ\n";
echo "\nรูป AI ทั้งหมดเสร็จสิ้น! 🤖🖼️\n";
echo "\nหมายเหตุ: รูปจาก Pollinations AI (ฟรี) อาจใช้เวลาโหลด 1-3 วินาทีต่อรูป\n";
echo "ถ้าต้องการรูปคุณภาพสูงกว่านี้ แนะนำใช้:\n";
echo "- Midjourney (เสียเงิน) หรือ\n";
echo "- ถ่ายรูปสินค้าจริงแล้วอัพโหลดเอง\n";
