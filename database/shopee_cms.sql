-- ============================================================
--  CMS + SUPERADMIN MIGRATION  –  shopee_db
-- ============================================================
USE shopee_db;

-- Add superadmin role to users
ALTER TABLE users MODIFY COLUMN role ENUM('buyer','seller','admin','superadmin') NOT NULL DEFAULT 'buyer';

-- ============================================================
-- SITE SETTINGS (key-value store)
-- ============================================================
CREATE TABLE IF NOT EXISTS site_settings (
    setting_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_group VARCHAR(50)  NOT NULL DEFAULT 'general',
    setting_key   VARCHAR(100) NOT NULL UNIQUE,
    setting_value LONGTEXT     DEFAULT NULL,
    setting_type  ENUM('text','number','boolean','json','html','image','color') NOT NULL DEFAULT 'text',
    label         VARCHAR(150) DEFAULT NULL,
    description   VARCHAR(500) DEFAULT NULL,
    sort_order    INT          NOT NULL DEFAULT 0,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CMS PAGES
-- ============================================================
CREATE TABLE IF NOT EXISTS cms_pages (
    page_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(255) NOT NULL,
    slug          VARCHAR(255) NOT NULL UNIQUE,
    content       LONGTEXT     DEFAULT NULL,
    meta_title    VARCHAR(255) DEFAULT NULL,
    meta_desc     VARCHAR(500) DEFAULT NULL,
    meta_keywords VARCHAR(500) DEFAULT NULL,
    og_image      VARCHAR(500) DEFAULT NULL,
    template      ENUM('default','fullwidth','sidebar','landing') NOT NULL DEFAULT 'default',
    status        ENUM('published','draft','private') NOT NULL DEFAULT 'draft',
    is_system     TINYINT(1)   NOT NULL DEFAULT 0,
    sort_order    INT          NOT NULL DEFAULT 0,
    created_by    INT UNSIGNED DEFAULT NULL,
    updated_by    INT UNSIGNED DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CMS MENUS
-- ============================================================
CREATE TABLE IF NOT EXISTS cms_menus (
    menu_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    location   VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cms_menu_items (
    item_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_id     INT UNSIGNED NOT NULL,
    parent_id   INT UNSIGNED DEFAULT NULL,
    label       VARCHAR(150) NOT NULL,
    url         VARCHAR(500) NOT NULL,
    target      ENUM('_self','_blank') NOT NULL DEFAULT '_self',
    icon        VARCHAR(100) DEFAULT NULL,
    sort_order  INT          NOT NULL DEFAULT 0,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    CONSTRAINT fk_cmi_menu   FOREIGN KEY (menu_id)   REFERENCES cms_menus(menu_id) ON DELETE CASCADE,
    CONSTRAINT fk_cmi_parent FOREIGN KEY (parent_id) REFERENCES cms_menu_items(item_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CMS WIDGETS
-- ============================================================
CREATE TABLE IF NOT EXISTS cms_widgets (
    widget_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    widget_type ENUM('html','image_banner','product_grid','category_list','text','video','countdown','announcement') NOT NULL DEFAULT 'html',
    position    VARCHAR(100) NOT NULL,
    content     LONGTEXT     DEFAULT NULL,
    config      JSON         DEFAULT NULL,
    sort_order  INT          NOT NULL DEFAULT 0,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    start_at    DATETIME     DEFAULT NULL,
    end_at      DATETIME     DEFAULT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ADMIN PERMISSIONS (Granular RBAC)
-- ============================================================
CREATE TABLE IF NOT EXISTS permissions (
    perm_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perm_key    VARCHAR(100) NOT NULL UNIQUE,
    label       VARCHAR(150) NOT NULL,
    perm_group  VARCHAR(100) NOT NULL DEFAULT 'general',
    description VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_permissions (
    user_id INT UNSIGNED NOT NULL,
    perm_id INT UNSIGNED NOT NULL,
    granted TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (user_id, perm_id),
    CONSTRAINT fk_ap_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_ap_perm FOREIGN KEY (perm_id) REFERENCES permissions(perm_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ADMIN ACTIVITY LOG
-- ============================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    log_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED DEFAULT NULL,
    action      VARCHAR(100) NOT NULL,
    module      VARCHAR(100) DEFAULT NULL,
    target_type VARCHAR(50)  DEFAULT NULL,
    target_id   INT UNSIGNED DEFAULT NULL,
    description TEXT         DEFAULT NULL,
    ip_address  VARCHAR(45)  DEFAULT NULL,
    user_agent  VARCHAR(500) DEFAULT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_al_user   (user_id),
    INDEX idx_al_module (module),
    CONSTRAINT fk_acl_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED: Superadmin user
-- ============================================================
INSERT INTO users (username, email, phone, password_hash, full_name, role, is_verified, is_active)
VALUES ('superadmin', 'superadmin@shopee.th', '0800000000',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
        'Super Administrator', 'superadmin', 1, 1)
ON DUPLICATE KEY UPDATE role='superadmin';

INSERT INTO users (username, email, phone, password_hash, full_name, role, is_verified, is_active)
VALUES ('admin', 'admin@shopee.th', '0800000003',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
        'Administrator', 'admin', 1, 1)
ON DUPLICATE KEY UPDATE role='admin';

-- ============================================================
-- SEED: Default permissions
-- ============================================================
INSERT INTO permissions (perm_key, label, perm_group) VALUES
('users.view',       'ดูรายการผู้ใช้',       'users'),
('users.create',     'เพิ่มผู้ใช้',           'users'),
('users.edit',       'แก้ไขผู้ใช้',           'users'),
('users.delete',     'ลบผู้ใช้',             'users'),
('shops.view',       'ดูร้านค้า',            'shops'),
('shops.edit',       'แก้ไขร้านค้า',          'shops'),
('shops.ban',        'ระงับร้านค้า',          'shops'),
('products.view',    'ดูสินค้า',             'products'),
('products.edit',    'แก้ไขสินค้า',           'products'),
('products.delete',  'ลบสินค้า',             'products'),
('orders.view',      'ดูออเดอร์',            'orders'),
('orders.edit',      'แก้ไขสถานะออเดอร์',    'orders'),
('reviews.view',     'ดูรีวิว',              'reviews'),
('reviews.hide',     'ซ่อน/แสดงรีวิว',       'reviews'),
('vouchers.manage',  'จัดการโค้ดส่วนลด',     'vouchers'),
('flash_sales.manage','จัดการ Flash Sale',   'promotions'),
('banners.manage',   'จัดการแบนเนอร์',       'promotions'),
('reports.view',     'ดูรายงาน',             'reports'),
('cms.pages',        'จัดการหน้าเว็บ',        'cms'),
('cms.menus',        'จัดการเมนู',           'cms'),
('cms.widgets',      'จัดการ Widget',         'cms'),
('settings.general', 'ตั้งค่าทั่วไป',         'settings'),
('settings.payment', 'ตั้งค่าการชำระเงิน',    'settings'),
('settings.shipping','ตั้งค่าการจัดส่ง',      'settings')
ON DUPLICATE KEY UPDATE label=VALUES(label);

-- ============================================================
-- SEED: Default site settings
-- ============================================================
INSERT INTO site_settings (setting_group, setting_key, setting_value, setting_type, label, sort_order) VALUES
('general',  'site_name',           'Shopee TH',                  'text',    'ชื่อเว็บไซต์',         1),
('general',  'site_tagline',        'ช้อปทุกอย่างง่ายๆ',           'text',    'Tagline',              2),
('general',  'site_email',          'contact@shopee.th',          'text',    'อีเมลติดต่อ',          3),
('general',  'site_phone',          '02-000-0000',                'text',    'เบอร์โทรติดต่อ',       4),
('general',  'site_address',        '123 ถนนสุขุมวิท กรุงเทพฯ',   'text',    'ที่อยู่',              5),
('general',  'site_logo',           '',                           'image',   'โลโก้เว็บไซต์',        6),
('general',  'site_favicon',        '',                           'image',   'Favicon',              7),
('general',  'maintenance_mode',    '0',                          'boolean', 'โหมดปิดปรับปรุง',      8),
('general',  'currency',            'THB',                        'text',    'สกุลเงิน',             9),
('general',  'currency_symbol',     '฿',                          'text',    'สัญลักษณ์เงิน',        10),
('seo',      'meta_title',          'Shopee TH - ช้อปออนไลน์',    'text',    'Meta Title หลัก',     1),
('seo',      'meta_description',    'ช้อปทุกอย่างบน Shopee TH',   'text',    'Meta Description',    2),
('seo',      'google_analytics',    '',                           'text',    'Google Analytics ID', 3),
('seo',      'facebook_pixel',      '',                           'text',    'Facebook Pixel ID',   4),
('social',   'facebook_url',        '',                           'text',    'Facebook URL',        1),
('social',   'instagram_url',       '',                           'text',    'Instagram URL',       2),
('social',   'line_url',            '',                           'text',    'LINE URL',             3),
('social',   'youtube_url',         '',                           'text',    'YouTube URL',         4),
('payment',  'cod_enabled',         '1',                          'boolean', 'เปิด COD',             1),
('payment',  'bank_transfer_enabled','1',                         'boolean', 'เปิดโอนเงิน',          2),
('payment',  'credit_card_enabled', '0',                          'boolean', 'เปิดบัตรเครดิต',       3),
('payment',  'bank_name',           'ธนาคารกสิกรไทย',             'text',    'ชื่อธนาคาร',          4),
('payment',  'bank_account',        '000-0-00000-0',              'text',    'เลขบัญชี',             5),
('payment',  'bank_account_name',   'Shopee TH Co.,Ltd.',         'text',    'ชื่อบัญชี',            6),
('shipping', 'free_shipping_min',   '500',                        'number',  'ฟรีค่าส่งขั้นต่ำ (฿)', 1),
('shipping', 'default_shipping_fee','40',                         'number',  'ค่าส่งมาตรฐาน (฿)',    2),
('email',    'smtp_host',           'smtp.gmail.com',             'text',    'SMTP Host',           1),
('email',    'smtp_port',           '587',                        'number',  'SMTP Port',           2),
('email',    'smtp_user',           '',                           'text',    'SMTP Username',       3),
('email',    'smtp_pass',           '',                           'text',    'SMTP Password',       4),
('email',    'from_name',           'Shopee TH',                  'text',    'ชื่อผู้ส่ง',           5),
('appearance','primary_color',      '#EE4D2D',                    'color',   'สีหลัก',              1),
('appearance','secondary_color',    '#FF7337',                    'color',   'สีรอง',               2),
('appearance','header_bg',          '#EE4D2D',                    'color',   'สี Header',           3),
('appearance','footer_bg',          '#222222',                    'color',   'สี Footer',           4)
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

-- ============================================================
-- SEED: Default CMS pages
-- ============================================================
INSERT INTO cms_pages (title, slug, content, status, is_system) VALUES
('เกี่ยวกับเรา',      'about-us',       '<h2>เกี่ยวกับ Shopee TH</h2><p>เราคือแพลตฟอร์มช้อปปิ้งออนไลน์ชั้นนำของไทย</p>', 'published', 1),
('นโยบายความเป็นส่วนตัว','privacy-policy','<h2>นโยบายความเป็นส่วนตัว</h2><p>เนื้อหานโยบาย...</p>', 'published', 1),
('ข้อกำหนดการใช้งาน', 'terms-of-service','<h2>ข้อกำหนดการใช้งาน</h2><p>เนื้อหาข้อกำหนด...</p>', 'published', 1),
('นโยบายการคืนสินค้า', 'return-policy',  '<h2>นโยบายการคืนสินค้า</h2><p>เนื้อหานโยบายคืนสินค้า...</p>', 'published', 1),
('ติดต่อเรา',         'contact-us',     '<h2>ติดต่อเรา</h2><p>อีเมล: contact@shopee.th</p>', 'published', 1)
ON DUPLICATE KEY UPDATE status='published';

-- SEED: Default menus
INSERT INTO cms_menus (name, location) VALUES
('เมนูหลัก',    'header_main'),
('Footer ซ้าย', 'footer_left'),
('Footer ขวา',  'footer_right')
ON DUPLICATE KEY UPDATE name=VALUES(name);
