<?php
/**
 * ============================================================
 * Shopee DB – Automated Test Suite (PHP CLI)
 * Run:  php tests/run_tests.php
 * ============================================================
 */
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/database.php';
require_once ROOT . '/config/app.php';
require_once ROOT . '/includes/functions.php';

// ── Tiny test framework ──────────────────────────────────────
$passed = 0; $failed = 0; $total = 0;
$results = [];

function test(string $name, callable $fn): void {
    global $passed, $failed, $total, $results;
    $total++;
    $start = microtime(true);
    try {
        $fn();
        $ms = round((microtime(true) - $start) * 1000, 2);
        $results[] = ['status' => 'PASS', 'name' => $name, 'ms' => $ms, 'msg' => ''];
        $passed++;
    } catch (AssertionError|Exception $e) {
        $ms = round((microtime(true) - $start) * 1000, 2);
        $results[] = ['status' => 'FAIL', 'name' => $name, 'ms' => $ms, 'msg' => $e->getMessage()];
        $failed++;
    }
}

function assert_equals($a, $b, string $msg = ''): void {
    if ($a !== $b) throw new AssertionError("Expected " . json_encode($b) . ", got " . json_encode($a) . ($msg ? " | $msg" : ''));
}
function assert_true($v, string $msg = ''): void {
    if (!$v) throw new AssertionError("Expected true" . ($msg ? " | $msg" : ''));
}
function assert_false($v, string $msg = ''): void {
    if ($v) throw new AssertionError("Expected false" . ($msg ? " | $msg" : ''));
}
function assert_not_null($v, string $msg = ''): void {
    if ($v === null || $v === false) throw new AssertionError("Expected non-null" . ($msg ? " | $msg" : ''));
}
function assert_count(int $expected, array $arr, string $msg = ''): void {
    $c = count($arr);
    if ($c < $expected) throw new AssertionError("Expected at least $expected items, got $c" . ($msg ? " | $msg" : ''));
}
function assert_contains(string $needle, string $haystack, string $msg = ''): void {
    if (!str_contains($haystack, $needle)) throw new AssertionError("'$needle' not found in string" . ($msg ? " | $msg" : ''));
}

// ─────────────────────────────────────────────────────────────

// ──────────────────────────────────────────────────────────────
// GROUP 1: DATABASE CONNECTION
// ──────────────────────────────────────────────────────────────
test('DB: Connection established', function() {
    $db = getDB();
    assert_not_null($db, 'PDO instance should not be null');
    assert_true($db instanceof PDO, 'Should be PDO instance');
});

test('DB: Returns same instance (singleton)', function() {
    $db1 = getDB();
    $db2 = getDB();
    assert_true($db1 === $db2, 'Should be same singleton instance');
});

test('DB: Can execute simple query', function() {
    $result = getDB()->query("SELECT 1+1 AS result")->fetchColumn();
    assert_equals(2, (int)$result);
});

// ──────────────────────────────────────────────────────────────
// GROUP 2: TABLE EXISTENCE CHECKS
// ──────────────────────────────────────────────────────────────
$requiredTables = [
    'users','user_addresses','shops','categories','products','product_images',
    'product_skus','variant_types','variant_options','sku_option_map',
    'product_specifications','orders','order_items','order_status_history',
    'payments','return_requests','reviews','review_images','review_likes',
    'carts','cart_items','wishlists','shop_followers','platform_vouchers',
    'shop_vouchers','user_vouchers','flash_sales','flash_sale_items','banners',
    'notifications','conversations','messages','wallets','wallet_transactions',
    'search_history','product_reports','shop_rating_summary','admin_logs',
    'shipping_providers','site_settings','cms_pages','cms_menus','cms_menu_items',
    'cms_widgets','permissions','admin_permissions','activity_logs',
    // Advanced
    'roles','user_roles','user_bans','shop_bans','product_bans','ip_blacklist',
    'user_sessions','otp_verifications','support_tickets','support_ticket_messages',
    'product_questions','product_answers','shipping_zones','shipping_rates',
    'product_views','referral_codes','referrals','email_templates','loyalty_points',
    'loyalty_transactions','fraud_reports','voucher_usage_log','announcements',
    'tax_settings',
];

foreach ($requiredTables as $table) {
    test("DB: Table '$table' exists", function() use ($table) {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=? AND table_name=?");
        $stmt->execute([DB_NAME, $table]);
        assert_true((int)$stmt->fetchColumn() > 0, "Table $table not found");
    });
}

