<?php
/**
 * Reset Thai Data - Insert proper Thai text
 * Run: C:\xampp\php\php.exe tools/reset_thai_data.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Reset Thai Data ===\n\n";

$db = getDB();

// Thai categories
$thaiCategories = [
    ['category_id' => 1, 'name' => 'โทรศัพท์มือถือ', 'description' => 'โทรศัพท์สมาร์ทโฟน แท็บเล็ต และอุปกรณ์เสริม'],
    ['category_id' => 2, 'name' => 'คอมพิวเตอร์', 'description' => 'โน๊ตบุ๊ค คอมพิวเตอร์ตั้งโต๊ะ และอุปกรณ์ไอที'],
    ['category_id' => 3, 'name' => 'เสื้อผ้าผู้ชาย', 'description' => 'เสื้อ กางเกง และแฟชั่นผู้ชาย'],
    ['category_id' => 4, 'name' => 'เสื้อผ้าผู้หญิง', 'description' => 'เสื้อ กระโปรง ชุดแฟชั่นผู้หญิง'],
    ['category_id' => 5, 'name' => 'ความงาม', 'description' => 'เครื่องสำอาง สกินแคร์ และอุปกรณ์ความงาม'],
    ['category_id' => 6, 'name' => 'ของใช้ในบ้าน', 'description' => 'เฟอร์นิเจอร์ เครื่องใช้ไฟฟ้า และของตกแต่งบ้าน'],
    ['category_id' => 7, 'name' => 'อาหารและเครื่องดื่ม', 'description' => 'อาหารสด อาหารแห้ง และเครื่องดื่ม'],
    ['category_id' => 8, 'name' => 'กีฬาและฟิตเนส', 'description' => 'อุปกรณ์กีฬา ชุดออกกำลังกาย และอาหารเสริม'],
];

// Thai products
$thaiProducts = [
    ['name' => 'เซรั่มวิตามินซี Skinsation 4 ชิ้น', 'brand' => 'Skinsation', 'description' => 'เซรั่มวิตามินซีเข้มข้น ช่วยให้ผิวกระจ่างใส'],
    ['name' => 'ดัมเบลน้ำหนักปรับได้ 2-24kg', 'brand' => 'FitnessPro', 'description' => 'ดัมเบลปรับน้ำหนักได้ 12 ระดับ'],
    ['name' => 'หม้อทอดไร้น้ำมัน 5 ลิตร', 'brand' => 'AirFryer', 'description' => 'หม้อทอดไร้น้ำมันดิจิตอล ควบคุมอุณหภูมิได้'],
    ['name' => 'เสื้อยืดคอกลมแพ็ค 3 ตัว', 'brand' => 'BasicWear', 'description' => 'เสื้อยืดคอกลมคุณภาพดี ผ้านุ่มใส่สบาย'],
    ['name' => 'ครีมกันแดด SPF50+ PA++++', 'brand' => 'SunShield', 'description' => 'ครีมกันแดดเนื้อบางเบา ไม่เหนียวเหนอะหนะ'],
    ['name' => 'หูฟังไร้สาย Bluetooth 5.3', 'brand' => 'SoundMax', 'description' => 'หูฟังบลูทูธเสียงคมชัด แบตอึด 30 ชั่วโมง'],
    ['name' => 'น้ำหอมกลิ่นกลางคืน 100ml', 'brand' => 'NightScent', 'description' => 'น้ำหอมหรูหรา กลิ่นติดทนนาน 8 ชั่วโมง'],
    ['name' => 'แปรงสีฟันไฟฟ้า โหมด Whitening', 'brand' => 'CleanTeeth', 'description' => 'แปรงสีฟันไฟฟ้า 5 โหมด หัวแปรงเปลี่ยนได้'],
    ['name' => 'ผ้าปูที่นอนเซ็ต 6 ฟุต 6 ชิ้น', 'brand' => 'SoftSleep', 'description' => 'ผ้าปูที่นอนผ้าฝ้าย 100% นุ่มสบาย'],
    ['name' => 'กระเป๋าเป้สะพายหลังกันน้ำ', 'brand' => 'TravelBag', 'description' => 'กระเป๋าเป้กันน้ำ มีช่องใส่โน๊ตบุ๊ค 15.6 นิ้ว'],
];

// Thai shop names
$thaiShops = [
    ['shop_name' => 'ร้านสกินแคร์ถูกและดี', 'description' => 'จำหน่ายเครื่องสำอางและสกินแคร์ราคาถูก'],
    ['shop_name' => 'ฟิตเนสพลัส', 'description' => 'อุปกรณ์ฟิตเนสและอาหารเสริมครบวงจร'],
    ['shop_name' => 'บ้านน่าอยู่', 'description' => 'ของใช้ในบ้านและเฟอร์นิเจอร์คุณภาพดี'],
    ['shop_name' => 'แฟชั่นไทย', 'description' => 'เสื้อผ้าแฟชั่นไทย อัพเดททุกซีซั่น'],
    ['shop_name' => 'ไอทีมอลล์', 'description' => 'สินค้าไอที โทรศัพท์ คอมพิวเตอร์ ราคาถูก'],
];

// Thai reviews
$thaiReviews = [
    'สินค้าดีมากค่ะ ส่งไว แพคของมาดี',
    'คุณภาพดีเกินราคา จะสั่งอีกแน่นอน',
    'ได้รับของแล้ว ตรงปก สภาพดี',
    'ร้านนี้บริการดี ตอบไว ส่งไว',
    'สินค้าคุณภาพดีมาก แนะนำเลยค่ะ',
    'ซื้อมาครั้งที่ 3 แล้ว ชอบมาก',
    'ส่งเร็วมาก ของดีจริง ไม่ผิดหวัง',
    'ราคาถูก คุณภาพดี คุ้มค่า',
    'แพคมาดีมาก สินค้าไม่มีตำหนิ',
    'ชอบมากค่ะ จะกลับมาซื้ออีก',
];

echo "Updating categories...\n";
foreach ($thaiCategories as $cat) {
    try {
        $stmt = $db->prepare("UPDATE categories SET name = ?, description = ? WHERE category_id = ?");
        $stmt->execute([$cat['name'], $cat['description'], $cat['category_id']]);
        echo "  ✓ Category {$cat['category_id']}: {$cat['name']}\n";
    } catch (Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n";
    }
}

echo "\nUpdating products (first 10)...\n";
$products = $db->query("SELECT product_id FROM products LIMIT 10")->fetchAll();
foreach ($products as $i => $prod) {
    if (!isset($thaiProducts[$i])) break;
    $data = $thaiProducts[$i];
    try {
        $stmt = $db->prepare("UPDATE products SET name = ?, brand = ?, description = ? WHERE product_id = ?");
        $stmt->execute([$data['name'], $data['brand'], $data['description'], $prod['product_id']]);
        echo "  ✓ Product {$prod['product_id']}: {$data['name']}\n";
    } catch (Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n";
    }
}

echo "\nUpdating shops (first 5)...\n";
$shops = $db->query("SELECT shop_id FROM shops LIMIT 5")->fetchAll();
foreach ($shops as $i => $shop) {
    if (!isset($thaiShops[$i])) break;
    $data = $thaiShops[$i];
    try {
        $stmt = $db->prepare("UPDATE shops SET shop_name = ?, description = ? WHERE shop_id = ?");
        $stmt->execute([$data['shop_name'], $data['description'], $shop['shop_id']]);
        echo "  ✓ Shop {$shop['shop_id']}: {$data['shop_name']}\n";
    } catch (Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n";
    }
}

echo "\nUpdating reviews...\n";
$reviews = $db->query("SELECT review_id FROM reviews LIMIT 10")->fetchAll();
foreach ($reviews as $i => $rev) {
    if (!isset($thaiReviews[$i])) break;
    $comment = $thaiReviews[$i];
    try {
        $stmt = $db->prepare("UPDATE reviews SET comment = ? WHERE review_id = ?");
        $stmt->execute([$comment, $rev['review_id']]);
        echo "  ✓ Review {$rev['review_id']}: " . substr($comment, 0, 30) . "...\n";
    } catch (Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n";
    }
}

echo "\n=== Done! ===\n";
echo "Thai text has been inserted into database.\n";
echo "Please refresh your web page to see the changes.\n";
