<?php
set_time_limit(300);
$baseDir = dirname(__DIR__);

function hardcodeSave($url, $path) {
    if(!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
    $ch = curl_init($url);
    $fp = fopen($path, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}

// Ensure all dirs exist and have at least one sample picture
$paths = [
    '/uploads/avatars/sample.jpg' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=800&q=80',
    '/uploads/banners/sample.jpg' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&q=80',
    '/uploads/categories/sample.jpg' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=600&q=80',
    '/uploads/flashsales/sample.jpg' => 'https://images.unsplash.com/photo-1493723843671-1d655e66ac1c?w=1200&q=80',
    '/uploads/products/sample.jpg' => 'https://images.unsplash.com/photo-1605336183652-329b3ae3d2ce?w=800&q=80',
    '/uploads/settings/site_logo.png' => 'https://images.unsplash.com/photo-1555664424-778a1e5e1b48?w=200&q=80',
    '/uploads/shops/sample_cover.jpg' => 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=1200&q=80',
    '/uploads/shops/logos/sample_logo.jpg' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=200&q=80',
    '/uploads/users/sample_avatar.jpg' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&q=80'
];

foreach ($paths as $local => $url) {
    hardcodeSave($url, $baseDir . $local);
}

echo "All physical directories have been fully populated with high quality images.";
?>
