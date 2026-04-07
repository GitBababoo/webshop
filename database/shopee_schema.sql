-- ============================================================
--  SHOPEE-LIKE E-COMMERCE DATABASE SCHEMA
--  Engine: MySQL / MariaDB (via XAMPP)
--  Charset: utf8mb4
-- ============================================================

CREATE DATABASE IF NOT EXISTS shopee_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE shopee_db;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. USERS
-- ============================================================
CREATE TABLE users (
    user_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(150) NOT NULL UNIQUE,
    phone         VARCHAR(20)  UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name     VARCHAR(150),
    avatar_url    VARCHAR(500),
    gender        ENUM('male','female','other') DEFAULT NULL,
    birth_date    DATE         DEFAULT NULL,
    role          ENUM('buyer','seller','admin') NOT NULL DEFAULT 'buyer',
    is_verified   TINYINT(1)   NOT NULL DEFAULT 0,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    last_login_at DATETIME     DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. USER ADDRESSES
-- ============================================================
CREATE TABLE user_addresses (
    address_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    label         VARCHAR(50)  DEFAULT 'Home',
    recipient_name VARCHAR(150) NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255) DEFAULT NULL,
    district      VARCHAR(100) NOT NULL,
    province      VARCHAR(100) NOT NULL,
    postal_code   VARCHAR(10)  NOT NULL,
    country       VARCHAR(100) NOT NULL DEFAULT 'Thailand',
    is_default    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_addr_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. SHOPS (Seller Stores)
-- ============================================================
CREATE TABLE shops (
    shop_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_user_id    INT UNSIGNED NOT NULL,
    shop_name        VARCHAR(150) NOT NULL UNIQUE,
    shop_slug        VARCHAR(150) NOT NULL UNIQUE,
    description      TEXT         DEFAULT NULL,
    logo_url         VARCHAR(500) DEFAULT NULL,
    banner_url       VARCHAR(500) DEFAULT NULL,
    shop_type        ENUM('individual','mall','official') NOT NULL DEFAULT 'individual',
    rating           DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    total_reviews    INT UNSIGNED NOT NULL DEFAULT 0,
    total_products   INT UNSIGNED NOT NULL DEFAULT 0,
    total_sales      INT UNSIGNED NOT NULL DEFAULT 0,
    response_rate    DECIMAL(5,2) DEFAULT NULL,
    response_time    VARCHAR(50)  DEFAULT NULL,
    joined_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_verified      TINYINT(1)   NOT NULL DEFAULT 0,
    is_active        TINYINT(1)   NOT NULL DEFAULT 1,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_shop_owner FOREIGN KEY (owner_user_id) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. CATEGORIES (Hierarchical)
-- ============================================================
CREATE TABLE categories (
    category_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id     INT UNSIGNED DEFAULT NULL,
    name          VARCHAR(100) NOT NULL,
    slug          VARCHAR(100) NOT NULL UNIQUE,
    icon_url      VARCHAR(500) DEFAULT NULL,
    image_url     VARCHAR(500) DEFAULT NULL,
    sort_order    INT          NOT NULL DEFAULT 0,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cat_parent FOREIGN KEY (parent_id) REFERENCES categories(category_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. PRODUCTS
-- ============================================================
CREATE TABLE products (
    product_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shop_id         INT UNSIGNED NOT NULL,
    category_id     INT UNSIGNED NOT NULL,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL,
    description     LONGTEXT     DEFAULT NULL,
    base_price      DECIMAL(12,2) NOT NULL,
    discount_price  DECIMAL(12,2) DEFAULT NULL,
    condition_type  ENUM('new','used','refurbished') NOT NULL DEFAULT 'new',
    brand           VARCHAR(100) DEFAULT NULL,
    sku             VARCHAR(100) DEFAULT NULL,
    weight_grams    INT UNSIGNED DEFAULT NULL,
    length_cm       DECIMAL(8,2) DEFAULT NULL,
    width_cm        DECIMAL(8,2) DEFAULT NULL,
    height_cm       DECIMAL(8,2) DEFAULT NULL,
    total_stock     INT UNSIGNED NOT NULL DEFAULT 0,
    total_sold      INT UNSIGNED NOT NULL DEFAULT 0,
    rating          DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    total_reviews   INT UNSIGNED NOT NULL DEFAULT 0,
    total_views     INT UNSIGNED NOT NULL DEFAULT 0,
    total_likes     INT UNSIGNED NOT NULL DEFAULT 0,
    status          ENUM('draft','active','inactive','banned') NOT NULL DEFAULT 'draft',
    is_featured     TINYINT(1)   NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_product_slug (shop_id, slug),
    CONSTRAINT fk_prod_shop     FOREIGN KEY (shop_id)     REFERENCES shops(shop_id)         ON DELETE CASCADE,
    CONSTRAINT fk_prod_category FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,
    INDEX idx_prod_status   (status),
    INDEX idx_prod_category (category_id),
    INDEX idx_prod_shop     (shop_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. PRODUCT IMAGES
-- ============================================================
CREATE TABLE product_images (
    image_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id   INT UNSIGNED NOT NULL,
    image_url    VARCHAR(500) NOT NULL,
    alt_text     VARCHAR(255) DEFAULT NULL,
    sort_order   INT          NOT NULL DEFAULT 0,
    is_primary   TINYINT(1)   NOT NULL DEFAULT 0,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pimg_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. PRODUCT VARIANT TYPES  (e.g. Color, Size)
-- ============================================================
CREATE TABLE variant_types (
    variant_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id      INT UNSIGNED NOT NULL,
    type_name       VARCHAR(100) NOT NULL,
    sort_order      INT          NOT NULL DEFAULT 0,
    CONSTRAINT fk_vtype_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. PRODUCT VARIANT OPTIONS  (e.g. Red, XL)
-- ============================================================
CREATE TABLE variant_options (
    option_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    variant_type_id INT UNSIGNED NOT NULL,
    value           VARCHAR(100) NOT NULL,
    image_url       VARCHAR(500) DEFAULT NULL,
    sort_order      INT          NOT NULL DEFAULT 0,
    CONSTRAINT fk_vopt_type FOREIGN KEY (variant_type_id) REFERENCES variant_types(variant_type_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. PRODUCT SKUs (Combination of variant options)
-- ============================================================
CREATE TABLE product_skus (
    sku_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id     INT UNSIGNED NOT NULL,
    sku_code       VARCHAR(100) DEFAULT NULL,
    price          DECIMAL(12,2) NOT NULL,
    discount_price DECIMAL(12,2) DEFAULT NULL,
    stock          INT UNSIGNED  NOT NULL DEFAULT 0,
    sold           INT UNSIGNED  NOT NULL DEFAULT 0,
    weight_grams   INT UNSIGNED  DEFAULT NULL,
    image_url      VARCHAR(500)  DEFAULT NULL,
    is_active      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_psku_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. SKU ↔ OPTION MAPPING
-- ============================================================
CREATE TABLE sku_option_map (
    sku_id    INT UNSIGNED NOT NULL,
    option_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (sku_id, option_id),
    CONSTRAINT fk_som_sku    FOREIGN KEY (sku_id)    REFERENCES product_skus(sku_id)     ON DELETE CASCADE,
    CONSTRAINT fk_som_option FOREIGN KEY (option_id) REFERENCES variant_options(option_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. PRODUCT SPECIFICATIONS / ATTRIBUTES
-- ============================================================
CREATE TABLE product_specifications (
    spec_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    spec_key   VARCHAR(150) NOT NULL,
    spec_value VARCHAR(500) NOT NULL,
    sort_order INT          NOT NULL DEFAULT 0,
    CONSTRAINT fk_spec_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. SHIPPING PROVIDERS
-- ============================================================
CREATE TABLE shipping_providers (
    provider_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    code          VARCHAR(50)  NOT NULL UNIQUE,
    logo_url      VARCHAR(500) DEFAULT NULL,
    tracking_url  VARCHAR(500) DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. ORDERS
-- ============================================================
CREATE TABLE orders (
    order_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number       VARCHAR(50)   NOT NULL UNIQUE,
    buyer_user_id      INT UNSIGNED  NOT NULL,
    shop_id            INT UNSIGNED  NOT NULL,
    address_id         INT UNSIGNED  NOT NULL,
    provider_id        INT UNSIGNED  DEFAULT NULL,
    voucher_id         INT UNSIGNED  DEFAULT NULL,
    platform_voucher_id INT UNSIGNED DEFAULT NULL,
    subtotal           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    shipping_fee       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    shop_discount      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    voucher_discount   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    coins_used         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_amount       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    payment_method     ENUM('cod','credit_card','debit_card','bank_transfer','e_wallet','shopee_pay','coins') NOT NULL,
    payment_status     ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
    order_status       ENUM('pending','confirmed','processing','shipped','delivered','completed','cancelled','return_requested','returned') NOT NULL DEFAULT 'pending',
    note               TEXT          DEFAULT NULL,
    tracking_number    VARCHAR(100)  DEFAULT NULL,
    shipped_at         DATETIME      DEFAULT NULL,
    delivered_at       DATETIME      DEFAULT NULL,
    completed_at       DATETIME      DEFAULT NULL,
    cancelled_at       DATETIME      DEFAULT NULL,
    cancel_reason      VARCHAR(255)  DEFAULT NULL,
    created_at         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_buyer    FOREIGN KEY (buyer_user_id) REFERENCES users(user_id)              ON DELETE RESTRICT,
    CONSTRAINT fk_order_shop     FOREIGN KEY (shop_id)       REFERENCES shops(shop_id)              ON DELETE RESTRICT,
    CONSTRAINT fk_order_address  FOREIGN KEY (address_id)    REFERENCES user_addresses(address_id)  ON DELETE RESTRICT,
    CONSTRAINT fk_order_provider FOREIGN KEY (provider_id)   REFERENCES shipping_providers(provider_id) ON DELETE SET NULL,
    INDEX idx_order_buyer  (buyer_user_id),
    INDEX idx_order_shop   (shop_id),
    INDEX idx_order_status (order_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. ORDER ITEMS
-- ============================================================
CREATE TABLE order_items (
    item_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED  NOT NULL,
    product_id   INT UNSIGNED  NOT NULL,
    sku_id       INT UNSIGNED  DEFAULT NULL,
    product_name VARCHAR(255)  NOT NULL,
    sku_snapshot VARCHAR(500)  DEFAULT NULL,
    image_url    VARCHAR(500)  DEFAULT NULL,
    unit_price   DECIMAL(12,2) NOT NULL,
    quantity     INT UNSIGNED  NOT NULL DEFAULT 1,
    subtotal     DECIMAL(12,2) NOT NULL,
    review_id    INT UNSIGNED  DEFAULT NULL,
    CONSTRAINT fk_oi_order   FOREIGN KEY (order_id)   REFERENCES orders(order_id)       ON DELETE CASCADE,
    CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products(product_id)   ON DELETE RESTRICT,
    CONSTRAINT fk_oi_sku     FOREIGN KEY (sku_id)     REFERENCES product_skus(sku_id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. ORDER STATUS HISTORY
-- ============================================================
CREATE TABLE order_status_history (
    history_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id    INT UNSIGNED NOT NULL,
    status      ENUM('pending','confirmed','processing','shipped','delivered','completed','cancelled','return_requested','returned') NOT NULL,
    note        VARCHAR(500) DEFAULT NULL,
    created_by  INT UNSIGNED DEFAULT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_osh_order FOREIGN KEY (order_id)   REFERENCES orders(order_id) ON DELETE CASCADE,
    CONSTRAINT fk_osh_user  FOREIGN KEY (created_by) REFERENCES users(user_id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. PAYMENTS
-- ============================================================
CREATE TABLE payments (
    payment_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id          INT UNSIGNED  NOT NULL,
    payment_method    ENUM('cod','credit_card','debit_card','bank_transfer','e_wallet','shopee_pay','coins') NOT NULL,
    amount            DECIMAL(12,2) NOT NULL,
    currency          CHAR(3)       NOT NULL DEFAULT 'THB',
    status            ENUM('pending','success','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
    transaction_ref   VARCHAR(255)  DEFAULT NULL,
    gateway_response  JSON          DEFAULT NULL,
    paid_at           DATETIME      DEFAULT NULL,
    refunded_at       DATETIME      DEFAULT NULL,
    refund_amount     DECIMAL(12,2) DEFAULT NULL,
    created_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pay_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. RETURN REQUESTS
-- ============================================================
CREATE TABLE return_requests (
    return_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id      INT UNSIGNED NOT NULL,
    buyer_user_id INT UNSIGNED NOT NULL,
    reason        VARCHAR(255) NOT NULL,
    description   TEXT         DEFAULT NULL,
    return_type   ENUM('return_refund','refund_only') NOT NULL DEFAULT 'return_refund',
    status        ENUM('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
    refund_amount DECIMAL(12,2) DEFAULT NULL,
    resolved_at   DATETIME      DEFAULT NULL,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ret_order FOREIGN KEY (order_id)      REFERENCES orders(order_id) ON DELETE RESTRICT,
    CONSTRAINT fk_ret_buyer FOREIGN KEY (buyer_user_id) REFERENCES users(user_id)   ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE return_request_images (
    image_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_id  INT UNSIGNED NOT NULL,
    image_url  VARCHAR(500) NOT NULL,
    CONSTRAINT fk_rimg_return FOREIGN KEY (return_id) REFERENCES return_requests(return_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 18. PRODUCT REVIEWS
-- ============================================================
CREATE TABLE reviews (
    review_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id   INT UNSIGNED NOT NULL,
    sku_id       INT UNSIGNED DEFAULT NULL,
    order_id     INT UNSIGNED NOT NULL,
    reviewer_id  INT UNSIGNED NOT NULL,
    shop_id      INT UNSIGNED NOT NULL,
    rating       TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment      TEXT             DEFAULT NULL,
    is_anonymous TINYINT(1)       NOT NULL DEFAULT 0,
    seller_reply TEXT             DEFAULT NULL,
    replied_at   DATETIME         DEFAULT NULL,
    is_hidden    TINYINT(1)       NOT NULL DEFAULT 0,
    created_at   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rev_product  FOREIGN KEY (product_id)  REFERENCES products(product_id) ON DELETE CASCADE,
    CONSTRAINT fk_rev_order    FOREIGN KEY (order_id)    REFERENCES orders(order_id)     ON DELETE RESTRICT,
    CONSTRAINT fk_rev_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(user_id)       ON DELETE RESTRICT,
    CONSTRAINT fk_rev_shop     FOREIGN KEY (shop_id)     REFERENCES shops(shop_id)       ON DELETE CASCADE,
    INDEX idx_rev_product (product_id),
    INDEX idx_rev_rating  (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE review_images (
    image_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id INT UNSIGNED NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    CONSTRAINT fk_revi_review FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 19. REVIEW LIKES (Helpful votes)
-- ============================================================
CREATE TABLE review_likes (
    user_id   INT UNSIGNED NOT NULL,
    review_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, review_id),
    CONSTRAINT fk_rl_user   FOREIGN KEY (user_id)   REFERENCES users(user_id)     ON DELETE CASCADE,
    CONSTRAINT fk_rl_review FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 20. SHOPPING CART
-- ============================================================
CREATE TABLE carts (
    cart_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL UNIQUE,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
    cart_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id      INT UNSIGNED  NOT NULL,
    product_id   INT UNSIGNED  NOT NULL,
    sku_id       INT UNSIGNED  DEFAULT NULL,
    quantity     INT UNSIGNED  NOT NULL DEFAULT 1,
    is_checked   TINYINT(1)    NOT NULL DEFAULT 1,
    added_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cart_sku (cart_id, product_id, sku_id),
    CONSTRAINT fk_ci_cart    FOREIGN KEY (cart_id)    REFERENCES carts(cart_id)         ON DELETE CASCADE,
    CONSTRAINT fk_ci_product FOREIGN KEY (product_id) REFERENCES products(product_id)   ON DELETE CASCADE,
    CONSTRAINT fk_ci_sku     FOREIGN KEY (sku_id)     REFERENCES product_skus(sku_id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 21. WISHLISTS (Liked Products)
-- ============================================================
CREATE TABLE wishlists (
    user_id    INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    added_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, product_id),
    CONSTRAINT fk_wl_user    FOREIGN KEY (user_id)    REFERENCES users(user_id)       ON DELETE CASCADE,
    CONSTRAINT fk_wl_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 22. SHOP FOLLOWERS
-- ============================================================
CREATE TABLE shop_followers (
    user_id    INT UNSIGNED NOT NULL,
    shop_id    INT UNSIGNED NOT NULL,
    followed_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, shop_id),
    CONSTRAINT fk_sf_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_sf_shop FOREIGN KEY (shop_id) REFERENCES shops(shop_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 23. PLATFORM VOUCHERS (Shopee-issued)
-- ============================================================
CREATE TABLE platform_vouchers (
    voucher_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code             VARCHAR(50)   NOT NULL UNIQUE,
    name             VARCHAR(150)  NOT NULL,
    description      TEXT          DEFAULT NULL,
    discount_type    ENUM('percentage','fixed','free_shipping') NOT NULL,
    discount_value   DECIMAL(12,2) NOT NULL,
    min_order_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    max_discount_cap DECIMAL(12,2) DEFAULT NULL,
    total_qty        INT UNSIGNED  DEFAULT NULL,
    used_qty         INT UNSIGNED  NOT NULL DEFAULT 0,
    per_user_limit   INT UNSIGNED  NOT NULL DEFAULT 1,
    applicable_to    ENUM('all','category','product') NOT NULL DEFAULT 'all',
    category_id      INT UNSIGNED  DEFAULT NULL,
    start_at         DATETIME      NOT NULL,
    expire_at        DATETIME      NOT NULL,
    is_active        TINYINT(1)    NOT NULL DEFAULT 1,
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 24. SHOP VOUCHERS (Seller-issued)
-- ============================================================
CREATE TABLE shop_vouchers (
    voucher_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shop_id          INT UNSIGNED  NOT NULL,
    code             VARCHAR(50)   NOT NULL,
    name             VARCHAR(150)  NOT NULL,
    description      TEXT          DEFAULT NULL,
    discount_type    ENUM('percentage','fixed','free_shipping') NOT NULL,
    discount_value   DECIMAL(12,2) NOT NULL,
    min_order_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    max_discount_cap DECIMAL(12,2) DEFAULT NULL,
    total_qty        INT UNSIGNED  DEFAULT NULL,
    used_qty         INT UNSIGNED  NOT NULL DEFAULT 0,
    per_user_limit   INT UNSIGNED  NOT NULL DEFAULT 1,
    start_at         DATETIME      NOT NULL,
    expire_at        DATETIME      NOT NULL,
    is_active        TINYINT(1)    NOT NULL DEFAULT 1,
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_shop_voucher_code (shop_id, code),
    CONSTRAINT fk_sv_shop FOREIGN KEY (shop_id) REFERENCES shops(shop_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 25. USER VOUCHER COLLECTIONS (Claimed)
-- ============================================================
CREATE TABLE user_vouchers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    voucher_type    ENUM('platform','shop') NOT NULL,
    platform_voucher_id INT UNSIGNED DEFAULT NULL,
    shop_voucher_id INT UNSIGNED DEFAULT NULL,
    is_used         TINYINT(1)   NOT NULL DEFAULT 0,
    used_order_id   INT UNSIGNED DEFAULT NULL,
    collected_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_uv_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 26. FLASH SALES
-- ============================================================
CREATE TABLE flash_sales (
    flash_sale_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(255) NOT NULL,
    start_at      DATETIME     NOT NULL,
    end_at        DATETIME     NOT NULL,
    banner_url    VARCHAR(500) DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE flash_sale_items (
    flash_item_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    flash_sale_id  INT UNSIGNED  NOT NULL,
    product_id     INT UNSIGNED  NOT NULL,
    sku_id         INT UNSIGNED  DEFAULT NULL,
    flash_price    DECIMAL(12,2) NOT NULL,
    original_price DECIMAL(12,2) NOT NULL,
    qty_available  INT UNSIGNED  NOT NULL DEFAULT 0,
    qty_sold       INT UNSIGNED  NOT NULL DEFAULT 0,
    per_user_limit INT UNSIGNED  NOT NULL DEFAULT 1,
    CONSTRAINT fk_fsi_sale    FOREIGN KEY (flash_sale_id) REFERENCES flash_sales(flash_sale_id) ON DELETE CASCADE,
    CONSTRAINT fk_fsi_product FOREIGN KEY (product_id)   REFERENCES products(product_id)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 27. BANNERS / PROMOTIONS
-- ============================================================
CREATE TABLE banners (
    banner_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    image_url   VARCHAR(500) NOT NULL,
    link_url    VARCHAR(500) DEFAULT NULL,
    position    ENUM('homepage_main','homepage_sub','category','flash_sale','popup') NOT NULL DEFAULT 'homepage_main',
    sort_order  INT          NOT NULL DEFAULT 0,
    start_at    DATETIME     DEFAULT NULL,
    end_at      DATETIME     DEFAULT NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 28. NOTIFICATIONS
-- ============================================================
CREATE TABLE notifications (
    notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    type            VARCHAR(50)  NOT NULL,
    title           VARCHAR(255) NOT NULL,
    body            TEXT         DEFAULT NULL,
    reference_type  VARCHAR(50)  DEFAULT NULL,
    reference_id    INT UNSIGNED DEFAULT NULL,
    is_read         TINYINT(1)   NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_notif_user   (user_id),
    INDEX idx_notif_unread (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 29. CHAT CONVERSATIONS
-- ============================================================
CREATE TABLE conversations (
    conversation_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_user_id   INT UNSIGNED NOT NULL,
    shop_id         INT UNSIGNED NOT NULL,
    last_message_at DATETIME     DEFAULT NULL,
    buyer_unread    INT UNSIGNED NOT NULL DEFAULT 0,
    seller_unread   INT UNSIGNED NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_conv (buyer_user_id, shop_id),
    CONSTRAINT fk_conv_buyer FOREIGN KEY (buyer_user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_conv_shop  FOREIGN KEY (shop_id)       REFERENCES shops(shop_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messages (
    message_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT UNSIGNED NOT NULL,
    sender_id       INT UNSIGNED NOT NULL,
    message_type    ENUM('text','image','product','order','sticker') NOT NULL DEFAULT 'text',
    content         TEXT         DEFAULT NULL,
    image_url       VARCHAR(500) DEFAULT NULL,
    product_id      INT UNSIGNED DEFAULT NULL,
    order_id        INT UNSIGNED DEFAULT NULL,
    is_read         TINYINT(1)   NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_msg_conv   FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_sender FOREIGN KEY (sender_id)       REFERENCES users(user_id)                ON DELETE RESTRICT,
    INDEX idx_msg_conv (conversation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 30. WALLET / SHOPEE COINS
-- ============================================================
CREATE TABLE wallets (
    wallet_id   INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED  NOT NULL UNIQUE,
    balance     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    coins       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_wallet_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE wallet_transactions (
    transaction_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wallet_id       INT UNSIGNED  NOT NULL,
    type            ENUM('topup','withdrawal','payment','refund','cashback','coins_earn','coins_spend') NOT NULL,
    amount          DECIMAL(12,2) NOT NULL,
    balance_before  DECIMAL(12,2) NOT NULL,
    balance_after   DECIMAL(12,2) NOT NULL,
    reference_type  VARCHAR(50)   DEFAULT NULL,
    reference_id    INT UNSIGNED  DEFAULT NULL,
    description     VARCHAR(255)  DEFAULT NULL,
    status          ENUM('pending','success','failed') NOT NULL DEFAULT 'success',
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wt_wallet FOREIGN KEY (wallet_id) REFERENCES wallets(wallet_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 31. SEARCH HISTORY
-- ============================================================
CREATE TABLE search_history (
    search_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    keyword    VARCHAR(255) NOT NULL,
    searched_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sh_user (user_id),
    CONSTRAINT fk_sh_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 32. PRODUCT REPORTS
-- ============================================================
CREATE TABLE product_reports (
    report_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id   INT UNSIGNED NOT NULL,
    reporter_id  INT UNSIGNED NOT NULL,
    reason       VARCHAR(255) NOT NULL,
    description  TEXT         DEFAULT NULL,
    status       ENUM('pending','reviewed','resolved','dismissed') NOT NULL DEFAULT 'pending',
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pr_product  FOREIGN KEY (product_id)  REFERENCES products(product_id) ON DELETE CASCADE,
    CONSTRAINT fk_pr_reporter FOREIGN KEY (reporter_id) REFERENCES users(user_id)       ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 33. SHOP RATINGS SUMMARY (Denormalized cache)
-- ============================================================
CREATE TABLE shop_rating_summary (
    shop_id      INT UNSIGNED NOT NULL PRIMARY KEY,
    rating_5     INT UNSIGNED NOT NULL DEFAULT 0,
    rating_4     INT UNSIGNED NOT NULL DEFAULT 0,
    rating_3     INT UNSIGNED NOT NULL DEFAULT 0,
    rating_2     INT UNSIGNED NOT NULL DEFAULT 0,
    rating_1     INT UNSIGNED NOT NULL DEFAULT 0,
    avg_rating   DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    total_reviews INT UNSIGNED NOT NULL DEFAULT 0,
    updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_srs_shop FOREIGN KEY (shop_id) REFERENCES shops(shop_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 34. ADMIN LOGS
-- ============================================================
CREATE TABLE admin_logs (
    log_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id     INT UNSIGNED NOT NULL,
    action       VARCHAR(100) NOT NULL,
    target_type  VARCHAR(50)  DEFAULT NULL,
    target_id    INT UNSIGNED DEFAULT NULL,
    detail       JSON         DEFAULT NULL,
    ip_address   VARCHAR(45)  DEFAULT NULL,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_al_admin FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
