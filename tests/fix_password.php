<?php
require_once dirname(__DIR__) . '/config/database.php';
$hash = password_hash('password', PASSWORD_BCRYPT);
$db   = getDB();
$db->prepare("UPDATE users SET password_hash=? WHERE username IN ('superadmin','admin','admin1','admin2','seller_a','seller_b','seller_c','seller_d','seller_e')")->execute([$hash]);
$db->prepare("UPDATE users SET password_hash=? WHERE role='buyer'")->execute([$hash]);
echo "Hash updated: " . substr($hash,0,20) . "...\n";
echo "Verify: " . (password_verify('password', $hash) ? 'OK' : 'FAIL') . "\n";
$stmt = $db->query("SELECT COUNT(*) FROM users");
echo "Total users: " . $stmt->fetchColumn() . "\n";
