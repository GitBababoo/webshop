<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    $pdo = getDB();
    
    $tables = [
        'product_images' => ['image_url'],
        'categories' => ['image_url', 'icon_url'],
        'banners' => ['image_url'],
        'shops' => ['logo_url', 'cover_url'],
        'users' => ['avatar_url']
    ];
    
    foreach($tables as $table => $columns) {
        foreach($columns as $col) {
            // Prepend /webshop to paths that start with /uploads
            $stmt = $pdo->prepare("UPDATE $table SET $col = CONCAT('/webshop', $col) WHERE $col LIKE '/uploads/%'");
            $stmt->execute();
        }
    }
    
    echo "Fixed paths to include /webshop prefix!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
