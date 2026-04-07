<?php
/**
 * Fix ALL Thai Encoding in Database
 * Comprehensive fix for all tables with text content
 * Run: C:\xampp\php\php.exe tools/fix_all_thai.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== FIX ALL THAI ENCODING IN DATABASE ===\n\n";

$db = getDB();

// Thai text data for different purposes
$thaiData = [
    'products' => [
        ['name' => 'เซรั่มวิตามินซี Skinsation 4 ชิ้น', 'brand' => 'Skinsation', 'description' => 'เซรั่มวิตามินซีเข้มข้น ช่วยให้ผิวกระจ่างใส ลดจุดด่างดำ 4 ขวดในเซ็ตเดียว'],
        ['name' => 'ดัมเบลน้ำหนักปรับได้ 2-24kg', 'brand' => 'FitnessPro', 'description' => 'ดัมเบลปรับน้ำหนักได้ 12 ระดับ เหมาะสำหรับออกกำลังกายที่บ้าน'],
        ['name' => 'หม้อทอดไร้น้ำมัน 5 ลิตร', 'brand' => 'AirFryer', 'description' => 'หม้อทอดไร้น้ำมันดิจิตอล ควบคุมอุณหภูมิได้ 80-200°C จอสัมผัส LCD'],
        ['name' => 'เสื้อยืดคอกลมแพ็ค 3 ตัว', 'brand' => 'BasicWear', 'description' => 'เสื้อยืดคอกลมคุณภาพดี ผ้านุ่มใส่สบาย ระบายอากาศได้ดี'],
        ['name' => 'ครีมกันแดด SPF50+ PA++++', 'brand' => 'SunShield', 'description' => 'ครีมกันแดดเนื้อบางเบา ไม่เหนียวเหนอะหนะ กันน้ำกันเหงื่อ'],
        ['name' => 'หูฟังไร้สาย Bluetooth 5.3', 'brand' => 'SoundMax', 'description' => 'หูฟังบลูทูธเสียงคมชัด แบตอึด 30 ชั่วโมง กันน้ำ IPX5'],
        ['name' => 'น้ำหอมกลิ่นกลางคืน 100ml', 'brand' => 'NightScent', 'description' => 'น้ำหอมหรูหรา กลิ่นติดทนนาน 8 ชั่วโมง สำหรับงานกลางคืน'],
        ['name' => 'แปรงสีฟันไฟฟ้า โหมด Whitening', 'brand' => 'CleanTeeth', 'description' => 'แปรงสีฟันไฟฟ้า 5 โหมด หัวแปรงเปลี่ยนได้ ตั้งเวลา 2 นาที'],
        ['name' => 'ผ้าปูที่นอนเซ็ต 6 ฟุต 6 ชิ้น', 'brand' => 'SoftSleep', 'description' => 'ผ้าปูที่นอนผ้าฝ้าย 100% นุ่มสบาย ระบายอากาศ ไม่ร้อน'],
        ['name' => 'กระเป๋าเป้สะพายหลังกันน้ำ', 'brand' => 'TravelBag', 'description' => 'กระเป๋าเป้กันน้ำ มีช่องใส่โน๊ตบุ๊ค 15.6 นิ้ว หลายช่องซิป'],
        ['name' => 'โทรศัพท์ Samsung Galaxy S24 Ultra', 'brand' => 'Samsung', 'description' => 'สมาร์ทโฟนเรือธง จอ 6.8 นิ้ว กล้อง 200MP แบต 5000mAh'],
        ['name' => 'โน๊ตบุ๊ค MacBook Air M3 13 นิ้ว', 'brand' => 'Apple', 'description' => 'โน๊ตบุ๊คบางเบา ชิป M3 แบตอึด 18 ชั่วโมง จอ Retina'],
        ['name' => 'หูฟัง Sony WH-1000XM5', 'brand' => 'Sony', 'description' => 'หูฟังตัดเสียงรบกวนระดับพรีเมียม แบต 30 ชั่วโมง สบายหู'],
        ['name' => 'เสื้อฮู้ดโอเวอร์ไซส์', 'brand' => 'StreetWear', 'description' => 'เสื้อฮู้ดทรงหลวม ผ้าหนานุ่ม มีหลายสีให้เลือก'],
        ['name' => 'รองเท้าผ้าใบหนาพื้นสูง', 'brand' => 'SneakerMax', 'description' => 'รองเท้าผ้าใบแฟชั่น ใส่สบาย สูง 5 ซม. มีสีขาวดำ'],
        ['name' => 'กระทะเซรามิก 28 ซม.', 'brand' => 'KitchenPro', 'description' => 'กระทะเคลือบเซรามิก ไม่ติด ทำความสะอาดง่าย ก้นแบน'],
        ['name' => 'ชุดเครื่องนอนฝ้าย', 'brand' => 'SleepWell', 'description' => 'ชุดผ้าปูที่นอนคุณภาพดี 6 ชิ้น ลายพื้นเรียบ'],
        ['name' => 'iPhone 15 Pro Max 256GB', 'brand' => 'Apple', 'description' => 'ไอโฟนรุ่นท็อป จอไทเทเนียม กล้อง 48MP USB-C'],
        ['name' => 'OPPO Find X7 Ultra', 'brand' => 'OPPO', 'description' => 'มือถือกล้องเทพ ซูม 100x ชาร์จไว 100W'],
        ['name' => 'โน๊ตบุ๊คเกมมิ่ง ASUS ROG', 'brand' => 'ASUS', 'description' => 'โน๊ตบุ๊คเล่นเกม RTX 4060 จอ 165Hz RGB'],
    ],
    'categories' => [
        ['name' => 'โทรศัพท์มือถือ', 'description' => 'โทรศัพท์สมาร์ทโฟน แท็บเล็ต และอุปกรณ์เสริม'],
        ['name' => 'คอมพิวเตอร์', 'description' => 'โน๊ตบุ๊ค คอมพิวเตอร์ตั้งโต๊ะ และอุปกรณ์ไอที'],
        ['name' => 'เสื้อผ้าผู้ชาย', 'description' => 'เสื้อ กางเกง และแฟชั่นผู้ชาย'],
        ['name' => 'เสื้อผ้าผู้หญิง', 'description' => 'เสื้อ กระโปรง ชุดแฟชั่นผู้หญิง'],
        ['name' => 'ความงาม', 'description' => 'เครื่องสำอาง สกินแคร์ และอุปกรณ์ความงาม'],
        ['name' => 'ของใช้ในบ้าน', 'description' => 'เฟอร์นิเจอร์ เครื่องใช้ไฟฟ้า และของตกแต่งบ้าน'],
        ['name' => 'อาหารและเครื่องดื่ม', 'description' => 'อาหารสด อาหารแห้ง และเครื่องดื่ม'],
        ['name' => 'กีฬาและฟิตเนส', 'description' => 'อุปกรณ์กีฬา ชุดออกกำลังกาย และอาหารเสริม'],
        ['name' => 'หูฟังและลำโพง', 'description' => 'หูฟังไร้สาย หูฟังเกมมิ่ง ลำโพงบลูทูธ'],
        ['name' => 'รองเท้า', 'description' => 'รองเท้าผ้าใบ รองเท้าหนัง รองเท้าแฟชั่น'],
        ['name' => 'เครื่องครัว', 'description' => 'หม้อ กระทะ เตา และอุปกรณ์ทำอาหาร'],
        ['name' => 'เครื่องนอน', 'description' => 'ผ้าปูที่นอน หมอน ผ้าห่ม และของใช้ในห้องนอน'],
    ],
    'shops' => [
        ['shop_name' => 'ร้านสกินแคร์ถูกและดี', 'description' => 'จำหน่ายเครื่องสำอางและสกินแคร์ราคาถูก ของแท้ 100%'],
        ['shop_name' => 'ฟิตเนสพลัส', 'description' => 'อุปกรณ์ฟิตเนสและอาหารเสริมครบวงจร คุณภาพดี'],
        ['shop_name' => 'บ้านน่าอยู่', 'description' => 'ของใช้ในบ้านและเฟอร์นิเจอร์คุณภาพดี ราคาเป็นมิตร'],
        ['shop_name' => 'แฟชั่นไทย', 'description' => 'เสื้อผ้าแฟชั่นไทย อัพเดททุกซีซั่น ส่งไวทุกออเดอร์'],
        ['shop_name' => 'ไอทีมอลล์', 'description' => 'สินค้าไอที โทรศัพท์ คอมพิวเตอร์ ราคาถูก ประกันศูนย์'],
        ['shop_name' => 'มือถือราคาดี', 'description' => 'โทรศัพท์มือถือทุกรุ่น แท้ 100% ผ่อน 0% ได้'],
        ['shop_name' => 'นอนสบาย', 'description' => 'เครื่องนอนคุณภาพดี ผ้านุ่ม นอนหลับฝันดี'],
        ['shop_name' => 'แฟชั่นผู้ชาย', 'description' => 'เสื้อผ้าผู้ชาย ทรงสวย ผ้าดี ราคาถูก'],
    ],
    'reviews' => [
        'สินค้าดีมากค่ะ ส่งไว แพคของมาดีมาก',
        'คุณภาพดีเกินราคา จะสั่งอีกแน่นอนค่ะ',
        'ได้รับของแล้ว ตรงปก สภาพดี ไม่มีตำหนิ',
        'ร้านนี้บริการดี ตอบไว ส่งไว ประทับใจมาก',
        'สินค้าคุณภาพดีมาก แนะนำเลยค่ะ ซื้อซ้ำแน่',
        'ซื้อมาครั้งที่ 3 แล้ว ชอบมาก ของดีจริง',
        'ส่งเร็วมาก ของดีจริง ไม่ผิดหวังที่สั่ง',
        'ราคาถูก คุณภาพดี คุ้มค่าเกินราคา',
        'แพคมาดีมาก สินค้าไม่มีตำหนิ ครบถ้วน',
        'ชอบมากค่ะ จะกลับมาซื้ออีกแน่นอน',
        'สินค้าตรงรูป คุณภาพตามที่คาดไว้',
        'ร้านนี้ดีจริง ส่งไว ของแท้ แนะนำ',
        'ประทับใจมาก สินค้าดี ราคาไม่แพง',
        'ดีมากค่ะ ไม่ผิดหวัง จะสั่งอีก',
        'ของดีมีคุณภาพ แพคมาอย่างดี ส่งไว',
    ],
    'cms_pages' => [
        ['title' => 'เกี่ยวกับเรา', 'content' => 'Shopee-style Shop คือแพลตฟอร์มอีคอมเมิร์ซที่ใหญ่ที่สุดในไทย เรามีสินค้าหลากหลายราคาถูก พร้อมบริการส่งฟรีและโปรโมชั่นพิเศษมากมาย'],
        ['title' => 'ข้อตกลงและเงื่อนไข', 'content' => 'การใช้บริการเว็บไซต์นี้ ถือว่าท่านยอมรับข้อตกลงและเงื่อนไขการใช้งานทั้งหมด กรุณาอ่านอย่างละเอียดก่อนใช้งาน'],
        ['title' => 'นโยบายความเป็นส่วนตัว', 'content' => 'เราให้ความสำคัญกับข้อมูลส่วนตัวของท่าน ข้อมูลทั้งหมดจะถูกเก็บเป็นความลับและไม่เปิดเผยแก่บุคคลภายนอก'],
        ['title' => 'วิธีการสั่งซื้อ', 'content' => '1. เลือกสินค้า 2. เพิ่มลงตะกร้า 3. กรอกที่อยู่ 4. เลือกวิธีชำระเงิน 5. รอรับสินค้า'],
        ['title' => 'การจัดส่ง', 'content' => 'เราจัดส่งทั่วประเทศไทย ใช้เวลา 1-3 วันทำการสำหรับกรุงเทพ และ 3-7 วันสำหรับต่างจังหวัด'],
    ],
    'announcements' => [
        'โปรโมชั่น 11.11 ลดสูงสุด 90% ฟรีค่าส่งทุกออเดอร์!',
        'สมาชิกใหม่รับส่วนลด 100 บาท พร้อมคูปองฟรีค่าส่ง',
        'แคมเปญสินค้าไอทีลดราคา โทรศัพท์ แท็บเล็ต ลดพิเศษ',
        'ช้อปครบ 499 ส่งฟรีทั่วไทย ไม่จำกัดจำนวน',
        'Flash Sale ทุกวันเวลา 12:00 น. สินค้าราคาพิเศษ',
        'สะสม Coins แลกส่วนลด แลกคูปอง ใช้แทนเงินสดได้',
        'นโยบายคืนสินค้า 14 วัน คืนได้ไม่ต้องบอกเหตุผล',
        'ShopeePay ชำระเงินง่าย ปลอดภัย ได้ Coins คืน 2%',
    ],
    'email_templates' => [
        ['subject' => 'ยืนยันการสมัครสมาชิก', 'body' => 'ขอบคุณที่สมัครสมาชิกกับเรา กรุณายืนยันอีเมลเพื่อเปิดใช้งานบัญชีของท่าน'],
        ['subject' => 'ออเดอร์ของคุณได้รับการยืนยัน', 'body' => 'ออเดอร์ #{order_number} ได้รับการยืนยันแล้ว เรากำลังจัดเตรียมสินค้าเพื่อจัดส่งให้คุณ'],
        ['subject' => 'สินค้าของคุณจัดส่งแล้ว', 'body' => 'ออเดอร์ของคุณจัดส่งแล้ว หมายเลขติดตาม: {tracking_number} คลิกเพื่อติดตามสถานะ'],
        ['subject' => 'รหัส OTP สำหรับยืนยันตัวตน', 'body' => 'รหัส OTP ของคุณคือ: {otp} รหัสนี้ใช้ได้ 5 นาที กรุณาไม่เปิดเผยให้ผู้อื่น'],
    ],
];

$totalFixed = 0;

// Fix products
echo "[1] Fixing products...\n";
$products = $db->query("SELECT product_id FROM products")->fetchAll();
foreach ($products as $i => $prod) {
    $dataIndex = $i % count($thaiData['products']);
    $data = $thaiData['products'][$dataIndex];
    try {
        $stmt = $db->prepare("UPDATE products SET name = ?, brand = ?, description = ? WHERE product_id = ?");
        $stmt->execute([$data['name'], $data['brand'], $data['description'], $prod['product_id']]);
        echo "  ✓ ID {$prod['product_id']}: {$data['name']}\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$prod['product_id']}: {$e->getMessage()}\n";
    }
}

// Fix categories
echo "\n[2] Fixing categories...\n";
foreach ($thaiData['categories'] as $i => $cat) {
    $catId = $i + 1;
    try {
        $stmt = $db->prepare("UPDATE categories SET name = ?, description = ? WHERE category_id = ?");
        $stmt->execute([$cat['name'], $cat['description'], $catId]);
        echo "  ✓ ID {$catId}: {$cat['name']}\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$catId}: {$e->getMessage()}\n";
    }
}

// Fix shops
echo "\n[3] Fixing shops...\n";
$shops = $db->query("SELECT shop_id FROM shops")->fetchAll();
foreach ($shops as $i => $shop) {
    $dataIndex = $i % count($thaiData['shops']);
    $data = $thaiData['shops'][$dataIndex];
    try {
        $stmt = $db->prepare("UPDATE shops SET shop_name = ?, description = ? WHERE shop_id = ?");
        $stmt->execute([$data['shop_name'], $data['description'], $shop['shop_id']]);
        echo "  ✓ ID {$shop['shop_id']}: {$data['shop_name']}\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$shop['shop_id']}: {$e->getMessage()}\n";
    }
}

// Fix reviews
echo "\n[4] Fixing reviews...\n";
$reviews = $db->query("SELECT review_id FROM reviews")->fetchAll();
foreach ($reviews as $i => $rev) {
    $dataIndex = $i % count($thaiData['reviews']);
    $comment = $thaiData['reviews'][$dataIndex];
    try {
        $stmt = $db->prepare("UPDATE reviews SET comment = ? WHERE review_id = ?");
        $stmt->execute([$comment, $rev['review_id']]);
        echo "  ✓ ID {$rev['review_id']}: " . mb_substr($comment, 0, 30) . "...\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$rev['review_id']}: {$e->getMessage()}\n";
    }
}

// Fix CMS pages
echo "\n[5] Fixing CMS pages...\n";
$pages = $db->query("SELECT page_id FROM cms_pages")->fetchAll();
foreach ($pages as $i => $page) {
    $dataIndex = $i % count($thaiData['cms_pages']);
    $data = $thaiData['cms_pages'][$dataIndex];
    try {
        $stmt = $db->prepare("UPDATE cms_pages SET title = ?, content = ? WHERE page_id = ?");
        $stmt->execute([$data['title'], $data['content'], $page['page_id']]);
        echo "  ✓ ID {$page['page_id']}: {$data['title']}\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$page['page_id']}: {$e->getMessage()}\n";
    }
}

// Fix announcements
echo "\n[6] Fixing announcements...\n";
$announcements = $db->query("SELECT ann_id FROM announcements")->fetchAll();
foreach ($announcements as $i => $ann) {
    $dataIndex = $i % count($thaiData['announcements']);
    $content = $thaiData['announcements'][$dataIndex];
    try {
        $stmt = $db->prepare("UPDATE announcements SET title = ? WHERE ann_id = ?");
        $stmt->execute([$content, $ann['ann_id']]);
        echo "  ✓ ID {$ann['ann_id']}: " . mb_substr($content, 0, 40) . "...\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$ann['ann_id']}: {$e->getMessage()}\n";
    }
}

// Fix email templates
echo "\n[7] Fixing email templates...\n";
$templates = $db->query("SELECT template_id FROM email_templates")->fetchAll();
foreach ($templates as $i => $tpl) {
    $dataIndex = $i % count($thaiData['email_templates']);
    $data = $thaiData['email_templates'][$dataIndex];
    try {
        $stmt = $db->prepare("UPDATE email_templates SET subject = ?, body_html = ? WHERE template_id = ?");
        $stmt->execute([$data['subject'], $data['body'], $tpl['template_id']]);
        echo "  ✓ ID {$tpl['template_id']}: {$data['subject']}\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$tpl['template_id']}: {$e->getMessage()}\n";
    }
}

// Fix site_settings
echo "\n[8] Fixing site settings...\n";
$settings = [
    ['key' => 'site_name', 'value' => 'Shopee Thailand'],
    ['key' => 'site_description', 'value' => 'แพลตฟอร์มช้อปปิ้งออนไลน์อันดับ 1 ของไทย สินค้าหลากหลาย ราคาถูก ส่งฟรีทั่วไทย'],
    ['key' => 'contact_email', 'value' => 'support@shopee.th'],
    ['key' => 'contact_phone', 'value' => '02-000-0000'],
];
foreach ($settings as $setting) {
    try {
        $stmt = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$setting['value'], $setting['key']]);
        echo "  ✓ {$setting['key']}: " . mb_substr($setting['value'], 0, 40) . "...\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error {$setting['key']}: {$e->getMessage()}\n";
    }
}

// Fix banners
echo "\n[9] Fixing banners...\n";
$banners = $db->query("SELECT banner_id FROM banners")->fetchAll();
$bannerTitles = [
    'Flash Sale ลดสูงสุด 90%',
    'ส่งฟรีทุกออเดอร์',
    'สมาชิกใหม่ลด 100 บาท',
    'ShopeePay รับเงินคืน 2%',
    'ช้อปครบ 499 ส่งฟรี',
];
foreach ($banners as $i => $banner) {
    $title = $bannerTitles[$i % count($bannerTitles)];
    try {
        $stmt = $db->prepare("UPDATE banners SET title = ? WHERE banner_id = ?");
        $stmt->execute([$title, $banner['banner_id']]);
        echo "  ✓ ID {$banner['banner_id']}: {$title}\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$banner['banner_id']}: {$e->getMessage()}\n";
    }
}

// Fix flash sales
echo "\n[10] Fixing flash sales...\n";
$flashSales = $db->query("SELECT flash_sale_id FROM flash_sales")->fetchAll();
$flashTitles = [
    'Flash Sale 11.11',
    'เที่ยงวันนี้ลดราคา',
    'Super Brand Day',
    'Midnight Sale',
    'Payday Sale',
];
foreach ($flashSales as $i => $fs) {
    $title = $flashTitles[$i % count($flashTitles)];
    try {
        $stmt = $db->prepare("UPDATE flash_sales SET title = ? WHERE flash_sale_id = ?");
        $stmt->execute([$title, $fs['flash_sale_id']]);
        echo "  ✓ ID {$fs['flash_sale_id']}: {$title}\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$fs['flash_sale_id']}: {$e->getMessage()}\n";
    }
}

// Fix shipping providers
echo "\n[11] Fixing shipping providers...\n";
$providers = $db->query("SELECT provider_id FROM shipping_providers")->fetchAll();
$providerNames = [
    'Kerry Express',
    'Flash Home',
    'J&T Express',
    'Thai Post',
    'DHL Express',
    'Ninja Van',
    'Best Express',
];
foreach ($providers as $i => $prov) {
    $name = $providerNames[$i % count($providerNames)];
    try {
        $stmt = $db->prepare("UPDATE shipping_providers SET name = ? WHERE provider_id = ?");
        $stmt->execute([$name, $prov['provider_id']]);
        echo "  ✓ ID {$prov['provider_id']}: {$name}\n";
        $totalFixed++;
    } catch (Exception $e) {
        echo "  ✗ Error ID {$prov['provider_id']}: {$e->getMessage()}\n";
    }
}

echo "\n=== สรุป ===\n";
echo "แก้ไขทั้งหมด: {$totalFixed} รายการ\n";
echo "\nเสร็จสิ้น! ข้อมูลทั้งหมดเป็นภาษาไทยแล้ว 🇹🇭\n";
echo "กรุณารีเฟรชหน้าเว็บเพื่อดูการเปลี่ยนแปลง\n";
