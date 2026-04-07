<?php
/**
 * Mojibake Fix Script
 * Converts double-encoded UTF-8 (mojibake) back to proper Thai text
 * Run: php tools/convert_mojibake.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Mojibake Conversion Tool ===\n\n";

$db = getDB();
$db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

/**
 * Fix double-encoded UTF-8 (UTF-8 interpreted as Latin1 then encoded as UTF-8 again)
 * Common mojibake patterns:
 * - ก becomes à¸ à¸
 * - ข becomes à¸
 */
function fixMojibake(string $text): string {
    // If text contains these patterns, it's likely double-encoded UTF-8
    // Convert: UTF-8 bytes interpreted as Latin1 → back to proper UTF-8
    return mb_convert_encoding($text, 'UTF-8', 'UTF-8,Latin1');
}

/**
 * Alternative: Force convert Latin1 interpretation of UTF-8 bytes
 */
function decodeMojibake(string $text): ?string {
    // Check if this looks like mojibake (contains specific byte patterns)
    // Pattern: à¸ (0xC3 0xA0 0xC2 0xB8) followed by another Thai char pattern
    if (!preg_match('/[\xC3][\xA0-\xBF][\xC2-\xC3]/', $text)) {
        return null; // Not mojibake
    }
    
    // Convert from "UTF-8 bytes read as Latin1" back to proper UTF-8
    // Step 1: Convert bytes back to Latin1 interpretation
    $fixed = @iconv('UTF-8', 'Latin1//IGNORE', $text);
    if ($fixed === false) {
        return null;
    }
    return $fixed;
}

// Tables and columns to check/fix
$fixTargets = [
    ['table' => 'products', 'columns' => ['name', 'description', 'brand'], 'id' => 'product_id'],
    ['table' => 'categories', 'columns' => ['name', 'description'], 'id' => 'category_id'],
    ['table' => 'shops', 'columns' => ['shop_name', 'description'], 'id' => 'shop_id'],
    ['table' => 'users', 'columns' => ['full_name', 'bio'], 'id' => 'user_id'],
    ['table' => 'cms_pages', 'columns' => ['title', 'content'], 'id' => 'page_id'],
    ['table' => 'reviews', 'columns' => ['comment'], 'id' => 'review_id'],
];

$fixedCount = 0;
$checkedCount = 0;

foreach ($fixTargets as $target) {
    $table = $target['table'];
    $columns = $target['columns'];
    $idCol = $target['id'];
    
    echo "Checking: $table\n";
    
    try {
        $rows = $db->query("SELECT $idCol, " . implode(', ', $columns) . " FROM $table")->fetchAll();
        
        foreach ($rows as $row) {
            $id = $row[$idCol];
            $updates = [];
            $params = [];
            
            foreach ($columns as $col) {
                $value = $row[$col] ?? '';
                if (empty($value)) continue;
                
                $checkedCount++;
                
                // Try to detect and fix mojibake
                $fixed = decodeMojibake($value);
                
                if ($fixed !== null && $fixed !== $value) {
                    $updates[] = "$col = ?";
                    $params[] = $fixed;
                    echo "   [FIX] $table.$col ID=$id\n";
                    echo "   FROM: " . substr($value, 0, 50) . "\n";
                    echo "   TO:   " . substr($fixed, 0, 50) . "\n\n";
                    $fixedCount++;
                }
            }
            
            // Apply fixes
            if (!empty($updates)) {
                $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $idCol = ?";
                $params[] = $id;
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
            }
        }
    } catch (Exception $e) {
        echo "   Error: {$e->getMessage()}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Fields checked: $checkedCount\n";
echo "Fields fixed: $fixedCount\n";

if ($fixedCount === 0) {
    echo "\nNo mojibake detected. If you still see garbled characters, try:\n";
    echo "1. Check your browser encoding (should be UTF-8)\n";
    echo "2. Check database collation: SHOW VARIABLES LIKE 'character_set_%';\n";
    echo "3. Check specific records manually\n";
}

echo "\nDone!\n";
