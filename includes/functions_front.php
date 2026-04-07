<?php
/**
 * Frontend Helper Functions
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/functions.php';

/* ── Constants ── */
if (!defined('ORDER_STATUSES')) {
define('ORDER_STATUSES', [
    'pending' => 'รอชำระเงิน',
    'confirmed' => 'ยืนยันแล้ว',
    'processing' => 'กำลังเตรียมสินค้า',
    'shipped' => 'จัดส่งแล้ว',
    'delivered' => 'จัดส่งสำเร็จ',
    'completed' => 'เสร็จสิ้น',
    'cancelled' => 'ยกเลิก',
    'return_requested' => 'ขอคืนสินค้า',
    'returned' => 'คืนสินค้าแล้ว'
]);
}

if (!defined('PAYMENT_METHODS')) {
define('PAYMENT_METHODS', [
    'cod' => 'เก็บเงินปลายทาง',
    'bank_transfer' => 'โอนเงินผ่านธนาคาร',
    'credit_card' => 'บัตรเครดิต/เดบิต',
    'shopee_pay' => 'Shopee Pay',
    'paypal' => 'PayPal'
]);
}

if (session_status() === PHP_SESSION_NONE) {
    session_name('shopee_front');
    session_start();
}

/* ── Auth ── */
function frontIsLoggedIn(): bool { return !empty($_SESSION['front_user_id']); }

function frontCurrentUser(): ?array {
    if (!frontIsLoggedIn()) return null;
    static $user = null;
    if ($user) return $user;
    $stmt = getDB()->prepare("SELECT user_id,username,full_name,email,phone,avatar_url,role,is_active,is_verified FROM users WHERE user_id=?");
    $stmt->execute([$_SESSION['front_user_id']]);
    $user = $stmt->fetch() ?: null;
    return $user;
}

function frontRequireLogin(string $redirect = ''): void {
    if (!frontIsLoggedIn()) {
        $back = $redirect ?: ($_SERVER['REQUEST_URI'] ?? '/webshop/');
        header('Location: /webshop/account/login.php?redirect=' . urlencode($back));
        exit;
    }
}

function frontLogin(string $username, string $password): array {
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE (username=? OR email=?) AND is_active=1 AND role!='admin' AND role!='superadmin'");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'];
    }
    // Check ban
    $banStmt = $db->prepare("SELECT reason,expires_at FROM user_bans WHERE user_id=? AND is_active=1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1");
    $banStmt->execute([$user['user_id']]);
    $ban = $banStmt->fetch();
    if ($ban) {
        $exp = $ban['expires_at'] ? date('d/m/Y', strtotime($ban['expires_at'])) : 'ถาวร';
        return ['success' => false, 'message' => "บัญชีถูกระงับ: {$ban['reason']} (ถึง {$exp})"];
    }
    $_SESSION['front_user_id']   = $user['user_id'];
    $_SESSION['front_username']  = $user['username'];
    $_SESSION['front_name']      = $user['full_name'] ?: $user['username'];
    $_SESSION['front_role']      = $user['role'];
    $db->prepare("UPDATE users SET last_login_at=NOW() WHERE user_id=?")->execute([$user['user_id']]);
    // Merge guest cart
    mergeGuestCart($user['user_id']);
    return ['success' => true];
}

function frontLogout(): void {
    session_unset(); session_destroy();
}

