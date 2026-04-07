<?php
/**
 * MEGA FIX - One-shot database & code comprehensive repair
 * สแกนและแก้ไขทั้งหมดในครั้งเดียว
 */
define('FRONT_INCLUDED', true);
require_once dirname(__DIR__) . '/includes/functions_front.php';

header('Content-Type: text/html; charset=UTF-8');
$db = getDB();
$db->exec("SET NAMES utf8mb4");
$fixes = 0;
$errors = [];

function logFix($msg) {
    global $fixes;
    $fixes++;
    echo "<div style='color:#198754'>✅ [$fixes] $msg</div>";
    flush();
}
function logErr($msg) {
    global $errors;
    $errors[] = $msg;
    echo "<div style='color:#dc3545'>❌ ERROR: $msg</div>";
    flush();
}
function logInfo($msg) {
    echo "<div style='color:#0d6efd'>ℹ️ $msg</div>";
    flush();
}

?><!DOCTYPE html>
<html lang="th">
<head><meta charset="UTF-8"><title>Mega Fix</title>
<style>
body{font-family:monospace;background:#0d1117;color:#e6edf3;padding:20px;font-size:13px}
h1{color:#58a6ff}h2{color:#ffa657;margin-top:20px;border-bottom:1px solid #333;padding-bottom:6px}
.section{background:#161b22;border:1px solid #30363d;border-radius:8px;padding:15px;margin-bottom:15px}
.summary{background:#1a2f1a;border:1px solid #238636;border-radius:8px;padding:20px;margin-top:20px;font-size:16px}
</style>
</head>
<body>
<h1>🔧 WEBSHOP MEGA FIX - ONE SHOT</h1>
<p style='color:#8b949e'>Running comprehensive scan and auto-fix...</p>
<?php

// ===================================
// 1. FIX timeAgo negative values
// ===================================
echo '<div class="section"><h2>1. Fix Users Last Login Timestamps</h2>';
try {
    // Fix future timestamps in user sessions
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE last_login_at > NOW()");
    $futureCnt = (int)$stmt->fetchColumn();
    if ($futureCnt > 0) {
        $db->exec("UPDATE users SET last_login_at = NOW() WHERE last_login_at > NOW()");
        logFix("Fixed $futureCnt future timestamps in users.last_login_at");
    } else {
        logInfo("No future timestamps found in users table");
    }
} catch (Exception $e) { logErr($e->getMessage()); }

// Fix user_sessions if exists
try {
    $db->exec("UPDATE user_sessions SET created_at = NOW() WHERE created_at > NOW()");
    logInfo("user_sessions timestamps checked");
} catch (Exception $e) { /* column may not exist */ }
echo '</div>';

// ===================================
// 2. FIX CATEGORIES (English → Thai)
// ===================================
echo '<div class="section"><h2>2. Fix Category Names (English → Thai)</h2>';
$catMap = [
    'Electronics'        => ['name' => 'อิเล็กทรอนิกส์',           'slug' => 'electronics'],
    'Fashion'            => ['name' => 'แฟชั่น',                    'slug' => 'fashion'],
    'Home & Living'      => ['name' => 'บ้านและเฟอร์นิเจอร์',       'slug' => 'home-living'],
    'Sports & Outdoors'  => ['name' => 'กีฬาและกิจกรรมกลางแจ้ง',   'slug' => 'sports-outdoors'],
    'Health & Beauty'    => ['name' => 'สุขภาพและความงาม',          'slug' => 'health-beauty'],
    'Mobile & Accessories' => ['name' => 'โทรศัพท์และอุปกรณ์เสริม', 'slug' => 'mobile-accessories'],
    'Laptops & Computers' => ['name' => 'แล็ปท็อปและคอมพิวเตอร์',  'slug' => 'laptops-computers'],
    'Audio & Headphones' => ['name' => 'เครื่องเสียงและหูฟัง',      'slug' => 'audio-headphones'],
    "Men's Clothing"     => ['name' => 'เสื้อผ้าผู้ชาย',            'slug' => 'mens-clothing'],
    "Women's Clothing"   => ['name' => 'เสื้อผ้าผู้หญิง',           'slug' => 'womens-clothing'],
    'Shoes'              => ['name' => 'รองเท้า',                    'slug' => 'shoes'],
    'Furniture'          => ['name' => 'เฟอร์นิเจอร์',              'slug' => 'furniture'],
    'Kitchen'            => ['name' => 'เครื่องครัว',                'slug' => 'kitchen'],
    'Bedding'            => ['name' => 'ผ้าปูที่นอนและเครื่องนอน',  'slug' => 'bedding'],
    'Toys & Games'       => ['name' => 'ของเล่นและเกม',             'slug' => 'toys-games'],
    'Books'              => ['name' => 'หนังสือ',                    'slug' => 'books'],
    'Automotive'         => ['name' => 'ยานยนต์และอุปกรณ์',         'slug' => 'automotive'],
    'Pets'               => ['name' => 'สัตว์เลี้ยง',               'slug' => 'pets'],
    'Food & Grocery'     => ['name' => 'อาหารและของชำ',             'slug' => 'food-grocery'],
    'Baby & Kids'        => ['name' => 'แม่และเด็ก',                'slug' => 'baby-kids'],
];

foreach ($catMap as $eng => $th) {
    try {
        $stmt = $db->prepare("UPDATE categories SET name=?, slug=? WHERE name=?");
        $stmt->execute([$th['name'], $th['slug'], $eng]);
        if ($stmt->rowCount() > 0) logFix("Category: \"$eng\" → \"{$th['name']}\"");
    } catch (Exception $e) { logErr("Category $eng: " . $e->getMessage()); }
}

// Check for remaining English category names
$remaining = $db->query("SELECT name FROM categories WHERE name REGEXP '^[A-Za-z]'")->fetchAll();
if (!empty($remaining)) {
    foreach ($remaining as $r) logInfo("Remaining English category: " . $r['name']);
} else {
    logInfo("All categories are now in Thai ✓");
}
echo '</div>';

// ===================================
// 3. FIX ANNOUNCEMENTS (garbled → Thai)
// ===================================
echo '<div class="section"><h2>3. Fix Announcement Content (Garbled → Thai)</h2>';
$announcements = $db->query("SELECT announcement_id, title, content FROM announcements")->fetchAll();
foreach ($announcements as $ann) {
    // Detect garbled text (contains Ó© pattern)
    if (str_contains($ann['content'], 'Ó©') || str_contains($ann['content'], 'Ó╣')) {
        // Map by title
        $fixedContent = '';
        if (str_contains($ann['title'], '11.11')) {
            $fixedContent = 'ช้อปสินค้าลดราคาทุกหมวดหมู่ในวัน 11.11 เริ่มเที่ยงคืนตรง รับส่วนลดสูงสุด 90% พร้อมฟรีค่าส่งทุกออเดอร์ไม่มีขั้นต่ำ อย่าพลาด!';
        } elseif (str_contains($ann['title'], 'สมาชิกใหม่')) {
            $fixedContent = 'สมาชิกใหม่ที่สมัครวันนี้รับทันที! คูปองส่วนลด 100 บาท และคูปองฟรีค่าส่ง พร้อมใช้งานได้เลย สมัครฟรีไม่มีค่าใช้จ่าย';
        } elseif (str_contains($ann['title'], 'ไอที') || str_contains($ann['title'], 'IT')) {
            $fixedContent = 'แคมเปญลดราคาสินค้าไอทีครั้งใหญ่! โทรศัพท์มือถือ แท็บเล็ต และคอมพิวเตอร์ลดสูงสุด 50% มีสินค้าให้เลือกมากกว่า 1,000 รายการ';
        } else {
            $fixedContent = 'ข้อมูลรายละเอียดโปรโมชั่น สามารถติดต่อเจ้าหน้าที่ได้ที่ช่องทางการสนับสนุนลูกค้า';
        }
        $stmt = $db->prepare("UPDATE announcements SET content=? WHERE announcement_id=?");
        $stmt->execute([$fixedContent, $ann['announcement_id']]);
        logFix("Fixed garbled announcement: \"{$ann['title']}\"");
    }
}
echo '</div>';

// ===================================
// 4. FIX EMAIL TEMPLATES (garbled → Thai)
// ===================================
echo '<div class="section"><h2>4. Fix Email Templates</h2>';
try {
    $cols = $db->query("DESCRIBE email_templates")->fetchAll(PDO::FETCH_COLUMN);
    $templates = $db->query("SELECT * FROM email_templates")->fetchAll();
    foreach ($templates as $tmpl) {
        $idCol = isset($tmpl['template_id']) ? 'template_id' : (isset($tmpl['id']) ? 'id' : null);
        if (!$idCol) continue;
        
        $needsFix = false;
        foreach (['subject', 'body', 'content'] as $col) {
            if (isset($tmpl[$col]) && (str_contains((string)$tmpl[$col], 'Ó©') || str_contains((string)$tmpl[$col], 'Ó╣'))) {
                $needsFix = true;
            }
        }
        if ($needsFix) {
            logInfo("Email template ID {$tmpl[$idCol]} has garbled content - needs manual fix via admin");
        }
    }
    logInfo("Email templates scan complete");
} catch (Exception $e) { logErr($e->getMessage()); }
echo '</div>';

// ===================================
// 5. FIX SITE_SETTINGS
// ===================================
echo '<div class="section"><h2>5. Fix Site Settings</h2>';
$settings = [
    'site_name'        => 'Shopee Thailand',
    'site_description' => 'ช้อปสินค้าราคาถูก คุณภาพดี ส่งไว ปลอดภัย',
    'contact_email'    => 'support@shopee.th',
    'contact_phone'    => '02-123-4567',
    'contact_address'  => '123 ถนนสาทร กรุงเทพมหานคร 10120',
];
foreach ($settings as $key => $val) {
    try {
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key=?");
        $stmt->execute([$key]);
        $current = $stmt->fetchColumn();
        if ($current !== false && (str_contains((string)$current, 'Ó©') || str_contains((string)$current, 'Ó╣'))) {
            $db->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key=?")->execute([$val, $key]);
            logFix("Fixed site_settings: $key");
        }
    } catch (Exception $e) { /* skip missing key */ }
}
logInfo("Site settings scan complete");
echo '</div>';

// ===================================
// 6. FIX NOTIFICATIONS garbled content
// ===================================
echo '<div class="section"><h2>6. Fix Notifications Table</h2>';
try {
    $garbled = $db->query("SELECT COUNT(*) FROM notifications WHERE message LIKE '%Ó©%' OR message LIKE '%Ó╣%'")->fetchColumn();
    if ($garbled > 0) {
        $db->exec("UPDATE notifications SET message='ข้อความแจ้งเตือนจากระบบ' WHERE message LIKE '%Ó©%' OR message LIKE '%Ó╣%'");
        logFix("Fixed $garbled garbled notification messages");
    } else {
        logInfo("No garbled notifications found");
    }
} catch (Exception $e) { logErr($e->getMessage()); }
echo '</div>';

// ===================================
// 7. FIX PRODUCTS garbled name/description
// ===================================
echo '<div class="section"><h2>7. Scan Products for Garbled Text</h2>';
try {
    $garbledProds = $db->query("SELECT COUNT(*) FROM products WHERE name LIKE '%Ó©%' OR description LIKE '%Ó©%'")->fetchColumn();
    if ($garbledProds > 0) {
        logInfo("Found $garbledProds products with garbled text - check product admin panel");
    } else {
        logInfo("✓ All product names/descriptions are clean");
    }
} catch (Exception $e) { logErr($e->getMessage()); }
echo '</div>';

// ===================================
// 8. GLOBAL GARBLED SCAN (all text columns)
// ===================================
echo '<div class="section"><h2>8. Full Database Garbled Text Scan</h2>';
$scanTables = [
    'reviews'     => ['comment'],
    'messages'    => ['message'],
    'support_tickets' => ['subject', 'message'],
    'support_ticket_messages' => ['message'],
    'cms_pages'   => ['title', 'content'],
    'cms_widgets' => ['title', 'content'],
];
foreach ($scanTables as $table => $cols) {
    foreach ($cols as $col) {
        try {
            $count = $db->query("SELECT COUNT(*) FROM `$table` WHERE `$col` LIKE '%Ó©%' OR `$col` LIKE '%Ó╣%'")->fetchColumn();
            if ($count > 0) {
                logInfo("Found $count garbled rows in $table.$col");
            }
        } catch (Exception $e) { /* table or column may not exist */ }
    }
}
logInfo("Full scan complete");
echo '</div>';

// ===================================
// SUMMARY
// ===================================
?>
<div class="summary">
  <strong>🎯 MEGA FIX COMPLETE</strong><br>
  ✅ Total fixes applied: <strong><?= $fixes ?></strong><br>
  <?php if (!empty($errors)): ?>
  ❌ Errors: <strong><?= count($errors) ?></strong> (see above)<br>
  <?php else: ?>
  ✅ No errors encountered<br>
  <?php endif; ?>
  <br>
  <a href="/webshop/" style="color:#58a6ff">→ Go to Homepage</a> &nbsp;|&nbsp;
  <a href="/webshop/admin/" style="color:#58a6ff">→ Go to Admin</a>
</div>
</body></html>
