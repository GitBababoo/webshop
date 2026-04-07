<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    $pdo = getDB();
    
    $stmt = $pdo->query("SELECT shop_id, logo_url FROM shops WHERE logo_url IS NOT NULL AND logo_url != ''");
    $shops = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($shops as $shop) {
        $localPath = dirname(__DIR__) . str_replace('/webshop', '', $shop['logo_url']);
        if (!file_exists($localPath)) {
            if (!is_dir(dirname($localPath))) {
                mkdir(dirname($localPath), 0777, true);
            }
            $ch = curl_init('https://images.unsplash.com/photo-1542838132-92c53300491e?w=200&q=80'); // nice shop logo
            $fp = fopen($localPath, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }
    }
    
    echo "Fixed missing shop logos.\n";
} catch(Exception $e) {
    echo "Error: ". $e->getMessage();
}
?>
