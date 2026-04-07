<?php
/**
 * Database Encoding Fix Script
 * Fixes mojibake (garbled Thai characters) in database
 * Run: php tools/fix_encoding.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Database Encoding Diagnostic & Fix ===\n\n";

$db = getDB();

// 1. Check database charset
echo "1. Checking database charset...\n";
$dbCharset = $db->query("SELECT @@character_set_database, @@collation_database")->fetch();
echo "   Database charset: {$dbCharset['@@character_set_database']}\n";
echo "   Database collation: {$dbCharset['@@collation_database']}\n\n";

// 2. Check connection charset
echo "2. Checking connection charset...\n";
$connCharset = $db->query("SELECT @@character_set_client, @@character_set_connection, @@character_set_results")->fetch();
echo "   Client charset: {$connCharset['@@character_set_client']}\n";
echo "   Connection charset: {$connCharset['@@character_set_connection']}\n";
echo "   Results charset: {$connCharset['@@character_set_results']}\n\n";

// 3. Check table charsets
echo "3. Checking table charsets...\n";
$tables = ['products', 'categories', 'shops', 'users', 'orders', 'reviews', 'cms_pages'];
foreach ($tables as $table) {
    try {
        $tblInfo = $db->query("SHOW CREATE TABLE $table")->fetch();
        $create = $tblInfo['Create Table'] ?? '';
        if (preg_match('/CHARSET=(\w+)/', $create, $m)) {
            echo "   $table: CHARSET={$m[1]}\n";
        }
    } catch (Exception $e) {
        echo "   $table: Error - {$e->getMessage()}\n";
    }
}
echo "\n";

// 4. Sample data check
echo "4. Checking sample data for mojibake...\n";
$checks = [
    ['table' => 'products', 'column' => 'name', 'where' => 'product_id <= 20'],
    ['table' => 'categories', 'column' => 'name', 'where' => '1=1'],
    ['table' => 'shops', 'column' => 'shop_name', 'where' => '1=1'],
];

$hasMojibake = false;
foreach ($checks as $check) {
    $table = $check['table'];
    $column = $check['column'];
    $where = $check['where'];
    try {
        $rows = $db->query("SELECT * FROM $table WHERE $where LIMIT 5")->fetchAll();
        foreach ($rows as $row) {
            $value = $row[$column] ?? '';
            // Check for mojibake patterns (UTF-8 interpreted as Latin1)
            if (preg_match('/[\xC0-\xDF][\x80-\xBF]/', $value) || 
                preg_match('/[\xE0-\xEF][\x80-\xBF]{2}/', $value) ||
                preg_match('/[\xF0-\xF7][\x80-\xBF]{3}/', $value)) {
                // This looks like valid UTF-8 multi-byte sequence
                // But if we see patterns like Ó╣Ç = U+00C3 U+00B9 U+00C3 U+00A0
                // that's double-encoded UTF-8
                if (preg_match('/Ã[\xA0-\xBF][Ã\xC0-\xCF]/', $value)) {
                    echo "   [MOJIBAKE] $table.$column: ID={$row[array_key_first($row)]} = " . substr($value, 0, 50) . "\n";
                    $hasMojibake = true;
                }
            }
        }
    } catch (Exception $e) {
        echo "   Error checking $table: {$e->getMessage()}\n";
    }
}

if (!$hasMojibake) {
    echo "   No obvious mojibake detected in sample data.\n";
}
echo "\n";

// 5. Fix connection charset if needed
echo "5. Setting proper connection charset...\n";
try {
    $db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    $db->exec("SET CHARACTER SET utf8mb4");
    echo "   ✓ Connection charset set to utf8mb4\n";
} catch (Exception $e) {
    echo "   ✗ Error: {$e->getMessage()}\n";
}

// 6. Display raw bytes of problematic data if any
echo "\n6. Raw byte analysis...\n";
try {
    $samples = $db->query("SELECT product_id, name, HEX(name) as hex_name FROM products LIMIT 3")->fetchAll();
    foreach ($samples as $s) {
        echo "   ID {$s['product_id']}: {$s['name']}\n";
        echo "   HEX: " . substr($s['hex_name'], 0, 60) . "...\n\n";
    }
} catch (Exception $e) {
    echo "   Error: {$e->getMessage()}\n";
}

echo "\n=== Diagnostic Complete ===\n";
echo "\nIf you still see garbled characters:\n";
echo "1. The data might have been double-encoded during seed\n";
echo "2. Run: php tools/convert_encoding.php to attempt conversion\n";