function frontRegister(array $data): array {
    $db = getDB();
    // Validate unique
    $stmt = $db->prepare("SELECT username, email, phone FROM users WHERE username=? OR email=? OR phone=?");
    $stmt->execute([$data['username'], $data['email'], $data['phone'] ?? '']);
    $existing = $stmt->fetch();
    if ($existing) {
        if ($existing['username'] === $data['username']) return ['success'=>false,'message'=>'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว'];
        if ($existing['email'] === $data['email']) return ['success'=>false,'message'=>'อีเมลนี้ถูกใช้งานแล้ว'];
        if ($existing['phone'] === $data['phone']) return ['success'=>false,'message'=>'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว'];
        return ['success'=>false,'message'=>'Username, Email หรือเบอร์โทรศัพท์ถูกใช้งานแล้ว'];
    }
    $hash = password_hash($data['password'], PASSWORD_BCRYPT);
    $db->prepare("INSERT INTO users (username,email,phone,password_hash,full_name,role,is_active) VALUES (?,?,?,?,?,'buyer',1)")
       ->execute([$data['username'],$data['email'],$data['phone']??null,$hash,$data['full_name']??$data['username']]);
    $uid = (int)$db->lastInsertId();
    // Create cart and wallet
    $db->prepare("INSERT IGNORE INTO carts (user_id) VALUES (?)")->execute([$uid]);
    $db->prepare("INSERT IGNORE INTO wallets (user_id,balance,coins) VALUES (?,0,0)")->execute([$uid]);
    $db->prepare("INSERT IGNORE INTO loyalty_points (user_id,total_points) VALUES (?,0)")->execute([$uid]);
    return ['success'=>true,'user_id'=>$uid];
}

/* ── Cart ── */
function getCartId(int $userId): int {
    $db   = getDB();
    $stmt = $db->prepare("SELECT cart_id FROM carts WHERE user_id=?");
    $stmt->execute([$userId]);
    $id = $stmt->fetchColumn();
    if (!$id) {
        $db->prepare("INSERT INTO carts (user_id) VALUES (?)")->execute([$userId]);
        $id = (int)$db->lastInsertId();
    }
    return (int)$id;
}