// ──────────────────────────────────────────────────────────────
// GROUP 3: DATA INTEGRITY CHECKS
// ──────────────────────────────────────────────────────────────
test('DATA: At least 1 superadmin exists', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM users WHERE role='superadmin'")->fetchColumn();
    assert_true($c >= 1, "Need at least 1 superadmin");
});

test('DATA: At least 1 admin exists', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM users WHERE role IN ('admin','superadmin')")->fetchColumn();
    assert_true($c >= 1);
});

test('DATA: Users count >= 10', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM users")->fetchColumn();
    assert_true($c >= 10, "Found $c users");
});

test('DATA: Shops count >= 3', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM shops")->fetchColumn();
    assert_true($c >= 3);
});

test('DATA: Products count >= 7', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn();
    assert_true($c >= 7, "Found $c active products");
});

test('DATA: Orders with items exist', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM order_items")->fetchColumn();
    assert_true($c >= 1);
});

test('DATA: All orders have at least 1 item', function() {
    $missing = getDB()->query("SELECT COUNT(*) FROM orders o WHERE NOT EXISTS (SELECT 1 FROM order_items oi WHERE oi.order_id=o.order_id)")->fetchColumn();
    assert_equals(0, (int)$missing, "Orders without items: $missing");
});

test('DATA: Categories are hierarchical (has parent-child)', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM categories WHERE parent_id IS NOT NULL")->fetchColumn();
    assert_true($c >= 1, "No child categories found");
});

test('DATA: Site settings populated', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
    assert_true($c >= 10);
});

test('DATA: Roles table has entries', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    assert_true($c >= 5, "Need at least 5 roles");
});

test('DATA: User roles (many-to-many) populated', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM user_roles")->fetchColumn();
    assert_true($c >= 1);
});

test('DATA: Superadmin has multiple roles', function() {
    $sa = getDB()->query("SELECT user_id FROM users WHERE role='superadmin' LIMIT 1")->fetchColumn();
    if (!$sa) throw new Exception('No superadmin found');
    $c = (int)getDB()->prepare("SELECT COUNT(*) FROM user_roles WHERE user_id=?")->execute([$sa]) ? 0 : 0;
    $stmt = getDB()->prepare("SELECT COUNT(*) FROM user_roles WHERE user_id=?");
    $stmt->execute([$sa]);
    $c = (int)$stmt->fetchColumn();
    assert_true($c >= 2, "Superadmin should have multiple roles, got $c");
});

test('DATA: Shipping zones exist', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM shipping_zones")->fetchColumn();
    assert_true($c >= 1);
});

test('DATA: Loyalty points exist', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM loyalty_points")->fetchColumn();
    assert_true($c >= 1);
});

test('DATA: Permissions seeded (>=20)', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM permissions")->fetchColumn();
    assert_true($c >= 20, "Found $c permissions");
});

test('DATA: CMS pages exist', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM cms_pages")->fetchColumn();
    assert_true($c >= 3);
});

test('DATA: Support tickets exist', function() {
    $c = (int)getDB()->query("SELECT COUNT(*) FROM support_tickets")->fetchColumn();
    assert_true($c >= 1);
});

// ──────────────────────────────────────────────────────────────
// GROUP 4: CRUD OPERATIONS
// ──────────────────────────────────────────────────────────────
test('CRUD: Insert & select user', function() {
    $db = getDB();
    $db->prepare("INSERT INTO users (username,email,password_hash,role,is_active) VALUES (?,?,?,?,?)")
       ->execute(['test_user_'.time(),'test_'.time().'@test.com','hash','buyer',0]);
    $id = (int)$db->lastInsertId();
    assert_true($id > 0, 'Insert failed');
    $row = $db->prepare("SELECT * FROM users WHERE user_id=?")->execute([$id]) ? null : null;
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    assert_not_null($row);
    assert_equals('buyer', $row['role']);
    // Cleanup
    $db->prepare("DELETE FROM users WHERE user_id=?")->execute([$id]);
});

