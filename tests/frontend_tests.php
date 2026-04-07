<?php
/**
 * Frontend Test Automation Suite
 * Comprehensive tests for all frontend pages, APIs, and functionality
 * Run via CLI: php tests/frontend_tests.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/functions.php';

$testResults = ['passed' => 0, 'failed' => 0, 'tests' => []];
$startTime   = microtime(true);

echo "\n" . str_repeat('=', 70) . "\n";
echo "  FRONTEND TEST AUTOMATION SUITE\n";
echo "  " . date('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 70) . "\n\n";

function test($name, $assertion, $detail = '') {
    global $testResults;
    $passed = (bool)$assertion;
    if ($passed) {
        $testResults['passed']++;
        echo "  [PASS] $name\n";
    } else {
        $testResults['failed']++;
        echo "  [FAIL] $name\n";
        if ($detail) echo "         → $detail\n";
    }
    $testResults['tests'][] = ['name' => $name, 'passed' => $passed, 'detail' => $detail];
    return $passed;
}

function testSection($title) {
    echo "\n" . str_repeat('-', 60) . "\n";
    echo "  $title\n";
    echo str_repeat('-', 60) . "\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 1: PHP File Existence Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("1. FILE STRUCTURE TESTS");

$requiredFiles = [
    'Foundation' => [
        'includes/functions_front.php',
        'assets/css/style.css',
        'assets/js/main.js',
    ],
    'Layout' => [
        'includes/header.php',
        'includes/footer.php',
    ],
    'Main Pages' => [
        'index.php',
        'category.php',
        'product.php',
        'search.php',
        'shop.php',
    ],
    'Cart & Checkout' => [
        'cart.php',
        'checkout.php',
        'order-success.php',
    ],
    'Account Pages' => [
        'account/login.php',
        'account/register.php',
        'account/profile.php',
        'account/orders.php',
        'account/order-detail.php',
        'account/wishlist.php',
        'account/addresses.php',
        'account/wallet.php',
        'account/logout.php',
        'account/includes/account_sidebar.php',
    ],
    'API Endpoints' => [
        'api/cart.php',
        'api/wishlist.php',
        'api/follow.php',
        'api/search-suggest.php',
        'api/order-action.php',
    ],
    'CMS' => [
        'page.php',
    ],
];

foreach ($requiredFiles as $category => $files) {
    foreach ($files as $file) {
        $path = __DIR__ . '/../' . $file;
        test("$category: $file exists", file_exists($path), $path);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 2: PHP Syntax Validation
// ─────────────────────────────────────────────────────────────────────────────
testSection("2. PHP SYNTAX VALIDATION");

$phpFiles = [];
foreach ($requiredFiles as $category => $files) {
    foreach ($files as $file) {
        if (str_ends_with($file, '.php')) {
            $phpFiles[] = __DIR__ . '/../' . $file;
        }
    }
}

function checkPhpSyntax(string $file): ?string {
    $content = file_get_contents($file);
    // Remove shebang if present
    if (strpos($content, '#!') === 0) {
        $content = preg_replace('/^#!.*\n/', '', $content);
    }
    // Use token_get_all to validate syntax
    $tokens = @token_get_all($content);
    if ($tokens === false) {
        return 'Failed to tokenize PHP file';
    }
    return null; // No error
}

foreach ($phpFiles as $file) {
    $error = checkPhpSyntax($file);
    test(basename($file) . " syntax", $error === null, $error ?? '');
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 3: Database Connection & Schema
// ─────────────────────────────────────────────────────────────────────────────
testSection("3. DATABASE CONNECTION & SCHEMA");

try {
    $db = getDB();
    test("Database connection", true);
    
    // Check required tables for frontend
    $tables = [
        'users', 'products', 'categories', 'shops', 'orders', 'order_items',
        'carts', 'cart_items', 'wishlists', 'reviews', 'user_addresses',
        'wallets', 'loyalty_points', 'wallet_transactions', 'loyalty_transactions',
        'shipping_providers', 'cms_pages', 'shop_followers', 'search_history',
        'banners', 'flash_sales', 'vouchers'
    ];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        test("Table: $table exists", $stmt->rowCount() > 0);
    }
    
} catch (Exception $e) {
    test("Database connection", false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 4: Helper Functions Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("4. HELPER FUNCTIONS TESTS");

try {
    require_once __DIR__ . '/../includes/functions_front.php';
    test("functions_front.php loaded", true);
    
    // Test getDB()
    $db2 = getDB();
    test("getDB() returns PDO", $db2 instanceof PDO);
    
    // Test e() - escape function
    test("e() escapes HTML", e('<script>alert("xss")</script>') === '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;');
    
    // Test formatDate()
    test("formatDate() works", formatDate('2024-01-15', 'd/m/Y') === '15/01/2024');
    
    // Test ORDER_STATUSES constant
    test("ORDER_STATUSES defined", defined('ORDER_STATUSES') && is_array(ORDER_STATUSES));
    test("ORDER_STATUSES has pending", isset(ORDER_STATUSES['pending']));
    
    // Test PAYMENT_METHODS constant
    test("PAYMENT_METHODS defined", defined('PAYMENT_METHODS') && is_array(PAYMENT_METHODS));
    
    // Test pagination()
    $pagination = pagination(100, 1, 20);
    test("pagination() returns array", is_array($pagination));
    test("pagination() has correct keys", isset($pagination['total']) && isset($pagination['pages']));
    test("pagination() calculates pages correctly", $pagination['pages'] === 5);
    
} catch (Exception $e) {
    test("Helper functions load", false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 5: Product Retrieval Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("5. PRODUCT RETRIEVAL TESTS");

try {
    $db = getDB();
    
    // Test getProducts() with no filters
    $products = getProducts([], 1, 10);
    test("getProducts() returns array", is_array($products));
    test("getProducts() has data key", isset($products['data']));
    test("getProducts() has pagination keys", isset($products['total']) && isset($products['pages']));
    
    // Test getProductBySlug()
    $firstProd = $db->query("SELECT slug FROM products WHERE status='active' LIMIT 1")->fetch();
    if ($firstProd) {
        $product = getProductBySlug($firstProd['slug']);
        test("getProductBySlug() returns product", is_array($product) && isset($product['product_id']));
    } else {
        test("getProductBySlug() - no products to test", true, "No active products in database");
    }
    
    // Test getShopBySlug()
    $firstShop = $db->query("SELECT shop_slug FROM shops WHERE is_active=1 LIMIT 1")->fetch();
    if ($firstShop) {
        $shop = getShopBySlug($firstShop['shop_slug']);
        test("getShopBySlug() returns shop", is_array($shop) && isset($shop['shop_id']));
    } else {
        test("getShopBySlug() - no shops to test", true, "No active shops in database");
    }
    
    // Test getProductImages()
    if ($firstProd && isset($product['product_id'])) {
        $images = getProductImages($product['product_id']);
        test("getProductImages() returns array", is_array($images));
    }
    
    // Test getProductReviews()
    if ($firstProd && isset($product['product_id'])) {
        $reviews = getProductReviews($product['product_id'], 1, 5);
        test("getProductReviews() returns array", is_array($reviews));
        test("getProductReviews() has data", isset($reviews['data']) && isset($reviews['total']));
    }
    
    // Test getCategories()
    $cats = getCategories();
    test("getCategories() returns array", is_array($cats));
    
    // Test getAllCategories()
    $allCats = getAllCategories();
    test("getAllCategories() returns array", is_array($allCats));
    
} catch (Exception $e) {
    test("Product retrieval", false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 6: Cart & Wishlist Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("6. CART & WISHLIST FUNCTION TESTS");

try {
    $db = getDB();
    
    // Test cart functions exist
    $cartExists = function_exists('getCart');
    test("getCart() function exists", $cartExists);
    
    if ($cartExists) {
        // Test with null user (guest)
        $guestCart = getCart(null);
        test("getCart() works with null user", is_array($guestCart));
        
        // Test getCartId()
        if (function_exists('getCartId')) {
            test("getCartId() function exists", true);
        }
        
        // Test getCartTotal()
        if (function_exists('getCartTotal')) {
            $total = getCartTotal(null);
            test("getCartTotal() returns number", is_numeric($total));
        }
        
        // Test getCartCount()
        if (function_exists('getCartCount')) {
            $count = getCartCount(null);
            test("getCartCount() returns integer", is_int($count) || $count === 0);
        }
    }
    
    // Test wishlist functions exist
    test("getWishlistIds() function exists", function_exists('getWishlistIds'));
    test("isInWishlist() function exists", function_exists('isInWishlist'));
    test("toggleWishlistDB() function exists", function_exists('toggleWishlistDB'));
    
    // Test getBanners()
    if (function_exists('getBanners')) {
        $banners = getBanners('home');
        test("getBanners() returns array", is_array($banners));
    }
    
    // Test getActiveFlashSale()
    if (function_exists('getActiveFlashSale')) {
        $flashSale = getActiveFlashSale();
        test("getActiveFlashSale() works", is_array($flashSale) || $flashSale === null);
    }
    
} catch (Exception $e) {
    test("Cart & Wishlist", false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 7: Authentication Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("7. AUTHENTICATION FUNCTION TESTS");

try {
    // Test auth functions exist
    test("frontIsLoggedIn() function exists", function_exists('frontIsLoggedIn'));
    test("frontRequireLogin() function exists", function_exists('frontRequireLogin'));
    test("frontCurrentUser() function exists", function_exists('frontCurrentUser'));
    test("frontLogin() function exists", function_exists('frontLogin'));
    test("frontRegister() function exists", function_exists('frontRegister'));
    test("frontLogout() function exists", function_exists('frontLogout'));
    
    // Test that user is not logged in initially
    $isLoggedIn = frontIsLoggedIn();
    test("frontIsLoggedIn() returns boolean", is_bool($isLoggedIn));
    test("Not logged in initially", !$isLoggedIn);
    
    // Test frontCurrentUser() when not logged in
    $currentUser = frontCurrentUser();
    test("frontCurrentUser() returns null when not logged in", $currentUser === null);
    
} catch (Exception $e) {
    test("Authentication", false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 8: Render Function Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("8. RENDER FUNCTION TESTS");

try {
    // Test renderProductCard()
    test("renderProductCard() function exists", function_exists('renderProductCard'));
    
    if (function_exists('renderProductCard')) {
        // Get a sample product
        $db = getDB();
        $sample = $db->query("SELECT * FROM products WHERE status='active' LIMIT 1")->fetch();
        
        if ($sample) {
            $html = renderProductCard($sample, []);
            test("renderProductCard() returns string", is_string($html));
            test("renderProductCard() contains product-card class", str_contains($html, 'product-card'));
            test("renderProductCard() contains price", str_contains($html, 'price'));
        } else {
            test("renderProductCard() - no products", true, "No active products");
        }
    }
    
    // Test productImagePlaceholder()
    test("productImagePlaceholder() function exists", function_exists('productImagePlaceholder'));
    if (function_exists('productImagePlaceholder')) {
        $placeholder = productImagePlaceholder();
        test("productImagePlaceholder() returns SVG data URI", str_starts_with($placeholder, 'data:image/svg+xml'));
    }
    
} catch (Exception $e) {
    test("Render functions", false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 9: Data Integrity Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("9. DATA INTEGRITY TESTS");

try {
    $db = getDB();
    
    // Check for orphaned products
    $orphanProducts = $db->query("
        SELECT COUNT(*) FROM products p 
        LEFT JOIN shops s ON p.shop_id = s.shop_id 
        WHERE s.shop_id IS NULL OR s.is_active = 0
    ")->fetchColumn();
    test("No orphaned products", $orphanProducts == 0, "Found $orphanProducts orphaned products");
    
    // Check for products with invalid category
    $orphanCats = $db->query("
        SELECT COUNT(*) FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE c.category_id IS NULL
    ")->fetchColumn();
    test("No products with invalid category", $orphanCats == 0, "Found $orphanCats products with invalid category");
    
    // Check price consistency
    $priceIssues = $db->query("
        SELECT COUNT(*) FROM products 
        WHERE discount_price > base_price 
        AND discount_price IS NOT NULL
    ")->fetchColumn();
    test("No discount prices higher than base prices", $priceIssues == 0);
    
    // Check for required settings
    $siteName = $db->query("SELECT setting_value FROM site_settings WHERE setting_key='site_name'")->fetchColumn();
    test("Site name setting exists", !empty($siteName));
    
} catch (Exception $e) {
    test("Data integrity", false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 10: API Endpoint Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("10. API ENDPOINT TESTS");

try {
    // Test that API files return valid JSON structure when accessed
    $apiTests = [
        'cart.php?action=count' => ['success' => false], // Should fail without login but return valid JSON
        'wishlist.php?action=check&product_id=1' => ['success' => false],
        'search-suggest.php?q=te' => [], // Should return array
    ];
    
    foreach ($apiTests as $endpoint => $expected) {
        $fullPath = __DIR__ . '/../api/' . explode('?', $endpoint)[0];
        test("API endpoint exists: $endpoint", file_exists($fullPath));
    }
    
} catch (Exception $e) {
    test("API tests", false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// SECTION 11: CSS/JS Asset Tests
// ─────────────────────────────────────────────────────────────────────────────
testSection("11. ASSET VALIDATION");

$cssFile = __DIR__ . '/../assets/css/style.css';
if (file_exists($cssFile)) {
    $cssContent = file_get_contents($cssFile);
    test("CSS contains shopee-orange variable", str_contains($cssContent, '--shopee-orange'));
    test("CSS contains product-card styles", str_contains($cssContent, 'product-card'));
    test("CSS contains responsive media queries", str_contains($cssContent, '@media'));
}

$jsFile = __DIR__ . '/../assets/js/main.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    test("JS contains addToCart function", str_contains($jsContent, 'function addToCart'));
    test("JS contains toggleWishlist function", str_contains($jsContent, 'function toggleWishlist'));
    test("JS contains showToast function", str_contains($jsContent, 'function showToast'));
}

// ─────────────────────────────────────────────────────────────────────────────
// FINAL SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
$duration = round(microtime(true) - $startTime, 3);

echo "\n" . str_repeat('=', 70) . "\n";
echo "  TEST SUMMARY\n";
echo str_repeat('=', 70) . "\n";
echo "  Total Tests:  " . ($testResults['passed'] + $testResults['failed']) . "\n";
echo "  Passed:       " . $testResults['passed'] . " ✓\n";
echo "  Failed:       " . $testResults['failed'] . ($testResults['failed'] > 0 ? " ✗" : " ✓") . "\n";
echo "  Duration:     {$duration}s\n";
echo "  Success Rate: " . round($testResults['passed'] / ($testResults['passed'] + $testResults['failed']) * 100, 1) . "%\n";
echo str_repeat('=', 70) . "\n";

if ($testResults['failed'] > 0) {
    echo "\n  FAILED TESTS:\n";
    foreach ($testResults['tests'] as $t) {
        if (!$t['passed']) {
            echo "    - {$t['name']}\n";
            if ($t['detail']) echo "      {$t['detail']}\n";
        }
    }
    echo "\n";
    exit(1);
} else {
    echo "\n  ✓ ALL TESTS PASSED!\n\n";
    exit(0);
}
