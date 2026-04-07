<?php
$destDir = __DIR__ . '/assets/screenshots';
if (!is_dir($destDir)) mkdir($destDir, 0755, true);

$sourceDir = 'C:/Users/Administrator/.gemini/antigravity/brain/305a30b8-1980-4687-95cd-cea496379427';
$files = [
    'homepage_live_1775570793678.png'   => 'homepage.png',
    'product_page_live_1775570800795.png' => 'product.png',
    'error_logs_live_1775570807414.png'   => 'admin.png',
    'crawler_success_live_1775571681843.png' => 'crawler.png'
];

echo "<pre>";
foreach ($files as $old => $new) {
    if (copy($sourceDir . '/' . $old, $destDir . '/' . $new)) {
        echo "✅ Copied: $old -> $new\n";
    } else {
        echo "❌ Failed: $old\n";
    }
}
echo "</pre>";
?>
