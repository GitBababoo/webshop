<?php
/**
 * Universal Database Guard
 * Automatically verifies core schema to prevent "Unknown Column" errors.
 */

function checkDatabaseHealth(): array {
    $db = getDB();
    $errors = [];
    
    // Define expected schema
    $schema = [
        'users' => ['user_id', 'username', 'email', 'phone', 'password_hash'],
        'products' => ['product_id', 'shop_id', 'category_id', 'name', 'slug', 'base_price', 'status'],
        'orders' => ['order_id', 'order_number', 'buyer_user_id', 'shop_id', 'total_amount', 'order_status'],
        'cart_items' => ['cart_id', 'product_id', 'quantity']
    ];
    
    foreach ($schema as $table => $columns) {
        try {
            $stmt = $db->query("DESCRIBE `$table` ");
            $existing = array_column($stmt->fetchAll(), 'Field');
            foreach ($columns as $col) {
                if (!in_array($col, $existing)) {
                    $errors[] = "Missing critical column '$col' in table '$table'";
                }
            }
        } catch (Exception $e) {
            $errors[] = "Critical table '$table' is missing or inaccessible.";
        }
    }
    
    return $errors;
}

// Auto-run health check and log issues if any
if (basename($_SERVER['PHP_SELF']) !== 'db_guard.php' && str_contains($_SERVER['PHP_SELF'], '/admin/')) {
    $healthErrors = checkDatabaseHealth();
    foreach ($healthErrors as $err) {
        siteLog($err, 'CRITICAL');
    }
}
