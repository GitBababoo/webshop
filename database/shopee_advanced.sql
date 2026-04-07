-- ============================================================
--  ADVANCED MIGRATION  –  shopee_db
--  Roles (many-to-many), Bans, Sessions, OTP, Support, etc.
-- ============================================================
USE shopee_db;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. ROLES TABLE  (system roles catalogue)
-- ============================================================
CREATE TABLE IF NOT EXISTS roles (
    role_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_key    VARCHAR(50)  NOT NULL UNIQUE,
    role_name   VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    color       VARCHAR(20)  NOT NULL DEFAULT '#6c757d',
    icon        VARCHAR(50)  DEFAULT NULL,
    is_default  TINYINT(1)   NOT NULL DEFAULT 0,
    is_system   TINYINT(1)   NOT NULL DEFAULT 1,
    sort_order  INT          NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. USER ROLES  (many-to-many: 1 user → many roles)
-- ============================================================
CREATE TABLE IF NOT EXISTS user_roles (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    role_id     INT UNSIGNED NOT NULL,
    assigned_by INT UNSIGNED DEFAULT NULL,
    assigned_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at  DATETIME     DEFAULT NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    reason      VARCHAR(255) DEFAULT NULL,
    UNIQUE KEY uq_user_role (user_id, role_id),
    CONSTRAINT fk_ur_user    FOREIGN KEY (user_id)     REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_ur_role    FOREIGN KEY (role_id)     REFERENCES roles(role_id) ON DELETE CASCADE,
    CONSTRAINT fk_ur_assigner FOREIGN KEY (assigned_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. USER BANS  (detailed ban history)
-- ============================================================
CREATE TABLE IF NOT EXISTS user_bans (
    ban_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    banned_by     INT UNSIGNED NOT NULL,
    ban_type      ENUM('warning','temporary','permanent') NOT NULL DEFAULT 'temporary',
    reason        TEXT         NOT NULL,
    detail        TEXT         DEFAULT NULL,
    evidence_url  VARCHAR(500) DEFAULT NULL,
    duration_days INT UNSIGNED DEFAULT NULL,
    expires_at    DATETIME     DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    unbanned_by   INT UNSIGNED DEFAULT NULL,
    unban_reason  VARCHAR(255) DEFAULT NULL,
    unbanned_at   DATETIME     DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ub_user   (user_id),
    INDEX idx_ub_active (is_active),
    CONSTRAINT fk_ub_user    FOREIGN KEY (user_id)    REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_ub_banner  FOREIGN KEY (banned_by)  REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_ub_unbanner FOREIGN KEY (unbanned_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. SHOP BANS
-- ============================================================
CREATE TABLE IF NOT EXISTS shop_bans (
    ban_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shop_id       INT UNSIGNED NOT NULL,
    banned_by     INT UNSIGNED NOT NULL,
    ban_type      ENUM('warning','temporary','permanent') NOT NULL DEFAULT 'temporary',
    reason        TEXT         NOT NULL,
    detail        TEXT         DEFAULT NULL,
    duration_days INT UNSIGNED DEFAULT NULL,
    expires_at    DATETIME     DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    unbanned_by   INT UNSIGNED DEFAULT NULL,
    unban_reason  VARCHAR(255) DEFAULT NULL,
    unbanned_at   DATETIME     DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sb_shop    FOREIGN KEY (shop_id)    REFERENCES shops(shop_id) ON DELETE CASCADE,
    CONSTRAINT fk_sb_banner  FOREIGN KEY (banned_by)  REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_sb_unbanner FOREIGN KEY (unbanned_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. PRODUCT BANS
-- ============================================================
CREATE TABLE IF NOT EXISTS product_bans (
    ban_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id    INT UNSIGNED NOT NULL,
    banned_by     INT UNSIGNED NOT NULL,
    reason        TEXT         NOT NULL,
    ban_category  ENUM('counterfeit','prohibited','spam','fraud','copyright','other') NOT NULL DEFAULT 'other',
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    unbanned_by   INT UNSIGNED DEFAULT NULL,
    unban_reason  VARCHAR(255) DEFAULT NULL,
    unbanned_at   DATETIME     DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pb_product  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    CONSTRAINT fk_pb_banner   FOREIGN KEY (banned_by)  REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_pb_unbanner FOREIGN KEY (unbanned_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. IP BLACKLIST
-- ============================================================
CREATE TABLE IF NOT EXISTS ip_blacklist (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address  VARCHAR(45)  NOT NULL UNIQUE,
    reason      VARCHAR(255) DEFAULT NULL,
    blocked_by  INT UNSIGNED DEFAULT NULL,
    expires_at  DATETIME     DEFAULT NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ipbl_user FOREIGN KEY (blocked_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. USER SESSIONS  (active session tracking)
-- ============================================================
CREATE TABLE IF NOT EXISTS user_sessions (
    session_id   VARCHAR(128) NOT NULL PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    ip_address   VARCHAR(45)  DEFAULT NULL,
    user_agent   VARCHAR(500) DEFAULT NULL,
    device_type  ENUM('desktop','mobile','tablet','unknown') NOT NULL DEFAULT 'unknown',
    os           VARCHAR(100) DEFAULT NULL,
    browser      VARCHAR(100) DEFAULT NULL,
    location     VARCHAR(255) DEFAULT NULL,
    last_active  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at   DATETIME     NOT NULL,
    is_active    TINYINT(1)   NOT NULL DEFAULT 1,
    INDEX idx_sess_user (user_id),
    CONSTRAINT fk_sess_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. OTP VERIFICATIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS otp_verifications (
    otp_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED DEFAULT NULL,
    contact     VARCHAR(150) NOT NULL,
    contact_type ENUM('email','phone') NOT NULL DEFAULT 'email',
    otp_code    VARCHAR(10)  NOT NULL,
    purpose     ENUM('register','login','reset_password','change_email','change_phone','verify') NOT NULL DEFAULT 'verify',
    attempts    TINYINT UNSIGNED NOT NULL DEFAULT 0,
    max_attempts TINYINT UNSIGNED NOT NULL DEFAULT 5,
    is_used     TINYINT(1)   NOT NULL DEFAULT 0,
    ip_address  VARCHAR(45)  DEFAULT NULL,
    expires_at  DATETIME     NOT NULL,
    used_at     DATETIME     DEFAULT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_otp_contact (contact, contact_type),
    CONSTRAINT fk_otp_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. SUPPORT TICKETS
-- ============================================================
CREATE TABLE IF NOT EXISTS support_tickets (
    ticket_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(20) NOT NULL UNIQUE,
    user_id      INT UNSIGNED NOT NULL,
    order_id     INT UNSIGNED DEFAULT NULL,
    subject      VARCHAR(255) NOT NULL,
    category     ENUM('payment','shipping','product','account','refund','fraud','other') NOT NULL DEFAULT 'other',
    priority     ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
    status       ENUM('open','in_progress','waiting_user','resolved','closed') NOT NULL DEFAULT 'open',
    assigned_to  INT UNSIGNED DEFAULT NULL,
    resolved_at  DATETIME     DEFAULT NULL,
    closed_at    DATETIME     DEFAULT NULL,
    satisfaction_rating TINYINT UNSIGNED DEFAULT NULL CHECK (satisfaction_rating BETWEEN 1 AND 5),
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_st_user    FOREIGN KEY (user_id)    REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_st_order   FOREIGN KEY (order_id)   REFERENCES orders(order_id) ON DELETE SET NULL,
    CONSTRAINT fk_st_assignee FOREIGN KEY (assigned_to) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_ticket_status (status),
    INDEX idx_ticket_user   (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS support_ticket_messages (
    message_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id    INT UNSIGNED NOT NULL,
    sender_id    INT UNSIGNED NOT NULL,
    sender_type  ENUM('user','admin','system') NOT NULL DEFAULT 'user',
    message      TEXT         NOT NULL,
    attachment_url VARCHAR(500) DEFAULT NULL,
    is_internal  TINYINT(1)   NOT NULL DEFAULT 0,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_stm_ticket FOREIGN KEY (ticket_id) REFERENCES support_tickets(ticket_id) ON DELETE CASCADE,
    CONSTRAINT fk_stm_sender FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. PRODUCT Q&A
-- ============================================================
CREATE TABLE IF NOT EXISTS product_questions (
    question_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id   INT UNSIGNED NOT NULL,
    user_id      INT UNSIGNED NOT NULL,
    question     TEXT         NOT NULL,
    is_anonymous TINYINT(1)   NOT NULL DEFAULT 0,
    status       ENUM('pending','answered','hidden') NOT NULL DEFAULT 'pending',
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pq_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    CONSTRAINT fk_pq_user    FOREIGN KEY (user_id)    REFERENCES users(user_id)    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_answers (
    answer_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id  INT UNSIGNED NOT NULL,
    answerer_id  INT UNSIGNED NOT NULL,
    answerer_type ENUM('seller','admin','user') NOT NULL DEFAULT 'seller',
    answer       TEXT         NOT NULL,
    is_verified  TINYINT(1)   NOT NULL DEFAULT 0,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pa_question FOREIGN KEY (question_id) REFERENCES product_questions(question_id) ON DELETE CASCADE,
    CONSTRAINT fk_pa_answerer  FOREIGN KEY (answerer_id) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. SHIPPING ZONES & RATES
-- ============================================================
CREATE TABLE IF NOT EXISTS shipping_zones (
    zone_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    zone_name   VARCHAR(100) NOT NULL,
    provinces   JSON         DEFAULT NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shipping_rates (
    rate_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id  INT UNSIGNED NOT NULL,
    zone_id      INT UNSIGNED NOT NULL,
    min_weight_g INT UNSIGNED NOT NULL DEFAULT 0,
    max_weight_g INT UNSIGNED DEFAULT NULL,
    base_rate    DECIMAL(8,2) NOT NULL,
    rate_per_kg  DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    min_days     TINYINT UNSIGNED NOT NULL DEFAULT 1,
    max_days     TINYINT UNSIGNED NOT NULL DEFAULT 3,
    is_active    TINYINT(1)   NOT NULL DEFAULT 1,
    CONSTRAINT fk_sr_provider FOREIGN KEY (provider_id) REFERENCES shipping_providers(provider_id) ON DELETE CASCADE,
    CONSTRAINT fk_sr_zone     FOREIGN KEY (zone_id)     REFERENCES shipping_zones(zone_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. PRODUCT VIEWS TRACKING
-- ============================================================
CREATE TABLE IF NOT EXISTS product_views (
    view_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED DEFAULT NULL,
    session_id  VARCHAR(128) DEFAULT NULL,
    ip_address  VARCHAR(45)  DEFAULT NULL,
    referrer    VARCHAR(500) DEFAULT NULL,
    viewed_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pv_product (product_id),
    INDEX idx_pv_user    (user_id),
    CONSTRAINT fk_pv_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    CONSTRAINT fk_pv_user    FOREIGN KEY (user_id)    REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. REFERRAL SYSTEM
-- ============================================================
CREATE TABLE IF NOT EXISTS referral_codes (
    code_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL UNIQUE,
    code        VARCHAR(20)  NOT NULL UNIQUE,
    total_referred INT UNSIGNED NOT NULL DEFAULT 0,
    total_earned   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rc_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS referrals (
    referral_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    referrer_id    INT UNSIGNED NOT NULL,
    referred_id    INT UNSIGNED NOT NULL UNIQUE,
    referral_code  VARCHAR(20)  NOT NULL,
    reward_amount  DECIMAL(12,2) DEFAULT NULL,
    reward_given   TINYINT(1)   NOT NULL DEFAULT 0,
    rewarded_at    DATETIME     DEFAULT NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ref_referrer  FOREIGN KEY (referrer_id)  REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_ref_referred  FOREIGN KEY (referred_id)  REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. EMAIL TEMPLATES
-- ============================================================
CREATE TABLE IF NOT EXISTS email_templates (
    template_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_key VARCHAR(100) NOT NULL UNIQUE,
    name         VARCHAR(150) NOT NULL,
    subject      VARCHAR(255) NOT NULL,
    body_html    LONGTEXT     NOT NULL,
    body_text    TEXT         DEFAULT NULL,
    variables    JSON         DEFAULT NULL,
    is_active    TINYINT(1)   NOT NULL DEFAULT 1,
    updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. LOYALTY / POINTS
-- ============================================================
CREATE TABLE IF NOT EXISTS loyalty_points (
    point_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL UNIQUE,
    total_points INT UNSIGNED NOT NULL DEFAULT 0,
    used_points  INT UNSIGNED NOT NULL DEFAULT 0,
    expired_points INT UNSIGNED NOT NULL DEFAULT 0,
    tier         ENUM('bronze','silver','gold','platinum','diamond') NOT NULL DEFAULT 'bronze',
    tier_updated_at DATETIME DEFAULT NULL,
    updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lp_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS loyalty_transactions (
    txn_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    points       INT          NOT NULL,
    type         ENUM('earn','spend','expire','adjust','bonus') NOT NULL,
    reference_type VARCHAR(50) DEFAULT NULL,
    reference_id INT UNSIGNED DEFAULT NULL,
    description  VARCHAR(255) DEFAULT NULL,
    expires_at   DATETIME     DEFAULT NULL,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lt_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. FRAUD REPORTS
-- ============================================================
CREATE TABLE IF NOT EXISTS fraud_reports (
    report_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reporter_id  INT UNSIGNED NOT NULL,
    target_type  ENUM('user','shop','product','order','review') NOT NULL,
    target_id    INT UNSIGNED NOT NULL,
    fraud_type   ENUM('counterfeit','scam','harassment','spam','fake_review','other') NOT NULL,
    description  TEXT         NOT NULL,
    evidence_url VARCHAR(500) DEFAULT NULL,
    status       ENUM('pending','investigating','resolved','dismissed') NOT NULL DEFAULT 'pending',
    reviewed_by  INT UNSIGNED DEFAULT NULL,
    resolution   TEXT         DEFAULT NULL,
    reviewed_at  DATETIME     DEFAULT NULL,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fr_reporter FOREIGN KEY (reporter_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    CONSTRAINT fk_fr_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. PROMO CODE USAGE LOG
-- ============================================================
CREATE TABLE IF NOT EXISTS voucher_usage_log (
    log_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    voucher_type ENUM('platform','shop') NOT NULL,
    voucher_id   INT UNSIGNED NOT NULL,
    user_id      INT UNSIGNED NOT NULL,
    order_id     INT UNSIGNED NOT NULL,
    discount_applied DECIMAL(12,2) NOT NULL,
    used_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vul_user  FOREIGN KEY (user_id)  REFERENCES users(user_id)  ON DELETE RESTRICT,
    CONSTRAINT fk_vul_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 18. ANNOUNCEMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS announcements (
    ann_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    content     TEXT         NOT NULL,
    type        ENUM('info','warning','success','danger') NOT NULL DEFAULT 'info',
    target      ENUM('all','buyer','seller','admin') NOT NULL DEFAULT 'all',
    start_at    DATETIME     DEFAULT NULL,
    end_at      DATETIME     DEFAULT NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    is_dismissible TINYINT(1) NOT NULL DEFAULT 1,
    created_by  INT UNSIGNED DEFAULT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ann_creator FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 19. TAX SETTINGS
-- ============================================================
CREATE TABLE IF NOT EXISTS tax_settings (
    tax_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tax_name    VARCHAR(100) NOT NULL,
    tax_rate    DECIMAL(5,2) NOT NULL,
    applies_to  ENUM('all','category','product') NOT NULL DEFAULT 'all',
    category_id INT UNSIGNED DEFAULT NULL,
    is_inclusive TINYINT(1)  NOT NULL DEFAULT 0,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED: Roles
-- ============================================================
INSERT INTO roles (role_key, role_name, description, color, icon, is_default, is_system, sort_order) VALUES
('superadmin', 'Super Administrator', 'ผู้ดูแลระบบสูงสุด เข้าถึงได้ทุกอย่าง', '#dc3545', 'bi-shield-fill', 0, 1, 10),
('admin',       'Administrator',      'ผู้ดูแลระบบทั่วไป',                    '#fd7e14', 'bi-shield-half', 0, 1, 20),
('content_mod', 'Content Moderator',  'ดูแลเนื้อหา รีวิว และรายงาน',          '#6610f2', 'bi-eye-fill',    0, 1, 30),
('finance',     'Finance Manager',    'ดูแลการเงิน รายงาน และการชำระเงิน',    '#198754', 'bi-cash-coin',   0, 1, 40),
('support',     'Customer Support',   'ดูแล Ticket และช่วยเหลือลูกค้า',       '#0d6efd', 'bi-headset',     0, 1, 50),
('seller',      'Seller',             'เจ้าของร้านค้า',                       '#ff7337', 'bi-shop',        0, 1, 60),
('buyer',       'Buyer',              'ผู้ซื้อทั่วไป',                        '#6c757d', 'bi-person',      1, 1, 70),
('vip_buyer',   'VIP Buyer',          'ลูกค้า VIP ยอดซื้อสูง',               '#ffc107', 'bi-star-fill',   0, 0, 80),
('seller_premium','Premium Seller',   'ร้านค้าพรีเมียม',                      '#20c997', 'bi-shop-window', 0, 0, 90)
ON DUPLICATE KEY UPDATE role_name=VALUES(role_name);

-- ============================================================
-- SEED: Assign roles to existing users
-- ============================================================
INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by, reason)
SELECT u.user_id, r.role_id, 11, 'Initial setup'
FROM users u
JOIN roles r ON r.role_key = u.role
WHERE u.role IN ('buyer','seller','admin','superadmin');

-- Give superadmin ALL roles
INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by, reason)
SELECT 11, r.role_id, 11, 'SuperAdmin gets all roles'
FROM roles r WHERE r.role_key IN ('superadmin','admin','finance','content_mod','support');

-- Give admin user extra roles
INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by, reason)
SELECT 12, r.role_id, 11, 'Admin extra roles'
FROM roles r WHERE r.role_key IN ('admin','content_mod','support');

-- VIP buyer for buyer1
INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by, reason)
SELECT 6, r.role_id, 11, 'High purchase volume'
FROM roles r WHERE r.role_key = 'vip_buyer';

-- Premium seller for seller_a
INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by, reason)
SELECT 3, r.role_id, 11, 'Verified premium seller'
FROM roles r WHERE r.role_key = 'seller_premium';

-- ============================================================
-- SEED: Ban data (demo)
-- ============================================================
INSERT INTO user_bans (user_id, banned_by, ban_type, reason, detail, duration_days, expires_at, is_active) VALUES
(7, 11, 'warning', 'พฤติกรรมไม่เหมาะสม', 'ส่งข้อความรบกวนผู้ขาย', NULL, NULL, 0),
(9, 11, 'temporary', 'ซื้อแล้วยกเลิกซ้ำซาก', 'ยกเลิก 5 ครั้งใน 1 สัปดาห์โดยไม่มีเหตุผล', 7, DATE_ADD(NOW(), INTERVAL 7 DAY), 0);

INSERT INTO shop_bans (shop_id, banned_by, ban_type, reason, detail, duration_days, is_active) VALUES
(3, 11, 'warning', 'สินค้าไม่ตรงตามคำโฆษณา', 'ลูกค้าร้องเรียน 3 ราย', NULL, 0);

INSERT INTO product_bans (product_id, banned_by, reason, ban_category, is_active) VALUES
(5, 11, 'สินค้าลอกเลียนแบบตราสินค้า', 'counterfeit', 0);

-- ============================================================
-- SEED: Shipping zones
-- ============================================================
INSERT INTO shipping_zones (zone_name, provinces) VALUES
('กรุงเทพฯ และปริมณฑล', '["กรุงเทพมหานคร","นนทบุรี","ปทุมธานี","สมุทรปราการ","นครปฐม"]'),
('ภาคกลาง',             '["อยุธยา","สระบุรี","ลพบุรี","ชัยนาท","อ่างทอง","สิงห์บุรี","อุทัยธานี","กาญจนบุรี"]'),
('ภาคเหนือ',            '["เชียงใหม่","เชียงราย","ลำปาง","ลำพูน","แม่ฮ่องสอน","พะเยา","น่าน","แพร่"]'),
('ภาคอีสาน',            '["ขอนแก่น","นครราชสีมา","อุดรธานี","อุบลราชธานี","สกลนคร","บึงกาฬ","มุกดาหาร","กาฬสินธุ์"]'),
('ภาคใต้',              '["ภูเก็ต","สุราษฎร์ธานี","ชุมพร","นครศรีธรรมราช","สงขลา","ยะลา","ปัตตานี","นราธิวาส"]'),
('ภาคตะวันออก',         '["ชลบุรี","ระยอง","จันทบุรี","ตราด","สระแก้ว","ปราจีนบุรี","ฉะเชิงเทรา","นครนายก"]');

-- SEED: Shipping rates
INSERT INTO shipping_rates (provider_id, zone_id, min_weight_g, max_weight_g, base_rate, rate_per_kg, min_days, max_days) VALUES
-- J&T Bangkok
(1, 1, 0, 500, 30.00, 0.00, 1, 2),
(1, 1, 501, 1000, 35.00, 0.00, 1, 2),
(1, 1, 1001, NULL, 35.00, 10.00, 1, 2),
-- J&T Central
(1, 2, 0, 500, 35.00, 0.00, 1, 3),
(1, 2, 501, NULL, 35.00, 12.00, 1, 3),
-- J&T North/NE/South/East
(1, 3, 0, NULL, 40.00, 15.00, 2, 4),
(1, 4, 0, NULL, 40.00, 15.00, 2, 4),
(1, 5, 0, NULL, 45.00, 18.00, 2, 5),
(1, 6, 0, NULL, 40.00, 12.00, 1, 3),
-- Shopee Xpress Bangkok
(5, 1, 0, 2000, 25.00, 0.00, 1, 2),
(5, 1, 2001, NULL, 25.00, 8.00, 1, 2),
(5, 2, 0, NULL, 30.00, 10.00, 1, 3),
(5, 3, 0, NULL, 35.00, 12.00, 2, 4),
(5, 4, 0, NULL, 35.00, 12.00, 2, 4),
(5, 5, 0, NULL, 40.00, 15.00, 2, 5);

-- SEED: Loyalty points
INSERT INTO loyalty_points (user_id, total_points, used_points, tier) VALUES
(6,  1250, 200, 'silver'),
(7,   450,  50, 'bronze'),
(8,  3200, 500, 'gold'),
(9,   180,   0, 'bronze'),
(10,  720, 100, 'bronze');

INSERT INTO loyalty_transactions (user_id, points, type, reference_type, reference_id, description) VALUES
(6,  500,  'earn',  'order', 1, 'ซื้อ Samsung Galaxy S24 Ultra'),
(6,  750,  'earn',  'order', 3, 'ซื้อ Sony WH-1000XM5'),
(6, -200,  'spend', NULL, NULL, 'แลกส่วนลด 200 บาท'),
(8, 1500,  'earn',  'order', 3, 'ซื้อสินค้าครบ 3 รายการ'),
(8, 1000,  'bonus', NULL, NULL, 'โบนัสสมาชิก VIP ประจำเดือน'),
(8,  700,  'earn',  'order', 4, 'ซื้อกระทะ'),
(8, -500,  'spend', NULL, NULL, 'แลก Gift Voucher'),
(7,  490,  'earn',  'order', 2, 'ซื้อ Hoodie Oversized'),
(7,  -40,  'expire',NULL, NULL, 'Points หมดอายุ');

-- SEED: Support tickets
INSERT INTO support_tickets (ticket_number, user_id, order_id, subject, category, priority, status, assigned_to, resolved_at) VALUES
('TKT-20240401-0001', 6, 1, 'สินค้าไม่ตรงกับภาพ', 'product', 'medium', 'resolved', 11, '2024-04-03 14:00:00'),
('TKT-20240402-0002', 7, 2, 'ยังไม่ได้รับสินค้า', 'shipping', 'high', 'resolved', 12, '2024-04-06 10:00:00'),
('TKT-20240501-0003', 9, 4, 'ต้องการขอคืนเงิน', 'refund', 'high', 'open', 12, NULL),
('TKT-20240502-0004', 10, 5, 'ช่วยด้วย ไม่สามารถชำระเงินได้', 'payment', 'urgent', 'in_progress', 11, NULL);

INSERT INTO support_ticket_messages (ticket_id, sender_id, sender_type, message) VALUES
(1, 6,  'user',  'สั่งซื้อ Samsung S24 Ultra มาแต่สีไม่ตรง ในรูปเป็นสีม่วงแต่ได้รับสีเทา'),
(1, 11, 'admin', 'ขอโทษด้วยนะคะ ทางเราจะติดต่อร้านค้าและส่งสินค้าสีถูกต้องให้คุณภายใน 3 วันทำการค่ะ'),
(1, 6,  'user',  'ขอบคุณมากค่ะ'),
(2, 7,  'user',  'สั่งไป 5 วันแล้วยังไม่ได้รับสินค้าเลยครับ'),
(2, 12, 'admin', 'ตรวจสอบแล้วพบว่าพัสดุติดอยู่ที่สาขาครับ จะประสาน J&T ให้นำส่งวันนี้เลยครับ'),
(3, 9,  'user',  'กระทะที่สั่งมาแตกตั้งแต่แกะกล่อง ขอคืนเงินด้วยครับ'),
(4, 10, 'user',  'กดชำระเงินแล้วขึ้น error ตลอด ทำยังไงดีคะ'),
(4, 11, 'admin', 'ลองล้าง cache แล้วลองใหม่ครับ หรือเปลี่ยน browser ดูครับ');

-- SEED: Product Q&A
INSERT INTO product_questions (product_id, user_id, question, status) VALUES
(1, 7, 'Samsung S24 Ultra รุ่นนี้รองรับ 5G มั้ยครับ?', 'answered'),
(1, 8, 'หน้าจอ 6.8 นิ้วใหญ่เกินไปมั้ยคะ ถือแล้วมือถนัดมั้ย?', 'answered'),
(2, 9, 'MacBook Air M3 เล่นเกมได้มั้ยครับ?', 'answered'),
(3, 6, 'Sony XM5 ใส่แล้วหนักมั้ยคะ?', 'answered'),
(4, 9, 'Hoodie ไซส์ M สูง 175 ใส่ได้มั้ยครับ?', 'answered'),
(6, 7, 'กระทะใช้กับเตาแม่เหล็กไฟฟ้าได้มั้ยครับ?', 'pending');

INSERT INTO product_answers (question_id, answerer_id, answerer_type, answer, is_verified) VALUES
(1, 3, 'seller', 'รองรับ 5G ครบทุก Band ในไทยครับ รวมถึง Sub-6GHz และ mmWave ด้วย', 1),
(2, 3, 'seller', 'ขนาด 6.8 นิ้วถือสบายครับ เพราะขอบบางมาก น้ำหนักแค่ 232 กรัม', 1),
(3, 3, 'seller', 'เล่นเกมได้ครับ แต่ไม่ได้ออกแบบมาเพื่อเกมโดยเฉพาะ เกมทั่วไปเล่นได้สบาย', 1),
(4, 3, 'seller', 'น้ำหนักเบามากค่ะ แค่ 250 กรัม ใส่นานๆ ไม่เมื่อย', 1),
(5, 4, 'seller', 'ไซส์ M ความสูง 165-175 เหมาะมากเลยครับ แต่ถ้าชอบ Oversized มากๆ ขึ้น L ได้เลย', 1);

-- SEED: Announcements
INSERT INTO announcements (title, content, type, target, start_at, end_at, is_active, created_by) VALUES
('ระบบปิดปรับปรุง', 'ระบบจะปิดให้บริการชั่วคราวในวันที่ 15 เม.ย. 04:00-06:00 น.', 'warning', 'all', '2024-04-14 00:00:00', '2024-04-15 08:00:00', 0, 11),
('Shopee Birthday Sale', 'มหกรรมช้อปวันเกิด Shopee ลดสูงสุด 70%! 1-30 มิ.ย. เท่านั้น', 'success', 'buyer', '2024-06-01 00:00:00', '2024-06-30 23:59:59', 1, 11),
('นโยบายใหม่สำหรับผู้ขาย', 'ขอแจ้งการปรับปรุงนโยบายค่าธรรมเนียมและการจัดส่ง มีผลตั้งแต่ 1 ก.ค.', 'info', 'seller', '2024-06-20 00:00:00', '2024-07-31 23:59:59', 1, 11);

-- SEED: Email templates
INSERT INTO email_templates (template_key, name, subject, body_html, variables) VALUES
('order_confirm', 'ยืนยันออเดอร์', 'ยืนยันออเดอร์ {{order_number}} ของคุณ',
'<h2>ขอบคุณที่สั่งซื้อ!</h2><p>ออเดอร์ <strong>{{order_number}}</strong> ได้รับการยืนยันแล้ว</p><p>ยอดรวม: <strong>{{total_amount}}</strong></p>',
'["order_number","total_amount","customer_name","items"]'),
('order_shipped', 'แจ้งจัดส่ง', 'ออเดอร์ {{order_number}} จัดส่งแล้ว!',
'<h2>สินค้าของคุณกำลังเดินทาง</h2><p>เลข Tracking: <strong>{{tracking_number}}</strong></p>',
'["order_number","tracking_number","provider_name"]'),
('welcome', 'ต้อนรับสมาชิกใหม่', 'ยินดีต้อนรับสู่ Shopee TH!',
'<h2>สวัสดี {{username}}!</h2><p>บัญชีของคุณถูกสร้างเรียบร้อยแล้ว</p>',
'["username","email"]')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- SEED: Tax settings
INSERT INTO tax_settings (tax_name, tax_rate, applies_to, is_inclusive, is_active) VALUES
('VAT 7%', 7.00, 'all', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
