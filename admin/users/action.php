<?php
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireLogin();

$db  = getDB();
$id  = (int)($_GET['id'] ?? 0);
$act = $_GET['act'] ?? '';

if (!$id) { header('Location: index.php'); exit; }

$stmt = $db->prepare("SELECT * FROM users WHERE user_id=?");
$stmt->execute([$id]);
$user_row = $stmt->fetch();
if (!$user_row) { flash('danger','ไม่พบผู้ใช้'); header('Location: index.php'); exit; }

switch ($act) {
    case 'activate':
        $db->prepare("UPDATE users SET is_active=1 WHERE user_id=?")->execute([$id]);
        logActivity('activate','users','user',$id,'เปิดใช้งาน '.$user_row['username']);
        flash('success','เปิดใช้งานผู้ใช้เรียบร้อย');
        break;
    case 'deactivate':
        $db->prepare("UPDATE users SET is_active=0 WHERE user_id=?")->execute([$id]);
        logActivity('deactivate','users','user',$id,'ระงับ '.$user_row['username']);
        flash('success','ระงับผู้ใช้เรียบร้อย');
        break;
    case 'delete':
        if (!isSuperAdmin()) { flash('danger','ไม่มีสิทธิ์'); break; }
        if (in_array($user_row['role'], ['admin','superadmin'])) { flash('danger','ไม่สามารถลบ Admin ได้'); break; }
        $db->prepare("DELETE FROM users WHERE user_id=?")->execute([$id]);
        logActivity('delete','users','user',$id,'ลบผู้ใช้ '.$user_row['username']);
        flash('success','ลบผู้ใช้เรียบร้อย');
        break;
    case 'verify':
        $db->prepare("UPDATE users SET is_verified=1 WHERE user_id=?")->execute([$id]);
        flash('success','ยืนยันผู้ใช้เรียบร้อย');
        break;
    default:
        flash('danger','Action ไม่ถูกต้อง');
}
header('Location: index.php');
exit;
