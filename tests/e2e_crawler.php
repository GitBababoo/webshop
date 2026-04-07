<?php
/**
 * ============================================================
 * WEBSHOP ADVANCED RECURSIVE E2E SYSTEM SCANNER
 * ============================================================
 * This tool dynamically discovers all pages in the project,
 * tests for Syntax, Runtime, and SQL errors automatically.
 */
set_time_limit(0); 
header('Content-Type: text/plain');
echo "============================================================\n";
echo "🔍 WEBSHOP ADVANCED RECURSIVE E2E SYSTEM SCANNER\n";
echo "============================================================\n\n";

$baseUrl = 'http://localhost/webshop/';
$scanLimit = 100; // Maximum unique pages to scan
$depthLimit = 3;  // Maximum link depth
$excludedStrings = ['logout.php', 'delete', 'truncate', 'remove', 'admin/order-action.php'];

// --- PHASE 1: Recursive Syntax Scanner ---
function getPhpFiles($dir) {
    if (!is_dir($dir)) return [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            function ($current, $key, $iterator) {
                if ($current->isDir() && in_array($current->getFilename(), ['vendor', '.git', '.vscode', 'tests', 'storage'])) return false;
                return true;
            }
        )
    );
    $files = [];
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') $files[] = $file->getPathname();
    }
    return $files;
}

$rootDir = dirname(__DIR__);
$phpFiles = getPhpFiles($rootDir);
$syntaxErrors = 0;

echo "[PHASE 1] Scanning Syntax for " . count($phpFiles) . " PHP files...\n";
foreach ($phpFiles as $file) {
    $res = shell_exec('php -l "' . $file . '" 2>&1');
    if (strpos((string)$res, 'No syntax errors detected') === false && strpos((string)$res, 'Errors parsing') !== false) {
        $relPath = str_replace($rootDir, '', $file);
        echo "❌ [SYNTAX] {$relPath} -> " . trim($res) . "\n";
        $syntaxErrors++;
    }
}
if ($syntaxErrors === 0) echo "✅ PASS: No Syntax Errors found.\n\n";
else echo "⚠️ FAILED: Found {$syntaxErrors} syntax errors.\n\n";


// --- PHASE 2: Dynamic Recursive Crawler ---
echo "[PHASE 2] Starting Dynamic Crawler (Discovering pages automatically)...\n";

$queue = [['url' => $baseUrl . 'index.php', 'depth' => 0]];
$visited = [];
$results = ['ok' => 0, 'error' => 0, 'warning' => 0, '404' => 0];

function extractLinks($html, $currentUrl, $base) {
    global $excludedStrings;
    $links = [];
    if (!$html) return $links;
    
    // Find all <a href="...">
    preg_match_all('/<a\s+(?:[^>]*?\s+)?href=["\'](.*?)["\']/i', $html, $matches);
    foreach ($matches[1] as $href) {
        // Build absolute URL
        if (strpos($href, 'http') === 0) {
            $url = $href;
        } elseif (strpos($href, '/') === 0) {
            $url = 'http://localhost' . $href;
        } else {
            $url = $base . $href;
        }
        
        // Filter: Must be within base shop, not excluded
        if (strpos($url, $base) === 0 && !strpos($url, '#')) {
            $isExcluded = false;
            foreach ($excludedStrings as $ex) {
                if (strpos($url, $ex) !== false) { $isExcluded = true; break; }
            }
            if (!$isExcluded) $links[] = explode('?', $url)[0] . (isset(explode('?', $url)[1]) ? '?' . explode('?', $url)[1] : '');
        }
    }
    return array_unique($links);
}

while (!empty($queue) && count($visited) < $scanLimit) {
    $current = array_shift($queue);
    $url = $current['url'];
    $depth = $current['depth'];
    
    if (isset($visited[$url])) continue;
    $visited[$url] = true;
    
    // Scan page
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $cleanHtml = strip_tags($html ?? '');
    $hasError = false;
    $displayUrl = str_replace($baseUrl, '/', $url);
    
    if ($httpCode >= 500) {
        echo "❌ [HTTP $httpCode] $displayUrl\n";
        $hasError = true; $results['error']++;
    } elseif ($httpCode === 404) {
        echo "❔ [404] $displayUrl\n";
        $results['404']++;
    } else {
        // Detect Errors
        $patterns = [
            'FATAL' => '/Fatal error:(.*?)(in |Stack trace:)/is',
            'PARSE' => '/Parse error:(.*?)(in |Stack trace:)/is',
            'SQL'   => '/(SQLSTATE|Unknown column|Table .*? doesn\'t exist|You have an error in your SQL syntax)/is',
            'TYPE'  => '/Uncaught TypeError:(.*?)(in |Stack trace:)/is',
            'WARN'  => '/Warning:(.*?)(in |Stack trace:)/is'
        ];
        
        foreach ($patterns as $type => $p) {
            if (preg_match($p, $cleanHtml, $m)) {
                $excerpt = trim($m[1] ?? (isset($m[0]) ? $m[0] : 'Unknown error'));
                if ($type === 'WARN') {
                   echo "⚠️  [WARN] $displayUrl -> $excerpt\n"; $results['warning']++;
                } else {
                   echo "❌ [$type] $displayUrl -> $excerpt\n"; 
                   $hasError = true; $results['error']++;
                   break;
                }
            }
        }
    }
    
    if (!$hasError && $httpCode === 200) {
        echo "✅ [OK] $displayUrl\n"; $results['ok']++;
        
        // Extract links and add to queue if depth permits
        if ($depth < $depthLimit) {
            $newLinks = extractLinks($html, $url, $baseUrl);
            foreach ($newLinks as $nl) {
                if (!isset($visited[$nl])) {
                    $queue[] = ['url' => $nl, 'depth' => $depth + 1];
                }
            }
        }
    }
}

echo "\n============================================================\n";
echo "📊 SCAN SUMMARY\n";
echo "------------------------------------------------------------\n";
echo "Total Pages Scanned: " . count($visited) . "\n";
echo "Success (OK):       " . $results['ok'] . "\n";
echo "Broken (Error):     " . $results['error'] . "\n";
echo "Warnings:           " . $results['warning'] . "\n";
echo "Not Found (404):    " . $results['404'] . "\n";
echo "------------------------------------------------------------\n";

if ($syntaxErrors === 0 && $results['error'] === 0) {
    echo "🎯 ALL SYSTEMS GO! Your project is stable and clean.\n";
} else {
    echo "🔥 CRITICAL ISSUES FOUND! Please review the logged errors above.\n";
}
echo "============================================================\n";
