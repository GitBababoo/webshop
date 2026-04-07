<?php
require_once dirname(__DIR__) . '/config/database.php';

function checkUrl($url) {
    if (empty($url)) return false;
    
    // If it's a local webshop URL (e.g. /webshop/uploads/...)
    if (strpos($url, '/webshop/') === 0) {
        $localPath = dirname(__DIR__) . str_replace('/webshop', '', $url);
        return file_exists($localPath);
    } 
    // If it's an external URL
    else if (strpos($url, 'http') === 0) {
        $headers = @get_headers($url);
        return $headers && strpos($headers[0], '200') !== false;
    }
    
    return false;
}

try {
    $pdo = getDB();
    $report = [
        'Banners' => ['total' => 0, 'ok' => 0, 'missing' => [], 'table' => 'banners', 'col' => 'image_url'],
        'Categories' => ['total' => 0, 'ok' => 0, 'missing' => [], 'table' => 'categories', 'col' => 'image_url'],
        'Shop Logos' => ['total' => 0, 'ok' => 0, 'missing' => [], 'table' => 'shops', 'col' => 'logo_url'],
        'Product Images' => ['total' => 0, 'ok' => 0, 'missing' => [], 'table' => 'product_images', 'col' => 'image_url'],
        'User Avatars' => ['total' => 0, 'ok' => 0, 'missing' => [], 'table' => 'users', 'col' => 'avatar_url'],
    ];

    $hasErrors = false;

    foreach ($report as $key => &$data) {
        $stmt = $pdo->query("SELECT {$data['col']} as url FROM {$data['table']} WHERE {$data['col']} IS NOT NULL AND {$data['col']} != ''");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data['total'] = count($rows);
        foreach ($rows as $row) {
            $url = $row['url'];
            if (checkUrl($url)) {
                $data['ok']++;
            } else {
                $data['missing'][] = $url;
                $hasErrors = true;
            }
        }
    }

    echo "====================================\n";
    echo "AUTOMATED IMAGE VERIFICATION REPORT\n";
    echo "====================================\n\n";

    foreach ($report as $key => $data) {
        echo strtoupper($key) . " (Total: {$data['total']})\n";
        echo "✅ OK: {$data['ok']}\n";
        if (!empty($data['missing'])) {
            echo "❌ MISSING: " . count($data['missing']) . "\n";
            foreach (array_slice($data['missing'], 0, 5) as $miss) {
                echo "   - $miss\n";
            }
            if (count($data['missing']) > 5) echo "   - ... and " . (count($data['missing']) - 5) . " more.\n";
        }
        echo "------------------------------------\n";
    }

    if ($hasErrors) {
        echo "\nRESULT: FAILED. Please fix the missing images.\n";
    } else {
        echo "\nRESULT: PASSED. All images successfully verified!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