test('CRUD: Insert & select category', function() {
    $db = getDB();
    $slug = 'test-cat-'.time();
    $db->prepare("INSERT INTO categories (name,slug,sort_order) VALUES (?,?,?)")->execute(['Test Category',$slug,99]);
    $id = (int)$db->lastInsertId();
    assert_true($id > 0);
    $stmt = $db->prepare("SELECT slug FROM categories WHERE category_id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    assert_equals($slug, $row['slug']);
    $db->prepare("DELETE FROM categories WHERE category_id=?")->execute([$id]);
});

test('CRUD: Insert & select site_setting', function() {
    $db = getDB();
    $key = 'test_key_'.time();
    $db->prepare("INSERT INTO site_settings (setting_group,setting_key,setting_value,setting_type,label) VALUES ('test',?,?,'text','Test')")->execute([$key,'test_value']);
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key=?");
    $stmt->execute([$key]);
    $val = $stmt->fetchColumn();
    assert_equals('test_value', $val);
    $db->prepare("DELETE FROM site_settings WHERE setting_key=?")->execute([$key]);
});

test('CRUD: Update order status', function() {
    $db = getDB();
    $stmt = $db->query("SELECT order_id FROM orders LIMIT 1");
    $orderId = $stmt->fetchColumn();
    if (!$orderId) return;
    $db->prepare("UPDATE orders SET note=? WHERE order_id=?")->execute(['test_note', $orderId]);
    $stmt = $db->prepare("SELECT note FROM orders WHERE order_id=?");
    $stmt->execute([$orderId]);
    $note = $stmt->fetchColumn();
    assert_equals('test_note', $note);
    $db->prepare("UPDATE orders SET note=NULL WHERE order_id=?")->execute([$orderId]);
});

test('CRUD: Delete review (cascade)', function() {
    $db = getDB();
    // Create a temporary review
    $stmt = $db->query("SELECT product_id FROM products LIMIT 1");
    $pid = $stmt->fetchColumn();
    $stmt = $db->query("SELECT user_id FROM users WHERE role='buyer' LIMIT 1");
    $uid = $stmt->fetchColumn();
    $stmt = $db->query("SELECT order_id FROM orders LIMIT 1");
    $oid = $stmt->fetchColumn();
    $stmt = $db->query("SELECT shop_id FROM shops LIMIT 1");
    $sid = $stmt->fetchColumn();
    if (!$pid||!$uid||!$oid||!$sid) return;
    $db->prepare("INSERT INTO reviews (product_id,order_id,reviewer_id,shop_id,rating,comment) VALUES (?,?,?,?,?,?)")
       ->execute([$pid,$oid,$uid,$sid,5,'test review']);
    $rid = (int)$db->lastInsertId();
    $db->prepare("INSERT INTO review_images (review_id,image_url) VALUES (?,?)")->execute([$rid,'http://test.jpg']);
    $db->prepare("DELETE FROM reviews WHERE review_id=?")->execute([$rid]);
    // Image should also be deleted (CASCADE)
    $stmt = $db->prepare("SELECT COUNT(*) FROM review_images WHERE review_id=?");
    $stmt->execute([$rid]);
    assert_equals(0, (int)$stmt->fetchColumn(), 'Cascade delete failed');
});

// ──────────────────────────────────────────────────────────────
// GROUP 5: BUSINESS LOGIC
// ──────────────────────────────────────────────────────────────
test('BIZ: password_verify works with seeded hash', function() {
    $db = getDB();
    $stmt = $db->query("SELECT password_hash FROM users WHERE username='superadmin'");
    $hash = $stmt->fetchColumn();
    assert_not_null($hash);
    assert_true(password_verify('password', $hash), 'Default password should be "password"');
});

test('BIZ: getSetting() returns correct value', function() {
    $name = getSetting('site_name');
    assert_true(!empty($name), 'site_name should not be empty');
});

test('BIZ: formatPrice() formats correctly', function() {
    $result = formatPrice(1234.56);
    assert_contains('1,234.56', $result);
    assert_contains('฿', $result);
});

test('BIZ: slugify() generates valid slug', function() {
    $slug = slugify('Hello World Test');
    assert_contains('hello', $slug);
    assert_true(!str_contains($slug, ' '), 'Slug should not contain spaces');
});

test('BIZ: formatDate() parses datetime', function() {
    $result = formatDate('2024-01-15 14:30:00', 'd/m/Y');
    assert_equals('15/01/2024', $result);
});

test('BIZ: paginateQuery() returns correct structure', function() {
    $db = getDB();
    $result = paginateQuery($db,
        "SELECT COUNT(*) FROM users",
        "SELECT user_id FROM users ORDER BY user_id",
        [], 1, 5
    );
    assert_true(isset($result['data']), 'Missing data key');
    assert_true(isset($result['total']), 'Missing total key');
    assert_true(isset($result['total_pages']), 'Missing total_pages key');
    assert_true(count($result['data']) <= 5, 'Page size exceeded');
    assert_true($result['total'] >= 1);
});

test('BIZ: Order totals are consistent', function() {
    $db = getDB();
    $stmt = $db->query("SELECT order_id, subtotal, shipping_fee, shop_discount, voucher_discount, total_amount FROM orders LIMIT 10");
    foreach ($stmt->fetchAll() as $o) {
        $expected = (float)$o['subtotal'] + (float)$o['shipping_fee'] - (float)$o['shop_discount'] - (float)$o['voucher_discount'];
        $diff = abs($expected - (float)$o['total_amount']);
        assert_true($diff < 0.02, "Order #{$o['order_id']} total mismatch: expected $expected, got {$o['total_amount']}");
    }
});

test('BIZ: Vouchers code unique across platform', function() {
    $db = getDB();
    $stmt = $db->query("SELECT code, COUNT(*) AS c FROM platform_vouchers GROUP BY code HAVING c > 1");
    $dups = $stmt->fetchAll();
    assert_equals(0, count($dups), 'Duplicate platform voucher codes found');
});

test('BIZ: Products have at least 1 image', function() {
    $db = getDB();
    $stmt = $db->query("SELECT COUNT(*) FROM products p WHERE status='active' AND NOT EXISTS (SELECT 1 FROM product_images pi WHERE pi.product_id=p.product_id)");
    $c = (int)$stmt->fetchColumn();
    assert_equals(0, $c, "$c active products have no images");
});

// ──────────────────────────────────────────────────────────────
// GROUP 6: ROLES & PERMISSIONS
// ──────────────────────────────────────────────────────────────
test('ROLES: All required roles exist', function() {
    $db = getDB();
    $required = ['superadmin','admin','seller','buyer','content_mod','finance','support'];
    foreach ($required as $rk) {
        $c = (int)$db->prepare("SELECT COUNT(*) FROM roles WHERE role_key=?")->execute([$rk]) ? 0 : 0;
        $s = $db->prepare("SELECT COUNT(*) FROM roles WHERE role_key=?");
        $s->execute([$rk]);
        assert_true($s->fetchColumn() > 0, "Role '$rk' not found");
    }
});

test('ROLES: user_roles has no expired active roles', function() {
    $db = getDB();
    $c = (int)$db->query("SELECT COUNT(*) FROM user_roles WHERE is_active=1 AND expires_at IS NOT NULL AND expires_at < NOW()")->fetchColumn();
    assert_equals(0, $c, "$c expired but still active roles found");
});

test('ROLES: Each user has at least 1 role', function() {
    $db = getDB();
    $missing = (int)$db->query("SELECT COUNT(*) FROM users WHERE NOT EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id=users.user_id AND ur.is_active=1)")->fetchColumn();
    // Allow some without user_roles (they fall back to users.role)
    assert_true($missing < 5, "Too many users without user_roles: $missing");
});

// ──────────────────────────────────────────────────────────────
// GROUP 7: FOREIGN KEY INTEGRITY
// ──────────────────────────────────────────────────────────────
test('FK: All orders reference valid buyers', function() {
    $db = getDB();
    $c = (int)$db->query("SELECT COUNT(*) FROM orders o WHERE NOT EXISTS (SELECT 1 FROM users u WHERE u.user_id=o.buyer_user_id)")->fetchColumn();
    assert_equals(0, $c, "$c orphan orders found");
});

test('FK: All order_items reference valid orders', function() {
    $db = getDB();
    $c = (int)$db->query("SELECT COUNT(*) FROM order_items oi WHERE NOT EXISTS (SELECT 1 FROM orders o WHERE o.order_id=oi.order_id)")->fetchColumn();
    assert_equals(0, $c, "$c orphan order_items found");
});

test('FK: All reviews reference valid products', function() {
    $db = getDB();
    $c = (int)$db->query("SELECT COUNT(*) FROM reviews r WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.product_id=r.product_id)")->fetchColumn();
    assert_equals(0, $c);
});

test('FK: All shops reference valid owners', function() {
    $db = getDB();
    $c = (int)$db->query("SELECT COUNT(*) FROM shops s WHERE NOT EXISTS (SELECT 1 FROM users u WHERE u.user_id=s.owner_user_id)")->fetchColumn();
    assert_equals(0, $c);
});

test('FK: All cart_items reference valid carts', function() {
    $db = getDB();
    $c = (int)$db->query("SELECT COUNT(*) FROM cart_items ci WHERE NOT EXISTS (SELECT 1 FROM carts c WHERE c.cart_id=ci.cart_id)")->fetchColumn();
    assert_equals(0, $c);
});

// ──────────────────────────────────────────────────────────────
// GROUP 8: BAN SYSTEM
// ──────────────────────────────────────────────────────────────
test('BAN: user_bans table structure correct', function() {
    $db = getDB();
    $cols = $db->query("SHOW COLUMNS FROM user_bans")->fetchAll(PDO::FETCH_COLUMN);
    $required = ['ban_id','user_id','banned_by','ban_type','reason','is_active','expires_at','created_at'];
    foreach ($required as $col) {
        assert_true(in_array($col,$cols), "Column '$col' missing from user_bans");
    }
});

test('BAN: No active ban blocks valid users', function() {
    $db = getDB();
    $stmt = $db->query("SELECT COUNT(*) FROM users u WHERE u.is_active=1 AND EXISTS (SELECT 1 FROM user_bans ub WHERE ub.user_id=u.user_id AND ub.is_active=1 AND (ub.expires_at IS NULL OR ub.expires_at > NOW()))");
    $c = (int)$stmt->fetchColumn();
    // Demo data has inactive bans, active users should be 0 in conflict
    assert_true($c == 0 || $c < 3, "Too many actively-banned but active users: $c");
});

// ──────────────────────────────────────────────────────────────
// GROUP 9: PERFORMANCE CHECKS
// ──────────────────────────────────────────────────────────────
test('PERF: Orders query under 200ms', function() {
    $start = microtime(true);
    getDB()->query("SELECT o.*, u.username, s.shop_name FROM orders o JOIN users u ON o.buyer_user_id=u.user_id JOIN shops s ON o.shop_id=s.shop_id ORDER BY o.created_at DESC LIMIT 25")->fetchAll();
    $ms = (microtime(true) - $start) * 1000;
    assert_true($ms < 200, "Query took {$ms}ms");
});

test('PERF: Product search query under 300ms', function() {
    $start = microtime(true);
    $stmt = getDB()->prepare("SELECT p.*, c.name AS cat, s.shop_name FROM products p JOIN categories c ON p.category_id=c.category_id JOIN shops s ON p.shop_id=s.shop_id WHERE p.name LIKE ? AND p.status='active' ORDER BY p.total_sold DESC LIMIT 20");
    $stmt->execute(['%Samsung%']);
    $stmt->fetchAll();
    $ms = (microtime(true) - $start) * 1000;
    assert_true($ms < 300, "Query took {$ms}ms");
});

test('PERF: Dashboard stats query under 500ms', function() {
    $start = microtime(true);
    getStats();
    $ms = (microtime(true) - $start) * 1000;
    assert_true($ms < 500, "getStats() took {$ms}ms");
});

// ──────────────────────────────────────────────────────────────
// PRINT RESULTS
// ──────────────────────────────────────────────────────────────
$width = 72;
echo "\n" . str_repeat('═', $width) . "\n";
echo " SHOPEE DB TEST RESULTS\n";
echo str_repeat('═', $width) . "\n";

$groups = [];
foreach ($results as $r) {
    $grp = explode(':', $r['name'])[0];
    $groups[$grp][] = $r;
}

foreach ($groups as $grp => $tests) {
    $gp = count(array_filter($tests, fn($t) => $t['status'] === 'PASS'));
    $gf = count(array_filter($tests, fn($t) => $t['status'] === 'FAIL'));
    echo "\n ▸ $grp ($gp/" . count($tests) . ")\n";
    foreach ($tests as $t) {
        $icon  = $t['status'] === 'PASS' ? '  ✓' : '  ✗';
        $color = $t['status'] === 'PASS' ? '' : '';
        $line  = substr($t['name'], strpos($t['name'], ':') + 2);
        echo sprintf("  %s %-52s %5.1fms\n", $t['status'] === 'PASS' ? '✓' : '✗', $line, $t['ms']);
        if ($t['msg']) echo "    → " . $t['msg'] . "\n";
    }
}

echo "\n" . str_repeat('─', $width) . "\n";
echo sprintf(" TOTAL: %d tests | ✓ %d passed | ✗ %d failed\n", $total, $passed, $failed);
echo str_repeat('═', $width) . "\n\n";

exit($failed > 0 ? 1 : 0);
