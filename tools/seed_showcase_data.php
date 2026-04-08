<?php
/**
 * Shopee TH - Showcase Data Seeding Tool (V2 - BUG FIX)
 * Purpose: Populates the DB with realistic sample data including required Foreign Keys (Addresses).
 */

require_once __DIR__ . '/../includes/functions.php';

// Set display errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "========================================\n";
echo "🚀 Shopee TH - Data Seeding (Fix Mode)\n";
echo "========================================\n\n";

$db = getDB();

try {
    // 1. Get or Create Target User
    $targetUser = $db->query("SELECT user_id FROM users WHERE role='buyer' LIMIT 1")->fetchColumn();
    if (!$targetUser) {
        $db->prepare("INSERT INTO users (username, password, email, role, full_name, status) VALUES (?, ?, ?, ?, ?, ?)")
           ->execute(['testbuyer', password_hash('password123', PASSWORD_DEFAULT), 'buyer@example.com', 'buyer', 'สมชาย รักการช้อป', 'active']);
        $targetUser = $db->lastInsertId();
    }
    
    // 2. IMPORTANT: Create a Default Address to satisfy Foreign Key constraints
    echo "[1/6] Ensuring valid shipping address exists...\n";
    $addressId = $db->query("SELECT address_id FROM user_addresses WHERE user_id = $targetUser LIMIT 1")->fetchColumn();
    if (!$addressId) {
        $db->prepare("INSERT INTO user_addresses (user_id, label, recipient_name, phone, address_line1, district, province, postal_code, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)")
           ->execute([$targetUser, 'Home', 'สมชาย รักการช้อป', '0812345678', '123/45 หมู่บ้านพฤกษา', 'ลำลูกกา', 'ปทุมธานี', '12150']);
        $addressId = $db->lastInsertId();
    }

    // 3. Wallet & Transactions
    echo "[2/6] Populating Wallet & Transactions...\n";
    $db->prepare("INSERT IGNORE INTO wallets (user_id, balance, coins) VALUES (?, 25000.50, 1250)")->execute([$targetUser]);
    $walletId = $db->query("SELECT wallet_id FROM wallets WHERE user_id=$targetUser")->fetchColumn();
    
    // Clear old sample transactions for a clean shot
    $db->prepare("DELETE FROM wallet_transactions WHERE wallet_id = ?")->execute([$walletId]);
    $txns = [
        ['topup', 15000.00, 'เติมเงินผ่าน K-Bank Plus'],
        ['payment', -1290.00, 'ชำระค่าสินค้าเซรั่มบำรุงผิว'],
        ['refund', 450.00, 'คืนเงินเคสโทรศัพท์ไม่ตรงรุ่น'],
        ['topup', 5000.00, 'เติมเงินผ่านพร้อมเพย์'],
        ['payment', -8400.00, 'ชำระค่า MacBook มือสอง'],
        ['cashback', 84.00, 'รับเงินคืน (Cashback Loyalty)']
    ];
    
    $balanceBefore = 0;
    foreach ($txns as $t) {
        $amount = $t[1];
        $balanceAfter = $balanceBefore + $amount;
        $db->prepare("INSERT INTO wallet_transactions (wallet_id, type, amount, balance_before, balance_after, description, created_at) VALUES (?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL ".rand(1, 15)." DAY))")
           ->execute([$walletId, $t[0], $amount, $balanceBefore, $balanceAfter, $t[2]]);
        $balanceBefore = $balanceAfter;
    }
    $db->prepare("UPDATE wallets SET balance = ? WHERE wallet_id = ?")->execute([$balanceBefore, $walletId]);

    // 4. Inject Orders for Dashboard Revenue Charts (30 Day Spread)
    echo "[3/6] Injecting 60+ Orders for Chart Visualization...\n";
    $shopId = $db->query("SELECT shop_id FROM shops LIMIT 1")->fetchColumn() ?: 1;
    
    // Simple way to spread data over 30 days
    for ($i = 0; $i < 65; $i++) {
        $daysAgo = rand(0, 30);
        $amount = rand(350, 8500);
        $status = ['pending', 'processing', 'shipped', 'delivered', 'completed'][rand(0, 4)];
        $payStatus = (rand(0, 10) > 2) ? 'paid' : 'pending';
        
        $db->prepare("INSERT INTO orders (order_number, buyer_user_id, shop_id, address_id, total_amount, order_status, payment_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL ? DAY))")
           ->execute(['ORD-REF-'.rand(100000, 999999), $targetUser, $shopId, $addressId, $amount, $status, $payStatus, $daysAgo]);
    }

    // 5. Product Reviews
    echo "[4/6] Adding High-Rating Reviews...\n";
    $products = $db->query("SELECT product_id FROM products LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($products as $pid) {
        $db->prepare("INSERT INTO product_reviews (product_id, user_id, rating, comment, is_visible) VALUES (?, ?, ?, ?, 1)")
           ->execute([$pid, $targetUser, 5, "คุณภาพสินค้าดีมากครับ ตรงปก ส่งไว ห่อกันกระแทกมาอย่างดี คุ้มค่าที่สุด!", 1]);
    }

    // 6. Set Loyalty Points & Tier
    echo "[5/6] Upgrading User Points & Tier...\n";
    $db->prepare("INSERT IGNORE INTO loyalty_points (user_id, total_points, tier) VALUES (?, 8500, 'gold') ON DUPLICATE KEY UPDATE total_points=8500, tier='gold'")->execute([$targetUser]);

    // 7. Fresh Vouchers
    echo "[6/6] Injecting Promotional Vouchers...\n";
    $db->prepare("INSERT INTO vouchers (code, name, type, discount_value, min_spend, is_active, start_at, end_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY))")
       ->execute(['GRAND-SHOT-2024', 'ส่วนลดแคมเปญเปิดตัวระบบใหม่', 'percentage', 25.00, 1000.00]);

    echo "\n✅ SUCCESS: Database Greenhouse was successful! charts are now ready.\n";
    echo "========================================\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}
