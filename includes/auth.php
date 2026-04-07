<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

function requireRole(string ...$roles): void {
    requireLogin();
    if (!in_array($_SESSION['admin_role'] ?? '', $roles)) {
        http_response_code(403);
        include dirname(__DIR__) . '/admin/403.php';
        exit;
    }
}

function isSuperAdmin(): bool {
    return ($_SESSION['admin_role'] ?? '') === 'superadmin';
}

function isAdmin(): bool {
    return in_array($_SESSION['admin_role'] ?? '', ['admin', 'superadmin']);
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'       => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'],
        'name'     => $_SESSION['admin_name'],
        'role'     => $_SESSION['admin_role'],
        'roles'    => $_SESSION['admin_roles'] ?? [$_SESSION['admin_role'] ?? ''],
        'avatar'   => $_SESSION['admin_avatar'] ?? '',
    ];
}

/**
 * Check if current user has a specific role key (supports many-to-many).
 */
function hasRole(string $roleKey): bool {
    if (!isLoggedIn()) return false;
    if (isSuperAdmin()) return true;
    $roles = $_SESSION['admin_roles'] ?? [$_SESSION['admin_role'] ?? ''];
    return in_array($roleKey, $roles);
}

/**
 * Load all active roles for a user from user_roles table.
 */
function loadUserRoles(int $userId): array {
    try {
        $db   = getDB();
        $stmt = $db->prepare("
            SELECT r.role_key FROM user_roles ur
            JOIN roles r ON ur.role_id = r.role_id
            WHERE ur.user_id = ? AND ur.is_active = 1
              AND (ur.expires_at IS NULL OR ur.expires_at > NOW())
        ");
        $stmt->execute([$userId]);
        return array_column($stmt->fetchAll(), 'role_key');
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Assign a role to a user (many-to-many).
 */
function assignRole(int $userId, string $roleKey, ?int $assignedBy = null, ?string $reason = null, ?string $expiresAt = null): bool {
    try {
        $db   = getDB();
        $stmt = $db->prepare("SELECT role_id FROM roles WHERE role_key=?");
        $stmt->execute([$roleKey]);
        $role = $stmt->fetch();
        if (!$role) return false;
        $db->prepare("INSERT INTO user_roles (user_id,role_id,assigned_by,reason,expires_at,is_active)
                      VALUES (?,?,?,?,?,1) ON DUPLICATE KEY UPDATE is_active=1, assigned_by=VALUES(assigned_by),
                      expires_at=VALUES(expires_at), reason=VALUES(reason)")
           ->execute([$userId, $role['role_id'], $assignedBy, $reason, $expiresAt]);
        return true;
    } catch (Exception $e) { return false; }
}

/**
 * Revoke a role from a user.
 */
function revokeRole(int $userId, string $roleKey): bool {
    try {
        $db = getDB();
        $db->prepare("UPDATE user_roles ur JOIN roles r ON ur.role_id=r.role_id
                      SET ur.is_active=0 WHERE ur.user_id=? AND r.role_key=?")
           ->execute([$userId, $roleKey]);
        return true;
    } catch (Exception $e) { return false; }
}

/**
 * Check if a user is currently banned (active ban).
 */
function isUserBanned(int $userId): ?array {
    try {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM user_bans WHERE user_id=? AND is_active=1
                              AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY ban_id DESC LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    } catch (Exception $e) { return null; }
}

function hasPerm(string $permKey): bool {
    if (isSuperAdmin()) return true;
    $db = getDB();
    $stmt = $db->prepare("
        SELECT ap.granted FROM admin_permissions ap
        JOIN permissions p ON ap.perm_id = p.perm_id
        WHERE ap.user_id = ? AND p.perm_key = ?
    ");
    $stmt->execute([$_SESSION['admin_id'], $permKey]);
    $row = $stmt->fetch();
    return $row && $row['granted'];
}

function login(string $username, string $password): array {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE (username=? OR email=?) AND role IN ('admin','superadmin') AND is_active=1");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'];
    }
    // Check active ban
    $ban = isUserBanned((int)$user['user_id']);
    if ($ban) {
        $exp = $ban['expires_at'] ? ' (หมดอายุ: ' . date('d/m/Y', strtotime($ban['expires_at'])) . ')' : ' (ถาวร)';
        return ['success' => false, 'message' => 'บัญชีถูกระงับ: ' . $ban['reason'] . $exp];
    }
    $_SESSION['admin_id']       = $user['user_id'];
    $_SESSION['admin_username'] = $user['username'];
    $_SESSION['admin_name']     = $user['full_name'] ?: $user['username'];
    $_SESSION['admin_role']     = $user['role'];
    $_SESSION['admin_avatar']   = $user['avatar_url'] ?? '';
    // Load all roles (many-to-many)
    $allRoles = loadUserRoles((int)$user['user_id']);
    if (empty($allRoles)) $allRoles = [$user['role']];
    $_SESSION['admin_roles'] = $allRoles;
    $db->prepare("UPDATE users SET last_login_at=NOW() WHERE user_id=?")->execute([$user['user_id']]);
    logActivity('login', 'auth', null, null, 'เข้าสู่ระบบ | roles: ' . implode(',', $allRoles));
    return ['success' => true];
}

function logout(): void {
    logActivity('logout', 'auth', null, null, 'ออกจากระบบ');
    session_unset();
    session_destroy();
}

function logActivity(string $action, string $module = '', ?string $targetType = null, ?int $targetId = null, string $desc = ''): void {
    try {
        $db = getDB();
        $userId = $_SESSION['admin_id'] ?? null;
        $db->prepare("INSERT INTO activity_logs (user_id,action,module,target_type,target_id,description,ip_address,user_agent) VALUES (?,?,?,?,?,?,?,?)")
           ->execute([$userId, $action, $module, $targetType, $targetId, $desc, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
    } catch (Exception $e) {}
}

function generateCsrf(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verifyCsrf(string $token): bool {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function csrfField(): string {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . generateCsrf() . '">';
}