function getCart(?int $userId = null): array {
    if (!$userId && frontIsLoggedIn()) $userId = $_SESSION['front_user_id'];
    if (!$userId) return getGuestCart();
    $cartId = getCartId($userId);
    $stmt   = getDB()->prepare("
        SELECT ci.cart_item_id, ci.product_id, ci.sku_id, ci.quantity, ci.is_checked,
               p.name, p.slug, p.base_price, p.discount_price, p.total_stock, p.status,
               pi.image_url, s.shop_id, s.shop_name, s.shop_slug
        FROM cart_items ci
        JOIN products p  ON ci.product_id=p.product_id
        JOIN shops s     ON p.shop_id=s.shop_id
        LEFT JOIN product_images pi ON pi.product_id=p.product_id AND pi.is_primary=1
        WHERE ci.cart_id=? AND p.status='active'
        ORDER BY s.shop_id, ci.cart_item_id
    ");
    $stmt->execute([$cartId]);
    $items = $stmt->fetchAll();
    foreach ($items as &$item) {
        $item['effective_price'] = (float)($item['discount_price'] ?: $item['base_price']);
        $item['subtotal']        = $item['effective_price'] * $item['quantity'];
    }
    return $items;
}

function getCartCount(?int $userId = null): int {
    if (!$userId && frontIsLoggedIn()) $userId = $_SESSION['front_user_id'];
    if (!$userId) return array_sum(array_column(getGuestCart(), 'quantity'));
    $cartId = getCartId($userId);
    $stmt   = getDB()->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart_items WHERE cart_id=?");
    $stmt->execute([$cartId]);
    return (int)$stmt->fetchColumn();
}

function getCartTotal(?int $userId = null): float {
    $items = getCart($userId);
    return array_sum(array_column(array_filter($items, fn($i) => $i['is_checked']), 'subtotal'));
}

function addToCartDB(int $userId, int $productId, int $qty = 1, ?int $skuId = null): array {
    $db = getDB();
    // Check stock
    $stock = $db->prepare("SELECT total_stock, status FROM products WHERE product_id=?");
    $stock->execute([$productId]);
    $product = $stock->fetch();
    if (!$product || $product['status'] !== 'active') return ['success'=>false,'message'=>'สินค้าไม่พร้อมจำหน่าย'];
    if ($product['total_stock'] < $qty) return ['success'=>false,'message'=>'สินค้าในสต็อกไม่เพียงพอ'];
    $cartId = getCartId($userId);
    $stmt = $db->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE cart_id=? AND product_id=?");
    $stmt->execute([$cartId, $productId]);
    $existing = $stmt->fetch();
    if ($existing) {
        $newQty = $existing['quantity'] + $qty;
        if ($newQty > $product['total_stock']) $newQty = $product['total_stock'];
        $db->prepare("UPDATE cart_items SET quantity=? WHERE cart_item_id=?")->execute([$newQty, $existing['cart_item_id']]);
    } else {
        $db->prepare("INSERT INTO cart_items (cart_id,product_id,sku_id,quantity) VALUES (?,?,?,?)")->execute([$cartId,$productId,$skuId,$qty]);
    }
    return ['success'=>true,'cart_count'=>getCartCount($userId)];
}

function getGuestCart(): array {
    return $_SESSION['guest_cart'] ?? [];
}

function mergeGuestCart(int $userId): void {
    if (empty($_SESSION['guest_cart'])) return;
    foreach ($_SESSION['guest_cart'] as $item) {
        addToCartDB($userId, $item['product_id'], $item['quantity'], $item['sku_id'] ?? null);
    }
    unset($_SESSION['guest_cart']);
}

/* ── Wishlist ── */
function getWishlistIds(int $userId): array {
    $stmt = getDB()->prepare("SELECT product_id FROM wishlists WHERE user_id=?");
    $stmt->execute([$userId]);
    return array_column($stmt->fetchAll(), 'product_id');
}

function toggleWishlistDB(int $userId, int $productId): array {
    $db   = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id=? AND product_id=?");
    $stmt->execute([$userId, $productId]);
    $exists = (bool)$stmt->fetchColumn();
    if ($exists) {
        $db->prepare("DELETE FROM wishlists WHERE user_id=? AND product_id=?")->execute([$userId,$productId]);
    } else {
        $db->prepare("INSERT IGNORE INTO wishlists (user_id,product_id) VALUES (?,?)")->execute([$userId,$productId]);
    }
    return ['success'=>true,'in_wishlist'=>!$exists];
}

/* ── Products ── */
function getProducts(array $filters = [], int $page = 1, int $perPage = 20): array {
    $db    = getDB();
    $where = ["p.status='active'"];
    $params = [];
    if (!empty($filters['category_id'])) { $where[] = "(p.category_id=? OR c.parent_id=?)"; $params[] = $filters['category_id']; $params[] = $filters['category_id']; }
    if (!empty($filters['shop_id']))     { $where[] = "p.shop_id=?"; $params[] = $filters['shop_id']; }
    if (!empty($filters['q']))           { $where[] = "(p.name LIKE ? OR p.brand LIKE ?)"; $params[] = "%{$filters['q']}%"; $params[] = "%{$filters['q']}%"; }
    if (!empty($filters['brand']))       { $where[] = "p.brand=?"; $params[] = $filters['brand']; }
    if (!empty($filters['price_min']))   { $where[] = "COALESCE(p.discount_price,p.base_price)>=?"; $params[] = $filters['price_min']; }
    if (!empty($filters['price_max']))   { $where[] = "COALESCE(p.discount_price,p.base_price)<=?"; $params[] = $filters['price_max']; }
    if (!empty($filters['condition']))   { $where[] = "p.condition_type=?"; $params[] = $filters['condition']; }
    if (isset($filters['is_featured']) && $filters['is_featured']) { $where[] = "p.is_featured=1"; }
    $whereSQL = implode(' AND ', $where);
    $sortMap = [
        'newest'   => 'p.created_at DESC',
        'price_asc'=> 'COALESCE(p.discount_price,p.base_price) ASC',
        'price_desc'=> 'COALESCE(p.discount_price,p.base_price) DESC',
        'rating'   => 'p.rating DESC, p.total_reviews DESC',
        'popular'  => 'p.total_sold DESC',
    ];
    $sort = $sortMap[$filters['sort'] ?? 'newest'] ?? $sortMap['newest'];
    $countStmt = $db->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id=c.category_id WHERE $whereSQL");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();
    $offset = ($page - 1) * $perPage;
    $stmt  = $db->prepare("
        SELECT p.product_id, p.name, p.slug, p.base_price, p.discount_price,
               p.rating, p.total_reviews, p.total_sold, p.is_featured, p.condition_type,
               p.brand, p.total_stock,
               pi.image_url, s.shop_name, s.shop_slug, s.shop_id
        FROM products p
        JOIN categories c ON p.category_id=c.category_id
        JOIN shops s ON p.shop_id=s.shop_id
        LEFT JOIN product_images pi ON pi.product_id=p.product_id AND pi.is_primary=1
        WHERE $whereSQL
        ORDER BY $sort LIMIT $perPage OFFSET $offset
    ");
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    return ['data'=>$data,'total'=>$total,'page'=>$page,'per_page'=>$perPage,'total_pages'=>max(1,ceil($total/$perPage))];
}

function getProductBySlug(string $slug): ?array {
    $stmt = getDB()->prepare("
        SELECT p.*, pi.image_url AS main_image,
               s.shop_id, s.shop_name, s.shop_slug, s.rating AS shop_rating,
               s.total_sales AS shop_sales, s.total_reviews AS shop_reviews,
               s.is_verified AS shop_verified,
               c.name AS category_name, c.slug AS category_slug
        FROM products p
        JOIN shops s ON p.shop_id=s.shop_id
        JOIN categories c ON p.category_id=c.category_id
        LEFT JOIN product_images pi ON pi.product_id=p.product_id AND pi.is_primary=1
        WHERE p.slug=? AND p.status='active'
    ");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();
    if (!$product) return null;

    // All images
    $imgStmt = getDB()->prepare("SELECT image_url, sort_order, is_primary FROM product_images WHERE product_id=? ORDER BY sort_order");
    $imgStmt->execute([$product['product_id']]);
    $product['images'] = $imgStmt->fetchAll();

    // Specifications
    $specStmt = getDB()->prepare("SELECT spec_key, spec_value FROM product_specifications WHERE product_id=? ORDER BY sort_order");
    $specStmt->execute([$product['product_id']]);
    $product['specs'] = $specStmt->fetchAll();
    return $product;
}

function getProductById(int $id): ?array {
    $stmt = getDB()->prepare("
        SELECT p.*, pi.image_url AS main_image,
               s.shop_id, s.shop_name, s.shop_slug, s.rating AS shop_rating,
               s.total_sales AS shop_sales, s.total_reviews AS shop_reviews,
               s.is_verified AS shop_verified,
               c.name AS category_name, c.slug AS category_slug
        FROM products p
        JOIN shops s ON p.shop_id=s.shop_id
        JOIN categories c ON p.category_id=c.category_id
        LEFT JOIN product_images pi ON pi.product_id=p.product_id AND pi.is_primary=1
        WHERE p.product_id=? AND p.status='active'
    ");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    return $product ?: null;
}

function getCategories(?int $parentId = null): array {
    $stmt = $parentId === null
        ? getDB()->query("SELECT * FROM categories WHERE parent_id IS NULL AND is_active=1 ORDER BY sort_order,name")
        : getDB()->prepare("SELECT * FROM categories WHERE parent_id=? AND is_active=1 ORDER BY sort_order,name");
    if ($parentId !== null) $stmt->execute([$parentId]);
    return $stmt->fetchAll();
}

function getCategoryBySlug(string $slug): ?array {
    $stmt = getDB()->prepare("SELECT * FROM categories WHERE slug=? AND is_active=1");
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function getRelatedProducts(int $productId, int $categoryId, int $shopId, int $limit = 10): array {
    $stmt = getDB()->prepare("
        SELECT p.product_id, p.name, p.slug, p.base_price, p.discount_price, p.rating, p.total_sold,
               pi.image_url
        FROM products p
        LEFT JOIN product_images pi ON pi.product_id=p.product_id AND pi.is_primary=1
        WHERE p.status='active' AND p.product_id!=? AND (p.category_id=? OR p.shop_id=?)
        ORDER BY p.total_sold DESC LIMIT ?
    ");
    $stmt->execute([$productId, $categoryId, $shopId, $limit]);
    return $stmt->fetchAll();
}

function getProductReviews(int $productId, int $page = 1, int $perPage = 10, int $ratingFilter = 0): array {
    $where  = "r.product_id=? AND r.is_hidden=0";
    $params = [$productId];
    if ($ratingFilter > 0) { $where .= " AND r.rating=?"; $params[] = $ratingFilter; }
    $total = (int)getDB()->prepare("SELECT COUNT(*) FROM reviews r WHERE $where")->execute($params) ? 0 : 0;
    $cntStmt = getDB()->prepare("SELECT COUNT(*) FROM reviews r WHERE $where");
    $cntStmt->execute($params);
    $total  = (int)$cntStmt->fetchColumn();
    $offset = ($page - 1) * $perPage;
    $stmt   = getDB()->prepare("
        SELECT r.*, u.username, u.avatar_url, o.created_at AS order_date
        FROM reviews r
        JOIN users u ON r.reviewer_id=u.user_id
        LEFT JOIN orders o ON r.order_id=o.order_id
        WHERE $where
        ORDER BY r.created_at DESC LIMIT $perPage OFFSET $offset
    ");
    $stmt->execute($params);
    return ['data'=>$stmt->fetchAll(),'total'=>$total,'total_pages'=>max(1,ceil($total/$perPage))];
}

/* ── Flash Sales ── */
function getActiveFlashSale(): ?array {
    $stmt = getDB()->query("
        SELECT fs.*, COUNT(fsi.flash_item_id) AS item_count
        FROM flash_sales fs
        LEFT JOIN flash_sale_items fsi ON fs.flash_sale_id=fsi.flash_sale_id
        WHERE fs.is_active=1 AND fs.start_at<=NOW() AND fs.end_at>NOW()
        GROUP BY fs.flash_sale_id LIMIT 1
    ");
    $sale = $stmt->fetch();
    if (!$sale) return null;
    $items = getDB()->prepare("
        SELECT fsi.*, p.name, p.slug, p.base_price, pi.image_url,
               fsi.flash_price AS sale_price, fsi.qty_available AS available_stock, fsi.qty_sold AS sold_count
        FROM flash_sale_items fsi
        JOIN products p ON fsi.product_id=p.product_id
        LEFT JOIN product_images pi ON pi.product_id=p.product_id AND pi.is_primary=1
        WHERE fsi.flash_sale_id=? AND p.status='active'
        ORDER BY fsi.qty_sold DESC LIMIT 12
    ");
    $items->execute([$sale['flash_sale_id']]);
    $sale['items'] = $items->fetchAll();
    return $sale;
}

/* ── Banners ── */
function getBanners(string $position = 'homepage_main'): array {
    $stmt = getDB()->prepare("SELECT * FROM banners WHERE position=? AND is_active=1 AND (start_at IS NULL OR start_at<=NOW()) AND (end_at IS NULL OR end_at>NOW()) ORDER BY sort_order");
    $stmt->execute([$position]);
    return $stmt->fetchAll();
}

/* ── Shops ── */
function getShopBySlug(string $slug): ?array {
    $stmt = getDB()->prepare("SELECT s.*,u.username,u.avatar_url AS owner_avatar FROM shops s JOIN users u ON s.owner_user_id=u.user_id WHERE s.shop_slug=? AND s.is_active=1");
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function isFollowingShop(int $userId, int $shopId): bool {
    $stmt = getDB()->prepare("SELECT COUNT(*) FROM shop_followers WHERE user_id=? AND shop_id=?");
    $stmt->execute([$userId,$shopId]);
    return (bool)$stmt->fetchColumn();
}

/* ── Helper: Product Card HTML ── */
function renderProductCard(array $p, array $wishlistIds = []): string {
    $price    = (float)($p['discount_price'] ?: $p['base_price']);
    $origPrice = $p['discount_price'] ? (float)$p['base_price'] : null;
    $disc     = $origPrice ? round((1 - $price/$origPrice)*100) : 0;
    $inWish   = in_array($p['product_id'], $wishlistIds);
    $img      = e($p['image_url'] ?: 'https://via.placeholder.com/300');
    $name     = e($p['name']);
    $slug     = e($p['slug']);
    $stars    = str_repeat('★', round($p['rating'])) . str_repeat('☆', 5-round($p['rating']));
    $sold     = $p['total_sold'] > 0 ? number_format((int)$p['total_sold']) . ' ขาย' : '';
    $heartCls = $inWish ? 'active' : '';
    $heartIco = $inWish ? 'bi-heart-fill' : 'bi-heart';
    $badgeSold = $sold ? '<span class="badge-sold">' . $sold . '</span>' : '';
    $badgeDisc = $disc ? '<span class="badge-discount">-' . $disc . '%</span>' : '';
    $origPriceSpan = $origPrice ? '<span class="price-original">฿' . number_format($origPrice,0) . '</span>' : '';
    $soldSpan = $sold ? '<span class="ms-auto">' . $sold . '</span>' : '';
    $totalReviews = (int)($p['total_reviews'] ?? 0);
    return <<<HTML
<div class="product-card h-100">
  <a href="/webshop/product.php?slug={$slug}" class="d-block">
    <div class="img-wrap">
      <img src="{$img}" alt="{$name}" loading="lazy">
      {$badgeSold}
      {$badgeDisc}
    </div>
  </a>
  <button class="wishlist-btn {$heartCls}" onclick="toggleWishlist({$p['product_id']},this)" title="Wishlist">
    <i class="bi {$heartIco}"></i>
  </button>
  <div class="card-body">
    <a href="/webshop/product.php?slug={$slug}" class="product-name d-block text-dark">{$name}</a>
    <div class="price-wrap">
      <span class="price">฿{$price}</span>
      {$origPriceSpan}
    </div>
    <div class="rating-row">
      <span class="stars">{$stars}</span>
      <span>({$totalReviews})</span>
      {$soldSpan}
    </div>
    <button class="add-cart-btn" onclick="addToCart({$p['product_id']})">
      <i class="bi bi-cart-plus me-1"></i>ใส่ตะกร้า
    </button>
  </div>
</div>
HTML;
}

/* ── Placeholders ── */
function productImagePlaceholder(): string {
    return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300"><rect fill="%23f5f5f5" width="300" height="300"/><text fill="%23ccc" font-size="40" x="50%25" y="50%25" text-anchor="middle" dy=".35em">📷</text></svg>';
}

/* ── Pagination Helper ── */
function pagination(int $total, int $currentPage = 1, int $perPage = 20): array {
    $totalPages = max(1, (int)ceil($total / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    return [
        'total' => $total,
        'page' => $currentPage,
        'per_page' => $perPage,
        'pages' => $totalPages,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
        'next_page' => $currentPage < $totalPages ? $currentPage + 1 : null,
    ];
}

/* ── All Categories Helper ── */
function getAllCategories(): array {
    return getDB()->query("SELECT * FROM categories WHERE is_active=1 ORDER BY parent_id IS NULL DESC, sort_order, name")->fetchAll();
}

/* ── Product Images Helper ── */
function getProductImages(int $productId): array {
    $stmt = getDB()->prepare("SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

/* ── Wishlist Check Helper ── */
function isInWishlist(int $userId, int $productId): bool {
    $stmt = getDB()->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id=? AND product_id=?");
    $stmt->execute([$userId, $productId]);
    return (bool)$stmt->fetchColumn();
}

/* ── Category Navigation Helper ── */
function getCategoryNav(): array {
    $db = getDB();
    $parents = $db->query("SELECT category_id, name, slug, icon FROM categories WHERE parent_id IS NULL AND is_active=1 ORDER BY sort_order LIMIT 10")->fetchAll();
    foreach ($parents as &$p) {
        $sub = $db->prepare("SELECT category_id, name, slug FROM categories WHERE parent_id=? AND is_active=1 ORDER BY sort_order LIMIT 8");
        $sub->execute([$p['category_id']]);
        $p['children'] = $sub->fetchAll();
    }
    return $parents;
}
